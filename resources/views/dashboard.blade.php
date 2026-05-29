@extends('adminlte::page')

@section('title', 'Resumen Operativo')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .chart-box {
        position: relative;
        height: 260px;
    }

    .chart-box-large {
        position: relative;
        height: 300px;
    }

    .chart-box-pie {
        position: relative;
        height: 260px;
    }

    .avance-box {
        height: 260px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 20px 28px;
    }

    .avance-box h1 {
        font-size: 58px;
        line-height: 1;
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

    .progress {
        height: 14px !important;
        border-radius: 20px;
    }

    .content-header {
        padding-bottom: 8px;
    }

    .content {
        padding-top: 5px;
    }

    select[multiple] {
        min-height: 92px !important;
        border-radius: 10px !important;
        font-size: 13px;
    }

    select[multiple] option {
        padding: 4px;
    }

    .dropdown-menu label {
        font-size: 13px;
        cursor: pointer;
    }

    .dropdown-menu label:hover {
        background: rgba(15, 139, 111, .08);
    }

    .dropdown-toggle {
        height: 38px;
        border-radius: 8px;
    }

    .filtro-card {
        position: relative;
        z-index: 9999;
        overflow: visible !important;
    }

    .filtro-card .card-body,
    .filtro-card .row,
    .filtro-card .dropdown {
        overflow: visible !important;
    }

    .filtro-card .dropdown-menu {
        z-index: 99999 !important;
        max-height: 280px;
        overflow-y: auto;
        width: 100%;
        font-size: 13px;
    }

    .filtro-card .dropdown-item {
        white-space: normal;
        cursor: pointer;
    }

    .content-wrapper,
    .content,
    .container-fluid {
        overflow: visible !important;
    }
</style>

@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="font-weight-bold text-success mb-1">
            Resumen Operativo
        </h1>
        <small class="text-muted">
            Hospital Belén de Trujillo · Consulta Externa
        </small>
    </div>

    <div class="hbt-header-date">
        Rango: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        · Hora: {{ now()->format('H:i') }}
    </div>
</div>

<form method="GET" action="{{ route('dashboard') }}" class="card mb-3 filtro-card">
    <div class="card-body py-3">

        <div class="row align-items-end">

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">
                    Fecha inicio
                </label>

                <input
                    type="date"
                    name="fecha_inicio"
                    value="{{ $fechaInicio }}"
                    class="form-control">
            </div>

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">
                    Fecha fin
                </label>

                <input
                    type="date"
                    name="fecha_fin"
                    value="{{ $fechaFin }}"
                    class="form-control">
            </div>

            <div class="col-md-4">
                <label class="font-weight-bold text-muted">Especialidades</label>

                <div class="dropdown w-100">
                    <button class="btn btn-light border w-100 text-left dropdown-toggle"
                        type="button"
                        data-toggle="dropdown"
                        data-boundary="viewport"
                        aria-expanded="false">
                        Seleccionar especialidades
                    </button>

                    <div class="dropdown-menu w-100 p-2"
                        style="max-height: 260px; overflow-y: auto;">

                        @foreach($especialidadesFiltro as $esp)
                        <label class="dropdown-item mb-1">
                            <input type="checkbox"
                                name="especialidades[]"
                                value="{{ $esp }}"
                                class="mr-2"
                                {{ in_array($esp, $especialidadesFiltroSeleccionadas) ? 'checked' : '' }}>
                            {{ $esp }}
                        </label>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <button
                    type="submit"
                    class="btn btn-success btn-block">

                    <i class="fas fa-filter mr-1"></i>
                    Filtrar

                </button>
            </div>

            <div class="col-md-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="btn btn-outline-secondary btn-block">

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
                <h3>{{ $totalCitas }}</h3>
                <p>Citas Programadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalAtendidas }}</h3>
                <p>Atenciones Realizadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-md"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalPendientes }}</h3>
                <p>Pendientes por Atender</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalConsultoriosActivos }}</h3>
                <p>Consultorios Activos</p>
            </div>

            <div class="icon">
                <i class="fas fa-clinic-medical"></i>
            </div>
        </div>
    </div>



</div>

<div class="row">

    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-percentage mr-1"></i>
                    Avance Diario
                </h3>
            </div>

            <div class="card-body text-center avance-box">
                <h1 class="font-weight-bold text-success">
                    {{ $avance }}%
                </h1>

                <p class="text-muted mb-3">
                    Citas atendidas respecto al total programado.
                </p>

                <div class="progress">
                    <div class="progress-bar bg-success"
                        role="progressbar"
                        style="width: {{ $avance }}%;"
                        aria-valuenow="{{ $avance }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>

                <small class="text-muted mt-2">
                    {{ $totalAtendidas }} atendidas de {{ $totalCitas }} programadas
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-wallet mr-1"></i>
                    Fuente Financiamiento - Atendidos
                </h3>
            </div>

            <div class="card-body">
                <div class="chart-box-pie">
                    <canvas id="chartFinanciamiento"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Flujo Horario de Citas
                </h3>
            </div>

            <div class="card-body">
                <div class="chart-box">
                    <canvas id="chartFlujoHorario"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-stethoscope mr-1"></i>
                    Top 10 Servicios por Demanda
                </h3>
            </div>

            <div class="card-body">
                <div class="chart-box-large">
                    <canvas id="chartEspecialidades"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-user-check mr-1"></i>
                    Top 10 Servicios Atendidas
                </h3>
            </div>

            <div class="card-body">
                <div class="chart-box-large">
                    <canvas id="chartEspecialidadesAtendidas"></canvas>
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

    new Chart(document.getElementById('chartFlujoHorario'), {
        type: 'line',
        data: {
            labels: @json($horas),
            datasets: [{
                label: 'Citas',
                data: @json($cantidades),
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
                legend: {
                    labels: {
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('chartFinanciamiento'), {
        type: 'pie',
        data: {
            labels: @json($financiamientoLabels),
            datasets: [{
                data: @json($financiamientoData),
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
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('chartEspecialidades'), {
        type: 'bar',
        data: {
            labels: @json($servicioLabels),
            datasets: [{
                label: 'Total de citas',
                data: @json($servicioData),
                backgroundColor: hbtColors,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    new Chart(document.getElementById('chartEspecialidadesAtendidas'), {
        type: 'bar',
        data: {
            labels: @json($servicioAtendidoLabels),
            datasets: [{
                label: 'Atenciones realizadas',
                data: @json($servicioAtendidoData),
                backgroundColor: hbtColors,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    setTimeout(() => {
        location.reload();
    }, 60000);
</script>
@stop