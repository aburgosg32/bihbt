@extends('adminlte::page')

@section('title', 'Radiología - Reportes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>
    <i class="fas fa-file-alt"></i>
    Reportes de Radiología
</h1>
@stop

@section('content')

<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">Filtros de Reporte</h3>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('radiologia.reportes') }}">
            <div class="row">

                <div class="col-md-2">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Modalidad</label>
                    <select name="modalidad" class="form-control">
                        <option value="">Todas</option>
                        <option value="RAYOS X" {{ $modalidad == 'RAYOS X' ? 'selected' : '' }}>Rayos X</option>
                        <option value="TOMOGRAFIA" {{ $modalidad == 'TOMOGRAFIA' ? 'selected' : '' }}>Tomografía</option>
                        <option value="ECOGRAFIA" {{ $modalidad == 'ECOGRAFIA' ? 'selected' : '' }}>Ecografía</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="REGISTRADO" {{ $estado == 'REGISTRADO' ? 'selected' : '' }}>Registrado</option>
                        <option value="ATENDIDO" {{ $estado == 'ATENDIDO' ? 'selected' : '' }}>Atendido</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Buscar</label>
                    <input type="text"
                        name="buscar"
                        value="{{ $buscar }}"
                        class="form-control"
                        placeholder="Cuenta, HC, paciente, código, estudio, servicio o financiamiento">
                </div>

            </div>

            <div class="row mt-3">

                <div class="col-md-2">
                    <button class="btn btn-success btn-block">
                        <i class="fas fa-search"></i>
                        Consultar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('radiologia.reportes') }}"
                        class="btn btn-outline-success btn-block">
                        Limpiar
                    </a>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a href="{{ route('radiologia.reportes.excel', request()->query()) }}"
                        class="btn btn-success btn-block">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a href="{{ route('radiologia.reportes.pdf', request()->query()) }}"
                        target="_blank"
                        class="btn btn-outline-success btn-block">
                        <i class="fas fa-file-pdf"></i>
                        PDF
                    </a>
                </div>

            </div>

        </form>
    </div>
</div>


<div class="row">

    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Registros</p>
            </div>
            <div class="icon">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $registrados }}</h3>
                <p>Registrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $atendidos }}</h3>
                <p>Atendidos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

</div>


<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            Resultado del Reporte
        </h3>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover table-sm">
            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>HC</th>
                    <th>Paciente</th>
                    <th>Edad</th>
                    <th>Modalidad</th>
                    <th>Código</th>
                    <th>Estudio</th>
                    <th>Tipo Servicio</th>
                    <th>Servicio</th>
                    <th>Financiamiento</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>
                @forelse($citas as $item)
                <tr>
                    <td>{{ $item->id_cuenta_atencion }}</td>
                    <td>{{ $item->nro_historia_clinica }}</td>
                    <td>{{ $item->paciente }}</td>
                    <td>{{ $item->edad }} {{ $item->tipo_edad }}</td>
                    <td>{{ $item->modalidad }}</td>
                    <td>{{ $item->codigo_estudio }}</td>
                    <td>{{ $item->estudio }}</td>
                    <td>{{ $item->tipo_servicio }}</td>
                    <td>{{ $item->nombre_servicio }}</td>
                    <td>{{ $item->fuente_financiamiento }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->fecha_cita)) }}</td>
                    <td>{{ substr($item->hora_cita, 0, 5) }}</td>
                    <td>
                        @if($item->estado === 'ATENDIDO')
                        <span class="badge badge-success">ATENDIDO</span>
                        @else
                        <span class="badge badge-warning">REGISTRADO</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center">
                        No existen registros con los filtros seleccionados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

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