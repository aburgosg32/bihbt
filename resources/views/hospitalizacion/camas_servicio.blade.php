@extends('adminlte::page')

@section('title', 'Camas por Servicio - Hospitalización')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
<style>
    .kpi-card {
        border: 1px solid var(--hbt-border);
        background: #ffffff;
        padding: 18px;
        min-height: 96px;
    }

    .kpi-card h3 {
        font-size: 30px;
        margin: 0;
        font-weight: 700;
        color: var(--hbt-primary);
    }

    .kpi-card p {
        margin: 4px 0 0;
        color: var(--hbt-muted);
        font-weight: 500;
        font-size: 13px;
    }

    .table-camas thead th {
        background: #F3F2F1 !important;
        color: var(--hbt-text) !important;
        font-size: 12px;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-camas tbody td {
        font-size: 12px;
        vertical-align: middle;
    }

    .badge-libre {
        background: #DFF6DD;
        color: #107C10;
        border: 1px solid #B7E2B2;
        font-weight: 700;
        padding: 6px 10px;
    }

    .badge-ocupada {
        background: #FDE7E9;
        color: #A4262C;
        border: 1px solid #F3B5BA;
        font-weight: 700;
        padding: 6px 10px;
    }

    .badge-cama {
        background: #E5F1FB;
        color: #005A9E;
        border: 1px solid #BBDCF5;
        font-weight: 700;
        padding: 6px 10px;
    }

    .paciente-cell {
        min-width: 260px;
    }

    .servicio-cell {
        min-width: 230px;
    }

    .pagination {
        margin-bottom: 0;
    }
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="mb-1">
            CAMAS HOSPITALIZACIÓN
        </h1>
        <small class="text-muted">
            Hospital Belén de Trujillo · Hospitalización
        </small>
    </div>

    <div class="hbt-header-date">
        Total camas: {{ $resumen->total_camas ?? 0 }}
    </div>
</div>

<form method="GET" action="{{ route('hospitalizacion.camas-servicio') }}" class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Servicio</label>
                <select name="servicio" class="form-control">
                    <option value="">Todos los servicios</option>
                    @foreach($servicios as $item)
                    <option value="{{ $item }}" {{ $servicio == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    @foreach($estados as $item)
                    <option value="{{ $item }}" {{ $estado == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Buscar</label>
                <input type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    class="form-control"
                    placeholder="Cama, paciente, servicio, especialidad...">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-search mr-1"></i>
                    Filtrar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('hospitalizacion.camas-servicio') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Limpiar
                </a>
            </div>

        </div>
    </div>
</form>
@stop

@section('content')

<div class="row">

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumen->total_camas ?? 0 }}</h3>
            <p>Total camas</p>
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumen->ocupadas ?? 0 }}</h3>
            <p>Ocupadas</p>
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumen->libres ?? 0 }}</h3>
            <p>Libres</p>
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumenServicio->total ?? 0 }}</h3>
            <p>Total servicio</p>
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumenServicio->ocupadas ?? 0 }}</h3>
            <p>Ocupadas servicio</p>
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <div class="kpi-card mb-3">
            <h3>{{ $resumenServicio->libres ?? 0 }}</h3>
            <p>Libres servicio</p>
        </div>
    </div>

</div>

@if($servicio)
<div class="alert alert-info py-2">
    <strong>Servicio seleccionado:</strong> {{ $servicio }}
</div>
@endif

<div class="card">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-bed mr-1"></i>
            Listado de camas por servicio
        </h3>

        <div class="card-tools">
            <span class="badge badge-light">
                Total: {{ $camas->total() }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm table-camas mb-0">
                <thead>
                    <tr>
                        <th>Cama</th>
                        <th>Servicio</th>
                        <th>Especialidad</th>
                        <th>Tipo cama</th>
                        <th>Tipo servicio</th>
                        <th>Estado cama</th>
                        <th>Estado operativo</th>
                        <th>Paciente</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($camas as $cama)
                    <tr>
                        <td>
                            <span class="badge-cama">
                                {{ $cama->CAMA }}
                            </span>
                        </td>

                        <td class="servicio-cell">
                            {{ $cama->SERVICIO }}
                        </td>

                        <td>{{ $cama->ESPECIALIDAD }}</td>

                        <td>{{ $cama->TIPO_CAMA }}</td>

                        <td>{{ $cama->TIPO_SERVICIO }}</td>

                        <td>{{ $cama->ESTADO_CAMA ?? 'NO REGISTRADO' }}</td>

                        <td>
                            @if($cama->ESTADO_OPERATIVO === 'OCUPADA')
                            <span class="badge-ocupada">
                                OCUPADA
                            </span>
                            @else
                            <span class="badge-libre">
                                LIBRE
                            </span>
                            @endif
                        </td>

                        <td class="paciente-cell">
                            @if($cama->PACIENTE)
                            <strong>{{ $cama->PACIENTE }}</strong>
                            @else
                            <span class="text-muted">SIN PACIENTE</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            No se encontraron camas con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-muted small mb-2 mb-md-0">
            Mostrando {{ $camas->firstItem() }} a {{ $camas->lastItem() }}
            de {{ $camas->total() }} registros
        </div>

        <div class="pagination-sm">
            {{ $camas->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@stop