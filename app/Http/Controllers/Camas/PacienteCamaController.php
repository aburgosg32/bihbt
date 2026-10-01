<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\CamaOcupacion;
use App\Models\CamasDepartamento;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PacienteCamaController extends Controller
{
    public function index(Request $request)
    {
        $departamentoId = $request->input('departamento_id');

        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $ocupaciones = CamaOcupacion::query()
            ->with([
                'paciente',
                'cama.servicio.departamento',
            ])
            ->where('estado', 'ACTIVO')
            ->when(
                $departamentoId,
                function ($query) use ($departamentoId) {

                    $query->whereHas(
                        'cama.servicio',
                        function ($q) use ($departamentoId) {

                            $q->where(
                                'departamento_id',
                                $departamentoId
                            );
                        }
                    );
                }
            )
            ->orderBy('cama_id')
            ->get();

        return view(
            'camas.pacientes.index',
            compact(
                'ocupaciones',
                'departamentos'
            )
        );
    }

    public function pdf(Request $request)
    {
        $departamentoId =
            $request->input('departamento_id');


        $ocupaciones = CamaOcupacion::query()
            ->with([
                'paciente',
                'cama.servicio.departamento',
            ])
            ->where(
                'estado',
                'ACTIVO'
            )

            ->when(
                $departamentoId,
                function ($query) use ($departamentoId) {

                    $query->whereHas(
                        'cama.servicio',
                        function ($q) use ($departamentoId) {

                            $q->where(
                                'departamento_id',
                                $departamentoId
                            );
                        }
                    );
                }
            )

            ->orderBy('cama_id')

            ->get();


        $departamento =
            $departamentoId
            ? CamasDepartamento::find(
                $departamentoId
            )
            : null;


        $ocupacionesAgrupadas =
            $ocupaciones->groupBy(
                function ($ocupacion) {

                    return $ocupacion
                        ->cama
                        ?->servicio
                        ?->nombre
                        ?? 'SIN SERVICIO';
                }
            );


        $pdf =
            Pdf::loadView(
                'camas.pacientes.pdf',
                compact(
                    'ocupaciones',
                    'ocupacionesAgrupadas',
                    'departamento'
                )
            );


        $pdf->setPaper(
            'a4',
            'landscape'
        );


        return $pdf->stream(
            'censo-actual-' .
                now()->format(
                    'Y-m-d-His'
                ) .
                '.pdf'
        );
    }
}
