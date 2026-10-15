@extends('adminlte::page')

@section('title', 'Solicitudes quirúrgicas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-0">
            <i class="fas fa-clipboard-list mr-2"></i>
            Solicitudes quirúrgicas
        </h1>

        <small class="text-muted">
            Seguimiento general de solicitudes
        </small>
    </div>

    <a
        href="{{ route('quirofano.solicitudes.create') }}"
        class="btn btn-primary btn-sm">
        <i class="fas fa-plus mr-1"></i>
        Nueva solicitud
    </a>
</div>
@stop

@section('content')
<link
    rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">

@if (session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('success') }}
</div>
@endif

{{-- Resumen --}}
<div class="row quirofano-resumen">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $resumen['total'] }}</h3>
                <p>Total de solicitudes</p>
            </div>

            <div class="icon">
                <i class="fas fa-file-medical"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $resumen['enviadas'] }}</h3>
                <p>Pendientes de revisión</p>
            </div>

            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $resumen['aprobadas'] }}</h3>
                <p>Aprobadas</p>
            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $resumen['programadas'] }}</h3>
                <p>Programadas</p>
            </div>

            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Filtros
        </h3>
    </div>

    <div class="card-body">
        <form
            method="GET"
            action="{{ route('quirofano.solicitudes.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Búsqueda general</label>

                        <input
                            type="text"
                            name="buscar"
                            class="form-control form-control-sm"
                            value="{{ request('buscar') }}"
                            placeholder="Cuenta, HC, paciente, médico...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Servicio</label>

                        <select
                            name="servicio"
                            class="form-control form-control-sm">
                            <option value="">
                                Todos
                            </option>

                            @foreach ($servicios as $servicio)
                            <option
                                value="{{ $servicio }}"
                                @selected(
                                request('servicio')===$servicio
                                )>
                                {{ $servicio }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Estado</label>

                        <select
                            name="estado"
                            class="form-control form-control-sm">
                            <option value="">
                                Todos
                            </option>

                            @foreach ($estados as $estado)
                            <option
                                value="{{ $estado }}"
                                @selected(
                                request('estado')===$estado
                                )>
                                {{ str_replace('_', ' ', $estado) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de solicitud</label>

                        <select
                            name="tipo_solicitud"
                            class="form-control form-control-sm">
                            <option value="">
                                Todos
                            </option>

                            @foreach ($tipos as $tipo)
                            <option
                                value="{{ $tipo }}"
                                @selected(
                                request('tipo_solicitud')===$tipo
                                )>
                                {{ $tipo }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Prioridad</label>

                        <select
                            name="prioridad"
                            class="form-control form-control-sm">
                            <option value="">
                                Todas
                            </option>

                            @foreach ($prioridades as $prioridad)
                            <option
                                value="{{ $prioridad }}"
                                @selected(
                                request('prioridad')===$prioridad
                                )>
                                {{ $prioridad }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha desde</label>

                        <input
                            type="date"
                            name="fecha_desde"
                            class="form-control form-control-sm"
                            value="{{ request('fecha_desde') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha hasta</label>

                        <input
                            type="date"
                            name="fecha_hasta"
                            class="form-control form-control-sm"
                            value="{{ request('fecha_hasta') }}">
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button
                            type="submit"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>

                        <a
                            href="{{ route(
                                    'quirofano.solicitudes.index'
                                ) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="fas fa-eraser mr-1"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-1"></i>
            Solicitudes registradas
        </h3>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>N.°</th>
                        <th>Fecha</th>
                        <th>Cuenta / HC</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Procedimiento</th>
                        <th class="text-center">Prioridad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($solicitudes as $solicitud)
                    @php
                    $claseEstado = match (
                    $solicitud->estado
                    ) {
                    'ENVIADA' => 'warning',
                    'EN_REVISION' => 'info',
                    'OBSERVADA' => 'secondary',
                    'APROBADA' => 'success',
                    'PROGRAMADA' => 'primary',
                    'CANCELADA' => 'dark',
                    'RECHAZADA' => 'danger',
                    'FINALIZADA' => 'success',
                    default => 'secondary',
                    };

                    $clasePrioridad = match (
                    $solicitud->prioridad
                    ) {
                    'BAJA' => 'secondary',
                    'MEDIA' => 'info',
                    'ALTA' => 'warning',
                    'CRITICA' => 'danger',
                    default => 'secondary',
                    };
                    @endphp

                    <tr>
                        <td class="align-middle">
                            <strong>
                                {{ str_pad(
                                            $solicitud->id,
                                            6,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
                            </strong>
                        </td>

                        <td class="align-middle text-nowrap">
                            {{ optional(
                                        $solicitud->fecha_envio
                                    )->format('d/m/Y') }}

                            <div class="small text-muted">
                                {{ optional(
                                            $solicitud->fecha_envio
                                        )->format('H:i') }}
                            </div>
                        </td>

                        <td class="align-middle">
                            <strong>
                                {{ $solicitud->numero_cuenta }}
                            </strong>

                            <div class="small text-muted">
                                HC:
                                {{ $solicitud->historia_clinica
                                            ?: 'Sin HC' }}
                            </div>
                        </td>

                        <td class="align-middle">
                            {{ $solicitud->paciente }}
                        </td>

                        <td class="align-middle">
                            {{ $solicitud->servicio_solicitante }}
                        </td>

                        <td class="align-middle">
                            <strong>
                                {{ $solicitud->procedimiento_codigo }}
                            </strong>

                            <div class="small text-muted">
                                {{ $solicitud->procedimiento_nombre }}
                            </div>
                        </td>

                        <td class="text-center align-middle">
                            <span
                                class="badge badge-{{ $clasePrioridad }}">
                                {{ $solicitud->prioridad }}
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            <span
                                class="badge badge-{{ $claseEstado }}">
                                {{ str_replace(
                                            '_',
                                            ' ',
                                            $solicitud->estado
                                        ) }}
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            <button
                                type="button"
                                class="btn btn-info btn-sm"
                                data-toggle="modal"
                                data-target="#detalleSolicitud{{ $solicitud->id }}"
                                title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal detalle --}}
                    <div
                        class="modal fade"
                        id="detalleSolicitud{{ $solicitud->id }}"
                        tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-info">
                                    <h5 class="modal-title">
                                        Solicitud quirúrgica N.°
                                        {{ str_pad(
                                                    $solicitud->id,
                                                    6,
                                                    '0',
                                                    STR_PAD_LEFT
                                                ) }}
                                    </h5>

                                    <button
                                        type="button"
                                        class="close text-white"
                                        data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Paciente</strong>
                                            <p>
                                                {{ $solicitud->paciente }}
                                            </p>
                                        </div>

                                        <div class="col-md-2">
                                            <strong>Cuenta</strong>
                                            <p>
                                                {{ $solicitud->numero_cuenta }}
                                            </p>
                                        </div>

                                        <div class="col-md-2">
                                            <strong>Historia</strong>
                                            <p>
                                                {{ $solicitud->historia_clinica }}
                                            </p>
                                        </div>

                                        <div class="col-md-2">
                                            <strong>DNI</strong>
                                            <p>
                                                {{ $solicitud->dni ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-2">
                                            <strong>Sexo / Edad</strong>
                                            <p>
                                                {{ $solicitud->sexo ?: '-' }}
                                                /
                                                {{ $solicitud->edad ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Servicio solicitante</strong>
                                            <p>
                                                {{ $solicitud->servicio_solicitante }}
                                            </p>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Médico</strong>
                                            <p>
                                                {{ $solicitud->medico_nombre }}
                                            </p>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Especialidad</strong>
                                            <p>
                                                {{ $solicitud->medico_especialidad
                                                            ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-12">
                                            <strong>
                                                Diagnóstico
                                            </strong>
                                            <p>
                                                {{ $solicitud->diagnostico_codigo }}
                                                -
                                                {{ $solicitud->diagnostico_nombre }}
                                            </p>
                                        </div>

                                        <div class="col-md-12">
                                            <strong>
                                                Procedimiento
                                            </strong>
                                            <p>
                                                {{ $solicitud->procedimiento_codigo }}
                                                -
                                                {{ $solicitud->procedimiento_nombre }}
                                            </p>
                                        </div>

                                        <div class="col-md-3">
                                            <strong>Tipo</strong>
                                            <p>
                                                {{ $solicitud->tipo_solicitud }}
                                            </p>
                                        </div>

                                        <div class="col-md-3">
                                            <strong>Prioridad</strong>
                                            <p>
                                                {{ $solicitud->prioridad }}
                                            </p>
                                        </div>

                                        <div class="col-md-3">
                                            <strong>Fecha sugerida</strong>
                                            <p>
                                                {{ optional(
                                                            $solicitud->fecha_sugerida
                                                        )->format('d/m/Y') ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-3">
                                            <strong>Duración</strong>
                                            <p>
                                                {{ $solicitud
                                                            ->duracion_estimada_minutos
                                                            ? $solicitud
                                                                ->duracion_estimada_minutos .
                                                                ' minutos'
                                                            : '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <strong>Requerimientos</strong>
                                            <p>
                                                {{ $solicitud
                                                            ->requerimientos_especiales
                                                            ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <strong>Observaciones</strong>
                                            <p>
                                                {{ $solicitud->observaciones
                                                            ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-dismiss="modal">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-muted py-4">
                            No se encontraron solicitudes.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($solicitudes->hasPages())
    <div class="card-footer">
        {{ $solicitudes->links(
                    'pagination::bootstrap-4'
                ) }}
    </div>
    @endif
</div>
@stop