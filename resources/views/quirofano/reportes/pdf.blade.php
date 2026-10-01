<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte de operaciones quirúrgicas</title>

    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #17364b;
            font-size: 7px;
        }

        h1 {
            margin: 0;
            color: #075a94;
            font-size: 16px;
        }

        .periodo {
            margin: 4px 0 12px;
            color: #555;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 5px 3px;
            border: 1px solid #d4e1e9;
            background: #075a94;
            color: white;
            text-align: center;
        }

        td {
            padding: 4px 3px;
            border: 1px solid #d4e1e9;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #eef4f8;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <h1>Reporte de operaciones quirúrgicas</h1>

    <div class="periodo">
        Periodo:
        {{ $fechaDesde->format('d/m/Y') }}
        al
        {{ $fechaHasta->format('d/m/Y') }}
        |
        Total: {{ $operaciones->count() }} operaciones
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Sala</th>
                <th>Paciente</th>
                <th>Cuenta / HC</th>
                <th>Procedimiento</th>
                <th>Médico</th>
                <th>Programación</th>
                <th>Inicio real</th>
                <th>Fin real</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($operaciones as $programacion)
            @php
            $solicitud = $programacion->solicitud;
            $sala = $programacion->sala;
            @endphp

            <tr>
                <td class="nowrap">
                    {{ optional(
                            $programacion->fecha_hora_inicio
                        )->format('d/m/Y') }}
                </td>

                <td>
                    {{ $sala->nombre ?? '-' }}
                </td>

                <td>
                    {{ $solicitud->paciente ?? '-' }}
                </td>

                <td class="nowrap">
                    Cuenta:
                    {{ $solicitud->numero_cuenta ?? '-' }}
                    <br>
                    HC:
                    {{ $solicitud->historia_clinica ?? '-' }}
                </td>

                <td>
                    {{ $solicitud->procedimiento_codigo ?? '' }}
                    {{ $solicitud->procedimiento_nombre ?? '-' }}
                </td>

                <td>
                    {{ $solicitud->medico_nombre ?? '-' }}
                    <br>
                    {{ $solicitud->medico_especialidad ?? '' }}
                </td>

                <td class="nowrap">
                    {{ optional(
                            $programacion->fecha_hora_inicio
                        )->format('H:i') }}
                    -
                    {{ optional(
                            $programacion->fecha_hora_fin
                        )->format('H:i') }}
                </td>

                <td class="nowrap">
                    {{ optional(
                            $programacion->fecha_inicio_real
                        )->format('H:i') ?? '-' }}
                </td>

                <td class="nowrap">
                    {{ optional(
                            $programacion->fecha_fin_real
                        )->format('H:i') ?? '-' }}
                </td>

                <td>
                    {{ str_replace(
                            '_',
                            ' ',
                            $programacion->estado
                        ) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center;">
                    No existen operaciones para los filtros seleccionados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>