<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\CamasDepartamento;
use App\Models\CamasServicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $departamentoId = $request->input('departamento_id');

        $servicios = CamasServicio::query()
            ->with('departamento')
            ->withCount(['habitaciones', 'camas'])
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigo', 'like', "%{$buscar}%")
                        ->orWhere('piso', 'like', "%{$buscar}%");
                });
            })
            ->when($departamentoId, function ($query, $departamentoId) {
                $query->where('departamento_id', $departamentoId);
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.servicios.index',
            compact(
                'servicios',
                'departamentos',
                'buscar',
                'departamentoId'
            )
        );
    }

    public function create()
    {
        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.servicios.create',
            compact('departamentos')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'departamento_id' => [
                'required',
                'exists:camas_departamentos,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:30',
            ],
            'piso' => [
                'nullable',
                'string',
                'max:50',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $existe = CamasServicio::query()
            ->where('departamento_id', $datos['departamento_id'])
            ->where('nombre', $datos['nombre'])
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Este servicio ya existe dentro del departamento seleccionado.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        CamasServicio::create($datos);

        return redirect()
            ->route('camas.servicios.index')
            ->with(
                'success',
                'Servicio registrado correctamente.'
            );
    }

    public function edit(CamasServicio $servicio)
    {
        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.servicios.edit',
            compact('servicio', 'departamentos')
        );
    }

    public function update(
        Request $request,
        CamasServicio $servicio
    ) {
        $datos = $request->validate([
            'departamento_id' => [
                'required',
                'exists:camas_departamentos,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:30',
            ],
            'piso' => [
                'nullable',
                'string',
                'max:50',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $existe = CamasServicio::query()
            ->where('departamento_id', $datos['departamento_id'])
            ->where('nombre', $datos['nombre'])
            ->where('id', '<>', $servicio->id)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Este servicio ya existe dentro del departamento seleccionado.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        $servicio->update($datos);

        return redirect()
            ->route('camas.servicios.index')
            ->with(
                'success',
                'Servicio actualizado correctamente.'
            );
    }

    public function destroy(CamasServicio $servicio)
    {
        if (
            $servicio->habitaciones()->exists() ||
            $servicio->camas()->exists()
        ) {
            return redirect()
                ->route('camas.servicios.index')
                ->with(
                    'error',
                    'No se puede eliminar porque el servicio tiene habitaciones o camas registradas.'
                );
        }

        $servicio->delete();

        return redirect()
            ->route('camas.servicios.index')
            ->with(
                'success',
                'Servicio eliminado correctamente.'
            );
    }
}
