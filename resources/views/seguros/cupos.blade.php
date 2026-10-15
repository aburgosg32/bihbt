@extends('adminlte::page')

@section('title', 'Seguros | Gestión de cupos')

@section('content_header')
<h1 class="text-primary">
    <i class="fas fa-calendar-alt mr-2"></i>
    Gestión de cupos
</h1>
@stop

@section('content')
<div class="seguros-cupos">

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Buscar programación</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('seguros.cupos.index') }}">
                        <label for="cuenta">Cuenta del paciente</label>

                        <input
                            type="number"
                            name="cuenta"
                            id="cuenta"
                            class="form-control"
                            value="{{ $cuenta }}"
                            min="1"
                            required>

                        <button class="btn btn-primary btn-block mt-3">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>
                    </form>

                    @if ($programacion)
                    <hr>

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <small>Totales</small>
                            <div class="cupos-numero">{{ $resumen['total'] }}</div>
                        </div>
                        <div class="col-4">
                            <small>Ocupados</small>
                            <div class="cupos-numero">{{ $resumen['ocupados'] }}</div>
                        </div>
                        <div class="col-4">
                            <small>Libres</small>
                            <div class="cupos-numero">{{ $resumen['libres'] }}</div>
                        </div>
                    </div>

                    <p class="text-muted">
                        Un cupo:
                        <strong>{{ $programacion->TiempoPromedioAtencion }} minutos</strong>.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('seguros.cupos.actualizar') }}"
                        id="formAjustarCupos">
                        @csrf

                        <input type="hidden" name="cuenta" value="{{ $cuenta }}">
                        <input
                            type="hidden"
                            name="programacion"
                            value="{{ $programacion->IdProgramacion }}">

                        <button
                            type="submit"
                            name="accion"
                            value="sumar"
                            class="btn btn-info btn-block">
                            <i class="fas fa-plus mr-1"></i>
                            Sumar +1 cupo
                        </button>

                        <button
                            type="submit"
                            name="accion"
                            value="restar"
                            class="btn btn-danger btn-block">
                            <i class="fas fa-minus mr-1"></i>
                            Restar −1 cupo
                        </button>

                        <hr>

                        <button
                            type="submit"
                            name="accion"
                            value="desmontar"
                            class="btn btn-warning btn-block">
                            Desmontar
                        </button>

                        <button
                            type="submit"
                            name="accion"
                            value="cerrar"
                            class="btn btn-secondary btn-block">
                            Cerrar cupos
                        </button>

                        <small class="text-muted d-block mt-2">
                            Cerrar y Desmontar ajustan la hora final
                            al término de la última cita registrada.
                        </small>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @if ($programacion)
            @if ($programaciones->count() > 1)
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('seguros.cupos.index') }}">
                        <input type="hidden" name="cuenta" value="{{ $cuenta }}">

                        <label for="programacion">
                            Programaciones vinculadas a esta cuenta
                        </label>

                        <div class="input-group">
                            <select
                                name="programacion"
                                id="programacion"
                                class="form-control">
                                @foreach ($programaciones as $opcion)
                                <option
                                    value="{{ $opcion->IdProgramacion }}"
                                    @selected(
                                    $opcion->IdProgramacion ==
                                    $programacion->IdProgramacion
                                    )
                                    >
                                    {{ $opcion->IdProgramacion }} —
                                    {{ \Carbon\Carbon::parse($opcion->Fecha)->format('d/m/Y') }} —
                                    {{ $opcion->Servicio }} —
                                    {{ $opcion->Medico }}
                                </option>
                                @endforeach
                            </select>

                            <div class="input-group-append">
                                <button class="btn btn-primary">Mostrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de la programación</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Especialidad:</strong>
                            {{ $programacion->Especialidad }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Servicio:</strong>
                            {{ $programacion->Servicio }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Médico:</strong>
                            {{ $programacion->Medico }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>ID programación:</strong>
                            {{ $programacion->IdProgramacion }}
                        </div>

                        <div class="col-md-6">
                            <strong>Fecha:</strong>
                            {{ \Carbon\Carbon::parse($programacion->Fecha)->format('d/m/Y') }}
                        </div>

                        <div class="col-md-6">
                            <strong>Horario:</strong>
                            {{ $programacion->HoraInicio }} —
                            {{ $programacion->HoraFin }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cuenta</th>
                                <th>Paciente</th>
                                <th>Financiamiento</th>
                                <th>Fecha</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado de cita (ID)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($citas as $cita)
                            <tr class="{{ $cita->IdCuentaAtencion == $cuenta ? 'table-info' : '' }}">
                                <td>{{ $cita->IdCuentaAtencion }}</td>
                                <td>{{ $cita->Paciente }}</td>
                                <td>{{ $cita->Financiamiento }}</td>
                                <td class="text-nowrap">
                                    {{ $cita->Fecha
                                                ? \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y')
                                                : '—' }}
                                </td>
                                <td>{{ $cita->HoraInicio }}</td>
                                <td>{{ $cita->HoraFin }}</td>
                                <td>{{ $cita->IdEstadoCita }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay citas registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif ($cuenta)
            <div class="alert alert-info">
                No se encontraron programaciones vinculadas a esta cuenta.
            </div>
            @else
            <div class="alert alert-light border">
                Ingrese una cuenta de paciente para consultar y ajustar su programación.
            </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
<style>
    .seguros-cupos .cupos-numero {
        padding: 8px;
        margin-top: 5px;
        background: #eef4fa;
        border: 1px solid #d5e2ef;
        border-radius: 5px;
        color: #174a78;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .seguros-cupos .table thead th {
        background: #eaf2fb;
        color: #174a78;
        vertical-align: middle;
    }

    .seguros-cupos .table td {
        vertical-align: middle;
    }
</style>
@stop

@section('js')
<script>
    document.getElementById('formAjustarCupos')?.addEventListener('submit', function(evento) {
        if (this.dataset.enviando === '1') {
            evento.preventDefault();
            return;
        }

        this.dataset.enviando = '1';

        // Conservar el valor del botón enviado y evitar clics repetidos.
        this.querySelectorAll('button').forEach(boton => {
            boton.style.pointerEvents = 'none';
            boton.setAttribute('aria-disabled', 'true');
        });
    });
</script>
@stop