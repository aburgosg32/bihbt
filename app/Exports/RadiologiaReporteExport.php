<?php

namespace App\Exports;

use App\Models\RadiologiaCita;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RadiologiaReporteExport implements FromView
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function view(): View
    {
        $query = RadiologiaCita::query()
            ->whereBetween('fecha_cita', [
                $this->filtros['fecha_inicio'],
                $this->filtros['fecha_fin']
            ]);

        if (!empty($this->filtros['modalidad'])) {
            $query->where('modalidad', $this->filtros['modalidad']);
        }

        if (!empty($this->filtros['estado'])) {
            $query->where('estado', $this->filtros['estado']);
        }

        if (!empty($this->filtros['buscar'])) {
            $buscar = $this->filtros['buscar'];

            $query->where(function ($q) use ($buscar) {
                $q->where('id_cuenta_atencion', 'like', "%{$buscar}%")
                    ->orWhere('nro_historia_clinica', 'like', "%{$buscar}%")
                    ->orWhere('paciente', 'like', "%{$buscar}%")
                    ->orWhere('codigo_estudio', 'like', "%{$buscar}%")
                    ->orWhere('estudio', 'like', "%{$buscar}%")
                    ->orWhere('fuente_financiamiento', 'like', "%{$buscar}%")
                    ->orWhere('tipo_servicio', 'like', "%{$buscar}%")
                    ->orWhere('nombre_servicio', 'like', "%{$buscar}%");
            });
        }

        $citas = $query
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get();

        return view('radiologia.exports.excel', compact('citas'));
    }
}
