@extends('adminlte::page')

@section('title', 'Dashboard Emergencia')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .chart-box {
        position: relative;
        height: 260px;
    }

    .chart-box-large {
        position: relative;
        height: 310px;
    }

    .hbt-header-date {
        background: linear-gradient(135deg, #0f8b6f, #16a085);
        color: #fff;
        padding: 9px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 8px 18px rgba(15, 139, 111, .18);
    }

    .small-box {
        min-height: 118px;
        border-radius: 16px;
        overflow: hidden;
    }

    .small-box .inner h3 {
        font-size: 34px;
        font-weight: 800;
    }

    .small-box .inner p {
        font-weight: 600;
        font-size: 14px;
    }

    .card {
        border-radius: 16px;
        overflow: hidden;
    }

    .card-header {
        padding: 11px 16px;
    }

    .card-body {
        padding: 18px;
    }

    .content-header {
        padding-bottom: 8px;
    }

    .content {
        padding-top: 5px;
    }
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="font-weight-bold text-success mb-1">
            Dashboard Emergencia
        </h1>
        <small class="text-muted">
            Hospital Belén de Trujillo · Emergencia
        </small>
    </div>

    <div class="hbt-header-date">
        Rango: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        · Hora: {{ now()->format('H:i') }}
    </div>
</div>

<form method="GET" action="{{ route('emergencia.dashboard') }}" class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Fecha inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Fecha fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('emergencia.dashboard') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-sync-alt mr-1"></i> Limpiar
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
                <h3>{{ $totalAtenciones }}</h3>
                <p>Total Atenciones</p>
            </div>
            <div class="icon">
                <i class="fas fa-ambulance"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $pacientesActivos }}</h3>
                <p>Pacientes Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-procedures"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pacientesEgresados }}</h3>
                <p>Egresados</p>
            </div>
            <div class="icon">
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $sinCierreAdministrativo }}</h3>
                <p>Sin Cierre Administrativo</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat mr-1"></i>
                    Distribución por Gravedad
                </h3>
            </div>
            <div class="card-body">
                <div class="chart-box">
                    <canvas id="chartGravedad"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-wallet mr-1"></i>
                    Fuente de Financiamiento
                </h3>
            </div>
            <div class="card-body">
                <div class="chart-box">
                    <canvas id="chartFinanciamiento"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $serviciosActivos }}</h3>
                <p>Servicios con Pacientes Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-hospital"></i>
            </div>
        </div>

        <a href="{{ route('emergencia.pacientes-activos') }}" class="btn btn-success btn-block">
            <i class="fas fa-list mr-1"></i>
            Ver Pacientes Activos por Servicio
        </a>
    </div>

</div>

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Pacientes Activos por Servicio Actual
                </h3>
            </div>
            <div class="card-body">
                <div class="chart-box-large">
                    <canvas id="chartServicios"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Flujo Horario de Ingresos
                </h3>
            </div>
            <div class="card-body">
                <div class="chart-box-large">
                    <canvas id="chartFlujo"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const hbtColors = [
        '#0f8b6f',
        '#16a085',
        '#1abc9c',
        '#27ae60',
        '#2ecc71',
        '#f59e0b',
        '#ef4444',
        '#3b82f6',
        '#6366f1',
        '#64748b'
    ];

    new Chart(document.getElementById('chartGravedad'), {
        type: 'pie',
        data: {
            labels: @json($gravedad->pluck('TIPOGRAVEDAD')),
            datasets: [{
                data: @json($gravedad->pluck('total')),
                backgroundColor: hbtColors,
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, font: { size: 10 } }
                }
            }
        }
    });

    new Chart(document.getElementById('chartFinanciamiento'), {
        type: 'doughnut',
        data: {
            labels: @json($financiamiento->pluck('FUENTEFINANCIAMIENTO')),
            datasets: [{
                data: @json($financiamiento->pluck('total')),
                backgroundColor: hbtColors,
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, font: { size: 10 } }
                }
            }
        }
    });

    new Chart(document.getElementById('chartServicios'), {
        type: 'bar',
        data: {
            labels: @json($servicios->pluck('SERVICIOACTUAL')),
            datasets: [{
                label: 'Pacientes activos',
                data: @json($servicios->pluck('total')),
                backgroundColor: hbtColors,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { usePointStyle: true } }
            },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('chartFlujo'), {
        type: 'line',
        data: {
            labels: @json($flujoHorario->pluck('hora')->map(fn($h) => $h . ':00')),
            datasets: [{
                label: 'Ingresos',
                data: @json($flujoHorario->pluck('total')),
                borderColor: '#0f8b6f',
                backgroundColor: 'rgba(15,139,111,0.15)',
                pointBackgroundColor: '#0f8b6f',
                pointBorderColor: '#ffffff',
                pointRadius: 5,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { usePointStyle: true } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    setTimeout(() => {
        location.reload();
    }, 60000);
</script>
@stop