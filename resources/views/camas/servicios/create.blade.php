@extends('adminlte::page')

@section('title', 'Nuevo Servicio')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-plus-circle mr-2"></i>

            Nuevo Servicio

        </h1>

        <small class="text-muted">

            Registrar un servicio hospitalario

        </small>

    </div>

    <a href="{{ route('camas.servicios.index') }}"
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
                action="{{ route('camas.servicios.store') }}">

                @csrf

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Departamento

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="departamento_id"
                                    required
                                    class="form-control
                                        @error('departamento_id')
                                            is-invalid
                                        @enderror">

                                    <option value="">

                                        Seleccione...

                                    </option>

                                    @foreach($departamentos as $departamento)

                                    <option value="{{ $departamento->id }}"
                                        {{ old('departamento_id') == $departamento->id
                                                ? 'selected'
                                                : '' }}>

                                        {{ $departamento->nombre }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('departamento_id')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Código
                                </label>

                                <input type="text"
                                    name="codigo"
                                    value="{{ old('codigo') }}"
                                    maxlength="30"
                                    class="form-control"
                                    placeholder="MED-A">

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Piso
                                </label>

                                <input type="text"
                                    name="piso"
                                    value="{{ old('piso') }}"
                                    maxlength="50"
                                    class="form-control"
                                    placeholder="2do piso">

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>

                            Nombre del Servicio

                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            required
                            maxlength="150"
                            class="form-control
                               @error('nombre')
                                   is-invalid
                               @enderror"
                            placeholder="Ej. Medicina A">

                        @error('nombre')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="form-group">

                        <label>
                            Descripción
                        </label>

                        <textarea name="descripcion"
                            rows="4"
                            class="form-control"
                            placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>

                    </div>

                    <div class="custom-control custom-switch">

                        <input type="checkbox"
                            class="custom-control-input"
                            id="activo"
                            name="activo"
                            value="1"
                            {{ old('activo', true) ? 'checked' : '' }}>

                        <label class="custom-control-label"
                            for="activo">

                            Servicio activo

                        </label>

                    </div>

                </div>

                <div class="card-footer bg-white text-right">

                    <a href="{{ route('camas.servicios.index') }}"
                        class="btn btn-outline-secondary mr-2">

                        Cancelar

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Guardar Servicio

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