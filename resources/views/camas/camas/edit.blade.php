```blade
@extends('adminlte::page')

@section('title', 'Editar Cama')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-edit mr-2"></i>

            Editar Cama

        </h1>

        <small class="text-muted">

            Modificar la información de la cama hospitalaria

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
                  action="{{ route(
                      'camas.camas.update',
                      $cama
                  ) }}">

                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- DEPARTAMENTO / SERVICIO / HABITACIÓN --}}

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

                                        <option value="{{ $departamento->id }}"
                                            {{ old(
                                                'departamento_id',
                                                $cama->servicio?->departamento_id
                                            ) == $departamento->id
                                                ? 'selected'
                                                : '' }}>

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

                                        Seleccione...

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
                                        class="form-control
                                        @error('habitacion_id')
                                            is-invalid
                                        @enderror">

                                    <option value="">

                                        Seleccione...

                                    </option>

                                </select>

                                @error('habitacion_id')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- CÓDIGO / NÚMERO / TIPO --}}

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">

                                <label>

                                    Código

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text"
                                       name="codigo"
                                       value="{{ old(
                                            'codigo',
                                            $cama->codigo
                                       ) }}"
                                       maxlength="50"
                                       required
                                       class="form-control
                                       @error('codigo')
                                           is-invalid
                                       @enderror"
                                       placeholder="Ej. MED-A-101-A">

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
                                       value="{{ old(
                                            'numero',
                                            $cama->numero
                                       ) }}"
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
                                       value="{{ old(
                                            'tipo_cama',
                                            $cama->tipo_cama
                                       ) }}"
                                       maxlength="100"
                                       class="form-control
                                       @error('tipo_cama')
                                           is-invalid
                                       @enderror"
                                       placeholder="Ej. Hospitalización">

                                @error('tipo_cama')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- ESTADO OPERATIVO / SEXO PERMITIDO --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Estado operativo

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="estado_operativo"
                                        class="form-control
                                        @error('estado_operativo')
                                            is-invalid
                                        @enderror"
                                        required>

                                    @foreach([
                                        'OPERATIVA' => 'Operativa',
                                        'LIMPIEZA' => 'Limpieza',
                                        'MANTENIMIENTO' => 'Mantenimiento',
                                        'BLOQUEADA' => 'Bloqueada',
                                        'FUERA_DE_SERVICIO' => 'Fuera de servicio',
                                    ] as $valor => $texto)

                                        <option value="{{ $valor }}"
                                            {{ old(
                                                'estado_operativo',
                                                $cama->estado_operativo
                                            ) === $valor
                                                ? 'selected'
                                                : '' }}>

                                            {{ $texto }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('estado_operativo')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Sexo permitido

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="sexo_permitido"
                                        class="form-control
                                        @error('sexo_permitido')
                                            is-invalid
                                        @enderror"
                                        required>

                                    @foreach([
                                        'INDISTINTO' => 'Indistinto',
                                        'MASCULINO' => 'Masculino',
                                        'FEMENINO' => 'Femenino',
                                    ] as $valor => $texto)

                                        <option value="{{ $valor }}"
                                            {{ old(
                                                'sexo_permitido',
                                                $cama->sexo_permitido
                                            ) === $valor
                                                ? 'selected'
                                                : '' }}>

                                            {{ $texto }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('sexo_permitido')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- OBSERVACIONES --}}

                    <div class="form-group">

                        <label>

                            Observaciones

                        </label>

                        <textarea name="observaciones"
                                  rows="3"
                                  class="form-control
                                  @error('observaciones')
                                      is-invalid
                                  @enderror"
                                  placeholder="Observaciones opcionales...">{{ old(
                                      'observaciones',
                                      $cama->observaciones
                                  ) }}</textarea>

                        @error('observaciones')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    {{-- ESTADO ACTIVO --}}

                    <div class="custom-control custom-switch">

                        <input type="checkbox"
                               id="activo"
                               name="activo"
                               value="1"
                               class="custom-control-input"
                               {{ old(
                                   'activo',
                                   $cama->activo
                               ) ? 'checked' : '' }}>

                        <label class="custom-control-label"
                               for="activo">

                            Cama activa

                        </label>

                    </div>

                </div>


                {{-- BOTONES --}}

                <div class="card-footer bg-white text-right">

                    <a href="{{ route('camas.camas.index') }}"
                       class="btn btn-outline-secondary mr-2">

                        Cancelar

                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Actualizar Cama

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

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const departamento =
            document.getElementById(
                'departamento_id'
            );

        const servicio =
            document.getElementById(
                'servicio_id'
            );

        const habitacion =
            document.getElementById(
                'habitacion_id'
            );


        /*
        |--------------------------------------------------------------------------
        | Valores actuales
        |--------------------------------------------------------------------------
        */

        const departamentoActual =
            @json(
                old(
                    'departamento_id',
                    $cama->servicio?->departamento_id
                )
            );

        const servicioActual =
            @json(
                old(
                    'servicio_id',
                    $cama->servicio_id
                )
            );

        const habitacionActual =
            @json(
                old(
                    'habitacion_id',
                    $cama->habitacion_id
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Cargar servicios
        |--------------------------------------------------------------------------
        */

        function cargarServicios(
            seleccionarActual = false
        ) {

            const departamentoId =
                departamento.value;

            servicio.innerHTML =
                '<option value="">Cargando...</option>';

            habitacion.innerHTML =
                '<option value="">Seleccione primero un servicio...</option>';


            if (!departamentoId) {

                servicio.innerHTML =
                    '<option value="">Seleccione primero un departamento...</option>';

                return Promise.resolve();

            }


            return fetch(
                '{{ url("/camas/departamentos") }}/' +
                departamentoId +
                '/servicios'
            )
            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'No se pudieron cargar los servicios.'
                    );

                }

                return response.json();

            })
            .then(data => {

                servicio.innerHTML =
                    '<option value="">Seleccione...</option>';


                data.forEach(item => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        item.id;

                    option.textContent =
                        item.nombre;


                    if (
                        seleccionarActual &&
                        String(item.id) ===
                        String(servicioActual)
                    ) {

                        option.selected = true;

                    }

                    servicio.appendChild(
                        option
                    );

                });

            })
            .catch(error => {

                console.error(error);

                servicio.innerHTML =
                    '<option value="">Error al cargar servicios</option>';

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Cargar habitaciones
        |--------------------------------------------------------------------------
        */

        function cargarHabitaciones(
            seleccionarActual = false
        ) {

            const servicioId =
                servicio.value;

            habitacion.innerHTML =
                '<option value="">Cargando...</option>';


            if (!servicioId) {

                habitacion.innerHTML =
                    '<option value="">Seleccione primero un servicio...</option>';

                return Promise.resolve();

            }


            return fetch(
                '{{ url("/camas/servicios") }}/' +
                servicioId +
                '/habitaciones'
            )
            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'No se pudieron cargar las habitaciones.'
                    );

                }

                return response.json();

            })
            .then(data => {

                habitacion.innerHTML =
                    '<option value="">Sin habitación específica</option>';


                data.forEach(item => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        item.id;

                    option.textContent =
                        item.numero
                            ? item.nombre +
                              ' (' +
                              item.numero +
                              ')'
                            : item.nombre;


                    if (
                        seleccionarActual &&
                        String(item.id) ===
                        String(habitacionActual)
                    ) {

                        option.selected = true;

                    }

                    habitacion.appendChild(
                        option
                    );

                });

            })
            .catch(error => {

                console.error(error);

                habitacion.innerHTML =
                    '<option value="">Error al cargar habitaciones</option>';

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Cambio de departamento
        |--------------------------------------------------------------------------
        */

        departamento.addEventListener(
            'change',
            function () {

                cargarServicios(
                    false
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Cambio de servicio
        |--------------------------------------------------------------------------
        */

        servicio.addEventListener(
            'change',
            function () {

                cargarHabitaciones(
                    false
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Carga inicial para edición
        |--------------------------------------------------------------------------
        */

        if (departamentoActual) {

            departamento.value =
                departamentoActual;


            cargarServicios(true)
                .then(() => {

                    if (servicioActual) {

                        servicio.value =
                            servicioActual;

                        return cargarHabitaciones(
                            true
                        );

                    }

                });

        }

    }
);

</script>

@stop