@extends('adminlte::page')

@section('title', 'Código de prestación | Seguros')

@section('content_header')
<h1>Seguros <small class="text-muted">/ Código de prestación</small></h1>
@stop

@section('content')
<div class="container-fluid codigo-prestacion">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    @php
    $asignables = $atenciones->filter(fn ($a) => $a->PuedeAsignar);
    @endphp

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Código de prestación</strong>
                </div>

                <div class="card-body">
                    <form method="GET"
                        action="{{ route('seguros.codigo-prestacion.index') }}">
                        <label for="cuenta">Número de cuenta</label>

                        <div class="input-group">
                            <input
                                type="number"
                                id="cuenta"
                                name="cuenta"
                                class="form-control"
                                min="1"
                                value="{{ $cuenta }}"
                                placeholder="Ingrese el número de cuenta"
                                required>

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($asignables->isNotEmpty())
                    <hr class="my-4">

                    @if ($prestaciones->isEmpty())
                    <p class="text-muted mb-0">
                        No hay prestaciones disponibles en el catálogo.
                    </p>
                    @else
                    <form id="form-prestacion"
                        method="POST"
                        action="{{ route('seguros.codigo-prestacion.store') }}">
                        @csrf

                        <input type="hidden" name="cuenta" value="{{ $cuenta }}">

                        @if ($asignables->count() === 1)
                        <input
                            type="hidden"
                            name="id_atencion"
                            value="{{ $asignables->first()->IdAtencion }}">
                        @else
                        <div class="form-group">
                            <label for="id_atencion">Atención</label>
                            <select id="id_atencion"
                                name="id_atencion"
                                class="form-control"
                                required>
                                <option value="">Seleccione la atención</option>
                                @foreach ($asignables as $atencion)
                                <option
                                    value="{{ $atencion->IdAtencion }}"
                                    @selected(old('id_atencion')==$atencion->IdAtencion)
                                    >
                                    Atención #{{ $atencion->IdAtencion }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="codigo_prestacion">Código de prestación</label>

                            <select id="codigo_prestacion"
                                name="codigo_prestacion"
                                class="form-control"
                                required>
                                <option value="">Seleccione una prestación</option>

                                @foreach ($prestaciones as $prestacion)
                                <option
                                    value="{{ $prestacion->codigo }}"
                                    @selected(old('codigo_prestacion')===$prestacion->codigo)
                                    >
                                    {{ $prestacion->codigo }} |
                                    {{ $prestacion->descripcion }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Guardar código
                        </button>

                        <a href="{{ route('seguros.codigo-prestacion.index') }}"
                            class="btn btn-outline-secondary ml-1">
                            Limpiar
                        </a>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8 mb-3">
            @if ($cuenta === null)
            <div class="alert alert-info">
                Ingrese una cuenta para consultar y asignar su código de prestación.
            </div>
            @elseif ($atenciones->isEmpty())
            <div class="alert alert-warning">
                No se encontraron atenciones para la cuenta {{ $cuenta }}.
            </div>
            @else
            @foreach ($atenciones as $atencion)
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>
                        Cuenta {{ $cuenta }} · Atención #{{ $atencion->IdAtencion }}
                    </strong>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <th>Financiamiento</th>
                                    <td>{{ $atencion->Financiamiento ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Paciente</th>
                                    <td>{{ $atencion->Paciente ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>DNI</th>
                                    <td>{{ $atencion->NroDocumento ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Código actual</th>
                                    <td>
                                        @if (trim((string) $atencion->FuaCodigoPrestacion) !== '')
                                        <span class="badge badge-primary p-2">
                                            {{ trim($atencion->FuaCodigoPrestacion) }}
                                        </span>
                                        @else
                                        <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ((int) $atencion->IdFuenteFinanciamiento !== 3)
                    <div class="alert alert-warning mt-3 mb-0">
                        Esta atención no es SIS. No se puede asignar el código.
                    </div>
                    @elseif (! $atencion->TieneCita)
                    <div class="alert alert-warning mt-3 mb-0">
                        La atención no tiene una cita registrada.
                    </div>
                    @elseif ($atencion->IdDatosAdicionales === null)
                    <div class="alert alert-warning mt-3 mb-0">
                        La atención no tiene un registro de datos adicionales.
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="modalPrestacion" tabindex="-1"
    role="dialog" aria-labelledby="tituloPrestacion" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloPrestacion">
                    <i class="fas fa-save mr-2"></i> Confirmar código de prestación
                </h5>

                <button type="button" class="close text-white"
                    data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>
                    Se guardará esta prestación en la cuenta
                    <strong>{{ $cuenta }}</strong>,
                    atención <strong id="confirmar-atencion"></strong>.
                </p>

                <div class="bg-light rounded p-3">
                    <strong id="confirmar-prestacion"></strong>
                </div>

                <p class="small text-muted mt-3 mb-0">
                    Si la atención ya tiene un código, será reemplazado.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                    data-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" id="confirmar-guardar" class="btn btn-primary">
                    <i class="fas fa-check mr-1"></i> Sí, guardar
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .codigo-prestacion .table th,
    .codigo-prestacion .table td {
        padding: .65rem .4rem;
        vertical-align: middle;
    }

    .codigo-prestacion .table th {
        width: 30%;
        min-width: 120px;
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formulario = document.getElementById('form-prestacion');

        if (!formulario) return;

        const selector = document.getElementById('codigo_prestacion');
        const confirmar = document.getElementById('confirmar-guardar');
        let enviando = false;

        formulario.addEventListener('submit', function(evento) {
            evento.preventDefault();

            if (!formulario.reportValidity()) return;

            document.getElementById('confirmar-prestacion').textContent =
                selector.selectedOptions[0].textContent.trim();

            document.getElementById('confirmar-atencion').textContent =
                '#' + formulario.elements['id_atencion'].value;

            $('#modalPrestacion').modal('show');
        });

        confirmar.addEventListener('click', function() {
            if (enviando) return;

            enviando = true;
            confirmar.disabled = true;
            confirmar.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            formulario.submit();
        });
    });
</script>
@stop