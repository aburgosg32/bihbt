<?php

namespace App\Http\Controllers;

use App\Models\RadiologiaCita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RadiologiaController extends Controller
{
    public function citas(Request $request)
    {
        $buscar = $request->buscar;

        $citas = RadiologiaCita::query()
            ->when($buscar, function ($q) use ($buscar) {
                $q->where('id_cuenta_atencion', 'like', "%{$buscar}%")
                    ->orWhere('paciente', 'like', "%{$buscar}%")
                    ->orWhere('nro_historia_clinica', 'like', "%{$buscar}%")
                    ->orWhere('modalidad', 'like', "%{$buscar}%")
                    ->orWhere('estudio', 'like', "%{$buscar}%");
            })
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('radiologia.citas', compact('citas', 'buscar'));
    }

    public function buscarCuenta($cuenta)
    {
        try {
            $paciente = DB::connection('sigh')
                ->table('vw_radiologia_cuenta_atencion')
                ->where('IdCuentaAtencion', $cuenta)
                ->orderBy('FechaIngreso', 'desc')
                ->first();

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información para la cuenta ingresada.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $paciente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cuenta_atencion' => 'required',
            'paciente' => 'required',
            'modalidad' => 'required',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
        ]);

        $fechaCita = Carbon::parse($request->fecha_cita);

        if ($fechaCita->isSunday()) {
            return back()->with('error', 'No se pueden registrar citas los domingos.');
        }

        $horasPermitidas = [
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '12:00',
            '12:30',
            '13:00',
        ];

        if (!in_array($request->hora_cita, $horasPermitidas)) {
            return back()->with('error', 'La hora seleccionada no está permitida.');
        }

        $existe = RadiologiaCita::where('modalidad', $request->modalidad)
            ->where('fecha_cita', $request->fecha_cita)
            ->where('hora_cita', $request->hora_cita)
            ->whereIn('estado', ['REGISTRADO', 'ATENDIDO'])
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ese cupo ya está ocupado.');
        }

        $cita = RadiologiaCita::create($request->all());

        return redirect()
            ->route('radiologia.ticket', $cita->id);
    }

    public function ticket($id)
    {
        $cita = RadiologiaCita::findOrFail($id);

        $pdf = Pdf::loadView('radiologia.ticket', compact('cita'))
            ->setPaper([0, 0, 226.77, 500], 'portrait');

        return $pdf->stream('ticket-radiologia.pdf');
    }

    public function buscarProcedimiento($codigo)
    {
        $procedimiento = DB::connection('sigh')
            ->table('FactCatalogoServicios')
            ->select('Codigo', 'Nombre')
            ->where('Codigo', $codigo)
            ->first();

        if (!$procedimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Código no encontrado'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $procedimiento
        ]);
    }
}
