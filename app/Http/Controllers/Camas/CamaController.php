<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\Cama;
use App\Models\CamasDepartamento;
use App\Models\CamasHabitacion;
use App\Models\CamasServicio;
use Illuminate\Http\Request;

class CamaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $departamentoId = $request->input('departamento_id');
        $servicioId = $request->input('servicio_id');
        $habitacionId = $request->input('habitacion_id');
        $estadoOperativo = $request->input('estado_operativo');

        $camas = Cama::query()
            ->with([
                'servicio.departamento',
                'habitacion',
            ])
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('numero', 'like', "%{$buscar}%")
                        ->orWhere('tipo_cama', 'like', "%{$buscar}%");
                });
            })
            ->when($departamentoId, function ($query, $departamentoId) {
                $query->whereHas('servicio', function ($q) use ($departamentoId) {
                    $q->where('departamento_id', $departamentoId);
                });
            })
            ->when($servicioId, function ($query, $servicioId) {
                $query->where('servicio_id', $servicioId);
            })
            ->when($habitacionId, function ($query, $habitacionId) {
                $query->where('habitacion_id', $habitacionId);
            })
            ->when($estadoOperativo, function ($query, $estadoOperativo) {
                $query->where('estado_operativo', $estadoOperativo);
            })
            ->orderBy('numero')
            ->paginate(20)
            ->withQueryString();

        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->when(
                $departamentoId,
                fn($q) => $q->where('departamento_id', $departamentoId)
            )
            ->orderBy('nombre')
            ->get();

        $habitaciones = CamasHabitacion::query()
            ->where('activo', true)
            ->when(
                $servicioId,
                fn($q) => $q->where('servicio_id', $servicioId)
            )
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.camas.index',
            compact(
                'camas',
                'departamentos',
                'servicios',
                'habitaciones',
                'buscar',
                'departamentoId',
                'servicioId',
                'habitacionId',
                'estadoOperativo'
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
            'camas.camas.create',
            compact('departamentos')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'servicio_id' => [
                'required',
                'exists:camas_servicios,id',
            ],
            'habitacion_id' => [
                'nullable',
                'exists:camas_habitaciones,id',
            ],
            'codigo' => [
                'required',
                'string',
                'max:50',
                'unique:camas,codigo',
            ],
            'numero' => [
                'required',
                'string',
                'max:50',
            ],
            'tipo_cama' => [
                'nullable',
                'string',
                'max:100',
            ],
            'estado_operativo' => [
                'required',
                'in:OPERATIVA,LIMPIEZA,MANTENIMIENTO,BLOQUEADA,FUERA_DE_SERVICIO',
            ],
            'sexo_permitido' => [
                'required',
                'in:MASCULINO,FEMENINO,INDISTINTO',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $duplicada = Cama::query()
            ->where('servicio_id', $datos['servicio_id'])
            ->where('numero', $datos['numero'])
            ->exists();

        if ($duplicada) {
            return back()
                ->withInput()
                ->withErrors([
                    'numero' => 'Ya existe una cama con este número en el servicio seleccionado.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        Cama::create($datos);

        return redirect()
            ->route('camas.camas.index')
            ->with(
                'success',
                'Cama registrada correctamente.'
            );
    }

    public function edit(Cama $cama)
    {
        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $habitaciones = CamasHabitacion::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'camas.camas.edit',
            compact(
                'cama',
                'departamentos',
                'servicios',
                'habitaciones'
            )
        );
    }

    public function update(
        Request $request,
        Cama $cama
    ) {
        $datos = $request->validate([
            'servicio_id' => [
                'required',
                'exists:camas_servicios,id',
            ],
            'habitacion_id' => [
                'nullable',
                'exists:camas_habitaciones,id',
            ],
            'codigo' => [
                'required',
                'string',
                'max:50',
                'unique:camas,codigo,' . $cama->id,
            ],
            'numero' => [
                'required',
                'string',
                'max:50',
            ],
            'tipo_cama' => [
                'nullable',
                'string',
                'max:100',
            ],
            'estado_operativo' => [
                'required',
                'in:OPERATIVA,LIMPIEZA,MANTENIMIENTO,BLOQUEADA,FUERA_DE_SERVICIO',
            ],
            'sexo_permitido' => [
                'required',
                'in:MASCULINO,FEMENINO,INDISTINTO',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $duplicada = Cama::query()
            ->where('servicio_id', $datos['servicio_id'])
            ->where('numero', $datos['numero'])
            ->where('id', '<>', $cama->id)
            ->exists();

        if ($duplicada) {
            return back()
                ->withInput()
                ->withErrors([
                    'numero' => 'Ya existe una cama con este número en el servicio seleccionado.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        $cama->update($datos);

        return redirect()
            ->route('camas.camas.index')
            ->with(
                'success',
                'Cama actualizada correctamente.'
            );
    }

    public function destroy(Cama $cama)
    {
        $cama->delete();

        return redirect()
            ->route('camas.camas.index')
            ->with(
                'success',
                'Cama eliminada correctamente.'
            );
    }

    public function habitacionesPorServicio(
        CamasServicio $servicio
    ) {
        $habitaciones = CamasHabitacion::query()
            ->where('servicio_id', $servicio->id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'numero',
            ]);

        return response()->json($habitaciones);
    }
}
