<?php

namespace App\Http\Controllers;

use App\Models\Procedimiento;
use App\Models\ProcedimientoMedico;
use App\Models\ProcedimientoProgramacion;
use Illuminate\Http\Request;
use App\Models\ProcedimientoCupo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcedimientoProgramacionController extends Controller
{
    public function index(Request $request)
    {
        $query = ProcedimientoProgramacion::with([
            'procedimiento.servicio',
            'medico',
        ])
            ->orderBy('fecha_programacion')
            ->orderBy('hora_inicio');

        if ($request->filled('procedimiento_id')) {
            $query->where(
                'procedimiento_id',
                $request->procedimiento_id
            );
        }

        if ($request->filled('medico_id')) {
            $query->where(
                'procedimiento_medico_id',
                $request->medico_id
            );
        }

        if ($request->filled('fecha')) {
            $query->whereDate(
                'fecha_programacion',
                $request->fecha
            );
        }

        $programaciones = $query
            ->paginate(15)
            ->withQueryString();

        $procedimientos = Procedimiento::with('servicio')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $medicos = ProcedimientoMedico::where('activo', true)
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->get();


        return view(
            'procedimientos.programaciones.index',
            compact(
                'programaciones',
                'procedimientos',
                'medicos'
            )
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        if ($this->existeCruce($datos)) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'El médico ya tiene una programación que se cruza con este horario en la fecha seleccionada.',
                ]);
        }

        DB::transaction(function () use ($datos) {

            $programacion = ProcedimientoProgramacion::create([
                ...$datos,
                'activo' => true,
            ]);

            $this->generarCupos($programacion);
        });

        return redirect()
            ->route('procedimientos.programaciones.index')
            ->with(
                'success',
                'Programación registrada y cupos generados correctamente.'
            );
    }

    public function update(
        Request $request,
        ProcedimientoProgramacion $programacion
    ) {
        $datos = $this->validar($request);

        if ($programacion->cupos()
            ->where(
                'estado',
                ProcedimientoCupo::ESTADO_RESERVADO
            )
            ->exists()
        ) {
            return back()->withErrors([
                'programacion' =>
                'No se puede modificar esta programación porque ya tiene citas reservadas.',
            ]);
        }

        if ($this->existeCruce($datos, $programacion->id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'El médico ya tiene una programación que se cruza con este horario en la fecha seleccionada.',
                ]);
        }

        DB::transaction(function () use ($programacion, $datos) {

            $programacion->update($datos);

            /*
         * Se eliminan los cupos anteriores porque aún no tienen citas.
         */
            $programacion->cupos()->delete();

            /*
         * Se generan nuevamente según el nuevo horario.
         */
            $this->generarCupos($programacion->fresh());
        });

        return redirect()
            ->route('procedimientos.programaciones.index')
            ->with(
                'success',
                'Programación y cupos actualizados correctamente.'
            );
    }
    public function cambiarEstado(
        ProcedimientoProgramacion $programacion
    ) {
        $programacion->update([
            'activo' => !$programacion->activo,
        ]);

        $mensaje = $programacion->activo
            ? 'Programación activada correctamente.'
            : 'Programación desactivada correctamente.';

        return redirect()
            ->route('procedimientos.programaciones.index')
            ->with('success', $mensaje);
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'procedimiento_id' => [
                'required',
                'integer',
                'exists:procedimientos,id',
            ],

            'procedimiento_medico_id' => [
                'required',
                'integer',
                'exists:procedimiento_medicos,id',
            ],

            'fecha_programacion' => [
                'required',
                'date',
            ],

            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'hora_fin' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],

            'intervalo_minutos' => [
                'required',
                'integer',
                'min:5',
                'max:480',
            ],

            'cantidad_maxima' => [
                'nullable',
                'integer',
                'min:1',
                'max:500',
            ],
        ], [
            'procedimiento_id.required' =>
            'Seleccione el procedimiento.',

            'procedimiento_id.exists' =>
            'El procedimiento seleccionado no existe.',

            'procedimiento_medico_id.required' =>
            'Seleccione el médico responsable.',

            'procedimiento_medico_id.exists' =>
            'El médico seleccionado no existe.',

            'hora_inicio.required' =>
            'Ingrese la hora de inicio.',

            'hora_fin.required' =>
            'Ingrese la hora final.',

            'hora_fin.after' =>
            'La hora final debe ser posterior a la hora inicial.',

            'intervalo_minutos.required' =>
            'Ingrese el intervalo entre cupos.',

            'intervalo_minutos.min' =>
            'El intervalo mínimo es de 5 minutos.',

            'fecha_inicio.required' =>
            'Ingrese la fecha de inicio.',

            'fecha_fin.after_or_equal' =>
            'La fecha final no puede ser anterior a la fecha inicial.',

            'cantidad_maxima.min' =>
            'La cantidad máxima debe ser mayor que cero.',

            'fecha_programacion.required' =>
            'Seleccione la fecha de programación.',

            'fecha_programacion.date' =>
            'La fecha de programación no es válida.',
        ]);

        $procedimiento = Procedimiento::findOrFail(
            $datos['procedimiento_id']
        );

        if (!$procedimiento->activo) {
            abort(422, 'El procedimiento seleccionado está inactivo.');
        }

        $medico = ProcedimientoMedico::findOrFail(
            $datos['procedimiento_medico_id']
        );

        if (!$medico->activo) {
            abort(422, 'El médico seleccionado está inactivo.');
        }

        return $datos;
    }

    private function existeCruce(
        array $datos,
        ?int $ignorarId = null
    ): bool {
        $query = ProcedimientoProgramacion::query()
            ->where(
                'procedimiento_medico_id',
                $datos['procedimiento_medico_id']
            )
            ->whereDate(
                'fecha_programacion',
                $datos['fecha_programacion']
            )
            ->where('activo', true)
            ->where(
                'hora_inicio',
                '<',
                $datos['hora_fin']
            )
            ->where(
                'hora_fin',
                '>',
                $datos['hora_inicio']
            );

        if ($ignorarId !== null) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }

    private function generarCupos(
        ProcedimientoProgramacion $programacion
    ): void {
        $horaActual = Carbon::parse(
            (string) $programacion->hora_inicio
        );

        $horaFinal = Carbon::parse(
            (string) $programacion->hora_fin
        );
        $limite = $programacion->cantidad_maxima;
        $cupos = [];

        while (
            $horaActual->lt($horaFinal) &&
            (
                is_null($limite) ||
                count($cupos) < $limite
            )
        ) {
            $cupos[] = [
                'procedimiento_programacion_id' =>
                $programacion->id,

                'hora' => $horaActual->format('H:i:s'),

                'estado' =>
                ProcedimientoCupo::ESTADO_DISPONIBLE,

                'motivo_bloqueo' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $horaActual->addMinutes(
                $programacion->intervalo_minutos
            );
        }

        if (!empty($cupos)) {
            ProcedimientoCupo::insert($cupos);
        }
    }
}
