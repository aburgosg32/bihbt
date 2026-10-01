<?php

namespace App\Http\Controllers;

use App\Models\Procedimiento;
use App\Models\ProcedimientoServicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcedimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Procedimiento::with('servicio')
            ->orderBy('nombre');

        if ($request->filled('servicio_id')) {
            $query->where(
                'procedimiento_servicio_id',
                $request->servicio_id
            );
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($consulta) use ($buscar) {
                $consulta
                    ->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        $procedimientos = $query
            ->paginate(15)
            ->withQueryString();

        $servicios = ProcedimientoServicio::orderBy('nombre')
            ->get();

        return view(
            'procedimientos.procedimientos.index',
            compact('procedimientos', 'servicios')
        );
    }

    public function store(Request $request)
    {
        $request->merge([
            'codigo' => mb_strtoupper(trim($request->codigo)),
            'nombre' => mb_strtoupper(trim($request->nombre)),
        ]);

        $request->validate([
            'procedimiento_servicio_id' => [
                'required',
                'integer',
                'exists:procedimiento_servicios,id',
            ],

            'codigo' => [
                'required',
                'string',
                'max:30',
                'unique:procedimientos,codigo',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',

                Rule::unique('procedimientos', 'nombre')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'procedimiento_servicio_id',
                            $request->procedimiento_servicio_id
                        );
                    }),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'duracion_minutos' => [
                'required',
                'integer',
                'min:5',
                'max:480',
            ],

            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'indicaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'procedimiento_servicio_id.required' =>
            'Seleccione el servicio.',

            'procedimiento_servicio_id.exists' =>
            'El servicio seleccionado no existe.',

            'codigo.required' =>
            'Ingrese el código del procedimiento.',

            'codigo.unique' =>
            'El código ya se encuentra registrado.',

            'nombre.required' =>
            'Ingrese el nombre del procedimiento.',

            'nombre.unique' =>
            'El procedimiento ya está registrado en este servicio.',

            'duracion_minutos.required' =>
            'Ingrese la duración del procedimiento.',

            'duracion_minutos.min' =>
            'La duración mínima es de 5 minutos.',

            'duracion_minutos.max' =>
            'La duración máxima permitida es de 480 minutos.',

            'color.required' =>
            'Seleccione un color.',

            'color.regex' =>
            'El color seleccionado no tiene un formato válido.',
        ]);

        $servicio = ProcedimientoServicio::findOrFail(
            $request->procedimiento_servicio_id
        );

        if (!$servicio->activo) {
            return back()
                ->withInput()
                ->withErrors([
                    'procedimiento_servicio_id' =>
                    'No se puede utilizar un servicio inactivo.',
                ]);
        }

        Procedimiento::create([
            'procedimiento_servicio_id' =>
            $request->procedimiento_servicio_id,

            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion_minutos' => $request->duracion_minutos,
            'color' => strtoupper($request->color),
            'indicaciones' => $request->indicaciones,
            'activo' => true,
        ]);

        return redirect()
            ->route('procedimientos.tipos.index')
            ->with(
                'success',
                'Procedimiento registrado correctamente.'
            );
    }

    public function update(
        Request $request,
        Procedimiento $procedimiento
    ) {
        $request->merge([
            'codigo' => mb_strtoupper(trim($request->codigo)),
            'nombre' => mb_strtoupper(trim($request->nombre)),
        ]);

        $request->validate([
            'procedimiento_servicio_id' => [
                'required',
                'integer',
                'exists:procedimiento_servicios,id',
            ],

            'codigo' => [
                'required',
                'string',
                'max:30',

                Rule::unique('procedimientos', 'codigo')
                    ->ignore($procedimiento->id),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',

                Rule::unique('procedimientos', 'nombre')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'procedimiento_servicio_id',
                            $request->procedimiento_servicio_id
                        );
                    })
                    ->ignore($procedimiento->id),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'duracion_minutos' => [
                'required',
                'integer',
                'min:5',
                'max:480',
            ],

            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'indicaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'procedimiento_servicio_id.required' =>
            'Seleccione el servicio.',

            'procedimiento_servicio_id.exists' =>
            'El servicio seleccionado no existe.',

            'codigo.required' =>
            'Ingrese el código del procedimiento.',

            'codigo.unique' =>
            'El código ya se encuentra registrado.',

            'nombre.required' =>
            'Ingrese el nombre del procedimiento.',

            'nombre.unique' =>
            'El procedimiento ya está registrado en este servicio.',

            'duracion_minutos.required' =>
            'Ingrese la duración del procedimiento.',

            'duracion_minutos.min' =>
            'La duración mínima es de 5 minutos.',

            'duracion_minutos.max' =>
            'La duración máxima permitida es de 480 minutos.',

            'color.required' =>
            'Seleccione un color.',

            'color.regex' =>
            'El color seleccionado no tiene un formato válido.',
        ]);

        $servicio = ProcedimientoServicio::findOrFail(
            $request->procedimiento_servicio_id
        );

        if (!$servicio->activo) {
            return back()->withErrors([
                'procedimiento_servicio_id' =>
                'No se puede utilizar un servicio inactivo.',
            ]);
        }

        $procedimiento->update([
            'procedimiento_servicio_id' =>
            $request->procedimiento_servicio_id,

            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion_minutos' => $request->duracion_minutos,
            'color' => strtoupper($request->color),
            'indicaciones' => $request->indicaciones,
        ]);

        return redirect()
            ->route('procedimientos.tipos.index')
            ->with(
                'success',
                'Procedimiento actualizado correctamente.'
            );
    }

    public function cambiarEstado(Procedimiento $procedimiento)
    {
        $procedimiento->update([
            'activo' => !$procedimiento->activo,
        ]);

        $mensaje = $procedimiento->activo
            ? 'Procedimiento activado correctamente.'
            : 'Procedimiento desactivado correctamente.';

        return redirect()
            ->route('procedimientos.tipos.index')
            ->with('success', $mensaje);
    }
}
