<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SegurosImpresionFuaController extends Controller
{
    public function index(Request $request): View
    {
        $datos = $request->validate([
            'especialidad' => ['nullable', 'integer', 'min:1'],
        ]);

        $idEspecialidad = isset($datos['especialidad'])
            ? (int) $datos['especialidad']
            : null;

        $sigh = DB::connection('sigh');

        // Solo especialidades que tienen servicios de Consulta Externa.
        $especialidades = $sigh->table('Especialidades as e')
            ->join(
                'Servicios as s',
                's.IdEspecialidad',
                '=',
                'e.IdEspecialidad'
            )
            ->where('s.IdTipoServicio', 1)
            ->select('e.IdEspecialidad', 'e.Nombre')
            ->distinct()
            ->orderBy('e.Nombre')
            ->get();

        $servicios = $sigh->table('Servicios as s')
            ->leftJoin(
                'Especialidades as e',
                'e.IdEspecialidad',
                '=',
                's.IdEspecialidad'
            )
            ->where('s.IdTipoServicio', 1)
            ->when(
                $idEspecialidad !== null,
                fn($consulta) => $consulta->where(
                    's.IdEspecialidad',
                    $idEspecialidad
                )
            )
            ->select(
                's.IdServicio',
                's.Nombre',
                's.IdEstado',
                's.UsaGalenHos',
                'e.Nombre as Especialidad'
            )
            ->orderBy('s.Nombre')
            ->get();

        $enAdmision = $servicios
            ->filter(fn($servicio) => (int) $servicio->UsaGalenHos === 1)
            ->count();

        $enMedico = $servicios
            ->filter(fn($servicio) => (int) $servicio->UsaGalenHos === 0)
            ->count();

        return view('seguros.impresion-fua', compact(
            'servicios',
            'especialidades',
            'idEspecialidad',
            'enAdmision',
            'enMedico'
        ));
    }

    public function cambiar(
        Request $request,
        int $idServicio
    ): RedirectResponse {
        $datos = $request->validate([
            'usa_galen_hos' => ['required', 'integer', 'in:0,1'],
        ]);

        $sigh = DB::connection('sigh');

        $servicio = $sigh->table('Servicios')
            ->where('IdServicio', $idServicio)
            ->where('IdTipoServicio', 1)
            ->first(['IdServicio', 'Nombre', 'UsaGalenHos']);

        if (! $servicio) {
            return back()->with(
                'error',
                'No se encontró el servicio de Consulta Externa.'
            );
        }

        $nuevoValor = (int) $datos['usa_galen_hos'];

        if ((int) $servicio->UsaGalenHos === $nuevoValor) {
            return back()->with(
                'info',
                "El servicio {$servicio->Nombre} ya tiene esa configuración."
            );
        }

        $sigh->table('Servicios')
            ->where('IdServicio', $idServicio)
            ->where('IdTipoServicio', 1)
            ->update([
                'UsaGalenHos' => $nuevoValor,
            ]);

        $responsable = $nuevoValor === 1 ? 'Admisión' : 'Médico';

        return back()->with(
            'success',
            "La impresión FUA de {$servicio->Nombre} cambió a {$responsable}."
        );
    }

    public function restablecerTodos(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'especialidad' => ['nullable', 'integer', 'min:1'],
        ]);

        $idEspecialidad = isset($datos['especialidad'])
            ? (int) $datos['especialidad']
            : null;

        $consulta = DB::connection('sigh')
            ->table('Servicios')
            ->where('IdTipoServicio', 1)
            ->where('UsaGalenHos', 1);

        if ($idEspecialidad !== null) {
            $consulta->where('IdEspecialidad', $idEspecialidad);
        }

        $modificados = $consulta->update([
            'UsaGalenHos' => 0,
        ]);

        $alcance = $idEspecialidad !== null
            ? 'de la especialidad seleccionada'
            : 'de Consulta Externa';

        return back()->with(
            'success',
            "Se restablecieron {$modificados} servicios {$alcance} a impresión por Médico."
        );
    }
}
