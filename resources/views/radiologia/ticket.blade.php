<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 6px;
        }

        .center {
            text-align: center;
        }

        .titulo {
            font-size: 13px;
            font-weight: bold;
        }

        .subtitulo {
            font-size: 11px;
            font-weight: bold;
        }

        .linea {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 38%;
        }

        .modalidad {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin-top: 4px;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <div class="center">
        <div class="titulo">HOSPITAL BELÉN DE TRUJILLO</div>
        <div class="subtitulo">CITA DE RADIOLOGÍA</div>
    </div>

    <div class="linea"></div>

    <table>
        <tr>
            <td class="label">Cuenta:</td>
            <td>{{ $cita->id_cuenta_atencion }}</td>
        </tr>

        <tr>
            <td class="label">HC:</td>
            <td>{{ $cita->nro_historia_clinica }}</td>
        </tr>

        <tr>
            <td class="label">Paciente:</td>
            <td>{{ $cita->paciente }}</td>
        </tr>

        <tr>
            <td class="label">Edad:</td>
            <td>{{ $cita->edad }} {{ $cita->tipo_edad }}</td>
        </tr>

        <tr>
            <td class="label">Financ.:</td>
            <td>{{ $cita->fuente_financiamiento }}</td>
        </tr>
    </table>

    <div class="linea"></div>

    <div class="modalidad">
        {{ $cita->modalidad }}
    </div>

    <table>
        <tr>
            <td class="label">Estudio:</td>
            <td>{{ $cita->estudio }}</td>
        </tr>

        <tr>
            <td class="label">Fecha:</td>
            <td>{{ date('d-m-Y', strtotime($cita->fecha_cita)) }}</td>
        </tr>

        <tr>
            <td class="label">Hora:</td>
            <td>{{ $cita->hora_cita }}</td>
        </tr>

        <tr>
            <td class="label">Estado:</td>
            <td>{{ $cita->estado }}</td>
        </tr>
    </table>

    @if($cita->observacion)
    <div class="linea"></div>

    <table>
        <tr>
            <td class="label">Obs.:</td>
            <td>{{ $cita->observacion }}</td>
        </tr>
    </table>
    @endif

    <div class="linea"></div>

    <div class="footer">
        Presentarse 15 minutos antes de la cita.<br>
        Ticket generado: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>