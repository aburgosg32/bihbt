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
            ->paginate(20)
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

        $horaInicio = '07:00';
        $horaFin = '13:00';

        $intervalo = match ($request->modalidad) {
            'RAYOS X' => 10,
            'TOMOGRAFIA' => 15,
            'ECOGRAFIA' => 20,
            default => 30,
        };

        $horasPermitidas = [];

        $inicio = Carbon::createFromFormat('H:i', $horaInicio);
        $fin = Carbon::createFromFormat('H:i', $horaFin);

        while ($inicio <= $fin) {
            $horasPermitidas[] = $inicio->format('H:i');
            $inicio->addMinutes($intervalo);
        }

        if (!in_array($request->hora_cita, $horasPermitidas)) {
            return response()->json([
                'success' => false,
                'message' => 'La hora seleccionada no está permitida para la modalidad.'
            ], 422);
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

        return response()->json([
            'success' => true,
            'ticket_url' => route('radiologia.ticket', $cita->id)
        ]);
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
    public function horariosDisponibles(Request $request)
    {
        $request->validate([
            'modalidad' => 'required',
            'fecha' => 'required|date',
        ]);

        $modalidad = $request->modalidad;
        $fecha = $request->fecha;

        $horaInicio = '07:00';
        $horaFin = '13:00';

        $intervalo = match ($modalidad) {
            'RAYOS X' => 10,
            'TOMOGRAFIA' => 15,
            'ECOGRAFIA' => 20,
            default => 30,
        };

        $horariosBase = [];

        $inicio = Carbon::createFromFormat('H:i', $horaInicio);
        $fin = Carbon::createFromFormat('H:i', $horaFin);

        while ($inicio <= $fin) {
            $horariosBase[] = $inicio->format('H:i');
            $inicio->addMinutes($intervalo);
        }

        $ocupados = RadiologiaCita::where('modalidad', $modalidad)
            ->where('fecha_cita', $fecha)
            ->whereIn('estado', ['REGISTRADO', 'ATENDIDO'])
            ->pluck('hora_cita')
            ->map(function ($hora) {
                return substr($hora, 0, 5);
            })
            ->toArray();

        $horarios = collect($horariosBase)->map(function ($hora) use ($ocupados) {
            return [
                'hora' => $hora,
                'ocupado' => in_array($hora, $ocupados),
            ];
        });

        return response()->json([
            'success' => true,
            'modalidad' => $modalidad,
            'intervalo' => $intervalo,
            'horarios' => $horarios,
        ]);
    }
    public function rayosx(Request $request)
    {
        $buscar = $request->buscar;

        $citas = RadiologiaCita::where('modalidad', 'RAYOS X')
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('id_cuenta_atencion', 'like', "%{$buscar}%")
                        ->orWhere('nro_historia_clinica', 'like', "%{$buscar}%")
                        ->orWhere('paciente', 'like', "%{$buscar}%")
                        ->orWhere('estudio', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('radiologia.rayosx', compact('citas', 'buscar'));
    }
    public function atenderRayosx($id)
    {
        $cita = RadiologiaCita::where('modalidad', 'RAYOS X')
            ->where('id', $id)
            ->firstOrFail();

        if ($cita->estado === 'ATENDIDO') {
            return redirect()
                ->route('radiologia.rayosx')
                ->with('error', 'El paciente ya fue atendido.');
        }

        $cita->update([
            'estado' => 'ATENDIDO',
        ]);

        return redirect()
            ->route('radiologia.rayosx')
            ->with('success', 'Atención registrada correctamente.');
    }
    public function tomografias(Request $request)
    {
        $buscar = $request->buscar;

        $citas = RadiologiaCita::where('modalidad', 'TOMOGRAFIA')
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('id_cuenta_atencion', 'like', "%{$buscar}%")
                        ->orWhere('nro_historia_clinica', 'like', "%{$buscar}%")
                        ->orWhere('paciente', 'like', "%{$buscar}%")
                        ->orWhere('estudio', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('radiologia.tomografias', compact('citas', 'buscar'));
    }

    public function atenderTomografia($id)
    {
        $cita = RadiologiaCita::where('modalidad', 'TOMOGRAFIA')
            ->where('id', $id)
            ->firstOrFail();

        if ($cita->estado === 'ATENDIDO') {
            return redirect()
                ->route('radiologia.tomografias')
                ->with('error', 'El paciente ya fue atendido.');
        }

        $cita->update([
            'estado' => 'ATENDIDO',
        ]);

        return redirect()
            ->route('radiologia.tomografias')
            ->with('success', 'Atención registrada correctamente.');
    }
    public function ecografias(Request $request)
    {
        $buscar = $request->buscar;

        $citas = RadiologiaCita::where('modalidad', 'ECOGRAFIA')
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('id_cuenta_atencion', 'like', "%{$buscar}%")
                        ->orWhere('nro_historia_clinica', 'like', "%{$buscar}%")
                        ->orWhere('paciente', 'like', "%{$buscar}%")
                        ->orWhere('estudio', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('radiologia.ecografias', compact('citas', 'buscar'));
    }

    public function atenderEcografia($id)
    {
        $cita = RadiologiaCita::where('modalidad', 'ECOGRAFIA')
            ->where('id', $id)
            ->firstOrFail();

        if ($cita->estado === 'ATENDIDO') {
            return redirect()
                ->route('radiologia.ecografias')
                ->with('error', 'El paciente ya fue atendido.');
        }

        $cita->update([
            'estado' => 'ATENDIDO',
        ]);

        return redirect()
            ->route('radiologia.ecografias')
            ->with('success', 'Atención registrada correctamente.');
    }
    public function dashboard(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $base = RadiologiaCita::whereBetween('fecha_cita', [$fechaInicio, $fechaFin]);

        $totalCitas = (clone $base)->count();

        $registrados = (clone $base)
            ->where('estado', 'REGISTRADO')
            ->count();

        $atendidos = (clone $base)
            ->where('estado', 'ATENDIDO')
            ->count();

        $rayosx = (clone $base)
            ->where('modalidad', 'RAYOS X')
            ->count();

        $tomografias = (clone $base)
            ->where('modalidad', 'TOMOGRAFIA')
            ->count();

        $ecografias = (clone $base)
            ->where('modalidad', 'ECOGRAFIA')
            ->count();

        $porModalidad = (clone $base)
            ->select('modalidad', DB::raw('COUNT(*) as total'))
            ->groupBy('modalidad')
            ->get();

        $porEstado = (clone $base)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        $porDia = (clone $base)
            ->select('fecha_cita', DB::raw('COUNT(*) as total'))
            ->groupBy('fecha_cita')
            ->orderBy('fecha_cita')
            ->get();

        $atendidosPorModalidad = (clone $base)
            ->select('modalidad', DB::raw('COUNT(*) as total'))
            ->where('estado', 'ATENDIDO')
            ->groupBy('modalidad')
            ->get();

        $registradosPorModalidad = (clone $base)
            ->select('modalidad', DB::raw('COUNT(*) as total'))
            ->where('estado', 'REGISTRADO')
            ->groupBy('modalidad')
            ->get();

        $topEstudios = (clone $base)
            ->select('estudio', DB::raw('COUNT(*) as total'))
            ->groupBy('estudio')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $citasHoy = RadiologiaCita::whereDate('fecha_cita', now()->format('Y-m-d'))
            ->orderBy('hora_cita')
            ->limit(10)
            ->get();

        return view('radiologia.dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'totalCitas',
            'registrados',
            'atendidos',
            'rayosx',
            'tomografias',
            'ecografias',
            'porModalidad',
            'porEstado',
            'porDia',
            'atendidosPorModalidad',
            'registradosPorModalidad',
            'topEstudios',
            'citasHoy'
        ));
    }
}
