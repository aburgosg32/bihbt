<?php

namespace App\Http\Controllers;

use App\Models\QuirofanoProgramacion;
use App\Models\QuirofanoSala;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Exports\QuirofanoReporteExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class QuirofanoReporteController extends Controller
{
    /**
     * Reporte general de operaciones quirúrgicas.
     */
    public function index(Request $request)
    {
        [$query, $fechaDesde, $fechaHasta] =
            $this->construirConsulta($request);

        /*
        |--------------------------------------------------------------------------
        | Indicadores
        |--------------------------------------------------------------------------
        |
        | Se clona la consulta antes de paginar.
        */

        $consultaIndicadores = clone $query;

        $programacionesIndicadores = $consultaIndicadores->get();

        $totalOperaciones = $programacionesIndicadores->count();

        $programadas = $programacionesIndicadores
            ->where('estado', 'PROGRAMADA')
            ->count();

        $enPreparacion = $programacionesIndicadores
            ->where('estado', 'EN_PREPARACION')
            ->count();

        $enCurso = $programacionesIndicadores
            ->where('estado', 'EN_CURSO')
            ->count();

        $finalizadas = $programacionesIndicadores
            ->filter(function ($programacion) {
                return in_array(
                    $programacion->estado,
                    [
                        'FINALIZADA',
                        'LIMPIEZA',
                        'CULMINADA',
                    ],
                    true
                );
            })
            ->count();

        $canceladas = $programacionesIndicadores
            ->where('estado', 'CANCELADA')
            ->count();

        $emergencias = $programacionesIndicadores
            ->filter(function ($programacion) {
                return mb_strtoupper(
                    (string) optional($programacion->solicitud)
                        ->tipo_solicitud,
                    'UTF-8'
                ) === 'EMERGENCIA';
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Cálculos de tiempos
        |--------------------------------------------------------------------------
        */

        $operacionesConInicioReal = $programacionesIndicadores
            ->filter(function ($programacion) {
                return !empty($programacion->fecha_inicio_real);
            });

        $operacionesPuntuales = $operacionesConInicioReal
            ->filter(function ($programacion) {
                $inicioProgramado = Carbon::parse(
                    $programacion->fecha_hora_inicio
                );

                $inicioReal = Carbon::parse(
                    $programacion->fecha_inicio_real
                );

                return $inicioReal->lessThanOrEqualTo(
                    $inicioProgramado
                );
            })
            ->count();

        $operacionesConRetraso = $operacionesConInicioReal
            ->filter(function ($programacion) {
                $inicioProgramado = Carbon::parse(
                    $programacion->fecha_hora_inicio
                );

                $inicioReal = Carbon::parse(
                    $programacion->fecha_inicio_real
                );

                return $inicioReal->greaterThan(
                    $inicioProgramado
                );
            })
            ->count();

        /*
         * Promedio de retraso.
         * Solo se consideran las operaciones que iniciaron después
         * de la hora programada.
         */
        $retrasos = $operacionesConInicioReal
            ->map(function ($programacion) {
                $inicioProgramado = Carbon::parse(
                    $programacion->fecha_hora_inicio
                );

                $inicioReal = Carbon::parse(
                    $programacion->fecha_inicio_real
                );

                if ($inicioReal->lessThanOrEqualTo($inicioProgramado)) {
                    return 0;
                }

                return $inicioProgramado->diffInMinutes(
                    $inicioReal
                );
            })
            ->filter(function ($minutos) {
                return $minutos > 0;
            });

        $promedioRetraso = $retrasos->isNotEmpty()
            ? round($retrasos->avg(), 1)
            : 0;

        /*
         * Duración real de las operaciones.
         */
        $duracionesReales = $programacionesIndicadores
            ->filter(function ($programacion) {
                return
                    !empty($programacion->fecha_inicio_real) &&
                    !empty($programacion->fecha_fin_real);
            })
            ->map(function ($programacion) {
                return Carbon::parse(
                    $programacion->fecha_inicio_real
                )->diffInMinutes(
                    Carbon::parse($programacion->fecha_fin_real)
                );
            });

        $promedioDuracionReal = $duracionesReales->isNotEmpty()
            ? round($duracionesReales->avg(), 1)
            : 0;

        /*
         * Tiempo real de limpieza.
         */
        $tiemposLimpieza = $programacionesIndicadores
            ->filter(function ($programacion) {
                return
                    !empty($programacion->fecha_fin_real) &&
                    !empty($programacion->fecha_fin_limpieza);
            })
            ->map(function ($programacion) {
                return Carbon::parse(
                    $programacion->fecha_fin_real
                )->diffInMinutes(
                    Carbon::parse(
                        $programacion->fecha_fin_limpieza
                    )
                );
            });

        $promedioLimpieza = $tiemposLimpieza->isNotEmpty()
            ? round($tiemposLimpieza->avg(), 1)
            : 0;

        /*
         * Porcentaje de puntualidad.
         */
        $totalConInicioReal = $operacionesConInicioReal->count();

        $porcentajePuntualidad = $totalConInicioReal > 0
            ? round(
                ($operacionesPuntuales / $totalConInicioReal) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Resultado paginado
        |--------------------------------------------------------------------------
        */

        $operaciones = $query
            ->orderByDesc('fecha_hora_inicio')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Salas para el filtro
        |--------------------------------------------------------------------------
        */

        $salas = QuirofanoSala::query()
            ->orderBy('codigo')
            ->get();

        return view(
            'quirofano.reportes.index',
            compact(
                'operaciones',
                'salas',
                'fechaDesde',
                'fechaHasta',
                'totalOperaciones',
                'programadas',
                'enPreparacion',
                'enCurso',
                'finalizadas',
                'canceladas',
                'emergencias',
                'operacionesPuntuales',
                'operacionesConRetraso',
                'promedioRetraso',
                'promedioDuracionReal',
                'promedioLimpieza',
                'porcentajePuntualidad'
            )
        );
    }

    /**
     * Aplicar los filtros seleccionados.
     */
    private function aplicarFiltros(
        Builder $query,
        Request $request
    ): void {
        /*
         * Sala.
         */
        $query->when(
            $request->filled('sala_id'),
            function (Builder $consulta) use ($request) {
                $consulta->where(
                    'quirofano_sala_id',
                    $request->sala_id
                );
            }
        );

        /*
         * Estado de la programación.
         */
        $query->when(
            $request->filled('estado'),
            function (Builder $consulta) use ($request) {
                $consulta->where(
                    'estado',
                    $request->estado
                );
            }
        );

        /*
         * Tipo de solicitud.
         */
        $query->when(
            $request->filled('tipo_solicitud'),
            function (Builder $consulta) use ($request) {
                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($request) {
                        $solicitud->where(
                            'tipo_solicitud',
                            $request->tipo_solicitud
                        );
                    }
                );
            }
        );

        /*
         * Prioridad.
         */
        $query->when(
            $request->filled('prioridad'),
            function (Builder $consulta) use ($request) {
                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($request) {
                        $solicitud->where(
                            'prioridad',
                            $request->prioridad
                        );
                    }
                );
            }
        );

        /*
         * Servicio solicitante.
         */
        $query->when(
            $request->filled('servicio'),
            function (Builder $consulta) use ($request) {
                $valor = trim($request->servicio);

                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($valor) {
                        $solicitud->where(
                            'servicio_solicitante',
                            'like',
                            '%' . $valor . '%'
                        );
                    }
                );
            }
        );

        /*
         * Médico.
         */
        $query->when(
            $request->filled('medico'),
            function (Builder $consulta) use ($request) {
                $valor = trim($request->medico);

                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($valor) {
                        $solicitud->where(function ($medico) use ($valor) {
                            $medico
                                ->where(
                                    'medico_codigo',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'medico_nombre',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'medico_especialidad',
                                    'like',
                                    '%' . $valor . '%'
                                );
                        });
                    }
                );
            }
        );

        /*
         * Paciente, cuenta o historia clínica.
         */
        $query->when(
            $request->filled('paciente'),
            function (Builder $consulta) use ($request) {
                $valor = trim($request->paciente);

                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($valor) {
                        $solicitud->where(function ($paciente) use ($valor) {
                            $paciente
                                ->where(
                                    'paciente',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'numero_cuenta',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'historia_clinica',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'dni',
                                    'like',
                                    '%' . $valor . '%'
                                );
                        });
                    }
                );
            }
        );

        /*
         * Diagnóstico.
         */
        $query->when(
            $request->filled('diagnostico'),
            function (Builder $consulta) use ($request) {
                $valor = trim($request->diagnostico);

                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($valor) {
                        $solicitud->where(function ($diagnostico) use ($valor) {
                            $diagnostico
                                ->where(
                                    'diagnostico_codigo',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'diagnostico_nombre',
                                    'like',
                                    '%' . $valor . '%'
                                );
                        });
                    }
                );
            }
        );

        /*
         * Procedimiento.
         */
        $query->when(
            $request->filled('procedimiento'),
            function (Builder $consulta) use ($request) {
                $valor = trim($request->procedimiento);

                $consulta->whereHas(
                    'solicitud',
                    function (Builder $solicitud) use ($valor) {
                        $solicitud->where(function ($procedimiento) use ($valor) {
                            $procedimiento
                                ->where(
                                    'procedimiento_codigo',
                                    'like',
                                    '%' . $valor . '%'
                                )
                                ->orWhere(
                                    'procedimiento_nombre',
                                    'like',
                                    '%' . $valor . '%'
                                );
                        });
                    }
                );
            }
        );

        /*
         * Clasificación por puntualidad.
         */
        if ($request->puntualidad === 'PUNTUAL') {
            $query
                ->whereNotNull('fecha_inicio_real')
                ->whereColumn(
                    'fecha_inicio_real',
                    '<=',
                    'fecha_hora_inicio'
                );
        }

        if ($request->puntualidad === 'RETRASADA') {
            $query
                ->whereNotNull('fecha_inicio_real')
                ->whereColumn(
                    'fecha_inicio_real',
                    '>',
                    'fecha_hora_inicio'
                );
        }

        if ($request->puntualidad === 'SIN_INICIAR') {
            $query->whereNull('fecha_inicio_real');
        }
    }
    /**
     * Exportar el reporte a Excel.
     */
    public function excel(Request $request)
    {
        [$query, $fechaDesde, $fechaHasta] =
            $this->construirConsulta($request);

        $operaciones = $query
            ->orderBy('fecha_hora_inicio')
            ->orderBy('id')
            ->get();

        $nombre = 'reporte_quirofano_' .
            $fechaDesde->format('Ymd') . '_' .
            $fechaHasta->format('Ymd') . '.xlsx';

        return Excel::download(
            new QuirofanoReporteExport($operaciones),
            $nombre
        );
    }

    /**
     * Exportar el reporte a PDF.
     */
    public function pdf(Request $request)
    {
        [$query, $fechaDesde, $fechaHasta] =
            $this->construirConsulta($request);

        $operaciones = $query
            ->orderBy('fecha_hora_inicio')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView(
            'quirofano.reportes.pdf',
            compact(
                'operaciones',
                'fechaDesde',
                'fechaHasta'
            )
        )->setPaper('a4', 'landscape');

        $nombre = 'reporte_quirofano_' .
            $fechaDesde->format('Ymd') . '_' .
            $fechaHasta->format('Ymd') . '.pdf';

        return $pdf->download($nombre);
    }

    /**
     * Construir la consulta aplicando las fechas y demás filtros.
     */
    private function construirConsulta(Request $request): array
    {
        $fechaDesde = $request->filled('fecha_desde')
            ? Carbon::parse($request->fecha_desde)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $fechaHasta = $request->filled('fecha_hasta')
            ? Carbon::parse($request->fecha_hasta)->endOfDay()
            : now()->endOfDay();

        $query = QuirofanoProgramacion::query()
            ->with([
                'sala',
                'solicitud',
            ])
            ->whereBetween(
                'fecha_hora_inicio',
                [
                    $fechaDesde,
                    $fechaHasta,
                ]
            );

        $this->aplicarFiltros($query, $request);

        return [
            $query,
            $fechaDesde,
            $fechaHasta,
        ];
    }
}
