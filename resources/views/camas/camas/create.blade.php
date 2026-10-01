@extends('adminlte::page')

@section('title', 'Nueva Cama')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-plus-circle mr-2"></i>
            Nueva Cama

        </h1>

        <small class="text-muted">
            Registrar una cama hospitalaria
        </small>

    </div>

    <a href="{{ route('camas.camas.index') }}"
        class="btn btn-outline-secondary">

        <i class="fas fa-arrow-left mr-1"></i>
        Volver

    </a>

</div>

@stop

@section('content')

<div class="row justify-content-center">

    <div class="col-lg-10">

        <div class="card shadow-sm">

            <form method="POST"
                action="{{ route('camas.camas.store') }}">

                @csrf

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">

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

                        <div class="col-md-4">

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

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Habitación
                                </label>

                                <select id="habitacion_id"
                                    name="habitacion_id"
                                    class="form-control">

                                    <option value="">
                                        Seleccione primero un servicio...
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Código
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    name="codigo"
                                    value="{{ old('codigo') }}"
                                    maxlength="50"
                                    required
                                    class="form-control
                                       @error('codigo')
                                           is-invalid
                                       @enderror"
                                    placeholder="Ej. MED-A-101-01">

                                @error('codigo')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Número de cama
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    name="numero"
                                    value="{{ old('numero') }}"
                                    maxlength="50"
                                    required
                                    class="form-control
                                       @error('numero')
                                           is-invalid
                                       @enderror"
                                    placeholder="Ej. 101-A">

                                @error('numero')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Tipo de cama
                                </label>

                                <input type="text"
                                    name="tipo_cama"
                                    value="{{ old('tipo_cama') }}"
                                    maxlength="100"
                                    class="form-control"
                                    placeholder="Ej. Hospitalización">

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Estado operativo
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="estado_operativo"
                                    class="form-control"
                                    required>

                                    <option value="OPERATIVA">
                                        Operativa
                                    </option>

                                    <option value="LIMPIEZA">
                                        Limpieza
                                    </option>

                                    <option value="MANTENIMIENTO">
                                        Mantenimiento
                                    </option>

                                    <option value="BLOQUEADA">
                                        Bloqueada
                                    </option>

                                    <option value="FUERA_DE_SERVICIO">
                                        Fuera de servicio
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Sexo permitido
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="sexo_permitido"
                                    class="form-control"
                                    required>

                                    <option value="INDISTINTO">
                                        Indistinto
                                    </option>

                                    <option value="MASCULINO">
                                        Masculino
                                    </option>

                                    <option value="FEMENINO">
                                        Femenino
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>
                            Observaciones
                        </label>

                        <textarea name="observaciones"
                            rows="3"
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

                            Cama activa

                        </label>

                    </div>

                </div>

                <div class="card-footer bg-white text-right">

                    <a href="{{ route('camas.camas.index') }}"
                        class="btn btn-outline-secondary mr-2">

                        Cancelar

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>
                        Guardar Cama

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
    const departamento =
        document.getElementById('departamento_id');

    const servicio =
        document.getElementById('servicio_id');

    const habitacion =
        document.getElementById('habitacion_id');

    const servicioAnterior =
        @json(old('servicio_id'));

    const habitacionAnterior =
        @json(old('habitacion_id'));

    departamento.addEventListener(
        'change',
        cargarServicios
    );

    servicio.addEventListener(
        'change',
        cargarHabitaciones
    );

    function cargarServicios() {

        servicio.innerHTML =
            '<option value="">Cargando...</option>';

        habitacion.innerHTML =
            '<option value="">Seleccione primero un servicio...</option>';

        if (!departamento.value) {

            servicio.innerHTML =
                '<option value="">Seleccione primero un departamento...</option>';

            return;

        }

        fetch(
                '{{ url("/camas/departamentos") }}/' +
                departamento.value +
                '/servicios'
            )
            .then(response => response.json())
            .then(data => {

                servicio.innerHTML =
                    '<option value="">Seleccione...</option>';

                data.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.nombre;

                    if (
                        String(item.id) ===
                        String(servicioAnterior)
                    ) {
                        option.selected = true;
                    }

                    servicio.appendChild(option);

                });

                if (servicio.value) {
                    cargarHabitaciones();
                }

            });

    }

    function cargarHabitaciones() {

        habitacion.innerHTML =
            '<option value="">Cargando...</option>';

        if (!servicio.value) {

            habitacion.innerHTML =
                '<option value="">Seleccione primero un servicio...</option>';

            return;

        }

        fetch(
                '{{ url("/camas/servicios") }}/' +
                servicio.value +
                '/habitaciones'
            )
            .then(response => response.json())
            .then(data => {

                habitacion.innerHTML =
                    '<option value="">Sin habitación específica</option>';

                data.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.nombre;

                    if (
                        String(item.id) ===
                        String(habitacionAnterior)
                    ) {
                        option.selected = true;
                    }

                    habitacion.appendChild(option);

                });

            });

    }
</script>

@stop