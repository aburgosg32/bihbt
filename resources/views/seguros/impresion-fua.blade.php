@extends('adminlte::page')

@section('title', 'Impresión FUA | Seguros')

@section('content_header')
<h1>
    Seguros
    <small class="text-muted">/ Impresión FUA</small>
</h1>
@stop

@section('content')
<div class="container-fluid seguros-impresion-fua">

    <div class="fua-encabezado">
        <div>
            <h4 class="mb-1">Control de impresión FUA</h4>
            <p class="text-muted mb-0">
                Servicios de Consulta Externa
            </p>
        </div>

        <form method="POST"
            action="{{ route('seguros.impresion-fua.restablecer-todos') }}"
            onsubmit="return confirm('¿Cambiar a Médico todos los servicios de Consulta Externa que actualmente imprimen en Admisión?');">
            @csrf

            <button type="submit"
                class="btn btn-primary"
                @disabled($enAdmision===0)>
                <i class="fas fa-undo mr-1"></i>
                Restablecer todos a Médico
            </button>
        </form>
    </div>

    <form method="GET"
        action="{{ route('seguros.impresion-fua.index') }}"
        class="form-inline mb-3">

        <label for="especialidad"
            class="font-weight-bold mr-2">
            Especialidad:
        </label>

        <select name="especialidad"
            id="especialidad"
            class="form-control mr-2 mb-2 mb-sm-0"
            style="min-width: 280px;"
            onchange="this.form.submit()">

            <option value="">Todas las especialidades</option>

            @foreach ($especialidades as $especialidad)
            <option value="{{ $especialidad->IdEspecialidad }}"
                @selected(
                $idEspecialidad===(int) $especialidad->IdEspecialidad
                )>
                {{ $especialidad->Nombre }}
            </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-1"></i>
            Filtrar
        </button>
    </form>



    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if (session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        No se pudo cambiar la configuración solicitada.
    </div>
    @endif

    <div class="fua-resumen mb-3">
        <span class="fua-indicador fua-indicador-medico">
            <i class="fas fa-user-md mr-1"></i>
            Médico: {{ $enMedico }}
        </span>

        <span class="fua-indicador fua-indicador-admision">
            <i class="fas fa-user-friends mr-1"></i>
            Admisión: {{ $enAdmision }}
        </span>

        <span class="fua-indicador fua-indicador-total">
            Total: {{ $servicios->count() }}
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 85px;">Código</th>
                            <th>Servicio / Consultorio</th>
                            <th>Especialidad</th>
                            <th class="text-center" style="width: 145px;">
                                Estado
                            </th>
                            <th class="text-center" style="width: 160px;">
                                Impresión FUA
                            </th>
                            <th class="text-center" style="width: 190px;">
                                Acción
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($servicios as $servicio)
                        @php
                        $imprimeAdmision =
                        (int) $servicio->UsaGalenHos === 1;
                        @endphp

                        <tr>
                            <td>{{ $servicio->IdServicio }}</td>

                            <td class="font-weight-bold">
                                {{ $servicio->Nombre }}
                            </td>

                            <td>
                                {{ $servicio->Especialidad ?: '—' }}
                            </td>

                            <td class="text-center">
                                @if ((int) $servicio->IdEstado === 1)
                                <span class="badge badge-success p-2">
                                    Activo
                                </span>
                                @else
                                <span class="badge badge-secondary p-2">
                                    Inactivo
                                </span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($imprimeAdmision)
                                <span class="fua-estado fua-estado-admision">
                                    Admisión
                                </span>
                                @else
                                <span class="fua-estado fua-estado-medico">
                                    Médico
                                </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <form method="POST"
                                    action="{{ route('seguros.impresion-fua.cambiar', $servicio->IdServicio) }}"
                                    class="m-0"
                                    onsubmit="return confirm('¿Cambiar la impresión FUA de {{ addslashes($servicio->Nombre) }} a {{ $imprimeAdmision ? 'Médico' : 'Admisión' }}?');">
                                    @csrf

                                    <input type="hidden"
                                        name="usa_galen_hos"
                                        value="{{ $imprimeAdmision ? 0 : 1 }}">

                                    @if ($imprimeAdmision)
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-user-md mr-1"></i>
                                        Volver a Médico
                                    </button>
                                    @else
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-user-friends mr-1"></i>
                                        Pasar a Admisión
                                    </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted py-4">
                                No se encontraron servicios de
                                Consulta Externa.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
<style>
    .seguros-impresion-fua .fua-encabezado {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .seguros-impresion-fua .fua-resumen {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .seguros-impresion-fua .fua-indicador {
        display: inline-block;
        border-radius: 8px;
        padding: 7px 11px;
        font-size: 13px;
        font-weight: 600;
    }

    .seguros-impresion-fua .fua-indicador-medico,
    .seguros-impresion-fua .fua-estado-medico {
        background: #e4f5ea;
        color: #17623a;
    }

    .seguros-impresion-fua .fua-indicador-admision,
    .seguros-impresion-fua .fua-estado-admision {
        background: #fff0dc;
        color: #8d5000;
    }

    .seguros-impresion-fua .fua-indicador-total {
        background: #e9f2fc;
        color: #18558d;
    }

    .seguros-impresion-fua .fua-estado {
        display: inline-block;
        min-width: 85px;
        border-radius: 12px;
        padding: 5px 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .seguros-impresion-fua .table th,
    .seguros-impresion-fua .table td {
        vertical-align: middle;
        font-size: 13px;
    }
</style>
@stop