<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\CamasDepartamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $departamentos = CamasDepartamento::query()
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigo', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view(
            'camas.departamentos.index',
            compact('departamentos', 'buscar')
        );
    }

    public function create()
    {
        return view('camas.departamentos.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                'unique:camas_departamentos,nombre',
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:30',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $datos['activo'] = $request->boolean('activo');

        CamasDepartamento::create($datos);

        return redirect()
            ->route('camas.departamentos.index')
            ->with(
                'success',
                'Departamento registrado correctamente.'
            );
    }

    public function edit(CamasDepartamento $departamento)
    {
        return view(
            'camas.departamentos.edit',
            compact('departamento')
        );
    }

    public function update(
        Request $request,
        CamasDepartamento $departamento
    ) {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                'unique:camas_departamentos,nombre,' . $departamento->id,
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:30',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $datos['activo'] = $request->boolean('activo');

        $departamento->update($datos);

        return redirect()
            ->route('camas.departamentos.index')
            ->with(
                'success',
                'Departamento actualizado correctamente.'
            );
    }

    public function destroy(CamasDepartamento $departamento)
    {
        if ($departamento->servicios()->exists()) {
            return redirect()
                ->route('camas.departamentos.index')
                ->with(
                    'error',
                    'No se puede eliminar porque el departamento tiene servicios registrados.'
                );
        }

        $departamento->delete();

        return redirect()
            ->route('camas.departamentos.index')
            ->with(
                'success',
                'Departamento eliminado correctamente.'
            );
    }
}
