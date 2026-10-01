<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegurosSeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $dni = trim((string) $request->query('dni', ''));
        $atenciones = collect();
        $buscado = false;

        if ($request->has('dni')) {
            // Normalizar el valor antes de validarlo.
            $request->merge(['dni' => $dni]);

            $request->validate(
                [
                    'dni' => ['required', 'regex:/^[0-9]{8}$/'],
                ],
                [
                    'dni.required' => 'Ingrese el DNI del paciente.',
                    'dni.regex' => 'El DNI debe tener exactamente 8 dígitos.',
                ]
            );

            $buscado = true;

            $sql = <<<'SQL'
                SELECT
                    CONVERT(date, A.FechaIngreso) AS FechaIngreso,
                    A.IdCuentaAtencion,
                    P.NroHistoriaClinica,

                    LTRIM(RTRIM(
                        COALESCE(P.ApellidoPaterno, '') + ' ' +
                        COALESCE(P.ApellidoMaterno, '') + ' ' +
                        COALESCE(P.PrimerNombre, '') + ' ' +
                        COALESCE(P.SegundoNombre, '')
                    )) AS Paciente,

                    A.Edad,
                    TS.Descripcion AS TipoServicio,
                    S.Nombre AS Servicio,

                    COALESCE(
                        NULLIF(
                            LTRIM(RTRIM(
                                COALESCE(E.ApellidoPaterno, '') + ' ' +
                                COALESCE(E.ApellidoMaterno, '') + ' ' +
                                COALESCE(E.Nombres, '')
                            )),
                            ''
                        ),
                        'SIN MÉDICO REGISTRADO'
                    ) AS Medico

                FROM Atenciones AS A

                INNER JOIN Pacientes AS P
                    ON A.IdPaciente = P.IdPaciente

                LEFT JOIN TiposServicio AS TS
                    ON A.IdTipoServicio = TS.IdTipoServicio

                LEFT JOIN Servicios AS S
                    ON A.IdServicioIngreso = S.IdServicio

                LEFT JOIN Medicos AS M
                    ON A.IdMedicoIngreso = M.IdMedico

                LEFT JOIN Empleados AS E
                    ON M.IdEmpleado = E.IdEmpleado

                WHERE P.NroDocumento = ?

                ORDER BY
                    A.FechaIngreso DESC,
                    A.IdCuentaAtencion DESC
                SQL;

            $atenciones = collect(
                DB::connection('sigh_produccion')->select($sql, [$dni])
            );
        }

        return view('seguros.seguimiento', compact(
            'dni',
            'atenciones',
            'buscado'
        ));
    }

    public function detalle(int $cuenta)
    {
        $db = DB::connection('sigh_produccion');

        $atencion = $db->selectOne(
            <<<'SQL'
        SELECT TOP (1)
            A.IdAtencion,
            A.IdCuentaAtencion,
            CONVERT(date, A.FechaIngreso) AS FechaIngreso,
            P.NroHistoriaClinica,
            LTRIM(RTRIM(
                COALESCE(P.ApellidoPaterno, '') + ' ' +
                COALESCE(P.ApellidoMaterno, '') + ' ' +
                COALESCE(P.PrimerNombre, '') + ' ' +
                COALESCE(P.SegundoNombre, '')
            )) AS Paciente
        FROM Atenciones AS A
        LEFT JOIN Pacientes AS P
            ON A.IdPaciente = P.IdPaciente
        WHERE A.IdCuentaAtencion = ?
        ORDER BY A.FechaIngreso DESC, A.IdAtencion DESC
        SQL,
            [$cuenta]
        );

        abort_if(!$atencion, 404, 'No se encontró la atención.');

        // Diagnósticos separados para evitar multiplicarlos por los servicios.
        $diagnosticos = $db->select(
            <<<'SQL'
        SELECT DISTINCT
            D.CodigoCIE10 AS Codigo,
            D.DescripcionMINSA AS Diagnostico
        FROM Atenciones AS A
        INNER JOIN AtencionesDiagnosticos AS AD
            ON A.IdAtencion = AD.IdAtencion
        INNER JOIN Diagnosticos AS D
            ON AD.IdDiagnostico = D.IdDiagnostico
        WHERE A.IdCuentaAtencion = ?
        ORDER BY D.CodigoCIE10, D.DescripcionMINSA
        SQL,
            [$cuenta]
        );

        // Mantener los registros de despacho:
        // un servicio puede haberse consumido más de una vez.
        $servicios = $db->select(
            <<<'SQL'
        SELECT
            FO.IdOrden,
            FC.Codigo,
            FC.Nombre AS Servicio
        FROM FactOrdenServicio AS FO
        INNER JOIN FacturacionServicioDespacho AS FS
            ON FO.IdOrden = FS.idOrden
        LEFT JOIN FactCatalogoServicios AS FC
            ON FS.IdProducto = FC.IdProducto
        WHERE FO.IdCuentaAtencion = ?
        ORDER BY FO.IdOrden, FC.Codigo
        SQL,
            [$cuenta]
        );

        // Ejecutar el procedimiento existente, sin modificarlo.
        $farmacia = collect(
            $db->select(
                'EXEC dbo.FarmaciaFinanciamientosPorNroCuenta @idCuentaAtencion = ?',
                [$cuenta]
            )
        )->map(function ($fila) {
            return [
                'Codigo' => $fila->Codigo ?? '',
                'Nombre' => $fila->Nombre ?? '',
                'MovNumero' => $fila->MovNumero ?? '',
                'MovTipo' => $fila->MovTipo ?? '',
                'EstadoMovimiento' => $fila->idEstadoMovimiento ?? '',
            ];
        })->values();

        return response()->json([
            'atencion' => $atencion,
            'diagnosticos' => $diagnosticos,
            'servicios' => $servicios,
            'farmacia' => $farmacia,
        ]);
    }
}
