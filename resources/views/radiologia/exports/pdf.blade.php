<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h3 {
            text-align: center;
            margin-bottom: 4px;
        }

        .subtitulo {
            text-align: center;
            margin-bottom: 10px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #eaeaea;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 3px;
        }
    </style>
</head>

<body>

    <h3>REPORTE DE RADIOLOGÍA</h3>

    <div class="subtitulo">
        Del {{ date('d-m-Y', strtotime($fechaInicio)) }}
        al {{ date('d-m-Y', strtotime($fechaFin)) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Cuenta</th>
                <th>HC</th>
                <th>Paciente</th>
                <th>Edad</th>
                <th>Modalidad</th>
                <th>Código</th>
                <th>Estudio</th>
                <th>Servicio</th>
                <th>Financ.</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @foreach($citas as $item)
            <tr>
                <td>{{ $item->id_cuenta_atencion }}</td>
                <td>{{ $item->nro_historia_clinica }}</td>
                <td>{{ $item->paciente }}</td>
                <td>{{ $item->edad }} {{ $item->tipo_edad }}</td>
                <td>{{ $item->modalidad }}</td>
                <td>{{ $item->codigo_estudio }}</td>
                <td>{{ $item->estudio }}</td>
                <td>{{ $item->nombre_servicio }}</td>
                <td>{{ $item->fuente_financiamiento }}</td>
                <td>{{ date('d-m-Y', strtotime($item->fecha_cita)) }}</td>
                <td>{{ substr($item->hora_cita, 0, 5) }}</td>
                <td>{{ $item->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>