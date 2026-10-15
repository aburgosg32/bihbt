@extends('adminlte::page')

@section('title', 'Médicos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">

    <div>
        <h1 class="m-0">
            <i class="fas fa-user-md text-primary mr-2"></i>
            Médicos
        </h1>

        <small class="text-muted">
            Médicos responsables de los procedimientos
        </small>
    </div>

    <button
        type="button"
        class="btn btn-primary mt-2 mt-md-0"
        data-toggle="modal"
        data-target="#modalCrearMedico">
        <i class="fas fa-plus-circle mr-1"></i>
        Nuevo médico
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
            data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    {{-- Errores --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">

        <strong>
            <i class="fas fa-exclamation-circle mr-1"></i>
            No se pudo completar la operación:
        </strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button
            type="button"
            class="close"
            data-dismiss="alert">
            <span>&times;</span>
        </button>

    </div>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i>
                Filtros de búsqueda
            </h3>
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('procedimientos.medicos.index') }}">
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group mb-md-0">

                            <label for="buscar">
                                Médico o especialidad
                            </label>

                            <input
                                type="text"
                                name="buscar"
                                id="buscar"
                                class="form-control"
                                value="{{ request('buscar') }}"
                                placeholder="Ingrese nombre, apellido o especialidad">

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-group mb-md-0">

                            <label for="estado">
                                Estado
                            </label>

                            <select
                                name="estado"
                                id="estado"
                                class="form-control">
                                <option value="">Todos</option>

                                <option
                                    value="activo"
                                    {{ request('estado') === 'activo'
                                            ? 'selected'
                                            : '' }}>
                                    Activos
                                </option>

                                <option
                                    value="inactivo"
                                    {{ request('estado') === 'inactivo'
                                            ? 'selected'
                                            : '' }}>
                                    Inactivos
                                </option>
                            </select>

                        </div>

                    </div>

                    <div class="col-md-3 d-flex align-items-end">

                        <div class="form-group mb-0">

                            <button
                                type="submit"
                                class="btn btn-primary"
                                title="Buscar">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>

                            <a
                                href="{{ route(
                                        'procedimientos.medicos.index'
                                    ) }}"
                                class="btn btn-secondary"
                                title="Limpiar filtros">
                                <i class="fas fa-eraser"></i>
                            </a>

                        </div>

                    </div>

                </div>
            </form>

        </div>

    </div>

    {{-- Listado --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-list mr-1"></i>
                Médicos registrados
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    Total: {{ $medicos->total() }}
                </span>
            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th style="width: 70px;">N.°</th>
                            <th>Médico</th>
                            <th>Especialidad</th>

                            <th
                                class="text-center"
                                style="width: 120px;">
                                Estado
                            </th>

                            <th
                                class="text-center"
                                style="width: 150px;">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($medicos as $medico)

                        <tr>

                            <td class="align-middle">
                                {{ $medicos->firstItem()
                                            + $loop->index }}
                            </td>

                            <td class="align-middle">

                                <div class="font-weight-bold">
                                    {{ $medico->nombre_completo }}
                                </div>

                            </td>

                            <td class="align-middle">
                                {{ $medico->especialidad }}
                            </td>

                            <td class="text-center align-middle">

                                @if ($medico->activo)
                                <span
                                    class="badge badge-success px-2 py-2">
                                    ACTIVO
                                </span>
                                @else
                                <span
                                    class="badge badge-secondary px-2 py-2">
                                    INACTIVO
                                </span>
                                @endif

                            </td>

                            <td class="text-center align-middle">

                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEditarMedico{{ $medico->id }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route(
                                                'procedimientos.medicos.estado',
                                                $medico
                                            ) }}"
                                    class="d-inline formulario-estado">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-sm
                                                    {{ $medico->activo
                                                        ? 'btn-danger'
                                                        : 'btn-success' }}"
                                        data-nombre="{{ $medico->nombre_completo }}"
                                        data-accion="{{ $medico->activo
                                                    ? 'desactivar'
                                                    : 'activar' }}"
                                        title="{{ $medico->activo
                                                    ? 'Desactivar'
                                                    : 'Activar' }}">
                                        <i class="fas
                                                    {{ $medico->activo
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
                                colspan="5"
                                class="text-center text-muted py-5">
                                <i class="fas fa-user-md fa-3x mb-3"></i>

                                <div>
                                    No existen médicos registrados.
                                </div>
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($medicos->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $medicos->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Modal para registrar --}}
<div
    class="modal fade"
    id="modalCrearMedico"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('procedimientos.medicos.store') }}">
                @csrf

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="crear">

                <div class="modal-header bg-primary">

                    <h5 class="modal-title">
                        <i class="fas fa-user-plus mr-1"></i>
                        Registrar médico
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

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="crear_apellido_paterno">
                                    Apellido paterno
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="apellido_paterno"
                                    id="crear_apellido_paterno"
                                    class="form-control texto-mayuscula"
                                    value="{{ old('apellido_paterno') }}"
                                    maxlength="80"
                                    required>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="crear_apellido_materno">
                                    Apellido materno
                                </label>

                                <input
                                    type="text"
                                    name="apellido_materno"
                                    id="crear_apellido_materno"
                                    class="form-control texto-mayuscula"
                                    value="{{ old('apellido_materno') }}"
                                    maxlength="80">

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label for="crear_nombres">
                            Nombres
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombres"
                            id="crear_nombres"
                            class="form-control texto-mayuscula"
                            value="{{ old('nombres') }}"
                            maxlength="120"
                            required>

                    </div>

                    <div class="form-group mb-0">

                        <label for="crear_especialidad">
                            Especialidad
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="especialidad"
                            id="crear_especialidad"
                            class="form-control texto-mayuscula"
                            value="{{ old('especialidad') }}"
                            maxlength="150"
                            placeholder="Ejemplo: CARDIOLOGÍA"
                            required>

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
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

{{-- Modales para editar --}}
@foreach ($medicos as $medico)

<div
    class="modal fade"
    id="modalEditarMedico{{ $medico->id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route(
                            'procedimientos.medicos.update',
                            $medico
                        ) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="editar-{{ $medico->id }}">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Editar médico
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

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Apellido paterno</label>

                                <input
                                    type="text"
                                    name="apellido_paterno"
                                    class="form-control texto-mayuscula"
                                    value="{{ $medico->apellido_paterno }}"
                                    maxlength="80"
                                    required>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Apellido materno</label>

                                <input
                                    type="text"
                                    name="apellido_materno"
                                    class="form-control texto-mayuscula"
                                    value="{{ $medico->apellido_materno }}"
                                    maxlength="80">

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label>Nombres</label>

                        <input
                            type="text"
                            name="nombres"
                            class="form-control texto-mayuscula"
                            value="{{ $medico->nombres }}"
                            maxlength="120"
                            required>

                    </div>

                    <div class="form-group mb-0">

                        <label>Especialidad</label>

                        <input
                            type="text"
                            name="especialidad"
                            class="form-control texto-mayuscula"
                            value="{{ $medico->especialidad }}"
                            maxlength="150"
                            required>

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

        const formularioOrigen = @json(old('formulario_origen'));

        @if($errors -> any())
        if (
            formularioOrigen &&
            formularioOrigen.startsWith('editar-')
        ) {
            const id = formularioOrigen.replace('editar-', '');

            $('#modalEditarMedico' + id).modal('show');
        } else {
            $('#modalCrearMedico').modal('show');
        }
        @endif

        document.querySelectorAll('.texto-mayuscula')
            .forEach(function(campo) {
                campo.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });

        document.querySelectorAll('.formulario-estado')
            .forEach(function(formulario) {

                formulario.addEventListener(
                    'submit',
                    function(evento) {

                        const boton = formulario.querySelector(
                            'button[type="submit"]'
                        );

                        const mensaje =
                            '¿Está seguro de ' +
                            boton.dataset.accion +
                            ' al médico "' +
                            boton.dataset.nombre +
                            '"?';

                        if (!confirm(mensaje)) {
                            evento.preventDefault();
                        }
                    }
                );

            });

    });
</script>
@stop