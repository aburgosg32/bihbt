@extends('adminlte::page')

@section('title', 'Habitaciones - Gestión de Camas')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-door-open mr-2"></i>

            Habitaciones

        </h1>

        <small class="text-muted">

            Gestión de habitaciones hospitalarias

        </small>

    </div>

    <a href="{{ route('camas.habitaciones.create') }}"
        class="btn btn-primary">

        <i class="fas fa-plus mr-1"></i>

        Nueva Habitación

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
            action="{{ route('camas.habitaciones.index') }}">

            <div class="row">

                <div class="col-lg-4 mb-2">

                    <input type="text"
                        name="buscar"
                        class="form-control"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar habitación, número o piso...">

                </div>

                <div class="col-lg-3 mb-2">

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

                <div class="col-lg-3 mb-2">

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

                    <div class="d-flex">

                        <button type="submit"
                            class="btn btn-primary mr-2">

                            <i class="fas fa-search"></i>

                        </button>

                        <a href="{{ route('camas.habitaciones.index') }}"
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

                        <th>Departamento</th>

                        <th>Servicio</th>

                        <th>Habitación</th>

                        <th>Número</th>

                        <th>Piso</th>

                        <th class="text-center">

                            Camas

                        </th>

                        <th class="text-center">

                            Estado

                        </th>

                        <th class="text-center">

                            Acciones

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($habitaciones as $habitacion)

                    <tr>

                        <td>

                            {{ $habitacion->id }}

                        </td>

                        <td>

                            {{ $habitacion->servicio?->departamento?->nombre ?? '-' }}

                        </td>

                        <td>

                            <strong>

                                {{ $habitacion->servicio?->nombre ?? '-' }}

                            </strong>

                        </td>

                        <td>

                            {{ $habitacion->nombre }}

                        </td>

                        <td>

                            {{ $habitacion->numero ?: '-' }}

                        </td>

                        <td>

                            {{ $habitacion->piso ?: '-' }}

                        </td>

                        <td class="text-center">

                            <span class="badge badge-primary">

                                {{ $habitacion->camas_count }}

                            </span>

                        </td>

                        <td class="text-center">

                            @if($habitacion->activo)

                            <span class="badge badge-success">

                                Activa

                            </span>

                            @else

                            <span class="badge badge-secondary">

                                Inactiva

                            </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <a href="{{ route(
                            'camas.habitaciones.edit',
                            $habitacion
                        ) }}"
                                class="btn btn-sm btn-warning">

                                <i class="fas fa-edit"></i>

                            </a>

                            <button type="button"
                                class="btn btn-sm btn-danger"
                                onclick="confirmarEliminar(
                                    {{ $habitacion->id }},
                                    @js($habitacion->nombre)
                                )">

                                <i class="fas fa-trash"></i>

                            </button>

                            <form id="form-eliminar-{{ $habitacion->id }}"
                                method="POST"
                                action="{{ route(
                                'camas.habitaciones.destroy',
                                $habitacion
                              ) }}"
                                class="d-none">

                                @csrf
                                @method('DELETE')

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="9"
                            class="text-center py-5 text-muted">

                            <i class="fas fa-door-open fa-3x mb-3"></i>

                            <p class="mb-0">

                                No se encontraron habitaciones registradas.

                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if($habitaciones->hasPages())

    <div class="card-footer bg-white">

        {{ $habitaciones->links() }}

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
    function confirmarEliminar(id, nombre) {

        Swal.fire({
            title: '¿Eliminar habitación?',
            html: 'Se eliminará la habitación:<br><strong>' +
                nombre +
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

    document
        .getElementById('filtro_departamento')
        .addEventListener('change', function() {

            const departamentoId = this.value;

            const servicio =
                document.getElementById(
                    'filtro_servicio'
                );

            servicio.innerHTML =
                '<option value="">Todos los servicios</option>';

            if (!departamentoId) {

                return;

            }

            fetch(
                    '{{ url("/camas/departamentos") }}/' +
                    departamentoId +
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
                            document.createElement('option');

                        option.value = item.id;

                        option.textContent =
                            item.nombre;

                        servicio.appendChild(option);

                    });

                })
                .catch(error => {

                    console.error(error);

                });

        });
</script>

@stop