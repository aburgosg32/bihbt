<?php

namespace App\Http\Controllers;

use App\Models\QuirofanoProgramacion;
use App\Models\QuirofanoSala;
use App\Models\QuirofanoSolicitud;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuirofanoProgramacionController extends Controller
{
    /**
     * Muestra solicitudes pendientes y programación
     * de la fecha seleccionada.
     */
    public function index(Request $request)
    {
        $fecha = $request->input(
            'fecha',
            now()->toDateString()
        );

        /*
         * Se conserva el orden de envío como referencia,
         * pero ya no se bloquea la selección.
         */
        $solicitudesPendientes =
            QuirofanoSolicitud::query()
            ->where('estado', 'ENVIADA')
            ->orderBy('fecha_envio')
            ->orderBy('id')
            ->get();

        $programaciones =
            QuirofanoProgramacion::query()
            ->with([
                'solicitud',
                'sala',
            ])
            ->whereDate(
                'fecha_hora_inicio',
                $fecha
            )
            ->where(
                'estado',
                '<>',
                'CANCELADA'
            )
            ->orderBy('fecha_hora_inicio')
            ->get();

        $salas = QuirofanoSala::query()
            ->where('operativa', true)
            ->orderBy('tipo_sala')
            ->orderBy('codigo')
            ->get();

        return view(
            'quirofano.programacion.index',
            compact(
                'fecha',
                'solicitudesPendientes',
                'programaciones',
                'salas'
            )
        );
    }

    /**
     * Registra manualmente una programación.
     */
    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'solicitud_id' => [
                    'required',
                    'integer',
                    'exists:quirofano_solicitudes,id',
                ],
                'sala_id' => [
                    'required',
                    'integer',
                    'exists:quirofano_salas,id',
                ],
                'fecha_operacion' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                ],
                'hora_inicio' => [
                    'required',
                    'date_format:H:i',
                ],
                'observaciones' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'solicitud_id.required' =>
                'Seleccione una solicitud.',

                'sala_id.required' =>
                'Seleccione una sala.',

                'fecha_operacion.required' =>
                'Seleccione la fecha de la operación.',

                'fecha_operacion.after_or_equal' =>
                'La fecha no puede ser anterior al día actual.',

                'hora_inicio.required' =>
                'Ingrese la hora probable de inicio.',

                'hora_inicio.date_format' =>
                'La hora de inicio no tiene un formato válido.',
            ]
        );

        $programacion = DB::transaction(
            function () use ($datos) {
                $solicitud =
                    QuirofanoSolicitud::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $datos['solicitud_id']
                    );

                if ($solicitud->estado !== 'ENVIADA') {
                    throw ValidationException::withMessages([
                        'solicitud_id' =>
                        'La solicitud ya fue programada o cambió de estado.',
                    ]);
                }

                if (
                    empty($solicitud
                        ->duracion_estimada_minutos)
                ) {
                    throw ValidationException::withMessages([
                        'solicitud_id' =>
                        'La solicitud no tiene una duración estimada.',
                    ]);
                }

                $yaProgramada =
                    QuirofanoProgramacion::query()
                    ->where(
                        'quirofano_solicitud_id',
                        $solicitud->id
                    )
                    ->exists();

                if ($yaProgramada) {
                    throw ValidationException::withMessages([
                        'solicitud_id' =>
                        'Esta solicitud ya tiene una programación registrada.',
                    ]);
                }

                $sala = QuirofanoSala::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $datos['sala_id']
                    );

                if (!$sala->operativa) {
                    throw ValidationException::withMessages([
                        'sala_id' =>
                        'La sala seleccionada está inoperativa.',
                    ]);
                }

                $this->validarTipoSala(
                    $solicitud,
                    $sala
                );

                $inicio = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $datos['fecha_operacion']
                        . ' '
                        . $datos['hora_inicio']
                );

                if ($inicio->lessThan(now())) {
                    throw ValidationException::withMessages([
                        'hora_inicio' =>
                        'No puede programar una operación en una fecha y hora anterior a la actual.',
                    ]);
                }

                $duracion = (int)
                $solicitud
                    ->duracion_estimada_minutos;

                $fin = $inicio
                    ->copy()
                    ->addMinutes($duracion);

                $disponible = $fin
                    ->copy()
                    ->addMinutes(
                        (int) $sala->minutos_limpieza
                    );

                $this->validarJornada(
                    $sala,
                    $inicio,
                    $fin
                );

                $this->validarDisponibilidad(
                    $sala,
                    $inicio,
                    $disponible
                );

                $programacion =
                    QuirofanoProgramacion::create([
                        'quirofano_solicitud_id' =>
                        $solicitud->id,

                        'quirofano_sala_id' =>
                        $sala->id,

                        'fecha_hora_inicio' =>
                        $inicio,

                        'fecha_hora_fin' =>
                        $fin,

                        'fecha_hora_disponible' =>
                        $disponible,

                        'duracion_operacion_minutos' =>
                        $duracion,

                        'minutos_limpieza' =>
                        $sala->minutos_limpieza,

                        'estado' =>
                        'PROGRAMADA',

                        'usuario_programador_id' =>
                        Auth::id(),

                        'observaciones' =>
                        $datos['observaciones']
                            ?? null,
                    ]);

                $solicitud->update([
                    'estado' => 'PROGRAMADA',
                ]);

                return $programacion;
            }
        );

        return redirect()
            ->route(
                'quirofano.programacion.index',
                [
                    'fecha' =>
                    $programacion
                        ->fecha_hora_inicio
                        ->format('Y-m-d'),
                ]
            )
            ->with(
                'success',
                'La operación fue programada correctamente.'
            );
    }

    /**
     * Comprueba que el tipo de solicitud sea compatible
     * con el tipo de sala.
     */
    private function validarTipoSala(
        QuirofanoSolicitud $solicitud,
        QuirofanoSala $sala
    ): void {
        if (
            $solicitud->tipo_solicitud ===
            'ELECTIVA'
            && $sala->tipo_sala !==
            'PROGRAMADA'
        ) {
            throw ValidationException::withMessages([
                'sala_id' =>
                'Una operación electiva debe programarse en una sala de operaciones programadas.',
            ]);
        }

        if (
            $solicitud->tipo_solicitud ===
            'EMERGENCIA'
            && $sala->tipo_sala !==
            'EMERGENCIA'
        ) {
            throw ValidationException::withMessages([
                'sala_id' =>
                'Una emergencia debe programarse en una sala de emergencia.',
            ]);
        }

        /*
         * Las solicitudes de URGENCIA pueden utilizar
         * una sala programada o una sala de emergencia.
         */
    }

    /**
     * Verifica la jornada de las salas programadas.
     */
    private function validarJornada(
        QuirofanoSala $sala,
        Carbon $inicio,
        Carbon $fin
    ): void {
        if (
            $sala->tipo_sala !==
            'PROGRAMADA'
        ) {
            return;
        }

        $inicioJornada = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $inicio->format('Y-m-d')
                . ' '
                . $sala->hora_inicio_jornada
        );

        $finJornada = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $inicio->format('Y-m-d')
                . ' '
                . $sala->hora_fin_jornada
        );

        if ($inicio->lessThan($inicioJornada)) {
            throw ValidationException::withMessages([
                'hora_inicio' =>
                'Las salas programadas empiezan a atender a las '
                    . $inicioJornada->format('H:i')
                    . '.',
            ]);
        }

        /*
         * La operación debe terminar como máximo
         * a las 20:00. La limpieza sí puede terminar después.
         */
        if ($fin->greaterThan($finJornada)) {
            throw ValidationException::withMessages([
                'hora_inicio' =>
                'La operación terminaría a las '
                    . $fin->format('H:i')
                    . ', fuera del turno médico que finaliza a las '
                    . $finJornada->format('H:i')
                    . '.',
            ]);
        }
    }

    /**
     * Verifica que el nuevo bloque no se cruce con
     * otra operación ni con su tiempo de limpieza.
     */
    private function validarDisponibilidad(
        QuirofanoSala $sala,
        Carbon $inicio,
        Carbon $disponible
    ): void {
        $conflicto =
            QuirofanoProgramacion::query()
            ->where(
                'quirofano_sala_id',
                $sala->id
            )
            ->where(
                'estado',
                '<>',
                'CANCELADA'
            )
            ->where(
                'fecha_hora_inicio',
                '<',
                $disponible
            )
            ->where(
                'fecha_hora_disponible',
                '>',
                $inicio
            )
            ->orderBy(
                'fecha_hora_inicio'
            )
            ->first();

        if (!$conflicto) {
            return;
        }

        throw ValidationException::withMessages([
            'hora_inicio' =>
            'La sala seleccionada ya está ocupada en ese horario. '
                . 'La programación existente inicia a las '
                . $conflicto
                ->fecha_hora_inicio
                ->format('H:i')
                . ' y la sala estará disponible aproximadamente a las '
                . $conflicto
                ->fecha_hora_disponible
                ->format('H:i')
                . '.',
        ]);
    }
}
