@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('title', 'Citas de procedimientos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">

<style>
    .tabla-cupos tbody tr {
        transition: background-color .2s ease;
    }

    .tabla-cupos tbody tr:hover {
        background-color: #f4f8fb;
    }

    .hora-cupo {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .estado-cupo {
        min-width: 105px;
        display: inline-block;
        padding: 7px 10px;
    }

    .panel-inicial {
        min-height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@stop

@section('content_header')
<div>
    <h1 class="m-0">
        <i class="fas fa-calendar-check text-primary mr-2"></i>
        Citas de procedimientos
    </h1>

    <small class="text-muted">
        Seleccione el procedimiento y la fecha para consultar los cupos
    </small>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Selección --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-1"></i>
                Buscar disponibilidad
            </h3>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-7">
                    <div class="form-group mb-md-0">
                        <label>
                            Procedimiento
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="procedimiento_id"
                            class="form-control">
                            <option value="">
                                Seleccione un procedimiento...
                            </option>

                            @foreach ($procedimientos as $procedimiento)
                            <option value="{{ $procedimiento->id }}">
                                {{ $procedimiento->servicio->nombre
                                        ?? 'SIN SERVICIO' }}

                                - {{ $procedimiento->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <label>
                            Fecha de atención
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            id="fecha"
                            class="form-control"
                            disabled>

                        <small
                            id="mensajeFechas"
                            class="form-text text-muted">
                            Primero seleccione un procedimiento.
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Resumen --}}


    {{-- Listado --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-list-alt mr-1"></i>
                Cupos de atención
            </h3>

            <div
                id="resumenCupos"
                class="card-tools"
                style="display: none;">
                <span class="badge badge-success px-2 py-2 mr-1">
                    Disponibles:
                    <span id="totalDisponibles">0</span>
                </span>

                <span class="badge badge-primary px-2 py-2 mr-1">
                    Reservados:
                    <span id="totalReservados">0</span>
                </span>

                <span class="badge badge-warning px-2 py-2 mr-1">
                    En proceso:
                    <span id="totalEnProceso">0</span>
                </span>

                <span class="badge badge-danger px-2 py-2">
                    Bloqueados:
                    <span id="totalBloqueados">0</span>
                </span>
            </div>

        </div>

        <div class="card-body p-0">

            <div
                id="panelInicial"
                class="panel-inicial text-center text-muted">
                <div>
                    <i class="far fa-calendar-alt fa-4x mb-3"></i>

                    <h5>
                        Seleccione un procedimiento y una fecha
                    </h5>

                    <p class="mb-0">
                        Aquí aparecerá la lista completa de horarios.
                    </p>
                </div>
            </div>

            <div
                id="contenedorTabla"
                class="table-responsive"
                style="display: none;">
                <table class="table table-hover table-striped tabla-cupos mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Hora</th>
                            <th>Médico</th>
                            <th style="width: 135px;" class="text-center">
                                Estado
                            </th>
                            <th>Paciente</th>
                            <th style="width: 270px;" class="text-center">
                                Acción
                            </th>
                        </tr>
                    </thead>

                    <tbody id="cuerpoCupos"></tbody>
                </table>
            </div>

        </div>
    </div>

</div>
{{-- Modal: asignar paciente --}}
<div
    class="modal fade"
    id="modalAgregarPaciente"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form
                id="formAgregarPaciente"
                method="POST"
                action="{{ route('procedimientos.citas.store') }}">
                @csrf

                <input
                    type="hidden"
                    name="procedimiento_cupo_id"
                    id="modalCupoId">

                <input
                    type="hidden"
                    name="bloqueo_token"
                    id="bloqueoToken">

                <div class="modal-header bg-success">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus mr-1"></i>
                        Agregar paciente al cupo
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- Información del cupo --}}
                    <div class="alert alert-light border">
                        <div class="row">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Procedimiento
                                </small>

                                <strong id="modalProcedimiento">
                                    —
                                </strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Fecha
                                </small>

                                <strong id="modalFecha">
                                    —
                                </strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Hora seleccionada
                                </small>

                                <strong
                                    id="modalHora"
                                    class="text-success">
                                    —
                                </strong>
                            </div>

                        </div>
                    </div>

                    {{-- Búsqueda de cuenta --}}
                    <div class="border rounded p-3 mb-3">
                        <label>
                            Número de cuenta de atención
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input
                                type="text"
                                name="numero_cuenta"
                                id="numeroCuenta"
                                class="form-control"
                                placeholder="Ejemplo: 2375500"
                                autocomplete="off"
                                required>

                            <div class="input-group-append">
                                <button
                                    type="button"
                                    id="btnBuscarCuenta"
                                    class="btn btn-success">
                                    <i class="fas fa-search mr-1"></i>
                                    Buscar
                                </button>
                            </div>

                        </div>

                        <div
                            id="mensajeBusqueda"
                            class="mt-2"></div>
                    </div>

                    {{-- Datos encontrados --}}
                    <div
                        id="datosPaciente"
                        class="border rounded p-3"
                        style="display: none;">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user-injured mr-1"></i>
                            Datos del paciente
                        </h6>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">
                                    Paciente
                                </small>

                                <strong id="datoPaciente">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Historia clínica
                                </small>

                                <strong id="datoHistoria">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Edad
                                </small>

                                <strong id="datoEdad">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Teléfono
                                </small>

                                <strong id="datoTelefono">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Tipo de servicio
                                </small>

                                <strong id="datoTipoServicio">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Servicio
                                </small>

                                <strong id="datoServicio">—</strong>
                            </div>

                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block">
                                    Financiamiento
                                </small>

                                <strong id="datoFinanciamiento">—</strong>
                            </div>

                        </div>

                        <div class="form-group mb-0">
                            <label>Observación</label>

                            <textarea
                                name="observacion"
                                id="observacion"
                                class="form-control"
                                rows="2"
                                maxlength="1000"
                                placeholder="Opcional"></textarea>
                        </div>
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
                        id="btnGuardarCita"
                        class="btn btn-success"
                        disabled>
                        <i class="fas fa-save mr-1"></i>
                        Confirmar cita
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
{{-- Modal para reprogramar --}}
<div
    class="modal fade"
    id="modalReprogramar"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form
                id="formReprogramar"
                method="POST">
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="nuevo_cupo_id"
                    id="nuevoCupoId">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Reprogramar cita
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-light border">
                        <small class="text-muted d-block">
                            Paciente
                        </small>

                        <strong id="reprogramarPaciente">—</strong>

                        <div class="mt-2">
                            <small class="text-muted">
                                Horario actual:
                            </small>

                            <strong id="reprogramarHorarioActual">
                                —
                            </strong>
                        </div>
                    </div>

                    <div
                        id="mensajeReprogramacion"
                        class="mb-3"></div>

                    <div class="row">

                        <div class="col-md-5">
                            <div class="form-group">
                                <label>
                                    Nuevo procedimiento
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="nuevoProcedimiento"
                                    class="form-control"
                                    required>
                                    <option value="">
                                        Seleccione...
                                    </option>

                                    @foreach ($procedimientos as $procedimiento)
                                    <option
                                        value="{{ $procedimiento->id }}">
                                        {{ $procedimiento->servicio->nombre
                                                ?? 'SIN SERVICIO' }}
                                        -
                                        {{ $procedimiento->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    Nueva fecha
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="nuevaFecha"
                                    class="form-control"
                                    min="{{ now()->format('Y-m-d') }}"
                                    disabled
                                    required>

                                <small
                                    id="nuevasFechasDisponibles"
                                    class="text-muted"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Nuevo horario
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="nuevoHorario"
                                    class="form-control"
                                    disabled
                                    required>
                                    <option value="">
                                        Seleccione...
                                    </option>
                                </select>
                            </div>
                        </div>

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
                        id="btnConfirmarReprogramacion"
                        class="btn btn-warning"
                        disabled>
                        <i class="fas fa-save mr-1"></i>
                        Confirmar reprogramación
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

        const procedimiento = document.getElementById('procedimiento_id');
        const fecha = document.getElementById('fecha');
        const mensajeFechas = document.getElementById('mensajeFechas');
        const resumenCupos = document.getElementById('resumenCupos');
        const panelInicial = document.getElementById('panelInicial');
        const contenedorTabla = document.getElementById('contenedorTabla');
        const cuerpoCupos = document.getElementById('cuerpoCupos');
        const formAgregarPaciente = document.getElementById('formAgregarPaciente');
        const modalCupoId = document.getElementById('modalCupoId');
        const bloqueoToken = document.getElementById('bloqueoToken');
        const numeroCuenta = document.getElementById('numeroCuenta');
        const btnBuscarCuenta = document.getElementById('btnBuscarCuenta');
        const btnGuardarCita = document.getElementById('btnGuardarCita');
        const mensajeBusqueda = document.getElementById('mensajeBusqueda');
        const datosPaciente = document.getElementById('datosPaciente');

        const rutaBuscarCuenta = @json(
            route(
                'procedimientos.citas.buscar-cuenta',
                ['cuenta' => 'CUENTA_REEMPLAZAR']
            )
        );

        const rutaTicket = @json(
            route(
                'procedimientos.citas.ticket',
                ['cita' => 'CITA_REEMPLAZAR']
            )
        );

        const rutaReprogramar = @json(
            route(
                'procedimientos.citas.reprogramar',
                ['cita' => 'CITA_REEMPLAZAR']
            )
        );

        const rutaApartarCupo = @json(
            route(
                'procedimientos.citas.cupos.apartar',
                ['cupo' => 'CUPO_REEMPLAZAR']
            )
        );

        const rutaLiberarCupo = @json(
            route(
                'procedimientos.citas.cupos.liberar',
                ['cupo' => 'CUPO_REEMPLAZAR']
            )
        );

        const formReprogramar = document.getElementById(
            'formReprogramar'
        );

        const nuevoProcedimiento = document.getElementById(
            'nuevoProcedimiento'
        );

        const nuevaFecha = document.getElementById(
            'nuevaFecha'
        );

        const nuevoHorario = document.getElementById(
            'nuevoHorario'
        );

        const nuevoCupoId = document.getElementById(
            'nuevoCupoId'
        );

        const btnConfirmarReprogramacion =
            document.getElementById(
                'btnConfirmarReprogramacion'
            );

        const mensajeReprogramacion =
            document.getElementById(
                'mensajeReprogramacion'
            );

        const nuevasFechasDisponibles =
            document.getElementById(
                'nuevasFechasDisponibles'
            );

        let fechasLibresReprogramacion = [];

        let fechasDisponibles = [];
        let fechasProgramadas = [];

        let cupoTemporalId = null;
        let citaConfirmada = false;

        cuerpoCupos.addEventListener(
            'click',
            async function(evento) {
                const boton = evento.target.closest(
                    '.btn-agregar-paciente'
                );

                if (!boton) {
                    return;
                }

                const cupoId = boton.dataset.cupoId;
                const textoOriginal = boton.innerHTML;

                boton.disabled = true;
                boton.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-1"></i>
                    Apartando...
                `;

                try {
                    const url = rutaApartarCupo.replace(
                        'CUPO_REEMPLAZAR',
                        cupoId
                    );

                    const datos = new FormData();
                    datos.append('_token', @json(csrf_token()));

                    const respuesta = await fetch(url, {
                        method: 'POST',
                        body: datos,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const resultado = await respuesta.json();

                    if (!respuesta.ok) {
                        let mensaje = resultado.message ||
                            'No se pudo apartar el cupo.';

                        if (resultado.errors) {
                            mensaje = Object.values(
                                resultado.errors
                            ).flat().join(' ');
                        }

                        throw new Error(mensaje);
                    }

                    formAgregarPaciente.reset();

                    cupoTemporalId = cupoId;
                    citaConfirmada = false;
                    modalCupoId.value = cupoId;
                    bloqueoToken.value = resultado.bloqueo_token;

                    document.getElementById('modalHora')
                        .textContent = boton.dataset.hora;

                    document.getElementById('modalFecha')
                        .textContent = formatearFecha(fecha.value);

                    document.getElementById('modalProcedimiento')
                        .textContent =
                        procedimiento.options[
                            procedimiento.selectedIndex
                        ].text;

                    mensajeBusqueda.innerHTML = '';
                    datosPaciente.style.display = 'none';
                    btnGuardarCita.disabled = true;

                    $('#modalAgregarPaciente').modal('show');
                    cargarCupos(true);

                    setTimeout(function() {
                        numeroCuenta.focus();
                    }, 400);
                } catch (error) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cupo no disponible',
                        text: error.message,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#007bff'
                    });

                    cargarCupos(true);
                } finally {
                    boton.disabled = false;
                    boton.innerHTML = textoOriginal;
                }
            }
        );
        cuerpoCupos.addEventListener('click', function(evento) {
            const boton = evento.target.closest(
                '.btn-reprogramar'
            );

            if (!boton) {
                return;
            }

            formReprogramar.reset();

            nuevoCupoId.value = '';
            nuevaFecha.disabled = true;
            nuevoHorario.disabled = true;
            btnConfirmarReprogramacion.disabled = true;

            nuevoHorario.innerHTML = `
        <option value="">Seleccione...</option>
    `;

            nuevasFechasDisponibles.textContent = '';
            mensajeReprogramacion.innerHTML = '';

            document.getElementById('reprogramarPaciente')
                .textContent = boton.dataset.paciente;

            document.getElementById('reprogramarHorarioActual')
                .textContent =
                formatearFecha(fecha.value) +
                ' - ' +
                boton.dataset.hora;

            formReprogramar.action = rutaReprogramar.replace(
                'CITA_REEMPLAZAR',
                boton.dataset.citaId
            );

            $('#modalReprogramar').modal('show');
        });

        nuevoProcedimiento.addEventListener(
            'change',
            function() {
                nuevaFecha.value = '';
                nuevoHorario.innerHTML = `
            <option value="">Seleccione...</option>
        `;

                nuevoCupoId.value = '';
                fechasLibresReprogramacion = [];
                btnConfirmarReprogramacion.disabled = true;
                nuevoHorario.disabled = true;

                if (!nuevoProcedimiento.value) {
                    nuevaFecha.disabled = true;
                    nuevasFechasDisponibles.textContent = '';
                    return;
                }

                nuevasFechasDisponibles.textContent =
                    'Consultando fechas disponibles...';

                const url = new URL(
                    @json(route(
                        'procedimientos.citas.fechas-disponibles'
                    ))
                );

                url.searchParams.set(
                    'procedimiento_id',
                    nuevoProcedimiento.value
                );

                fetch(url)
                    .then(function(respuesta) {
                        return respuesta.json();
                    })
                    .then(function(datos) {
                        fechasLibresReprogramacion =
                            datos.fechas_disponibles || [];

                        nuevaFecha.disabled =
                            fechasLibresReprogramacion.length === 0;

                        if (
                            fechasLibresReprogramacion.length === 0
                        ) {
                            nuevasFechasDisponibles.textContent =
                                'No existen fechas con cupos disponibles.';

                            return;
                        }

                        nuevasFechasDisponibles.textContent =
                            'Disponibles: ' +
                            fechasLibresReprogramacion
                            .map(formatearFecha)
                            .join(', ');
                    })
                    .catch(function() {
                        nuevaFecha.disabled = true;

                        nuevasFechasDisponibles.textContent =
                            'No se pudieron consultar las fechas.';
                    });
            }
        );
        nuevaFecha.addEventListener('change', function() {
            nuevoHorario.innerHTML = `
        <option value="">Seleccione...</option>
    `;

            nuevoCupoId.value = '';
            btnConfirmarReprogramacion.disabled = true;

            if (
                !fechasLibresReprogramacion.includes(
                    nuevaFecha.value
                )
            ) {
                nuevoHorario.disabled = true;

                mensajeReprogramacion.innerHTML = `
            <div class="alert alert-warning py-2">
                La fecha seleccionada no tiene cupos libres.
            </div>
        `;

                return;
            }

            mensajeReprogramacion.innerHTML = '';

            const url = new URL(
                @json(route(
                    'procedimientos.citas.cupos-disponibles'
                ))
            );

            url.searchParams.set(
                'procedimiento_id',
                nuevoProcedimiento.value
            );

            url.searchParams.set(
                'fecha',
                nuevaFecha.value
            );

            fetch(url)
                .then(function(respuesta) {
                    return respuesta.json();
                })
                .then(function(datos) {
                    const cuposDisponibles = (
                        datos.cupos || []
                    ).filter(function(cupo) {
                        return cupo.estado === 'DISPONIBLE';
                    });

                    nuevoHorario.innerHTML = `
                <option value="">Seleccione...</option>
            `;

                    cuposDisponibles.forEach(function(cupo) {
                        const opcion =
                            document.createElement('option');

                        opcion.value = cupo.id;

                        opcion.textContent =
                            cupo.hora +
                            ' - ' +
                            cupo.medico;

                        nuevoHorario.appendChild(opcion);
                    });

                    nuevoHorario.disabled =
                        cuposDisponibles.length === 0;
                });
        });

        nuevoHorario.addEventListener('change', function() {
            nuevoCupoId.value = nuevoHorario.value;

            btnConfirmarReprogramacion.disabled = !nuevoHorario.value;
        });

        formReprogramar.addEventListener(
            'submit',
            function(evento) {
                evento.preventDefault();

                if (!nuevoCupoId.value) {
                    return;
                }

                btnConfirmarReprogramacion.disabled = true;

                const textoOriginal =
                    btnConfirmarReprogramacion.innerHTML;

                btnConfirmarReprogramacion.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Reprogramando...
        `;

                const datos = new FormData(formReprogramar);

                fetch(formReprogramar.action, {
                        method: 'POST',
                        body: datos,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(async function(respuesta) {
                        const resultado = await respuesta.json();

                        if (!respuesta.ok) {
                            let mensaje =
                                resultado.message ||
                                'No se pudo reprogramar la cita.';

                            if (resultado.errors) {
                                mensaje = Object.values(
                                    resultado.errors
                                ).flat().join(' ');
                            }

                            throw new Error(mensaje);
                        }

                        return resultado;
                    })
                    .then(function(resultado) {
                        $('#modalReprogramar').modal('hide');

                        cargarCupos();

                        setTimeout(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cita reprogramada',
                                text: resultado.message ||
                                    'La cita fue reprogramada correctamente.',
                                showCancelButton: true,
                                confirmButtonText: '<i class="fas fa-print mr-1"></i> Imprimir nuevo ticket',
                                cancelButtonText: 'Cerrar',
                                confirmButtonColor: '#007bff',
                                cancelButtonColor: '#6c757d',
                                allowOutsideClick: false,
                                reverseButtons: true
                            }).then(function(respuesta) {
                                const confirmado =
                                    respuesta.isConfirmed === true ||
                                    respuesta.value === true;

                                if (confirmado && resultado.ticket_url) {
                                    const ventanaTicket = window.open(
                                        resultado.ticket_url,
                                        '_blank'
                                    );

                                    if (!ventanaTicket) {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Ventana bloqueada',
                                            text: 'El navegador bloqueó la apertura del ticket. Permita las ventanas emergentes para este sistema.',
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonColor: '#007bff'
                                        });
                                    }
                                }
                            });
                        }, 300);
                    })
                    .catch(function(error) {
                        mensajeReprogramacion.innerHTML = `
        <div class="alert alert-danger py-2">
            ${escapar(error.message)}
        </div>
    `;

                        btnConfirmarReprogramacion.disabled = false;
                    })
                    .finally(function() {
                        btnConfirmarReprogramacion.innerHTML =
                            textoOriginal;
                    });
            }
        );

        btnBuscarCuenta.addEventListener('click', buscarCuenta);

        numeroCuenta.addEventListener('keydown', function(evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                buscarCuenta();
            }
        });

        numeroCuenta.addEventListener('input', function() {
            datosPaciente.style.display = 'none';
            btnGuardarCita.disabled = true;
            mensajeBusqueda.innerHTML = '';
        });

        function buscarCuenta() {
            const cuenta = numeroCuenta.value.trim();

            if (!cuenta) {
                mensajeBusqueda.innerHTML = `
            <div class="alert alert-warning py-2 mb-0">
                Ingrese el número de cuenta.
            </div>
        `;

                return;
            }

            btnBuscarCuenta.disabled = true;
            btnGuardarCita.disabled = true;
            datosPaciente.style.display = 'none';

            mensajeBusqueda.innerHTML = `
        <div class="alert alert-info py-2 mb-0">
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Buscando información en SIGH...
        </div>
    `;

            const url = rutaBuscarCuenta.replace(
                'CUENTA_REEMPLAZAR',
                encodeURIComponent(cuenta)
            );

            fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(async function(respuesta) {
                    const resultado = await respuesta.json();

                    if (!respuesta.ok || !resultado.success) {
                        throw new Error(
                            resultado.message ||
                            'No se encontró la cuenta.'
                        );
                    }

                    return resultado;
                })
                .then(function(resultado) {
                    const paciente = resultado.data;

                    document.getElementById('datoPaciente')
                        .textContent =
                        paciente.paciente || '—';

                    document.getElementById('datoHistoria')
                        .textContent =
                        paciente.historia_clinica || '—';

                    document.getElementById('datoEdad')
                        .textContent = [
                            paciente.edad,
                            paciente.tipo_edad
                        ].filter(Boolean).join(' ') || '—';

                    document.getElementById('datoTelefono')
                        .textContent =
                        paciente.telefono || '—';

                    document.getElementById('datoTipoServicio')
                        .textContent =
                        paciente.tipo_servicio || '—';

                    document.getElementById('datoServicio')
                        .textContent =
                        paciente.servicio_origen || '—';

                    document.getElementById('datoFinanciamiento')
                        .textContent =
                        paciente.financiamiento || '—';

                    datosPaciente.style.display = 'block';
                    btnGuardarCita.disabled = false;

                    mensajeBusqueda.innerHTML = `
                <div class="alert alert-success py-2 mb-0">
                    <i class="fas fa-check-circle mr-1"></i>
                    Paciente encontrado correctamente.
                </div>
            `;
                })
                .catch(function(error) {
                    mensajeBusqueda.innerHTML = `
                <div class="alert alert-danger py-2 mb-0">
                    ${escapar(error.message)}
                </div>
            `;
                })
                .finally(function() {
                    btnBuscarCuenta.disabled = false;
                });
        }
        formAgregarPaciente.addEventListener(
            'submit',
            function(evento) {
                evento.preventDefault();

                if (btnGuardarCita.disabled) {
                    return;
                }

                const ventanaTicket = window.open('', '_blank');

                btnGuardarCita.disabled = true;

                btnGuardarCita.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Guardando...
        `;

                const datos = new FormData(formAgregarPaciente);

                fetch(formAgregarPaciente.action, {
                        method: 'POST',
                        body: datos,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(async function(respuesta) {
                        const resultado = await respuesta.json();

                        if (!respuesta.ok) {
                            let mensaje =
                                resultado.message ||
                                'No se pudo registrar la cita.';

                            if (resultado.errors) {
                                mensaje = Object.values(
                                    resultado.errors
                                ).flat().join(' ');
                            }

                            throw new Error(mensaje);
                        }

                        return resultado;
                    })
                    .then(function(resultado) {
                        citaConfirmada = true;
                        cupoTemporalId = null;
                        bloqueoToken.value = '';

                        $('#modalAgregarPaciente').modal('hide');

                        formAgregarPaciente.reset();
                        datosPaciente.style.display = 'none';

                        cargarCupos();

                        if (ventanaTicket && resultado.ticket_url) {
                            ventanaTicket.location.href =
                                resultado.ticket_url;
                        } else if (resultado.ticket_url) {
                            window.location.href = resultado.ticket_url;
                        }

                        setTimeout(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cita registrada',
                                text: resultado.message ||
                                    'La cita fue registrada correctamente.',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#007bff',
                                allowOutsideClick: false
                            });
                        }, 300);
                    })
                    .catch(function(error) {
                        if (ventanaTicket) {
                            ventanaTicket.close();
                        }

                        mensajeBusqueda.innerHTML = `
        <div class="alert alert-danger py-2 mb-0">
            ${escapar(error.message)}
        </div>
    `;

                        btnGuardarCita.disabled = false;
                    })
                    .finally(function() {
                        btnGuardarCita.innerHTML = `
                    <i class="fas fa-save mr-1"></i>
                    Confirmar cita
                `;
                    });
            }
        );

        procedimiento.addEventListener('change', function() {

            limpiarListado();

            fecha.value = '';
            fechasDisponibles = [];
            fechasProgramadas = [];

            if (!procedimiento.value) {
                fecha.disabled = true;

                mensajeFechas.textContent =
                    'Primero seleccione un procedimiento.';

                return;
            }

            fecha.disabled = true;

            mensajeFechas.textContent =
                'Consultando fechas disponibles...';

            const url = new URL(
                @json(route(
                    'procedimientos.citas.fechas-disponibles'
                ))
            );

            url.searchParams.set(
                'procedimiento_id',
                procedimiento.value
            );

            fetch(url)
                .then(function(respuesta) {
                    if (!respuesta.ok) {
                        throw new Error(
                            'No se pudieron consultar las fechas.'
                        );
                    }

                    return respuesta.json();
                })
                .then(function(datos) {
                    fechasDisponibles =
                        datos.fechas_disponibles || [];

                    fechasProgramadas =
                        datos.fechas_programadas || [];

                    /*
                     * El calendario se habilita si existe
                     * alguna fecha programada, aunque esté llena.
                     */
                    fecha.disabled =
                        fechasProgramadas.length === 0;

                    if (fechasProgramadas.length === 0) {
                        mensajeFechas.textContent =
                            'Este procedimiento no tiene fechas programadas.';

                        return;
                    }

                    if (fechasDisponibles.length === 0) {
                        mensajeFechas.textContent =
                            'No existen fechas con cupos libres. Puede consultar una fecha programada en el calendario.';

                        return;
                    }

                    mensajeFechas.textContent =
                        'Fechas disponibles: ' +
                        fechasDisponibles
                        .map(formatearFecha)
                        .join(', ');
                })
                .catch(function(error) {
                    fecha.disabled = true;

                    mensajeFechas.textContent = error.message;
                });
        });

        fecha.addEventListener('change', function() {

            limpiarListado();

            if (
                !procedimiento.value ||
                !fecha.value
            ) {
                return;
            }

            if (!fechasProgramadas.includes(fecha.value)) {
                mostrarMensaje(
                    'La fecha seleccionada no tiene programación registrada.'
                );

                return;
            }

            cargarCupos();
        });

        function cargarCupos(silencioso = false) {
            if (!silencioso) {
                panelInicial.innerHTML = `
            <div>
                <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                <div>Consultando cupos...</div>
            </div>
        `;
            }

            const url = new URL(
                @json(route(
                    'procedimientos.citas.cupos-disponibles'
                ))
            );

            url.searchParams.set(
                'procedimiento_id',
                procedimiento.value
            );

            url.searchParams.set(
                'fecha',
                fecha.value
            );

            fetch(url)
                .then(function(respuesta) {
                    if (!respuesta.ok) {
                        throw new Error(
                            'No se pudieron consultar los cupos.'
                        );
                    }

                    return respuesta.json();
                })
                .then(function(datos) {
                    mostrarCupos(datos.cupos || []);
                })
                .catch(function(error) {
                    mostrarMensaje(error.message);
                });
        }

        function mostrarCupos(cupos) {
            cuerpoCupos.innerHTML = '';

            if (cupos.length === 0) {
                mostrarMensaje(
                    'No existen cupos para la fecha seleccionada.'
                );

                return;
            }

            cupos.forEach(function(cupo) {
                const fila = document.createElement('tr');

                fila.innerHTML = `
                <td class="align-middle">
                    <span class="hora-cupo">
                        ${escapar(cupo.hora)}
                    </span>
                </td>

                <td class="align-middle">
                    <div class="font-weight-bold">
                        ${escapar(cupo.medico)}
                    </div>

                    <small class="text-muted">
                        ${escapar(cupo.especialidad || '')}
                    </small>
                </td>

                <td class="text-center align-middle">
                    ${crearEstado(cupo.estado)}
                </td>

                <td class="align-middle">
                    ${crearPaciente(cupo)}
                </td>

                <td class="text-center align-middle">
                    ${crearAccion(cupo)}
                </td>
            `;

                cuerpoCupos.appendChild(fila);
            });

            actualizarResumen(cupos);

            panelInicial.style.display = 'none';
            contenedorTabla.style.display = 'block';
            resumenCupos.style.display = 'block';
        }

        function crearEstado(estado) {
            if (estado === 'DISPONIBLE') {
                return `
                <span class="badge badge-success estado-cupo">
                    DISPONIBLE
                </span>
            `;
            }

            if (estado === 'RESERVADO') {
                return `
                <span class="badge badge-primary estado-cupo">
                    RESERVADO
                </span>
            `;
            }

            if (estado === 'EN_PROCESO') {
                return `
                <span class="badge badge-warning estado-cupo">
                    <i class="fas fa-user-clock mr-1"></i>
                    EN PROCESO
                </span>
            `;
            }

            return `
            <span class="badge badge-danger estado-cupo">
                BLOQUEADO
            </span>
        `;
        }

        function crearPaciente(cupo) {
            if (!cupo.cita) {
                if (cupo.estado === 'EN_PROCESO') {
                    return `
                    <small class="text-warning font-weight-bold">
                        Otro usuario está registrando un paciente
                    </small>
                `;
                }

                if (
                    cupo.estado === 'BLOQUEADO' &&
                    cupo.motivo_bloqueo
                ) {
                    return `
                    <small class="text-danger">
                        ${escapar(cupo.motivo_bloqueo)}
                    </small>
                `;
                }

                return '<span class="text-muted">—</span>';
            }

            return `
            <div class="font-weight-bold">
                ${escapar(cupo.cita.paciente)}
            </div>

            <small class="text-muted">
                Cuenta:
                ${escapar(cupo.cita.numero_cuenta || '')}

                · HC:
                ${escapar(
                    cupo.cita.historia_clinica || ''
                )}
            </small>
        `;
        }

        function crearAccion(cupo) {
            /*
             * Cupo disponible:
             * permite registrar un paciente.
             */
            if (cupo.estado === 'DISPONIBLE') {
                return `
            <button
                type="button"
                class="btn btn-success btn-sm btn-agregar-paciente"
                data-cupo-id="${cupo.id}"
                data-hora="${escapar(cupo.hora)}"
            >
                <i class="fas fa-user-plus mr-1"></i>
                Agregar paciente
            </button>
        `;
            }

            if (cupo.estado === 'EN_PROCESO') {
                return `
                <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    disabled
                >
                    <i class="fas fa-user-clock mr-1"></i>
                    En proceso
                </button>
            `;
            }

            /*
             * Cupo reservado:
             * permite imprimir y reprogramar.
             */
            if (
                cupo.estado === 'RESERVADO' &&
                cupo.cita &&
                cupo.cita.id
            ) {
                const urlTicket = rutaTicket.replace(
                    'CITA_REEMPLAZAR',
                    cupo.cita.id
                );

                return `
            <div class="btn-group">
                <a
                    href="${escapar(urlTicket)}"
                    target="_blank"
                    class="btn btn-info btn-sm"
                    title="Abrir ticket"
                >
                    <i class="fas fa-print mr-1"></i>
                    Ver ticket
                </a>

                <button
                    type="button"
                    class="btn btn-warning btn-sm btn-reprogramar"
                    data-cita-id="${cupo.cita.id}"
                    data-cupo-id="${cupo.id}"
                    data-paciente="${escapar(
                        cupo.cita.paciente || ''
                    )}"
                    data-hora="${escapar(cupo.hora)}"
                    title="Reprogramar cita"
                >
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Reprogramar
                </button>
            </div>
        `;
            }

            /*
             * Estado reservado, pero sin información
             * de la cita relacionada.
             */
            if (cupo.estado === 'RESERVADO') {
                return `
            <button
                type="button"
                class="btn btn-info btn-sm"
                disabled
            >
                <i class="fas fa-lock mr-1"></i>
                Reservado
            </button>
        `;
            }

            /*
             * Cupo bloqueado u otro estado.
             */
            return `
        <button
            type="button"
            class="btn btn-secondary btn-sm"
            disabled
        >
            <i class="fas fa-ban mr-1"></i>
            No disponible
        </button>
    `;
        }

        function actualizarResumen(cupos) {

            document.getElementById('totalDisponibles')
                .textContent = cupos.filter(function(cupo) {
                    return cupo.estado === 'DISPONIBLE';
                }).length;

            document.getElementById('totalReservados')
                .textContent = cupos.filter(function(cupo) {
                    return cupo.estado === 'RESERVADO';
                }).length;

            document.getElementById('totalEnProceso')
                .textContent = cupos.filter(function(cupo) {
                    return cupo.estado === 'EN_PROCESO';
                }).length;

            document.getElementById('totalBloqueados')
                .textContent = cupos.filter(function(cupo) {
                    return cupo.estado === 'BLOQUEADO';
                }).length;
        }

        function limpiarListado() {
            cuerpoCupos.innerHTML = '';

            resumenCupos.style.display = 'none';
            contenedorTabla.style.display = 'none';
            panelInicial.style.display = 'flex';

            panelInicial.innerHTML = `
            <div>
                <i class="far fa-calendar-alt fa-4x mb-3"></i>

                <h5>
                    Seleccione un procedimiento y una fecha
                </h5>

                <p class="mb-0">
                    Aquí aparecerá la lista completa de horarios.
                </p>
            </div>
        `;
        }

        function mostrarMensaje(mensaje) {
            resumenCupos.style.display = 'none';
            contenedorTabla.style.display = 'none';
            panelInicial.style.display = 'flex';

            panelInicial.innerHTML = `
            <div>
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <div>${escapar(mensaje)}</div>
            </div>
        `;
        }

        async function liberarCupoTemporal() {
            if (
                !cupoTemporalId ||
                !bloqueoToken.value ||
                citaConfirmada
            ) {
                return;
            }

            const cupoId = cupoTemporalId;
            const token = bloqueoToken.value;

            cupoTemporalId = null;
            bloqueoToken.value = '';
            modalCupoId.value = '';

            const url = rutaLiberarCupo.replace(
                'CUPO_REEMPLAZAR',
                cupoId
            );

            const datos = new FormData();
            datos.append('_token', @json(csrf_token()));
            datos.append('bloqueo_token', token);

            try {
                await fetch(url, {
                    method: 'POST',
                    body: datos,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.error(
                    'No se pudo liberar el cupo:',
                    error
                );
            }

            if (procedimiento.value && fecha.value) {
                cargarCupos(true);
            }
        }

        function formatearFecha(valor) {
            const partes = valor.split('-');

            if (partes.length !== 3) {
                return valor;
            }

            return partes[2] + '/' + partes[1] + '/' + partes[0];
        }

        function escapar(valor) {
            const elemento = document.createElement('div');

            elemento.textContent = valor == null ?
                '' :
                String(valor);

            return elemento.innerHTML;
        }
        $('#modalAgregarPaciente').on(
            'hidden.bs.modal',
            function() {
                if (!citaConfirmada) {
                    liberarCupoTemporal();
                }

                citaConfirmada = false;
                formAgregarPaciente.reset();
                modalCupoId.value = '';
                bloqueoToken.value = '';
                mensajeBusqueda.innerHTML = '';
                datosPaciente.style.display = 'none';
                btnGuardarCita.disabled = true;
            }
        );

        setInterval(function() {
            if (
                procedimiento.value &&
                fecha.value &&
                !document.hidden
            ) {
                cargarCupos(true);
            }
        }, 7000);

    });
</script>
@stop