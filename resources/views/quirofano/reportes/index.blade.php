@extends('adminlte::page')

@section('title', 'Reportes Quirúrgicos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-0">
            <i class="fas fa-chart-bar text-hbt"></i>
            Reportes Quirúrgicos
        </h1>

        <small class="text-muted">
            Control de tiempos, productividad y utilización de salas
        </small>
    </div>
</div>
@stop

@section('content')
@php
$formatearFechaHora = function ($fecha) {
return $fecha
? \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i')
: '-';
};

$formatearHora = function ($fecha) {
return $fecha
? \Carbon\Carbon::parse($fecha)->format('H:i')
: '-';
};

$formatearMinutos = function ($minutos) {
if ($minutos === null) {
return '-';
}

$minutos = (int) round($minutos);

if ($minutos < 60) {
    return $minutos . ' min' ;
    }

    $horas=intdiv($minutos, 60);
    $resto=$minutos % 60;

    if ($resto===0) {
    return $horas . ' h' ;
    }

    return $horas . ' h ' . $resto . ' min' ;
    };

    $estadoClase=function ($estado) {
    return match ($estado) { 'PROGRAMADA'=> 'badge-primary',
    'EN_PREPARACION' => 'badge-warning',
    'EN_CURSO' => 'badge-danger',
    'LIMPIEZA' => 'badge-info',
    'FINALIZADA',
    'CULMINADA' => 'badge-success',
    'CANCELADA' => 'badge-secondary',
    default => 'badge-light',
    };
    };

    $estadoTexto = function ($estado) {
    return match ($estado) {
    'EN_PREPARACION' => 'EN PREPARACIÓN',
    'EN_CURSO' => 'EN CURSO',
    default => str_replace('_', ' ', $estado),
    };
    };
    @endphp

    {{-- ============================================================= --}}
    {{-- FILTROS                                                       --}}
    {{-- ============================================================= --}}

    <div class="card card-outline card-hbt">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i>
                Filtros del reporte
            </h3>

            <div class="card-tools">
                <button
                    type="button"
                    class="btn btn-tool"
                    data-card-widget="collapse"
                    title="Ocultar filtros">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body pb-2">
            <form
                method="GET"
                action="{{ route('quirofano.reportes.index') }}"
                id="formFiltros">
                <div class="row">
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_desde">
                                Fecha desde
                            </label>

                            <input
                                type="date"
                                name="fecha_desde"
                                id="fecha_desde"
                                class="form-control form-control-sm"
                                value="{{ request(
                                    'fecha_desde',
                                    $fechaDesde->format('Y-m-d')
                                ) }}">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_hasta">
                                Fecha hasta
                            </label>

                            <input
                                type="date"
                                name="fecha_hasta"
                                id="fecha_hasta"
                                class="form-control form-control-sm"
                                value="{{ request(
                                    'fecha_hasta',
                                    $fechaHasta->format('Y-m-d')
                                ) }}">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="sala_id">
                                Sala
                            </label>

                            <select
                                name="sala_id"
                                id="sala_id"
                                class="form-control form-control-sm">
                                <option value="">
                                    Todas las salas
                                </option>

                                @foreach ($salas as $sala)
                                <option
                                    value="{{ $sala->id }}"
                                    @selected(
                                    (string) request('sala_id')===(string) $sala->id
                                    )
                                    >
                                    {{ $sala->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="estado">
                                Estado
                            </label>

                            <select
                                name="estado"
                                id="estado"
                                class="form-control form-control-sm">
                                <option value="">
                                    Todos
                                </option>

                                <option
                                    value="PROGRAMADA"
                                    @selected(
                                    request('estado')==='PROGRAMADA'
                                    )>
                                    Programada
                                </option>

                                <option
                                    value="EN_PREPARACION"
                                    @selected(
                                    request('estado')==='EN_PREPARACION'
                                    )>
                                    En preparación
                                </option>

                                <option
                                    value="EN_CURSO"
                                    @selected(
                                    request('estado')==='EN_CURSO'
                                    )>
                                    En curso
                                </option>

                                <option
                                    value="LIMPIEZA"
                                    @selected(
                                    request('estado')==='LIMPIEZA'
                                    )>
                                    Limpieza
                                </option>

                                <option
                                    value="FINALIZADA"
                                    @selected(
                                    request('estado')==='FINALIZADA'
                                    )>
                                    Finalizada
                                </option>

                                <option
                                    value="CANCELADA"
                                    @selected(
                                    request('estado')==='CANCELADA'
                                    )>
                                    Cancelada
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="tipo_solicitud">
                                Tipo de solicitud
                            </label>

                            <select
                                name="tipo_solicitud"
                                id="tipo_solicitud"
                                class="form-control form-control-sm">
                                <option value="">
                                    Todas
                                </option>

                                <option
                                    value="ELECTIVA"
                                    @selected(
                                    request('tipo_solicitud')==='ELECTIVA'
                                    )>
                                    Electiva
                                </option>

                                <option
                                    value="EMERGENCIA"
                                    @selected(
                                    request('tipo_solicitud')==='EMERGENCIA'
                                    )>
                                    Emergencia
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label for="prioridad">
                                Prioridad
                            </label>

                            <select
                                name="prioridad"
                                id="prioridad"
                                class="form-control form-control-sm">
                                <option value="">
                                    Todas
                                </option>

                                <option
                                    value="BAJA"
                                    @selected(
                                    request('prioridad')==='BAJA'
                                    )>
                                    Baja
                                </option>

                                <option
                                    value="MEDIA"
                                    @selected(
                                    request('prioridad')==='MEDIA'
                                    )>
                                    Media
                                </option>

                                <option
                                    value="ALTA"
                                    @selected(
                                    request('prioridad')==='ALTA'
                                    )>
                                    Alta
                                </option>

                                <option
                                    value="EMERGENCIA"
                                    @selected(
                                    request('prioridad')==='EMERGENCIA'
                                    )>
                                    Emergencia
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="paciente">
                                Paciente, cuenta, HC o DNI
                            </label>

                            <input
                                type="text"
                                name="paciente"
                                id="paciente"
                                class="form-control form-control-sm"
                                value="{{ request('paciente') }}"
                                placeholder="Buscar paciente...">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="medico">
                                Médico o especialidad
                            </label>

                            <input
                                type="text"
                                name="medico"
                                id="medico"
                                class="form-control form-control-sm"
                                value="{{ request('medico') }}"
                                placeholder="DNI, nombre o especialidad...">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="procedimiento">
                                Procedimiento
                            </label>

                            <input
                                type="text"
                                name="procedimiento"
                                id="procedimiento"
                                class="form-control form-control-sm"
                                value="{{ request('procedimiento') }}"
                                placeholder="Código o procedimiento...">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="diagnostico">
                                Diagnóstico
                            </label>

                            <input
                                type="text"
                                name="diagnostico"
                                id="diagnostico"
                                class="form-control form-control-sm"
                                value="{{ request('diagnostico') }}"
                                placeholder="CIE-10 o diagnóstico...">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="servicio">
                                Servicio solicitante
                            </label>

                            <input
                                type="text"
                                name="servicio"
                                id="servicio"
                                class="form-control form-control-sm"
                                value="{{ request('servicio') }}"
                                placeholder="Servicio...">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="puntualidad">
                                Puntualidad
                            </label>

                            <select
                                name="puntualidad"
                                id="puntualidad"
                                class="form-control form-control-sm">
                                <option value="">
                                    Todas
                                </option>

                                <option
                                    value="PUNTUAL"
                                    @selected(
                                    request('puntualidad')==='PUNTUAL'
                                    )>
                                    Puntuales o anticipadas
                                </option>

                                <option
                                    value="RETRASADA"
                                    @selected(
                                    request('puntualidad')==='RETRASADA'
                                    )>
                                    Con retraso
                                </option>

                                <option
                                    value="SIN_INICIAR"
                                    @selected(
                                    request('puntualidad')==='SIN_INICIAR'
                                    )>
                                    Sin iniciar
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="d-block">
                                Acciones
                            </label>

                            <button
                                type="submit"
                                class="btn btn-hbt btn-sm">
                                <i class="fas fa-search mr-1"></i>
                                Buscar
                            </button>

                            <a
                                href="{{ route(
                                    'quirofano.reportes.index'
                                ) }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eraser mr-1"></i>
                                Limpiar
                            </a>

                            <<a
                                href="{{ route(
        'quirofano.reportes.excel',
        request()->query()
    ) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel mr-1"></i>
                                Excel
                                </a>

                                <a
                                    href="{{ route(
        'quirofano.reportes.pdf',
        request()->query()
    ) }}"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    PDF
                                </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- INDICADORES                                                   --}}
    {{-- ============================================================= --}}

    <div class="row report-indicators">
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="indicator-card indicator-blue">
                <div class="indicator-icon">
                    <i class="fas fa-procedures"></i>
                </div>

                <div>
                    <div class="indicator-value">
                        {{ $totalOperaciones }}
                    </div>

                    <div class="indicator-label">
                        Operaciones
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="indicator-card indicator-cyan">
                <div class="indicator-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div>
                    <div class="indicator-value">
                        {{ $programadas }}
                    </div>

                    <div class="indicator-label">
                        Programadas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="indicator-card indicator-green">
                <div class="indicator-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div>
                    <div class="indicator-value">
                        {{ $finalizadas }}
                    </div>

                    <div class="indicator-label">
                        Finalizadas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="indicator-card indicator-red">
                <div class="indicator-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>

                <div>
                    <div class="indicator-value">
                        {{ $emergencias }}
                    </div>

                    <div class="indicator-label">
                        Emergencias
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="indicator-card indicator-yellow">
                <div class="indicator-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <div class="indicator-value">
                        {{ $porcentajePuntualidad }}%
                    </div>

                    <div class="indicator-label">
                        Puntualidad
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row report-indicators">
        <div class="col-lg-3 col-md-6">
            <div class="metric-box">
                <span class="metric-title">
                    Operaciones en curso
                </span>

                <strong class="metric-value text-danger">
                    {{ $enCurso }}
                </strong>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-box">
                <span class="metric-title">
                    Retraso promedio
                </span>

                <strong class="metric-value text-warning">
                    {{ $formatearMinutos($promedioRetraso) }}
                </strong>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-box">
                <span class="metric-title">
                    Duración real promedio
                </span>

                <strong class="metric-value text-primary">
                    {{ $formatearMinutos($promedioDuracionReal) }}
                </strong>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-box">
                <span class="metric-title">
                    Limpieza promedio
                </span>

                <strong class="metric-value text-info">
                    {{ $formatearMinutos($promedioLimpieza) }}
                </strong>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- TABLA DE OPERACIONES                                          --}}
    {{-- ============================================================= --}}

    <div class="card card-outline card-hbt">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list-alt mr-1"></i>
                Detalle de operaciones
            </h3>

            <div class="card-tools">
                <span class="badge badge-hbt">
                    {{ $operaciones->total() }} registros
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive report-table-container">
                <table class="table table-sm table-hover report-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha / Sala</th>
                            <th>Paciente</th>
                            <th>Procedimiento / Médico</th>
                            <th>Solicitud</th>
                            <th>Programación</th>
                            <th>Preparación</th>
                            <th>Inicio real</th>
                            <th>Fin real</th>
                            <th>Limpieza</th>
                            <th>Tiempos</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($operaciones as $programacion)
                        @php
                        $solicitud = $programacion->solicitud;
                        $sala = $programacion->sala;

                        $inicioProgramado =
                        $programacion->fecha_hora_inicio
                        ? \Carbon\Carbon::parse(
                        $programacion->fecha_hora_inicio
                        )
                        : null;

                        $duracionEstimada = $solicitud
                        ? (int) $solicitud
                        ->duracion_estimada_minutos
                        : 0;

                        $finProgramado =
                        $programacion->fecha_hora_fin
                        ? \Carbon\Carbon::parse(
                        $programacion->fecha_hora_fin
                        )
                        : (
                        $inicioProgramado &&
                        $duracionEstimada > 0
                        ? $inicioProgramado
                        ->copy()
                        ->addMinutes(
                        $duracionEstimada
                        )
                        : null
                        );

                        $inicioReal =
                        $programacion->fecha_inicio_real
                        ? \Carbon\Carbon::parse(
                        $programacion->fecha_inicio_real
                        )
                        : null;

                        $finReal =
                        $programacion->fecha_fin_real
                        ? \Carbon\Carbon::parse(
                        $programacion->fecha_fin_real
                        )
                        : null;

                        $finLimpieza =
                        $programacion->fecha_fin_limpieza
                        ? \Carbon\Carbon::parse(
                        $programacion->fecha_fin_limpieza
                        )
                        : null;

                        $inicioPreparacion =
                        $programacion
                        ->fecha_inicio_preparacion
                        ? \Carbon\Carbon::parse(
                        $programacion
                        ->fecha_inicio_preparacion
                        )
                        : null;

                        $retraso = null;

                        if ($inicioProgramado && $inicioReal) {
                        $retraso = $inicioProgramado
                        ->diffInMinutes(
                        $inicioReal,
                        false
                        );
                        }

                        $duracionReal = null;

                        if ($inicioReal && $finReal) {
                        $duracionReal = $inicioReal
                        ->diffInMinutes($finReal);
                        }

                        $tiempoLimpieza = null;

                        if ($finReal && $finLimpieza) {
                        $tiempoLimpieza = $finReal
                        ->diffInMinutes($finLimpieza);
                        }

                        $ocupacionTotal = null;

                        if ($inicioPreparacion && $finLimpieza) {
                        $ocupacionTotal = $inicioPreparacion
                        ->diffInMinutes($finLimpieza);
                        }

                        $numeroFila =
                        $operaciones->firstItem() +
                        $loop->index;
                        @endphp

                        <tr>
                            <td class="text-center">
                                {{ $numeroFila }}
                            </td>

                            <td class="report-nowrap">
                                <strong class="text-hbt">
                                    {{ $inicioProgramado
                                            ? $inicioProgramado
                                                ->format('d/m/Y')
                                            : '-' }}
                                </strong>

                                <small class="d-block text-muted">
                                    <i class="fas fa-door-open mr-1"></i>
                                    {{ $sala->nombre ?? '-' }}
                                </small>

                                <small class="d-block text-muted">
                                    {{ $sala->tipo ?? '-' }}
                                </small>
                            </td>

                            <td class="report-patient">
                                <strong>
                                    {{ $solicitud->paciente ?? '-' }}
                                </strong>

                                <small class="d-block text-muted">
                                    Cuenta:
                                    {{ $solicitud->numero_cuenta ?? '-' }}
                                </small>

                                <small class="d-block text-muted">
                                    HC:
                                    {{ $solicitud->historia_clinica ?? '-' }}
                                </small>

                                <small class="d-block text-muted">
                                    {{ $solicitud->servicio_solicitante
                                            ?? '-' }}
                                </small>
                            </td>

                            <td class="report-description">
                                <strong class="text-hbt">
                                    {{ $solicitud->procedimiento_codigo
                                            ?? '-' }}
                                </strong>

                                <span>
                                    {{ $solicitud->procedimiento_nombre
                                            ?? '-' }}
                                </span>

                                <small class="d-block mt-1 text-muted">
                                    <i class="fas fa-user-md mr-1"></i>
                                    {{ $solicitud->medico_nombre ?? '-' }}
                                </small>

                                <small class="d-block text-muted">
                                    {{ $solicitud->medico_especialidad
                                            ?? '-' }}
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <strong>
                                    {{ $formatearFechaHora(
                                            $solicitud->fecha_envio ?? null
                                        ) }}
                                </strong>

                                <small class="d-block text-muted">
                                    Enviada
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <strong>
                                    {{ $formatearHora(
                                            $inicioProgramado
                                        ) }}
                                    –
                                    {{ $formatearHora(
                                            $finProgramado
                                        ) }}
                                </strong>

                                <small class="d-block text-muted">
                                    {{ $duracionEstimada > 0
                                            ? $duracionEstimada . ' min'
                                            : '-' }}
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <strong>
                                    {{ $formatearHora(
                                            $inicioPreparacion
                                        ) }}
                                </strong>

                                <small class="d-block text-muted">
                                    Inicio preparación
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <strong class="text-danger">
                                    {{ $formatearHora($inicioReal) }}
                                </strong>

                                @if ($retraso !== null)
                                @if ($retraso > 0)
                                <small class="d-block text-danger">
                                    +{{ $retraso }} min
                                </small>
                                @elseif ($retraso < 0)
                                    <small class="d-block text-success">
                                    {{ abs($retraso) }} min antes
                                    </small>
                                    @else
                                    <small class="d-block text-success">
                                        Puntual
                                    </small>
                                    @endif
                                    @endif
                            </td>

                            <td class="report-nowrap">
                                <strong>
                                    {{ $formatearHora($finReal) }}
                                </strong>

                                <small class="d-block text-muted">
                                    Duración:
                                    {{ $formatearMinutos(
                                            $duracionReal
                                        ) }}
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <strong class="text-info">
                                    {{ $formatearHora($finLimpieza) }}
                                </strong>

                                <small class="d-block text-muted">
                                    {{ $formatearMinutos(
                                            $tiempoLimpieza
                                        ) }}
                                </small>
                            </td>

                            <td class="report-nowrap">
                                <small class="d-block">
                                    <strong>Estimada:</strong>
                                    {{ $formatearMinutos(
                                            $duracionEstimada
                                        ) }}
                                </small>

                                <small class="d-block">
                                    <strong>Real:</strong>
                                    {{ $formatearMinutos(
                                            $duracionReal
                                        ) }}
                                </small>

                                <small class="d-block">
                                    <strong>Ocupación:</strong>
                                    {{ $formatearMinutos(
                                            $ocupacionTotal
                                        ) }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span
                                    class="badge {{
                                            $estadoClase(
                                                $programacion->estado
                                            )
                                        }}">
                                    {{ $estadoTexto(
                                            $programacion->estado
                                        ) }}
                                </span>

                                @if (
                                ($solicitud->tipo_solicitud ?? null)
                                === 'EMERGENCIA'
                                )
                                <span class="badge badge-danger mt-1">
                                    EMERGENCIA
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td
                                colspan="12"
                                class="text-center py-5 text-muted">
                                <i
                                    class="fas fa-search fa-2x mb-2"></i>

                                <div>
                                    No se encontraron operaciones con
                                    los filtros seleccionados.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($operaciones->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left text-muted small pt-2">
                Mostrando
                {{ $operaciones->firstItem() }}
                a
                {{ $operaciones->lastItem() }}
                de
                {{ $operaciones->total() }}
                registros
            </div>

            <div class="float-right">
                {{ $operaciones->links(
                        'pagination::bootstrap-4'
                    ) }}
            </div>
        </div>
        @endif
    </div>
    @stop

    @section('css')
    <link
        rel="stylesheet"
        href="{{ asset('css/hbt.css') }}">

    <style>
        :root {
            --hbt-blue-dark: #073b61;
            --hbt-blue: #075a94;
            --hbt-blue-light: #159bd3;
            --hbt-yellow: #f4c430;
            --hbt-background: #eef4f8;
            --hbt-border: #d4e1e9;
        }

        .text-hbt {
            color: var(--hbt-blue) !important;
        }

        .card-hbt {
            border-top-color: var(--hbt-blue) !important;
        }

        .card-hbt>.card-header {
            background: linear-gradient(90deg,
                    #ffffff,
                    #eef7fc);
            color: var(--hbt-blue-dark);
        }

        .btn-hbt {
            border-color: var(--hbt-blue);
            background: var(--hbt-blue);
            color: #ffffff;
        }

        .btn-hbt:hover,
        .btn-hbt:focus {
            border-color: var(--hbt-blue-dark);
            background: var(--hbt-blue-dark);
            color: #ffffff;
        }

        .badge-hbt {
            background: var(--hbt-blue);
            color: #ffffff;
        }

        .report-indicators {
            margin-right: -5px;
            margin-left: -5px;
        }

        .report-indicators>div {
            padding-right: 5px;
            padding-left: 5px;
        }

        .indicator-card {
            display: flex;
            align-items: center;
            min-height: 72px;
            margin-bottom: 10px;
            padding: 10px 12px;
            overflow: hidden;
            border-radius: 7px;
            background: #ffffff;
            box-shadow: 0 2px 7px rgba(0, 47, 76, .12);
            color: #ffffff;
        }

        .indicator-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            margin-right: 11px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .18);
            font-size: 19px;
        }

        .indicator-value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }

        .indicator-label {
            margin-top: 5px;
            font-size: 11px;
            font-weight: 600;
            opacity: .95;
            text-transform: uppercase;
        }

        .indicator-blue {
            background: linear-gradient(135deg,
                    #075a94,
                    #0879bc);
        }

        .indicator-cyan {
            background: linear-gradient(135deg,
                    #118daa,
                    #1cacc1);
        }

        .indicator-green {
            background: linear-gradient(135deg,
                    #188b57,
                    #20ac6b);
        }

        .indicator-red {
            background: linear-gradient(135deg,
                    #c93648,
                    #e84b5d);
        }

        .indicator-yellow {
            background: linear-gradient(135deg,
                    #cf9512,
                    #ecb832);
        }

        .metric-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 48px;
            margin-bottom: 10px;
            padding: 9px 12px;
            border-left: 4px solid var(--hbt-blue-light);
            border-radius: 5px;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 47, 76, .09);
        }

        .metric-title {
            color: #60798a;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .metric-value {
            padding-left: 8px;
            font-size: 15px;
            white-space: nowrap;
        }

        .report-table-container {
            max-height: 570px;
        }

        .report-table {
            min-width: 1750px;
            font-size: 11px;
        }

        .report-table thead th {
            position: sticky;
            z-index: 2;
            top: 0;
            padding: 8px 7px;
            border-bottom: 2px solid var(--hbt-blue);
            background: var(--hbt-blue-dark);
            color: #ffffff;
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
            white-space: nowrap;
        }

        .report-table tbody td {
            padding: 7px;
            border-color: #e0e9ef;
            color: #17364b;
            vertical-align: middle;
        }

        .report-table tbody tr:nth-child(even) {
            background: #f5f9fc;
        }

        .report-table tbody tr:hover {
            background: #e8f4fb;
        }

        .report-nowrap {
            white-space: nowrap;
        }

        .report-patient {
            min-width: 200px;
        }

        .report-description {
            min-width: 260px;
            max-width: 320px;
        }

        .report-description span {
            display: block;
            line-height: 1.2;
        }

        .badge {
            font-size: 9px;
            white-space: nowrap;
        }

        .pagination {
            margin-bottom: 0;
        }

        @media (max-width: 767px) {
            .indicator-card {
                min-height: 67px;
            }

            .indicator-value {
                font-size: 19px;
            }

            .indicator-label {
                font-size: 9px;
            }
        }
    </style>
    @stop