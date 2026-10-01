@extends('adminlte::page')

@section('title', 'Censo Actual')


@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-list mr-2"></i>

            Censo Actual

        </h1>

        <small class="text-muted">

            Pacientes actualmente ubicados en camas hospitalarias

        </small>

    </div>


    <a href="{{ route(
        'camas.pacientes.pdf',
        request()->query()
    ) }}"
        target="_blank"
        class="btn btn-danger">

        <i class="fas fa-file-pdf mr-1"></i>

        Generar PDF

    </a>

</div>

@stop



@section('content')


{{-- ============================================================= --}}
{{-- FILTRO --}}
{{-- ============================================================= --}}

<div class="card shadow-sm mb-3">

    <div class="card-body py-2">

        <form method="GET"
            action="{{ route('camas.pacientes.index') }}">

            <div class="row align-items-end">


                <div class="col-lg-6 col-md-7">

                    <div class="form-group mb-1">

                        <label class="mb-1">

                            <i class="fas fa-hospital mr-1"></i>

                            Departamento

                        </label>


                        <select name="departamento_id"
                            class="form-control form-control-sm">

                            <option value="">

                                Todos los departamentos

                            </option>


                            @foreach(
                            $departamentos
                            as $departamento
                            )

                            <option
                                value="{{ $departamento->id }}"

                                {{ request('departamento_id')
                                        == $departamento->id
                                        ? 'selected'
                                        : ''
                                    }}>

                                {{ $departamento->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>



                <div class="col-lg-2 col-md-2">

                    <div class="form-group mb-1">

                        <button type="submit"
                            class="btn btn-primary btn-sm btn-block">

                            <i class="fas fa-filter mr-1"></i>

                            Filtrar

                        </button>

                    </div>

                </div>



                <div class="col-lg-2 col-md-2">

                    <div class="form-group mb-1">

                        <a href="{{ route(
                            'camas.pacientes.index'
                        ) }}"
                            class="btn btn-outline-secondary btn-sm btn-block">

                            <i class="fas fa-sync-alt mr-1"></i>

                            Limpiar

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>



{{-- ============================================================= --}}
{{-- TOTAL --}}
{{-- ============================================================= --}}

<div class="censo-total mb-3">

    <strong>
        Total actual:
    </strong>

    {{ $ocupaciones->count() }}

    {{ $ocupaciones->count() == 1
        ? 'paciente'
        : 'pacientes'
    }}

</div>



{{-- ============================================================= --}}
{{-- AGRUPACIÓN --}}
{{-- ============================================================= --}}

@php

$ocupacionesAgrupadas =
$ocupaciones
->groupBy(
function ($ocupacion) {

return $ocupacion
->cama
?->servicio
?->nombre
?? 'SIN SERVICIO';

}
);

@endphp



@if($ocupacionesAgrupadas->count() > 0)


<div class="row">


    @foreach(
    $ocupacionesAgrupadas
    as $nombreServicio => $grupo
    )


    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">


        <div class="censo-servicio">


            {{-- CABECERA --}}

            <div class="censo-servicio-header">


                <strong>

                    {{ $nombreServicio }}

                </strong>


                <span>

                    {{ $grupo->count() }}

                    {{ $grupo->count() == 1
                                ? 'paciente'
                                : 'pacientes'
                            }}

                </span>


            </div>



            {{-- TABLA --}}

            <div class="table-responsive">


                <table class="table table-sm table-hover mb-0 tabla-censo-actual">


                    <thead>

                        <tr>

                            <th class="col-hc">
                                HC
                            </th>

                            <th>
                                Paciente
                            </th>

                            <th class="col-telefono">
                                Teléfono
                            </th>

                            <th class="col-sexo">
                                Sexo
                            </th>

                            <th class="col-fecha">
                                F. Nac.
                            </th>

                            <th class="col-cama text-center">
                                Cama
                            </th>

                            <th class="col-servicio">
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

                                <strong>

                                    {{ $paciente
                                                    ?->nombre_completo
                                                    ?? '-'
                                                }}

                                </strong>

                            </td>

                            {{-- TELÉFONO --}}

                            <td>

                                @if($paciente?->telefono)

                                <i class="fas fa-phone mr-1 text-muted"></i>

                                {{ $paciente->telefono }}

                                @else

                                <span class="text-muted">
                                    -
                                </span>

                                @endif

                            </td>

                            {{-- SEXO --}}

                            <td>

                                @php

                                $sexo =
                                strtoupper(
                                $paciente
                                ?->sexo
                                ?? ''
                                );

                                @endphp


                                @if(
                                in_array(
                                $sexo,
                                [
                                'M',
                                'MASCULINO'
                                ]
                                )
                                )

                                M

                                @elseif(
                                in_array(
                                $sexo,
                                [
                                'F',
                                'FEMENINO'
                                ]
                                )
                                )

                                F

                                @else

                                {{ $paciente
                                                    ?->sexo
                                                    ?? '-'
                                                }}

                                @endif

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

                            <td class="text-center">

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


        </div>


    </div>


    @endforeach


</div>


@else


<div class="text-center py-5">

    <i class="fas fa-user-injured fa-3x text-muted mb-3"></i>

    <h5 class="text-muted">

        No hay pacientes en cama

    </h5>

    <p class="text-muted mb-0">

        No existen pacientes activos
        para el departamento seleccionado.

    </p>

</div>


@endif


@stop



@section('css')

<link rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">


<style>
    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    .censo-total {

        padding: 4px 2px;

        font-size: 13px;

        color: #263238;

    }



    /*
    |--------------------------------------------------------------------------
    | SERVICIO
    |--------------------------------------------------------------------------
    */

    .censo-servicio {

        border:
            1px solid #dce3e9;

        background: #fff;

        border-radius: 4px;

        overflow: hidden;

        height: 100%;

    }


    .censo-servicio-header {

        min-height: 38px;

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding:
            6px 10px;

        background:
            #f6f8fa;

        border-bottom:
            1px solid #dce3e9;

        color:
            #263238;

        font-size:
            13px;

    }


    .censo-servicio-header span {

        color:
            #64748b;

        font-size:
            10px;

        font-weight:
            normal;

    }



    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */

    .tabla-censo-actual {

        font-size:
            10px;

    }


    .tabla-censo-actual thead th {

        padding:
            4px 5px;

        border-top:
            0;

        border-bottom:
            1px solid #dde3e8;

        background:
            #fff;

        color:
            #475569;

        font-size:
            9px;

        font-weight:
            600;

        white-space:
            nowrap;

    }


    .tabla-censo-actual tbody td {

        padding:
            4px 5px;

        vertical-align:
            middle;

        line-height:
            1.15;

        border-top:
            1px solid #edf0f2;

    }


    .tabla-censo-actual tbody td strong {

        font-size:
            10px;

        color:
            #222;

    }


    .col-hc {
        width: 55px;
    }


    .col-sexo {
        width: 38px;
        text-align: center;
    }


    .col-fecha {
        width: 68px;
    }


    .col-cama {
        width: 38px;
    }


    .col-servicio {
        width: 90px;
    }

    .col-telefono {
        width: 95px;
        white-space: nowrap;
    }



    /*
    |--------------------------------------------------------------------------
    | MONITORES GRANDES
    |--------------------------------------------------------------------------
    */

    @media (min-width: 1600px) {

        .tabla-censo-actual {

            font-size:
                11px;

        }

        .tabla-censo-actual tbody td strong {

            font-size:
                11px;

        }

    }



    /*
    |--------------------------------------------------------------------------
    | TABLET / CELULAR
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {

        .tabla-censo-actual {

            min-width:
                680px;

        }

    }
</style>

@stop