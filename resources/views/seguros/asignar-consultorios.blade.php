@extends('adminlte::page')

@section('title', 'Asignar consultorios | Seguros')

@section('content_header')
<h1>
    Seguros
    <small class="text-muted">/ Asignar consultorios</small>
</h1>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('seguros.consultorios.index') }}"
                class="form-inline">
                <label for="fecha" class="mr-2">Fecha</label>

                <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    value="{{ $fecha }}"
                    class="form-control mr-2"
                    required>

                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search mr-1"></i>
                    Cargar programaciones
                </button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('seguros.consultorios.store') }}">
        @csrf

        <input type="hidden" name="fecha" value="{{ $fecha }}">

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center">
                    <button
                        type="submit"
                        name="accion"
                        value="guardar"
                        class="btn btn-success mr-2 mb-2">
                        <i class="fas fa-save mr-1"></i>
                        Guardar asignaciones
                    </button>

                    <button
                        type="submit"
                        name="accion"
                        value="aplicar_habituales"
                        class="btn btn-info mr-2 mb-2">
                        Aplicar habituales al día
                    </button>

                    <button
                        type="submit"
                        name="accion"
                        value="guardar_habituales"
                        class="btn btn-outline-primary mb-2">
                        Guardar seleccionados como habituales
                    </button>
                </div>

                <hr>

                <div class="d-flex flex-wrap align-items-center">
                    <input
                        type="text"
                        name="numero_masivo"
                        value="{{ old('numero_masivo') }}"
                        maxlength="20"
                        placeholder="Ejemplo: 101 o A-03"
                        class="form-control mr-2 mb-2"
                        style="max-width: 240px;"
                        aria-label="Número de consultorio para asignación masiva">

                    <button
                        type="submit"
                        name="accion"
                        value="masiva"
                        class="btn btn-primary mb-2">
                        Aplicar a seleccionados pendientes
                    </button>
                </div>

                <p class="text-muted small mb-0">
                    Las acciones masivas completan solo asignaciones pendientes.
                    Para corregir una asignación existente, edite su número y
                    pulse Guardar asignaciones.
                    Dejar el campo vacío y guardar elimina la asignación diaria.
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">
                                <input
                                    type="checkbox"
                                    id="seleccionarTodos"
                                    aria-label="Seleccionar todas las programaciones">
                            </th>
                            <th>Servicio</th>
                            <th>Médico</th>
                            <th>Horario</th>
                            <th>Consultorio del día</th>
                            <th>Habitual</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($programaciones as $p)
                        @php
                        $medico = trim(implode(' ', array_filter([
                        $p->ApellidoPaterno,
                        $p->ApellidoMaterno,
                        $p->Nombres,
                        ])));

                        $seleccionados = array_map(
                        'strval',
                        old('seleccionados', [])
                        );
                        @endphp

                        <tr>
                            <td class="text-center align-middle">
                                <input
                                    type="checkbox"
                                    name="seleccionados[]"
                                    value="{{ $p->IdProgramacion }}"
                                    class="seleccion-programacion"
                                    aria-label="Seleccionar programación {{ $p->IdProgramacion }}"
                                    @checked(in_array(
                                    (string) $p->IdProgramacion,
                                $seleccionados,
                                true
                                ))
                                >
                            </td>

                            <td class="align-middle">
                                <strong>{{ $p->Servicio }}</strong>
                            </td>

                            <td class="align-middle">
                                {{ $medico ?: 'SIN MÉDICO REGISTRADO' }}
                            </td>

                            <td class="text-nowrap align-middle">
                                {{ $p->HoraInicio }} – {{ $p->HoraFin }}
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="numeros[{{ $p->IdProgramacion }}]"
                                    value="{{ old(
                                            'numeros.'.$p->IdProgramacion,
                                            $asignaciones->get($p->IdProgramacion, '')
                                        ) }}"
                                    maxlength="20"
                                    placeholder="Por asignar"
                                    class="form-control"
                                    aria-label="Consultorio para {{ $p->Servicio }}">
                            </td>

                            <td class="align-middle">
                                {{ $habituales->get($p->IdServicio, '—') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay programaciones para esta fecha.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const todos = document.getElementById('seleccionarTodos');
        const filas = Array.from(
            document.querySelectorAll('.seleccion-programacion')
        );

        function actualizarSeleccion() {
            const marcados = filas.filter(fila => fila.checked).length;
            todos.checked = filas.length > 0 && marcados === filas.length;
            todos.indeterminate = marcados > 0 && marcados < filas.length;
        }

        todos.addEventListener('change', function() {
            filas.forEach(fila => fila.checked = todos.checked);
            actualizarSeleccion();
        });

        filas.forEach(fila => {
            fila.addEventListener('change', actualizarSeleccion);
        });

        actualizarSeleccion();
    });
</script>
@stop