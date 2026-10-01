@extends('adminlte::page')

@section('title', 'Nueva solicitud quirúrgica')

@section('content_header')
<div>
    <h1 class="h4 mb-0">
        <i class="fas fa-file-medical mr-2"></i>
        Nueva solicitud quirúrgica
    </h1>

    <small class="text-muted">
        Solicitud de programación para Sala de Operaciones
    </small>
</div>
@stop

@section('content')
<link
    rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">

@if (session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <strong>
        No se pudo registrar la solicitud:
    </strong>

    <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form
    method="POST"
    action="{{ route('quirofano.solicitudes.store') }}"
    id="formSolicitud">
    @csrf

    {{-- Búsqueda de cuenta --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-1"></i>
                1. Buscar atención
            </h3>
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-md-0">
                        <label>
                            Número de cuenta
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="text"
                                id="numero_cuenta"
                                name="numero_cuenta"
                                class="form-control"
                                value="{{ old('numero_cuenta') }}"
                                maxlength="30"
                                autocomplete="off"
                                required>

                            <div class="input-group-append">
                                <button
                                    type="button"
                                    id="btnBuscarCuenta"
                                    class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div
                        id="mensajeCuenta"
                        class="alert mb-0 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Datos del paciente --}}
    <div
        class="card card-outline card-info"
        id="cardPaciente">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-injured mr-1"></i>
                2. Datos del paciente
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Paciente</label>

                        <input
                            type="text"
                            id="paciente"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Historia clínica</label>

                        <input
                            type="text"
                            id="historia_clinica"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>DNI</label>

                        <input
                            type="text"
                            id="dni"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Sexo</label>

                        <input
                            type="text"
                            id="sexo"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de nacimiento</label>

                        <input
                            type="date"
                            id="fecha_nacimiento"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Edad</label>

                        <input
                            type="text"
                            id="edad"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Cama</label>

                        <input
                            type="text"
                            id="cama"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Servicio actual</label>

                        <input
                            type="text"
                            id="servicio_actual"
                            class="form-control"
                            readonly>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>
                            Servicio solicitante
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="servicio_solicitante"
                            name="servicio_solicitante"
                            class="form-control"
                            value="{{ old('servicio_solicitante') }}"
                            maxlength="150"
                            required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Financiamiento</label>

                        <input
                            type="text"
                            id="tipo_seguro"
                            class="form-control"
                            readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Médico, diagnóstico y procedimiento --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-md mr-1"></i>
                3. Información de la operación
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            DNI del médico
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="text"
                                id="medico_codigo"
                                name="medico_codigo"
                                class="form-control"
                                value="{{ old('medico_codigo') }}"
                                maxlength="20"
                                autocomplete="off"
                                placeholder="Ingrese DNI"
                                required>

                            <div class="input-group-append">
                                <button
                                    type="button"
                                    id="btnBuscarMedico"
                                    class="btn btn-primary"
                                    title="Buscar médico">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Médico cirujano</label>

                        <input
                            type="text"
                            id="medico_nombre"
                            name="medico_nombre"
                            class="form-control"
                            value="{{ old('medico_nombre') }}"
                            placeholder="Se cargará automáticamente"
                            readonly
                            required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Especialidad</label>

                        <input
                            type="text"
                            id="medico_especialidad"
                            name="medico_especialidad"
                            class="form-control"
                            value="{{ old('medico_especialidad') }}"
                            placeholder="Se cargará automáticamente"
                            readonly>
                    </div>
                </div>

                <div class="col-12">
                    <div
                        id="mensajeMedico"
                        class="alert d-none py-2"></div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            Código CIE-10
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="text"
                                id="diagnostico_codigo"
                                name="diagnostico_codigo"
                                class="form-control text-uppercase"
                                value="{{ old('diagnostico_codigo') }}"
                                maxlength="30"
                                autocomplete="off"
                                placeholder="Ejemplo: K35.8"
                                required>

                            <div class="input-group-append">
                                <button
                                    type="button"
                                    id="btnBuscarDiagnostico"
                                    class="btn btn-primary"
                                    title="Buscar diagnóstico">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="form-group">
                        <label>Diagnóstico preoperatorio</label>

                        <input
                            type="text"
                            id="diagnostico_nombre"
                            name="diagnostico_nombre"
                            class="form-control"
                            value="{{ old('diagnostico_nombre') }}"
                            placeholder="Se cargará automáticamente"
                            readonly
                            required>
                    </div>
                </div>

                <div class="col-12">
                    <div
                        id="mensajeDiagnostico"
                        class="alert d-none py-2"></div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            Código del procedimiento
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="text"
                                id="procedimiento_codigo"
                                name="procedimiento_codigo"
                                class="form-control text-uppercase"
                                value="{{ old('procedimiento_codigo') }}"
                                maxlength="30"
                                autocomplete="off"
                                placeholder="Ingrese código SIS"
                                required>

                            <div class="input-group-append">
                                <button
                                    type="button"
                                    id="btnBuscarProcedimiento"
                                    class="btn btn-primary"
                                    title="Buscar procedimiento">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="form-group">
                        <label>Procedimiento u operación</label>

                        <input
                            type="text"
                            id="procedimiento_nombre"
                            name="procedimiento_nombre"
                            class="form-control"
                            value="{{ old('procedimiento_nombre') }}"
                            placeholder="Se cargará automáticamente"
                            readonly
                            required>
                    </div>
                </div>

                <div class="col-12">
                    <div
                        id="mensajeProcedimiento"
                        class="alert d-none py-2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Datos complementarios --}}
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list mr-1"></i>
                4. Datos complementarios
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de solicitud</label>

                        <select
                            name="tipo_solicitud"
                            class="form-control"
                            required>
                            <option
                                value="ELECTIVA"
                                @selected(
                                old('tipo_solicitud')==='ELECTIVA'
                                )>
                                Electiva
                            </option>

                            <option
                                value="URGENCIA"
                                @selected(
                                old('tipo_solicitud')==='URGENCIA'
                                )>
                                Urgencia
                            </option>

                            <option
                                value="EMERGENCIA"
                                @selected(
                                old('tipo_solicitud')==='EMERGENCIA'
                                )>
                                Emergencia
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Prioridad</label>

                        <select
                            name="prioridad"
                            class="form-control"
                            required>
                            @foreach (
                            ['BAJA', 'MEDIA', 'ALTA', 'CRITICA']
                            as $prioridad
                            )
                            <option
                                value="{{ $prioridad }}"
                                @selected(
                                old( 'prioridad' , 'MEDIA'
                                )===$prioridad
                                )>
                                {{ $prioridad }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha sugerida</label>

                        <input
                            type="date"
                            name="fecha_sugerida"
                            class="form-control"
                            min="{{ now()->toDateString() }}"
                            value="{{ old('fecha_sugerida') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            Duración estimada
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                name="duracion_estimada_minutos"
                                class="form-control"
                                value="{{ old(
                    'duracion_estimada_minutos',
                    60
                ) }}"
                                min="15"
                                max="720"
                                step="5"
                                required>

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    minutos
                                </span>
                            </div>
                        </div>

                        <small class="form-text text-muted">
                            Tiempo aproximado indicado por el médico.
                        </small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de anestesia</label>

                        <select
                            name="tipo_anestesia"
                            class="form-control">
                            <option value="">
                                Seleccione
                            </option>
                            <option value="GENERAL">
                                General
                            </option>
                            <option value="REGIONAL">
                                Regional
                            </option>
                            <option value="LOCAL">
                                Local
                            </option>
                            <option value="SEDACION">
                                Sedación
                            </option>
                            <option value="POR DEFINIR">
                                Por definir
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group">
                        <label>Requerimientos especiales</label>

                        <textarea
                            name="requerimientos_especiales"
                            class="form-control"
                            rows="2">{{ old('requerimientos_especiales') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group mb-0">
                        <label>Observaciones</label>

                        <textarea
                            name="observaciones"
                            class="form-control"
                            rows="2">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right pb-4">
        <button
            type="reset"
            class="btn btn-secondary">
            <i class="fas fa-eraser mr-1"></i>
            Limpiar
        </button>

        <button
            type="submit"
            id="btnGuardar"
            class="btn btn-primary"
            disabled>
            <i class="fas fa-paper-plane mr-1"></i>
            Enviar solicitud
        </button>
    </div>
</form>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cuenta = document.getElementById('numero_cuenta');
        const botonBuscar = document.getElementById('btnBuscarCuenta');
        const botonGuardar = document.getElementById('btnGuardar');
        const mensaje = document.getElementById('mensajeCuenta');

        function mostrarMensaje(tipo, texto) {
            mensaje.className = 'alert alert-' + tipo + ' mb-0';
            mensaje.textContent = texto;
        }
        const procedimientoCodigo =
            document.getElementById('procedimiento_codigo');

        const procedimientoNombre =
            document.getElementById('procedimiento_nombre');

        const btnBuscarProcedimiento =
            document.getElementById('btnBuscarProcedimiento');

        const mensajeProcedimiento =
            document.getElementById('mensajeProcedimiento');

        function mostrarMensajeProcedimiento(tipo, texto) {
            mensajeProcedimiento.className =
                'alert alert-' + tipo + ' py-2';

            mensajeProcedimiento.textContent = texto;
        }

        function limpiarProcedimiento() {
            procedimientoNombre.value = '';
        }

        async function buscarProcedimiento() {
            const codigo = procedimientoCodigo.value
                .trim()
                .toUpperCase();

            limpiarProcedimiento();

            if (!codigo) {
                mostrarMensajeProcedimiento(
                    'warning',
                    'Ingrese el código del procedimiento.'
                );

                procedimientoCodigo.focus();
                return;
            }

            procedimientoCodigo.value = codigo;

            btnBuscarProcedimiento.disabled = true;
            btnBuscarProcedimiento.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i>';

            mostrarMensajeProcedimiento(
                'info',
                'Consultando procedimiento...'
            );

            const plantillaProcedimiento = @json(
                route(
                    'quirofano.solicitudes.buscar-procedimiento',
                    ['codigo' => '__CODIGO__']
                )
            );

            const url = plantillaProcedimiento.replace(
                '__CODIGO__',
                encodeURIComponent(codigo)
            );

            try {
                const respuesta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const resultado = await respuesta.json();

                if (!respuesta.ok || !resultado.success) {
                    throw new Error(
                        resultado.message ||
                        'No se encontró el procedimiento.'
                    );
                }

                procedimientoCodigo.value =
                    resultado.data.codigo || codigo;

                procedimientoNombre.value =
                    resultado.data.procedimiento || '';

                mostrarMensajeProcedimiento(
                    'success',
                    'Procedimiento encontrado correctamente.'
                );
            } catch (error) {
                limpiarProcedimiento();

                mostrarMensajeProcedimiento(
                    'danger',
                    error.message
                );
            } finally {
                btnBuscarProcedimiento.disabled = false;
                btnBuscarProcedimiento.innerHTML =
                    '<i class="fas fa-search"></i>';
            }
        }

        btnBuscarProcedimiento.addEventListener(
            'click',
            buscarProcedimiento
        );

        procedimientoCodigo.addEventListener(
            'keydown',
            function(evento) {
                if (evento.key === 'Enter') {
                    evento.preventDefault();
                    buscarProcedimiento();
                }
            }
        );

        procedimientoCodigo.addEventListener(
            'input',
            function() {
                procedimientoCodigo.value =
                    procedimientoCodigo.value.toUpperCase();

                limpiarProcedimiento();

                mensajeProcedimiento.className =
                    'alert d-none py-2';

                mensajeProcedimiento.textContent = '';
            }
        );

        const diagnosticoCodigo =
            document.getElementById('diagnostico_codigo');

        const diagnosticoNombre =
            document.getElementById('diagnostico_nombre');

        const btnBuscarDiagnostico =
            document.getElementById('btnBuscarDiagnostico');

        const mensajeDiagnostico =
            document.getElementById('mensajeDiagnostico');

        function mostrarMensajeDiagnostico(tipo, texto) {
            mensajeDiagnostico.className =
                'alert alert-' + tipo + ' py-2';

            mensajeDiagnostico.textContent = texto;
        }

        function limpiarDiagnostico() {
            diagnosticoNombre.value = '';
        }

        async function buscarDiagnostico() {
            const codigo = diagnosticoCodigo.value
                .trim()
                .toUpperCase();

            limpiarDiagnostico();

            if (!codigo) {
                mostrarMensajeDiagnostico(
                    'warning',
                    'Ingrese el código CIE-10.'
                );

                diagnosticoCodigo.focus();
                return;
            }

            diagnosticoCodigo.value = codigo;

            btnBuscarDiagnostico.disabled = true;
            btnBuscarDiagnostico.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i>';

            mostrarMensajeDiagnostico(
                'info',
                'Consultando diagnóstico...'
            );

            const plantillaDiagnostico = @json(
                route(
                    'quirofano.solicitudes.buscar-diagnostico',
                    ['codigo' => '__CODIGO__']
                )
            );

            const url = plantillaDiagnostico.replace(
                '__CODIGO__',
                encodeURIComponent(codigo)
            );

            try {
                const respuesta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const resultado = await respuesta.json();

                if (!respuesta.ok || !resultado.success) {
                    throw new Error(
                        resultado.message ||
                        'No se encontró el diagnóstico.'
                    );
                }

                diagnosticoCodigo.value =
                    resultado.data.codigo || codigo;

                diagnosticoNombre.value =
                    resultado.data.diagnostico || '';

                mostrarMensajeDiagnostico(
                    'success',
                    'Diagnóstico encontrado correctamente.'
                );
            } catch (error) {
                limpiarDiagnostico();

                mostrarMensajeDiagnostico(
                    'danger',
                    error.message
                );
            } finally {
                btnBuscarDiagnostico.disabled = false;
                btnBuscarDiagnostico.innerHTML =
                    '<i class="fas fa-search"></i>';
            }
        }

        btnBuscarDiagnostico.addEventListener(
            'click',
            buscarDiagnostico
        );

        diagnosticoCodigo.addEventListener(
            'keydown',
            function(evento) {
                if (evento.key === 'Enter') {
                    evento.preventDefault();
                    buscarDiagnostico();
                }
            }
        );

        diagnosticoCodigo.addEventListener(
            'input',
            function() {
                diagnosticoCodigo.value =
                    diagnosticoCodigo.value.toUpperCase();

                limpiarDiagnostico();

                mensajeDiagnostico.className =
                    'alert d-none py-2';

                mensajeDiagnostico.textContent = '';
            }
        );
        const medicoCodigo =
            document.getElementById('medico_codigo');

        const medicoNombre =
            document.getElementById('medico_nombre');

        const medicoEspecialidad =
            document.getElementById('medico_especialidad');

        const btnBuscarMedico =
            document.getElementById('btnBuscarMedico');

        const mensajeMedico =
            document.getElementById('mensajeMedico');

        function mostrarMensajeMedico(tipo, texto) {
            mensajeMedico.className =
                'alert alert-' + tipo + ' py-2';

            mensajeMedico.textContent = texto;
        }

        function limpiarMedico() {
            medicoNombre.value = '';
            medicoEspecialidad.value = '';
        }

        async function buscarMedico() {
            const dni = medicoCodigo.value.trim();

            limpiarMedico();

            if (!dni) {
                mostrarMensajeMedico(
                    'warning',
                    'Ingrese el DNI del médico.'
                );

                medicoCodigo.focus();
                return;
            }

            btnBuscarMedico.disabled = true;
            btnBuscarMedico.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i>';

            mostrarMensajeMedico(
                'info',
                'Consultando datos del médico...'
            );

            const plantillaMedico = @json(
                route(
                    'quirofano.solicitudes.buscar-medico',
                    ['dni' => '__DNI__']
                )
            );

            const url = plantillaMedico.replace(
                '__DNI__',
                encodeURIComponent(dni)
            );

            try {
                const respuesta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const resultado = await respuesta.json();

                if (!respuesta.ok || !resultado.success) {
                    throw new Error(
                        resultado.message ||
                        'No se encontró el médico.'
                    );
                }

                medicoCodigo.value =
                    resultado.data.codigo || dni;

                medicoNombre.value =
                    resultado.data.nombre || '';

                medicoEspecialidad.value =
                    resultado.data.especialidad || '';

                mostrarMensajeMedico(
                    'success',
                    'Médico encontrado correctamente.'
                );
            } catch (error) {
                limpiarMedico();

                mostrarMensajeMedico(
                    'danger',
                    error.message
                );
            } finally {
                btnBuscarMedico.disabled = false;
                btnBuscarMedico.innerHTML =
                    '<i class="fas fa-search"></i>';
            }
        }

        btnBuscarMedico.addEventListener(
            'click',
            buscarMedico
        );

        medicoCodigo.addEventListener(
            'keydown',
            function(evento) {
                if (evento.key === 'Enter') {
                    evento.preventDefault();
                    buscarMedico();
                }
            }
        );

        medicoCodigo.addEventListener(
            'input',
            function() {
                limpiarMedico();

                mensajeMedico.className =
                    'alert d-none py-2';

                mensajeMedico.textContent = '';
            }
        );

        function limpiarPaciente() {
            [
                'paciente',
                'historia_clinica',
                'dni',
                'sexo',
                'fecha_nacimiento',
                'edad',
                'cama',
                'servicio_actual',
                'tipo_seguro'
            ].forEach(function(id) {
                document.getElementById(id).value = '';
            });

            botonGuardar.disabled = true;
        }

        async function buscarCuenta() {
            const numeroCuenta = cuenta.value.trim();

            limpiarPaciente();

            if (!numeroCuenta) {
                mostrarMensaje(
                    'warning',
                    'Ingrese el número de cuenta.'
                );

                cuenta.focus();
                return;
            }

            botonBuscar.disabled = true;
            botonBuscar.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-1"></i> Buscando';

            mostrarMensaje(
                'info',
                'Consultando información en SIGH...'
            );

            const plantillaUrl = @json(
                route(
                    'quirofano.solicitudes.buscar-cuenta',
                    ['cuenta' => '__CUENTA__']
                )
            );

            const url = plantillaUrl.replace(
                '__CUENTA__',
                encodeURIComponent(numeroCuenta)
            );

            try {
                const respuesta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const resultado = await respuesta.json();

                if (!respuesta.ok || !resultado.success) {
                    throw new Error(
                        resultado.message ||
                        'No se pudo consultar la cuenta.'
                    );
                }

                const datos = resultado.data;

                document.getElementById('paciente').value =
                    datos.paciente || '';

                document.getElementById('historia_clinica').value =
                    datos.historia_clinica || '';

                document.getElementById('dni').value =
                    datos.dni || '';

                document.getElementById('sexo').value =
                    datos.sexo || '';

                document.getElementById('fecha_nacimiento').value =
                    datos.fecha_nacimiento || '';

                document.getElementById('edad').value =
                    datos.edad ?
                    datos.edad + ' ' + (datos.tipo_edad || '') :
                    '';

                document.getElementById('cama').value =
                    datos.cama || '';

                document.getElementById('servicio_actual').value =
                    datos.servicio_actual || '';

                document.getElementById('tipo_seguro').value =
                    datos.tipo_seguro || '';

                document.getElementById('servicio_solicitante').value =
                    datos.servicio_actual || '';

                cuenta.value = datos.numero_cuenta || numeroCuenta;

                mostrarMensaje(
                    'success',
                    'Paciente encontrado correctamente.'
                );

                botonGuardar.disabled = false;
            } catch (error) {
                mostrarMensaje('danger', error.message);
                limpiarPaciente();
            } finally {
                botonBuscar.disabled = false;
                botonBuscar.innerHTML =
                    '<i class="fas fa-search mr-1"></i> Buscar';
            }
        }

        botonBuscar.addEventListener('click', buscarCuenta);

        cuenta.addEventListener('keydown', function(evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                buscarCuenta();
            }
        });

        cuenta.addEventListener('input', function() {
            limpiarPaciente();
            mensaje.className = 'alert mb-0 d-none';
            mensaje.textContent = '';
        });

        document
            .getElementById('formSolicitud')
            .addEventListener('submit', function() {
                botonGuardar.disabled = true;
                botonGuardar.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Enviando';
            });
    });
</script>
@stop