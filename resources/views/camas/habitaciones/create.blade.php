@extends('adminlte::page')

@section('title', 'Nueva Habitación')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-plus-circle mr-2"></i>

            Nueva Habitación

        </h1>

        <small class="text-muted">

            Registrar una habitación hospitalaria

        </small>

    </div>

    <a href="{{ route('camas.habitaciones.index') }}"
        class="btn btn-outline-secondary">

        <i class="fas fa-arrow-left mr-1"></i>

        Volver

    </a>

</div>

@stop

@section('content')

<div class="row justify-content-center">

    <div class="col-lg-9">

        <div class="card shadow-sm">

            <form method="POST"
                action="{{ route('camas.habitaciones.store') }}">

                @csrf

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Departamento

                                    <span class="text-danger">*</span>

                                </label>

                                <select id="departamento_id"
                                    class="form-control"
                                    required>

                                    <option value="">

                                        Seleccione...

                                    </option>

                                    @foreach($departamentos as $departamento)

                                    <option value="{{ $departamento->id }}">

                                        {{ $departamento->nombre }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Servicio

                                    <span class="text-danger">*</span>

                                </label>

                                <select id="servicio_id"
                                    name="servicio_id"
                                    class="form-control
                                        @error('servicio_id')
                                            is-invalid
                                        @enderror"
                                    required>

                                    <option value="">

                                        Seleccione primero un departamento...

                                    </option>

                                </select>

                                @error('servicio_id')

                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>

                                @enderror

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-5">

                            <div class="form-group">

                                <label>

                                    Nombre de la Habitación

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    required
                                    maxlength="100"
                                    class="form-control
                                       @error('nombre')
                                           is-invalid
                                       @enderror"
                                    placeholder="Ej. Habitación 101">

                                @error('nombre')

                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Número
                                </label>

                                <input type="text"
                                    name="numero"
                                    value="{{ old('numero') }}"
                                    maxlength="30"
                                    class="form-control"
                                    placeholder="101">

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Piso
                                </label>

                                <input type="text"
                                    name="piso"
                                    value="{{ old('piso') }}"
                                    maxlength="50"
                                    class="form-control"
                                    placeholder="2do Piso">

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>

                            Observaciones

                        </label>

                        <textarea name="observaciones"
                            rows="4"
                            class="form-control"
                            placeholder="Observaciones opcionales...">{{ old('observaciones') }}</textarea>

                    </div>

                    <div class="custom-control custom-switch">

                        <input type="checkbox"
                            id="activo"
                            name="activo"
                            value="1"
                            class="custom-control-input"
                            {{ old('activo', true)
                                    ? 'checked'
                                    : '' }}>

                        <label class="custom-control-label"
                            for="activo">

                            Habitación activa

                        </label>

                    </div>

                </div>

                <div class="card-footer bg-white text-right">

                    <a href="{{ route(
                        'camas.habitaciones.index'
                    ) }}"
                        class="btn btn-outline-secondary mr-2">

                        Cancelar

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Guardar Habitación

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
    const departamentoSelect =
        document.getElementById('departamento_id');

    const servicioSelect =
        document.getElementById('servicio_id');

    const servicioAnterior =
        @json(old('servicio_id'));

    departamentoSelect
        .addEventListener(
            'change',
            cargarServicios
        );

    function cargarServicios() {

        const departamentoId =
            departamentoSelect.value;

        servicioSelect.innerHTML =
            '<option value="">Cargando...</option>';

        if (!departamentoId) {

            servicioSelect.innerHTML =
                '<option value="">Seleccione primero un departamento...</option>';

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
                        'Error al obtener servicios.'
                    );

                }

                return response.json();

            })
            .then(data => {

                servicioSelect.innerHTML =
                    '<option value="">Seleccione...</option>';

                data.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value = item.id;

                    option.textContent =
                        item.nombre;

                    if (
                        String(item.id) ===
                        String(servicioAnterior)
                    ) {

                        option.selected = true;

                    }

                    servicioSelect.appendChild(
                        option
                    );

                });

            })
            .catch(error => {

                console.error(error);

                servicioSelect.innerHTML =
                    '<option value="">No se pudieron cargar los servicios</option>';

            });

    }
</script>

@stop