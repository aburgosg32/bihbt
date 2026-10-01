@extends('adminlte::page')

@section('title', 'Camas - Gestión de Camas')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>
        <h1 class="m-0">
            <i class="fas fa-bed mr-2"></i>
            Camas
        </h1>

        <small class="text-muted">
            Gestión y configuración de camas hospitalarias
        </small>
    </div>

    <a href="{{ route('camas.camas.create') }}"
        class="btn btn-primary">

        <i class="fas fa-plus mr-1"></i>
        Nueva Cama

    </a>

</div>

@stop

@section('content')

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

<div class="card shadow-sm">

    <div class="card-header bg-white">

        <form method="GET"
            action="{{ route('camas.camas.index') }}">

            <div class="row">

                <div class="col-lg-3 mb-2">

                    <input type="text"
                        name="buscar"
                        class="form-control"
                        value="{{ request('buscar') }}"
                        placeholder="Código, número o tipo...">

                </div>

                <div class="col-lg-2 mb-2">

                    <select name="departamento_id"
                        id="filtro_departamento"
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

                <div class="col-lg-2 mb-2">

                    <select name="servicio_id"
                        id="filtro_servicio"
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

                <div class="col-lg-2 mb-2">

                    <select name="habitacion_id"
                        id="filtro_habitacion"
                        class="form-control">

                        <option value="">
                            Todas las habitaciones
                        </option>

                        @foreach($habitaciones as $habitacion)

                        <option value="{{ $habitacion->id }}"
                            {{ request('habitacion_id') == $habitacion->id
                                ? 'selected'
                                : '' }}>

                            {{ $habitacion->nombre }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2 mb-2">

                    <select name="estado_operativo"
                        class="form-control">

                        <option value="">
                            Todos los estados
                        </option>

                        @foreach([
                        'OPERATIVA' => 'Operativa',
                        'LIMPIEZA' => 'Limpieza',
                        'MANTENIMIENTO' => 'Mantenimiento',
                        'BLOQUEADA' => 'Bloqueada',
                        'FUERA_DE_SERVICIO' => 'Fuera de servicio',
                        ] as $valor => $texto)

                        <option value="{{ $valor }}"
                            {{ request('estado_operativo') === $valor
                                ? 'selected'
                                : '' }}>

                            {{ $texto }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-1 mb-2">

                    <div class="d-flex">

                        <button type="submit"
                            class="btn btn-primary mr-1">

                            <i class="fas fa-search"></i>

                        </button>

                        <a href="{{ route('camas.camas.index') }}"
                            class="btn btn-outline-secondary">

                            <i class="fas fa-sync-alt"></i>

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Número</th>
                        <th>Departamento</th>
                        <th>Servicio</th>
                        <th>Habitación</th>
                        <th>Tipo</th>
                        <th>Sexo</th>
                        <th class="text-center">Estado operativo</th>
                        <th class="text-center">Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($camas as $cama)

                    <tr>

                        <td>
                            {{ $cama->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $cama->codigo }}
                            </strong>
                        </td>

                        <td>
                            {{ $cama->numero }}
                        </td>

                        <td>
                            {{ $cama->servicio?->departamento?->nombre ?? '-' }}
                        </td>

                        <td>
                            {{ $cama->servicio?->nombre ?? '-' }}
                        </td>

                        <td>
                            {{ $cama->habitacion?->nombre ?? '-' }}
                        </td>

                        <td>
                            {{ $cama->tipo_cama ?: '-' }}
                        </td>

                        <td>
                            {{ ucfirst(strtolower($cama->sexo_permitido)) }}
                        </td>

                        <td class="text-center">

                            @php
                            $clase = match($cama->estado_operativo) {
                            'OPERATIVA' => 'success',
                            'LIMPIEZA' => 'info',
                            'MANTENIMIENTO' => 'warning',
                            'BLOQUEADA' => 'secondary',
                            'FUERA_DE_SERVICIO' => 'danger',
                            default => 'secondary',
                            };
                            @endphp

                            <span class="badge badge-{{ $clase }}">

                                {{ str_replace(
                                '_',
                                ' ',
                                $cama->estado_operativo
                            ) }}

                            </span>

                        </td>

                        <td class="text-center">

                            @if($cama->activo)

                            <span class="badge badge-success">
                                Sí
                            </span>

                            @else

                            <span class="badge badge-secondary">
                                No
                            </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <a href="{{ route(
                            'camas.camas.edit',
                            $cama
                        ) }}"
                                class="btn btn-sm btn-warning">

                                <i class="fas fa-edit"></i>

                            </a>

                            <button type="button"
                                class="btn btn-sm btn-danger"
                                onclick="confirmarEliminar(
                                    {{ $cama->id }},
                                    @js($cama->numero)
                                )">

                                <i class="fas fa-trash"></i>

                            </button>

                            <form id="form-eliminar-{{ $cama->id }}"
                                action="{{ route(
                                  'camas.camas.destroy',
                                  $cama
                              ) }}"
                                method="POST"
                                class="d-none">

                                @csrf
                                @method('DELETE')

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="11"
                            class="text-center py-5 text-muted">

                            <i class="fas fa-bed fa-3x mb-3"></i>

                            <p class="mb-0">
                                No se encontraron camas registradas.
                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if($camas->hasPages())

    <div class="card-footer bg-white">
        {{ $camas->links() }}
    </div>

    @endif

</div>

@stop

@section('css')

<link rel="stylesheet"
    href="{{ asset('css/hbt.css') }}">

@stop

@section('js')

<script>
    function confirmarEliminar(id, numero) {

        Swal.fire({
            title: '¿Eliminar cama?',
            html: 'Se eliminará la cama:<br><strong>' +
                numero +
                '</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then((result) => {

            if (result.isConfirmed) {

                document
                    .getElementById(
                        'form-eliminar-' + id
                    )
                    .submit();

            }

        });

    }

    const departamento =
        document.getElementById(
            'filtro_departamento'
        );

    const servicio =
        document.getElementById(
            'filtro_servicio'
        );

    const habitacion =
        document.getElementById(
            'filtro_habitacion'
        );

    departamento.addEventListener(
        'change',
        function() {

            servicio.innerHTML =
                '<option value="">Todos los servicios</option>';

            habitacion.innerHTML =
                '<option value="">Todas las habitaciones</option>';

            if (!this.value) {
                return;
            }

            fetch(
                    '{{ url("/camas/departamentos") }}/' +
                    this.value +
                    '/servicios'
                )
                .then(response => response.json())
                .then(data => {

                    data.forEach(item => {

                        const option =
                            document.createElement('option');

                        option.value =
                            item.id;

                        option.textContent =
                            item.nombre;

                        servicio.appendChild(option);

                    });

                });

        }
    );

    servicio.addEventListener(
        'change',
        function() {

            habitacion.innerHTML =
                '<option value="">Todas las habitaciones</option>';

            if (!this.value) {
                return;
            }

            fetch(
                    '{{ url("/camas/servicios") }}/' +
                    this.value +
                    '/habitaciones'
                )
                .then(response => response.json())
                .then(data => {

                    data.forEach(item => {

                        const option =
                            document.createElement('option');

                        option.value =
                            item.id;

                        option.textContent =
                            item.nombre;

                        habitacion.appendChild(option);

                    });

                });

        }
    );
</script>

@stop