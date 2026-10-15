@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Disponibilidad de citas | Seguros')

@section('content_header')
<h1>
    Seguros
    <small class="text-muted">/ Disponibilidad</small>
</h1>
@stop

@section('content')
<div class="container-fluid seguros-disponibilidad">
    <h4 class="mb-3">Citas disponibles</h4>

    <div class="row">
        {{-- COLUMNA IZQUIERDA: BUSCADOR Y ETIQUETAS --}}
        <div class="col-12 col-lg-4 mb-3">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET"
                        action="{{ route('seguros.disponibilidad') }}">

                        <div class="form-group">
                            <label for="especialidad">Especialidad</label>

                            <select
                                name="especialidad"
                                id="especialidad"
                                class="form-control"
                                required>
                                <option value=""></option>

                                @foreach ($especialidades as $especialidad)
                                <option
                                    value="{{ $especialidad->IdEspecialidad }}"
                                    @selected(
                                    $idEspecialidad===(int) $especialidad->IdEspecialidad
                                    )
                                    >
                                    {{ $especialidad->Nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>
                    </form>

                    @if ($errors->any())
                    <div class="alert alert-danger mt-3 mb-0">
                        Seleccione una especialidad válida.
                    </div>
                    @endif
                </div>
            </div>

            @if ($idEspecialidad !== null)
            <div class="consultorios-sin-disponibilidad">
                <div class="font-weight-bold mb-1">
                    Consultorios sin disponibilidad
                </div>

                <div class="text-muted small mb-2">
                    Para la especialidad seleccionada · Próximos 30 días
                </div>

                @forelse ($consultoriosSinDisponibilidad as $consultorio)
                <span class="consultorio-etiqueta">
                    {{ $consultorio->Nombre }}
                </span>
                @empty
                <span class="text-muted small">
                    Ninguno.
                </span>
                @endforelse
            </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: RESULTADOS --}}
        <div class="col-12 col-lg-8 mb-3">
            @if ($idEspecialidad !== null)
            <div class="mb-2">
                <span class="badge badge-primary p-2 mr-1">
                    {{ $resultados->count() }}
                    programaciones disponibles
                </span>

                <span class="badge badge-success p-2">
                    {{ $resultados->sum('cupos_libres') }}
                    cupos libres
                </span>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora inicio</th>
                                    <th>Hora fin</th>
                                    <th>Consultorio</th>
                                    <th>Médico</th>
                                    <th class="text-center">
                                        Cupos libres
                                    </th>
                                    <th>Primera hora libre</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($resultados as $fila)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $fila->fecha }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $fila->inicio }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $fila->fin }}
                                    </td>

                                    <td>
                                        {{ $fila->consultorio }}
                                    </td>

                                    <td>
                                        {{ $fila->medico }}
                                    </td>

                                    <td class="text-center">
                                        <span class="badge badge-success p-2">
                                            {{ $fila->cupos_libres }}
                                        </span>
                                    </td>

                                    <td class="text-nowrap">
                                        <strong class="text-primary">
                                            {{ $fila->primera_hora }}
                                        </strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7"
                                        class="text-center text-muted py-4">
                                        No hay cupos disponibles
                                        para esta especialidad en
                                        los próximos 30 días.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                Seleccione una especialidad para ver los cupos
                disponibles.
            </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">

<style>
    .seguros-disponibilidad .consultorios-sin-disponibilidad {
        line-height: 1.8;
    }

    .seguros-disponibilidad .consultorio-etiqueta {
        display: inline-block;
        margin: 0 6px 7px 0;
        padding: 4px 9px;
        border-radius: 12px;
        background: #edf3f9;
        color: #315677;
        font-size: 12px;
        line-height: 1.4;
    }

    .seguros-disponibilidad .table th,
    .seguros-disponibilidad .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .seguros-disponibilidad .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    .seguros-disponibilidad .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem;
        padding-left: .75rem;
        padding-right: 2.5rem;
    }

    .seguros-disponibilidad .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.25rem;
    }

    .seguros-disponibilidad .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    /* Lista de especialidades más alta */
    .especialidades-dropdown .select2-results>.select2-results__options {
        max-height: 420px !important;
        max-height: min(420px, 60vh) !important;
        overflow-y: auto;
    }
</style>
@stop
@section('js')
<script>
    $(function() {
        $('#especialidad').select2({
            width: '100%',
            placeholder: 'Seleccione una especialidad',
            allowClear: true,
            minimumResultsForSearch: 0,
            dropdownCssClass: 'especialidades-dropdown',
            language: {
                noResults: function() {
                    return 'No se encontraron especialidades';
                }
            }
        });
    });
</script>
@stop