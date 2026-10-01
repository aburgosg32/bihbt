<?php

namespace App\Http\Controllers\Camas;

use App\Http\Controllers\Controller;
use App\Models\Cama;
use App\Models\CamaOcupacion;
use App\Models\CamasDepartamento;
use App\Models\CamasPaciente;
use App\Models\CamasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CensoCamaController extends Controller
{
    public function index(Request $request)
    {
        $departamentoId = $request->input('departamento_id');
        $servicioId = $request->input('servicio_id');
        $buscar = trim($request->input('buscar', ''));

        $departamentos = CamasDepartamento::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $servicios = CamasServicio::query()
            ->where('activo', true)
            ->when(
                $departamentoId,
                fn($q) => $q->where(
                    'departamento_id',
                    $departamentoId
                )
            )
            ->orderBy('nombre')
            ->get();

        $camas = Cama::query()
            ->with([
                'servicio.departamento',
                'habitacion',
                'ocupacionActiva.paciente',
            ])
            ->where('activo', true)

            ->when(
                $departamentoId,
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

            ->when(
                $servicioId,
                fn($query) =>
                $query->where(
                    'servicio_id',
                    $servicioId
                )
            )

            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {

                    $query->where(function ($q) use ($buscar) {

                        $q->where(
                            'numero',
                            'like',
                            "%{$buscar}%"
                        )
                            ->orWhere(
                                'codigo',
                                'like',
                                "%{$buscar}%"
                            )
                            ->orWhereHas(
                                'ocupacionActiva.paciente',
                                function ($paciente) use ($buscar) {

                                    $paciente
                                        ->where(
                                            'nombre_completo',
                                            'like',
                                            "%{$buscar}%"
                                        )
                                        ->orWhere(
                                            'nro_historia_clinica',
                                            'like',
                                            "%{$buscar}%"
                                        )
                                        ->orWhere(
                                            'telefono',
                                            'like',
                                            "%{$buscar}%"
                                        );
                                }
                            );
                    });
                }
            )

            ->orderBy('servicio_id')
            ->orderBy('habitacion_id')
            ->orderBy('numero')
            ->get();

        $totalCamas = $camas->count();

        $ocupadas = $camas
            ->filter(
                fn($cama) =>
                $cama->ocupacionActiva !== null
            )
            ->count();

        $libres = $totalCamas - $ocupadas;

        $camasAgrupadas = $camas
            ->groupBy(function ($cama) {

                return $cama->servicio_id . '-' .
                    ($cama->habitacion_id ?? 'SIN');
            });

        return view(
            'camas.censo.index',
            compact(
                'departamentos',
                'servicios',
                'camasAgrupadas',
                'totalCamas',
                'ocupadas',
                'libres'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR PACIENTE EN SQL SERVER SIGH
    |--------------------------------------------------------------------------
    */

    public function buscarPaciente(Request $request)
    {
        $request->validate([
            'historia' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $historia = trim(
            $request->historia
        );

        $paciente = DB::connection('sigh')
            ->table('Pacientes as P')

            ->leftJoin(
                'TiposSexo as TS',
                'TS.IdTipoSexo',
                '=',
                'P.IdTipoSexo'
            )

            ->select([
                'P.IdPaciente',
                'P.NroHistoriaClinica',
                'P.ApellidoPaterno',
                'P.ApellidoMaterno',
                'P.PrimerNombre',
                'P.SegundoNombre',
                'P.Telefono',
                'P.FechaNacimiento',

                'TS.Descripcion as Sexo',
            ])

            ->where(
                'P.NroHistoriaClinica',
                $historia
            )

            ->first();

        if (!$paciente) {

            return response()->json([
                'success' => false,
                'message' =>
                'No se encontró ningún paciente con esa historia clínica.',
            ], 404);
        }

        $nombreCompleto = trim(
            implode(
                ' ',
                array_filter([
                    $paciente->ApellidoPaterno ?? null,
                    $paciente->ApellidoMaterno ?? null,
                    $paciente->PrimerNombre ?? null,
                    $paciente->SegundoNombre ?? null,
                ])
            )
        );

        return response()->json([
            'success' => true,

            'paciente' => [

                'id_paciente_sigh' =>
                $paciente->IdPaciente,

                'nro_historia_clinica' =>
                $paciente->NroHistoriaClinica,

                'nombre_completo' =>
                mb_strtoupper(
                    $nombreCompleto,
                    'UTF-8'
                ),

                'telefono' =>
                $paciente->Telefono,

                'sexo' =>
                $paciente->Sexo ?? null,

                'fecha_nacimiento' =>
                $paciente->FechaNacimiento
                    ? date(
                        'Y-m-d',
                        strtotime(
                            $paciente->FechaNacimiento
                        )
                    )
                    : null,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ASIGNAR PACIENTE A CAMA
    |--------------------------------------------------------------------------
    */

    public function asignar(Request $request)
    {
        $datos = $request->validate([
            'cama_id' => [
                'required',
                'exists:camas,id',
            ],

            'id_paciente_sigh' => [
                'required',
                'integer',
            ],

            'nro_historia_clinica' => [
                'required',
                'string',
                'max:50',
            ],

            'nombre_completo' => [
                'required',
                'string',
                'max:300',
            ],
            'sexo' => [
                'nullable',
                'string',
                'max:20',
            ],

            'fecha_nacimiento' => [
                'nullable',
                'date',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        DB::transaction(function () use ($datos) {

            /*
            |--------------------------------------------------------------------------
            | BLOQUEAR CAMA
            |--------------------------------------------------------------------------
            */

            $cama = Cama::query()
                ->lockForUpdate()
                ->findOrFail(
                    $datos['cama_id']
                );

            if (!$cama->activo) {
                abort(
                    422,
                    'La cama se encuentra inactiva.'
                );
            }

            if (
                $cama->estado_operativo !==
                'OPERATIVA'
            ) {
                abort(
                    422,
                    'La cama no se encuentra operativa.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAR QUE LA CAMA NO ESTÉ OCUPADA
            |--------------------------------------------------------------------------
            */

            $camaOcupada = CamaOcupacion::query()
                ->where(
                    'cama_id',
                    $cama->id
                )
                ->where(
                    'estado',
                    'ACTIVO'
                )
                ->lockForUpdate()
                ->exists();

            if ($camaOcupada) {
                abort(
                    422,
                    'La cama ya se encuentra ocupada.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR / ACTUALIZAR PACIENTE EN MYSQL
            |--------------------------------------------------------------------------
            */

            $paciente = CamasPaciente::updateOrCreate(
                [
                    'id_paciente_sigh' =>
                    $datos['id_paciente_sigh'],
                ],
                [
                    'nro_historia_clinica' =>
                    $datos['nro_historia_clinica'],

                    'nombre_completo' =>
                    $datos['nombre_completo'],
                    'sexo' =>
                    $datos['sexo'] ?? null,

                    'fecha_nacimiento' =>
                    $datos['fecha_nacimiento'] ?? null,

                    'telefono' =>
                    $datos['telefono'] ?? null,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDAR QUE PACIENTE NO TENGA OTRA CAMA
            |--------------------------------------------------------------------------
            */

            $yaTieneCama = CamaOcupacion::query()
                ->where(
                    'paciente_id',
                    $paciente->id
                )
                ->where(
                    'estado',
                    'ACTIVO'
                )
                ->lockForUpdate()
                ->exists();

            if ($yaTieneCama) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'paciente' => 'El paciente ya tiene una cama asignada actualmente.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CREAR OCUPACIÓN
            |--------------------------------------------------------------------------
            */

            CamaOcupacion::create([
                'paciente_id' =>
                $paciente->id,

                'cama_id' =>
                $cama->id,

                'fecha_hora_ingreso' =>
                now(),

                'estado' =>
                'ACTIVO',

                'usuario_ingreso_id' =>
                auth()->id(),
            ]);
        });

        return redirect()
            ->route('camas.censo')
            ->with(
                'success',
                'Paciente asignado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DAR ALTA / LIBERAR CAMA
    |--------------------------------------------------------------------------
    */

    public function alta(CamaOcupacion $ocupacion)
    {
        DB::transaction(function () use ($ocupacion) {

            $registro = CamaOcupacion::query()
                ->lockForUpdate()
                ->findOrFail($ocupacion->id);

            if ($registro->estado !== 'ACTIVO') {
                abort(
                    422,
                    'La ocupación ya se encuentra finalizada.'
                );
            }

            $registro->estado = 'ALTA';
            $registro->fecha_hora_alta = now();
            $registro->usuario_alta_id = auth()->id();

            $registro->save();
        });

        return redirect()
            ->route('camas.censo')
            ->with(
                'success',
                'Paciente dado de alta. La cama quedó disponible.'
            );
    }
}
