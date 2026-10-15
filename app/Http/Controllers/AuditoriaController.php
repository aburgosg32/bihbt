<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    // Cambia a 'sigh_prod' si ese es el alias de producción configurado.
    private const CONEXION = 'sigh';

    private function filtros(Request $request): array
    {
        return $request->validate([
            'fecha_inicio' => ['required', 'date_format:Y-m-d'],
            'fecha_fin' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:fecha_inicio',
            ],
            'usuario' => ['nullable', 'integer', 'min:1'],
            'cuenta' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
            'servicio' => ['nullable', 'integer', 'min:1'],
            'tabla' => ['nullable', 'string', 'max:100'],
            'accion' => ['nullable', 'in:A,M,E,C,T'],
            'equipo' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string', 'max:200'],
        ]);
    }

    private function consulta(array $filtros)
    {
        $inicio = Carbon::createFromFormat(
            'Y-m-d',
            $filtros['fecha_inicio']
        )->startOfDay();

        // Límite exclusivo: incluye todas las horas del último día.
        $fin = Carbon::createFromFormat(
            'Y-m-d',
            $filtros['fecha_fin']
        )->startOfDay()->addDay();

        $query = DB::connection(self::CONEXION)
            ->table('dbo.Auditoria as AU')
            ->leftJoin(
                'dbo.Empleados as E',
                'E.IdEmpleado',
                '=',
                'AU.IdEmpleado'
            )
            ->whereRaw(
                'AU.FechaHora >= CONVERT(datetime, ?, 112)',
                [$inicio->format('Ymd')]
            )
            ->whereRaw(
                'AU.FechaHora < CONVERT(datetime, ?, 112)',
                [$fin->format('Ymd')]
            );

        if (!empty($filtros['usuario'])) {
            $query->where('AU.IdEmpleado', $filtros['usuario']);
        }

        if (!empty($filtros['tabla'])) {
            $query->whereRaw(
                'LTRIM(RTRIM(AU.Tabla)) = ?',
                [$filtros['tabla']]
            );
        }

        if (!empty($filtros['accion'])) {
            $query->where('AU.Accion', $filtros['accion']);
        }

        if (!empty($filtros['equipo'])) {
            $query->where(
                'AU.nombrePc',
                'like',
                '%' . $filtros['equipo'] . '%'
            );
        }

        if (!empty($filtros['observaciones'])) {
            $query->where(
                'AU.observaciones',
                'like',
                '%' . $filtros['observaciones'] . '%'
            );
        }

        /*
         * EXISTS evita duplicar movimientos.
         * Estas relaciones solo se aplican cuando se filtra
         * por cuenta o servicio.
         */
        if (!empty($filtros['cuenta']) || !empty($filtros['servicio'])) {
            $query->where(function ($grupo) use ($filtros) {
                // Auditoría guarda IdAtencion.
                $grupo->where(function ($rama) use ($filtros) {
                    $rama->whereIn('AU.Tabla', [
                        'Atenciones',
                        'AtencionesCE',
                    ])->whereExists(function ($atencion) use ($filtros) {
                        $atencion->selectRaw('1')
                            ->from('dbo.Atenciones as AT')
                            ->whereColumn(
                                'AT.IdAtencion',
                                'AU.IdRegistro'
                            );

                        if (!empty($filtros['cuenta'])) {
                            $atencion->where(
                                'AT.IdCuentaAtencion',
                                $filtros['cuenta']
                            );
                        }

                        if (!empty($filtros['servicio'])) {
                            $atencion->where(
                                'AT.IdServicioIngreso',
                                $filtros['servicio']
                            );
                        }
                    });
                });

                // Auditoría guarda directamente IdCuentaAtencion.
                $grupo->orWhere(function ($rama) use ($filtros) {
                    $rama->whereIn('AU.Tabla', [
                        'FacturacionCuentasAtencion',
                        'FUA',
                    ]);

                    if (!empty($filtros['cuenta'])) {
                        $rama->where(
                            'AU.IdRegistro',
                            $filtros['cuenta']
                        );
                    }

                    if (!empty($filtros['servicio'])) {
                        $rama->whereExists(function ($atencion) use ($filtros) {
                            $atencion->selectRaw('1')
                                ->from('dbo.Atenciones as AT')
                                ->whereColumn(
                                    'AT.IdCuentaAtencion',
                                    'AU.IdRegistro'
                                )
                                ->where(
                                    'AT.IdServicioIngreso',
                                    $filtros['servicio']
                                );
                        });
                    }
                });
                // Auditoría guarda IdCita.
                // Citas permite llegar a la atención y su cuenta.
                $grupo->orWhere(function ($rama) use ($filtros) {
                    $rama->where('AU.Tabla', 'Citas')
                        ->whereExists(function ($cita) use ($filtros) {
                            $cita->selectRaw('1')
                                ->from('dbo.Citas as CI')
                                ->join(
                                    'dbo.Atenciones as AT',
                                    'AT.IdAtencion',
                                    '=',
                                    'CI.IdAtencion'
                                )
                                ->whereColumn('CI.IdCita', 'AU.IdRegistro');

                            if (!empty($filtros['cuenta'])) {
                                $cita->where(
                                    'AT.IdCuentaAtencion',
                                    $filtros['cuenta']
                                );
                            }

                            if (!empty($filtros['servicio'])) {
                                $cita->where(
                                    'AT.IdServicioIngreso',
                                    $filtros['servicio']
                                );
                            }
                        });
                });
            });
        }

        return $query
            ->select([
                'AU.IdAuditoria',
                'AU.FechaHora',
                'AU.IdRegistro',
                'AU.IdEmpleado',
                'AU.idListItem',
            ])
            ->selectRaw("
                LTRIM(RTRIM(AU.Tabla)) AS Tabla,
                LTRIM(RTRIM(AU.Accion)) AS Accion,
                LTRIM(RTRIM(E.Usuario)) AS Usuario,
                LTRIM(RTRIM(
                    COALESCE(E.ApellidoPaterno, '') + ' ' +
                    COALESCE(E.ApellidoMaterno, '') + ' ' +
                    COALESCE(E.Nombres, '')
                )) AS Empleado,
                LTRIM(RTRIM(AU.nombrePc)) AS Equipo,
                AU.observaciones AS Observaciones
            ")
            ->orderByDesc('AU.FechaHora')
            ->orderByDesc('AU.IdAuditoria');
    }

    public function index(Request $request)
    {
        $conexion = DB::connection(self::CONEXION);

        // Incluye empleados inactivos: pueden tener movimientos históricos.
        $usuarios = $conexion->table('dbo.Empleados')
            ->select('IdEmpleado', 'Usuario')
            ->selectRaw("
                LTRIM(RTRIM(
                    COALESCE(ApellidoPaterno, '') + ' ' +
                    COALESCE(ApellidoMaterno, '') + ' ' +
                    COALESCE(Nombres, '')
                )) AS Empleado
            ")
            ->whereNotNull('Usuario')
            ->whereRaw("LTRIM(RTRIM(Usuario)) <> ''")
            ->orderBy('ApellidoPaterno')
            ->orderBy('ApellidoMaterno')
            ->get();

        $servicios = $conexion->table('dbo.Servicios')
            ->select('IdServicio', 'Nombre')
            ->orderBy('Nombre')
            ->get();

        $movimientos = null;

        if ($request->boolean('buscar')) {
            $movimientos = $this->consulta($this->filtros($request))
                ->simplePaginate(50)
                ->withQueryString();
        }

        return view('auditoria.index', compact(
            'usuarios',
            'servicios',
            'movimientos'
        ));
    }

    public function exportar(Request $request)
    {
        $filtros = $this->filtros($request);

        $nombre = 'auditoria_' .
            $filtros['fecha_inicio'] . '_' .
            $filtros['fecha_fin'] . '.csv';

        return response()->streamDownload(function () use ($filtros) {
            $archivo = fopen('php://output', 'w');

            // BOM para que Excel reconozca correctamente las tildes.
            fwrite($archivo, "\xEF\xBB\xBF");

            fputcsv($archivo, [
                'ID Auditoría',
                'Fecha y hora',
                'Usuario',
                'Empleado',
                'Tabla',
                'ID Registro',
                'Acción',
                'ID Empleado',
                'ID ListItem',
                'Equipo',
                'Observaciones',
            ], ';');

            // Procesa por bloques sin cargar todo el resultado en memoria.
            $this->consulta($filtros)->chunk(1000, function ($filas) use ($archivo) {
                foreach ($filas as $fila) {
                    $valores = [
                        $fila->IdAuditoria,
                        $fila->FechaHora,
                        $fila->Usuario,
                        $fila->Empleado,
                        $fila->Tabla,
                        $fila->IdRegistro,
                        $fila->Accion,
                        $fila->IdEmpleado,
                        $fila->idListItem,
                        $fila->Equipo,
                        $fila->Observaciones,
                    ];

                    // Evita interpretar texto del registro como fórmula.
                    $valores = array_map(function ($valor) {
                        $texto = (string) ($valor ?? '');

                        if (preg_match('/^[\s]*[=+\-@]/u', $texto)) {
                            return "'" . $texto;
                        }

                        return $texto;
                    }, $valores);

                    fputcsv($archivo, $valores, ';');
                }
            });

            fclose($archivo);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
