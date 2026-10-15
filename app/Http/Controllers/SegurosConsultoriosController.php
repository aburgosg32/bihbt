<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SegurosConsultoriosController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $fecha = $request->input('fecha')
            ?: Carbon::today('America/Lima')->toDateString();

        $programaciones = $this->programaciones($fecha);

        $asignaciones = DB::connection('mysql')
            ->table('seguros_consultorios_asignaciones')
            ->where('fecha', $fecha)
            ->pluck('numero_consultorio', 'id_programacion_sigh');

        $habituales = DB::connection('mysql')
            ->table('seguros_consultorios_habituales')
            ->pluck('numero_consultorio', 'id_servicio_sigh');

        return view('seguros.asignar-consultorios', compact(
            'fecha',
            'programaciones',
            'asignaciones',
            'habituales'
        ));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
            'accion' => [
                'required',
                'in:guardar,masiva,guardar_habituales,aplicar_habituales',
            ],
            'numeros' => ['sometimes', 'array'],
            'numeros.*' => ['nullable', 'string', 'max:20'],
            'seleccionados' => ['sometimes', 'array'],
            'seleccionados.*' => ['integer', 'min:1', 'distinct'],
            'numero_masivo' => ['nullable', 'string', 'max:20'],
        ]);

        $fecha = $datos['fecha'];
        $accion = $datos['accion'];
        $programaciones = $this->programaciones($fecha)
            ->keyBy('IdProgramacion');

        $numeros = [];

        foreach ($datos['numeros'] ?? [] as $id => $numero) {
            if (! $programaciones->has($id)) {
                throw ValidationException::withMessages([
                    'numeros' => 'Una programación ya no pertenece a esta fecha.',
                ]);
            }

            $numeros[(int) $id] = trim((string) $numero);
        }

        $seleccionados = array_map(
            'intval',
            $datos['seleccionados'] ?? []
        );

        foreach ($seleccionados as $id) {
            if (! $programaciones->has($id)) {
                throw ValidationException::withMessages([
                    'seleccionados' => 'Una programación seleccionada ya no está disponible.',
                ]);
            }
        }

        if (
            in_array($accion, ['masiva', 'guardar_habituales'], true)
            && count($seleccionados) === 0
        ) {
            throw ValidationException::withMessages([
                'seleccionados' => 'Marque al menos una programación.',
            ]);
        }

        $numeroMasivo = trim((string) ($datos['numero_masivo'] ?? ''));

        if ($accion === 'masiva' && $numeroMasivo === '') {
            throw ValidationException::withMessages([
                'numero_masivo' => 'Ingrese el número de consultorio para aplicar.',
            ]);
        }

        // Preparar las plantillas antes de escribir.
        $plantillas = [];

        if ($accion === 'guardar_habituales') {
            foreach ($seleccionados as $id) {
                $numero = $numeros[$id] ?? '';

                if ($numero === '') {
                    throw ValidationException::withMessages([
                        'numeros' => 'Todas las filas seleccionadas deben tener un número.',
                    ]);
                }

                $servicio = (int) $programaciones->get($id)->IdServicio;

                if (
                    isset($plantillas[$servicio])
                    && $plantillas[$servicio] !== $numero
                ) {
                    throw ValidationException::withMessages([
                        'numeros' => 'Para guardar un habitual, seleccione un solo número por servicio.',
                    ]);
                }

                $plantillas[$servicio] = $numero;
            }
        }

        $db = DB::connection('mysql');

        $cantidad = $db->transaction(function () use (
            $db,
            $fecha,
            $accion,
            $numeros,
            $seleccionados,
            $numeroMasivo,
            $plantillas,
            $programaciones
        ) {
            $ahora = now();
            $cantidad = 0;

            if ($accion === 'guardar') {
                foreach ($numeros as $id => $numero) {
                    if ($numero === '') {
                        $db->table('seguros_consultorios_asignaciones')
                            ->where('fecha', $fecha)
                            ->where('id_programacion_sigh', $id)
                            ->delete();

                        continue;
                    }

                    $db->table('seguros_consultorios_asignaciones')
                        ->upsert([
                            [
                                'fecha' => $fecha,
                                'id_programacion_sigh' => $id,
                                'numero_consultorio' => $numero,
                                'created_at' => $ahora,
                                'updated_at' => $ahora,
                            ],
                        ], ['fecha', 'id_programacion_sigh'], [
                            'numero_consultorio',
                            'updated_at',
                        ]);

                    $cantidad++;
                }
            }

            if ($accion === 'guardar_habituales') {
                foreach ($plantillas as $servicio => $numero) {
                    $db->table('seguros_consultorios_habituales')
                        ->upsert([
                            [
                                'id_servicio_sigh' => $servicio,
                                'numero_consultorio' => $numero,
                                'created_at' => $ahora,
                                'updated_at' => $ahora,
                            ],
                        ], ['id_servicio_sigh'], [
                            'numero_consultorio',
                            'updated_at',
                        ]);

                    $cantidad++;
                }
            }

            if (in_array($accion, ['masiva', 'aplicar_habituales'], true)) {
                $habituales = $db->table('seguros_consultorios_habituales')
                    ->pluck('numero_consultorio', 'id_servicio_sigh');

                $destinos = $accion === 'masiva'
                    ? $seleccionados
                    : $programaciones->keys()->all();

                foreach ($destinos as $id) {
                    $p = $programaciones->get($id);

                    $numero = $accion === 'masiva'
                        ? $numeroMasivo
                        : $habituales->get($p->IdServicio);

                    if (! $numero) {
                        continue;
                    }

                    // Solo completar pendientes; conservar asignaciones existentes.
                    $cantidad += $db
                        ->table('seguros_consultorios_asignaciones')
                        ->insertOrIgnore([
                            'fecha' => $fecha,
                            'id_programacion_sigh' => $id,
                            'numero_consultorio' => $numero,
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                        ]);
                }
            }

            return $cantidad;
        });

        $mensaje = match ($accion) {
            'guardar' => 'Asignaciones diarias guardadas.',
            'masiva' => "{$cantidad} asignaciones pendientes completadas.",
            'guardar_habituales' => "{$cantidad} consultorios habituales guardados. Para asignarlos al día, pulse Aplicar habituales.",
            'aplicar_habituales' => "{$cantidad} asignaciones pendientes completadas con los habituales.",
        };

        return redirect()
            ->route('seguros.consultorios.index', ['fecha' => $fecha])
            ->with('success', $mensaje);
    }

    private function programaciones(string $fecha)
    {
        $inicio = Carbon::createFromFormat(
            '!Y-m-d',
            $fecha,
            'America/Lima'
        );

        return DB::connection('sigh_produccion')
            ->table('ProgramacionMedica as p')
            ->join('Servicios as s', 's.IdServicio', '=', 'p.IdServicio')
            ->leftJoin('Medicos as m', 'm.IdMedico', '=', 'p.IdMedico')
            ->leftJoin('Empleados as e', 'e.IdEmpleado', '=', 'm.IdEmpleado')
            ->where('s.IdEstado', 1)
            ->whereIn('s.IdTipoServicio', [1, 5])
            ->where('p.Fecha', '>=', $inicio->format('Ymd'))
            ->where('p.Fecha', '<', $inicio->copy()->addDay()->format('Ymd'))
            ->select(
                'p.IdProgramacion',
                'p.IdServicio',
                'p.HoraInicio',
                'p.HoraFin',
                's.Nombre as Servicio',
                'e.ApellidoPaterno',
                'e.ApellidoMaterno',
                'e.Nombres'
            )
            ->orderBy('s.Nombre')
            ->orderBy('p.HoraInicio')
            ->orderBy('p.IdProgramacion')
            ->get();
    }
}
