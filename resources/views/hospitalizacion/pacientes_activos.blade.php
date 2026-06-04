@extends('adminlte::page')

@section('title', 'Pacientes Activos - Hospitalización')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .table-hospitalizacion thead th {
        background: #F3F2F1 !important;
        color: var(--hbt-text) !important;
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

    .badge-servicio {
        background: #E5F1FB;
        color: #005A9E;
        border: 1px solid #BBDCF5;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .badge-gravedad {
        background: #FFF4CE;
        color: #8A6A00;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .badge-cama {
        background: #DDEEFF;
        color: #005A9E;
        border: 1px solid #BBDCF5;
        font-size: 10px;
        padding: 5px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .pagination {
        margin-bottom: 0;
    }
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="mb-1">
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
                    placeholder="Cuenta, paciente, cama, servicio, médico...">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-search mr-1"></i>
                    Filtrar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('hospitalizacion.pacientes-activos') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Limpiar
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
                        <th>Cama</th>
                        <th>Financiamiento</th>
                        <th>Médico ingreso</th>
                        <th>Detalle</th>
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

                        <td class="text-center">
                            @if($p->CAMAACTUAL)
                            <span class="badge-cama">
                                {{ $p->CAMAACTUAL }}
                            </span>
                            @else
                            <span class="badge badge-secondary">
                                SIN CAMA
                            </span>
                            @endif
                        </td>

                        <td>{{ $p->FUENTEFINANCIAMIENTO }}</td>
                        <td>{{ $p->MEDICOINGRESO }}</td>

                        <td class="text-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-success btn-detalle-paciente"
                                data-id="{{ $p->IdAtencion }}"
                                data-cuenta="{{ $p->CUENTA }}"
                                data-paciente="{{ $p->PACIENTE }}">
                                <i class="fas fa-notes-medical"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
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

<div class="modal fade" id="modalDiagnosticos" tabindex="-1" role="dialog" aria-labelledby="modalDiagnosticosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalDiagnosticosLabel">
                    <i class="fas fa-notes-medical mr-1"></i>
                    Detalle clínico y consumo del paciente
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <h6 class="font-weight-bold text-primary mb-3" id="detallePaciente">
                    Paciente
                </h6>

                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabDx" role="tab">
                            <i class="fas fa-notes-medical mr-1"></i> Diagnósticos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabServicios" role="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Servicios
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabFarmacia" role="tab">
                            <i class="fas fa-pills mr-1"></i> Farmacia
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane fade show active" id="tabDx" role="tabpanel">
                        <div id="dxLoading" class="text-center py-4 d-none">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2 text-muted">Cargando diagnósticos...</p>
                        </div>

                        <div id="dxContent"></div>
                    </div>

                    <div class="tab-pane fade" id="tabServicios" role="tabpanel">
                        <div id="consumoLoading" class="text-center py-4 d-none">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2 text-muted">Cargando consumo de servicios...</p>
                        </div>

                        <div id="consumoContent"></div>
                    </div>

                    <div class="tab-pane fade" id="tabFarmacia" role="tabpanel">
                        <div id="farmaciaLoading" class="text-center py-4 d-none">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2 text-muted">Cargando consumo de farmacia...</p>
                        </div>

                        <div id="farmaciaContent"></div>
                    </div>

                </div>

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
    $(document).on('click', '.btn-detalle-paciente', function() {
        const idAtencion = $(this).data('id');
        const cuenta = $(this).data('cuenta');
        const paciente = $(this).data('paciente');

        $('#detallePaciente').text(paciente + ' · Cuenta: ' + cuenta);

        $('#dxContent').html('');
        $('#consumoContent').html('');
        $('#farmaciaContent').html('');

        $('#dxLoading').removeClass('d-none');
        $('#consumoLoading').removeClass('d-none');
        $('#farmaciaLoading').removeClass('d-none');

        $('#modalDiagnosticos').modal('show');

        $.ajax({
            url: "{{ url('/hospitalizacion/diagnosticos') }}/" + idAtencion,
            method: "GET",
            success: function(response) {
                $('#dxLoading').addClass('d-none');

                if (!response.diagnosticos || response.diagnosticos.length === 0) {
                    $('#dxContent').html(`
                    <div class="alert alert-warning mb-0">
                        No se encontraron diagnósticos registrados.
                    </div>
                `);
                    return;
                }

                let html = `
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="width:120px;">CIE10</th>
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

                html += `</tbody></table>`;
                $('#dxContent').html(html);
            },
            error: function() {
                $('#dxLoading').addClass('d-none');
                $('#dxContent').html(`
                <div class="alert alert-danger mb-0">
                    Error al cargar diagnósticos.
                </div>
            `);
            }
        });

        $.ajax({
            url: "{{ url('/hospitalizacion/consumo') }}/" + cuenta,
            method: "GET",
            success: function(response) {
                $('#consumoLoading').addClass('d-none');

                if (!response.consumos || response.consumos.length === 0) {
                    $('#consumoContent').html(`
                    <div class="alert alert-warning mb-0">
                        No se encontraron consumos de servicios.
                    </div>
                `);
                    return;
                }

                let totalGeneral = 0;

                let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Código</th>
                                <th>Servicio / Producto</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Financiamiento</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                response.consumos.forEach(function(item) {
                    const total = parseFloat(item.Total ?? 0);
                    totalGeneral += total;

                    html += `
                    <tr>
                        <td>${item.idOrden ?? ''}</td>
                        <td>${item.Codigo ?? ''}</td>
                        <td>${item.Nombre ?? ''}</td>
                        <td class="text-center">${item.Cantidad ?? ''}</td>
                        <td class="text-right">${parseFloat(item.Precio ?? 0).toFixed(2)}</td>
                        <td class="text-right font-weight-bold">${total.toFixed(2)}</td>
                        <td>${item.Descripcion ?? ''}</td>
                        <td>${item.dfinanciamiento ?? ''}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">TOTAL GENERAL</th>
                                <th class="text-right">${totalGeneral.toFixed(2)}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

                $('#consumoContent').html(html);
            },
            error: function() {
                $('#consumoLoading').addClass('d-none');
                $('#consumoContent').html(`
                <div class="alert alert-danger mb-0">
                    Error al cargar consumo de servicios.
                </div>
            `);
            }
        });

        $.ajax({
            url: "{{ url('/hospitalizacion/farmacia') }}/" + cuenta,
            method: "GET",
            success: function(response) {
                $('#farmaciaLoading').addClass('d-none');

                if (!response.farmacia || response.farmacia.length === 0) {
                    $('#farmaciaContent').html(`
                    <div class="alert alert-warning mb-0">
                        No se encontraron consumos de farmacia.
                    </div>
                `);
                    return;
                }

                let totalFarmacia = 0;

                let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Receta</th>
                                <th>Documento</th>
                                <th>Código</th>
                                <th>Medicamento / Insumo</th>
                                <th>Forma</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                response.farmacia.forEach(function(item) {
                    const cantidad = parseFloat(item.CantidadPedida ?? 0);
                    const precio = parseFloat(item.Precio ?? 0);
                    const total = cantidad * precio;

                    totalFarmacia += total;

                    html += `
                    <tr>
                        <td>${item.idReceta ?? ''}</td>
                        <td>${item.DocumentoDespacho ?? ''}</td>
                        <td>${item.Codigo ?? ''}</td>
                        <td>${item.Nombre ?? ''}</td>
                        <td>${item.FormaFarmaceutica ?? ''}</td>
                        <td class="text-center">${cantidad}</td>
                        <td class="text-right">${precio.toFixed(2)}</td>
                        <td class="text-right font-weight-bold">${total.toFixed(2)}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-right">TOTAL FARMACIA</th>
                                <th class="text-right">${totalFarmacia.toFixed(2)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

                $('#farmaciaContent').html(html);
            },
            error: function() {
                $('#farmaciaLoading').addClass('d-none');
                $('#farmaciaContent').html(`
                <div class="alert alert-danger mb-0">
                    Error al cargar consumo de farmacia.
                </div>
            `);
            }
        });
    });
</script>
@stop