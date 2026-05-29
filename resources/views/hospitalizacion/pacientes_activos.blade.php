@extends('adminlte::page')

@section('title', 'Pacientes Activos - Hospitalización')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .table-hospitalizacion thead th {
        background: #0f8b6f;
        color: #fff;
        font-size: 11px;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-hospitalizacion tbody td {
        font-size: 12px;
        vertical-align: middle;
    }

    .paciente-cell {
        min-width: 260px;
    }

    .servicio-cell {
        min-width: 220px;
    }

    .diagnostico-cell {
        min-width: 330px;
        white-space: normal;
    }

    .badge-servicio {
        background: #16a085;
        color: white;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 5px;
    }

    .badge-gravedad {
        background: #ffc107;
        color: #111827;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 5px;
        font-weight: 800;
    }

    .hbt-header-date {
        background: linear-gradient(135deg, #0f8b6f, #16a085);
        color: #fff;
        padding: 9px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 8px 18px rgba(15, 139, 111, .18);
    }

    .pagination {
        margin-bottom: 0;
    }
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="font-weight-bold text-success mb-1">
            Pacientes Activos en Hospitalización
        </h1>
        <small class="text-muted">
            Hospital Belén de Trujillo · Hospitalización
        </small>
    </div>

    <div class="hbt-header-date">
        Año: {{ $anio }} · Total: {{ $pacientes->total() }}
    </div>
</div>

<form method="GET" action="{{ route('hospitalizacion.pacientes-activos') }}" class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-end">

            <div class="col-md-2">
                <label class="font-weight-bold text-muted">Año</label>
                <select name="anio" class="form-control">
                    @foreach($anios as $item)
                    <option value="{{ $item }}" {{ (string)$anio === (string)$item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Servicio actual</label>
                <select name="servicio" class="form-control">
                    <option value="">Todos los servicios</option>
                    @foreach($servicios as $item)
                    <option value="{{ $item }}" {{ $servicio == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="font-weight-bold text-muted">Buscar</label>
                <input type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    class="form-control"
                    placeholder="Cuenta, paciente, servicio, médico...">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('hospitalizacion.pacientes-activos') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-sync-alt mr-1"></i> Limpiar
                </a>
            </div>

        </div>
    </div>
</form>
@stop

@section('content')

<div class="card">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-bed mr-1"></i>
            Listado de pacientes activos en hospitalización
        </h3>

        <div class="card-tools">
            <span class="badge badge-light">
                Total: {{ $pacientes->total() }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm table-hospitalizacion mb-0">
                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>Paciente</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>Ingreso</th>
                        <th>Hora</th>
                        <th>Servicio ingreso</th>
                        <th>Servicio actual</th>
                        <th>Gravedad</th>
                        <th>Financiamiento</th>
                        <th>Médico ingreso</th>
                        <th>Dx</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pacientes as $p)
                    <tr>
                        <td>{{ $p->CUENTA }}</td>

                        <td class="paciente-cell">
                            <strong>{{ $p->PACIENTE }}</strong><br>
                            <small class="text-muted">
                                ID Atención: {{ $p->IdAtencion }}
                            </small>
                        </td>

                        <td>{{ $p->Edad }} {{ $p->TIPOEDAD }}</td>
                        <td>{{ $p->SEXO }}</td>

                        <td>
                            {{ $p->FechaIngreso ? \Carbon\Carbon::parse($p->FechaIngreso)->format('d/m/Y') : '' }}
                        </td>

                        <td>{{ $p->HoraIngreso }}</td>

                        <td class="servicio-cell">
                            {{ $p->SERVICIOINGRESO }}
                        </td>

                        <td class="servicio-cell">
                            <span class="badge-servicio">
                                {{ $p->SERVICIOACTUAL ?? 'SIN SERVICIO' }}
                            </span>
                        </td>

                        <td>

                            {{ $p->TIPOGRAVEDAD ?? 'NO REGISTRADO' }}

                        </td>



                        <td>{{ $p->FUENTEFINANCIAMIENTO }}</td>
                        <td>{{ $p->MEDICOINGRESO }}</td>
                        <td class="text-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-success btn-diagnosticos"
                                data-id="{{ $p->IdAtencion }}"
                                data-paciente="{{ $p->PACIENTE }}">
                                <i class="fas fa-notes-medical"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            No se encontraron pacientes activos con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-muted small mb-2 mb-md-0">
            Mostrando {{ $pacientes->firstItem() }} a {{ $pacientes->lastItem() }}
            de {{ $pacientes->total() }} registros
        </div>

        <div class="pagination-sm">
            {{ $pacientes->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<div class="modal fade" id="modalDiagnosticos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-notes-medical mr-1"></i>
                    Diagnósticos del paciente
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h6 class="font-weight-bold text-success mb-3" id="dxPaciente"></h6>

                <div id="dxLoading" class="text-center py-4 d-none">
                    <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                    <p class="mt-2 text-muted">Cargando diagnósticos...</p>
                </div>

                <div id="dxContent"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(document).on('click', '.btn-diagnosticos', function() {
        const idAtencion = $(this).data('id');
        const paciente = $(this).data('paciente');

        $('#dxPaciente').text(paciente);
        $('#dxContent').html('');
        $('#dxLoading').removeClass('d-none');
        $('#modalDiagnosticos').modal('show');

        $.ajax({
            url: "{{ url('/hospitalizacion/diagnosticos') }}/" + idAtencion,
            method: "GET",
            success: function(response) {
                $('#dxLoading').addClass('d-none');

                if (!response.diagnosticos || response.diagnosticos.length === 0) {
                    $('#dxContent').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            No se encontraron diagnósticos registrados.
                        </div>
                    `);
                    return;
                }

                let html = `
                    <table class="table table-bordered table-sm">
                        <thead class="bg-success text-white">
                            <tr>
                                <th style="width: 120px;">CIE10</th>
                                <th>Diagnóstico</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                response.diagnosticos.forEach(function(dx) {
                    html += `
                        <tr>
                            <td><strong>${dx.cie10 ?? ''}</strong></td>
                            <td>${dx.descripcion ?? ''}</td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                $('#dxContent').html(html);
            },
            error: function() {
                $('#dxLoading').addClass('d-none');
                $('#dxContent').html(`
                    <div class="alert alert-danger mb-0">
                        Error al cargar los diagnósticos.
                    </div>
                `);
            }
        });
    });
</script>
@stop