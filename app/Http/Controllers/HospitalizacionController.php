<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalizacionController extends Controller
{
    public function dashboard(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $queryBase = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_operativa')
            ->whereRaw("FechaIngreso >= CONVERT(date, ?, 120)", [$fechaInicio])
            ->whereRaw("FechaIngreso < DATEADD(day, 1, CONVERT(date, ?, 120))", [$fechaFin]);

        $totalAtenciones = (clone $queryBase)->count();

        $pacientesActivos = (clone $queryBase)
            ->whereNull('FechaEgreso')
            ->whereNull('FechaEgresoAdministrativo')
            ->count();

        $pacientesEgresados = (clone $queryBase)
            ->where(function ($query) {
                $query->whereNotNull('FechaEgreso')
                    ->orWhereNotNull('FechaEgresoAdministrativo');
            })
            ->count();

        $sinCierreAdministrativo = (clone $queryBase)
            ->whereNull('FechaEgresoAdministrativo')
            ->count();

        $serviciosActivosSql = DB::connection('sigh')->select("
            SELECT COUNT(DISTINCT SERVICIOACTUAL) AS total
            FROM dashboard.vw_hospitalizacion_operativa
            WHERE FechaIngreso >= CONVERT(date, ?, 120)
              AND FechaIngreso < DATEADD(day, 1, CONVERT(date, ?, 120))
              AND FechaEgreso IS NULL
              AND FechaEgresoAdministrativo IS NULL
              AND SERVICIOACTUAL IS NOT NULL
        ", [$fechaInicio, $fechaFin]);

        $serviciosActivos = $serviciosActivosSql[0]->total ?? 0;

        $camasOcupadas = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_operativa')
            ->whereNull('FechaEgreso')
            ->whereNull('FechaEgresoAdministrativo')
            ->whereNotNull('CAMAACTUAL')
            ->select('SERVICIOACTUAL', DB::raw('COUNT(*) as total'))
            ->groupBy('SERVICIOACTUAL')
            ->orderByDesc('total')
            ->get();

        $servicios = (clone $queryBase)
            ->whereNull('FechaEgreso')
            ->whereNull('FechaEgresoAdministrativo')
            ->whereNotNull('SERVICIOACTUAL')
            ->select('SERVICIOACTUAL', DB::raw('COUNT(*) as total'))
            ->groupBy('SERVICIOACTUAL')
            ->orderByDesc('total')
            ->get();

        $flujoHorario = (clone $queryBase)
            ->selectRaw('DATEPART(HOUR, HoraIngreso) as hora, COUNT(*) as total')
            ->whereNotNull('HoraIngreso')
            ->groupByRaw('DATEPART(HOUR, HoraIngreso)')
            ->orderBy('hora')
            ->get();

        $financiamiento = (clone $queryBase)
            ->select('FUENTEFINANCIAMIENTO', DB::raw('COUNT(*) as total'))
            ->groupBy('FUENTEFINANCIAMIENTO')
            ->orderByDesc('total')
            ->get();

        return view('hospitalizacion.dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'totalAtenciones',
            'pacientesActivos',
            'pacientesEgresados',
            'sinCierreAdministrativo',
            'serviciosActivos',
            'camasOcupadas',
            'servicios',
            'flujoHorario',
            'financiamiento'
        ));
    }

    public function pacientesActivos(Request $request)
    {
        $anio = $request->input('anio', 2026);
        $servicio = $request->input('servicio');
        $buscar = trim($request->input('buscar', ''));

        $anios = collect([2026]);

        $serviciosSql = DB::connection('sigh')->select("
            SELECT DISTINCT 
                UPPER(SER.Nombre) AS SERVICIOACTUAL
            FROM Atenciones AS A
            LEFT JOIN Servicios AS SER
                ON A.IdServicioEgreso = SER.IdServicio
            WHERE A.IdTipoServicio = 3
              AND A.FechaIngreso >= CONVERT(date, '20260101', 112)
              AND A.FechaEgreso IS NULL
              AND A.FechaEgresoAdministrativo IS NULL
              AND SER.Nombre IS NOT NULL
            ORDER BY SERVICIOACTUAL
        ");

        $servicios = collect($serviciosSql)->pluck('SERVICIOACTUAL');

        $pacientes = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_operativa')
            ->select([
                'IdAtencion',
                'CUENTA',
                'PACIENTE',
                'Edad',
                'TIPOEDAD',
                'SEXO',
                'FechaIngreso',
                'HoraIngreso',
                'SERVICIOINGRESO',
                'SERVICIOACTUAL',
                'CAMAACTUAL',
                'FUENTEFINANCIAMIENTO',
                'MEDICOINGRESO',
            ])
            ->whereNull('FechaEgreso')
            ->whereNull('FechaEgresoAdministrativo')
            ->whereRaw("YEAR(FechaIngreso) = ?", [$anio])
            ->when($servicio, function ($query) use ($servicio) {
                $query->where('SERVICIOACTUAL', $servicio);
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->whereRaw("CAST(CUENTA AS VARCHAR(30)) LIKE ?", ["%{$buscar}%"])
                        ->orWhereRaw("CAST(IdAtencion AS VARCHAR(30)) LIKE ?", ["%{$buscar}%"])
                        ->orWhere('PACIENTE', 'like', "%{$buscar}%")
                        ->orWhere('SERVICIOINGRESO', 'like', "%{$buscar}%")
                        ->orWhere('SERVICIOACTUAL', 'like', "%{$buscar}%")
                        ->orWhere('CAMAACTUAL', 'like', "%{$buscar}%")
                        ->orWhere('FUENTEFINANCIAMIENTO', 'like', "%{$buscar}%")
                        ->orWhere('MEDICOINGRESO', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('SERVICIOACTUAL')
            ->orderByDesc('FechaIngreso')
            ->paginate(25)
            ->withQueryString();

        return view('hospitalizacion.pacientes_activos', compact(
            'pacientes',
            'anios',
            'servicios',
            'anio',
            'servicio',
            'buscar'
        ));
    }

    public function diagnosticos($idAtencion)
    {
        $diagnosticos = DB::connection('sigh')->select("
            SELECT 
                D.CodigoCIE10 AS cie10,
                UPPER(D.Descripcion) AS descripcion
            FROM AtencionesDiagnosticos AD
            INNER JOIN Diagnosticos D
                ON D.IdDiagnostico = AD.IdDiagnostico
            WHERE AD.IdAtencion = ?
            ORDER BY D.CodigoCIE10
        ", [$idAtencion]);

        return response()->json([
            'ok' => true,
            'idAtencion' => $idAtencion,
            'diagnosticos' => $diagnosticos
        ]);
    }
    public function camasServicio(Request $request)
    {
        $servicio = $request->input('servicio');
        $estado = $request->input('estado');
        $buscar = trim($request->input('buscar', ''));

        $servicios = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_camas_servicio')
            ->whereNotNull('SERVICIO')
            ->select('SERVICIO')
            ->distinct()
            ->orderBy('SERVICIO')
            ->pluck('SERVICIO');

        $estados = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_camas_servicio')
            ->whereNotNull('ESTADO_OPERATIVO')
            ->select('ESTADO_OPERATIVO')
            ->distinct()
            ->orderBy('ESTADO_OPERATIVO')
            ->pluck('ESTADO_OPERATIVO');

        $camas = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_camas_servicio')
            ->when($servicio, function ($query) use ($servicio) {
                $query->where('SERVICIO', $servicio);
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('ESTADO_OPERATIVO', $estado);
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('CAMA', 'like', "%{$buscar}%")
                        ->orWhere('SERVICIO', 'like', "%{$buscar}%")
                        ->orWhere('ESPECIALIDAD', 'like', "%{$buscar}%")
                        ->orWhere('TIPO_CAMA', 'like', "%{$buscar}%")
                        ->orWhere('ESTADO_CAMA', 'like', "%{$buscar}%")
                        ->orWhere('PACIENTE', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('SERVICIO')
            ->orderBy('CAMA')
            ->paginate(30)
            ->withQueryString();

        $resumen = DB::connection('sigh')
            ->table('dashboard.vw_hospitalizacion_camas_servicio')
            ->selectRaw("
            COUNT(*) AS total_camas,
            SUM(CASE WHEN ESTADO_OPERATIVO = 'OCUPADA' THEN 1 ELSE 0 END) AS ocupadas,
            SUM(CASE WHEN ESTADO_OPERATIVO = 'LIBRE' THEN 1 ELSE 0 END) AS libres
        ")
            ->first();

        $resumenServicio = null;

        if ($servicio) {
            $resumenServicio = DB::connection('sigh')
                ->table('dashboard.vw_hospitalizacion_camas_servicio')
                ->where('SERVICIO', $servicio)
                ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN ESTADO_OPERATIVO = 'OCUPADA' THEN 1 ELSE 0 END) AS ocupadas,
                SUM(CASE WHEN ESTADO_OPERATIVO = 'LIBRE' THEN 1 ELSE 0 END) AS libres
            ")
                ->first();
        }

        return view('hospitalizacion.camas_servicio', compact(
            'camas',
            'servicios',
            'estados',
            'servicio',
            'estado',
            'buscar',
            'resumen',
            'resumenServicio'
        ));
    }
}
