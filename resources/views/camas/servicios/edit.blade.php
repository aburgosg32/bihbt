@extends('adminlte::page')

@section('title', 'Editar Servicio')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-edit mr-2"></i>

            Editar Servicio

        </h1>

        <small class="text-muted">

            Modificar información del servicio

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
                action="{{ route(
                    'camas.servicios.update',
                    $servicio
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

                                <select name="departamento_id"
                                    required
                                    class="form-control">

                                    @foreach($departamentos as $departamento)

                                    <option value="{{ $departamento->id }}"
                                        {{ old(
                                                'departamento_id',
                                                $servicio->departamento_id
                                            ) == $departamento->id
                                                ? 'selected'
                                                : '' }}>

                                        {{ $departamento->nombre }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Código
                                </label>

                                <input type="text"
                                    name="codigo"
                                    maxlength="30"
                                    value="{{ old(
                                            'codigo',
                                            $servicio->codigo
                                       ) }}"
                                    class="form-control">

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Piso
                                </label>

                                <input type="text"
                                    name="piso"
                                    maxlength="50"
                                    value="{{ old(
                                            'piso',
                                            $servicio->piso
                                       ) }}"
                                    class="form-control">

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
                            required
                            maxlength="150"
                            value="{{ old(
                                    'nombre',
                                    $servicio->nombre
                               ) }}"
                            class="form-control
                               @error('nombre')
                                   is-invalid
                               @enderror">

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
                            class="form-control">{{ old(
                                    'descripcion',
                                    $servicio->descripcion
                                  ) }}</textarea>

                    </div>

                    <div class="custom-control custom-switch">

                        <input type="checkbox"
                            class="custom-control-input"
                            id="activo"
                            name="activo"
                            value="1"
                            {{ old(
                                    'activo',
                                    $servicio->activo
                               ) ? 'checked' : '' }}>

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

                        Actualizar Servicio

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