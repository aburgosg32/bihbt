@extends('adminlte::page')

@section('title', 'Reportes y Auditoría | HBT')

@section('plugins.Select2', true)

@section('content_header')
<h1>
    Reportes y Auditoría
    <small class="text-muted">/ Movimientos SIGH</small>
</h1>
@stop

@section('content')
<div class="container-fluid auditoria-hbt">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card auditoria-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filtros de búsqueda
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('auditoria.index') }}">
                <input type="hidden" name="buscar" value="1">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha inicial</label>
                            <input
                                type="date"
                                name="fecha_inicio"
                                class="form-control"
                                value="{{ old('fecha_inicio', request('fecha_inicio', now()->format('Y-m-d'))) }}"
                                required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha final</label>
                            <input
                                type="date"
                                name="fecha_fin"
                                class="form-control"
                                value="{{ old('fecha_fin', request('fecha_fin', now()->format('Y-m-d'))) }}"
                                required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario SIGH</label>
                            <select
                                name="usuario"
                                class="form-control auditoria-select">
                                <option value="">Todos los usuarios</option>
                                @foreach ($usuarios as $usuario)
                                <option
                                    value="{{ $usuario->IdEmpleado }}"
                                    @selected((string) request('usuario')===(string) $usuario->IdEmpleado)
                                    >
                                    {{ trim($usuario->Usuario) }}
                                    — {{ $usuario->Empleado }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número de cuenta</label>
                            <input
                                type="number"
                                min="1"
                                max="2147483647"
                                name="cuenta"
                                class="form-control"
                                value="{{ request('cuenta') }}"
                                placeholder="Ejemplo: 2594440">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Servicio de ingreso de la atención</label>
                            <select
                                name="servicio"
                                class="form-control auditoria-select">
                                <option value="">Todos los servicios</option>
                                @foreach ($servicios as $servicio)
                                <option
                                    value="{{ $servicio->IdServicio }}"
                                    @selected((string) request('servicio')===(string) $servicio->IdServicio)
                                    >
                                    {{ $servicio->Nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Acción</label>
                            <select name="accion" class="form-control">
                                <option value="">Todas las acciones</option>
                                @foreach (['A', 'M', 'E', 'C', 'T'] as $accion)
                                <option
                                    value="{{ $accion }}"
                                    @selected(request('accion')===$accion)>
                                    {{ $accion }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tabla o evento — nombre exacto</label>
                            <input
                                type="text"
                                name="tabla"
                                class="form-control"
                                maxlength="100"
                                value="{{ request('tabla') }}"
                                placeholder="Ejemplo: Atenciones, FUA, Citas">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Equipo</label>
                            <input
                                type="text"
                                name="equipo"
                                class="form-control"
                                maxlength="100"
                                value="{{ request('equipo') }}"
                                placeholder="Nombre del equipo">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Texto en observaciones</label>
                            <input
                                type="text"
                                name="observaciones"
                                class="form-control"
                                maxlength="200"
                                value="{{ request('observaciones') }}"
                                placeholder="Texto a buscar">
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center">
                    <button type="submit" class="btn btn-primary mr-2 mb-2">
                        <i class="fas fa-search mr-1"></i>
                        Buscar movimientos
                    </button>

                    <a
                        href="{{ route('auditoria.index') }}"
                        class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-eraser mr-1"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if (request('cuenta') || request('servicio'))
    <div class="alert alert-info">
        El filtro por cuenta o servicio incluye los movimientos de
        <strong>Atenciones, AtencionesCE,
            FacturacionCuentasAtencion y FUA</strong>.
        El servicio corresponde al ingreso de la atención según
        los datos disponibles actualmente en SIGH.
    </div>
    @endif

    <div class="card auditoria-card">
        <div class="card-header d-flex flex-wrap align-items-center">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Movimientos registrados
            </h3>

            @if ($movimientos && $movimientos->count())
            <a
                href="{{ route('auditoria.exportar', request()->only([
                        'fecha_inicio', 'fecha_fin', 'usuario', 'cuenta',
                        'servicio', 'tabla', 'accion', 'equipo',
                        'observaciones'
                    ])) }}"
                class="btn btn-success btn-sm ml-auto">
                <i class="fas fa-file-excel mr-1"></i>
                Exportar CSV para Excel
            </a>
            @endif
        </div>

        <div class="card-body p-0">
            @if ($movimientos === null)
            <div class="p-4 text-center text-muted">
                Selecciona los filtros y pulsa
                <strong>Buscar movimientos</strong>.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID / Fecha y hora</th>
                            <th>Usuario / Empleado</th>
                            <th>Tabla o evento</th>
                            <th>ID Registro</th>
                            <th>Acción</th>
                            <th>Equipo</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($movimientos as $movimiento)
                        <tr>
                            <td class="text-nowrap">
                                <strong>
                                    {{ \Carbon\Carbon::parse($movimiento->FechaHora)->format('d/m/Y') }}
                                </strong>
                                <br>
                                {{ \Carbon\Carbon::parse($movimiento->FechaHora)->format('H:i:s.v') }}
                                <br>
                                <small class="text-muted">
                                    #{{ $movimiento->IdAuditoria }}
                                </small>
                            </td>

                            <td>
                                <strong class="auditoria-usuario">
                                    {{ $movimiento->Usuario ?: 'Sin usuario' }}
                                </strong>
                                <br>
                                <small>
                                    {{ $movimiento->Empleado ?: 'Sin nombre' }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    ID empleado:
                                    {{ $movimiento->IdEmpleado }}
                                </small>
                            </td>

                            <td class="auditoria-tabla">
                                {{ $movimiento->Tabla }}
                            </td>

                            <td>
                                {{ $movimiento->IdRegistro }}
                            </td>

                            <td>
                                <span class="badge badge-primary">
                                    {{ $movimiento->Accion }}
                                </span>
                            </td>

                            <td class="auditoria-equipo">
                                {{ $movimiento->Equipo ?: '—' }}
                            </td>

                            <td class="auditoria-observaciones">
                                @if (trim($movimiento->Observaciones ?? '') !== '')
                                <details>
                                    <summary>
                                        {{ \Illuminate\Support\Str::limit($movimiento->Observaciones, 85) }}
                                    </summary>
                                    <div class="mt-2 auditoria-detalle">
                                        {{ $movimiento->Observaciones }}
                                    </div>
                                </details>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">
                                No se encontraron movimientos
                                con los filtros seleccionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @if ($movimientos && $movimientos->count())
        <div class="card-footer">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <span class="text-muted mb-2">
                    Página {{ $movimientos->currentPage() }}
                    · {{ $movimientos->count() }} movimientos en esta página
                </span>

                <div class="mb-2">
                    @if ($movimientos->previousPageUrl())
                    <a
                        href="{{ $movimientos->previousPageUrl() }}"
                        class="btn btn-outline-primary btn-sm">
                        Anterior
                    </a>
                    @endif

                    @if ($movimientos->hasMorePages())
                    <a
                        href="{{ $movimientos->nextPageUrl() }}"
                        class="btn btn-outline-primary btn-sm">
                        Siguiente
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .auditoria-hbt .auditoria-card {
        border-top: 3px solid #1565a8;
        border-radius: 10px;
        overflow: hidden;
    }

    .auditoria-hbt .card-header {
        background: #edf4fb;
        color: #164a75;
    }

    .auditoria-hbt label {
        color: #234b6c;
        font-size: .9rem;
    }

    .auditoria-hbt thead th {
        background: #164a75;
        color: #fff;
        border: 0;
        vertical-align: middle;
    }

    .auditoria-hbt tbody td {
        vertical-align: top;
        font-size: .88rem;
    }

    .auditoria-usuario {
        color: #1565a8;
    }

    .auditoria-tabla,
    .auditoria-equipo {
        overflow-wrap: anywhere;
        min-width: 130px;
    }

    .auditoria-observaciones {
        min-width: 260px;
        max-width: 420px;
        overflow-wrap: anywhere;
    }

    .auditoria-observaciones summary {
        cursor: pointer;
        color: #234b6c;
    }

    .auditoria-detalle {
        white-space: pre-wrap;
        background: #edf4fb;
        padding: 10px;
        border-radius: 6px;
    }

    .auditoria-hbt .select2-container {
        width: 100% !important;
    }

    .auditoria-hbt .select2-container--default .select2-selection--single {
        height: 38px;
        border-color: #ced4da;
    }

    .auditoria-hbt .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }

    .auditoria-hbt .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-results__options {
        max-height: 350px !important;
    }
</style>
@stop

@section('js')
<script>
    $(function() {
        $('.auditoria-select').select2({
            width: '100%',
            language: {
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            }
        });
    });
</script>
@stop