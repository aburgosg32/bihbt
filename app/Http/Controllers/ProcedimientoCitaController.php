<?php

namespace App\Http\Controllers;

use App\Models\Procedimiento;
use App\Models\ProcedimientoCupo;
use App\Models\ProcedimientoProgramacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ProcedimientoCita;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProcedimientoCitaController extends Controller
{
    public function index()
    {
        $procedimientos = Procedimiento::query()
            ->with('servicio')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'procedimientos.procedimientos.citas.index',
            compact('procedimientos')
        );
    }

    public function fechasDisponibles(
        Request $request
    ): JsonResponse {
        $datos = $request->validate([
            'procedimiento_id' => [
                'required',
                'integer',
                'exists:procedimientos,id',
            ],
        ]);

        /*
     * Todas las fechas programadas.
     * Incluye fechas anteriores para consultar
     * citas y realizar reprogramaciones.
     */
        $fechasProgramadas = ProcedimientoProgramacion::query()
            ->where(
                'procedimiento_id',
                $datos['procedimiento_id']
            )
            ->whereHas('cupos')
            ->orderBy('fecha_programacion')
            ->pluck('fecha_programacion')
            ->map(function ($fecha) {
                return $fecha instanceof \Carbon\Carbon
                    ? $fecha->format('Y-m-d')
                    : substr((string) $fecha, 0, 10);
            })
            ->unique()
            ->values();

        /*
     * Solo fechas actuales o futuras que
     * todavía tengan cupos disponibles.
     */
        $fechasDisponibles = ProcedimientoProgramacion::query()
            ->where(
                'procedimiento_id',
                $datos['procedimiento_id']
            )
            ->where('activo', true)
            ->whereDate(
                'fecha_programacion',
                '>=',
                now()->toDateString()
            )
            ->whereHas('cupos', function ($query) {
                $query
                    ->where(
                        'estado',
                        ProcedimientoCupo::ESTADO_DISPONIBLE
                    )
                    ->where(function ($bloqueo) {
                        $bloqueo
                            ->whereNull('bloqueado_hasta')
                            ->orWhere(
                                'bloqueado_hasta',
                                '<=',
                                now()
                            );
                    });
            })
            ->orderBy('fecha_programacion')
            ->pluck('fecha_programacion')
            ->map(function ($fecha) {
                return $fecha instanceof \Carbon\Carbon
                    ? $fecha->format('Y-m-d')
                    : substr((string) $fecha, 0, 10);
            })
            ->unique()
            ->values();

        return response()->json([
            'fechas_programadas' =>
            $fechasProgramadas,

            'fechas_disponibles' =>
            $fechasDisponibles,
        ]);
    }

    public function cuposDisponibles(
        Request $request
    ): JsonResponse {
        $datos = $request->validate([
            'procedimiento_id' => [
                'required',
                'integer',
                'exists:procedimientos,id',
            ],

            'fecha' => [
                'required',
                'date_format:Y-m-d',
            ],
        ]);

        $programaciones = ProcedimientoProgramacion::query()
            ->with([
                'medico',
                'cupos' => function ($query) {
                    $query
                        ->with('cita')
                        ->orderBy('hora');
                },
            ])
            ->where(
                'procedimiento_id',
                $datos['procedimiento_id']
            )
            ->whereDate(
                'fecha_programacion',
                $datos['fecha']
            )
            ->where('activo', true)
            ->orderBy('hora_inicio')
            ->get();

        $cupos = $programaciones
            ->flatMap(function ($programacion) {
                return $programacion->cupos->map(
                    function ($cupo) use ($programacion) {
                        $bloqueoTemporalActivo =
                            $cupo->estado ===
                            ProcedimientoCupo::ESTADO_DISPONIBLE &&
                            !empty($cupo->bloqueo_token) &&
                            !empty($cupo->bloqueado_hasta) &&
                            $cupo->bloqueado_hasta->isFuture();

                        $estadoVisible = $bloqueoTemporalActivo
                            ? 'EN_PROCESO'
                            : $cupo->estado;

                        return [
                            'id' => $cupo->id,

                            'hora' => substr(
                                (string) $cupo->hora,
                                0,
                                5
                            ),

                            'estado' => $estadoVisible,

                            'bloqueado_hasta' =>
                            $bloqueoTemporalActivo
                                ? $cupo->bloqueado_hasta
                                ->toIso8601String()
                                : null,

                            'motivo_bloqueo' =>
                            $cupo->motivo_bloqueo,

                            'medico' =>
                            $programacion->medico
                                ->nombre_completo
                                ?? 'SIN MÉDICO',

                            'especialidad' =>
                            $programacion->medico
                                ->especialidad
                                ?? null,

                            'cita' => $cupo->cita
                                ? [
                                    'id' => $cupo->cita->id,

                                    'numero_cuenta' =>
                                    $cupo->cita->numero_cuenta,

                                    'historia_clinica' =>
                                    $cupo->cita->historia_clinica,

                                    'paciente' =>
                                    $cupo->cita->paciente,

                                    'estado' =>
                                    $cupo->cita->estado,
                                ]
                                : null,
                        ];
                    }
                );
            })
            ->sortBy('hora')
            ->values();

        return response()->json([
            'cupos' => $cupos,
        ]);
    }
    public function buscarCuenta(
        string $cuenta
    ): JsonResponse {
        try {
            $cuenta = trim($cuenta);

            $citaExistente = ProcedimientoCita::query()
                ->with([
                    'cupo.programacion.procedimiento',
                ])
                ->where('numero_cuenta', $cuenta)
                ->first();

            if ($citaExistente) {
                $cupo = $citaExistente->cupo;
                $programacion = $cupo?->programacion;
                $procedimiento = $programacion?->procedimiento;

                $detalle = '';

                if ($programacion && $cupo) {
                    $detalle = ' para ' .
                        ($procedimiento?->nombre ?? 'un procedimiento') .
                        ', el ' .
                        $programacion->fecha_programacion->format('d/m/Y') .
                        ' a las ' .
                        substr((string) $cupo->hora, 0, 5);
                }

                return response()->json([
                    'success' => false,
                    'message' =>
                    'Este número de cuenta ya tiene una cita registrada' .
                        $detalle . '.',
                ], 422);
            }

            $paciente = $this->buscarPacienteSighPorCuenta(
                $cuenta
            );

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'No se encontró información para la cuenta ingresada.',
                ], 404);
            }

            return response()->json([
                'success' => true,

                'data' => [
                    'numero_cuenta' =>
                    $paciente->IdCuentaAtencion ?? '',

                    'id_paciente_sigh' =>
                    $paciente->IdPaciente ?? null,

                    'paciente' =>
                    $paciente->Paciente ?? '',

                    'historia_clinica' =>
                    $paciente->NroHistoriaClinica ?? '',

                    'telefono' =>
                    $paciente->TelPaciente ?? '',

                    'fecha_nacimiento' =>
                    $paciente->FechaNacimiento ?? null,

                    'edad' =>
                    $paciente->Edad ?? '',

                    'tipo_edad' =>
                    $paciente->TipoEdad ?? '',

                    'fecha_ingreso' =>
                    $paciente->FechaIngreso ?? null,

                    'hora_ingreso' =>
                    $paciente->HoraIngreso ?? null,

                    'tipo_servicio' =>
                    $paciente->TipoServicio ?? '',

                    'servicio_origen' =>
                    $paciente->NombreServicio ?? '',

                    'financiamiento' =>
                    $paciente->FF ?? '',
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error SIGH: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function store(
        Request $request
    ): JsonResponse {
        $request->merge([
            'numero_cuenta' => trim(
                (string) $request->input('numero_cuenta')
            ),
        ]);

        $datos = $request->validate(
            [
                'procedimiento_cupo_id' => [
                    'required',
                    'integer',
                    'exists:procedimiento_cupos,id',
                ],

                'bloqueo_token' => [
                    'required',
                    'string',
                    'max:64',
                ],

                'numero_cuenta' => [
                    'required',
                    'string',
                    'max:30',

                    Rule::unique(
                        'procedimiento_citas',
                        'numero_cuenta'
                    ),
                ],

                'observacion' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'procedimiento_cupo_id.required' =>
                'No se ha seleccionado el cupo.',

                'procedimiento_cupo_id.exists' =>
                'El cupo seleccionado no existe.',

                'bloqueo_token.required' =>
                'La reserva temporal del cupo no existe o venció.',

                'numero_cuenta.required' =>
                'Ingrese el número de cuenta.',

                'numero_cuenta.unique' =>
                'Este número de cuenta ya tiene una cita registrada.',
            ]
        );

        /*
     * Los datos se vuelven a consultar en SIGH.
     * No confiamos en datos ocultos enviados desde el navegador.
     */
        $paciente = $this->buscarPacienteSighPorCuenta(
            $datos['numero_cuenta']
        );

        if (!$paciente) {
            throw ValidationException::withMessages([
                'numero_cuenta' =>
                'La cuenta no fue encontrada en SIGH.',
            ]);
        }

        $fechaNacimiento = null;

        if (!empty($paciente->FechaNacimiento)) {
            try {
                $fechaNacimiento = \Carbon\Carbon::parse(
                    $paciente->FechaNacimiento
                )->format('Y-m-d');
            } catch (\Throwable $e) {
                $fechaNacimiento = null;
            }
        }

        try {
            $cita = DB::transaction(
                function () use (
                    $datos,
                    $paciente,
                    $fechaNacimiento
                ) {
                    $numeroCuenta = trim(
                        (string) (
                            $paciente->IdCuentaAtencion
                            ?? $datos['numero_cuenta']
                        )
                    );

                    $citaExistente = ProcedimientoCita::query()
                        ->where('numero_cuenta', $numeroCuenta)
                        ->lockForUpdate()
                        ->first();

                    if ($citaExistente) {
                        throw ValidationException::withMessages([
                            'numero_cuenta' =>
                            'Este número de cuenta ya tiene una cita registrada.',
                        ]);
                    }

                    /*
             * Bloqueamos el cupo mientras se registra la cita.
             * Así dos usuarios no pueden reservarlo simultáneamente.
             */
                    $cupo = ProcedimientoCupo::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $datos['procedimiento_cupo_id']
                        );

                    if (
                        $cupo->estado !==
                        ProcedimientoCupo::ESTADO_DISPONIBLE
                    ) {
                        throw ValidationException::withMessages([
                            'procedimiento_cupo_id' =>
                            'El cupo ya no está disponible.',
                        ]);
                    }

                    $tokenCorrecto =
                        !empty($cupo->bloqueo_token) &&
                        hash_equals(
                            (string) $cupo->bloqueo_token,
                            (string) $datos['bloqueo_token']
                        );

                    $bloqueoVigente =
                        !empty($cupo->bloqueado_hasta) &&
                        $cupo->bloqueado_hasta->isFuture();

                    if (!$tokenCorrecto || !$bloqueoVigente) {
                        throw ValidationException::withMessages([
                            'procedimiento_cupo_id' =>
                            'La reserva temporal del cupo venció. ' .
                                'Seleccione nuevamente el horario.',
                        ]);
                    }

                    if ($cupo->cita()->exists()) {
                        throw ValidationException::withMessages([
                            'procedimiento_cupo_id' =>
                            'Este cupo ya tiene una cita registrada.',
                        ]);
                    }

                    $cita = ProcedimientoCita::create([
                        'procedimiento_cupo_id' =>
                        $cupo->id,

                        'numero_cuenta' => $numeroCuenta,

                        'id_paciente_sigh' =>
                        isset($paciente->IdPaciente)
                            ? (string) $paciente->IdPaciente
                            : null,

                        'historia_clinica' =>
                        $paciente->NroHistoriaClinica ?? null,

                        'paciente' =>
                        $paciente->Paciente,

                        'telefono' =>
                        $paciente->TelPaciente ?? null,

                        'sexo' =>
                        $paciente->Sexo ?? null,

                        'fecha_nacimiento' =>
                        $fechaNacimiento,

                        'servicio_origen' =>
                        $paciente->NombreServicio ?? null,

                        'tipo_servicio' =>
                        $paciente->TipoServicio ?? null,

                        'financiamiento' =>
                        $paciente->FF ?? null,

                        'estado' =>
                        ProcedimientoCita::ESTADO_PROGRAMADA,

                        'observacion' =>
                        $datos['observacion'] ?? null,

                        'fecha_registro' => now(),
                    ]);

                    $cupo->update([
                        'estado' =>
                        ProcedimientoCupo::ESTADO_RESERVADO,

                        'motivo_bloqueo' => null,
                        'bloqueo_token' => null,
                        'bloqueado_hasta' => null,
                    ]);

                    return $cita;
                }
            );
        } catch (QueryException $e) {
            $esCuentaDuplicada =
                ($e->errorInfo[1] ?? null) === 1062 &&
                str_contains(
                    $e->getMessage(),
                    'procedimiento_citas_numero_cuenta_unique'
                );

            if ($esCuentaDuplicada) {
                throw ValidationException::withMessages([
                    'numero_cuenta' =>
                    'Este número de cuenta ya tiene una cita registrada.',
                ]);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,

            'message' =>
            'La cita fue registrada correctamente.',

            'cita_id' => $cita->id,

            'ticket_url' => route(
                'procedimientos.citas.ticket',
                $cita
            ),
        ]);
    }
    public function ticket(
        ProcedimientoCita $cita
    ) {
        $cita->load([
            'cupo.programacion.procedimiento.servicio',
            'cupo.programacion.medico',
        ]);

        $pdf = Pdf::loadView(
            'procedimientos.procedimientos.citas.ticket',
            compact('cita')
        );

        /*
     * Papel térmico de 80 mm.
     * 226.77 puntos = 80 mm.
     
        $pdf->setPaper(
            [0, 0, 226.77, 425.20],
            'portrait'
        );*/

        /*
 * Ticket térmico:
 * 80 mm de ancho por 100 mm de alto.
 */
        $pdf->setPaper(
            [0, 0, 226.77, 283.46],
            'portrait'
        );

        return $pdf->stream(
            'ticket-cita-' . $cita->id . '.pdf'
        );
    }
    public function reprogramar(
        Request $request,
        ProcedimientoCita $cita
    ): JsonResponse {
        $datos = $request->validate(
            [
                'nuevo_cupo_id' => [
                    'required',
                    'integer',
                    'exists:procedimiento_cupos,id',
                ],
            ],
            [
                'nuevo_cupo_id.required' =>
                'Seleccione el nuevo horario.',

                'nuevo_cupo_id.exists' =>
                'El nuevo cupo seleccionado no existe.',
            ]
        );

        DB::transaction(function () use ($cita, $datos) {

            $citaBloqueada = ProcedimientoCita::query()
                ->lockForUpdate()
                ->findOrFail($cita->id);

            if (
                $citaBloqueada->estado !==
                ProcedimientoCita::ESTADO_PROGRAMADA
            ) {
                throw ValidationException::withMessages([
                    'cita' =>
                    'Solo se pueden reprogramar citas programadas.',
                ]);
            }

            $cupoAnterior = ProcedimientoCupo::query()
                ->lockForUpdate()
                ->findOrFail(
                    $citaBloqueada->procedimiento_cupo_id
                );

            $nuevoCupo = ProcedimientoCupo::query()
                ->lockForUpdate()
                ->findOrFail($datos['nuevo_cupo_id']);

            if ($nuevoCupo->id === $cupoAnterior->id) {
                throw ValidationException::withMessages([
                    'nuevo_cupo_id' =>
                    'Seleccione un cupo diferente al actual.',
                ]);
            }

            if (
                $nuevoCupo->estado !==
                ProcedimientoCupo::ESTADO_DISPONIBLE
            ) {
                throw ValidationException::withMessages([
                    'nuevo_cupo_id' =>
                    'El nuevo cupo ya no está disponible.',
                ]);
            }

            $nuevoCupoEnProceso =
                !empty($nuevoCupo->bloqueo_token) &&
                !empty($nuevoCupo->bloqueado_hasta) &&
                $nuevoCupo->bloqueado_hasta->isFuture();

            if ($nuevoCupoEnProceso) {
                throw ValidationException::withMessages([
                    'nuevo_cupo_id' =>
                    'Otro usuario está registrando un paciente ' .
                        'en el nuevo cupo seleccionado.',
                ]);
            }

            if ($nuevoCupo->cita()->exists()) {
                throw ValidationException::withMessages([
                    'nuevo_cupo_id' =>
                    'El nuevo cupo ya tiene una cita registrada.',
                ]);
            }

            /*
         * La cita conserva todos los datos del paciente,
         * pero cambia al nuevo cupo.
         */
            $citaBloqueada->update([
                'procedimiento_cupo_id' =>
                $nuevoCupo->id,
            ]);

            /*
         * Reservamos el nuevo horario.
         */
            $nuevoCupo->update([
                'estado' =>
                ProcedimientoCupo::ESTADO_RESERVADO,

                'motivo_bloqueo' => null,
                'bloqueo_token' => null,
                'bloqueado_hasta' => null,
            ]);

            /*
         * Liberamos el horario anterior.
         */
            $cupoAnterior->update([
                'estado' =>
                ProcedimientoCupo::ESTADO_DISPONIBLE,

                'motivo_bloqueo' => null,
                'bloqueo_token' => null,
                'bloqueado_hasta' => null,
            ]);
        });

        return response()->json([
            'success' => true,

            'message' =>
            'La cita fue reprogramada correctamente.',

            'ticket_url' => route(
                'procedimientos.citas.ticket',
                $cita
            ),
        ]);
    }

    /**
     * Consulta directamente las tablas de SIGHO sin depender de una vista.
     */
    private function buscarPacienteSighPorCuenta(
        string $cuenta
    ): ?object {
        return DB::connection('sigh')
            ->table('Atenciones as A')
            ->leftJoin('Pacientes as P', 'A.IdPaciente', '=', 'P.IdPaciente')
            ->leftJoin('Servicios as S', 'A.IdServicioIngreso', '=', 'S.IdServicio')
            ->leftJoin(
                'FuentesFinanciamiento as FF',
                'FF.IdFuenteFinanciamiento',
                '=',
                'A.IdFuenteFinanciamiento'
            )
            ->leftJoin('TiposEdad as TE', 'TE.IdTipoEdad', '=', 'A.IdTipoEdad')
            ->leftJoin(
                'TiposServicio as TS',
                'A.IdTipoServicio',
                '=',
                'TS.IdTipoServicio'
            )
            ->leftJoin('TiposSexo as Se', 'P.IdTipoSexo', '=', 'Se.IdTipoSexo')
            ->select([
                'A.IdCuentaAtencion',
                'A.IdPaciente',
                'P.NroDocumento',
                DB::raw('UPPER(Se.Descripcion) AS Sexo'),
                DB::raw("
    UPPER(
        LTRIM(
            RTRIM(
                ISNULL(P.ApellidoPaterno, '') + ' ' +
                ISNULL(P.ApellidoMaterno, '') + ' ' +
                ISNULL(P.PrimerNombre, '') + ' ' +
                ISNULL(P.SegundoNombre, '')
            )
        )
    ) AS Paciente
"),
                DB::raw('P.Telefono AS TelPaciente'),
                'P.NroHistoriaClinica',
                DB::raw(
                    'CONVERT(date, P.FechaNacimiento) AS FechaNacimiento'
                ),
                'A.Edad',
                DB::raw(
                    'CONVERT(date, A.FechaIngreso) AS FechaIngreso'
                ),
                'A.HoraIngreso',
                DB::raw('UPPER(TS.Descripcion) AS TipoServicio'),
                DB::raw('S.Nombre AS NombreServicio'),
                DB::raw('UPPER(TE.Descripcion) AS TipoEdad'),
                DB::raw('FF.Descripcion AS FF'),
            ])
            ->where('A.IdCuentaAtencion', trim($cuenta))
            ->orderByDesc('A.FechaIngreso')
            ->first();
    }

    public function apartarCupo(
        ProcedimientoCupo $cupo
    ): JsonResponse {
        $resultado = DB::transaction(
            function () use ($cupo) {
                $cupoBloqueado = ProcedimientoCupo::query()
                    ->lockForUpdate()
                    ->findOrFail($cupo->id);

                if (
                    $cupoBloqueado->estado !==
                    ProcedimientoCupo::ESTADO_DISPONIBLE
                ) {
                    throw ValidationException::withMessages([
                        'cupo' =>
                        'El cupo ya no está disponible.',
                    ]);
                }

                if ($cupoBloqueado->cita()->exists()) {
                    throw ValidationException::withMessages([
                        'cupo' =>
                        'Este cupo ya tiene una cita registrada.',
                    ]);
                }

                $bloqueoVigente =
                    !empty($cupoBloqueado->bloqueo_token) &&
                    !empty($cupoBloqueado->bloqueado_hasta) &&
                    $cupoBloqueado->bloqueado_hasta->isFuture();

                if ($bloqueoVigente) {
                    throw ValidationException::withMessages([
                        'cupo' =>
                        'Otro usuario está registrando un paciente en este cupo.',
                    ]);
                }

                $token = (string) Str::uuid();
                $bloqueadoHasta = now()->addMinutes(5);

                $cupoBloqueado->update([
                    'bloqueo_token' => $token,
                    'bloqueado_hasta' => $bloqueadoHasta,
                ]);

                return [
                    'token' => $token,
                    'bloqueado_hasta' =>
                    $bloqueadoHasta->toIso8601String(),
                ];
            }
        );

        return response()->json([
            'success' => true,
            'message' =>
            'El cupo fue apartado temporalmente.',
            'bloqueo_token' => $resultado['token'],
            'bloqueado_hasta' =>
            $resultado['bloqueado_hasta'],
        ]);
    }
    public function liberarCupo(
        Request $request,
        ProcedimientoCupo $cupo
    ): JsonResponse {
        $datos = $request->validate([
            'bloqueo_token' => [
                'required',
                'string',
                'max:64',
            ],
        ]);

        DB::transaction(
            function () use ($cupo, $datos) {
                $cupoBloqueado = ProcedimientoCupo::query()
                    ->lockForUpdate()
                    ->findOrFail($cupo->id);

                if (
                    hash_equals(
                        (string) $cupoBloqueado->bloqueo_token,
                        (string) $datos['bloqueo_token']
                    )
                ) {
                    $cupoBloqueado->update([
                        'bloqueo_token' => null,
                        'bloqueado_hasta' => null,
                    ]);
                }
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'El cupo fue liberado.',
        ]);
    }
}
