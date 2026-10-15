@extends('adminlte::page')

@section('title', 'Seguros | Actualizar paciente')

@section('content_header')
<h1 class="text-primary">
    <i class="fas fa-user-edit mr-2"></i>
    Actualizar paciente
</h1>
@stop

@section('content')
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

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            Historia clínica:
            <strong>{{ $paciente->NroHistoriaClinica }}</strong>
            <span class="ml-3">
                ID paciente: {{ $paciente->IdPaciente }}
            </span>
        </h3>
    </div>

    <form
        method="POST"
        action="{{ route('seguros.pacientes.update', [
                'paciente' => $paciente->IdPaciente
            ]) }}">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row">
                @foreach ([
                'ApellidoPaterno' => ['Apellido paterno', 40],
                'ApellidoMaterno' => ['Apellido materno', 40],
                'PrimerNombre' => ['Primer nombre', 40],
                'SegundoNombre' => ['Segundo nombre', 40],
                'TercerNombre' => ['Tercer nombre', 40],
                'NroDocumento' => ['Número de documento', 12],
                'Telefono' => ['Teléfono', 10],
                'Email' => ['Correo electrónico', 50],
                'NombrePadre' => ['Nombre del padre', 20],
                'NombreMadre' => ['Nombre de la madre', 20],
                ] as $campo => $configuracion)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="{{ $campo }}">
                            {{ $configuracion[0] }}
                        </label>

                        <input
                            id="{{ $campo }}"
                            name="{{ $campo }}"
                            type="{{ $campo === 'Email' ? 'email' : 'text' }}"
                            maxlength="{{ $configuracion[1] }}"
                            value="{{ old($campo, $paciente->{$campo}) }}"
                            class="form-control @error($campo) is-invalid @enderror"
                            @if (in_array($campo, ['ApellidoPaterno', 'PrimerNombre' ]))
                            required
                            @endif>

                        @error($campo)
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endforeach

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="FechaNacimiento">Fecha de nacimiento</label>

                        <input
                            type="date"
                            id="FechaNacimiento"
                            name="FechaNacimiento"
                            class="form-control"
                            value="{{ old(
                                    'FechaNacimiento',
                                    $paciente->FechaNacimiento
                                        ? \Carbon\Carbon::parse($paciente->FechaNacimiento)->format('Y-m-d')
                                        : ''
                                ) }}"
                            required>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="DireccionDomicilio">Dirección</label>

                        <input
                            type="text"
                            id="DireccionDomicilio"
                            name="DireccionDomicilio"
                            class="form-control"
                            maxlength="100"
                            value="{{ old('DireccionDomicilio', $paciente->DireccionDomicilio) }}">
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="Observacion">Observaciones</label>

                        <textarea
                            id="Observacion"
                            name="Observacion"
                            class="form-control"
                            rows="3"
                            maxlength="150">{{ old('Observacion', $paciente->Observacion) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Guardar cambios
            </button>

            <a
                href="{{ route('seguros.pacientes.index', [
                        'historia' => $paciente->NroHistoriaClinica
                    ]) }}"
                class="btn btn-outline-secondary">
                Volver al listado
            </a>
        </div>
    </form>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop