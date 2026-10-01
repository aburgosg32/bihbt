@extends('adminlte::page')

@section('title', 'Departamentos - Gestión de Camas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0">
            <i class="fas fa-hospital-alt mr-2"></i>
            Departamentos
        </h1>
        <small class="text-muted">
            Gestión de departamentos para el módulo de camas
        </small>
    </div>

    <a href="{{ route('camas.departamentos.create') }}"
        class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i>
        Nuevo Departamento
    </a>
</div>
@stop

@section('content')

{{-- ALERTAS --}}
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
            action="{{ route('camas.departamentos.index') }}">

            <div class="row">

                <div class="col-md-8">
                    <div class="input-group">

                        <input type="text"
                            name="buscar"
                            class="form-control"
                            value="{{ request('buscar') }}"
                            placeholder="Buscar por nombre o código...">

                        <div class="input-group-append">

                            <button class="btn btn-primary"
                                type="submit">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>

                        </div>

                    </div>
                </div>

                <div class="col-md-4 text-md-right mt-2 mt-md-0">

                    @if(request('buscar'))

                    <a href="{{ route('camas.departamentos.index') }}"
                        class="btn btn-outline-secondary">

                        <i class="fas fa-times mr-1"></i>
                        Limpiar búsqueda

                    </a>

                    @endif

                </div>

            </div>

        </form>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>
                    <tr>
                        <th width="70">#</th>
                        <th>Código</th>
                        <th>Departamento</th>
                        <th>Descripción</th>
                        <th class="text-center">Servicios</th>
                        <th class="text-center">Estado</th>
                        <th width="150" class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($departamentos as $departamento)

                    <tr>

                        <td>
                            {{ $departamento->id }}
                        </td>

                        <td>
                            @if($departamento->codigo)
                            <span class="badge badge-light border">
                                {{ $departamento->codigo }}
                            </span>
                            @else
                            <span class="text-muted">
                                -
                            </span>
                            @endif
                        </td>

                        <td>
                            <strong>
                                {{ $departamento->nombre }}
                            </strong>
                        </td>

                        <td>
                            {{ $departamento->descripcion ?: '-' }}
                        </td>

                        <td class="text-center">

                            <span class="badge badge-info">
                                {{ $departamento->servicios()->count() }}
                            </span>

                        </td>

                        <td class="text-center">

                            @if($departamento->activo)

                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i>
                                Activo
                            </span>

                            @else

                            <span class="badge badge-secondary">
                                <i class="fas fa-ban mr-1"></i>
                                Inactivo
                            </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <a href="{{ route(
                                'camas.departamentos.edit',
                                $departamento
                            ) }}"
                                class="btn btn-sm btn-warning"
                                title="Editar">

                                <i class="fas fa-edit"></i>

                            </a>

                            <button type="button"
                                class="btn btn-sm btn-danger"
                                title="Eliminar"
                                onclick="confirmarEliminar(
                                        {{ $departamento->id }},
                                        '{{ addslashes($departamento->nombre) }}'
                                    )">

                                <i class="fas fa-trash"></i>

                            </button>

                            <form id="form-eliminar-{{ $departamento->id }}"
                                action="{{ route(
                                    'camas.departamentos.destroy',
                                    $departamento
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

                        <td colspan="7"
                            class="text-center py-5 text-muted">

                            <i class="fas fa-folder-open fa-3x mb-3"></i>

                            <p class="mb-0">
                                No se encontraron departamentos registrados.
                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if($departamentos->hasPages())

    <div class="card-footer bg-white">

        {{ $departamentos->links() }}

    </div>

    @endif

</div>

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('js')

<script>
    function confirmarEliminar(id, nombre) {

        Swal.fire({
            title: '¿Eliminar departamento?',
            html: 'Se eliminará el departamento:<br><strong>' +
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