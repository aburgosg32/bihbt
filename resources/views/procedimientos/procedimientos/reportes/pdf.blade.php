<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte de citas de procedimientos</title>

    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 8px;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .subtitulo {
            margin-top: 2px;
            color: #555;
            font-size: 9px;
            text-align: center;
        }

        .filtros {
            margin: 10px 0;
            padding: 6px;
            border: 1px solid #ccc;
            background: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 5px 3px;
            border: 1px solid #777;
            background: #dcecff;
            font-size: 7px;
            text-align: center;
        }

        td {
            padding: 4px 3px;
            border: 1px solid #aaa;
            vertical-align: top;
        }

        .centrado {
            text-align: center;
        }

        .pie {
            margin-top: 8px;
            color: #555;
            font-size: 7px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="titulo">
        HOSPITAL BELÉN DE TRUJILLO
    </div>

    <div class="subtitulo">
        Reporte de citas de procedimientos
    </div>

    <div class="filtros">
        <strong>Filtros aplicados:</strong>

        Búsqueda:
        {{ $filtros['buscar'] ?: 'Todos' }}

        |

        Estado:
        {{ $filtros['estado'] ?: 'Todos' }}

        |

        Desde:
        {{ $filtros['fecha_inicio']
            ? date(
                'd/m/Y',
                strtotime($filtros['fecha_inicio'])
            )
            : 'Inicio' }}

        |

        Hasta:
        {{ $filtros['fecha_fin']
            ? date(
                'd/m/Y',
                strtotime($filtros['fecha_fin'])
            )
            : 'Actualidad' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Fecha</th>
                <th style="width: 5%;">Hora</th>
                <th style="width: 8%;">Cuenta</th>
                <th style="width: 8%;">HC</th>
                <th style="width: 18%;">Paciente</th>
                <th style="width: 18%;">Procedimiento</th>
                <th style="width: 14%;">Médico</th>
                <th style="width: 14%;">Servicio origen</th>
                <th style="width: 8%;">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($citas as $cita)

            @php
            $cupo = $cita->cupo;
            $programacion = $cupo->programacion;
            $procedimiento = $programacion->procedimiento;
            $medico = $programacion->medico;
            @endphp

            <tr>
                <td class="centrado">
                    {{ $programacion
                        ->fecha_programacion
                        ->format('d/m/Y') }}
                </td>

                <td class="centrado">
                    {{ substr($cupo->hora, 0, 5) }}
                </td>

                <td>{{ $cita->numero_cuenta }}</td>

                <td>
                    {{ $cita->historia_clinica
                        ?? 'NO REGISTRA' }}
                </td>

                <td>{{ $cita->paciente }}</td>

                <td>{{ $procedimiento->nombre }}</td>

                <td>
                    {{ $medico->nombre_completo
                        ?? 'SIN MÉDICO' }}
                </td>

                <td>
                    {{ $cita->servicio_origen
                        ?? 'NO REGISTRA' }}
                </td>

                <td class="centrado">
                    {{ $cita->estado }}
                </td>
            </tr>

            @empty

            <tr>
                <td colspan="9" class="centrado">
                    No existen registros.
                </td>
            </tr>

            @endforelse
        </tbody>
    </table>

    <div class="pie">
        Total de registros: {{ $citas->count() }}
        -
        Generado: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>