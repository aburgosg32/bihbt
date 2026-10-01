@extends('adminlte::page')

@section('title', 'Censo de Camas')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>
        <h1 class="m-0">
            <i class="fas fa-clipboard-list mr-2"></i>
            Censo de Camas
        </h1>

        <small class="text-muted">
            Estado actual de ocupación por servicio y habitación
        </small>
    </div>

</div>

@stop


@section('content')

{{-- MENSAJES --}}

@if($errors->any())

<div class="alert alert-warning alert-dismissible fade show shadow-sm">

    <button type="button"
        class="close"
        data-dismiss="alert"
        aria-label="Cerrar">

        <span aria-hidden="true">&times;</span>

    </button>

    <i class="fas fa-exclamation-triangle mr-2"></i>

    <strong>No se pudo realizar la asignación.</strong>

    {{ $errors->first() }}

</div>

@endif

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    <i class="fas fa-check-circle mr-2"></i>

    {{ session('success') }}

    <button type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show">

    <i class="fas fa-exclamation-circle mr-2"></i>

    {{ session('error') }}

    <button type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


{{-- TARJETAS RESUMEN --}}

<div class="row">

    <div class="col-lg-4 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>
                    {{ $totalCamas }}
                </h3>

                <p>
                    Total de Camas
                </p>

            </div>

            <div class="icon">
                <i class="fas fa-bed"></i>
            </div>

        </div>

    </div>


    <div class="col-lg-4 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>
                    {{ $ocupadas }}
                </h3>

                <p>
                    Camas Ocupadas
                </p>

            </div>

            <div class="icon">
                <i class="fas fa-user-injured"></i>
            </div>

        </div>

    </div>


    <div class="col-lg-4 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>
                    {{ $libres }}
                </h3>

                <p>
                    Camas Libres
                </p>

            </div>

            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>

        </div>

    </div>

</div>


{{-- FILTROS --}}

<div class="card shadow-sm">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="fas fa-filter mr-2"></i>

            Filtros

        </h5>

    </div>


    <div class="card-body">

        <form method="GET"
            action="{{ route('camas.censo') }}">

            <div class="row">

                {{-- DEPARTAMENTO --}}

                <div class="col-md-3">

                    <div class="form-group">

                        <label>
                            Departamento
                        </label>

                        <select name="departamento_id"
                            id="departamento_id"
                            class="form-control">

                            <option value="">
                                Todos los departamentos
                            </option>

                            @foreach($departamentos as $departamento)

                            <option value="{{ $departamento->id }}"
                                {{ request('departamento_id') == $departamento->id
                                        ? 'selected'
                                        : '' }}>

                                {{ $departamento->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- SERVICIO --}}

                <div class="col-md-3">

                    <div class="form-group">

                        <label>
                            Servicio
                        </label>

                        <select name="servicio_id"
                            id="servicio_id"
                            class="form-control">

                            <option value="">
                                Todos los servicios
                            </option>

                            @foreach($servicios as $servicio)

                            <option value="{{ $servicio->id }}"
                                {{ request('servicio_id') == $servicio->id
                                        ? 'selected'
                                        : '' }}>

                                {{ $servicio->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- BUSCAR --}}

                <div class="col-md-4">

                    <div class="form-group">

                        <label>
                            Buscar
                        </label>

                        <input type="text"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            class="form-control"
                            placeholder="Cama, paciente, historia o teléfono...">

                    </div>

                </div>


                {{-- BOTÓN --}}

                <div class="col-md-2 d-flex align-items-end">

                    <div class="form-group w-100">

                        <button type="submit"
                            class="btn btn-primary btn-block">

                            <i class="fas fa-search mr-1"></i>

                            Buscar

                        </button>

                    </div>

                </div>

            </div>


            <div class="text-right">

                <a href="{{ route('camas.censo') }}"
                    class="btn btn-outline-secondary btn-sm">

                    <i class="fas fa-sync-alt mr-1"></i>

                    Limpiar filtros

                </a>

            </div>

        </form>

    </div>

</div>


{{-- ============================================================= --}}
{{-- CENSO --}}
{{-- ============================================================= --}}

@if($camasAgrupadas->count() > 0)

<div class="row">

    @foreach($camasAgrupadas as $grupo)

    @php

    $primeraCama =
    $grupo->first();

    $departamento =
    $primeraCama
    ->servicio
    ?->departamento
    ?->nombre
    ?? '-';

    $nombreServicio =
    $primeraCama
    ->servicio
    ?->nombre
    ?? '-';

    $totalGrupo =
    $grupo->count();

    $ocupadasGrupo =
    $grupo
    ->filter(
    fn($cama) =>
    $cama->ocupacionActiva !== null
    )
    ->count();

    $libresGrupo =
    $totalGrupo - $ocupadasGrupo;

    @endphp


    {{-- DOS SERVICIOS POR FILA EN PC --}}

    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">

        <div class="card shadow-sm h-100 censo-servicio-card">


            {{-- ================================================= --}}
            {{-- CABECERA --}}
            {{-- ================================================= --}}

            <div class="card-header bg-white censo-servicio-header">

                <div>

                    {{-- SERVICIO PRINCIPAL --}}

                    <div class="censo-servicio-nombre">

                        {{ $nombreServicio }}

                    </div>


                    {{-- DEPARTAMENTO SECUNDARIO --}}

                    <div class="censo-departamento">

                        <i class="fas fa-hospital mr-1"></i>

                        {{ $departamento }}

                    </div>

                </div>


                {{-- RESUMEN DEL SERVICIO --}}

                <div class="censo-contadores">

                    <span class="contador-total">

                        Total:
                        {{ $totalGrupo }}

                    </span>

                    <span class="contador-ocupadas">

                        Ocupadas:
                        {{ $ocupadasGrupo }}

                    </span>

                    <span class="contador-libres">

                        Libres:
                        {{ $libresGrupo }}

                    </span>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- TABLA --}}
            {{-- ================================================= --}}

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-sm mb-0 censo-tabla">

                        <thead>

                            <tr>

                                <th class="col-cama">
                                    Cama
                                </th>

                                <th>
                                    Paciente
                                </th>

                                <th class="col-telefono">
                                    Teléfono
                                </th>

                                <th class="col-estado text-center">
                                    Estado
                                </th>

                                <th class="col-accion text-center">
                                    Acción
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($grupo as $cama)

                            @php

                            $ocupacion =
                            $cama->ocupacionActiva;

                            $paciente =
                            $ocupacion?->paciente;

                            @endphp


                            <tr>


                                {{-- ================================= --}}
                                {{-- CAMA --}}
                                {{-- ================================= --}}

                                <td>

                                    <span class="numero-cama">

                                        {{ $cama->numero }}

                                    </span>

                                </td>



                                {{-- ================================= --}}
                                {{-- PACIENTE --}}
                                {{-- ================================= --}}

                                <td>

                                    @if($paciente)

                                    <div class="nombre-paciente">

                                        {{ $paciente->nombre_completo }}

                                    </div>


                                    @if(
                                    $paciente
                                    ->nro_historia_clinica
                                    )

                                    <div class="historia-paciente">

                                        HC:
                                        {{ $paciente->nro_historia_clinica }}

                                    </div>

                                    @endif


                                    @else

                                    @if(
                                    $cama->estado_operativo ===
                                    'OPERATIVA'
                                    )

                                    <span class="texto-disponible">

                                        DISPONIBLE

                                    </span>

                                    @else

                                    <span class="texto-no-disponible">

                                        {{ str_replace(
                                                        '_',
                                                        ' ',
                                                        $cama->estado_operativo
                                                    ) }}

                                    </span>

                                    @endif

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- TELÉFONO --}}
                                {{-- ================================= --}}

                                <td>

                                    @if(
                                    $paciente &&
                                    $paciente->telefono
                                    )

                                    <i class="fas fa-phone mr-1 text-muted"></i>

                                    {{ $paciente->telefono }}

                                    @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- ESTADO --}}
                                {{-- ================================= --}}

                                <td class="text-center">

                                    @if($ocupacion)

                                    <span class="badge badge-danger">

                                        <i class="fas fa-user mr-1"></i>

                                        OCUPADA

                                    </span>

                                    @else

                                    @if(
                                    $cama->estado_operativo ===
                                    'OPERATIVA'
                                    )

                                    <span class="badge badge-light border text-primary">

                                        <i class="fas fa-check mr-1"></i>

                                        LIBRE

                                    </span>

                                    @else

                                    <span class="badge badge-warning">

                                        {{ str_replace(
                                                        '_',
                                                        ' ',
                                                        $cama->estado_operativo
                                                    ) }}

                                    </span>

                                    @endif

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- ACCIÓN --}}
                                {{-- ================================= --}}

                                <td class="text-center">

                                    @if($ocupacion)

                                    <form method="POST"
                                        action="{{ route(
                                                      'camas.censo.alta',
                                                      $ocupacion->id
                                                  ) }}"
                                        class="d-inline form-alta"
                                        data-paciente="{{ $paciente->nombre_completo }}"
                                        data-cama="{{ $cama->numero }}">

                                        @csrf
                                        @method('PUT')


                                        <button type="submit"
                                            class="btn btn-danger btn-sm">

                                            <i class="fas fa-sign-out-alt mr-1"></i>

                                            Alta

                                        </button>

                                    </form>


                                    @else

                                    @if(
                                    $cama->estado_operativo ===
                                    'OPERATIVA'
                                    )

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        onclick="abrirAsignacion(
                                                            {{ $cama->id }},
                                                            @js($cama->numero),
                                                            @js($nombreServicio),
                                                            ''
                                                        )">

                                        <i class="fas fa-user-plus mr-1"></i>

                                        Asignar

                                    </button>

                                    @else

                                    <button type="button"
                                        class="btn btn-secondary btn-sm"
                                        disabled>

                                        No disponible

                                    </button>

                                    @endif

                                    @endif

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    @endforeach

</div>


@else

<div class="card shadow-sm">

    <div class="card-body text-center py-5">

        <i class="fas fa-bed fa-4x text-muted mb-3"></i>

        <h5 class="text-muted">

            No se encontraron camas

        </h5>

        <p class="text-muted mb-0">

            Verifique los filtros o registre camas
            en la configuración.

        </p>

    </div>

</div>

@endif


{{-- ============================================================= --}}
{{-- MODAL ASIGNAR PACIENTE --}}
{{-- ============================================================= --}}

<div class="modal fade"
    id="modalAsignar"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">

    <div class="modal-dialog modal-lg"
        role="document">

        <div class="modal-content">

            <form method="POST"
                action="{{ route('camas.censo.asignar') }}"
                id="formAsignar">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="fas fa-bed mr-2"></i>

                        Asignar Paciente

                    </h5>


                    <button type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>


                <div class="modal-body">

                    {{-- DATOS OCULTOS --}}

                    <input type="hidden"
                        name="cama_id"
                        id="cama_id">


                    <input type="hidden"
                        name="id_paciente_sigh"
                        id="id_paciente_sigh">


                    <input type="hidden"
                        name="nro_historia_clinica"
                        id="nro_historia_clinica">


                    <input type="hidden"
                        name="nombre_completo"
                        id="nombre_completo">

                    <input type="hidden"
                        name="sexo"
                        id="sexo">

                    <input type="hidden"
                        name="fecha_nacimiento"
                        id="fecha_nacimiento">


                    <input type="hidden"
                        name="telefono"
                        id="telefono">


                    {{-- INFORMACIÓN DE CAMA --}}

                    <div class="alert alert-info">

                        <div class="row">

                            <div class="col-md-4">

                                <strong>
                                    Servicio:
                                </strong>

                                <div id="modal_servicio">
                                    -
                                </div>

                            </div>


                            <div class="col-md-4">

                                <strong>
                                    Habitación / Área:
                                </strong>

                                <div id="modal_habitacion">
                                    -
                                </div>

                            </div>


                            <div class="col-md-4">

                                <strong>
                                    Cama:
                                </strong>

                                <div id="modal_cama"
                                    class="font-weight-bold">

                                    -

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- BUSCAR HISTORIA --}}

                    <div class="form-group">

                        <label>

                            Número de Historia Clínica

                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group">

                            <input type="text"
                                id="historia_busqueda"
                                class="form-control"
                                placeholder="Ingrese historia clínica..."
                                autocomplete="off">


                            <div class="input-group-append">

                                <button type="button"
                                    id="btnBuscarPaciente"
                                    class="btn btn-primary">

                                    <i class="fas fa-search mr-1"></i>

                                    Buscar

                                </button>

                            </div>

                        </div>

                    </div>


                    {{-- BUSCANDO --}}

                    <div id="buscandoPaciente"
                        class="text-center py-3 d-none">

                        <i class="fas fa-spinner fa-spin mr-2"></i>

                        Buscando paciente...

                    </div>


                    {{-- ERROR --}}

                    <div id="errorPaciente"
                        class="alert alert-danger d-none">

                    </div>


                    {{-- RESULTADO PACIENTE --}}

                    <div id="resultadoPaciente"
                        class="card d-none">

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-8">

                                    <small class="text-muted">
                                        PACIENTE
                                    </small>


                                    <h5 id="paciente_nombre"
                                        class="mb-2">

                                    </h5>


                                    <span class="text-muted">
                                        Historia Clínica:
                                    </span>


                                    <strong id="paciente_historia">

                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        TELÉFONO
                                    </small>


                                    <h5 id="paciente_telefono"
                                        class="mb-0">

                                    </h5>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-dismiss="modal">

                        Cancelar

                    </button>


                    <button type="submit"
                        id="btnConfirmarAsignacion"
                        class="btn btn-primary"
                        disabled>

                        <i class="fas fa-check mr-1"></i>

                        Asignar Paciente

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@stop



@section('css')

<link rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">

@stop



@section('js')

<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTO → SERVICIO
            |--------------------------------------------------------------------------
            */

            const departamento =
                document.getElementById(
                    'departamento_id'
                );

            const servicio =
                document.getElementById(
                    'servicio_id'
                );


            if (departamento && servicio) {

                departamento.addEventListener(
                    'change',
                    function() {

                        servicio.innerHTML =
                            '<option value="">Todos los servicios</option>';


                        if (!this.value) {
                            return;
                        }


                        fetch(
                                '{{ url("/camas/departamentos") }}/' +
                                this.value +
                                '/servicios'
                            )
                            .then(response => {

                                if (!response.ok) {
                                    throw new Error(
                                        'No se pudieron cargar los servicios.'
                                    );
                                }

                                return response.json();

                            })
                            .then(data => {

                                data.forEach(item => {

                                    const option =
                                        document.createElement(
                                            'option'
                                        );

                                    option.value =
                                        item.id;

                                    option.textContent =
                                        item.nombre;

                                    servicio.appendChild(
                                        option
                                    );

                                });

                            })
                            .catch(error => {

                                console.error(error);

                            });

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | BUSCAR PACIENTE
            |--------------------------------------------------------------------------
            */

            const btnBuscar =
                document.getElementById(
                    'btnBuscarPaciente'
                );

            const historia =
                document.getElementById(
                    'historia_busqueda'
                );


            if (btnBuscar && historia) {

                btnBuscar.addEventListener(
                    'click',
                    buscarPaciente
                );


                historia.addEventListener(
                    'keydown',
                    function(event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            buscarPaciente();

                        }

                    }
                );

            }


            function buscarPaciente() {

                const numeroHistoria =
                    historia.value.trim();


                if (!numeroHistoria) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Historia clínica',
                        text: 'Ingrese un número de historia clínica.'
                    });

                    return;

                }


                limpiarPaciente();


                document
                    .getElementById(
                        'buscandoPaciente'
                    )
                    .classList
                    .remove('d-none');


                fetch(
                        '{{ route("camas.censo.buscar-paciente") }}', {
                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': '{{ csrf_token() }}'

                            },

                            body: JSON.stringify({

                                historia: numeroHistoria

                            })
                        }
                    )
                    .then(async response => {

                        let data;

                        try {

                            data =
                                await response.json();

                        } catch (e) {

                            throw new Error(
                                'El servidor devolvió una respuesta inválida.'
                            );

                        }


                        if (!response.ok) {

                            throw new Error(
                                data.message ??
                                'Paciente no encontrado.'
                            );

                        }


                        return data;

                    })
                    .then(data => {

                        document
                            .getElementById(
                                'buscandoPaciente'
                            )
                            .classList
                            .add('d-none');


                        const paciente =
                            data.paciente;


                        document
                            .getElementById(
                                'paciente_nombre'
                            )
                            .textContent =
                            paciente.nombre_completo;


                        document
                            .getElementById(
                                'paciente_historia'
                            )
                            .textContent =
                            paciente.nro_historia_clinica;


                        document
                            .getElementById(
                                'paciente_telefono'
                            )
                            .textContent =
                            paciente.telefono ?
                            paciente.telefono :
                            'Sin teléfono';


                        document
                            .getElementById(
                                'id_paciente_sigh'
                            )
                            .value =
                            paciente.id_paciente_sigh;


                        document
                            .getElementById(
                                'nro_historia_clinica'
                            )
                            .value =
                            paciente.nro_historia_clinica;


                        document
                            .getElementById(
                                'nombre_completo'
                            )
                            .value =
                            paciente.nombre_completo;

                        document
                            .getElementById('sexo')
                            .value =
                            paciente.sexo ?? '';

                        document
                            .getElementById('fecha_nacimiento')
                            .value =
                            paciente.fecha_nacimiento ?? '';

                        document
                            .getElementById(
                                'telefono'
                            )
                            .value =
                            paciente.telefono ?? '';


                        document
                            .getElementById(
                                'resultadoPaciente'
                            )
                            .classList
                            .remove('d-none');


                        document
                            .getElementById(
                                'btnConfirmarAsignacion'
                            )
                            .disabled =
                            false;

                    })
                    .catch(error => {

                        document
                            .getElementById(
                                'buscandoPaciente'
                            )
                            .classList
                            .add('d-none');


                        const errorDiv =
                            document.getElementById(
                                'errorPaciente'
                            );


                        errorDiv.textContent =
                            error.message;


                        errorDiv.classList
                            .remove('d-none');

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | ALTA
            |--------------------------------------------------------------------------
            |
            | Interceptamos el formulario REAL.
            | Esto evita el problema que tenías con el formulario oculto.
            |
            */

            document
                .querySelectorAll('.form-alta')
                .forEach(function(form) {

                    form.addEventListener(
                        'submit',
                        function(event) {

                            /*
                            | Si ya fue confirmado,
                            | permitir envío normal.
                            */

                            if (
                                form.dataset.confirmado ===
                                '1'
                            ) {

                                return;

                            }


                            event.preventDefault();


                            const paciente =
                                form.dataset.paciente;

                            const cama =
                                form.dataset.cama;


                            /*
                            | Si SweetAlert no existe por alguna
                            | razón, usamos confirm() normal.
                            */

                            if (
                                typeof Swal ===
                                'undefined'
                            ) {

                                const confirmar =
                                    window.confirm(
                                        '¿Dar de alta a ' +
                                        paciente +
                                        ' y liberar la cama ' +
                                        cama +
                                        '?'
                                    );


                                if (confirmar) {

                                    form.dataset.confirmado =
                                        '1';

                                    form.submit();

                                }


                                return;

                            }


                            Swal.fire({

                                    title: '¿Liberar la cama ' +
                                        cama +
                                        '?',

                                    html: 'Paciente:<br>' +
                                        '<strong>' +
                                        paciente +
                                        '</strong><br><br>' +
                                        'La cama quedará disponible.',

                                    icon: 'warning',

                                    showCancelButton: true,

                                    confirmButtonText: 'Sí, dar alta',

                                    cancelButtonText: 'Cancelar',

                                    confirmButtonColor: '#dc3545'

                                })
                                .then(function(result) {

                                    /*
                                    | Compatible con versiones nuevas:
                                    | result.isConfirmed
                                    |
                                    | y versiones anteriores:
                                    | result.value
                                    */

                                    if (
                                        result.isConfirmed ||
                                        result.value
                                    ) {

                                        form.dataset.confirmado =
                                            '1';

                                        form.submit();

                                    }

                                });

                        }
                    );

                });

        }
    );



    /*
    |--------------------------------------------------------------------------
    | ABRIR MODAL DE ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    function abrirAsignacion(
        camaId,
        camaNumero,
        servicio,
        habitacion
    ) {

        const form =
            document.getElementById(
                'formAsignar'
            );


        form.reset();


        document
            .getElementById(
                'cama_id'
            )
            .value =
            camaId;


        document
            .getElementById(
                'modal_cama'
            )
            .textContent =
            camaNumero;


        document
            .getElementById(
                'modal_servicio'
            )
            .textContent =
            servicio;


        document
            .getElementById(
                'modal_habitacion'
            )
            .textContent =
            habitacion;


        limpiarPaciente();


        $('#modalAsignar')
            .modal('show');


        setTimeout(
            function() {

                document
                    .getElementById(
                        'historia_busqueda'
                    )
                    .focus();

            },
            400
        );

    }



    /*
    |--------------------------------------------------------------------------
    | LIMPIAR PACIENTE
    |--------------------------------------------------------------------------
    */

    function limpiarPaciente() {

        const resultado =
            document.getElementById(
                'resultadoPaciente'
            );

        const error =
            document.getElementById(
                'errorPaciente'
            );

        const buscando =
            document.getElementById(
                'buscandoPaciente'
            );

        const btnConfirmar =
            document.getElementById(
                'btnConfirmarAsignacion'
            );


        if (resultado) {

            resultado.classList
                .add('d-none');

        }


        if (error) {

            error.classList
                .add('d-none');

            error.textContent = '';

        }


        if (buscando) {

            buscando.classList
                .add('d-none');

        }


        document
            .getElementById(
                'id_paciente_sigh'
            )
            .value = '';


        document
            .getElementById(
                'nro_historia_clinica'
            )
            .value = '';


        document
            .getElementById(
                'nombre_completo'
            )
            .value = '';

        document
            .getElementById('sexo')
            .value = '';

        document
            .getElementById('fecha_nacimiento')
            .value = '';

        document
            .getElementById(
                'telefono'
            )
            .value = '';


        if (btnConfirmar) {

            btnConfirmar.disabled =
                true;

        }

    }
</script>

@stop