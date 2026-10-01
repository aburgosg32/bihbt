@extends('adminlte::page')

@section('title', 'Programación de procedimientos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">

    <div>
        <h1 class="m-0">
            <i class="fas fa-calendar-alt text-primary mr-2"></i>
            Programación de procedimientos
        </h1>

        <small class="text-muted">
            Programación por fecha, horario y médico responsable
        </small>
    </div>

    <button
        type="button"
        class="btn btn-primary mt-2 mt-md-0"
        data-toggle="modal"
        data-target="#modalCrearProgramacion">
        <i class="fas fa-plus-circle mr-1"></i>
        Nueva programación
    </button>

</div>
@stop

@section('content')
<div class="container-fluid">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

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

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i>
                Filtros
            </h3>
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('procedimientos.programaciones.index') }}">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
                            <label>Procedimiento</label>

                            <select
                                name="procedimiento_id"
                                class="form-control">
                                <option value="">Todos</option>

                                @foreach ($procedimientos as $procedimiento)
                                <option
                                    value="{{ $procedimiento->id }}"
                                    {{ (string) request('procedimiento_id') ===
                                            (string) $procedimiento->id
                                            ? 'selected'
                                            : '' }}>
                                    {{ $procedimiento->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-md-0">
                            <label>Médico</label>

                            <select
                                name="medico_id"
                                class="form-control">
                                <option value="">Todos</option>

                                @foreach ($medicos as $medico)
                                <option
                                    value="{{ $medico->id }}"
                                    {{ (string) request('medico_id') ===
                                            (string) $medico->id
                                            ? 'selected'
                                            : '' }}>
                                    {{ $medico->nombre_completo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-md-0">
                            <label>Fecha</label>

                            <input
                                type="date"
                                name="fecha"
                                class="form-control"
                                value="{{ request('fecha') }}">
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-0">

                            <button
                                type="submit"
                                class="btn btn-primary"
                                title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>

                            <a
                                href="{{ route(
                                    'procedimientos.programaciones.index'
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
                <i class="fas fa-calendar-check mr-1"></i>
                Horarios programados
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    Total: {{ $programaciones->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th style="width: 60px;">N.°</th>
                            <th>Servicio / Procedimiento</th>
                            <th>Médico</th>
                            <th style="width: 130px;">Fecha</th>

                            <th
                                class="text-center"
                                style="width: 130px;">
                                Horario
                            </th>

                            <th
                                class="text-center"
                                style="width: 100px;">
                                Intervalo
                            </th>

                            <th
                                class="text-center"
                                style="width: 90px;">
                                Cupos
                            </th>

                            <th
                                class="text-center"
                                style="width: 100px;">
                                Estado
                            </th>

                            <th
                                class="text-center"
                                style="width: 130px;">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($programaciones as $programacion)

                        <tr>

                            <td class="align-middle">
                                {{ $programaciones->firstItem()
                                        + $loop->index }}
                            </td>

                            <td class="align-middle">

                                <small class="text-muted">
                                    {{ $programacion
                                            ->procedimiento
                                            ->servicio
                                            ->nombre ?? 'SIN SERVICIO' }}
                                </small>

                                <div class="font-weight-bold">
                                    {{ $programacion
                                            ->procedimiento
                                            ->nombre }}
                                </div>

                            </td>

                            <td class="align-middle">
                                <div class="font-weight-bold">
                                    {{ $programacion
                                            ->medico
                                            ->nombre_completo }}
                                </div>

                                <small class="text-muted">
                                    {{ $programacion
                                            ->medico
                                            ->especialidad }}
                                </small>
                            </td>

                            <td class="align-middle">

                                <div class="font-weight-bold">
                                    {{ $programacion
                                            ->fecha_programacion
                                            ->format('d/m/Y') }}
                                </div>

                                <small class="text-muted">
                                    {{ mb_strtoupper(
                                            $programacion
                                                ->fecha_programacion
                                                ->locale('es')
                                                ->translatedFormat('l')
                                        ) }}
                                </small>

                            </td>

                            <td class="text-center align-middle">
                                {{ substr(
                                        $programacion->hora_inicio,
                                        0,
                                        5
                                    ) }}

                                -

                                {{ substr(
                                        $programacion->hora_fin,
                                        0,
                                        5
                                    ) }}
                            </td>

                            <td class="text-center align-middle">
                                <span class="badge badge-info">
                                    {{ $programacion
                                            ->intervalo_minutos }} min
                                </span>
                            </td>

                            <td class="text-center align-middle">
                                {{ $programacion->cantidad_maxima
                                        ?? 'AUTOMÁTICO' }}
                            </td>

                            <td class="text-center align-middle">
                                @if ($programacion->activo)
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
                                    data-target="#modalEditar{{ $programacion->id }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route(
                                            'procedimientos.programaciones.estado',
                                            $programacion
                                        ) }}"
                                    class="d-inline formulario-estado">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-sm
                                                {{ $programacion->activo
                                                    ? 'btn-danger'
                                                    : 'btn-success' }}"
                                        data-accion="{{ $programacion->activo
                                                ? 'desactivar'
                                                : 'activar' }}"
                                        title="{{ $programacion->activo
                                                ? 'Desactivar'
                                                : 'Activar' }}">
                                        <i class="fas
                                                {{ $programacion->activo
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
                                colspan="9"
                                class="text-center text-muted py-5">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>

                                <div>
                                    No existen programaciones registradas.
                                </div>
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($programaciones->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $programaciones->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Modal registrar --}}
<div
    class="modal fade"
    id="modalCrearProgramacion"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route(
                    'procedimientos.programaciones.store'
                ) }}">
                @csrf

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="crear">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        Registrar programación
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @include(
                    'procedimientos.programaciones.partials.formulario',
                    [
                    'registro' => null
                    ]
                    )
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
@foreach ($programaciones as $programacion)

<div
    class="modal fade"
    id="modalEditar{{ $programacion->id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form
                method="POST"
                action="{{ route(
                        'procedimientos.programaciones.update',
                        $programacion
                    ) }}">
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="formulario_origen"
                    value="editar-{{ $programacion->id }}">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Editar programación
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @include(
                    'procedimientos.programaciones.partials.formulario',
                    [
                    'registro' => $programacion
                    ]
                    )
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

        const origen = @json(old('formulario_origen'));

        @if($errors -> any())
        if (origen && origen.startsWith('editar-')) {
            const id = origen.replace('editar-', '');

            $('#modalEditar' + id).modal('show');
        } else {
            $('#modalCrearProgramacion').modal('show');
        }
        @endif

        document.querySelectorAll('.formulario-estado')
            .forEach(function(formulario) {

                formulario.addEventListener(
                    'submit',
                    function(evento) {

                        const boton = formulario.querySelector(
                            'button[type="submit"]'
                        );

                        const confirmar = confirm(
                            '¿Está seguro de ' +
                            boton.dataset.accion +
                            ' esta programación?'
                        );

                        if (!confirmar) {
                            evento.preventDefault();
                        }
                    }
                );

            });

    });
</script>
@stop