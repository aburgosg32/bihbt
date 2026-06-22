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
    public function pacientesConsultorio(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $consultorio = $request->consultorio;
        $estado = $request->estado;
        $buscar = trim($request->buscar ?? '');

        $consultorios = DB::connection('sigh')
            ->table('dashboard.vw_consulta_externa_pacientes')
            ->select('CONSULTORIO')
            ->distinct()
            ->orderBy('CONSULTORIO')
            ->pluck('CONSULTORIO');

        $query = DB::connection('sigh')
            ->table('dashboard.vw_consulta_externa_pacientes')
            ->whereBetween('FECHA_INGRESO', [$fechaInicio, $fechaFin]);

        if ($consultorio) {
            $query->where('CONSULTORIO', $consultorio);
        }

        if ($estado) {
            $query->where('ESTADO_PACIENTE', $estado);
        }

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('NROCUENTA', 'like', "%{$buscar}%")
                    ->orWhere('PACIENTE', 'like', "%{$buscar}%")
                    ->orWhere('HISTORIA_CLINICA', 'like', "%{$buscar}%")
                    ->orWhere('TELEFONO', 'like', "%{$buscar}%");
            });
        }

        $pacientes = $query
            ->orderBy('CONSULTORIO')
            ->orderBy('HORA_INGRESO')
            ->paginate(25)
            ->withQueryString();

        $resumen = DB::connection('sigh')
            ->table('dashboard.vw_consulta_externa_pacientes')
            ->whereBetween('FECHA_INGRESO', [$fechaInicio, $fechaFin]);

        if ($consultorio) {
            $resumen->where('CONSULTORIO', $consultorio);
        }

        $totalPacientes = (clone $resumen)->count();

        $totalAtendidos = (clone $resumen)
            ->where('ESTADO_PACIENTE', 'ATENDIDO')
            ->count();

        $totalPendientes = (clone $resumen)
            ->where('ESTADO_PACIENTE', 'PENDIENTE')
            ->count();

        $consultoriosActivos = (clone $resumen)
            ->distinct('CONSULTORIO')
            ->count('CONSULTORIO');

        return view('consulta_externa.pacientes_consultorio', compact(
            'pacientes',
            'consultorios',
            'fechaInicio',
            'fechaFin',
            'consultorio',
            'estado',
            'buscar',
            'totalPacientes',
            'totalAtendidos',
            'totalPendientes',
            'consultoriosActivos'
        ));
    }
}
