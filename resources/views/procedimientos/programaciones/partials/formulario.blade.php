@php
$esEdicion = !is_null($registro);

$valorProcedimiento = $esEdicion
? $registro->procedimiento_id
: old('procedimiento_id');

$valorMedico = $esEdicion
? $registro->procedimiento_medico_id
: old('procedimiento_medico_id');

$fechaProgramacion = $esEdicion
? $registro->fecha_programacion->format('Y-m-d')
: old('fecha_programacion', now()->format('Y-m-d'));

$horaInicio = $esEdicion
? substr($registro->hora_inicio, 0, 5)
: old('hora_inicio', '08:00');

$horaFin = $esEdicion
? substr($registro->hora_fin, 0, 5)
: old('hora_fin', '13:00');

$intervalo = $esEdicion
? $registro->intervalo_minutos
: old('intervalo_minutos', 15);

$cantidadMaxima = $esEdicion
? $registro->cantidad_maxima
: old('cantidad_maxima');
@endphp

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>
                Procedimiento
                <span class="text-danger">*</span>
            </label>

            <select
                name="procedimiento_id"
                class="form-control"
                required>
                <option value="">Seleccione...</option>

                @foreach ($procedimientos as $procedimiento)
                <option
                    value="{{ $procedimiento->id }}"
                    {{ (string) $valorProcedimiento ===
                            (string) $procedimiento->id
                            ? 'selected'
                            : '' }}>
                    {{ $procedimiento->servicio->nombre ?? 'SIN SERVICIO' }}
                    - {{ $procedimiento->nombre }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>
                Médico responsable
                <span class="text-danger">*</span>
            </label>

            <select
                name="procedimiento_medico_id"
                class="form-control"
                required>
                <option value="">Seleccione...</option>

                @foreach ($medicos as $medico)
                <option
                    value="{{ $medico->id }}"
                    {{ (string) $valorMedico ===
                            (string) $medico->id
                            ? 'selected'
                            : '' }}>
                    {{ $medico->nombre_completo }}
                    - {{ $medico->especialidad }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>
                Fecha de programación
                <span class="text-danger">*</span>
            </label>

            <input
                type="date"
                name="fecha_programacion"
                class="form-control"
                value="{{ $fechaProgramacion }}"
                required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>
                Hora inicial
                <span class="text-danger">*</span>
            </label>

            <input
                type="time"
                name="hora_inicio"
                class="form-control"
                value="{{ $horaInicio }}"
                required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>
                Hora final
                <span class="text-danger">*</span>
            </label>

            <input
                type="time"
                name="hora_fin"
                class="form-control"
                value="{{ $horaFin }}"
                required>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group mb-md-0">
            <label>
                Intervalo entre cupos
                <span class="text-danger">*</span>
            </label>

            <div class="input-group">
                <input
                    type="number"
                    name="intervalo_minutos"
                    class="form-control"
                    value="{{ $intervalo }}"
                    min="5"
                    max="480"
                    required>

                <div class="input-group-append">
                    <span class="input-group-text">minutos</span>
                </div>
            </div>

            <small class="text-muted">
                Ejemplo: 15 genera cupos cada 15 minutos.
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-0">
            <label>Cantidad máxima de cupos</label>

            <input
                type="number"
                name="cantidad_maxima"
                class="form-control"
                value="{{ $cantidadMaxima }}"
                min="1"
                max="500"
                placeholder="Opcional">

            <small class="text-muted">
                Si queda vacío, se calculará según el horario y el intervalo.
            </small>
        </div>
    </div>

</div>