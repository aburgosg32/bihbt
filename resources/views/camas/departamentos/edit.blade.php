@extends('adminlte::page')

@section('title', 'Editar Departamento')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="m-0">

            <i class="fas fa-edit mr-2"></i>

            Editar Departamento

        </h1>

        <small class="text-muted">

            Modificar la información del departamento

        </small>

    </div>

    <a href="{{ route('camas.departamentos.index') }}"
        class="btn btn-outline-secondary">

        <i class="fas fa-arrow-left mr-1"></i>

        Volver

    </a>

</div>

@stop

@section('content')

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="fas fa-hospital-alt mr-2 text-primary"></i>

                    {{ $departamento->nombre }}

                </h5>

            </div>

            <form method="POST"
                action="{{ route(
                    'camas.departamentos.update',
                    $departamento
                  ) }}">

                @csrf
                @method('PUT')

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="codigo">

                                    Código

                                </label>

                                <input type="text"
                                    id="codigo"
                                    name="codigo"
                                    maxlength="30"
                                    value="{{ old(
                                            'codigo',
                                            $departamento->codigo
                                       ) }}"
                                    class="form-control
                                       @error('codigo')
                                           is-invalid
                                       @enderror">

                                @error('codigo')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-8">

                            <div class="form-group">

                                <label for="nombre">

                                    Nombre del Departamento

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input type="text"
                                    id="nombre"
                                    name="nombre"
                                    maxlength="150"
                                    required
                                    value="{{ old(
                                            'nombre',
                                            $departamento->nombre
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

                        </div>

                    </div>

                    <div class="form-group">

                        <label for="descripcion">

                            Descripción

                        </label>

                        <textarea id="descripcion"
                            name="descripcion"
                            rows="4"
                            class="form-control
                                  @error('descripcion')
                                      is-invalid
                                  @enderror">{{ old(
                                    'descripcion',
                                    $departamento->descripcion
                                  ) }}</textarea>

                        @error('descripcion')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="form-group mb-0">

                        <div class="custom-control custom-switch">

                            <input type="checkbox"
                                class="custom-control-input"
                                id="activo"
                                name="activo"
                                value="1"
                                {{ old(
                                        'activo',
                                        $departamento->activo
                                   )
                                        ? 'checked'
                                        : '' }}>

                            <label class="custom-control-label"
                                for="activo">

                                Departamento activo

                            </label>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white text-right">

                    <a href="{{ route(
                        'camas.departamentos.index'
                    ) }}"
                        class="btn btn-outline-secondary mr-2">

                        Cancelar

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Actualizar Departamento

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop