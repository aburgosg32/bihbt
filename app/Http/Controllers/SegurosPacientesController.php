<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SegurosPacientesController extends Controller
{
    private const CONEXION = 'sigh';

    public function index(Request $request)
    {
        $datos = $request->validate([
            'documento' => ['nullable', 'string', 'max:12'],
            'historia' => ['nullable', 'integer', 'min:1'],
            'apellido_paterno' => ['nullable', 'string', 'max:40'],
            'apellido_materno' => ['nullable', 'string', 'max:40'],
        ]);

        $filtros = array_filter(
            $datos,
            fn($valor) => trim((string) $valor) !== ''
        );

        $pacientes = null;

        if ($filtros) {
            $query = DB::connection(self::CONEXION)
                ->table('Pacientes')
                ->select(
                    'IdPaciente',
                    'NroDocumento',
                    'NroHistoriaClinica',
                    'ApellidoPaterno',
                    'ApellidoMaterno',
                    'PrimerNombre',
                    'SegundoNombre',
                    'TercerNombre',
                    'FechaNacimiento',
                    'Telefono'
                );

            if (!empty($filtros['documento'])) {
                $query->where('NroDocumento', trim($filtros['documento']));
            }

            if (!empty($filtros['historia'])) {
                $query->where('NroHistoriaClinica', $filtros['historia']);
            }

            // Búsqueda por inicio del apellido, sin interpretar comodines.
            foreach (
                [
                    'apellido_paterno' => 'ApellidoPaterno',
                    'apellido_materno' => 'ApellidoMaterno',
                ] as $filtro => $columna
            ) {
                if (!empty($filtros[$filtro])) {
                    $valor = str_replace(
                        ['[', '%', '_'],
                        ['[[]', '[%]', '[_]'],
                        trim($filtros[$filtro])
                    );

                    $query->where($columna, 'like', $valor . '%');
                }
            }

            $pacientes = $query
                ->orderBy('ApellidoPaterno')
                ->orderBy('ApellidoMaterno')
                ->orderBy('PrimerNombre')
                ->orderBy('IdPaciente')
                ->paginate(15)
                ->withQueryString();
        }

        return view('seguros.pacientes.index', compact('pacientes'));
    }

    public function edit(int $paciente)
    {
        $paciente = DB::connection(self::CONEXION)
            ->table('Pacientes')
            ->where('IdPaciente', $paciente)
            ->first();

        abort_if(!$paciente, 404, 'Paciente no encontrado.');

        return view('seguros.pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, int $paciente)
    {
        $datos = $request->validate([
            'ApellidoPaterno' => ['required', 'string', 'max:40'],
            'ApellidoMaterno' => ['nullable', 'string', 'max:40'],
            'PrimerNombre' => ['required', 'string', 'max:40'],
            'SegundoNombre' => ['nullable', 'string', 'max:40'],
            'TercerNombre' => ['nullable', 'string', 'max:40'],
            'FechaNacimiento' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'NroDocumento' => ['nullable', 'string', 'max:12'],
            'Telefono' => ['nullable', 'string', 'max:10'],
            'Email' => ['nullable', 'email', 'max:50'],
            'DireccionDomicilio' => ['nullable', 'string', 'max:100'],
            'Observacion' => ['nullable', 'string', 'max:150'],
            'NombrePadre' => ['nullable', 'string', 'max:20'],
            'NombreMadre' => ['nullable', 'string', 'max:20'],
        ]);

        foreach ($datos as $campo => $valor) {
            $datos[$campo] = is_string($valor) ? trim($valor) : $valor;
        }
        // Formato YYYYMMDD, independiente del idioma de SQL Server.
        $datos['FechaNacimiento'] = str_replace(
            '-',
            '',
            $datos['FechaNacimiento']
        );
        $db = DB::connection(self::CONEXION);

        $db->transaction(function () use ($db, $paciente, $datos) {
            $actual = $db->table('Pacientes')
                ->where('IdPaciente', $paciente)
                ->lockForUpdate()
                ->first();

            abort_if(!$actual, 404, 'Paciente no encontrado.');

            // Si cambia el documento, comprobar duplicados del mismo tipo.
            $documento = trim((string) ($datos['NroDocumento'] ?? ''));

            if (
                $documento !== ''
                && $documento !== trim((string) $actual->NroDocumento)
            ) {
                $duplicado = $db->table('Pacientes')
                    ->where('NroDocumento', $documento)
                    ->where('IdDocIdentidad', $actual->IdDocIdentidad)
                    ->where('IdPaciente', '<>', $paciente)
                    ->exists();

                if ($duplicado) {
                    throw ValidationException::withMessages([
                        'NroDocumento' =>
                        'Otro paciente ya tiene ese documento con el mismo tipo de identidad.',
                    ]);
                }
            }

            // Solo actualizar los campos definidos en la validación.
            $db->table('Pacientes')
                ->where('IdPaciente', $paciente)
                ->update($datos);
        }, 3);

        return redirect()
            ->route('seguros.pacientes.edit', ['paciente' => $paciente])
            ->with('success', 'La información del paciente se actualizó correctamente.');
    }
}
