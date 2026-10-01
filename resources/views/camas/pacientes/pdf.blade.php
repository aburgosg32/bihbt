<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Censo Actual
    </title>


    <style>
        @page {
            margin: 15px 18px;
        }

        body {
            font-family:
                DejaVu Sans,
                sans-serif;

            font-size:
                8px;

            color:
                #222;
        }


        .titulo {

            font-size:
                17px;

            font-weight:
                bold;

            margin-bottom:
                2px;

        }


        .subtitulo {

            font-size:
                9px;

            color:
                #555;

            margin-bottom:
                8px;

        }


        .cabecera {

            width:
                100%;

            border-bottom:
                2px solid #333;

            padding-bottom:
                6px;

            margin-bottom:
                8px;

        }


        .cabecera td {

            vertical-align:
                top;

        }


        .fecha {

            text-align:
                right;

            font-size:
                8px;

        }


        .total {

            margin-bottom:
                8px;

            font-size:
                9px;

        }


        .servicio {

            margin-bottom:
                8px;

            page-break-inside:
                avoid;

        }


        .servicio-titulo {

            padding:
                4px 6px;

            background:
                #e9ecef;

            border:
                1px solid #999;

            font-size:
                9px;

            font-weight:
                bold;

        }


        .servicio-total {

            float:
                right;

            font-size:
                8px;

            font-weight:
                normal;

        }


        table.detalle {

            width:
                100%;

            border-collapse:
                collapse;

        }


        table.detalle th {

            background:
                #f5f5f5;

            border:
                1px solid #aaa;

            padding:
                3px;

            text-align:
                left;

            font-size:
                7px;

        }


        table.detalle td {

            border:
                1px solid #ccc;

            padding:
                3px;

            font-size:
                7px;

        }


        .footer {

            margin-top:
                10px;

            padding-top:
                5px;

            border-top:
                1px solid #aaa;

            text-align:
                center;

            font-size:
                7px;

            color:
                #666;

        }
    </style>

</head>


<body>


    <table class="cabecera">

        <tr>

            <td>

                <div class="titulo">

                    CENSO ACTUAL DE PACIENTES

                </div>


                <div class="subtitulo">

                    Hospital Belén de Trujillo

                    @if($departamento)

                    -
                    {{ $departamento->nombre }}

                    @endif

                </div>

            </td>


            <td class="fecha">

                Fecha:
                {{ now()->format('d/m/Y') }}

                <br>

                Hora:
                {{ now()->format('H:i') }}

            </td>

        </tr>

    </table>



    <div class="total">

        <strong>
            Total actual:
        </strong>

        {{ $ocupaciones->count() }}

        pacientes

    </div>



    @foreach(
    $ocupacionesAgrupadas
    as $nombreServicio => $grupo
    )


    <div class="servicio">


        <div class="servicio-titulo">

            {{ $nombreServicio }}


            <span class="servicio-total">

                {{ $grupo->count() }}

                pacientes

            </span>

        </div>


        <table class="detalle">

            <thead>

                <tr>

                    <th style="width:8%;">
                        HC
                    </th>

                    <th style="width:34%;">
                        Paciente
                    </th>

                    <th style="width:12%;">
                        Teléfono
                    </th>

                    <th style="width:8%;">
                        Sexo
                    </th>

                    <th style="width:11%;">
                        Fecha Nac.
                    </th>

                    <th style="width:7%;">
                        Cama
                    </th>

                    <th style="width:20%;">
                        Servicio
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach(
                $grupo
                as $ocupacion
                )

                @php

                $paciente =
                $ocupacion
                ->paciente;

                $cama =
                $ocupacion
                ->cama;

                $servicio =
                $cama
                ?->servicio;

                @endphp


                <tr>

                    {{-- HC --}}

                    <td>

                        {{ $paciente
                        ?->nro_historia_clinica
                        ?? '-'
                    }}

                    </td>


                    {{-- PACIENTE --}}

                    <td>

                        {{ $paciente
                        ?->nombre_completo
                        ?? '-'
                    }}

                    </td>


                    {{-- TELÉFONO --}}

                    <td>

                        {{ $paciente
                        ?->telefono
                        ?? '-'
                    }}

                    </td>


                    {{-- SEXO --}}

                    <td>

                        {{ $paciente
                        ?->sexo
                        ?? '-'
                    }}

                    </td>


                    {{-- FECHA NACIMIENTO --}}

                    <td>

                        @if(
                        $paciente
                        ?->fecha_nacimiento
                        )

                        {{ $paciente
                            ->fecha_nacimiento
                            ->format(
                                'd/m/Y'
                            )
                        }}

                        @else

                        -

                        @endif

                    </td>


                    {{-- CAMA --}}

                    <td>

                        {{ $cama
                        ?->numero
                        ?? '-'
                    }}

                    </td>


                    {{-- SERVICIO --}}

                    <td>

                        {{ $servicio
                        ?->nombre
                        ?? '-'
                    }}

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>


    </div>


    @endforeach



    <div class="footer">

        Hospital Belén de Trujillo ·
        Gestión de Camas ·
        Censo generado el
        {{ now()->format('d/m/Y H:i:s') }}

    </div>


</body>

</html>