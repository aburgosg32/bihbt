@extends('adminlte::page')

@section('title', 'Procedimientos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')

<div class="d-flex justify-content-between align-items-center flex-wrap">

    <div>
        <h1 class="m-0">
            <i class="fas fa-heartbeat text-primary mr-2"></i>
            Procedimientos
        </h1>

        <small class="text-muted">
            Administración de procedimientos médicos
        </small>
    </div>

    <button
        type="button"
        class="btn btn-primary mt-2 mt-md-0"
        data-toggle="modal"
        data-target="#modalCrearProcedimiento">
        <i class="fas fa-plus-circle mr-1"></i>
        Nuevo procedimiento
    </button>

</div>

@stop

@section('content')

<div class="container-fluid">

    {{-- Mensajes satisfactorios --}}
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
                action="{{ route('procedimientos.tipos.index') }}">
                <div class="row">

                    <div class="col-md-5">
                        <div class="form-group mb-md-0">

                            <label for="servicio_id">
                                Servicio
                            </label>

                            <select
                                name="servicio_id"
                                id="servicio_id"
                                class="form-control">
                                <option value="">
                                    Todos los servicios
                                </option>

                                @foreach ($servicios as $servicio)
                                <option
                                    value="{{ $servicio->id }}"
                                    {{ (string) request('servicio_id') ===
                                                (string) $servicio->id
                                                ? 'selected'
                                                : '' }}>
                                    {{ $servicio->nombre }}

                                    @if (!$servicio->activo)
                                    - INACTIVO
                                    @endif
                                </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group mb-md-0">

                            <label for="buscar">
                                Código o procedimiento
                            </label>

                            <input
                                type="text"
                                name="buscar"
                                id="buscar"
                                class="form-control"
                                value="{{ request('buscar') }}"
                                placeholder="Buscar...">

                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <div class="form-group mb-0 w-100">

                            <button
                                type="submit"
                                class="btn btn-primary"
                                title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>

                            <a
                                href="{{ route(
                                        'procedimientos.tipos.index'
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
                Procedimientos registrados
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    Total: {{ $procedimientos->total() }}
                </span>
            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th style="width: 60px;">
                                N.°
                            </th>

                            <th style="width: 110px;">
                                Código
                            </th>

                            <th>
                                Servicio
                            </th>

                            <th>
                                Procedimiento
                            </th>

                            <th class="text-center" style="width: 100px;">
                                Duración
                            </th>

                            <th class="text-center" style="width: 80px;">
                                Color
                            </th>

                            <th class="text-center" style="width: 110px;">
                                Estado
                            </th>

                            <th class="text-center" style="width: 150px;">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($procedimientos as $procedimiento)

                        <tr>

                            <td class="align-middle">
                                {{ $procedimientos->firstItem()
                                            + $loop->index }}
                            </td>

                            <td class="align-middle">
                                <span class="badge badge-info">
                                    {{ $procedimiento->codigo }}
                                </span>
                            </td>

                            <td class="align-middle">
                                {{ $procedimiento->servicio->nombre
                                            ?? 'SIN SERVICIO' }}
                            </td>

                            <td class="align-middle">

                                <div class="font-weight-bold">
                                    {{ $procedimiento->nombre }}
                                </div>

                                @if ($procedimiento->descripcion)
                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit(
                                                    $procedimiento->descripcion,
                                                    70
                                                ) }}
                                </small>
                                @endif

                            </td>

                            <td class="text-center align-middle">
                                <span class="badge badge-light">
                                    {{ $procedimiento->duracion_minutos }}
                                    min
                                </span>
                            </td>

                            <td class="text-center align-middle">

                                <span
                                    style="
                                                display: inline-block;
                                                width: 30px;
                                                height: 30px;
                                                border-radius: 50%;
                                                border: 1px solid #999;
                                                background-color:
                                                    {{ $procedimiento->color }};
                                            "
                                    title="{{ $procedimiento->color }}"></span>

                            </td>

                            <td class="text-center align-middle">

                                @if ($procedimiento->activo)
                                <span class="badge badge-success px-2 py-2">
                                    ACTIVO
                                </span>
                                @else
                                <span class="badge badge-secondary px-2 py-2">
                                    INACTIVO
                                </span>
                                @endif

                            </td>

                            <td class="text-center align-middle">

                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEditarProcedimiento{{ $procedimiento->id }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route(
                                                'procedimientos.tipos.estado',
                                                $procedimiento
                                            ) }}"
                                    class="d-inline formulario-estado">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-sm
                                                    {{ $procedimiento->activo
                                                        ? 'btn-danger'
                                                        : 'btn-success' }}"
                                        data-nombre="{{ $procedimiento->nombre }}"
                                        data-accion="{{ $procedimiento->activo
                                                    ? 'desactivar'
                                                    : 'activar' }}"
                                        title="{{ $procedimiento->activo
                                                    ? 'Desactivar'
                                                    : 'Activar' }}">
                                        <i class="fas
                                                    {{ $procedimiento->activo
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
                                colspan="8"
                                class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-3x mb-3"></i>

                                <div>
                                    No existen procedimientos registrados.
                                </div>
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($procedimientos->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $procedimientos->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Modal crear --}}
<div
    class="modal fade"
    id="modalCrearProcedimiento"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('procedimientos.tipos.store') }}"
                id="formCrearProcedimiento">
                @csrf

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="crear">

                <div class="modal-header bg-primary">

                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Registrar procedimiento
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

                        <div class="col-md-8">
                            <div class="form-group">

                                <label for="crear_servicio_id">
                                    Servicio
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="procedimiento_servicio_id"
                                    id="crear_servicio_id"
                                    class="form-control"
                                    required>
                                    <option value="">
                                        Seleccione...
                                    </option>

                                    @foreach ($servicios->where('activo', true)
                                    as $servicio)
                                    <option
                                        value="{{ $servicio->id }}"
                                        {{ (string) old(
                                                    'procedimiento_servicio_id'
                                                ) === (string) $servicio->id
                                                    ? 'selected'
                                                    : '' }}>
                                        {{ $servicio->nombre }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">

                                <label for="crear_codigo">
                                    Código
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="codigo"
                                    id="crear_codigo"
                                    class="form-control texto-mayuscula"
                                    value="{{ old('codigo') }}"
                                    maxlength="30"
                                    placeholder="Ejemplo: ECG"
                                    required>

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label for="crear_nombre">
                            Nombre del procedimiento
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            id="crear_nombre"
                            class="form-control texto-mayuscula"
                            value="{{ old('nombre') }}"
                            maxlength="150"
                            placeholder="Ejemplo: ELECTROCARDIOGRAMA"
                            required>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="crear_duracion">
                                    Duración predeterminada
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        name="duracion_minutos"
                                        id="crear_duracion"
                                        class="form-control"
                                        value="{{ old(
                                                'duracion_minutos',
                                                15
                                            ) }}"
                                        min="5"
                                        max="480"
                                        required>

                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            minutos
                                        </span>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="crear_color">
                                    Color del calendario
                                </label>

                                <input
                                    type="color"
                                    name="color"
                                    id="crear_color"
                                    class="form-control"
                                    value="{{ old('color', '#007BFF') }}"
                                    required>

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label for="crear_descripcion">
                            Descripción
                        </label>

                        <textarea
                            name="descripcion"
                            id="crear_descripcion"
                            class="form-control"
                            rows="2"
                            maxlength="1000"
                            placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>

                    </div>

                    <div class="form-group mb-0">

                        <label for="crear_indicaciones">
                            Indicaciones para el paciente
                        </label>

                        <textarea
                            name="indicaciones"
                            id="crear_indicaciones"
                            class="form-control"
                            rows="3"
                            maxlength="2000"
                            placeholder="Ejemplo: acudir 15 minutos antes...">{{ old('indicaciones') }}</textarea>

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

{{-- Modales editar --}}
@foreach ($procedimientos as $procedimiento)

<div
    class="modal fade"
    id="modalEditarProcedimiento{{ $procedimiento->id }}"
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
                            'procedimientos.tipos.update',
                            $procedimiento
                        ) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="editar-{{ $procedimiento->id }}">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Editar procedimiento
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

                        <div class="col-md-8">
                            <div class="form-group">

                                <label>
                                    Servicio
                                </label>

                                <select
                                    name="procedimiento_servicio_id"
                                    class="form-control"
                                    required>
                                    @foreach ($servicios->where(
                                    'activo',
                                    true
                                    ) as $servicio)
                                    <option
                                        value="{{ $servicio->id }}"
                                        {{ $procedimiento
                                                        ->procedimiento_servicio_id
                                                        == $servicio->id
                                                        ? 'selected'
                                                        : '' }}>
                                        {{ $servicio->nombre }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">

                                <label>
                                    Código
                                </label>

                                <input
                                    type="text"
                                    name="codigo"
                                    class="form-control texto-mayuscula"
                                    value="{{ $procedimiento->codigo }}"
                                    maxlength="30"
                                    required>

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label>
                            Nombre del procedimiento
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control texto-mayuscula"
                            value="{{ $procedimiento->nombre }}"
                            maxlength="150"
                            required>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>
                                    Duración
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        name="duracion_minutos"
                                        class="form-control"
                                        value="{{ $procedimiento
                                                    ->duracion_minutos }}"
                                        min="5"
                                        max="480"
                                        required>

                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            minutos
                                        </span>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>
                                    Color
                                </label>

                                <input
                                    type="color"
                                    name="color"
                                    class="form-control"
                                    value="{{ $procedimiento->color }}"
                                    required>

                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label>
                            Descripción
                        </label>

                        <textarea
                            name="descripcion"
                            class="form-control"
                            rows="2"
                            maxlength="1000">{{ $procedimiento->descripcion }}</textarea>

                    </div>

                    <div class="form-group mb-0">

                        <label>
                            Indicaciones para el paciente
                        </label>

                        <textarea
                            name="indicaciones"
                            class="form-control"
                            rows="3"
                            maxlength="2000">{{ $procedimiento->indicaciones }}</textarea>

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

        const formularioOrigen =
            @json(old('formulario_origen'));

        @if($errors -> any())
        if (
            formularioOrigen &&
            formularioOrigen.startsWith('editar-')
        ) {
            const id = formularioOrigen.replace('editar-', '');

            $('#modalEditarProcedimiento' + id).modal('show');
        } else {
            $('#modalCrearProcedimiento').modal('show');
        }
        @endif

        document.querySelectorAll('.texto-mayuscula')
            .forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });

        document.querySelectorAll('.formulario-estado')
            .forEach(function(formulario) {

                formulario.addEventListener(
                    'submit',
                    function(event) {

                        const boton = formulario.querySelector(
                            'button[type="submit"]'
                        );

                        const accion = boton.dataset.accion;
                        const nombre = boton.dataset.nombre;

                        const confirmar = confirm(
                            '¿Está seguro de ' +
                            accion +
                            ' el procedimiento "' +
                            nombre +
                            '"?'
                        );

                        if (!confirmar) {
                            event.preventDefault();
                        }
                    }
                );
            });

    });
</script>
@stop