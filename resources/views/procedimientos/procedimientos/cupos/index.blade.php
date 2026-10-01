@extends('adminlte::page')

@section('title', 'Cupos de procedimientos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">

    <div>
        <h1 class="m-0">
            <i class="fas fa-clock text-primary mr-2"></i>
            Cupos programados
        </h1>

        <small class="text-muted">
            Consulta, bloqueo y habilitación de horarios
        </small>
    </div>

    <a
        href="{{ route('procedimientos.programaciones.index') }}"
        class="btn btn-secondary mt-2 mt-md-0">
        <i class="fas fa-arrow-left mr-1"></i>
        Volver a programaciones
    </a>

</div>
@stop

@section('content')
<div class="container-fluid">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="close"
            data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-2"></i>

        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach

        <button
            type="button"
            class="close"
            data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    {{-- Información de la programación --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-check mr-1"></i>
                Información de la programación
            </h3>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">
                        Procedimiento
                    </small>

                    <strong>
                        {{ $programacion->procedimiento->nombre }}
                    </strong>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">
                        Servicio
                    </small>

                    <strong>
                        {{ $programacion->procedimiento
                            ->servicio
                            ->nombre ?? 'SIN SERVICIO' }}
                    </strong>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">
                        Médico
                    </small>

                    <strong>
                        {{ $programacion->medico->nombre_completo }}
                    </strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">
                        Fecha
                    </small>

                    <strong>
                        {{ $programacion
                            ->fecha_programacion
                            ->format('d/m/Y') }}
                    </strong>
                </div>

            </div>

            <hr>

            <div class="row">

                <div class="col-md-3">
                    <small class="text-muted d-block">
                        Horario
                    </small>

                    <strong>
                        {{ substr($programacion->hora_inicio, 0, 5) }}
                        -
                        {{ substr($programacion->hora_fin, 0, 5) }}
                    </strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">
                        Intervalo
                    </small>

                    <strong>
                        {{ $programacion->intervalo_minutos }}
                        minutos
                    </strong>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block">
                        Resumen
                    </small>

                    <span class="badge badge-success mr-1">
                        Disponibles:
                        {{ $programacion->cupos
                            ->where('estado', 'DISPONIBLE')
                            ->count() }}
                    </span>

                    <span class="badge badge-danger mr-1">
                        Bloqueados:
                        {{ $programacion->cupos
                            ->where('estado', 'BLOQUEADO')
                            ->count() }}
                    </span>

                    <span class="badge badge-primary">
                        Reservados:
                        {{ $programacion->cupos
                            ->where('estado', 'RESERVADO')
                            ->count() }}
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- Cuadrícula de cupos --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list-alt mr-1"></i>
                Horarios disponibles
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    Total: {{ $programacion->cupos->count() }}
                </span>
            </div>
        </div>

        <div class="card-body">

            <div class="row">

                @forelse ($programacion->cupos as $cupo)

                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div
                        class="card h-100
                                @if ($cupo->estado === 'DISPONIBLE')
                                    border-success
                                @elseif ($cupo->estado === 'BLOQUEADO')
                                    border-danger
                                @else
                                    border-primary
                                @endif
                            ">
                        <div class="card-body text-center">

                            <div class="h4 font-weight-bold mb-2">
                                <i class="far fa-clock mr-1"></i>
                                {{ substr($cupo->hora, 0, 5) }}
                            </div>

                            @if ($cupo->estado === 'DISPONIBLE')

                            <span class="badge badge-success px-3 py-2">
                                DISPONIBLE
                            </span>

                            @elseif ($cupo->estado === 'BLOQUEADO')

                            <span class="badge badge-danger px-3 py-2">
                                BLOQUEADO
                            </span>

                            @else

                            <span class="badge badge-primary px-3 py-2">
                                RESERVADO
                            </span>

                            @endif

                            @if ($cupo->estado === 'BLOQUEADO')
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ $cupo->motivo_bloqueo }}
                                </small>
                            </div>
                            @endif

                        </div>

                        <div class="card-footer text-center">

                            @if ($cupo->estado === 'DISPONIBLE')

                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-block"
                                data-toggle="modal"
                                data-target="#modalBloquear{{ $cupo->id }}">
                                <i class="fas fa-ban mr-1"></i>
                                Bloquear
                            </button>

                            @elseif ($cupo->estado === 'BLOQUEADO')

                            <form
                                method="POST"
                                action="{{ route(
                                            'procedimientos.cupos.habilitar',
                                            $cupo
                                        ) }}"
                                class="formulario-habilitar">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-check mr-1"></i>
                                    Habilitar
                                </button>
                            </form>

                            @else

                            <button
                                type="button"
                                class="btn btn-secondary btn-sm btn-block"
                                disabled>
                                <i class="fas fa-lock mr-1"></i>
                                Cupo reservado
                            </button>

                            @endif

                        </div>
                    </div>
                </div>

                @empty

                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>

                    <div>
                        Esta programación no tiene cupos generados.
                    </div>
                </div>

                @endforelse

            </div>
        </div>
    </div>

</div>

{{-- Modales para bloquear --}}
@foreach ($programacion->cupos as $cupo)

@if ($cupo->estado === 'DISPONIBLE')

<div
    class="modal fade"
    id="modalBloquear{{ $cupo->id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route(
                            'procedimientos.cupos.bloquear',
                            $cupo
                        ) }}">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-ban mr-1"></i>
                        Bloquear cupo
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-light border">
                        Horario seleccionado:

                        <strong>
                            {{ substr($cupo->hora, 0, 5) }}
                        </strong>
                    </div>

                    <div class="form-group mb-0">
                        <label>
                            Motivo del bloqueo
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="motivo_bloqueo"
                            class="form-control"
                            rows="3"
                            maxlength="500"
                            placeholder="Ejemplo: reunión médica, mantenimiento o ausencia del profesional"
                            required></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">
                        <i class="fas fa-ban mr-1"></i>
                        Confirmar bloqueo
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>

@endif

@endforeach
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.formulario-habilitar')
            .forEach(function(formulario) {

                formulario.addEventListener(
                    'submit',
                    function(evento) {

                        const confirmar = confirm(
                            '¿Desea habilitar nuevamente este cupo?'
                        );

                        if (!confirmar) {
                            evento.preventDefault();
                        }
                    }
                );

            });

    });
</script>
@stop