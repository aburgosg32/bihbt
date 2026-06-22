@extends('adminlte::page')

@section('title', 'Pacientes por Consultorio - Consulta Externa')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .table-consulta thead th {
        background: #F3F2F1 !important;
        color: var(--hbt-text) !important;
        font-size: 11px;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-consulta tbody td {
        font-size: 12px;
        vertical-align: middle;
    }

    .paciente-cell {
        min-width: 260px;
    }

    .consultorio-cell {
        min-width: 220px;
    }

    .badge-consultorio {
        background: #E5F1FB;
        color: #005A9E;
        border: 1px solid #BBDCF5;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .badge-atendido {
        background: #DFF6DD;
        color: #107C10;
        border: 1px solid #B7E2B2;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .badge-pendiente {
        background: #FFF4CE;
        color: #8A6A00;
        border: 1px solid #F2C811;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
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
            Atenciones Consulta Externa
        </h1>
        <small class="text-muted">
            Hospital Belén de Trujillo · Consulta Externa
        </small>
    </div>

    <div class="hbt-header-date">
        {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        · Total: {{ $pacientes->total() }}
    </div>
</div>

<form method="GET" action="{{ route('consulta.pacientes-consultorio') }}" class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-end">

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">Fecha inicio</label>
                <input type="date"
                    name="fecha_inicio"
                    value="{{ $fechaInicio }}"
                    class="form-control">
            </div>

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">Fecha fin</label>
                <input type="date"
                    name="fecha_fin"
                    value="{{ $fechaFin }}"
                    class="form-control">
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Consultorio</label>
                <select name="consultorio" class="form-control">
                    <option value="">Todos los consultorios</option>
                    @foreach($consultorios as $item)
                    <option value="{{ $item }}" {{ $consultorio == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="ATENDIDO" {{ $estado == 'ATENDIDO' ? 'selected' : '' }}>
                        ATENDIDO
                    </option>
                    <option value="PENDIENTE" {{ $estado == 'PENDIENTE' ? 'selected' : '' }}>
                        PENDIENTE
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Buscar</label>
                <input type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    class="form-control"
                    placeholder="Cuenta, HC, paciente, teléfono...">
            </div>

        </div>

        <div class="row mt-3">
            <div class="col-md-8"></div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-search mr-1"></i>
                    Filtrar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('consulta.pacientes-consultorio') }}" class="btn btn-outline-secondary btn-block">
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

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPacientes }}</h3>
                <p>Total Pacientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalAtendidos }}</h3>
                <p>Atendidos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalPendientes }}</h3>
                <p>Pendientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $consultoriosActivos }}</h3>
                <p>Consultorios Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-stethoscope mr-1"></i>
            Listado de pacientes por consultorio
        </h3>

        <div class="card-tools">
            <span class="badge badge-light">
                Total: {{ $pacientes->total() }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm table-consulta mb-0">
                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>HC</th>
                        <th>Paciente</th>
                        <th>Teléfono</th>
                        <th>Fecha nac.</th>
                        <th>Consultorio</th>
                        <th>Ingreso</th>
                        <th>Hora ingreso</th>
                        <th>Hora egreso</th>
                        <th>Financiamiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pacientes as $p)
                    <tr>
                        <td>{{ $p->NROCUENTA }}</td>

                        <td>{{ $p->HISTORIA_CLINICA }}</td>

                        <td class="paciente-cell">
                            <strong>{{ $p->PACIENTE }}</strong><br>
                            <small class="text-muted">
                                ID Atención: {{ $p->IdAtencion }}
                            </small>
                        </td>

                        <td>{{ $p->TELEFONO ?? '-' }}</td>

                        <td>
                            {{ $p->FECHA_NACIMIENTO ? \Carbon\Carbon::parse($p->FECHA_NACIMIENTO)->format('d/m/Y') : '-' }}
                        </td>

                        <td class="consultorio-cell">
                            <span class="badge-consultorio">
                                {{ $p->CONSULTORIO ?? 'SIN CONSULTORIO' }}
                            </span>
                        </td>

                        <td>
                            {{ $p->FECHA_INGRESO ? \Carbon\Carbon::parse($p->FECHA_INGRESO)->format('d/m/Y') : '-' }}
                        </td>

                        <td>{{ $p->HORA_INGRESO ? substr($p->HORA_INGRESO, 0, 5) : '-' }}</td>

                        <td>{{ $p->HORA_EGRESO ? substr($p->HORA_EGRESO, 0, 5) : '-' }}</td>

                        <td>{{ $p->FUENTE_FINANCIAMIENTO ?? '-' }}</td>

                        <td class="text-center">
                            @if($p->ESTADO_PACIENTE == 'ATENDIDO')
                            <span class="badge-atendido">
                                ATENDIDO
                            </span>
                            @else
                            <span class="badge-pendiente">
                                PENDIENTE
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            No se encontraron pacientes con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-muted small mb-2 mb-md-0">
            Mostrando {{ $pacientes->firstItem() }} a {{ $pacientes->lastItem() }}
            de {{ $pacientes->total() }} registros
        </div>

        <div class="pagination-sm">
            {{ $pacientes->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@stop