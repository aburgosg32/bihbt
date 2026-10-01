<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\CamasDepartamento;
use App\Models\CamasHabitacion;
use App\Models\CamasServicio;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $departamentoId = $request->input('departamento_id');
        $servicioId = $request->input('servicio_id');

        $habitaciones = CamasHabitacion::query()
            ->with([
                'servicio.departamento',
            ])
            ->withCount('camas')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('numero', 'like', "%{$buscar}%")
                        ->orWhere('piso', 'like', "%{$buscar}%");
                });
            })
            ->when($servicioId, function ($query, $servicioId) {
                $query->where('servicio_id', $servicioId);
            })
            ->when(
                $departamentoId && !$servicioId,
                function ($query) use ($departamentoId) {
                    $query->whereHas(
                        'servicio',
                        function ($q) use ($departamentoId) {
                            $q->where(
                                'departamento_id',
                                $departamentoId
                            );
                        }
                    );
                }
            )
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->when(
                $departamentoId,
                fn($q) =>
                $q->where(
                    'departamento_id',
                    $departamentoId
                )
            )
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.habitaciones.index',
            compact(
                'habitaciones',
                'departamentos',
                'servicios',
                'buscar',
                'departamentoId',
                'servicioId'
            )
        );
    }

    public function create()
    {
        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.habitaciones.create',
            compact(
                'departamentos',
                'servicios'
            )
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'servicio_id' => [
                'required',
                'exists:camas_servicios,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'numero' => [
                'nullable',
                'string',
                'max:30',
            ],
            'piso' => [
                'nullable',
                'string',
                'max:50',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $existe = CamasHabitacion::query()
            ->where(
                'servicio_id',
                $datos['servicio_id']
            )
            ->where(
                'nombre',
                $datos['nombre']
            )
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' =>
                    'Esta habitación ya existe en el servicio seleccionado.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        CamasHabitacion::create($datos);

        return redirect()
            ->route('camas.habitaciones.index')
            ->with(
                'success',
                'Habitación registrada correctamente.'
            );
    }

    public function edit(CamasHabitacion $habitacione)
    {
        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.habitaciones.edit',
            [
                'habitacion' => $habitacione,
                'departamentos' => $departamentos,
                'servicios' => $servicios,
            ]
        );
    }

    public function update(
        Request $request,
        CamasHabitacion $habitacione
    ) {
        $datos = $request->validate([
            'servicio_id' => [
                'required',
                'exists:camas_servicios,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'numero' => [
                'nullable',
                'string',
                'max:30',
            ],
            'piso' => [
                'nullable',
                'string',
                'max:50',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $existe = CamasHabitacion::query()
            ->where(
                'servicio_id',
                $datos['servicio_id']
            )
            ->where(
                'nombre',
                $datos['nombre']
            )
            ->where(
                'id',
                '<>',
                $habitacione->id
            )
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' =>
                    'Esta habitación ya existe en el servicio seleccionado.',
                ]);
        }

        $datos['activo'] =
            $request->boolean('activo');

        $habitacione->update($datos);

        return redirect()
            ->route('camas.habitaciones.index')
            ->with(
                'success',
                'Habitación actualizada correctamente.'
            );
    }

    public function destroy(
        CamasHabitacion $habitacione
    ) {
        if ($habitacione->camas()->exists()) {
            return redirect()
                ->route('camas.habitaciones.index')
                ->with(
                    'error',
                    'No se puede eliminar porque la habitación tiene camas registradas.'
                );
        }

        $habitacione->delete();

        return redirect()
            ->route('camas.habitaciones.index')
            ->with(
                'success',
                'Habitación eliminada correctamente.'
            );
    }

    public function serviciosPorDepartamento(
        CamasDepartamento $departamento
    ) {
        $servicios = CamasServicio::query()
            ->where(
                'departamento_id',
                $departamento->id
            )
            ->where('activo', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        return response()->json($servicios);
    }
}
