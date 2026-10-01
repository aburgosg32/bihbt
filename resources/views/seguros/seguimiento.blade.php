@extends('adminlte::page')

@section('title', 'Seguros | Seguimiento')

@section('content_header')
<h1 class="text-primary">
    <i class="fas fa-history mr-2"></i>
    Seguimiento de pacientes
</h1>
@stop

@section('content')
<div class="seguros-seguimiento">

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-2"></i>
                Buscar atenciones por DNI
            </h3>
        </div>

        <div class="card-body">
            <form
                action="{{ route('seguros.seguimiento') }}"
                method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <div class="form-group mb-md-0">
                            <label for="dni">DNI del paciente</label>

                            <input
                                type="text"
                                id="dni"
                                name="dni"
                                value="{{ old('dni', $dni) }}"
                                class="form-control @error('dni') is-invalid @enderror"
                                placeholder="Ingrese 8 dígitos"
                                maxlength="8"
                                minlength="8"
                                pattern="[0-9]{8}"
                                inputmode="numeric"
                                autocomplete="off"
                                required>

                            @error('dni')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>

                        <a
                            href="{{ route('seguros.seguimiento') }}"
                            class="btn btn-outline-secondary ml-1">
                            <i class="fas fa-eraser mr-1"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($buscado && $atenciones->isNotEmpty())
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list-alt mr-2"></i>
                Historial de atenciones
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $atenciones->count() }}
                    {{ $atenciones->count() === 1 ? 'atención' : 'atenciones' }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="px-3 py-2 border-bottom text-muted">
                DNI:
                <strong>{{ $dni }}</strong>
                <span class="ml-3">
                    Ordenado desde la atención más reciente.
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha de ingreso</th>
                            <th>Cuenta</th>
                            <th>Historia clínica</th>
                            <th>Paciente</th>
                            <th>Edad registrada</th>
                            <th>Tipo de servicio</th>
                            <th>Servicio</th>
                            <th>Médico</th>
                            <th class="text-center">Detalle</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($atenciones as $atencion)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="text-nowrap">
                                {{ $atencion->FechaIngreso
                                                ? \Carbon\Carbon::parse($atencion->FechaIngreso)->format('d/m/Y')
                                                : '—' }}
                            </td>

                            <td>{{ $atencion->IdCuentaAtencion }}</td>

                            <td>{{ $atencion->NroHistoriaClinica }}</td>

                            <td class="seguimiento-paciente">
                                {{ $atencion->Paciente }}
                            </td>

                            <td>{{ $atencion->Edad ?? '—' }}</td>

                            <td>
                                @php
                                $tipoServicio = $atencion->TipoServicio ?? 'SIN TIPO DE SERVICIO';

                                $esConsultaExterna = in_array(
                                mb_strtoupper(trim($tipoServicio)),
                                ['CONSULTORIOS EXTERNOS', 'CONSULTA EXTERNA']
                                );
                                @endphp

                                @if ($esConsultaExterna)
                                <span class="seguimiento-consulta-externa">
                                    {{ $tipoServicio }}
                                </span>
                                @else
                                {{ $tipoServicio }}
                                @endif
                            </td>

                            <td>
                                {{ $atencion->Servicio ?? 'SIN SERVICIO' }}
                            </td>

                            <td>{{ $atencion->Medico }}</td>
                            <td class="text-center text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm btn-detalle-atencion"
                                    data-url="{{ route('seguros.seguimiento.detalle', [
            'cuenta' => $atencion->IdCuentaAtencion
        ]) }}"
                                    data-cuenta="{{ $atencion->IdCuentaAtencion }}"
                                    aria-label="Ver detalle de la cuenta {{ $atencion->IdCuentaAtencion }}">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver detalle
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @elseif ($buscado)
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        No se encontraron atenciones para el DNI
        <strong>{{ $dni }}</strong>.
    </div>

    @else
    <div class="alert alert-light border">
        <i class="fas fa-info-circle text-primary mr-2"></i>
        Ingrese el DNI para consultar las atenciones del paciente.
    </div>
    @endif

</div>
<div
    class="modal fade"
    id="modalDetalleAtencion"
    tabindex="-1"
    aria-labelledby="tituloDetalleAtencion"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="tituloDetalleAtencion">
                    <i class="fas fa-file-medical mr-2"></i>
                    Detalle de atención
                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                    aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="detalleCargando" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 mb-0">Cargando información...</p>
                </div>

                <div
                    id="detalleError"
                    class="alert alert-danger"
                    role="alert"
                    hidden></div>

                <div id="detalleContenido" hidden>
                    <div class="detalle-resumen mb-3">
                        <div class="font-weight-bold text-primary" id="detallePaciente"></div>

                        <div class="mt-1">
                            Cuenta: <strong id="detalleCuenta"></strong>
                            <span class="ml-3">
                                HC: <strong id="detalleHistoria"></strong>
                            </span>
                            <span class="ml-3">
                                Fecha: <strong id="detalleFecha"></strong>
                            </span>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                id="tabDiagnosticos"
                                data-toggle="tab"
                                href="#panelDiagnosticos"
                                role="tab"
                                aria-controls="panelDiagnosticos"
                                aria-selected="true">
                                Diagnósticos
                                <span class="badge badge-primary ml-1" id="totalDiagnosticos">0</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="tabServicios"
                                data-toggle="tab"
                                href="#panelServicios"
                                role="tab"
                                aria-controls="panelServicios"
                                aria-selected="false">
                                Servicios
                                <span class="badge badge-primary ml-1" id="totalServicios">0</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="tabFarmacia"
                                data-toggle="tab"
                                href="#panelFarmacia"
                                role="tab"
                                aria-controls="panelFarmacia"
                                aria-selected="false">
                                Farmacia / bienes
                                <span class="badge badge-primary ml-1" id="totalFarmacia">0</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        <div
                            class="tab-pane fade show active"
                            id="panelDiagnosticos"
                            role="tabpanel"
                            aria-labelledby="tabDiagnosticos">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm detalle-tabla">
                                    <thead>
                                        <tr>
                                            <th>CIE-10</th>
                                            <th>Diagnóstico</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalleDiagnosticos"></tbody>
                                </table>
                            </div>
                        </div>

                        <div
                            class="tab-pane fade"
                            id="panelServicios"
                            role="tabpanel"
                            aria-labelledby="tabServicios">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm detalle-tabla">
                                    <thead>
                                        <tr>
                                            <th>Orden</th>
                                            <th>Código</th>
                                            <th>Servicio</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalleServicios"></tbody>
                                </table>
                            </div>
                        </div>

                        <div
                            class="tab-pane fade"
                            id="panelFarmacia"
                            role="tabpanel"
                            aria-labelledby="tabFarmacia">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm detalle-tabla">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Medicamento / bien</th>
                                            <th>Movimiento</th>
                                            <th>Tipo</th>
                                            <th>Estado del movimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalleFarmacia"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .seguros-seguimiento .table thead th {
        background-color: #eaf2fb;
        color: #174a78;
        border-bottom: 2px solid #cbdced;
        vertical-align: middle;
    }

    .seguros-seguimiento .table td {
        vertical-align: middle;
    }

    .seguros-seguimiento .seguimiento-paciente {
        min-width: 240px;
        font-weight: 600;
    }

    .seguros-seguimiento .seguimiento-consulta-externa {
        display: inline-block;
        padding: 4px 10px;
        border: 1px solid #007bff;
        border-radius: 6px;
        background-color: #eef6ff;
        color: #0056b3;
        font-weight: 600;
    }

    #modalDetalleAtencion .detalle-resumen {
        padding: 12px 15px;
        border: 1px solid #cbdced;
        border-radius: 6px;
        background-color: #f0f6fc;
    }

    #modalDetalleAtencion .detalle-tabla thead th {
        background-color: #eaf2fb;
        color: #174a78;
        border-bottom: 2px solid #cbdced;
    }

    #modalDetalleAtencion .detalle-tabla td {
        vertical-align: middle;
    }

    #modalDetalleAtencion .nav-tabs .nav-link.active {
        color: #174a78;
        font-weight: 600;
    }
</style>
@stop
@section('js')
<script>
    $(function() {
        let solicitudActual = null;
        let numeroSolicitud = 0;

        function escribirTabla(id, registros, columnas, mensajeVacio) {
            const cuerpo = document.getElementById(id);
            cuerpo.replaceChildren();

            if (!registros.length) {
                const fila = document.createElement('tr');
                const celda = document.createElement('td');

                celda.colSpan = columnas.length;
                celda.className = 'text-center text-muted py-3';
                celda.textContent = mensajeVacio;

                fila.appendChild(celda);
                cuerpo.appendChild(fila);
                return;
            }

            registros.forEach(registro => {
                const fila = document.createElement('tr');

                columnas.forEach(columna => {
                    const celda = document.createElement('td');
                    celda.textContent = registro[columna] ?? '—';
                    fila.appendChild(celda);
                });

                cuerpo.appendChild(fila);
            });
        }

        function mostrarTexto(id, valor) {
            document.getElementById(id).textContent = valor ?? '—';
        }

        function formatearFecha(valor) {
            if (!valor) return '—';

            const coincidencia = String(valor).match(/^(\d{4})-(\d{2})-(\d{2})/);

            return coincidencia ?
                `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]}` :
                valor;
        }

        $(document).on('click', '.btn-detalle-atencion', async function() {
            const url = this.dataset.url;
            const cuenta = this.dataset.cuenta;
            const idSolicitud = ++numeroSolicitud;

            if (solicitudActual) {
                solicitudActual.abort();
            }

            const controlador = new AbortController();
            solicitudActual = controlador;

            document.getElementById('detalleCargando').hidden = false;
            document.getElementById('detalleContenido').hidden = true;
            document.getElementById('detalleError').hidden = true;

            mostrarTexto('tituloDetalleAtencion', `Detalle de atención — Cuenta ${cuenta}`);

            $('#tabDiagnosticos').tab('show');
            $('#modalDetalleAtencion').modal('show');

            try {
                const respuesta = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    signal: controlador.signal
                });

                if (!respuesta.ok) {
                    throw new Error(
                        respuesta.status === 404 ?
                        'No se encontró la atención.' :
                        'No se pudo cargar el detalle. Intente nuevamente.'
                    );
                }

                const datos = await respuesta.json();

                if (idSolicitud !== numeroSolicitud) return;

                mostrarTexto('detallePaciente', datos.atencion.Paciente);
                mostrarTexto('detalleCuenta', datos.atencion.IdCuentaAtencion);
                mostrarTexto('detalleHistoria', datos.atencion.NroHistoriaClinica);
                mostrarTexto('detalleFecha', formatearFecha(datos.atencion.FechaIngreso));

                mostrarTexto('totalDiagnosticos', datos.diagnosticos.length);
                mostrarTexto('totalServicios', datos.servicios.length);
                mostrarTexto('totalFarmacia', datos.farmacia.length);

                escribirTabla(
                    'detalleDiagnosticos',
                    datos.diagnosticos,
                    ['Codigo', 'Diagnostico'],
                    'No hay diagnósticos registrados.'
                );

                escribirTabla(
                    'detalleServicios',
                    datos.servicios,
                    ['IdOrden', 'Codigo', 'Servicio'],
                    'No hay servicios despachados registrados.'
                );

                escribirTabla(
                    'detalleFarmacia',
                    datos.farmacia,
                    ['Codigo', 'Nombre', 'MovNumero', 'MovTipo', 'EstadoMovimiento'],
                    'No hay registros de farmacia / bienes.'
                );

                document.getElementById('detalleContenido').hidden = false;
            } catch (error) {
                if (error.name === 'AbortError' || idSolicitud !== numeroSolicitud) {
                    return;
                }

                const aviso = document.getElementById('detalleError');
                aviso.textContent = error.message;
                aviso.hidden = false;
            } finally {
                if (idSolicitud === numeroSolicitud) {
                    document.getElementById('detalleCargando').hidden = true;
                    solicitudActual = null;
                }
            }
        });

        $('#modalDetalleAtencion').on('hidden.bs.modal', function() {
            numeroSolicitud++;

            if (solicitudActual) {
                solicitudActual.abort();
                solicitudActual = null;
            }
        });
    });
</script>
@stop