@extends('adminlte::page')

@section('title', 'Dashboard Radiología')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>
    <i class="fas fa-x-ray"></i>
    Dashboard Radiología
</h1>
@stop

@section('content')

<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            Filtros de Consulta
        </h3>
    </div>

    <div class="card-body">
        <form method="GET"
            action="{{ route('radiologia.dashboard') }}">

            <div class="row">

                <div class="col-md-3">
                    <label>Fecha Inicio</label>
                    <input type="date"
                        name="fecha_inicio"
                        value="{{ $fechaInicio }}"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Fecha Fin</label>
                    <input type="date"
                        name="fecha_fin"
                        value="{{ $fechaFin }}"
                        class="form-control">
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-success btn-block">
                        <i class="fas fa-search"></i>
                        Consultar
                    </button>
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a href="{{ route('radiologia.dashboard') }}"
                        class="btn btn-outline-success btn-block">
                        Hoy
                    </a>
                </div>

            </div>

        </form>
    </div>
</div>


<div class="row">

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalCitas }}</h3>
                <p>Total Citas</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
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

    <div class="col-lg-3 col-6">
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

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>
                    {{ $totalCitas > 0 ? round(($atendidos / $totalCitas) * 100, 1) : 0 }}%
                </h3>
                <p>% Atención</p>
            </div>
            <div class="icon">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Rayos X</h3>
            </div>
            <div class="card-body text-center">
                <h2>{{ $rayosx }}</h2>
                <span class="badge badge-servicio">Total registrados</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Tomografías</h3>
            </div>
            <div class="card-body text-center">
                <h2>{{ $tomografias }}</h2>
                <span class="badge badge-servicio">Total registrados</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Ecografías</h3>
            </div>
            <div class="card-body text-center">
                <h2>{{ $ecografias }}</h2>
                <span class="badge badge-servicio">Total registrados</span>
            </div>
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Citas por Modalidad
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartModalidad" height="130"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Estado de Citas
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartEstado" height="130"></canvas>
            </div>
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Producción por Día
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartPorDia" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Atendidos por Modalidad
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartAtendidosModalidad" height="220"></canvas>
            </div>
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Top 10 Estudios Solicitados
                </h3>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudio</th>
                            <th width="80">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topEstudios as $item)
                        <tr>
                            <td>{{ $item->estudio }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->total }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">
                                Sin registros
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    Próximas Citas de Hoy
                </h3>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citasHoy as $item)
                        <tr>
                            <td>{{ substr($item->hora_cita, 0, 5) }}</td>
                            <td>{{ $item->paciente }}</td>
                            <td>{{ $item->modalidad }}</td>
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
                            <td colspan="4" class="text-center">
                                No hay citas para hoy
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@stop


@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modalidadLabels = @json($porModalidad->pluck('modalidad'));
        const modalidadData = @json($porModalidad->pluck('total'));

        const estadoLabels = @json($porEstado->pluck('estado'));
        const estadoData = @json($porEstado->pluck('total'));

        const diaLabels = @json($porDia->pluck('fecha_cita') -> map(function($f) {
            return date('d-m-Y', strtotime($f));
        }));

        const diaData = @json($porDia->pluck('total'));

        const atendidosModalidadLabels = @json($atendidosPorModalidad -> pluck('modalidad'));
        const atendidosModalidadData = @json($atendidosPorModalidad -> pluck('total'));

        new Chart(document.getElementById('chartModalidad'), {
            type: 'bar',
            data: {
                labels: modalidadLabels,
                datasets: [{
                    label: 'Citas',
                    data: modalidadData,
                    backgroundColor: '#0078D4'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        new Chart(document.getElementById('chartEstado'), {
            type: 'doughnut',
            data: {
                labels: estadoLabels,
                datasets: [{
                    data: estadoData,
                    backgroundColor: [
                        '#F2C811',
                        '#0078D4',
                        '#D83B01'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('chartPorDia'), {
            type: 'line',
            data: {
                labels: diaLabels,
                datasets: [{
                    label: 'Citas por día',
                    data: diaData,
                    borderColor: '#0078D4',
                    backgroundColor: 'rgba(0,120,212,.12)',
                    tension: .3,
                    fill: true
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('chartAtendidosModalidad'), {
            type: 'bar',
            data: {
                labels: atendidosModalidadLabels,
                datasets: [{
                    label: 'Atendidos',
                    data: atendidosModalidadData,
                    backgroundColor: '#106EBE'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    });
</script>

@stop