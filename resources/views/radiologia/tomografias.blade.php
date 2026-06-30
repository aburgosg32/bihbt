@extends('adminlte::page')

@section('title', 'Radiología - Tomografías')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>
    <i class="fas fa-procedures"></i>
    Tomografías
</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<div class="card card-outline card-success">

    <div class="card-header">
        <h3 class="card-title">
            Pacientes registrados en Tomografía
        </h3>
    </div>

    <div class="card-body">

        <form method="GET"
            action="{{ route('radiologia.tomografias') }}"
            class="mb-3">

            <div class="row">

                <div class="col-md-5">
                    <input type="text"
                        name="buscar"
                        value="{{ $buscar ?? '' }}"
                        class="form-control"
                        placeholder="Buscar por cuenta, historia, paciente o estudio">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success btn-block">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('radiologia.tomografias') }}"
                        class="btn btn-outline-success btn-block">
                        Limpiar
                    </a>
                </div>

            </div>

        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">

                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>HC</th>
                        <th>Paciente</th>
                        <th>Edad</th>
                        <th>Código</th>
                        <th>Estudio</th>
                        <th>Tipo Servicio</th>
                        <th>Servicio</th>
                        <th>Financiamiento</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th width="120">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($citas as $item)

                    <tr>
                        <td>{{ $item->id_cuenta_atencion }}</td>
                        <td>{{ $item->nro_historia_clinica }}</td>
                        <td>{{ $item->paciente }}</td>
                        <td>{{ $item->edad }} {{ $item->tipo_edad }}</td>
                        <td>{{ $item->codigo_estudio }}</td>
                        <td>{{ $item->estudio }}</td>
                        <td>{{ $item->tipo_servicio }}</td>
                        <td>{{ $item->nombre_servicio }}</td>
                        <td>{{ $item->fuente_financiamiento }}</td>
                        <td>{{ date('d-m-Y', strtotime($item->fecha_cita)) }}</td>
                        <td>{{ substr($item->hora_cita, 0, 5) }}</td>

                        <td>
                            @if($item->estado === 'ATENDIDO')
                            <span class="badge badge-success">
                                ATENDIDO
                            </span>
                            @else
                            <span class="badge badge-warning">
                                REGISTRADO
                            </span>
                            @endif
                        </td>

                        <td>
                            @if($item->estado === 'REGISTRADO')
                            <button type="button"
                                class="btn btn-success btn-sm"
                                data-toggle="modal"
                                data-target="#modalAtenderTomografia{{ $item->id }}">
                                Atender
                            </button>
                            @else
                            <button class="btn btn-outline-success btn-sm" disabled>
                                Atendido
                            </button>
                            @endif
                        </td>
                    </tr>

                    {{-- MODAL CONFIRMAR ATENCIÓN --}}
                    <div class="modal fade"
                        id="modalAtenderTomografia{{ $item->id }}"
                        tabindex="-1"
                        role="dialog">

                        <div class="modal-dialog modal-sm"
                            role="document">

                            <form method="POST"
                                action="{{ route('radiologia.tomografias.atender', $item->id) }}">

                                @csrf

                                <div class="modal-content">

                                    <div class="modal-header bg-success">
                                        <h5 class="modal-title">
                                            Confirmar Atención
                                        </h5>

                                        <button type="button"
                                            class="close text-white"
                                            data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <p>
                                            ¿Desea marcar como atendido?
                                        </p>

                                        <p class="mb-1">
                                            <strong>Paciente:</strong><br>
                                            {{ $item->paciente }}
                                        </p>

                                        <p class="mb-1">
                                            <strong>Estudio:</strong><br>
                                            {{ $item->estudio }}
                                        </p>

                                        <p class="mb-0">
                                            <strong>Fecha/Hora:</strong><br>
                                            {{ date('d-m-Y', strtotime($item->fecha_cita)) }}
                                            {{ substr($item->hora_cita, 0, 5) }}
                                        </p>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button"
                                            class="btn btn-outline-success btn-sm"
                                            data-dismiss="modal">
                                            Cancelar
                                        </button>

                                        <button type="submit"
                                            class="btn btn-success btn-sm">
                                            Sí, atender
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>

                    @empty

                    <tr>
                        <td colspan="8"
                            class="text-center">
                            No existen registros de Tomografía.
                        </td>
                    </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

    <div class="card-footer clearfix">
        <div class="float-left">
            @if($citas->total() > 0)
            Mostrando {{ $citas->firstItem() }} a {{ $citas->lastItem() }}
            de {{ $citas->total() }} registros
            @else
            Sin registros
            @endif
        </div>

        <div class="float-right">
            {{ $citas->links() }}
        </div>
    </div>

</div>

@stop