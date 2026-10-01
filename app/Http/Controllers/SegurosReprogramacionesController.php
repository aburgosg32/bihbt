<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SegurosReprogramacionesController extends Controller
{
    private const ZONA = 'America/Lima';

    // Estados que ocupan un cupo, según la lógica usada en Disponibilidad.
    private const ESTADOS_OCUPADOS = [1, 2, 4];

    public function index(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['nullable', 'integer', 'min:1'],
        ]);

        $cuenta = isset($datos['cuenta']) ? (int) $datos['cuenta'] : null;
        $citas = collect();

        if ($cuenta !== null) {
            $citas = DB::connection('sigh')
                ->table('Atenciones as a')
                ->join('Citas as c', 'c.IdAtencion', '=', 'a.IdAtencion')
                ->leftJoin('Pacientes as pac', 'pac.IdPaciente', '=', 'a.IdPaciente')
                ->leftJoin('FuentesFinanciamiento as ff', 'ff.IdFuenteFinanciamiento', '=', 'a.IdFuenteFinanciamiento')
                ->leftJoin('Especialidades as e', 'e.IdEspecialidad', '=', 'c.IdEspecialidad')
                ->leftJoin('Servicios as s', 's.IdServicio', '=', 'c.IdServicio')
                ->leftJoin('Medicos as m', 'm.IdMedico', '=', 'c.IdMedico')
                ->leftJoin('Empleados as emp', 'emp.IdEmpleado', '=', 'm.IdEmpleado')
                ->where('a.IdCuentaAtencion', $cuenta)
                ->select(
                    'c.IdCita',
                    'c.IdEstadoCita',
                    'c.Fecha',
                    'c.HoraInicio',
                    'c.HoraFin',
                    'a.HoraEgreso',
                    'pac.NroDocumento',
                    'pac.ApellidoPaterno as PacientePaterno',
                    'pac.ApellidoMaterno as PacienteMaterno',
                    'pac.PrimerNombre',
                    'pac.SegundoNombre',
                    'pac.TercerNombre',
                    'ff.Descripcion as Financiamiento',
                    'e.Nombre as Especialidad',
                    's.Nombre as Servicio',
                    'emp.ApellidoPaterno as MedicoPaterno',
                    'emp.ApellidoMaterno as MedicoMaterno',
                    'emp.Nombres as MedicoNombres'
                )
                ->orderByDesc('c.Fecha')
                ->orderByDesc('c.HoraInicio')
                ->get()
                ->map(function ($cita) {
                    $cita->paciente = trim(implode(' ', array_filter([
                        $cita->PacientePaterno,
                        $cita->PacienteMaterno,
                        $cita->PrimerNombre,
                        $cita->SegundoNombre,
                        $cita->TercerNombre,
                    ], fn($parte) => trim((string) $parte) !== '')));

                    $cita->medico = trim(implode(' ', array_filter([
                        $cita->MedicoPaterno,
                        $cita->MedicoMaterno,
                        $cita->MedicoNombres,
                    ], fn($parte) => trim((string) $parte) !== '')));

                    $cita->fecha_mostrar = Carbon::parse($cita->Fecha)->format('d/m/Y');
                    $cita->reprogramable =
                        in_array((int) $cita->IdEstadoCita, [1, 4], true)
                        && $this->sinEgreso($cita->HoraEgreso);

                    return $cita;
                });
        }

        return view('seguros.reprogramaciones', compact('cuenta', 'citas'));
    }

    public function programaciones(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
        ]);

        $fecha = Carbon::createFromFormat('!Y-m-d', $datos['fecha'], self::ZONA);
        $hoy = Carbon::today(self::ZONA);

        if ($fecha->lt($hoy)) {
            return response()->json([]);
        }

        $inicio = $fecha->format('Ymd');
        $fin = $fecha->copy()->addDay()->format('Ymd');

        $programaciones = DB::connection('sigh')
            ->table('ProgramacionMedica as p')
            ->join('Servicios as s', 's.IdServicio', '=', 'p.IdServicio')
            ->join('Medicos as m', 'm.IdMedico', '=', 'p.IdMedico')
            ->join('Empleados as emp', 'emp.IdEmpleado', '=', 'm.IdEmpleado')
            ->where('p.Fecha', '>=', $inicio)
            ->where('p.Fecha', '<', $fin)
            ->where('s.IdTipoServicio', 1)
            ->where('s.IdEstado', 1)
            ->select(
                'p.IdProgramacion',
                'p.HoraInicio',
                'p.HoraFin',
                'p.TiempoPromedioAtencion',
                's.Nombre as Servicio',
                'emp.ApellidoPaterno',
                'emp.ApellidoMaterno',
                'emp.Nombres'
            )
            ->orderBy('s.Nombre')
            ->orderBy('p.HoraInicio')
            ->get()
            ->filter(function ($p) {
                return $this->minutos($p->HoraInicio) !== null
                    && $this->minutos($p->HoraFin) !== null
                    && (int) $p->TiempoPromedioAtencion > 0;
            })
            ->map(function ($p) {
                $medico = trim(implode(' ', array_filter([
                    $p->ApellidoPaterno,
                    $p->ApellidoMaterno,
                    $p->Nombres,
                ], fn($parte) => trim((string) $parte) !== '')));

                return [
                    'id' => (int) $p->IdProgramacion,
                    'etiqueta' => trim($p->Servicio)
                        . ' — ' . ($medico ?: 'Médico N.º ' . $p->IdProgramacion)
                        . ' (' . $this->hora($p->HoraInicio)
                        . '–' . $this->hora($p->HoraFin) . ')',
                ];
            })
            ->values();

        return response()->json($programaciones);
    }

    public function horas(Request $request)
    {
        $datos = $request->validate([
            'programacion' => ['required', 'integer', 'min:1'],
            'id_cita' => ['required', 'integer', 'min:1'],
            'cuenta' => ['required', 'integer', 'min:1'],
        ]);

        $sigh = DB::connection('sigh');

        $cita = $sigh->table('Citas as c')
            ->join('Atenciones as a', 'a.IdAtencion', '=', 'c.IdAtencion')
            ->where('c.IdCita', $datos['id_cita'])
            ->where('a.IdCuentaAtencion', $datos['cuenta'])
            ->whereIn('c.IdEstadoCita', [1, 4])
            ->where(function ($query) {
                $query->whereNull('a.HoraEgreso')
                    ->orWhere('a.HoraEgreso', '')
                    ->orWhere('a.HoraEgreso', '__:__');
            })
            ->exists();

        if (! $cita) {
            return response()->json(['message' => 'La cita no puede reprogramarse.'], 422);
        }

        $programacion = $this->buscarProgramacion($sigh, (int) $datos['programacion']);

        if (! $programacion) {
            return response()->json(['message' => 'Programación no disponible.'], 422);
        }

        return response()->json(
            $this->cuposLibres($sigh, $programacion, (int) $datos['id_cita'])
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['required', 'integer', 'min:1'],
            'id_cita' => ['required', 'integer', 'min:1'],
            'programacion' => ['required', 'integer', 'min:1'],
            'hora_inicio' => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ]);

        $sigh = DB::connection('sigh');

        // Bloqueamos la programación y volvemos a comprobar el cupo antes de escribir.
        $sigh->transaction(function () use ($sigh, $datos) {
            $programacion = $this->buscarProgramacion(
                $sigh,
                (int) $datos['programacion'],
                true
            );

            if (! $programacion) {
                throw ValidationException::withMessages([
                    'programacion' => 'La programación seleccionada ya no está disponible.',
                ]);
            }

            $atencion = $sigh->table('Atenciones')
                ->where('IdCuentaAtencion', $datos['cuenta'])
                ->lockForUpdate()
                ->first();

            if (! $atencion || ! $this->sinEgreso($atencion->HoraEgreso)) {
                throw ValidationException::withMessages([
                    'cuenta' => 'La cuenta no existe o el paciente ya fue atendido.',
                ]);
            }

            $cita = $sigh->table('Citas')
                ->where('IdCita', $datos['id_cita'])
                ->where('IdAtencion', $atencion->IdAtencion)
                ->lockForUpdate()
                ->first();

            if (! $cita || ! in_array((int) $cita->IdEstadoCita, [1, 4], true)) {
                throw ValidationException::withMessages([
                    'id_cita' => 'La cita ya no puede reprogramarse.',
                ]);
            }

            $libres = $this->cuposLibres(
                $sigh,
                $programacion,
                (int) $cita->IdCita
            );

            if (! in_array($datos['hora_inicio'], $libres, true)) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'Ese cupo ya no está disponible. Seleccione otra hora.',
                ]);
            }

            $duracion = (int) $programacion->TiempoPromedioAtencion;
            $finMinutos = $this->minutos($datos['hora_inicio']) + $duracion;
            $horaFin = sprintf('%02d:%02d', intdiv($finMinutos, 60), $finMinutos % 60);
            $fecha = Carbon::parse($programacion->Fecha)->format('Ymd');

            $sigh->table('Citas')
                ->where('IdCita', $cita->IdCita)
                ->update([
                    'Fecha' => $fecha,
                    'HoraInicio' => $datos['hora_inicio'],
                    'HoraFin' => $horaFin,
                    'IdMedico' => $programacion->IdMedico,
                    'IdEspecialidad' => $programacion->IdEspecialidadServicio,
                    'IdServicio' => $programacion->IdServicio,
                    'IdProgramacion' => $programacion->IdProgramacion,
                ]);

            $sigh->table('Atenciones')
                ->where('IdAtencion', $atencion->IdAtencion)
                ->update([
                    'FechaIngreso' => $fecha,
                    'HoraIngreso' => $datos['hora_inicio'],
                    'IdMedicoIngreso' => $programacion->IdMedico,
                    'IdEspecialidadMedico' => $programacion->IdEspecialidadServicio,
                    'IdServicioIngreso' => $programacion->IdServicio,
                ]);
        }, 3);

        return redirect()
            ->route('seguros.reprogramaciones.index', ['cuenta' => $datos['cuenta']])
            ->with('success', 'La cita se reprogramó correctamente.');
    }

    private function buscarProgramacion($sigh, int $id, bool $bloquear = false)
    {
        $query = $sigh->table('ProgramacionMedica as p')
            ->join('Servicios as s', 's.IdServicio', '=', 'p.IdServicio')
            ->where('p.IdProgramacion', $id)
            ->where('s.IdTipoServicio', 1)
            ->where('s.IdEstado', 1)
            ->select(
                'p.IdProgramacion',
                'p.IdMedico',
                'p.IdServicio',
                'p.Fecha',
                'p.HoraInicio',
                'p.HoraFin',
                'p.TiempoPromedioAtencion',
                's.IdEspecialidad as IdEspecialidadServicio'
            );

        if ($bloquear) {
            $query->lockForUpdate();
        }

        $programacion = $query->first();

        if (! $programacion) {
            return null;
        }

        $fecha = Carbon::parse($programacion->Fecha, self::ZONA)->startOfDay();

        return $fecha->gte(Carbon::today(self::ZONA))
            ? $programacion
            : null;
    }

    private function cuposLibres($sigh, $programacion, int $idCita): array
    {
        $inicio = $this->minutos($programacion->HoraInicio);
        $fin = $this->minutos($programacion->HoraFin);
        $duracion = (int) $programacion->TiempoPromedioAtencion;

        if ($inicio === null || $fin === null || $duracion <= 0 || $inicio >= $fin) {
            return [];
        }

        $fecha = Carbon::parse($programacion->Fecha, self::ZONA)->format('Y-m-d');
        $ahora = Carbon::now(self::ZONA);

        $ocupadas = $sigh->table('Citas')
            ->where('IdProgramacion', $programacion->IdProgramacion)
            ->where('IdCita', '<>', $idCita)
            ->whereIn('IdEstadoCita', self::ESTADOS_OCUPADOS)
            ->select('HoraInicio', 'HoraFin')
            ->get();

        $libres = [];

        for ($minuto = $inicio; $minuto + $duracion <= $fin; $minuto += $duracion) {
            $inicioCupo = sprintf('%02d:%02d', intdiv($minuto, 60), $minuto % 60);
            $instante = Carbon::parse($fecha . ' ' . $inicioCupo, self::ZONA);

            if ($instante->lte($ahora)) {
                continue;
            }

            $finCupo = $minuto + $duracion;

            $ocupado = $ocupadas->contains(function ($cita) use ($minuto, $finCupo) {
                $inicioCita = $this->minutos($cita->HoraInicio);
                $finCita = $this->minutos($cita->HoraFin);

                return $inicioCita !== null
                    && $finCita !== null
                    && $inicioCita < $finCupo
                    && $finCita > $minuto;
            });

            if (! $ocupado) {
                $libres[] = $inicioCupo;
            }
        }

        return $libres;
    }

    private function sinEgreso($valor): bool
    {
        return in_array(trim((string) $valor), ['', '__:__'], true);
    }

    private function hora($valor): ?string
    {
        $minutos = $this->minutos($valor);

        return $minutos === null
            ? null
            : sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    private function minutos($valor): ?int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', trim((string) $valor), $partes)) {
            return null;
        }

        $hora = (int) $partes[1];
        $minuto = (int) $partes[2];

        return $hora < 24 && $minuto < 60
            ? $hora * 60 + $minuto
            : null;
    }
}
