<?php

namespace App\Http\Controllers;

use App\Models\QuirofanoProgramacion;
use App\Models\QuirofanoSala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuirofanoEstadoSalaController extends Controller
{
    public function index()
    {
        $salas = QuirofanoSala::query()
            ->orderBy('codigo')
            ->get()
            ->map(function ($sala) {
                $sala->programacion_activa =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->whereIn('estado', [
                        'EN_PREPARACION',
                        'EN_CURSO',
                    ])
                    ->orderByDesc(
                        'fecha_hora_inicio'
                    )
                    ->first();

                $sala->proxima_programacion =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->where(
                        'estado',
                        'PROGRAMADA'
                    )
                    ->where(
                        'fecha_hora_inicio',
                        '>=',
                        now()
                            ->copy()
                            ->startOfDay()
                    )
                    ->orderBy(
                        'fecha_hora_inicio'
                    )
                    ->first();

                $sala->ultima_finalizada =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->where(
                        'estado',
                        'FINALIZADA'
                    )
                    ->whereNull(
                        'fecha_fin_limpieza'
                    )
                    ->orderByDesc(
                        'fecha_fin_real'
                    )
                    ->first();

                return $sala;
            });

        return view(
            'quirofano.estado-salas.index',
            compact('salas')
        );
    }

    public function cambiarEstado(
        Request $request,
        QuirofanoProgramacion $programacion
    ) {
        $datos = $request->validate([
            'accion' => [
                'required',
                'in:PREPARAR,INICIAR,FINALIZAR,FINALIZAR_LIMPIEZA',
            ],
        ]);

        DB::transaction(function () use (
            $programacion,
            $datos
        ) {
            $programacionBloqueada =
                QuirofanoProgramacion::query()
                ->lockForUpdate()
                ->findOrFail(
                    $programacion->id
                );

            $sala = QuirofanoSala::query()
                ->lockForUpdate()
                ->findOrFail(
                    $programacionBloqueada
                        ->quirofano_sala_id
                );

            if (!$sala->operativa) {
                throw ValidationException::withMessages([
                    'sala' =>
                    'La sala se encuentra inoperativa.',
                ]);
            }

            switch ($datos['accion']) {
                case 'PREPARAR':
                    $this->iniciarPreparacion(
                        $programacionBloqueada,
                        $sala
                    );
                    break;

                case 'INICIAR':
                    $this->iniciarOperacion(
                        $programacionBloqueada,
                        $sala
                    );
                    break;

                case 'FINALIZAR':
                    $this->finalizarOperacion(
                        $programacionBloqueada,
                        $sala
                    );
                    break;

                case 'FINALIZAR_LIMPIEZA':
                    $this->finalizarLimpieza(
                        $programacionBloqueada,
                        $sala
                    );
                    break;
            }
        });

        return redirect()
            ->route('quirofano.estado-salas.index')
            ->with(
                'success',
                'El estado de la sala fue actualizado.'
            );
    }

    private function iniciarPreparacion(
        QuirofanoProgramacion $programacion,
        QuirofanoSala $sala
    ): void {
        if ($programacion->estado !== 'PROGRAMADA') {
            throw ValidationException::withMessages([
                'estado' =>
                'Solo una operación programada puede pasar a preparación.',
            ]);
        }

        if ($sala->estado_actual !== 'DISPONIBLE') {
            throw ValidationException::withMessages([
                'sala' =>
                'La sala no se encuentra disponible.',
            ]);
        }

        $otraActiva = QuirofanoProgramacion::query()
            ->where(
                'quirofano_sala_id',
                $sala->id
            )
            ->whereIn('estado', [
                'EN_PREPARACION',
                'EN_CURSO',
            ])
            ->where('id', '<>', $programacion->id)
            ->exists();

        if ($otraActiva) {
            throw ValidationException::withMessages([
                'sala' =>
                'La sala ya tiene otra operación activa.',
            ]);
        }

        $programacion->update([
            'estado' => 'EN_PREPARACION',
            'fecha_inicio_preparacion' => now(),
        ]);

        $sala->update([
            'estado_actual' => 'EN_PREPARACION',
        ]);
    }

    private function iniciarOperacion(
        QuirofanoProgramacion $programacion,
        QuirofanoSala $sala
    ): void {
        if (
            $programacion->estado !==
            'EN_PREPARACION'
        ) {
            throw ValidationException::withMessages([
                'estado' =>
                'La operación debe estar en preparación.',
            ]);
        }

        $programacion->update([
            'estado' => 'EN_CURSO',
            'fecha_inicio_real' => now(),
        ]);

        $sala->update([
            'estado_actual' => 'EN_USO',
        ]);
    }

    private function finalizarOperacion(
        QuirofanoProgramacion $programacion,
        QuirofanoSala $sala
    ): void {
        if ($programacion->estado !== 'EN_CURSO') {
            throw ValidationException::withMessages([
                'estado' =>
                'Solo puede finalizar una operación en curso.',
            ]);
        }

        $programacion->update([
            'estado' => 'FINALIZADA',
            'fecha_fin_real' => now(),
        ]);

        $programacion->solicitud()->update([
            'estado' => 'FINALIZADA',
        ]);

        $sala->update([
            'estado_actual' => 'EN_LIMPIEZA',
        ]);
    }

    private function finalizarLimpieza(
        QuirofanoProgramacion $programacion,
        QuirofanoSala $sala
    ): void {
        if ($programacion->estado !== 'FINALIZADA') {
            throw ValidationException::withMessages([
                'estado' =>
                'La operación todavía no ha finalizado.',
            ]);
        }

        if ($programacion->fecha_fin_limpieza) {
            throw ValidationException::withMessages([
                'estado' =>
                'La limpieza ya fue finalizada.',
            ]);
        }

        if ($sala->estado_actual !== 'EN_LIMPIEZA') {
            throw ValidationException::withMessages([
                'sala' =>
                'La sala no se encuentra en limpieza.',
            ]);
        }

        $programacion->update([
            'fecha_fin_limpieza' => now(),
        ]);

        $sala->update([
            'estado_actual' => 'DISPONIBLE',
        ]);
    }
    public function panelTv()
    {
        /*
     * Se muestran únicamente las seis salas reales.
     * Si una queda inoperativa continuará apareciendo
     * en color gris.
     */
        $salas = QuirofanoSala::query()
            ->whereIn('codigo', [
                'SALA-01',
                'SALA-02',
                'SALA-03',
                'SALA-04',
                'SALA-05',
                'SALA-06',
            ])
            ->orderBy('codigo')
            ->get()
            ->map(function ($sala) {
                $sala->programacion_activa =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->whereIn('estado', [
                        'EN_PREPARACION',
                        'EN_CURSO',
                    ])
                    ->orderByDesc(
                        'fecha_hora_inicio'
                    )
                    ->first();

                $sala->programacion_limpieza =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->where(
                        'estado',
                        'FINALIZADA'
                    )
                    ->whereNull(
                        'fecha_fin_limpieza'
                    )
                    ->orderByDesc(
                        'fecha_fin_real'
                    )
                    ->first();

                $sala->proxima_programacion =
                    QuirofanoProgramacion::query()
                    ->with('solicitud')
                    ->where(
                        'quirofano_sala_id',
                        $sala->id
                    )
                    ->where(
                        'estado',
                        'PROGRAMADA'
                    )
                    ->where(
                        'fecha_hora_inicio',
                        '>=',
                        now()
                            ->copy()
                            ->startOfDay()
                    )
                    ->orderBy(
                        'fecha_hora_inicio'
                    )
                    ->first();

                return $sala;
            });

        return view(
            'quirofano.estado-salas.panel-tv',
            compact('salas')
        );
    }
}
