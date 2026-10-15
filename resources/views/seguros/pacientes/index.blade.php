@extends('adminlte::page')

@section('title', 'Seguros | Pacientes')

@section('content_header')
<h1 class="text-primary">
    <i class="fas fa-user-edit mr-2"></i>
    Pacientes
</h1>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Buscar paciente</h3>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('seguros.pacientes.index') }}">
            <div class="row">
                @foreach ([
                'documento' => 'Documento',
                'historia' => 'Historia clínica',
                'apellido_paterno' => 'Apellido paterno',
                'apellido_materno' => 'Apellido materno',
                ] as $campo => $etiqueta)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="{{ $campo }}">{{ $etiqueta }}</label>

                        <input
                            id="{{ $campo }}"
                            name="{{ $campo }}"
                            type="{{ $campo === 'historia' ? 'number' : 'text' }}"
                            value="{{ request($campo) }}"
                            class="form-control">
                    </div>
                </div>
                @endforeach
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-search mr-1"></i>
                Buscar
            </button>

            <a
                href="{{ route('seguros.pacientes.index') }}"
                class="btn btn-outline-secondary">
                Limpiar
            </a>
        </form>

        @if ($errors->any())
        <div class="alert alert-danger mt-3 mb-0">
            {{ $errors->first() }}
        </div>
        @endif
    </div>
</div>

@if ($pacientes !== null)
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            Resultados: {{ $pacientes->total() }}
        </h3>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>HC</th>
                    <th>Documento</th>
                    <th>Paciente</th>
                    <th>Fecha de nacimiento</th>
                    <th>Teléfono</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pacientes as $p)
                <tr>
                    <td>{{ $p->NroHistoriaClinica }}</td>
                    <td>{{ $p->NroDocumento }}</td>
                    <td>
                        {{ trim(
                                        ($p->ApellidoPaterno ?? '') . ' ' .
                                        ($p->ApellidoMaterno ?? '') . ' ' .
                                        ($p->PrimerNombre ?? '') . ' ' .
                                        ($p->SegundoNombre ?? '') . ' ' .
                                        ($p->TercerNombre ?? '')
                                    ) }}
                    </td>
                    <td>
                        {{ $p->FechaNacimiento
                                        ? \Carbon\Carbon::parse($p->FechaNacimiento)->format('d/m/Y')
                                        : '—' }}
                    </td>
                    <td>{{ $p->Telefono }}</td>
                    <td>
                        <a
                            href="{{ route('seguros.pacientes.edit', [
                                            'paciente' => $p->IdPaciente
                                        ]) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Actualizar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        No se encontraron pacientes.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $pacientes->links('pagination::bootstrap-4') }}
    </div>
</div>
@else
<div class="alert alert-light border">
    Ingrese al menos un criterio de búsqueda.
</div>
@endif
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
@stop