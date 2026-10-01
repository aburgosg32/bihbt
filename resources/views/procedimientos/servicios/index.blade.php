@extends('adminlte::page')

@section('title', 'Servicios de procedimientos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">

    <div>
        <h1 class="m-0">
            <i class="fas fa-hospital-alt text-primary mr-2"></i>
            Servicios de procedimientos
        </h1>

        <small class="text-muted">
            Administración de los servicios que realizan procedimientos
        </small>
    </div>

    <button
        type="button"
        class="btn btn-primary mt-2 mt-md-0"
        data-toggle="modal"
        data-target="#modalCrearServicio">
        <i class="fas fa-plus-circle mr-1"></i>
        Nuevo servicio
    </button>

</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- Mensaje satisfactorio --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <div class="d-flex">
            <i class="fas fa-exclamation-circle mr-2 mt-1"></i>

            <div>
                <strong>No se pudo completar la operación.</strong>

                <ul class="mb-0 mt-1 pl-3">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-1"></i>
                Servicios registrados
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    Total: {{ $servicios->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th style="width: 80px;">
                                N.°
                            </th>

                            <th>
                                Servicio
                            </th>

                            <th
                                class="text-center"
                                style="width: 130px;">
                                Estado
                            </th>

                            <th
                                class="text-center"
                                style="width: 180px;">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($servicios as $servicio)

                        <tr>
                            <td class="align-middle">
                                {{ $servicios->firstItem() + $loop->index }}
                            </td>

                            <td class="align-middle">
                                <div class="font-weight-bold">
                                    {{ $servicio->nombre }}
                                </div>
                            </td>

                            <td class="text-center align-middle">

                                @if ($servicio->activo)
                                <span class="badge badge-success px-3 py-2">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    ACTIVO
                                </span>
                                @else
                                <span class="badge badge-secondary px-3 py-2">
                                    <i class="fas fa-ban mr-1"></i>
                                    INACTIVO
                                </span>
                                @endif

                            </td>

                            <td class="text-center align-middle">

                                {{-- Botón editar --}}
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEditarServicio{{ $servicio->id }}"
                                    title="Editar servicio">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Botón activar/desactivar --}}
                                <form
                                    action="{{ route(
                                                'procedimientos.servicios.estado',
                                                $servicio
                                            ) }}"
                                    method="POST"
                                    class="d-inline formulario-estado">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-sm
                                                    {{ $servicio->activo
                                                        ? 'btn-danger'
                                                        : 'btn-success' }}"
                                        data-nombre="{{ $servicio->nombre }}"
                                        data-accion="{{ $servicio->activo
                                                    ? 'desactivar'
                                                    : 'activar' }}"
                                        title="{{ $servicio->activo
                                                    ? 'Desactivar servicio'
                                                    : 'Activar servicio' }}">
                                        <i class="fas
                                                    {{ $servicio->activo
                                                        ? 'fa-ban'
                                                        : 'fa-check' }}">
                                        </i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                        @empty

                        <tr>
                            <td
                                colspan="4"
                                class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-3x mb-3"></i>

                                <div>
                                    No existen servicios registrados.
                                </div>
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($servicios->hasPages())
        <div class="card-footer clearfix">
            <div class="d-flex justify-content-end mb-0">
                {{ $servicios->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Modal para crear servicio --}}
<div
    class="modal fade"
    id="modalCrearServicio"
    tabindex="-1"
    role="dialog"
    aria-labelledby="tituloModalCrearServicio"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-centered"
        role="document">
        <div class="modal-content">

            <form
                action="{{ route('procedimientos.servicios.store') }}"
                method="POST"
                id="formCrearServicio">
                @csrf

                <div class="modal-header bg-primary">

                    <h5
                        class="modal-title"
                        id="tituloModalCrearServicio">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Registrar servicio
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group mb-0">

                        <label for="nombre">
                            Nombre del servicio
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control
                                    @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}"
                            maxlength="150"
                            placeholder="Ejemplo: CARDIOLOGÍA"
                            autocomplete="off"
                            required>

                        @error('nombre')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                        @enderror

                        <small class="form-text text-muted">
                            El nombre se guardará automáticamente en
                            mayúsculas.
                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btnGuardarServicio">
                        <i class="fas fa-save mr-1"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

{{-- Modales para editar --}}
@foreach ($servicios as $servicio)

<div
    class="modal fade"
    id="modalEditarServicio{{ $servicio->id }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="tituloEditarServicio{{ $servicio->id }}"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-centered"
        role="document">
        <div class="modal-content">

            <form
                action="{{ route(
                            'procedimientos.servicios.update',
                            $servicio
                        ) }}"
                method="POST"
                class="formEditarServicio">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">

                    <h5
                        class="modal-title"
                        id="tituloEditarServicio{{ $servicio->id }}">
                        <i class="fas fa-edit mr-1"></i>
                        Editar servicio
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group mb-0">

                        <label
                            for="nombreEditar{{ $servicio->id }}">
                            Nombre del servicio
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            id="nombreEditar{{ $servicio->id }}"
                            class="form-control"
                            value="{{ $servicio->nombre }}"
                            maxlength="150"
                            autocomplete="off"
                            required>

                        <small class="form-text text-muted">
                            El nombre se guardará automáticamente en
                            mayúsculas.
                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i>
                        Actualizar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endforeach

@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
         * Si existen errores de validación al registrar,
         * volvemos a abrir el modal de creación.
         */
        @if($errors -> any())
        $('#modalCrearServicio').modal('show');
        @endif

        /*
         * Confirmación para activar o desactivar.
         */
        document.querySelectorAll('.formulario-estado')
            .forEach(function(formulario) {

                formulario.addEventListener('submit', function(event) {

                    const boton = formulario.querySelector(
                        'button[type="submit"]'
                    );

                    const nombre = boton.dataset.nombre;
                    const accion = boton.dataset.accion;

                    const confirmar = window.confirm(
                        '¿Está seguro de ' +
                        accion +
                        ' el servicio "' +
                        nombre +
                        '"?'
                    );

                    if (!confirmar) {
                        event.preventDefault();
                    }
                });
            });

        /*
         * Evita enviar dos veces el formulario de creación.
         */
        const formularioCrear = document.getElementById(
            'formCrearServicio'
        );

        const botonGuardar = document.getElementById(
            'btnGuardarServicio'
        );

        if (formularioCrear) {
            formularioCrear.addEventListener('submit', function() {

                botonGuardar.disabled = true;

                botonGuardar.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    'Guardando...';
            });
        }

        /*
         * Evita enviar dos veces los formularios de edición.
         */
        document.querySelectorAll('.formEditarServicio')
            .forEach(function(formulario) {

                formulario.addEventListener('submit', function() {

                    const boton = formulario.querySelector(
                        'button[type="submit"]'
                    );

                    boton.disabled = true;

                    boton.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                        'Actualizando...';
                });
            });

        /*
         * Convierte visualmente el texto a mayúsculas.
         */
        document.querySelectorAll(
            'input[name="nombre"]'
        ).forEach(function(input) {

            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });

    });
</script>
@stop