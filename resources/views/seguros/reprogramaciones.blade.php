@extends('adminlte::page')

@section('title', 'Reprogramaciones | Seguros')

@section('content_header')
<h1>Seguros <small class="text-muted">/ Reprogramaciones</small></h1>
@stop

@section('content')
<div class="container-fluid seguros-reprogramaciones">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @php
    $reprogramables = $citas->filter(fn ($cita) => $cita->reprogramable);
    $primera = $citas->first();
    @endphp

    <div class="row">
        {{-- IZQUIERDA: BÚSQUEDA Y REPROGRAMACIÓN --}}
        <div class="col-12 col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong><i class="fas fa-calendar-alt mr-1"></i> Reprogramación médica</strong>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('seguros.reprogramaciones.index') }}">
                        <label for="cuenta">Número de cuenta</label>

                        <div class="input-group">
                            <input
                                id="cuenta"
                                type="number"
                                name="cuenta"
                                min="1"
                                class="form-control"
                                value="{{ $cuenta }}"
                                placeholder="Ingrese el número de cuenta"
                                required>

                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($cuenta !== null && $reprogramables->isNotEmpty())
                    <hr class="my-4">

                    <form
                        id="form-reprogramacion"
                        method="POST"
                        action="{{ route('seguros.reprogramaciones.store') }}">
                        @csrf
                        <input type="hidden" name="cuenta" value="{{ $cuenta }}">

                        <div class="form-group">
                            <label for="id_cita">Cita actual</label>

                            <select
                                id="id_cita"
                                name="id_cita"
                                class="form-control"
                                required>
                                <option value="">Seleccione la cita</option>

                                @foreach ($reprogramables as $cita)
                                <option
                                    value="{{ $cita->IdCita }}"
                                    @selected(old('id_cita')==$cita->IdCita)
                                    >
                                    #{{ $cita->IdCita }} ·
                                    {{ $cita->fecha_mostrar }}
                                    {{ trim($cita->HoraInicio) }} ·
                                    {{ $cita->Servicio ?: $cita->Especialidad }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_nueva">Nueva fecha</label>

                            <input
                                id="fecha_nueva"
                                type="date"
                                class="form-control"
                                min="{{ now('America/Lima')->toDateString() }}"
                                value="{{ old('fecha_nueva') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="programacion">Servicio y médico</label>

                            <select
                                id="programacion"
                                name="programacion"
                                class="form-control"
                                required
                                disabled>
                                <option value="">Seleccione primero una fecha</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora disponible</label>

                            <select
                                id="hora_inicio"
                                name="hora_inicio"
                                class="form-control"
                                required
                                disabled>
                                <option value="">Seleccione primero un servicio</option>
                            </select>
                        </div>

                        <div
                            id="aviso-cupos"
                            class="small text-muted mb-3"
                            aria-live="polite"></div>

                        <button
                            id="boton-reprogramar"
                            type="submit"
                            class="btn btn-primary"
                            disabled>
                            <i class="fas fa-calendar-check mr-1"></i>
                            Reprogramar
                        </button>

                        <a
                            href="{{ route('seguros.reprogramaciones.index') }}"
                            class="btn btn-outline-secondary ml-1">
                            Limpiar
                        </a>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- DERECHA: DATOS DE LA CUENTA Y CITA ACTUAL --}}
        <div class="col-12 col-lg-8 mb-3">
            @if ($cuenta === null)
            <div class="alert alert-info">
                Ingrese un número de cuenta para consultar la cita.
            </div>
            @elseif ($citas->isEmpty())
            <div class="alert alert-warning">
                No se encontraron citas para la cuenta {{ $cuenta }}.
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Resultados de la búsqueda · Cuenta {{ $cuenta }}</strong>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm tabla-datos mb-0">
                            <tbody>
                                <tr>
                                    <th>Financiamiento</th>
                                    <td>{{ $primera->Financiamiento ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Paciente</th>
                                    <td>{{ $primera->paciente ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>DNI</th>
                                    <td>{{ $primera->NroDocumento ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4 mb-2">Cita actual</h5>

                    @foreach ($citas as $cita)
                    <div class="cita-resumen">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Cita #{{ $cita->IdCita }}</strong>

                            @if ($cita->reprogramable)
                            <span class="badge badge-success">Reprogramable</span>
                            @else
                            <span class="badge badge-secondary">No reprogramable</span>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm tabla-datos mb-0">
                                <tbody>
                                    <tr>
                                        <th>Especialidad</th>
                                        <td>{{ $cita->Especialidad ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Servicio</th>
                                        <td>{{ $cita->Servicio ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Médico</th>
                                        <td>{{ $cita->medico ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha y hora</th>
                                        <td>
                                            {{ $cita->fecha_mostrar }}
                                            · {{ trim($cita->HoraInicio) }}
                                            – {{ trim($cita->HoraFin) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach

                    @if ($reprogramables->isEmpty())
                    <div class="alert alert-info mt-3 mb-0">
                        Esta cuenta no tiene citas pendientes que se puedan reprogramar.
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="modalConfirmarReprogramacion" tabindex="-1"
    role="dialog" aria-labelledby="tituloConfirmacion" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloConfirmacion">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Confirmar reprogramación
                </h5>

                <button type="button" class="close text-white"
                    data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="mb-3">
                    Revise los datos antes de cambiar la cita:
                </p>

                <dl class="row mb-0">
                    <dt class="col-4">Cita</dt>
                    <dd class="col-8" id="confirmar-cita"></dd>

                    <dt class="col-4">Nueva fecha</dt>
                    <dd class="col-8" id="confirmar-fecha"></dd>

                    <dt class="col-4">Servicio y médico</dt>
                    <dd class="col-8" id="confirmar-servicio"></dd>

                    <dt class="col-4">Nueva hora</dt>
                    <dd class="col-8 mb-0">
                        <span class="badge badge-success px-2 py-1"
                            id="confirmar-hora"></span>
                    </dd>
                </dl>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                    data-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" id="confirmar-envio"
                    class="btn btn-primary">
                    <i class="fas fa-check mr-1"></i>
                    Sí, reprogramar
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
<style>
    .seguros-reprogramaciones .card {
        border-radius: .45rem;
    }

    .seguros-reprogramaciones .tabla-datos th,
    .seguros-reprogramaciones .tabla-datos td {
        padding: .6rem .4rem;
        vertical-align: middle;
        border-top: 1px solid #e7ebef;
    }

    .seguros-reprogramaciones .tabla-datos th {
        width: 32%;
        min-width: 125px;
        color: #334155;
    }

    .seguros-reprogramaciones .cita-resumen {
        border-top: 1px solid #dfe5eb;
        padding-top: 1rem;
        margin-top: 1rem;
    }

    .seguros-reprogramaciones .cita-resumen:first-of-type {
        margin-top: .5rem;
    }

    @media (max-width: 991.98px) {
        .seguros-reprogramaciones .tabla-datos th {
            width: 40%;
        }
    }
</style>
@stop

@section('js')
@if ($cuenta !== null && $reprogramables->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cuenta = @json($cuenta);
        const urlProgramaciones = @json(route('seguros.reprogramaciones.programaciones'));
        const urlHoras = @json(route('seguros.reprogramaciones.horas'));

        const cita = document.getElementById('id_cita');
        const fecha = document.getElementById('fecha_nueva');
        const programacion = document.getElementById('programacion');
        const hora = document.getElementById('hora_inicio');
        const aviso = document.getElementById('aviso-cupos');
        const boton = document.getElementById('boton-reprogramar');
        const formulario = document.getElementById('form-reprogramacion');

        let peticion = 0;

        function limpiar(select, mensaje) {
            select.replaceChildren(new Option(mensaje, ''));
            select.disabled = true;
        }

        async function consultar(url, parametros) {
            const respuesta = await fetch(url + '?' + new URLSearchParams(parametros), {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('No se pudieron consultar los cupos. Intente nuevamente.');
            }

            return respuesta.json();
        }

        async function cargarProgramaciones() {
            const actual = ++peticion;

            limpiar(programacion, 'Consultando programaciones...');
            limpiar(hora, 'Seleccione primero un servicio');
            boton.disabled = true;
            aviso.textContent = '';

            if (!fecha.value || !cita.value) {
                limpiar(programacion, 'Seleccione la cita y la fecha');
                return;
            }

            try {
                const filas = await consultar(urlProgramaciones, {
                    fecha: fecha.value
                });

                if (actual !== peticion) return;

                limpiar(
                    programacion,
                    filas.length ?
                    'Seleccione un servicio y médico' :
                    'No hay programaciones para esta fecha'
                );

                filas.forEach(function(fila) {
                    programacion.add(new Option(fila.etiqueta, fila.id));
                });

                programacion.disabled = filas.length === 0;
                aviso.textContent = filas.length ?
                    'Seleccione un servicio para consultar sus horas libres.' :
                    'Pruebe con otra fecha.';
            } catch (error) {
                if (actual === peticion) {
                    limpiar(programacion, 'Error al consultar programaciones');
                    aviso.textContent = error.message;
                }
            }
        }

        async function cargarHoras() {
            const actual = ++peticion;

            limpiar(hora, 'Consultando horas...');
            boton.disabled = true;
            aviso.textContent = '';

            if (!programacion.value || !cita.value) {
                limpiar(hora, 'Seleccione primero un servicio');
                return;
            }

            try {
                const horas = await consultar(urlHoras, {
                    programacion: programacion.value,
                    id_cita: cita.value,
                    cuenta: cuenta
                });

                if (actual !== peticion) return;

                limpiar(
                    hora,
                    horas.length ? 'Seleccione una hora' : 'Sin cupos disponibles'
                );

                horas.forEach(function(valor) {
                    hora.add(new Option(valor, valor));
                });

                hora.disabled = horas.length === 0;
                aviso.textContent = horas.length ?
                    horas.length + ' cupo(s) disponible(s).' :
                    'Esta programación no tiene cupos libres; seleccione otra.';
            } catch (error) {
                if (actual === peticion) {
                    limpiar(hora, 'Error al consultar horas');
                    aviso.textContent = error.message;
                }
            }
        }

        cita.addEventListener('change', cargarProgramaciones);
        fecha.addEventListener('change', cargarProgramaciones);
        programacion.addEventListener('change', cargarHoras);

        hora.addEventListener('change', function() {
            boton.disabled = !hora.value;
        });

        const confirmarEnvio = document.getElementById('confirmar-envio');
        let enviando = false;

        formulario.addEventListener('submit', function(evento) {
            evento.preventDefault();

            if (!formulario.reportValidity() ||
                !cita.value ||
                !programacion.value ||
                !hora.value) {
                return;
            }

            document.getElementById('confirmar-cita').textContent =
                '#' + cita.value;

            const partesFecha = fecha.value.split('-');
            document.getElementById('confirmar-fecha').textContent =
                partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];

            document.getElementById('confirmar-servicio').textContent =
                programacion.selectedOptions[0].textContent.trim();

            document.getElementById('confirmar-hora').textContent =
                hora.value;

            $('#modalConfirmarReprogramacion').modal('show');
        });

        confirmarEnvio.addEventListener('click', function() {
            if (enviando) return;

            enviando = true;
            confirmarEnvio.disabled = true;
            confirmarEnvio.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            boton.disabled = true;
            formulario.submit();
        });

        if (cita.value && fecha.value) {
            cargarProgramaciones();
        }
    });
</script>
@endif
@stop