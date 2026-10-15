<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegurosPanelTvController extends Controller
{
    private const ZONA = 'America/Lima';

    public function index(Request $request)
    {
        $fecha = $this->fecha($request);

        return view('seguros.panel-tv', compact('fecha'));
    }

    public function datos(Request $request)
    {
        $fecha = $this->fecha($request);
        $inicio = Carbon::createFromFormat('!Y-m-d', $fecha, self::ZONA);
        $fin = $inicio->copy()->addDay();

        $db = DB::connection('sigh_produccion');

        $programaciones = $db->table('ProgramacionMedica as p')
            ->join('Servicios as s', 's.IdServicio', '=', 'p.IdServicio')
            ->leftJoin('Medicos as m', 'm.IdMedico', '=', 'p.IdMedico')
            ->leftJoin('Empleados as e', 'e.IdEmpleado', '=', 'm.IdEmpleado')
            ->where('s.IdEstado', 1)
            ->whereIn('s.IdTipoServicio', [1, 5])
            ->whereNotIn('s.IdServicio', [
                521,
                422,
                588,
                483,
                425,
                522,
                546,
                551,
                632,
            ])
            ->where('p.Fecha', '>=', $inicio->format('Ymd'))
            ->where('p.Fecha', '<', $fin->format('Ymd'))
            ->select(
                'p.IdProgramacion',
                'p.HoraInicio',
                'p.HoraFin',
                'p.TiempoPromedioAtencion',
                's.Nombre as Consultorio',
                'e.ApellidoPaterno',
                'e.ApellidoMaterno',
                'e.Nombres'
            )
            ->orderBy('s.Nombre')
            ->orderBy('p.HoraInicio')
            ->orderBy('p.IdProgramacion')
            ->get();

        // Consultar citas por bloques para respetar el límite de SQL Server.
        $citas = collect();

        foreach ($programaciones->pluck('IdProgramacion')->chunk(900) as $ids) {
            $filas = $db->table('Citas')
                ->whereIn('IdProgramacion', $ids->all())
                ->whereIn('IdEstadoCita', [1, 2, 4])
                ->select('IdProgramacion', 'HoraInicio', 'HoraFin')
                ->get();

            $citas = $citas->concat($filas);
        }

        $porProgramacion = $citas->groupBy('IdProgramacion');

        $asignaciones = DB::connection('mysql')
            ->table('seguros_consultorios_asignaciones')
            ->where('fecha', $fecha)
            ->pluck('numero_consultorio', 'id_programacion_sigh');

        $tarjetas = $programaciones->map(function ($p) use (
            $porProgramacion,
            $asignaciones
        ) {
            $desde = $this->minutos($p->HoraInicio);
            $hasta = $this->minutos($p->HoraFin);
            $duracion = (int) $p->TiempoPromedioAtencion;

            $valido = $desde !== null
                && $hasta !== null
                && $hasta >= $desde
                && $duracion > 0;

            $total = 0;
            $ocupados = 0;

            $intervalos = $porProgramacion
                ->get($p->IdProgramacion, collect())
                ->map(fn($cita) => [
                    'inicio' => $this->minutos($cita->HoraInicio),
                    'fin' => $this->minutos($cita->HoraFin),
                ]);

            if ($valido) {
                for (
                    $cursor = $desde;
                    $cursor + $duracion <= $hasta;
                    $cursor += $duracion
                ) {
                    $total++;
                    $finCupo = $cursor + $duracion;

                    $ocupado = $intervalos->contains(
                        fn($cita) =>
                        $cita['inicio'] !== null
                            && $cita['fin'] !== null
                            && $cita['inicio'] < $finCupo
                            && $cita['fin'] > $cursor
                    );

                    if ($ocupado) {
                        $ocupados++;
                    }
                }
            }

            $medico = trim(implode(' ', array_filter([
                trim((string) $p->ApellidoPaterno),
                trim((string) $p->ApellidoMaterno),
                trim((string) $p->Nombres),
            ])));

            return [
                'id' => (int) $p->IdProgramacion,
                'numero_consultorio' => $asignaciones->get($p->IdProgramacion),
                'consultorio' => trim((string) $p->Consultorio),
                'medico' => $medico ?: 'SIN MÉDICO REGISTRADO',
                'inicio' => $this->hora($desde),
                'turno' => $desde === null
                    ? 'SIN TURNO'
                    : ($desde >= 14 * 60 ? 'TARDE' : 'MAÑANA'),
                'fin' => $this->hora($hasta),
                'total' => $valido ? $total : null,
                'libres' => $valido ? $total - $ocupados : null,
                'ocupados' => $valido ? $ocupados : null,
                'valido' => $valido,
            ];
        })->values();

        return response()->json([
            'fecha' => $fecha,
            'actualizado' => Carbon::now(self::ZONA)->format('d/m/Y H:i:s'),
            'tarjetas' => $tarjetas,
        ])->header('Cache-Control', 'no-store');
    }

    private function fecha(Request $request): string
    {
        $datos = $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);

        return $datos['fecha'] ?? Carbon::today(self::ZONA)->toDateString();
    }

    private function minutos($valor): ?int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', trim((string) $valor), $p)) {
            return null;
        }

        $hora = (int) $p[1];
        $minuto = (int) $p[2];

        return $hora < 24 && $minuto < 60
            ? $hora * 60 + $minuto
            : null;
    }

    private function hora(?int $minutos): string
    {
        return $minutos === null
            ? '—'
            : sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }
}
