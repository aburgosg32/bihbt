<?php

namespace App\Http\Controllers;

use App\Models\ProcedimientoCupo;
use App\Models\ProcedimientoProgramacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedimientoCupoController extends Controller
{
    public function index(
        ProcedimientoProgramacion $programacion
    ) {
        $programacion->load([
            'procedimiento.servicio',
            'medico',
            'cupos',
        ]);

        return view(
            'procedimientos.procedimientos.cupos.index',
            compact('programacion')
        );
    }

    public function bloquear(
        Request $request,
        ProcedimientoCupo $cupo
    ) {
        $datos = $request->validate(
            [
                'motivo_bloqueo' => [
                    'required',
                    'string',
                    'max:500',
                ],
            ],
            [
                'motivo_bloqueo.required' =>
                'Ingrese el motivo del bloqueo.',

                'motivo_bloqueo.max' =>
                'El motivo no debe superar los 500 caracteres.',
            ]
        );

        DB::transaction(function () use ($cupo, $datos) {
            $cupoBloqueado = ProcedimientoCupo::query()
                ->lockForUpdate()
                ->findOrFail($cupo->id);

            if (
                $cupoBloqueado->estado !==
                ProcedimientoCupo::ESTADO_DISPONIBLE
            ) {
                abort(
                    422,
                    'Solo se pueden bloquear cupos disponibles.'
                );
            }

            $cupoBloqueado->update([
                'estado' =>
                ProcedimientoCupo::ESTADO_BLOQUEADO,

                'motivo_bloqueo' =>
                $datos['motivo_bloqueo'],
            ]);
        });

        return back()->with(
            'success',
            'El cupo fue bloqueado correctamente.'
        );
    }

    public function habilitar(
        ProcedimientoCupo $cupo
    ) {
        DB::transaction(function () use ($cupo) {
            $cupoBloqueado = ProcedimientoCupo::query()
                ->lockForUpdate()
                ->findOrFail($cupo->id);

            if (
                $cupoBloqueado->estado !==
                ProcedimientoCupo::ESTADO_BLOQUEADO
            ) {
                abort(
                    422,
                    'Solo se pueden habilitar cupos bloqueados.'
                );
            }

            $cupoBloqueado->update([
                'estado' =>
                ProcedimientoCupo::ESTADO_DISPONIBLE,

                'motivo_bloqueo' => null,
            ]);
        });

        return back()->with(
            'success',
            'El cupo fue habilitado correctamente.'
        );
    }
}
