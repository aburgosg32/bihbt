@extends('adminlte::page')

@section('title', 'Editar Habitación')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-edit mr-2"></i>

            Editar Habitación

        </h1>

        <small class="text-muted">

            Modificar información de la habitación

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
                action="{{ route(
                    'camas.habitaciones.update',
                    $habitacion
                  ) }}">

                @csrf
                @method('PUT')

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

                                    @foreach($departamentos as $departamento)

                                    <option value="{{ $departamento->id }}"
                                        {{ old(
                                            'departamento_id',
                                            $habitacion
                                                ->servicio
                                                ?->departamento_id
                                        ) == $departamento->id
                                            ? 'selected'
                                            : '' }}>

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
                                    class="form-control"
                                    required>

                                    @foreach(
                                    $servicios->where(
                                    'departamento_id',
                                    old(
                                    'departamento_id',
                                    $habitacion
                                    ->servicio
                                    ?->departamento_id
                                    )
                                    )
                                    as $servicio
                                    )

                                    <option value="{{ $servicio->id }}"
                                        {{ old(
                                            'servicio_id',
                                            $habitacion->servicio_id
                                        ) == $servicio->id
                                            ? 'selected'
                                            : '' }}>

                                        {{ $servicio->nombre }}

                                    </option>

                                    @endforeach

                                </select>

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
                                    value="{{ old(
                                            'nombre',
                                            $habitacion->nombre
                                       ) }}"
                                    required
                                    class="form-control">

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Número
                                </label>

                                <input type="text"
                                    name="numero"
                                    value="{{ old(
                                            'numero',
                                            $habitacion->numero
                                       ) }}"
                                    class="form-control">

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>
                                    Piso
                                </label>

                                <input type="text"
                                    name="piso"
                                    value="{{ old(
                                            'piso',
                                            $habitacion->piso
                                       ) }}"
                                    class="form-control">

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>
                            Observaciones
                        </label>

                        <textarea name="observaciones"
                            rows="4"
                            class="form-control">{{ old(
                                    'observaciones',
                                    $habitacion->observaciones
                                  ) }}</textarea>

                    </div>

                    <div class="custom-control custom-switch">

                        <input type="checkbox"
                            id="activo"
                            name="activo"
                            value="1"
                            class="custom-control-input"
                            {{ old(
                                    'activo',
                                    $habitacion->activo
                               ) ? 'checked' : '' }}>

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

                        Actualizar Habitación

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
        document.getElementById(
            'departamento_id'
        );

    const servicio =
        document.getElementById(
            'servicio_id'
        );

    const servicioActual =
        @json(
            old(
                'servicio_id',
                $habitacion - > servicio_id
            )
        );

    departamento.addEventListener(
        'change',
        cargarServicios
    );

    function cargarServicios() {

        const departamentoId =
            departamento.value;

        servicio.innerHTML =
            '<option value="">Cargando...</option>';

        fetch(
                '{{ url("/camas/departamentos") }}/' +
                departamentoId +
                '/servicios'
            )
            .then(response =>
                response.json()
            )
            .then(data => {

                servicio.innerHTML =
                    '<option value="">Seleccione...</option>';

                data.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value =
                        item.id;

                    option.textContent =
                        item.nombre;

                    if (
                        String(item.id) ===
                        String(servicioActual)
                    ) {

                        option.selected = true;

                    }

                    servicio.appendChild(
                        option
                    );

                });

            });

    }
</script>

@stop