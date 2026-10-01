@extends('adminlte::page')

@section('title', 'Estado de Salas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-0">
            <i class="fas fa-door-open mr-2"></i>
            Estado de Salas
        </h1>

        <small class="text-muted">
            Control operativo del Centro Quirúrgico
        </small>
    </div>

    <a
        href="{{ route('quirofano.estado-salas.index') }}"
        class="btn btn-primary btn-sm">
        <i class="fas fa-sync-alt mr-1"></i>
        Actualizar
    </a>
</div>
@stop

@section('content')
<link
    rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i>
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
<div class="alert alert-danger">
    <strong>
        No se pudo actualizar el estado:
    </strong>

    <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Leyenda --}}
<div class="card card-outline card-primary mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center">
            <strong class="mr-3">
                Estados:
            </strong>

            <span class="badge badge-success p-2 mr-2 mb-1">
                Disponible
            </span>

            <span class="badge badge-primary p-2 mr-2 mb-1">
                Programada
            </span>

            <span class="badge badge-warning p-2 mr-2 mb-1">
                En preparación
            </span>

            <span class="badge badge-danger p-2 mr-2 mb-1">
                En uso
            </span>

            <span class="badge badge-info p-2 mr-2 mb-1">
                En limpieza
            </span>

            <span class="badge badge-secondary p-2 mb-1">
                Inoperativa
            </span>
        </div>
    </div>
</div>

<div class="row">
    @forelse ($salas as $sala)
    @php
    $programacionActiva =
    $sala->programacion_activa;

    $programacionLimpieza =
    $sala->ultima_finalizada;

    $proximaProgramacion =
    $sala->proxima_programacion;

    if ($programacionActiva) {
    $programacionVisible =
    $programacionActiva;
    } elseif (
    $sala->estado_actual ===
    'EN_LIMPIEZA'
    && $programacionLimpieza
    ) {
    $programacionVisible =
    $programacionLimpieza;
    } else {
    $programacionVisible =
    $proximaProgramacion;
    }

    $claseSala = match (
    true
    ) {
    !$sala->operativa =>
    'sala-inoperativa',

    $sala->estado_actual ===
    'EN_PREPARACION' =>
    'sala-preparacion',

    $sala->estado_actual ===
    'EN_USO' =>
    'sala-en-uso',

    $sala->estado_actual ===
    'EN_LIMPIEZA' =>
    'sala-limpieza',

    $proximaProgramacion !== null =>
    'sala-programada',

    default =>
    'sala-disponible',
    };

    $textoEstado = !$sala->operativa
    ? 'INOPERATIVA'
    : (
    $sala->estado_actual ===
    'DISPONIBLE'
    && $proximaProgramacion
    ? 'PROGRAMADA'
    : str_replace(
    '_',
    ' ',
    $sala->estado_actual
    )
    );

    $claseBadge = match (
    true
    ) {
    !$sala->operativa =>
    'secondary',

    $sala->estado_actual ===
    'EN_PREPARACION' =>
    'warning',

    $sala->estado_actual ===
    'EN_USO' =>
    'danger',

    $sala->estado_actual ===
    'EN_LIMPIEZA' =>
    'info',

    $proximaProgramacion !== null =>
    'primary',

    default =>
    'success',
    };
    @endphp

    <div class="col-xl-4 col-lg-6 col-md-6">
        <div
            class="card sala-operacion-card {{ $claseSala }}">
            <div class="card-header">
                <div
                    class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title font-weight-bold">
                            <i
                                class="fas fa-procedures mr-1"></i>

                            {{ $sala->nombre }}
                        </h3>

                        <div class="small mt-1">
                            {{ $sala->codigo }}
                        </div>
                    </div>

                    <span
                        class="badge badge-{{ $claseBadge }} p-2">
                        {{ $textoEstado }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <span class="text-muted small">
                            Tipo
                        </span>

                        <div class="font-weight-bold">
                            {{ $sala->tipo_sala }}
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-muted small">
                            Horario
                        </span>

                        <div class="font-weight-bold">
                            @if (
                            $sala->tipo_sala ===
                            'EMERGENCIA'
                            )
                            24 HORAS
                            @else
                            {{ substr(
                                            (string)
                                            $sala->hora_inicio_jornada,
                                            0,
                                            5
                                        ) }}
                            –
                            {{ substr(
                                            (string)
                                            $sala->hora_fin_jornada,
                                            0,
                                            5
                                        ) }}
                            @endif
                        </div>
                    </div>
                </div>

                @if (!$sala->operativa)
                <div class="alert alert-secondary mb-0">
                    <i class="fas fa-ban mr-1"></i>

                    <strong>
                        Sala fuera de servicio
                    </strong>

                    @if ($sala->observaciones)
                    <div class="small mt-1">
                        {{ $sala->observaciones }}
                    </div>
                    @endif
                </div>
                @elseif ($programacionVisible)
                <div class="sala-paciente">
                    <div class="mb-2">
                        <span class="text-muted small">
                            Paciente
                        </span>

                        <div class="font-weight-bold">
                            {{ $programacionVisible
                                            ->solicitud
                                            ->paciente }}
                        </div>

                        <div class="small text-muted">
                            Cuenta:
                            {{ $programacionVisible
                                            ->solicitud
                                            ->numero_cuenta }}

                            · HC:
                            {{ $programacionVisible
                                            ->solicitud
                                            ->historia_clinica }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small">
                            Procedimiento
                        </span>

                        <div>
                            {{ $programacionVisible
                                            ->solicitud
                                            ->procedimiento_nombre }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small">
                            Médico
                        </span>

                        <div>
                            {{ $programacionVisible
                                            ->solicitud
                                            ->medico_nombre }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <span class="text-muted small">
                                Programada
                            </span>

                            <div class="font-weight-bold">
                                {{ $programacionVisible
                                                ->fecha_hora_inicio
                                                ->format('d/m H:i') }}
                            </div>
                        </div>

                        <div class="col-4">
                            <span class="text-muted small">
                                Fin probable
                            </span>

                            <div class="font-weight-bold">
                                {{ $programacionVisible
                                                ->fecha_hora_fin
                                                ->format('H:i') }}
                            </div>
                        </div>

                        <div class="col-4">
                            <span class="text-muted small">
                                Duración
                            </span>

                            <div class="font-weight-bold">
                                {{ $programacionVisible
                                                ->duracion_operacion_minutos }}
                                min
                            </div>
                        </div>
                    </div>

                    @if (
                    $programacionVisible
                    ->fecha_inicio_real
                    )
                    <div class="alert alert-light py-2 mt-3 mb-0">
                        Inicio real:
                        <strong>
                            {{ $programacionVisible
                                                ->fecha_inicio_real
                                                ->format('H:i') }}
                        </strong>

                        @if (
                        $programacionVisible
                        ->fecha_fin_real
                        )
                        · Fin real:
                        <strong>
                            {{ $programacionVisible
                                                    ->fecha_fin_real
                                                    ->format('H:i') }}
                        </strong>
                        @endif
                    </div>
                    @endif
                </div>
                @else
                <div
                    class="text-center text-success py-4">
                    <i
                        class="fas fa-check-circle fa-3x mb-2"></i>

                    <div class="font-weight-bold">
                        Sala disponible
                    </div>

                    <small class="text-muted">
                        No tiene operaciones pendientes
                    </small>
                </div>
                @endif
            </div>

            @if (
            $sala->operativa
            && $programacionVisible
            )
            <div class="card-footer text-right">
                {{-- Iniciar preparación --}}
                @if (
                $sala->estado_actual ===
                'DISPONIBLE'
                && $programacionVisible->estado ===
                'PROGRAMADA'
                )
                <form
                    method="POST"
                    action="{{ route(
                                        'quirofano.estado-salas.cambiar-estado',
                                        $programacionVisible
                                    ) }}"
                    class="d-inline"
                    onsubmit="return confirm(
                                        '¿Iniciar la preparación de esta sala?'
                                    );">
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="accion"
                        value="PREPARAR">

                    <button
                        type="submit"
                        class="btn btn-warning btn-sm">
                        <i
                            class="fas fa-clipboard-check mr-1"></i>
                        Iniciar preparación
                    </button>
                </form>
                @endif

                {{-- Iniciar operación --}}
                @if (
                $programacionVisible->estado ===
                'EN_PREPARACION'
                )
                <form
                    method="POST"
                    action="{{ route(
                                        'quirofano.estado-salas.cambiar-estado',
                                        $programacionVisible
                                    ) }}"
                    class="d-inline"
                    onsubmit="return confirm(
                                        '¿Confirmar el inicio de la operación?'
                                    );">
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="accion"
                        value="INICIAR">

                    <button
                        type="submit"
                        class="btn btn-danger btn-sm">
                        <i
                            class="fas fa-play mr-1"></i>
                        Iniciar operación
                    </button>
                </form>
                @endif

                {{-- Finalizar operación --}}
                @if (
                $programacionVisible->estado ===
                'EN_CURSO'
                )
                <form
                    method="POST"
                    action="{{ route(
                                        'quirofano.estado-salas.cambiar-estado',
                                        $programacionVisible
                                    ) }}"
                    class="d-inline"
                    onsubmit="return confirm(
                                        '¿Confirmar que la operación finalizó?'
                                    );">
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="accion"
                        value="FINALIZAR">

                    <button
                        type="submit"
                        class="btn btn-success btn-sm">
                        <i
                            class="fas fa-stop mr-1"></i>
                        Finalizar operación
                    </button>
                </form>
                @endif

                {{-- Finalizar limpieza --}}
                @if (
                $sala->estado_actual ===
                'EN_LIMPIEZA'
                && $programacionVisible->estado ===
                'FINALIZADA'
                && !$programacionVisible
                ->fecha_fin_limpieza
                )
                <form
                    method="POST"
                    action="{{ route(
                                        'quirofano.estado-salas.cambiar-estado',
                                        $programacionVisible
                                    ) }}"
                    class="d-inline"
                    onsubmit="return confirm(
                                        '¿Confirmar que terminó la limpieza?'
                                    );">
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="accion"
                        value="FINALIZAR_LIMPIEZA">

                    <button
                        type="submit"
                        class="btn btn-info btn-sm">
                        <i
                            class="fas fa-broom mr-1"></i>
                        Finalizar limpieza
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            No existen salas registradas.
        </div>
    </div>
    @endforelse
</div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        /*
         * Actualización automática cada 30 segundos.
         * Se detiene si el usuario está enviando un formulario.
         */
        let enviandoFormulario = false;

        document
            .querySelectorAll('form')
            .forEach(function(formulario) {
                formulario.addEventListener(
                    'submit',
                    function() {
                        enviandoFormulario = true;

                        const boton =
                            formulario.querySelector(
                                'button[type="submit"]'
                            );

                        if (boton) {
                            boton.disabled = true;
                        }
                    }
                );
            });

        window.setInterval(function() {
            if (!enviandoFormulario) {
                window.location.reload();
            }
        }, 30000);
    });
</script>
@stop