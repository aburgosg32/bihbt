@extends('adminlte::page')

@section('title', 'Servicios - Gestión de Camas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0">
            <i class="fas fa-clinic-medical mr-2"></i>
            Servicios
        </h1>

        <small class="text-muted">
            Gestión de servicios hospitalarios del módulo de camas
        </small>
    </div>

    <a href="{{ route('camas.servicios.create') }}"
        class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i>
        Nuevo Servicio
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
            action="{{ route('camas.servicios.index') }}">

            <div class="row">

                <div class="col-md-5 mb-2 mb-md-0">
                    <input type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="form-control"
                        placeholder="Buscar servicio, código o piso...">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">

                    <select name="departamento_id"
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

                <div class="col-md-3">

                    <div class="d-flex">

                        <button type="submit"
                            class="btn btn-primary mr-2">

                            <i class="fas fa-search mr-1"></i>
                            Buscar

                        </button>

                        <a href="{{ route('camas.servicios.index') }}"
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

                        <th>Departamento</th>

                        <th>Servicio</th>

                        <th>Piso</th>

                        <th class="text-center">
                            Habitaciones
                        </th>

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

                    @forelse($servicios as $servicio)

                    <tr>

                        <td>
                            {{ $servicio->id }}
                        </td>

                        <td>

                            @if($servicio->codigo)

                            <span class="badge badge-light border">
                                {{ $servicio->codigo }}
                            </span>

                            @else

                            <span class="text-muted">
                                -
                            </span>

                            @endif

                        </td>

                        <td>

                            {{ $servicio->departamento?->nombre ?? '-' }}

                        </td>

                        <td>

                            <strong>
                                {{ $servicio->nombre }}
                            </strong>

                        </td>

                        <td>

                            {{ $servicio->piso ?: '-' }}

                        </td>

                        <td class="text-center">

                            <span class="badge badge-info">

                                {{ $servicio->habitaciones_count }}

                            </span>

                        </td>

                        <td class="text-center">

                            <span class="badge badge-primary">

                                {{ $servicio->camas_count }}

                            </span>

                        </td>

                        <td class="text-center">

                            @if($servicio->activo)

                            <span class="badge badge-success">
                                Activo
                            </span>

                            @else

                            <span class="badge badge-secondary">
                                Inactivo
                            </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <a href="{{ route(
                                'camas.servicios.edit',
                                $servicio
                            ) }}"
                                class="btn btn-sm btn-warning"
                                title="Editar">

                                <i class="fas fa-edit"></i>

                            </a>

                            <button type="button"
                                class="btn btn-sm btn-danger"
                                onclick="confirmarEliminar(
                                        {{ $servicio->id }},
                                        @js($servicio->nombre)
                                    )">

                                <i class="fas fa-trash"></i>

                            </button>

                            <form id="form-eliminar-{{ $servicio->id }}"
                                action="{{ route(
                                    'camas.servicios.destroy',
                                    $servicio
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

                        <td colspan="9"
                            class="text-center py-5 text-muted">

                            <i class="fas fa-clinic-medical fa-3x mb-3"></i>

                            <p class="mb-0">

                                No se encontraron servicios registrados.

                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if($servicios->hasPages())

    <div class="card-footer bg-white">

        {{ $servicios->links() }}

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
            title: '¿Eliminar servicio?',
            html: 'Se eliminará el servicio:<br><strong>' +
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
                    .getElementById('form-eliminar-' + id)
                    .submit();

            }

        });

    }
</script>

@stop