<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SegurosCuposController extends Controller
{
    private const CONEXION = 'sigh';
    private const ESTADOS_OCUPADOS = [1, 2, 4];

    public function index(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['nullable', 'integer', 'min:1'],
            'programacion' => ['nullable', 'integer', 'min:1'],
        ]);

        $cuenta = $datos['cuenta'] ?? null;
        $programaciones = collect();
        $programacion = null;
        $citas = collect();
        $resumen = ['total' => 0, 'ocupados' => 0, 'libres' => 0];

        $db = DB::connection(self::CONEXION);

        if ($cuenta) {
            // Una cuenta puede estar vinculada a más de una programación.
            $ids = $db->table('Atenciones as a')
                ->join('Citas as c', 'c.IdAtencion', '=', 'a.IdAtencion')
                ->where('a.IdCuentaAtencion', $cuenta)
                ->whereNotNull('c.IdProgramacion')
                ->distinct()
                ->pluck('c.IdProgramacion');

            $programaciones = $db->table('ProgramacionMedica as p')
                ->leftJoin('Servicios as s', 's.IdServicio', '=', 'p.IdServicio')
                ->leftJoin('Especialidades as e', 'e.IdEspecialidad', '=', 's.IdEspecialidad')
                ->leftJoin('Medicos as m', 'm.IdMedico', '=', 'p.IdMedico')
                ->leftJoin('Empleados as emp', 'emp.IdEmpleado', '=', 'm.IdEmpleado')
                ->whereIn('p.IdProgramacion', $ids)
                ->select(
                    'p.IdProgramacion',
                    'p.Fecha',
                    'p.HoraInicio',
                    'p.HoraFin',
                    'p.TiempoPromedioAtencion',
                    's.Nombre as Servicio',
                    'e.Nombre as Especialidad',
                    'emp.ApellidoPaterno',
                    'emp.ApellidoMaterno',
                    'emp.Nombres'
                )
                ->orderByDesc('p.Fecha')
                ->orderByDesc('p.IdProgramacion')
                ->get()
                ->map(function ($p) {
                    $p->Medico = trim(
                        ($p->ApellidoPaterno ?? '') . ' ' .
                            ($p->ApellidoMaterno ?? '') . ' ' .
                            ($p->Nombres ?? '')
                    );

                    return $p;
                });

            $idSeleccionado = $datos['programacion']
                ?? $programaciones->first()?->IdProgramacion;

            $programacion = $programaciones->firstWhere(
                'IdProgramacion',
                $idSeleccionado
            );

            if (isset($datos['programacion']) && !$programacion) {
                throw ValidationException::withMessages([
                    'programacion' => 'La programación no pertenece a la cuenta buscada.',
                ]);
            }

            if ($programacion) {
                $citas = $db->table('Citas as c')
                    ->leftJoin('Atenciones as a', 'a.IdAtencion', '=', 'c.IdAtencion')
                    ->leftJoin('Pacientes as pac', 'pac.IdPaciente', '=', 'a.IdPaciente')
                    ->leftJoin(
                        'FuentesFinanciamiento as ff',
                        'ff.IdFuenteFinanciamiento',
                        '=',
                        'a.IdFuenteFinanciamiento'
                    )
                    ->where('c.IdProgramacion', $programacion->IdProgramacion)
                    ->select(
                        'c.IdCita',
                        'c.IdEstadoCita',
                        'c.Fecha',
                        'c.HoraInicio',
                        'c.HoraFin',
                        'a.IdCuentaAtencion',
                        'ff.Descripcion as Financiamiento',
                        'pac.ApellidoPaterno',
                        'pac.ApellidoMaterno',
                        'pac.PrimerNombre',
                        'pac.SegundoNombre'
                    )
                    ->orderBy('c.HoraInicio')
                    ->orderBy('c.IdCita')
                    ->get()
                    ->map(function ($c) {
                        $c->Paciente = trim(
                            ($c->ApellidoPaterno ?? '') . ' ' .
                                ($c->ApellidoMaterno ?? '') . ' ' .
                                ($c->PrimerNombre ?? '') . ' ' .
                                ($c->SegundoNombre ?? '')
                        );

                        return $c;
                    });

                $resumen = $this->resumen($programacion, $citas);
            }
        }

        return view('seguros.cupos', compact(
            'cuenta',
            'programaciones',
            'programacion',
            'citas',
            'resumen'
        ));
    }

    public function actualizar(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['required', 'integer', 'min:1'],
            'programacion' => ['required', 'integer', 'min:1'],
            'accion' => ['required', 'in:sumar,restar,cerrar,desmontar'],
        ]);

        $db = DB::connection(self::CONEXION);

        $mensaje = $db->transaction(function () use ($db, $datos) {
            $p = $db->table('ProgramacionMedica')
                ->where('IdProgramacion', $datos['programacion'])
                ->lockForUpdate()
                ->first();

            if (!$p) {
                $this->error('No se encontró la programación.');
            }

            $pertenece = $db->table('Citas as c')
                ->join('Atenciones as a', 'a.IdAtencion', '=', 'c.IdAtencion')
                ->where('c.IdProgramacion', $p->IdProgramacion)
                ->where('a.IdCuentaAtencion', $datos['cuenta'])
                ->exists();

            if (!$pertenece) {
                $this->error('La programación no pertenece a la cuenta indicada.');
            }

            $inicio = $this->minutos($p->HoraInicio);
            $fin = $this->minutos($p->HoraFin);
            $duracion = (int) $p->TiempoPromedioAtencion;

            if (
                $inicio === null || $fin === null ||
                $duracion <= 0 || $fin < $inicio
            ) {
                $this->error('El horario o el tiempo promedio no es válido.');
            }

            // Considerar todas las citas al proteger el horario existente.
            $citas = $db->table('Citas')
                ->where('IdProgramacion', $p->IdProgramacion)
                ->lockForUpdate()
                ->get(['IdCita', 'HoraInicio', 'HoraFin']);

            $ultimoFin = null;

            foreach ($citas as $cita) {
                $finCita = $this->minutos($cita->HoraFin);

                if ($finCita === null) {
                    $this->error(
                        'La cita ' . $cita->IdCita .
                            ' tiene una hora final inválida. Revísela antes de ajustar cupos.'
                    );
                }

                $ultimoFin = max($ultimoFin ?? $finCita, $finCita);
            }

            switch ($datos['accion']) {
                case 'sumar':
                    $nuevoFin = $fin + $duracion;
                    break;

                case 'restar':
                    $nuevoFin = $fin - $duracion;
                    break;

                default:
                    if ($ultimoFin === null) {
                        $this->error('No hay citas para determinar la hora de cierre.');
                    }

                    $nuevoFin = $ultimoFin;
                    break;
            }

            if ($nuevoFin < $inicio) {
                $this->error('La hora final no puede ser anterior a la hora inicial.');
            }

            if ($nuevoFin >= 1440) {
                $this->error('El ajuste no puede extender la programación al día siguiente.');
            }

            if ($ultimoFin !== null && $nuevoFin < $ultimoFin) {
                $this->error(
                    'No se puede disminuir: una cita termina a las ' .
                        $this->hora($ultimoFin) .
                        '. Reprograme esa cita antes de reducir el horario.'
                );
            }

            $horaAnterior = $this->hora($fin);
            $horaNueva = $this->hora($nuevoFin);

            $db->table('ProgramacionMedica')
                ->where('IdProgramacion', $p->IdProgramacion)
                ->update(['HoraFin' => $horaNueva]);

            return 'Programación ' . $p->IdProgramacion .
                ': hora final ' . $horaAnterior . ' → ' . $horaNueva . '.';
        }, 3);

        return redirect()
            ->route('seguros.cupos.index', [
                'cuenta' => $datos['cuenta'],
                'programacion' => $datos['programacion'],
            ])
            ->with('success', $mensaje);
    }

    private function resumen($p, $citas): array
    {
        $inicio = $this->minutos($p->HoraInicio);
        $fin = $this->minutos($p->HoraFin);
        $duracion = (int) $p->TiempoPromedioAtencion;

        if (
            $inicio === null || $fin === null ||
            $duracion <= 0 || $fin <= $inicio
        ) {
            return ['total' => 0, 'ocupados' => 0, 'libres' => 0];
        }

        $total = 0;
        $ocupados = 0;

        // Contar intervalos ocupados, evitando duplicar citas superpuestas.
        for ($minuto = $inicio; $minuto + $duracion <= $fin; $minuto += $duracion) {
            $total++;

            $ocupado = $citas->contains(function ($c) use ($minuto, $duracion) {
                if (!in_array((int) $c->IdEstadoCita, self::ESTADOS_OCUPADOS, true)) {
                    return false;
                }

                $desde = $this->minutos($c->HoraInicio);
                $hasta = $this->minutos($c->HoraFin);

                return $desde !== null && $hasta !== null
                    && $desde < $minuto + $duracion
                    && $hasta > $minuto;
            });

            if ($ocupado) {
                $ocupados++;
            }
        }

        return [
            'total' => $total,
            'ocupados' => $ocupados,
            'libres' => $total - $ocupados,
        ];
    }

    private function minutos($valor): ?int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})/', trim((string) $valor), $partes)) {
            return null;
        }

        $hora = (int) $partes[1];
        $minuto = (int) $partes[2];

        return $hora < 24 && $minuto < 60
            ? $hora * 60 + $minuto
            : null;
    }

    private function hora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    private function error(string $mensaje): void
    {
        throw ValidationException::withMessages([
            'cupos' => $mensaje,
        ]);
    }
}
