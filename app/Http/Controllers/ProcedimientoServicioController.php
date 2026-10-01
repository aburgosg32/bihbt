<?php

namespace App\Http\Controllers;

use App\Models\ProcedimientoServicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcedimientoServicioController extends Controller
{
    public function index()
    {
        $servicios = ProcedimientoServicio::orderBy('nombre')
            ->paginate(15);

        return view(
            'procedimientos.servicios.index',
            compact('servicios')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                'unique:procedimiento_servicios,nombre',
            ],
        ], [
            'nombre.required' => 'Ingrese el nombre del servicio.',
            'nombre.unique' => 'El servicio ya se encuentra registrado.',
        ]);

        ProcedimientoServicio::create([
            'nombre' => mb_strtoupper(trim($request->nombre)),
            'activo' => true,
        ]);

        return redirect()
            ->route('procedimientos.servicios.index')
            ->with('success', 'Servicio registrado correctamente.');
    }

    public function update(
        Request $request,
        ProcedimientoServicio $servicio
    ) {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'procedimiento_servicios',
                    'nombre'
                )->ignore($servicio->id),
            ],
        ], [
            'nombre.required' => 'Ingrese el nombre del servicio.',
            'nombre.unique' => 'El servicio ya se encuentra registrado.',
        ]);

        $servicio->update([
            'nombre' => mb_strtoupper(trim($request->nombre)),
        ]);

        return redirect()
            ->route('procedimientos.servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function cambiarEstado(
        ProcedimientoServicio $servicio
    ) {
        $servicio->update([
            'activo' => !$servicio->activo,
        ]);

        $mensaje = $servicio->activo
            ? 'Servicio activado correctamente.'
            : 'Servicio desactivado correctamente.';

        return redirect()
            ->route('procedimientos.servicios.index')
            ->with('success', $mensaje);
    }
}
