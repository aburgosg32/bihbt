<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegurosDisponibilidadController extends Controller
{
    public function index(Request $request)
    {
        $especialidadesExcluidas = [
            93,
            104,
            58,
            62,
            94,
            36,
            2,
            63,
            74,
            92,
            126,
            50,
            71,
            105,
            75,
            8,
            76,
            106,
            60,
            135,
            83,
            64,
            65,
            11,
            80,
            84,
            85,
            66,
            67,
            107,
            68,
            90,
            81,
            79,
            82,
            95,
            55,
            69,
            109,
            86,
            70,
            56,
            130,
            57,
            87,
            54,
            131,
            127,
            40,
            72,
            111,
            521,
        ];

        $request->validate([
            'especialidad' => ['nullable', 'integer', 'min:1'],
        ]);

        $sigh = DB::connection('sigh_produccion');
        $zonaHoraria = 'America/Lima';

        $especialidades = $sigh->table('Especialidades')
            ->whereNotIn('IdEspecialidad', $especialidadesExcluidas)
            ->select('IdEspecialidad', 'Nombre')
            ->orderBy('Nombre')
            ->get();

        $idEspecialidad = $request->filled('especialidad')
            ? (int) $request->input('especialidad')
            : null;

        if (
            $idEspecialidad !== null
            && in_array($idEspecialidad, $especialidadesExcluidas, true)
        ) {
            abort(404);
        }

        $resultados = collect();
        $consultoriosSinDisponibilidad = collect();

        if ($idEspecialidad !== null) {
            $ahora = Carbon::now($zonaHoraria);
            $hoy = $ahora->copy()->startOfDay();

            // Incluye hoy y los siguientes 29 días.
            $finExclusivo = $hoy->copy()->addDays(30);

            $programaciones = $sigh->table('ProgramacionMedica as p')
                ->join(
                    'Especialidades as e',
                    'e.IdEspecialidad',
                    '=',
                    'p.IdEspecialidad'
                )
                ->join('Servicios as s', function ($join) {
                    $join->on('s.IdServicio', '=', 'p.IdServicio')
                        ->where('s.IdEstado', '=', 1)
                        ->whereIn('s.IdTipoServicio', [1, 5]);
                })
                ->leftJoin('Medicos as m', 'm.IdMedico', '=', 'p.IdMedico')
                ->leftJoin(
                    'Empleados as emp',
                    'emp.IdEmpleado',
                    '=',
                    'm.IdEmpleado'
                )
                ->where('p.IdEspecialidad', $idEspecialidad)
                ->where('p.Fecha', '>=', $hoy->format('Ymd'))
                ->where('p.Fecha', '<', $finExclusivo->format('Ymd'))
                ->select(
                    'p.IdProgramacion',
                    'p.IdEspecialidad',
                    'p.IdServicio',
                    'p.IdMedico',
                    'p.Fecha',
                    'p.HoraInicio',
                    'p.HoraFin',
                    'p.TiempoPromedioAtencion',
                    'e.Nombre as Especialidad',
                    's.Nombre as Consultorio',
                    'emp.ApellidoPaterno',
                    'emp.ApellidoMaterno',
                    'emp.Nombres'
                )
                ->orderBy('p.Fecha')
                ->orderBy('p.HoraInicio')
                ->get();

            // Estados: 1 = Separada, 2 = Atendido, 4 = Pagada.
            $citasPorProgramacion = collect();

            foreach (
                $programaciones->pluck('IdProgramacion')->chunk(900) as $ids
            ) {
                $citas = $sigh->table('Citas')
                    ->whereIn('IdProgramacion', $ids->all())
                    ->whereIn('IdEstadoCita', [1, 2, 4])
                    ->select(
                        'IdProgramacion',
                        'HoraInicio',
                        'HoraFin'
                    )
                    ->get();

                foreach ($citas as $cita) {
                    $id = (int) $cita->IdProgramacion;

                    $citasPorProgramacion->put(
                        $id,
                        $citasPorProgramacion
                            ->get($id, collect())
                            ->push($cita)
                    );
                }
            }

            $calculados = $programaciones->map(function ($programacion) use (
                $citasPorProgramacion,
                $ahora,
                $zonaHoraria
            ) {
                $fecha = Carbon::parse(
                    $programacion->Fecha,
                    $zonaHoraria
                )->toDateString();

                $inicio = $this->hora($programacion->HoraInicio);
                $fin = $this->hora($programacion->HoraFin);
                $duracion = (int) $programacion->TiempoPromedioAtencion;

                $libres = [];
                $totalCupos = 0;

                if (
                    $inicio !== null
                    && $fin !== null
                    && $duracion > 0
                ) {
                    $cursor = Carbon::parse(
                        $fecha . ' ' . $inicio,
                        $zonaHoraria
                    );

                    $limite = Carbon::parse(
                        $fecha . ' ' . $fin,
                        $zonaHoraria
                    );

                    $citas = $citasPorProgramacion->get(
                        (int) $programacion->IdProgramacion,
                        collect()
                    );

                    while (
                        $cursor->copy()
                        ->addMinutes($duracion)
                        ->lte($limite)
                    ) {
                        $finCupo = $cursor->copy()
                            ->addMinutes($duracion);

                        $totalCupos++;

                        // Una cita que se cruza con el intervalo ocupa el cupo.
                        $ocupado = $citas->contains(
                            function ($cita) use (
                                $fecha,
                                $cursor,
                                $finCupo,
                                $zonaHoraria
                            ) {
                                $inicioCita = $this->hora(
                                    $cita->HoraInicio
                                );

                                $finCita = $this->hora(
                                    $cita->HoraFin
                                );

                                if (
                                    $inicioCita === null
                                    || $finCita === null
                                ) {
                                    return false;
                                }

                                $desde = Carbon::parse(
                                    $fecha . ' ' . $inicioCita,
                                    $zonaHoraria
                                );

                                $hasta = Carbon::parse(
                                    $fecha . ' ' . $finCita,
                                    $zonaHoraria
                                );

                                return $desde->lt($finCupo)
                                    && $hasta->gt($cursor);
                            }
                        );

                        // Muestra todos los cupos libres, aunque la hora ya haya pasado.
                        if (! $ocupado) {
                            $libres[] = $cursor->format('H:i');
                        }

                        $cursor = $finCupo;
                    }
                }

                $nombreMedico = trim(implode(' ', array_filter([
                    trim((string) $programacion->ApellidoPaterno),
                    trim((string) $programacion->ApellidoMaterno),
                    trim((string) $programacion->Nombres),
                ])));

                return (object) [
                    'id_servicio' => (int) $programacion->IdServicio,
                    'id_especialidad' => (int) $programacion->IdEspecialidad,
                    'fecha' => Carbon::parse(
                        $programacion->Fecha,
                        $zonaHoraria
                    )->format('d/m/Y'),
                    'fecha_orden' => $fecha,
                    'inicio' => $inicio ?? '—',
                    'fin' => $fin ?? '—',
                    'especialidad' => $programacion->Especialidad,
                    'consultorio' => $programacion->Consultorio,
                    'medico' => $nombreMedico !== ''
                        ? $nombreMedico
                        : 'Médico N.º ' . $programacion->IdMedico,
                    'total_cupos' => $totalCupos,
                    'cupos_libres' => count($libres),
                    'primera_hora' => $libres[0] ?? null,
                ];
            });

            // Tabla: únicamente programaciones con cupos libres.
            $resultados = $calculados
                ->filter(fn($fila) => $fila->cupos_libres > 0)
                ->values();

            $idsConsultoriosConCupos = $resultados
                ->pluck('id_servicio')
                ->unique();

            // Etiquetas: consultorios externos activos sin cupos.
            $consultoriosSinDisponibilidad = $sigh->table('Servicios')
                ->where('IdEspecialidad', $idEspecialidad)
                ->where('IdEstado', 1)
                ->where('IdTipoServicio', 1)
                ->select('IdServicio', 'Nombre')
                ->orderBy('Nombre')
                ->get()
                ->reject(
                    fn($servicio) =>
                    $idsConsultoriosConCupos->contains(
                        (int) $servicio->IdServicio
                    )
                )
                ->values();
        }

        return view('seguros.disponibilidad', compact(
            'especialidades',
            'idEspecialidad',
            'resultados',
            'consultoriosSinDisponibilidad'
        ));
    }

    private function hora(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        // Acepta horas como 8:00 y 08:00.
        if (! preg_match('/^(\d{1,2}):(\d{2})/', $valor, $partes)) {
            return null;
        }

        $hora = (int) $partes[1];
        $minuto = (int) $partes[2];

        if ($hora > 23 || $minuto > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hora, $minuto);
    }
}
