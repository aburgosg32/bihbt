<?php

namespace App\Http\Controllers;

use App\Models\ProcedimientoMedico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcedimientoMedicoController extends Controller
{
    public function index(Request $request)
    {
        $query = ProcedimientoMedico::query()
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres');

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($consulta) use ($buscar) {
                $consulta
                    ->where('apellido_paterno', 'like', "%{$buscar}%")
                    ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                    ->orWhere('nombres', 'like', "%{$buscar}%")
                    ->orWhere('especialidad', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where(
                'activo',
                $request->estado === 'activo'
            );
        }

        $medicos = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'procedimientos.medicos.index',
            compact('medicos')
        );
    }

    public function store(Request $request)
    {
        $this->normalizarDatos($request);

        $datos = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        $duplicado = $this->existeDuplicado($datos);

        if ($duplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombres' => 'Este médico ya está registrado.',
                ]);
        }

        ProcedimientoMedico::create([
            ...$datos,
            'activo' => true,
        ]);

        return redirect()
            ->route('procedimientos.medicos.index')
            ->with('success', 'Médico registrado correctamente.');
    }

    public function update(
        Request $request,
        ProcedimientoMedico $medico
    ) {
        $this->normalizarDatos($request);

        $datos = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        $duplicado = $this->existeDuplicado(
            $datos,
            $medico->id
        );

        if ($duplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombres' => 'Este médico ya está registrado.',
                ]);
        }

        $medico->update($datos);

        return redirect()
            ->route('procedimientos.medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function cambiarEstado(ProcedimientoMedico $medico)
    {
        $medico->update([
            'activo' => !$medico->activo,
        ]);

        $mensaje = $medico->activo
            ? 'Médico activado correctamente.'
            : 'Médico desactivado correctamente.';

        return redirect()
            ->route('procedimientos.medicos.index')
            ->with('success', $mensaje);
    }

    private function normalizarDatos(Request $request): void
    {
        $request->merge([
            'apellido_paterno' => mb_strtoupper(
                trim((string) $request->apellido_paterno)
            ),

            'apellido_materno' => $request->filled('apellido_materno')
                ? mb_strtoupper(trim($request->apellido_materno))
                : null,

            'nombres' => mb_strtoupper(
                trim((string) $request->nombres)
            ),

            'especialidad' => mb_strtoupper(
                trim((string) $request->especialidad)
            ),
        ]);
    }

    private function reglas(): array
    {
        return [
            'apellido_paterno' => [
                'required',
                'string',
                'max:80',
            ],

            'apellido_materno' => [
                'nullable',
                'string',
                'max:80',
            ],

            'nombres' => [
                'required',
                'string',
                'max:120',
            ],

            'especialidad' => [
                'required',
                'string',
                'max:150',
            ],
        ];
    }

    private function mensajes(): array
    {
        return [
            'apellido_paterno.required' =>
            'Ingrese el apellido paterno.',

            'nombres.required' =>
            'Ingrese los nombres del médico.',

            'especialidad.required' =>
            'Ingrese la especialidad.',
        ];
    }

    private function existeDuplicado(
        array $datos,
        ?int $ignorarId = null
    ): bool {
        $query = ProcedimientoMedico::query()
            ->where(
                'apellido_paterno',
                $datos['apellido_paterno']
            )
            ->where(
                'apellido_materno',
                $datos['apellido_materno']
            )
            ->where(
                'nombres',
                $datos['nombres']
            )
            ->where(
                'especialidad',
                $datos['especialidad']
            );

        if ($ignorarId !== null) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}
