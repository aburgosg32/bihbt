<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumenOperativoController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        // Filtro múltiple por especialidad
        $especialidadesFiltroSeleccionadas = $request->especialidades ?? [];

        $vista = 'dashboard.vw_consulta_externa_operativa';

        $base = function () use (
            $vista,
            $fechaInicio,
            $fechaFin,
            $especialidadesFiltroSeleccionadas
        ) {
            $query = DB::connection('sigh')
                ->table($vista)
                ->whereBetween(
                    DB::raw('CAST(Fecha AS date)'),
                    [$fechaInicio, $fechaFin]
                );

            if (!empty($especialidadesFiltroSeleccionadas)) {
                $query->whereIn('Especialidad', $especialidadesFiltroSeleccionadas);
            }

            return $query;
        };

        $totalCitas = $base()->count();

        $totalAtendidas = $base()
            ->where('EstadoCita', 'ATENDIDO')
            ->count();

        $totalAnuladas = $base()
            ->whereIn('EstadoCita', ['ANULADA', 'CANCELADA'])
            ->count();

        $totalPendientes = $base()
            ->where('EstadoCita', 'SEPARADA')
            ->count();

        $totalConsultoriosActivos = $base()
            ->whereNotNull('Servicio')
            ->distinct('Servicio')
            ->count('Servicio');

        $totalAdicionales = $base()
            ->where('EsCitaAdicional', 1)
            ->count();

        $avance = $totalCitas > 0
            ? round(($totalAtendidas / $totalCitas) * 100, 1)
            : 0;

        $flujoHorario = $base()
            ->selectRaw("
                LEFT(CONVERT(varchar(8), HoraInicio), 2) + ':00' AS hora,
                COUNT(*) AS total
            ")
            ->whereNotNull('HoraInicio')
            ->groupByRaw("LEFT(CONVERT(varchar(8), HoraInicio), 2) + ':00'")
            ->orderBy('hora')
            ->get();

        $horas = $flujoHorario->pluck('hora');
        $cantidades = $flujoHorario->pluck('total');

        $servicios = $base()
            ->selectRaw("Servicio, COUNT(*) AS total")
            ->whereNotNull('Servicio')
            ->groupBy('Servicio')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $servicioLabels = $servicios->pluck('Servicio');
        $servicioData = $servicios->pluck('total');

        $serviciosAtendidos = $base()
            ->selectRaw("Servicio, COUNT(*) AS total")
            ->where('EstadoCita', 'ATENDIDO')
            ->whereNotNull('Servicio')
            ->groupBy('Servicio')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $servicioAtendidoLabels = $serviciosAtendidos->pluck('Servicio');
        $servicioAtendidoData = $serviciosAtendidos->pluck('total');

        $financiamiento = $base()
            ->selectRaw("FF AS fuente, COUNT(*) AS total")
            ->where('EstadoCita', 'ATENDIDO')
            ->whereNotNull('FF')
            ->groupBy('FF')
            ->orderByDesc('total')
            ->get();

        $financiamientoLabels = $financiamiento->pluck('fuente');
        $financiamientoData = $financiamiento->pluck('total');

        // Lista para el select múltiple
        $especialidadesFiltro = DB::connection('sigh')
            ->table($vista)
            ->select('Especialidad')
            ->whereNotNull('Especialidad')
            ->distinct()
            ->orderBy('Especialidad')
            ->pluck('Especialidad');

        return view('dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'especialidadesFiltro',
            'especialidadesFiltroSeleccionadas',
            'totalCitas',
            'totalAtendidas',
            'totalPendientes',
            'totalAnuladas',
            'totalAdicionales',
            'totalConsultoriosActivos',
            'avance',
            'horas',
            'cantidades',
            'servicioLabels',
            'servicioData',
            'servicioAtendidoLabels',
            'servicioAtendidoData',
            'financiamientoLabels',
            'financiamientoData'
        ));
    }
}
