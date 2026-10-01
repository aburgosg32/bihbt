<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuirofanoReporteExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected Collection $operaciones;

    public function __construct(Collection $operaciones)
    {
        $this->operaciones = $operaciones;
    }

    public function collection(): Collection
    {
        return $this->operaciones;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Sala',
            'Tipo de sala',
            'Paciente',
            'N.º de cuenta',
            'Historia clínica',
            'DNI',
            'Servicio solicitante',
            'Procedimiento',
            'Médico',
            'Especialidad',
            'Tipo de solicitud',
            'Prioridad',
            'Inicio programado',
            'Fin programado',
            'Inicio preparación',
            'Inicio real',
            'Retraso (min)',
            'Fin real',
            'Duración real (min)',
            'Fin limpieza',
            'Limpieza (min)',
            'Ocupación total (min)',
            'Estado',
        ];
    }

    public function map($programacion): array
    {
        $solicitud = $programacion->solicitud;
        $sala = $programacion->sala;

        $inicioProgramado = $programacion->fecha_hora_inicio
            ? Carbon::parse($programacion->fecha_hora_inicio)
            : null;

        $finProgramado = $programacion->fecha_hora_fin
            ? Carbon::parse($programacion->fecha_hora_fin)
            : null;

        $inicioPreparacion = $programacion->fecha_inicio_preparacion
            ? Carbon::parse($programacion->fecha_inicio_preparacion)
            : null;

        $inicioReal = $programacion->fecha_inicio_real
            ? Carbon::parse($programacion->fecha_inicio_real)
            : null;

        $finReal = $programacion->fecha_fin_real
            ? Carbon::parse($programacion->fecha_fin_real)
            : null;

        $finLimpieza = $programacion->fecha_fin_limpieza
            ? Carbon::parse($programacion->fecha_fin_limpieza)
            : null;

        $retraso = null;

        if ($inicioProgramado && $inicioReal) {
            $retraso = $inicioProgramado->diffInMinutes(
                $inicioReal,
                false
            );
        }

        $duracionReal = null;

        if ($inicioReal && $finReal) {
            $duracionReal = $inicioReal->diffInMinutes($finReal);
        }

        $tiempoLimpieza = null;

        if ($finReal && $finLimpieza) {
            $tiempoLimpieza = $finReal->diffInMinutes($finLimpieza);
        }

        $ocupacionTotal = null;

        if ($inicioPreparacion && $finLimpieza) {
            $ocupacionTotal = $inicioPreparacion
                ->diffInMinutes($finLimpieza);
        }

        return [
            $inicioProgramado?->format('d/m/Y'),
            $sala->nombre ?? '',
            $sala->tipo ?? '',
            $solicitud->paciente ?? '',
            $solicitud->numero_cuenta ?? '',
            $solicitud->historia_clinica ?? '',
            $solicitud->dni ?? '',
            $solicitud->servicio_solicitante ?? '',
            trim(
                ($solicitud->procedimiento_codigo ?? '') . ' ' .
                    ($solicitud->procedimiento_nombre ?? '')
            ),
            $solicitud->medico_nombre ?? '',
            $solicitud->medico_especialidad ?? '',
            $solicitud->tipo_solicitud ?? '',
            $solicitud->prioridad ?? '',
            $inicioProgramado?->format('d/m/Y H:i'),
            $finProgramado?->format('d/m/Y H:i'),
            $inicioPreparacion?->format('d/m/Y H:i'),
            $inicioReal?->format('d/m/Y H:i'),
            $retraso,
            $finReal?->format('d/m/Y H:i'),
            $duracionReal,
            $finLimpieza?->format('d/m/Y H:i'),
            $tiempoLimpieza,
            $ocupacionTotal,
            str_replace('_', ' ', $programacion->estado),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['ARGB' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['ARGB' => 'FF075A94'],
                ],
            ],
        ];
    }
}
