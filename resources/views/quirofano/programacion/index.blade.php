@extends('adminlte::page')

@section('title', 'Planificación Quirúrgica')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 mb-0">
            <i class="fas fa-calendar-alt mr-2"></i>
            Planificación quirúrgica
        </h1>

        <small class="text-muted">
            Programación y control operativo de las salas
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
        No se pudo completar la operación:
    </strong>

    <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Resumen --}}
<div class="row quirofano-resumen">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>
                    {{ $solicitudesPendientes->count() }}
                </h3>

                <p>Pendientes de programación</p>
            </div>

            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $programaciones->count() }}</h3>
                <p>Operaciones de la fecha</p>
            </div>

            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>
                    {{ $programaciones
                            ->where('estado', 'EN_CURSO')
                            ->count() }}
                </h3>

                <p>Operaciones en curso</p>
            </div>

            <div class="icon">
                <i class="fas fa-procedures"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $salas->count() }}</h3>
                <p>Salas operativas</p>
            </div>

            <div class="icon">
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>
</div>

{{-- Selector de fecha --}}
<div class="card card-outline card-primary">
    <div class="card-body py-2">
        <form
            method="GET"
            action="{{ route(
                    'quirofano.programacion.index'
                ) }}">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group mb-md-0">
                        <label>
                            Consultar programación del día
                        </label>

                        <input
                            type="date"
                            name="fecha"
                            id="fechaConsulta"
                            class="form-control form-control-sm"
                            value="{{ $fecha }}"
                            required>
                    </div>
                </div>

                <div class="col-md-3">
                    <button
                        type="submit"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i>
                        Consultar
                    </button>

                    <a
                        href="{{ route(
                                'quirofano.programacion.index'
                            ) }}"
                        class="btn btn-secondary btn-sm">
                        Hoy
                    </a>
                </div>

                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <span class="badge badge-primary p-2">
                        Programadas: 08:10–20:00
                    </span>

                    <span class="badge badge-danger p-2">
                        Emergencias: 24 horas
                    </span>

                    <span class="badge badge-secondary p-2">
                        Limpieza: 30–45 min
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Solicitudes pendientes --}}
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list-ol mr-1"></i>
            Solicitudes pendientes de programación
        </h3>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table
                class="table table-sm table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th class="text-center">Orden</th>
                        <th>Fecha de envío</th>
                        <th>Cuenta / HC</th>
                        <th>Paciente</th>
                        <th>Procedimiento</th>
                        <th>Médico</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Duración</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (
                    $solicitudesPendientes
                    as $indice => $solicitud
                    )
                    @php
                    $claseTipo = match (
                    $solicitud->tipo_solicitud
                    ) {
                    'EMERGENCIA' => 'danger',
                    'URGENCIA' => 'warning',
                    default => 'primary',
                    };
                    @endphp

                    <tr>
                        <td
                            class="text-center align-middle">
                            <span class="badge badge-secondary">
                                {{ $indice + 1 }}
                            </span>
                        </td>

                        <td
                            class="align-middle text-nowrap">
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
                                            ?: '-' }}
                            </div>
                        </td>

                        <td class="align-middle">
                            {{ $solicitud->paciente }}
                        </td>

                        <td class="align-middle">
                            {{ $solicitud
                                        ->procedimiento_nombre }}
                        </td>

                        <td class="align-middle">
                            {{ $solicitud->medico_nombre }}
                        </td>

                        <td class="text-center align-middle">
                            <span
                                class="badge badge-{{ $claseTipo }}">
                                {{ $solicitud
                                            ->tipo_solicitud }}
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            <span class="badge badge-info">
                                {{ $solicitud
                                            ->duracion_estimada_minutos }}
                                min
                            </span>
                        </td>

                        <td class="text-center align-middle">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm btn-programar"
                                data-toggle="modal"
                                data-target="#modalProgramar"
                                data-id="{{ $solicitud->id }}"
                                data-paciente="{{
                                            $solicitud->paciente
                                        }}"
                                data-cuenta="{{
                                            $solicitud->numero_cuenta
                                        }}"
                                data-procedimiento="{{
                                            $solicitud
                                                ->procedimiento_nombre
                                        }}"
                                data-medico="{{
                                            $solicitud
                                                ->medico_nombre
                                        }}"
                                data-duracion="{{
                                            $solicitud
                                                ->duracion_estimada_minutos
                                        }}"
                                data-tipo="{{
                                            $solicitud
                                                ->tipo_solicitud
                                        }}">
                                <i
                                    class="fas fa-calendar-plus mr-1"></i>
                                Programar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-muted py-4">
                            No existen solicitudes pendientes.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Programaciones de la fecha --}}
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-check mr-1"></i>
            Operaciones programadas para el
            {{ \Carbon\Carbon::parse($fecha)
                    ->format('d/m/Y') }}
        </h3>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table
                class="table table-sm table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Sala</th>
                        <th>Horario probable</th>
                        <th>Sala disponible</th>
                        <th>Paciente</th>
                        <th>Procedimiento</th>
                        <th>Médico</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Control</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (
                    $programaciones as $programacion
                    )
                    @php
                    $claseEstado = match (
                    $programacion->estado
                    ) {
                    'PROGRAMADA' => 'primary',
                    'EN_PREPARACION' => 'warning',
                    'EN_CURSO' => 'danger',
                    'FINALIZADA' => 'success',
                    'CANCELADA' => 'secondary',
                    default => 'secondary',
                    };
                    @endphp

                    <tr>
                        <td class="align-middle">
                            <strong>
                                {{ $programacion
                                            ->sala
                                            ->nombre }}
                            </strong>

                            <div class="small text-muted">
                                {{ $programacion
                                            ->sala
                                            ->tipo_sala }}
                            </div>
                        </td>

                        <td
                            class="align-middle text-nowrap">
                            <strong>
                                {{ $programacion
                                            ->fecha_hora_inicio
                                            ->format('H:i') }}
                                –
                                {{ $programacion
                                            ->fecha_hora_fin
                                            ->format('H:i') }}
                            </strong>

                            <div class="small text-muted">
                                Duración:
                                {{ $programacion
                                            ->duracion_operacion_minutos }}
                                min
                            </div>
                        </td>

                        <td
                            class="align-middle text-nowrap">
                            <strong>
                                {{ $programacion
                                            ->fecha_hora_disponible
                                            ->format('H:i') }}
                            </strong>

                            <div class="small text-muted">
                                Incluye
                                {{ $programacion
                                            ->minutos_limpieza }}
                                min de limpieza
                            </div>
                        </td>

                        <td class="align-middle">
                            {{ $programacion
                                        ->solicitud
                                        ->paciente }}

                            <div class="small text-muted">
                                Cuenta:
                                {{ $programacion
                                            ->solicitud
                                            ->numero_cuenta }}
                            </div>
                        </td>

                        <td class="align-middle">
                            {{ $programacion
                                        ->solicitud
                                        ->procedimiento_nombre }}
                        </td>

                        <td class="align-middle">
                            {{ $programacion
                                        ->solicitud
                                        ->medico_nombre }}
                        </td>

                        <td class="text-center align-middle">
                            <span
                                class="badge badge-{{ $claseEstado }}">
                                {{ str_replace(
                                            '_',
                                            ' ',
                                            $programacion->estado
                                        ) }}
                            </span>
                        </td>

                        <td
                            class="text-center align-middle text-nowrap">
                            {{-- Iniciar preparación --}}
                            @if (
                            $programacion->estado ===
                            'PROGRAMADA'
                            )
                            <form
                                method="POST"
                                action="{{ route(
                                                'quirofano.estado-salas.cambiar-estado',
                                                $programacion
                                            ) }}"
                                class="d-inline"
                                onsubmit="return confirm(
                                                '¿Iniciar la preparación de la sala?'
                                            );">
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="PREPARAR">

                                <button
                                    type="submit"
                                    class="btn btn-warning btn-sm"
                                    title="Iniciar preparación">
                                    <i
                                        class="fas fa-clipboard-check"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Iniciar operación --}}
                            @if (
                            $programacion->estado ===
                            'EN_PREPARACION'
                            )
                            <form
                                method="POST"
                                action="{{ route(
                                                'quirofano.estado-salas.cambiar-estado',
                                                $programacion
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
                                    class="btn btn-danger btn-sm"
                                    title="Iniciar operación">
                                    <i class="fas fa-play"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Finalizar operación --}}
                            @if (
                            $programacion->estado ===
                            'EN_CURSO'
                            )
                            <form
                                method="POST"
                                action="{{ route(
                                                'quirofano.estado-salas.cambiar-estado',
                                                $programacion
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
                                    class="btn btn-success btn-sm"
                                    title="Finalizar operación">
                                    <i class="fas fa-stop"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Finalizar limpieza --}}
                            @if (
                            $programacion->estado ===
                            'FINALIZADA'
                            && !$programacion
                            ->fecha_fin_limpieza
                            )
                            <form
                                method="POST"
                                action="{{ route(
                                                'quirofano.estado-salas.cambiar-estado',
                                                $programacion
                                            ) }}"
                                class="d-inline"
                                onsubmit="return confirm(
                                                '¿Confirmar el fin de la limpieza?'
                                            );">
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="FINALIZAR_LIMPIEZA">

                                <button
                                    type="submit"
                                    class="btn btn-info btn-sm"
                                    title="Finalizar limpieza">
                                    <i class="fas fa-broom"></i>
                                </button>
                            </form>
                            @endif

                            @if (
                            $programacion->estado ===
                            'FINALIZADA'
                            && $programacion
                            ->fecha_fin_limpieza
                            )
                            <span class="text-success">
                                <i
                                    class="fas fa-check-circle"></i>
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="8"
                            class="text-center text-muted py-4">
                            No existen operaciones programadas
                            para esta fecha.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal de programación manual --}}
<div
    class="modal fade"
    id="modalProgramar"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route(
                        'quirofano.programacion.store'
                    ) }}"
                id="formProgramar">
                @csrf

                <input
                    type="hidden"
                    name="solicitud_id"
                    id="solicitudId">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        Programar operación
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong>Paciente</strong>
                                    <div id="modalPaciente"></div>
                                </div>

                                <div class="col-md-4">
                                    <strong>Cuenta</strong>
                                    <div id="modalCuenta"></div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <strong>Procedimiento</strong>
                                    <div id="modalProcedimiento"></div>
                                </div>

                                <div class="col-md-8 mt-2">
                                    <strong>Médico</strong>
                                    <div id="modalMedico"></div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <strong>Duración estimada</strong>
                                    <div>
                                        <span id="modalDuracion"></span>
                                        minutos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input
                        type="hidden"
                        id="tipoSolicitud">

                    <input
                        type="hidden"
                        id="duracionSolicitud">

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>
                                    Sala
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="sala_id"
                                    id="salaId"
                                    class="form-control"
                                    required>
                                    <option value="">
                                        Seleccione una sala
                                    </option>

                                    @foreach ($salas as $sala)
                                    <option
                                        value="{{ $sala->id }}"
                                        data-tipo="{{
                                                    $sala->tipo_sala
                                                }}"
                                        data-limpieza="{{
                                                    $sala
                                                        ->minutos_limpieza
                                                }}"
                                        data-inicio="{{
                                                    substr(
                                                        (string)
                                                        $sala
                                                            ->hora_inicio_jornada,
                                                        0,
                                                        5
                                                    )
                                                }}"
                                        data-fin="{{
                                                    substr(
                                                        (string)
                                                        $sala
                                                            ->hora_fin_jornada,
                                                        0,
                                                        5
                                                    )
                                                }}">
                                        {{ $sala->codigo }}
                                        -
                                        {{ $sala->nombre }}
                                        (
                                        {{ $sala->tipo_sala }}
                                        )
                                    </option>
                                    @endforeach
                                </select>

                                <small
                                    id="mensajeTipoSala"
                                    class="form-text text-muted"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Fecha de operación
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="fecha_operacion"
                                    id="fechaOperacion"
                                    class="form-control"
                                    min="{{ now()->toDateString() }}"
                                    value="{{ $fecha }}"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    Inicio probable
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="time"
                                    name="hora_inicio"
                                    id="horaInicio"
                                    class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div
                        id="resumenCalculo"
                        class="alert alert-info d-none">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Inicio probable</strong>
                                <div id="calculoInicio"></div>
                            </div>

                            <div class="col-md-4">
                                <strong>Fin probable</strong>
                                <div id="calculoFin"></div>
                            </div>

                            <div class="col-md-4">
                                <strong>Sala disponible</strong>
                                <div id="calculoDisponible"></div>
                            </div>
                        </div>

                        <div class="small mt-2">
                            Este horario será validado nuevamente
                            al guardar para evitar cruces.
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>

                        <textarea
                            name="observaciones"
                            class="form-control"
                            rows="2"
                            maxlength="2000"></textarea>
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
                        id="btnGuardarProgramacion"
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Guardar programación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sala = document.getElementById('salaId');
        const fecha = document.getElementById('fechaOperacion');
        const hora = document.getElementById('horaInicio');
        const duracion = document.getElementById(
            'duracionSolicitud'
        );
        const tipoSolicitud = document.getElementById(
            'tipoSolicitud'
        );
        const resumen = document.getElementById(
            'resumenCalculo'
        );

        document
            .querySelectorAll('.btn-programar')
            .forEach(function(boton) {
                boton.addEventListener('click', function() {
                    document.getElementById(
                        'solicitudId'
                    ).value = boton.dataset.id;

                    document.getElementById(
                        'modalPaciente'
                    ).textContent = boton.dataset.paciente;

                    document.getElementById(
                        'modalCuenta'
                    ).textContent = boton.dataset.cuenta;

                    document.getElementById(
                            'modalProcedimiento'
                        ).textContent =
                        boton.dataset.procedimiento;

                    document.getElementById(
                        'modalMedico'
                    ).textContent = boton.dataset.medico;

                    document.getElementById(
                        'modalDuracion'
                    ).textContent = boton.dataset.duracion;

                    duracion.value = boton.dataset.duracion;
                    tipoSolicitud.value = boton.dataset.tipo;

                    fecha.value =
                        document.getElementById(
                            'fechaConsulta'
                        ).value;

                    hora.value = '';
                    sala.value = '';
                    resumen.classList.add('d-none');

                    filtrarSalas();
                });
            });

        function filtrarSalas() {
            const tipo = tipoSolicitud.value;

            Array.from(sala.options).forEach(
                function(opcion) {
                    if (!opcion.value) {
                        opcion.hidden = false;
                        return;
                    }

                    const tipoSala =
                        opcion.dataset.tipo;

                    let visible = true;

                    if (tipo === 'ELECTIVA') {
                        visible =
                            tipoSala === 'PROGRAMADA';
                    }

                    if (tipo === 'EMERGENCIA') {
                        visible =
                            tipoSala === 'EMERGENCIA';
                    }

                    /*
                     * URGENCIA puede utilizar cualquier sala.
                     */
                    opcion.hidden = !visible;
                }
            );

            document.getElementById(
                    'mensajeTipoSala'
                ).textContent =
                tipo === 'EMERGENCIA' ?
                'Solo se muestran salas de emergencia.' :
                (
                    tipo === 'ELECTIVA' ?
                    'Solo se muestran salas programadas.' :
                    'Puede seleccionar una sala programada o de emergencia.'
                );
        }

        function formatearFechaHora(fechaObjeto) {
            return fechaObjeto.toLocaleString(
                'es-PE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            );
        }

        function calcularHorario() {
            const opcion =
                sala.options[sala.selectedIndex];

            if (
                !sala.value ||
                !fecha.value ||
                !hora.value ||
                !duracion.value
            ) {
                resumen.classList.add('d-none');
                return;
            }

            const inicio = new Date(
                fecha.value + 'T' + hora.value + ':00'
            );

            const fin = new Date(
                inicio.getTime() +
                Number(duracion.value) * 60000
            );

            const minutosLimpieza =
                Number(opcion.dataset.limpieza || 45);

            const disponible = new Date(
                fin.getTime() +
                minutosLimpieza * 60000
            );

            document.getElementById(
                    'calculoInicio'
                ).textContent =
                formatearFechaHora(inicio);

            document.getElementById(
                    'calculoFin'
                ).textContent =
                formatearFechaHora(fin);

            document.getElementById(
                    'calculoDisponible'
                ).textContent =
                formatearFechaHora(disponible) +
                ' (' +
                minutosLimpieza +
                ' min de limpieza)';

            resumen.classList.remove('d-none');
        }

        sala.addEventListener('change', calcularHorario);
        fecha.addEventListener('change', calcularHorario);
        hora.addEventListener('change', calcularHorario);

        document
            .getElementById('formProgramar')
            .addEventListener('submit', function() {
                const boton = document.getElementById(
                    'btnGuardarProgramacion'
                );

                boton.disabled = true;
                boton.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    ' Guardando';
            });
    });
</script>
@stop