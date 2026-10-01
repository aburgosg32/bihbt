<?php

namespace App\Http\Controllers;

use App\Models\Procedimiento;
use App\Models\ProcedimientoCita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProcedimientoReporteController extends Controller
{
    public function index(Request $request)
    {
        $citas = $this->construirConsulta($request)
            ->paginate(20)
            ->appends($request->query());

        $procedimientos = Procedimiento::query()
            ->with('servicio')
            ->orderBy('nombre')
            ->get();

        return view(
            'procedimientos.procedimientos.reportes.index',
            compact('citas', 'procedimientos')
        );
    }

    public function pdf(Request $request)
    {
        $citas = $this->construirConsulta($request)
            ->get();

        $filtros = [
            'buscar' => $request->buscar,
            'procedimiento_id' =>
            $request->procedimiento_id,
            'estado' => $request->estado,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ];

        $pdf = Pdf::loadView(
            'procedimientos.procedimientos.reportes.pdf',
            compact('citas', 'filtros')
        )->setPaper('a4', 'landscape');

        return $pdf->stream(
            'reporte-citas-procedimientos.pdf'
        );
    }

    private function construirConsulta(
        Request $request
    ): Builder {
        $buscar = trim((string) $request->buscar);

        return ProcedimientoCita::query()
            ->select('procedimiento_citas.*')

            ->join(
                'procedimiento_cupos as pc',
                'pc.id',
                '=',
                'procedimiento_citas.procedimiento_cupo_id'
            )

            ->join(
                'procedimiento_programaciones as pp',
                'pp.id',
                '=',
                'pc.procedimiento_programacion_id'
            )

            ->join(
                'procedimientos as pr',
                'pr.id',
                '=',
                'pp.procedimiento_id'
            )

            ->with([
                'cupo.programacion.procedimiento.servicio',
                'cupo.programacion.medico',
            ])

            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {
                    $query->where(function ($subconsulta) use ($buscar) {
                        $subconsulta
                            ->where(
                                'procedimiento_citas.numero_cuenta',
                                'like',
                                "%{$buscar}%"
                            )
                            ->orWhere(
                                'procedimiento_citas.historia_clinica',
                                'like',
                                "%{$buscar}%"
                            )
                            ->orWhere(
                                'procedimiento_citas.paciente',
                                'like',
                                "%{$buscar}%"
                            )
                            ->orWhere(
                                'pr.nombre',
                                'like',
                                "%{$buscar}%"
                            );
                    });
                }
            )

            ->when(
                $request->filled('procedimiento_id'),
                function ($query) use ($request) {
                    $query->where(
                        'pp.procedimiento_id',
                        $request->procedimiento_id
                    );
                }
            )

            ->when(
                $request->filled('estado'),
                function ($query) use ($request) {
                    $query->where(
                        'procedimiento_citas.estado',
                        $request->estado
                    );
                }
            )

            ->when(
                $request->filled('fecha_inicio'),
                function ($query) use ($request) {
                    $query->whereDate(
                        'pp.fecha_programacion',
                        '>=',
                        $request->fecha_inicio
                    );
                }
            )

            ->when(
                $request->filled('fecha_fin'),
                function ($query) use ($request) {
                    $query->whereDate(
                        'pp.fecha_programacion',
                        '<=',
                        $request->fecha_fin
                    );
                }
            )

            ->orderByDesc('pp.fecha_programacion')
            ->orderByDesc('pc.hora');
    }
}
