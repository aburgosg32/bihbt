<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SegurosCodigoPrestacionController extends Controller
{
    public function index(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['nullable', 'integer', 'min:1'],
        ]);

        $cuenta = isset($datos['cuenta']) ? (int) $datos['cuenta'] : null;
        $atenciones = collect();
        $prestaciones = collect();

        if ($cuenta !== null) {
            $atenciones = DB::connection('sigh')
                ->table('Atenciones as a')
                ->leftJoin('Pacientes as p', 'p.IdPaciente', '=', 'a.IdPaciente')
                ->leftJoin(
                    'FuentesFinanciamiento as ff',
                    'ff.IdFuenteFinanciamiento',
                    '=',
                    'a.IdFuenteFinanciamiento'
                )
                ->leftJoin(
                    'AtencionesDatosAdicionales as ada',
                    'ada.IdAtencion',
                    '=',
                    'a.IdAtencion'
                )
                ->select(
                    'a.IdAtencion',
                    'a.IdCuentaAtencion',
                    'a.IdFuenteFinanciamiento',
                    'a.FechaIngreso',
                    'p.NroDocumento',
                    'p.ApellidoPaterno',
                    'p.ApellidoMaterno',
                    'p.PrimerNombre',
                    'p.SegundoNombre',
                    'p.TercerNombre',
                    'ff.Descripcion as Financiamiento',
                    'ada.IdAtencion as IdDatosAdicionales',
                    'ada.FuaCodigoPrestacion'
                )
                ->where('a.IdCuentaAtencion', $cuenta)
                ->orderByDesc('a.IdAtencion')
                ->get()
                ->map(function ($atencion) {
                    $atencion->Paciente = trim(implode(' ', array_filter([
                        $atencion->ApellidoPaterno,
                        $atencion->ApellidoMaterno,
                        $atencion->PrimerNombre,
                        $atencion->SegundoNombre,
                        $atencion->TercerNombre,
                    ], fn($valor) => trim((string) $valor) !== '')));

                    $atencion->TieneCita = DB::connection('sigh')
                        ->table('Citas')
                        ->where('IdAtencion', $atencion->IdAtencion)
                        ->exists();

                    $atencion->PuedeAsignar =
                        (int) $atencion->IdFuenteFinanciamiento === 3
                        && $atencion->IdDatosAdicionales !== null
                        && $atencion->TieneCita;

                    return $atencion;
                });

            if ($atenciones->contains(fn($a) => $a->PuedeAsignar)) {
                $prestaciones = $this->catalogo()
                    ->select(
                        's.ser_CodSer as codigo',
                        's.ser_Descripcion as descripcion'
                    )
                    ->distinct()
                    ->orderBy('s.ser_Descripcion')
                    ->get()
                    ->map(function ($prestacion) {
                        $prestacion->codigo = trim((string) $prestacion->codigo);
                        $prestacion->descripcion = trim((string) $prestacion->descripcion);

                        return $prestacion;
                    })
                    ->unique('codigo')
                    ->values();
            }
        }

        return view('seguros.codigo-prestacion', compact(
            'cuenta',
            'atenciones',
            'prestaciones'
        ));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cuenta' => ['required', 'integer', 'min:1'],
            'id_atencion' => ['required', 'integer', 'min:1'],
            'codigo_prestacion' => ['required', 'string'],
        ]);

        $codigo = trim($datos['codigo_prestacion']);

        // Validamos contra el mismo catálogo que se muestra en pantalla.
        if ($codigo === '' || ! $this->catalogo()->where('s.ser_CodSer', $codigo)->exists()) {
            throw ValidationException::withMessages([
                'codigo_prestacion' => 'Seleccione un código de prestación válido.',
            ]);
        }

        $sigh = DB::connection('sigh');

        $sigh->transaction(function () use ($sigh, $datos, $codigo) {
            $atencion = $sigh->table('Atenciones as a')
                ->select(
                    'a.IdAtencion as IdAtencion',
                    'a.IdCuentaAtencion as IdCuentaAtencion',
                    'a.idFuenteFinanciamiento as IdFuenteFinanciamiento'
                )
                ->where('a.IdAtencion', $datos['id_atencion'])
                ->where('a.IdCuentaAtencion', $datos['cuenta'])
                ->lockForUpdate()
                ->first();

            if (! $atencion) {
                throw ValidationException::withMessages([
                    'cuenta' => 'La atención no pertenece a la cuenta indicada.',
                ]);
            }

            if ((int) $atencion->IdFuenteFinanciamiento !== 3) {
                throw ValidationException::withMessages([
                    'cuenta' => 'Solo se puede asignar el código a cuentas SIS.',
                ]);
            }

            if (! $sigh->table('Citas')
                ->where('IdAtencion', $atencion->IdAtencion)
                ->exists()) {
                throw ValidationException::withMessages([
                    'cuenta' => 'La atención no tiene una cita registrada.',
                ]);
            }

            $adicionales = $sigh->table('AtencionesDatosAdicionales')
                ->where('IdAtencion', $atencion->IdAtencion)
                ->lockForUpdate()
                ->first();

            if (! $adicionales) {
                throw ValidationException::withMessages([
                    'cuenta' => 'La atención no tiene un registro de datos adicionales.',
                ]);
            }

            $sigh->table('AtencionesDatosAdicionales')
                ->where('IdAtencion', $atencion->IdAtencion)
                ->update([
                    'FuaCodigoPrestacion' => $codigo,
                ]);
        }, 3);

        return redirect()
            ->route('seguros.codigo-prestacion.index', [
                'cuenta' => $datos['cuenta'],
            ])
            ->with('success', 'El código de prestación se guardó correctamente.');
    }

    private function catalogo()
    {
        // SIGH_SIS debe estar en el mismo servidor de la conexión sigh.
        return DB::connection('sigh')
            ->table('SIGH_SIS.dbo.a_detallereglasconvenio as dr')
            ->join(
                'SIGH_SIS.dbo.a_serviciosconvenio as sc',
                'sc.src_IdRCC',
                '=',
                'dr.rcc_IdRCC'
            )
            ->join(
                'SIGH_SIS.dbo.a_rc01 as rc',
                'rc.rc01_IdRegla',
                '=',
                'sc.src_IdRegla'
            )
            ->join(
                'SIGH_SIS.dbo.m_servicios as s',
                's.ser_IdServicio',
                '=',
                'rc.rc01_IdServicio'
            )
            ->where('dr.rcc_IdRCC', '001')
            ->where('dr.rcc_IdConvenio', '0')
            ->where('s.ser_Hosp', 'N');
    }
}
