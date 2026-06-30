@extends('adminlte::page')

@section('title', 'Radiología - Citas')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>
    <i class="fas fa-x-ray text-primary"></i>
    Citas de Radiología
</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif


<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Listado de Citas Radiológicas</h3>

        <div class="card-tools">
            <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalAgregarCita">
                <i class="fas fa-plus"></i> Agregar Cita
            </button>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('radiologia.citas') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text"
                        name="buscar"
                        value="{{ $buscar ?? '' }}"
                        class="form-control"
                        placeholder="Buscar por cuenta, paciente, HC, modalidad o estudio">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('radiologia.citas') }}" class="btn btn-secondary btn-block">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th>Cuenta</th>
                        <th>Paciente</th>
                        <th>HC</th>
                        <th>Modalidad</th>
                        <th>Estudio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th width="100">Ticket</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($citas as $item)
                    <tr>
                        <td>{{ $item->id_cuenta_atencion }}</td>
                        <td>{{ $item->paciente }}</td>
                        <td>{{ $item->nro_historia_clinica }}</td>
                        <td>{{ $item->modalidad }}</td>
                        <td>{{ $item->estudio }}</td>
                        <td>{{ date('d-m-Y', strtotime($item->fecha_cita)) }}</td>
                        <td>{{ $item->hora_cita }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ $item->estado }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('radiologia.ticket', $item->id) }}"
                                target="_blank"
                                class="btn btn-dark btn-sm">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            No existen citas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="card-footer clearfix">
        <div class="float-left">
            Mostrando {{ $citas->firstItem() }} a {{ $citas->lastItem() }}
            de {{ $citas->total() }} registros
        </div>

        <div class="float-right">
            {{ $citas->links() }}
        </div>
    </div>
</div>


{{-- MODAL AGREGAR CITA --}}
<div class="modal fade" id="modalAgregarCita" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">

        <form method="POST" action="{{ route('radiologia.store') }}" id="formCitaRadiologia">
            @csrf

            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> Nueva Cita Radiológica
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- BUSCAR CUENTA --}}
                    <div class="border rounded p-2 mb-2">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label class="mb-1">Nro Cuenta de Atención</label>
                                <input type="text"
                                    id="cuenta_buscar"
                                    class="form-control form-control-sm"
                                    placeholder="Ejemplo: 2375500">
                            </div>

                            <div class="col-md-3">
                                <button type="button"
                                    id="btnBuscarCuenta"
                                    class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>

                            <div class="col-md-4">
                                <div id="mensajeBusqueda" class="mb-0"></div>
                            </div>
                        </div>
                    </div>

                    <div id="bloquePaciente" style="display:none;">

                        {{-- DATOS DEL PACIENTE --}}
                        <div class="border rounded p-2 mb-2">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user-injured"></i> Datos del Paciente
                            </h6>

                            <input type="hidden" name="id_cuenta_atencion" id="id_cuenta_atencion">
                            <input type="hidden" name="paciente" id="paciente">
                            <input type="hidden" name="nro_historia_clinica" id="nro_historia_clinica">
                            <input type="hidden" name="telefono" id="telefono">
                            <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento">
                            <input type="hidden" name="edad" id="edad">
                            <input type="hidden" name="tipo_edad" id="tipo_edad">
                            <input type="hidden" name="fecha_ingreso" id="fecha_ingreso">
                            <input type="hidden" name="hora_ingreso" id="hora_ingreso">
                            <input type="hidden" name="tipo_servicio" id="tipo_servicio">
                            <input type="hidden" name="nombre_servicio" id="nombre_servicio">
                            <input type="hidden" name="fuente_financiamiento" id="fuente_financiamiento">

                            <div class="row small">
                                <div class="col-md-2">
                                    <strong>Cuenta:</strong><br>
                                    <span id="txtCuenta"></span>
                                </div>

                                <div class="col-md-4">
                                    <strong>Paciente:</strong><br>
                                    <span id="txtPaciente"></span>
                                </div>

                                <div class="col-md-2">
                                    <strong>HC:</strong><br>
                                    <span id="txtHistoria"></span>
                                </div>

                                <div class="col-md-2">
                                    <strong>Edad:</strong><br>
                                    <span id="txtEdad"></span>
                                </div>

                                <div class="col-md-2">
                                    <strong>Seguro:</strong><br>
                                    <span id="txtFF"></span>
                                </div>
                            </div>

                            <div class="row small mt-2">
                                <div class="col-md-3">
                                    <strong>Teléfono:</strong><br>
                                    <span id="txtTelefono"></span>
                                </div>

                                <div class="col-md-3">
                                    <strong>Fecha Ingreso:</strong><br>
                                    <span id="txtFechaIngreso"></span>
                                </div>

                                <div class="col-md-3">
                                    <strong>Tipo Servicio:</strong><br>
                                    <span id="txtTipoServicio"></span>
                                </div>

                                <div class="col-md-3">
                                    <strong>Servicio:</strong><br>
                                    <span id="txtNombreServicio"></span>
                                </div>
                            </div>
                        </div>

                        {{-- DATOS DE LA CITA --}}
                        <div class="border rounded p-2">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calendar-check"></i> Datos de la Cita
                            </h6>

                            <div class="row">
                                <div class="col-md-3">
                                    <label class="mb-1">Modalidad</label>
                                    <select name="modalidad" id="modalidad"
                                        class="form-control form-control-sm"
                                        required>
                                        <option value="">Seleccione</option>
                                        <option value="RAYOS X">Rayos X</option>
                                        <option value="TOMOGRAFIA">Tomografía</option>
                                        <option value="ECOGRAFIA">Ecografía</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="mb-1">Código</label>
                                    <input type="text"
                                        name="codigo_estudio"
                                        id="codigo_estudio"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="col-md-7">
                                    <label class="mb-1">Descripción</label>
                                    <input type="text"
                                        name="estudio"
                                        id="estudio"
                                        class="form-control form-control-sm"
                                        readonly>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label class="mb-1">Fecha Cita</label>
                                    <input type="date"
                                        name="fecha_cita"
                                        id="fecha_cita"
                                        class="form-control form-control-sm"
                                        required>
                                    <small class="text-muted">Lunes a sábado</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="mb-1">Hora Cita</label>
                                    <div id="contenedor_horas" class="d-flex flex-wrap">
                                        <small class="text-muted">
                                            Seleccione modalidad y fecha para ver horarios.
                                        </small>
                                    </div>

                                    <input type="hidden"
                                        name="hora_cita"
                                        id="hora_cita"
                                        required>
                                    <small class="text-muted">08:00 a 13:00</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="mb-1">Observación</label>
                                    <textarea name="observacion"
                                        class="form-control form-control-sm"
                                        rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-success" id="btnGuardarCita" disabled>
                        <i class="fas fa-save"></i> Guardar e Imprimir Ticket
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>

@stop


@section('js')

<script>
    document.getElementById('formCitaRadiologia')
        .addEventListener('submit', function(e) {

            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(result => {

                    if (!result.success) {
                        alert('Error al registrar la cita');
                        return;
                    }

                    // abre ticket en nueva pestaña
                    window.open(result.ticket_url, '_blank');

                    // cerrar modal
                    $('#modalAgregarCita').modal('hide');

                    // recargar listado
                    location.reload();

                })
                .catch(error => {
                    console.error(error);
                    alert('Error al guardar');
                });

        });
    document.addEventListener('DOMContentLoaded', function() {

        const btnBuscarCuenta = document.getElementById('btnBuscarCuenta');
        const cuentaBuscar = document.getElementById('cuenta_buscar');
        const mensajeBusqueda = document.getElementById('mensajeBusqueda');
        const bloquePaciente = document.getElementById('bloquePaciente');
        const btnGuardarCita = document.getElementById('btnGuardarCita');

        const fechaCita = document.getElementById('fecha_cita');
        const horaCita = document.getElementById('hora_cita');

        const horasDisponibles = [
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '12:00',
            '12:30',
            '13:00'
        ];

        btnBuscarCuenta.addEventListener('click', function() {

            const cuenta = cuentaBuscar.value.trim();

            if (!cuenta) {
                mensajeBusqueda.innerHTML = `
                <div class="alert alert-warning">
                    Ingrese un número de cuenta.
                </div>
            `;
                return;
            }

            mensajeBusqueda.innerHTML = `
            <div class="alert alert-info">
                Buscando información del paciente...
            </div>
        `;

            bloquePaciente.style.display = 'none';
            btnGuardarCita.disabled = true;

            let url = "{{ route('radiologia.buscarCuenta', ':cuenta') }}";
            url = url.replace(':cuenta', cuenta);

            fetch(url)
                .then(response => response.json())
                .then(result => {

                    if (!result.success) {
                        mensajeBusqueda.innerHTML = `
                        <div class="alert alert-danger">
                            ${result.message}
                        </div>
                    `;
                        return;
                    }

                    const p = result.data;

                    document.getElementById('id_cuenta_atencion').value = p.IdCuentaAtencion ?? '';
                    document.getElementById('paciente').value = p.Paciente ?? '';
                    document.getElementById('nro_historia_clinica').value = p.NroHistoriaClinica ?? '';
                    document.getElementById('telefono').value = p.TelPaciente ?? '';
                    document.getElementById('fecha_nacimiento').value = p.FechaNacimiento ?? '';
                    document.getElementById('edad').value = p.Edad ?? '';
                    document.getElementById('tipo_edad').value = p.TipoEdad ?? '';
                    document.getElementById('fecha_ingreso').value = p.FechaIngreso ?? '';
                    document.getElementById('hora_ingreso').value = p.HoraIngreso ?? '';
                    document.getElementById('tipo_servicio').value = p.TipoServicio ?? '';
                    document.getElementById('nombre_servicio').value = p.NombreServicio ?? '';
                    document.getElementById('fuente_financiamiento').value = p.FF ?? '';

                    document.getElementById('txtCuenta').innerText = p.IdCuentaAtencion ?? '';
                    document.getElementById('txtPaciente').innerText = p.Paciente ?? '';
                    document.getElementById('txtHistoria').innerText = p.NroHistoriaClinica ?? '';
                    document.getElementById('txtTelefono').innerText = p.TelPaciente ?? '';
                    document.getElementById('txtEdad').innerText = `${p.Edad ?? ''} ${p.TipoEdad ?? ''}`;
                    document.getElementById('txtFechaIngreso').innerText = formatearFecha(p.FechaIngreso);
                    document.getElementById('txtTipoServicio').innerText = p.TipoServicio ?? '';
                    document.getElementById('txtNombreServicio').innerText = p.NombreServicio ?? '';
                    document.getElementById('txtFF').innerText = p.FF ?? '';

                    mensajeBusqueda.innerHTML = `
                    <div class="alert alert-success">
                        Paciente encontrado correctamente.
                    </div>
                `;

                    bloquePaciente.style.display = 'block';
                    btnGuardarCita.disabled = false;

                })
                .catch(error => {
                    mensajeBusqueda.innerHTML = `
                    <div class="alert alert-danger">
                        Error al consultar la cuenta.
                    </div>
                `;
                });

        });

        /*  fechaCita.addEventListener('change', function() {

            horaCita.innerHTML = '<option value="">Seleccione</option>';

            if (!this.value) {
                return;
            }

            const fecha = new Date(this.value + 'T00:00:00');
            const diaSemana = fecha.getDay();

            if (diaSemana === 0) {
                alert('No se pueden registrar citas los domingos.');
                this.value = '';
                return;
            }

            horasDisponibles.forEach(function(hora) {
                const option = document.createElement('option');
                option.value = hora;
                option.textContent = hora;
                horaCita.appendChild(option);
            });

        });
*/

        const modalidad = document.getElementById('modalidad');
        const contenedorHoras = document.getElementById('contenedor_horas');
        const horaCitaInput = document.getElementById('hora_cita');

        modalidad.addEventListener('change', cargarHorarios);
        fechaCita.addEventListener('change', cargarHorarios);

        function cargarHorarios() {

            const modalidadSeleccionada = modalidad.value;
            const fechaSeleccionada = fechaCita.value;

            horaCitaInput.value = '';

            contenedorHoras.innerHTML = `
        <small class="text-muted">
            Seleccione modalidad y fecha para ver horarios.
        </small>
    `;

            if (!modalidadSeleccionada || !fechaSeleccionada) {
                return;
            }

            const fecha = new Date(fechaSeleccionada + 'T00:00:00');

            if (fecha.getDay() === 0) {
                alert('No se pueden registrar citas los domingos.');
                fechaCita.value = '';
                return;
            }

            let url = "{{ route('radiologia.horariosDisponibles') }}";
            url += `?modalidad=${encodeURIComponent(modalidadSeleccionada)}&fecha=${encodeURIComponent(fechaSeleccionada)}`;

            contenedorHoras.innerHTML = `
        <small class="text-muted">
            Cargando horarios...
        </small>
    `;

            fetch(url)
                .then(response => response.json())
                .then(result => {

                    if (!result.success) {
                        contenedorHoras.innerHTML = `
                    <small class="text-danger">
                        No se pudieron cargar los horarios.
                    </small>
                `;
                        return;
                    }

                    contenedorHoras.innerHTML = '';

                    result.horarios.forEach(item => {

                        const boton = document.createElement('button');

                        boton.type = 'button';
                        boton.textContent = item.hora;

                        // Guardamos la hora original
                        boton.dataset.hora = item.hora;

                        boton.className = 'hora-cupo';
                        if (item.ocupado) {
                            boton.classList.add('ocupada');
                            boton.disabled = true;
                            boton.title = 'Horario ocupado';
                        } else {
                            boton.addEventListener('click', function() {

                                document.querySelectorAll('.hora-cupo').forEach(btn => {

                                    btn.classList.remove('activa');

                                    // Restaurar texto original
                                    btn.innerHTML = btn.dataset.hora;

                                });

                                boton.classList.add('activa');

                                // Agregar check visual
                                boton.innerHTML = '✓ ' + item.hora;

                                horaCitaInput.value = item.hora;

                            });
                        }

                        contenedorHoras.appendChild(boton);

                    });

                })
                .catch(() => {
                    contenedorHoras.innerHTML = `
                <small class="text-danger">
                    Error al consultar horarios.
                </small>
            `;
                });
        }

        function formatearFecha(fecha) {

            if (!fecha) {
                return '';
            }

            const partes = fecha.substring(0, 10).split('-');

            if (partes.length !== 3) {
                return fecha;
            }

            return `${partes[2]}-${partes[1]}-${partes[0]}`;
        }

    });

    $('#modalAgregarCita').on('show.bs.modal', function() {

        document.getElementById('formCitaRadiologia').reset();

        document.getElementById('mensajeBusqueda').innerHTML = '';

        document.getElementById('bloquePaciente').style.display = 'none';

        document.getElementById('btnGuardarCita').disabled = true;

        document.getElementById('hora_cita').innerHTML = '<option value="">Seleccione</option>';

        const camposTexto = [
            'txtCuenta',
            'txtPaciente',
            'txtHistoria',
            'txtTelefono',
            'txtEdad',
            'txtFechaIngreso',
            'txtTipoServicio',
            'txtNombreServicio',
            'txtFF'
        ];

        camposTexto.forEach(function(id) {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.innerText = '';
            }
        });

    });

    document.getElementById('codigo_estudio')
        .addEventListener('blur', function() {

            let codigo = this.value.trim();

            if (codigo === '') {
                return;
            }

            let urlProcedimiento = "{{ route('radiologia.buscarProcedimiento', ':codigo') }}";
            urlProcedimiento = urlProcedimiento.replace(':codigo', codigo);

            fetch(urlProcedimiento)
                .then(response => response.json())
                .then(result => {

                    if (!result.success) {

                        document.getElementById('estudio').value = '';

                        alert('Código no encontrado');

                        return;
                    }

                    document.getElementById('estudio').value = result.data.Nombre;

                });

        });
</script>

@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">

<style>
    .hora-cupo {
        border: 1px solid #BBDCF5;
        background: #EFF6FC;
        color: #005A9E;
        padding: 5px 8px;
        margin: 3px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .hora-cupo:hover {
        background: #DDEEFF;
    }

    .hora-cupo.activa {
        background: #0078D4 !important;
        color: #ffffff !important;
        border: 2px solid #005A9E !important;
        font-weight: bold;
        box-shadow: 0 0 6px rgba(0, 120, 212, .4);
    }

    .hora-cupo.ocupada {
        background: #EDEBE9;
        color: #8A8886;
        border-color: #DADCE0;
        cursor: not-allowed;
        opacity: .55;
        text-decoration: line-through;
    }
</style>
@stop