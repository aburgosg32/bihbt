<?php

namespace App\Http\Controllers;

use App\Models\QuirofanoSala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuirofanoSalaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar'));

        $salas = QuirofanoSala::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('ubicacion', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $resumen = [
            'total' => QuirofanoSala::count(),
            'operativas' => QuirofanoSala::where('operativa', true)->count(),
            'inoperativas' => QuirofanoSala::where('operativa', false)->count(),
            'disponibles' => QuirofanoSala::where('operativa', true)
                ->where('estado_actual', 'DISPONIBLE')
                ->count(),
        ];

        return view(
            'quirofano.configuracion.salas.index',
            compact('salas', 'resumen', 'buscar')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:quirofano_salas,codigo',
                ],
                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    'unique:quirofano_salas,nombre',
                ],
                'ubicacion' => [
                    'nullable',
                    'string',
                    'max:150',
                ],
                'tipo_sala' => [
                    'required',
                    Rule::in([
                        'PROGRAMADA',
                        'EMERGENCIA',
                    ]),
                ],
                'hora_inicio_jornada' => [
                    'nullable',
                    'required_if:tipo_sala,PROGRAMADA',
                    'date_format:H:i',
                ],
                'hora_fin_jornada' => [
                    'nullable',
                    'required_if:tipo_sala,PROGRAMADA',
                    'date_format:H:i',
                    'after:hora_inicio_jornada',
                ],
                'minutos_limpieza' => [
                    'required',
                    'integer',
                    'min:30',
                    'max:45',
                ],
                'observaciones' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'codigo.required' =>
                'Ingrese el código de la sala.',

                'codigo.unique' =>
                'El código ingresado ya está registrado.',

                'nombre.required' =>
                'Ingrese el nombre de la sala.',

                'nombre.unique' =>
                'El nombre ingresado ya está registrado.',

                'tipo_sala.required' =>
                'Seleccione el tipo de sala.',

                'hora_inicio_jornada.required_if' =>
                'Ingrese la hora de inicio para una sala programada.',

                'hora_fin_jornada.required_if' =>
                'Ingrese la hora de finalización para una sala programada.',

                'hora_fin_jornada.after' =>
                'La hora final debe ser posterior a la hora inicial.',

                'minutos_limpieza.min' =>
                'La limpieza debe ser de al menos 30 minutos.',

                'minutos_limpieza.max' =>
                'La limpieza no puede superar los 45 minutos.',
            ]
        );

        $datos['codigo'] = strtoupper(
            trim($datos['codigo'])
        );

        $datos['nombre'] = trim($datos['nombre']);

        $datos['ubicacion'] = !empty($datos['ubicacion'])
            ? trim($datos['ubicacion'])
            : null;

        if ($datos['tipo_sala'] === 'EMERGENCIA') {
            $datos['hora_inicio_jornada'] = null;
            $datos['hora_fin_jornada'] = null;
        }

        $datos['estado_actual'] = 'DISPONIBLE';
        $datos['operativa'] = true;

        QuirofanoSala::create($datos);

        return redirect()
            ->route('quirofano.salas.index')
            ->with(
                'success',
                'La sala fue registrada correctamente.'
            );
    }
    public function update(
        Request $request,
        QuirofanoSala $sala
    ) {
        $datos = $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique(
                        'quirofano_salas',
                        'codigo'
                    )->ignore($sala->id),
                ],
                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(
                        'quirofano_salas',
                        'nombre'
                    )->ignore($sala->id),
                ],
                'ubicacion' => [
                    'nullable',
                    'string',
                    'max:150',
                ],
                'tipo_sala' => [
                    'required',
                    Rule::in([
                        'PROGRAMADA',
                        'EMERGENCIA',
                    ]),
                ],
                'hora_inicio_jornada' => [
                    'nullable',
                    'required_if:tipo_sala,PROGRAMADA',
                    'date_format:H:i',
                ],
                'hora_fin_jornada' => [
                    'nullable',
                    'required_if:tipo_sala,PROGRAMADA',
                    'date_format:H:i',
                    'after:hora_inicio_jornada',
                ],
                'minutos_limpieza' => [
                    'required',
                    'integer',
                    'min:30',
                    'max:45',
                ],
                'observaciones' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'codigo.required' =>
                'Ingrese el código de la sala.',

                'codigo.unique' =>
                'El código ingresado ya está registrado.',

                'nombre.required' =>
                'Ingrese el nombre de la sala.',

                'nombre.unique' =>
                'El nombre ingresado ya está registrado.',

                'tipo_sala.required' =>
                'Seleccione el tipo de sala.',

                'hora_inicio_jornada.required_if' =>
                'Ingrese la hora de inicio para una sala programada.',

                'hora_fin_jornada.required_if' =>
                'Ingrese la hora final para una sala programada.',

                'hora_fin_jornada.after' =>
                'La hora final debe ser posterior a la hora inicial.',

                'minutos_limpieza.min' =>
                'La limpieza debe ser de al menos 30 minutos.',

                'minutos_limpieza.max' =>
                'La limpieza no puede superar los 45 minutos.',
            ]
        );

        $datos['codigo'] = strtoupper(
            trim($datos['codigo'])
        );

        $datos['nombre'] = trim($datos['nombre']);

        $datos['ubicacion'] = !empty($datos['ubicacion'])
            ? trim($datos['ubicacion'])
            : null;

        if ($datos['tipo_sala'] === 'EMERGENCIA') {
            $datos['hora_inicio_jornada'] = null;
            $datos['hora_fin_jornada'] = null;
        }

        $sala->update($datos);

        return redirect()
            ->route('quirofano.salas.index')
            ->with(
                'success',
                'Los datos de la sala fueron actualizados.'
            );
    }

    public function cambiarOperatividad(
        Request $request,
        QuirofanoSala $sala
    ) {
        $request->validate(
            [
                'operativa' => [
                    'required',
                    'boolean',
                ],
                'motivo' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ]
        );

        $nuevaCondicion = $request->boolean('operativa');

        if (
            !$nuevaCondicion
            && in_array(
                $sala->estado_actual,
                ['EN_USO', 'EN_PREPARACION'],
                true
            )
        ) {
            return redirect()
                ->route('quirofano.salas.index')
                ->with(
                    'error',
                    'No se puede marcar como inoperativa una sala que está en uso o en preparación.'
                );
        }

        DB::transaction(function () use (
            $sala,
            $nuevaCondicion,
            $request
        ) {
            $sala->operativa = $nuevaCondicion;

            if ($nuevaCondicion) {
                $sala->estado_actual = 'DISPONIBLE';
            } else {
                $sala->estado_actual = 'MANTENIMIENTO';
            }

            if ($request->filled('motivo')) {
                $sala->observaciones = trim($request->input('motivo'));
            }

            $sala->save();
        });

        $mensaje = $nuevaCondicion
            ? 'La sala fue habilitada y se encuentra disponible.'
            : 'La sala fue marcada como inoperativa.';

        return redirect()
            ->route('quirofano.salas.index')
            ->with('success', $mensaje);
    }
}
