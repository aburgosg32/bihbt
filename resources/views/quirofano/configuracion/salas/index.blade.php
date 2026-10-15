@extends('adminlte::page')

@section('title', 'Configuración de Salas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-0">
            <i class="fas fa-procedures mr-2"></i>
            Configuración de Salas
        </h1>

        <small class="text-muted">
            Gestión de salas del Centro Quirúrgico
        </small>
    </div>

    <button
        type="button"
        class="btn btn-primary btn-sm"
        data-toggle="modal"
        data-target="#modalNuevaSala">
        <i class="fas fa-plus mr-1"></i>
        Nueva sala
    </button>
</div>
@stop

@section('content')
<link
    rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
{{-- Mensaje de éxito --}}
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

{{-- Mensaje de error --}}
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ session('error') }}

    <button
        type="button"
        class="close"
        data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

{{-- Errores de validación --}}
@if ($errors->any())
<div class="alert alert-danger">
    <div class="font-weight-bold mb-1">
        No se pudo completar la operación:
    </div>

    <ul class="mb-0 pl-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Resumen --}}
<div class="row quirofano-resumen">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $resumen['total'] }}</h3>
                <p>Total de salas</p>
            </div>

            <div class="icon">
                <i class="fas fa-hospital"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $resumen['operativas'] }}</h3>
                <p>Salas operativas</p>
            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $resumen['inoperativas'] }}</h3>
                <p>Salas inoperativas</p>
            </div>

            <div class="icon">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $resumen['disponibles'] }}</h3>
                <p>Disponibles ahora</p>
            </div>

            <div class="icon">
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>
</div>

{{-- Listado --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    Salas registradas
                </h3>
            </div>

            <div class="col-md-5">
                <form
                    method="GET"
                    action="{{ route('quirofano.salas.index') }}">
                    <div class="input-group input-group-sm">
                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            value="{{ $buscar }}"
                            placeholder="Código, sala o ubicación">

                        <div class="input-group-append">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>

                            @if ($buscar !== '')
                            <a
                                href="{{ route(
                                            'quirofano.salas.index'
                                        ) }}"
                                class="btn btn-secondary"
                                title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Sala</th>
                        <th>Ubicación</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Horario</th>
                        <th class="text-center">Limpieza</th>
                        <th class="text-center">Estado actual</th>
                        <th class="text-center">Condición</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($salas as $sala)
                    @php
                    $claseEstado = match (
                    $sala->estado_actual
                    ) {
                    'DISPONIBLE' => 'success',
                    'PROGRAMADA' => 'primary',
                    'EN_PREPARACION' => 'warning',
                    'EN_USO' => 'danger',
                    'EN_LIMPIEZA' => 'info',
                    'MANTENIMIENTO' => 'secondary',
                    default => 'dark',
                    };
                    @endphp

                    <tr>
                        <td class="align-middle text-nowrap">
                            <strong>
                                {{ $sala->codigo }}
                            </strong>
                        </td>

                        <td class="align-middle">
                            <strong>
                                {{ $sala->nombre }}
                            </strong>

                            @if ($sala->observaciones)
                            <div class="small text-muted">
                                {{ $sala->observaciones }}
                            </div>
                            @endif
                        </td>

                        <td class="align-middle">
                            {{ $sala->ubicacion
                                        ?: 'No registrada' }}
                        </td>

                        <td class="text-center align-middle">
                            @if (
                            $sala->tipo_sala ===
                            'EMERGENCIA'
                            )
                            <span class="badge badge-danger">
                                EMERGENCIA
                            </span>
                            @else
                            <span class="badge badge-primary">
                                PROGRAMADA
                            </span>
                            @endif
                        </td>

                        <td class="text-center align-middle text-nowrap">
                            @if (
                            $sala->tipo_sala ===
                            'EMERGENCIA'
                            )
                            <span
                                class="font-weight-bold text-danger">
                                24 HORAS
                            </span>
                            @else
                            <span class="font-weight-bold">
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
                            </span>
                            @endif
                        </td>

                        <td class="text-center align-middle">
                            <span class="badge badge-light">
                                {{ $sala->minutos_limpieza }}
                                min
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            <span
                                class="badge badge-{{ $claseEstado }} px-2 py-1">
                                {{ str_replace(
                                            '_',
                                            ' ',
                                            $sala->estado_actual
                                        ) }}
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            @if ($sala->operativa)
                            <span class="badge badge-success">
                                OPERATIVA
                            </span>
                            @else
                            <span class="badge badge-danger">
                                INOPERATIVA
                            </span>
                            @endif
                        </td>

                        <td class="text-center align-middle text-nowrap">
                            <button
                                type="button"
                                class="btn btn-warning btn-sm"
                                data-toggle="modal"
                                data-target="#modalEditarSala{{ $sala->id }}"
                                title="Editar sala">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm {{ $sala->operativa
                                            ? 'btn-danger'
                                            : 'btn-success' }}"
                                data-toggle="modal"
                                data-target="#modalOperatividad{{ $sala->id }}"
                                title="{{ $sala->operativa
                                            ? 'Deshabilitar sala'
                                            : 'Habilitar sala' }}">
                                <i
                                    class="fas {{ $sala->operativa
                                                ? 'fa-ban'
                                                : 'fa-check-circle' }} mr-1"></i>

                                {{ $sala->operativa
                                            ? 'Deshabilitar'
                                            : 'Habilitar' }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-muted py-4">
                            <i
                                class="fas fa-info-circle mr-1"></i>

                            No se encontraron salas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($salas->hasPages())
    <div class="card-footer">
        {{ $salas->links(
                    'pagination::bootstrap-4'
                ) }}
    </div>
    @endif
</div>

{{-- =====================================================
         MODALES DE EDICIÓN
         ===================================================== --}}
@foreach ($salas as $sala)
<div
    class="modal fade"
    id="modalEditarSala{{ $sala->id }}"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route(
                            'quirofano.salas.update',
                            $sala
                        ) }}">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Editar sala
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Código</label>

                                <input
                                    type="text"
                                    name="codigo"
                                    class="form-control"
                                    value="{{ $sala->codigo }}"
                                    maxlength="20"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>
                                    Nombre de la sala
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    class="form-control"
                                    value="{{ $sala->nombre }}"
                                    maxlength="100"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ubicación</label>

                        <input
                            type="text"
                            name="ubicacion"
                            class="form-control"
                            value="{{ $sala->ubicacion }}"
                            maxlength="150">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Tipo de sala
                                </label>

                                <select
                                    name="tipo_sala"
                                    class="form-control tipo-sala-select"
                                    required>
                                    <option
                                        value="PROGRAMADA"
                                        @selected(
                                        $sala->tipo_sala ===
                                        'PROGRAMADA'
                                        )
                                        >
                                        Programada
                                    </option>

                                    <option
                                        value="EMERGENCIA"
                                        @selected(
                                        $sala->tipo_sala ===
                                        'EMERGENCIA'
                                        )
                                        >
                                        Emergencia - 24 horas
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="col-md-3"
                            data-horario-programado>
                            <div class="form-group">
                                <label>
                                    Inicio de jornada
                                </label>

                                <input
                                    type="time"
                                    name="hora_inicio_jornada"
                                    class="form-control"
                                    value="{{ substr(
                                                (string)
                                                $sala->hora_inicio_jornada,
                                                0,
                                                5
                                            ) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-3"
                            data-horario-programado>
                            <div class="form-group">
                                <label>
                                    Fin de jornada
                                </label>

                                <input
                                    type="time"
                                    name="hora_fin_jornada"
                                    class="form-control"
                                    value="{{ substr(
                                                (string)
                                                $sala->hora_fin_jornada,
                                                0,
                                                5
                                            ) }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Limpieza</label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        name="minutos_limpieza"
                                        class="form-control"
                                        value="{{ $sala
                                                    ->minutos_limpieza }}"
                                        min="30"
                                        max="45"
                                        required>

                                    <div
                                        class="input-group-append">
                                        <span
                                            class="input-group-text">
                                            min
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="alert alert-info py-2 mensaje-emergencia d-none">
                        <i
                            class="fas fa-info-circle mr-1"></i>

                        Esta sala funciona las 24 horas.
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>

                        <textarea
                            name="observaciones"
                            class="form-control"
                            rows="3"
                            maxlength="1000">{{ $sala->observaciones }}</textarea>
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
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal habilitar/deshabilitar --}}
<div
    class="modal fade"
    id="modalOperatividad{{ $sala->id }}"
    tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route(
                            'quirofano.salas.operatividad',
                            $sala
                        ) }}">
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="operativa"
                    value="{{ $sala->operativa ? 0 : 1 }}">

                <div
                    class="modal-header {{ $sala->operativa
                                ? 'bg-danger'
                                : 'bg-success' }}">
                    <h5 class="modal-title">
                        @if ($sala->operativa)
                        <i class="fas fa-ban mr-1"></i>
                        Deshabilitar sala
                        @else
                        <i
                            class="fas fa-check-circle mr-1"></i>
                        Habilitar sala
                        @endif
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        Sala:

                        <strong>
                            {{ $sala->nombre }}
                        </strong>
                    </p>

                    @if ($sala->operativa)
                    <div class="alert alert-warning">
                        La sala dejará de aparecer
                        como disponible en la
                        planificación quirúrgica.
                    </div>

                    <div class="form-group">
                        <label>
                            Motivo de inoperatividad
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="motivo"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Ejemplo: mantenimiento preventivo..."
                            required></textarea>
                    </div>
                    @else
                    <div class="alert alert-info">
                        La sala será habilitada y su
                        estado cambiará a
                        <strong>DISPONIBLE</strong>.
                    </div>
                    @endif
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
                        class="btn {{ $sala->operativa
                                    ? 'btn-danger'
                                    : 'btn-success' }}">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- =====================================================
         MODAL NUEVA SALA
         ===================================================== --}}
<div
    class="modal fade"
    id="modalNuevaSala"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route('quirofano.salas.store') }}">
                @csrf

                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Registrar nueva sala
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
                            <div class="form-group">
                                <label>
                                    Código
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="codigo"
                                    class="form-control"
                                    value="{{ old('codigo') }}"
                                    placeholder="Ejemplo: SALA-09"
                                    maxlength="20"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>
                                    Nombre
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    class="form-control"
                                    value="{{ old('nombre') }}"
                                    placeholder="Ejemplo: Sala de Operaciones 9"
                                    maxlength="100"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ubicación</label>

                        <input
                            type="text"
                            name="ubicacion"
                            class="form-control"
                            value="{{ old(
                                    'ubicacion',
                                    'Centro Quirúrgico'
                                ) }}"
                            maxlength="150">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Tipo de sala
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="tipo_sala"
                                    class="form-control tipo-sala-select"
                                    required>
                                    <option
                                        value="PROGRAMADA"
                                        @selected(
                                        old( 'tipo_sala' , 'PROGRAMADA'
                                        )==='PROGRAMADA'
                                        )>
                                        Programada
                                    </option>

                                    <option
                                        value="EMERGENCIA"
                                        @selected(
                                        old('tipo_sala')==='EMERGENCIA'
                                        )>
                                        Emergencia - 24 horas
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="col-md-3"
                            data-horario-programado>
                            <div class="form-group">
                                <label>
                                    Inicio de jornada
                                </label>

                                <input
                                    type="time"
                                    name="hora_inicio_jornada"
                                    class="form-control"
                                    value="{{ old(
                                            'hora_inicio_jornada',
                                            '08:10'
                                        ) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-3"
                            data-horario-programado>
                            <div class="form-group">
                                <label>
                                    Fin de jornada
                                </label>

                                <input
                                    type="time"
                                    name="hora_fin_jornada"
                                    class="form-control"
                                    value="{{ old(
                                            'hora_fin_jornada',
                                            '20:00'
                                        ) }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Limpieza</label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        name="minutos_limpieza"
                                        class="form-control"
                                        value="{{ old(
                                                'minutos_limpieza',
                                                45
                                            ) }}"
                                        min="30"
                                        max="45"
                                        required>

                                    <div
                                        class="input-group-append">
                                        <span
                                            class="input-group-text">
                                            min
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="alert alert-info py-2 mensaje-emergencia d-none">
                        <i class="fas fa-info-circle mr-1"></i>
                        Esta sala funcionará las 24 horas.
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>

                        <textarea
                            name="observaciones"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Información adicional de la sala">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        La nueva sala se registrará como
                        <strong>operativa y disponible</strong>.
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
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Registrar sala
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {
            function actualizarHorarioSala(select) {
                const formulario =
                    select.closest('form');

                if (!formulario) {
                    return;
                }

                const esEmergencia =
                    select.value === 'EMERGENCIA';

                const camposHorario =
                    formulario.querySelectorAll(
                        '[data-horario-programado]'
                    );

                const mensaje =
                    formulario.querySelector(
                        '.mensaje-emergencia'
                    );

                camposHorario.forEach(
                    function(contenedor) {
                        const input =
                            contenedor.querySelector(
                                'input'
                            );

                        contenedor.classList.toggle(
                            'd-none',
                            esEmergencia
                        );

                        if (input) {
                            input.disabled =
                                esEmergencia;

                            input.required = !esEmergencia;
                        }
                    }
                );

                if (mensaje) {
                    mensaje.classList.toggle(
                        'd-none',
                        !esEmergencia
                    );
                }
            }

            document
                .querySelectorAll(
                    '.tipo-sala-select'
                )
                .forEach(function(select) {
                    actualizarHorarioSala(select);

                    select.addEventListener(
                        'change',
                        function() {
                            actualizarHorarioSala(
                                select
                            );
                        }
                    );
                });

            /*
             * Si falla la validación al registrar
             * una sala, vuelve a abrir el modal.
             */
            @if($errors -> any() && old('codigo'))
            $('#modalNuevaSala').modal('show');
            @endif

            /*
             * Cierra automáticamente los mensajes.
             */
            window.setTimeout(function() {
                $('.alert-dismissible')
                    .alert('close');
            }, 5000);
        }
    );
</script>
@stop