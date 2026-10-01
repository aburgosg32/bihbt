<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Ticket de cita</title>

    <style>
        @page {
            margin: 8px 10px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #000;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            line-height: 1.25;
        }

        .centrado {
            text-align: center;
        }

        .hospital {
            font-size: 11px;
            font-weight: bold;
        }

        .titulo {
            margin-top: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .separador {
            margin: 5px 0;
            border-top: 1px dashed #000;
        }

        .fila {
            margin-bottom: 3px;
        }

        .etiqueta {
            font-weight: bold;
        }

        .procedimiento {
            margin: 5px 0;
            padding: 5px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .fecha-hora {
            width: 100%;
            margin: 5px 0;
            border-collapse: collapse;
        }

        .fecha-hora td {
            width: 50%;
            padding: 4px 2px;
            border: 1px solid #000;
            text-align: center;
        }

        .valor-grande {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            font-weight: bold;
        }

        .aviso {
            margin-top: 6px;
            font-size: 7px;
            text-align: center;
        }

        .codigo {
            margin-top: 6px;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>

    @php
    $cupo = $cita->cupo;
    $programacion = $cupo->programacion;
    $procedimiento = $programacion->procedimiento;
    $medico = $programacion->medico;
    @endphp

    <div class="centrado">
        <div class="hospital">
            HOSPITAL BELÉN DE TRUJILLO
        </div>

        <div>
            MÓDULO DE PROCEDIMIENTOS
        </div>

        <div class="titulo">
            TICKET DE CITA
        </div>
    </div>

    <div class="separador"></div>

    <div class="fila">
        <span class="etiqueta">Cita N.°:</span>
        {{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}
    </div>

    <div class="fila">
        <span class="etiqueta">Cuenta:</span>
        {{ $cita->numero_cuenta }}
    </div>

    <div class="fila">
        <span class="etiqueta">HC:</span>
        {{ $cita->historia_clinica ?? 'NO REGISTRA' }}
    </div>

    <div class="fila">
        <span class="etiqueta">Paciente:</span><br>
        {{ mb_strtoupper($cita->paciente) }}
    </div>

    <div class="fila">
        <span class="etiqueta">Servicio origen:</span>
        {{ $cita->servicio_origen ?? 'NO REGISTRA' }}
    </div>

    <div class="fila">
        <span class="etiqueta">Financiamiento:</span>
        {{ $cita->financiamiento ?? 'NO REGISTRA' }}
    </div>

    <div class="separador"></div>

    <div class="procedimiento">
        {{ mb_strtoupper($procedimiento->nombre) }}
    </div>

    <table class="fecha-hora">
        <tr>
            <td>
                FECHA

                <span class="valor-grande">
                    {{ $programacion
                        ->fecha_programacion
                        ->format('d/m/Y') }}
                </span>
            </td>

            <td>
                HORA

                <span class="valor-grande">
                    {{ substr($cupo->hora, 0, 5) }}
                </span>
            </td>
        </tr>
    </table>

    <div class="fila">
        <span class="etiqueta">Médico:</span><br>
        {{ mb_strtoupper(
            $medico->nombre_completo ?? 'POR ASIGNAR'
        ) }}
    </div>

    @if ($cita->observacion)
    <div class="fila">
        <span class="etiqueta">Observación:</span><br>
        {{ $cita->observacion }}
    </div>
    @endif

    <div class="separador"></div>

    <div class="aviso">
        Presentarse 15 minutos antes con su documento de identidad
        y este ticket.
    </div>

    <div class="aviso">
        La hora puede variar según las condiciones de atención.
    </div>

    <div class="codigo">
        Emitido:
        {{ $cita->fecha_registro
            ? $cita->fecha_registro->format('d/m/Y H:i')
            : now()->format('d/m/Y H:i') }}
    </div>
    <div class="aviso">
        Desarrollado por Area de Informática - Hospital Belén de Trujillo.
    </div>

</body>

</html>