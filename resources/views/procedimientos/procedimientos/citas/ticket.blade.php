@php
$cupo = $cita->cupo;
$programacion = $cupo->programacion;
$procedimiento = $programacion->procedimiento;
$medico = $programacion->medico;

$fecha = $programacion->fecha_programacion
->format('d/m/Y');

$hora = substr(
(string) $cupo->hora,
0,
5
);
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Ticket de cita
    </title>

    <style>
        @page {
            margin: 4mm 4mm 3mm 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #000;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            line-height: 1.25;
        }

        .text-center {
            text-align: center;
        }

        .hospital {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
        }

        .ticket-titulo {
            margin: 2px 0 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .separador {
            margin: 3px 0;
            border-top: 1px dashed #000;
        }

        .dato {
            margin: 1px 0;
        }

        .dato strong {
            font-weight: bold;
        }

        .paciente {
            margin-top: 2px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .procedimiento {
            margin: 4px 0;
            padding: 4px 3px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .fecha-hora {
            width: 100%;
            border-collapse: collapse;
        }

        .fecha-hora td {
            width: 50%;
            padding: 3px 2px;
            border: 1px solid #000;
            text-align: center;
        }

        .etiqueta {
            display: block;
            font-size: 7px;
            font-weight: normal;
        }

        .valor-grande {
            display: block;
            margin-top: 1px;
            font-size: 13px;
            font-weight: bold;
        }

        .medico {
            margin-top: 4px;
        }

        .indicacion {
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px dashed #000;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="text-center">

        <div class="hospital">
            HOSPITAL BELÉN DE TRUJILLO
        </div>

        <div class="ticket-titulo">
            CITA DE PROCEDIMIENTO
        </div>

    </div>

    <div class="separador"></div>

    <div class="dato">
        <strong>Cita N.°:</strong>
        {{ str_pad(
            (string) $cita->id,
            6,
            '0',
            STR_PAD_LEFT
        ) }}
    </div>

    <div class="dato">
        <strong>Cuenta:</strong>
        {{ $cita->numero_cuenta }}

        &nbsp;|&nbsp;

        <strong>HC:</strong>
        {{ $cita->historia_clinica ?: '—' }}
    </div>

    <div class="dato">
        <strong>Paciente:</strong>
    </div>

    <div class="paciente">
        {{ $cita->paciente }}
    </div>

    <div class="procedimiento">
        {{ $procedimiento->nombre }}
    </div>

    <table class="fecha-hora">
        <tr>
            <td>
                <span class="etiqueta">
                    FECHA
                </span>

                <span class="valor-grande">
                    {{ $fecha }}
                </span>
            </td>

            <td>
                <span class="etiqueta">
                    HORA
                </span>

                <span class="valor-grande">
                    {{ $hora }}
                </span>
            </td>
        </tr>
    </table>

    <div class="medico">
        <strong>Médico:</strong>
        {{ mb_strtoupper(
            $medico->nombre_completo ?? 'POR ASIGNAR'
        ) }}
    </div>

    <div class="indicacion">
        Presentarse 15 minutos antes con su documento de identidad y este ticket.
    </div>

</body>

</html>