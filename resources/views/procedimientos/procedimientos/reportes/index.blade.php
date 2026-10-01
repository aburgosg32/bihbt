@extends('adminlte::page')

@section('title', 'Reportes de procedimientos')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<div>
    <h1 class="m-0">
        <i class="fas fa-file-alt text-primary mr-2"></i>
        Reportes de citas
    </h1>

    <small class="text-muted">
        Consulta y exportación de citas de procedimientos
    </small>
</div>
@stop

@section('content')
<div class="container-fluid">

    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i>
                Filtros
            </h3>
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route(
                    'procedimientos.reportes.index'
                ) }}">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Buscar</label>

                            <input
                                type="text"
                                name="buscar"
                                class="form-control"
                                value="{{ request('buscar') }}"
                                placeholder="Cuenta, HC, paciente o procedimiento">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Procedimiento</label>

                            <select
                                name="procedimiento_id"
                                class="form-control">
                                <option value="">
                                    Todos
                                </option>

                                @foreach ($procedimientos as $procedimiento)
                                <option
                                    value="{{ $procedimiento->id }}"
                                    {{ (string) request('procedimiento_id')
                                            === (string) $procedimiento->id
                                            ? 'selected'
                                            : '' }}>
                                    {{ $procedimiento->servicio->nombre
                                            ?? 'SIN SERVICIO' }}
                                    -
                                    {{ $procedimiento->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Estado</label>

                            <select
                                name="estado"
                                class="form-control">
                                <option value="">Todos</option>

                                <option
                                    value="PROGRAMADA"
                                    {{ request('estado') === 'PROGRAMADA'
                                        ? 'selected'
                                        : '' }}>
                                    Programada
                                </option>

                                <option
                                    value="ATENDIDA"
                                    {{ request('estado') === 'ATENDIDA'
                                        ? 'selected'
                                        : '' }}>
                                    Atendida
                                </option>

                                <option
                                    value="ANULADA"
                                    {{ request('estado') === 'ANULADA'
                                        ? 'selected'
                                        : '' }}>
                                    Anulada
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha inicial</label>

                            <input
                                type="date"
                                name="fecha_inicio"
                                class="form-control"
                                value="{{ request('fecha_inicio') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-md-0">
                            <label>Fecha final</label>

                            <input
                                type="date"
                                name="fecha_fin"
                                class="form-control"
                                value="{{ request('fecha_fin') }}">
                        </div>
                    </div>

                    <div class="col-md-10 d-flex align-items-end">
                        <div class="form-group mb-0">

                            <button
                                type="submit"
                                class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>
                                Buscar
                            </button>

                            <a
                                href="{{ route(
                                    'procedimientos.reportes.index'
                                ) }}"
                                class="btn btn-secondary">
                                <i class="fas fa-eraser mr-1"></i>
                                Limpiar
                            </a>

                            <a
                                href="{{ route(
                                    'procedimientos.reportes.pdf',
                                    request()->query()
                                ) }}"
                                target="_blank"
                                class="btn btn-danger">
                                <i class="fas fa-file-pdf mr-1"></i>
                                Generar PDF
                            </a>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-1"></i>
                Citas registradas
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary px-2 py-2">
                    Total: {{ $citas->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cuenta / HC</th>
                            <th>Paciente</th>
                            <th>Procedimiento</th>
                            <th>Médico</th>
                            <th>Servicio origen</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Ticket</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($citas as $cita)

                        @php
                        $cupo = $cita->cupo;
                        $programacion = $cupo->programacion;
                        $procedimiento =
                        $programacion->procedimiento;
                        $medico = $programacion->medico;
                        @endphp

                        <tr>
                            <td class="align-middle">
                                {{ $programacion
                                    ->fecha_programacion
                                    ->format('d/m/Y') }}
                            </td>

                            <td class="align-middle font-weight-bold">
                                {{ substr($cupo->hora, 0, 5) }}
                            </td>

                            <td class="align-middle">
                                <div>
                                    Cuenta:
                                    <strong>
                                        {{ $cita->numero_cuenta }}
                                    </strong>
                                </div>

                                <small class="text-muted">
                                    HC:
                                    {{ $cita->historia_clinica
                                        ?? 'NO REGISTRA' }}
                                </small>
                            </td>

                            <td class="align-middle">
                                <strong>
                                    {{ $cita->paciente }}
                                </strong>

                                <small class="d-block text-muted">
                                    {{ $cita->telefono
                                        ?? 'SIN TELÉFONO' }}
                                </small>
                            </td>

                            <td class="align-middle">
                                <small class="text-muted d-block">
                                    {{ $procedimiento
                                        ->servicio
                                        ->nombre ?? '' }}
                                </small>

                                <strong>
                                    {{ $procedimiento->nombre }}
                                </strong>
                            </td>

                            <td class="align-middle">
                                {{ $medico->nombre_completo
                                    ?? 'SIN MÉDICO' }}
                            </td>

                            <td class="align-middle">
                                {{ $cita->servicio_origen
                                    ?? 'NO REGISTRA' }}
                            </td>

                            <td class="text-center align-middle">
                                @if ($cita->estado === 'PROGRAMADA')
                                <span class="badge badge-primary">
                                    PROGRAMADA
                                </span>
                                @elseif ($cita->estado === 'ATENDIDA')
                                <span class="badge badge-success">
                                    ATENDIDA
                                </span>
                                @else
                                <span class="badge badge-secondary">
                                    {{ $cita->estado }}
                                </span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                <a
                                    href="{{ route(
                                        'procedimientos.citas.ticket',
                                        $cita
                                    ) }}"
                                    target="_blank"
                                    class="btn btn-info btn-sm"
                                    title="Ver ticket">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>

                        @empty

                        <tr>
                            <td
                                colspan="9"
                                class="text-center text-muted py-5">
                                No existen citas con los filtros seleccionados.
                            </td>
                        </tr>

                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>

        @if ($citas->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $citas->links() }}
            </div>
        </div>
        @endif

    </div>

</div>
@stop