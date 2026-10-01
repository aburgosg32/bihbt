<?php

namespace App\Http\Controllers;

use App\Models\QuirofanoSolicitud;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class QuirofanoSolicitudController extends Controller
{
    /**
     * Muestra el formulario para registrar una solicitud.
     */

    public function store(Request $request)
    {
        $request->merge([
            'numero_cuenta' => trim(
                (string) $request->input('numero_cuenta')
            ),
            'medico_codigo' => strtoupper(trim(
                (string) $request->input('medico_codigo')
            )),
            'diagnostico_codigo' => strtoupper(trim(
                (string) $request->input('diagnostico_codigo')
            )),
            'procedimiento_codigo' => strtoupper(trim(
                (string) $request->input('procedimiento_codigo')
            )),
        ]);

        $datos = $request->validate(
            [
                'numero_cuenta' => [
                    'required',
                    'string',
                    'max:30',
                ],
                'servicio_solicitante' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'medico_codigo' => [
                    'required',
                    'string',
                    'max:30',
                ],
                'medico_nombre' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'medico_especialidad' => [
                    'nullable',
                    'string',
                    'max:150',
                ],
                'diagnostico_codigo' => [
                    'required',
                    'string',
                    'max:30',
                ],
                'diagnostico_nombre' => [
                    'required',
                    'string',
                    'max:500',
                ],
                'procedimiento_codigo' => [
                    'required',
                    'string',
                    'max:30',
                ],
                'procedimiento_nombre' => [
                    'required',
                    'string',
                    'max:500',
                ],
                'tipo_solicitud' => [
                    'required',
                    'in:ELECTIVA,URGENCIA,EMERGENCIA',
                ],
                'prioridad' => [
                    'required',
                    'in:BAJA,MEDIA,ALTA,CRITICA',
                ],
                'fecha_sugerida' => [
                    'nullable',
                    'date',
                    'after_or_equal:today',
                ],
                'duracion_estimada_minutos' => [
                    'required',
                    'integer',
                    'min:15',
                    'max:720',
                ],
                'tipo_anestesia' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
                'requerimientos_especiales' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'observaciones' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'numero_cuenta.required' =>
                'Debe buscar un número de cuenta.',

                'servicio_solicitante.required' =>
                'Ingrese el servicio solicitante.',

                'medico_codigo.required' =>
                'Ingrese el código del médico.',

                'medico_nombre.required' =>
                'Debe cargar los datos del médico.',

                'diagnostico_codigo.required' =>
                'Ingrese el código del diagnóstico.',

                'diagnostico_nombre.required' =>
                'Debe cargar el diagnóstico.',

                'procedimiento_codigo.required' =>
                'Ingrese el código del procedimiento.',

                'procedimiento_nombre.required' =>
                'Debe cargar el procedimiento u operación.',

                'fecha_sugerida.after_or_equal' =>
                'La fecha sugerida no puede ser anterior a hoy.',

                'duracion_estimada_minutos.required' =>
                'Ingrese la duración estimada de la operación.',

                'duracion_estimada_minutos.min' =>
                'La duración mínima es de 15 minutos.',

                'duracion_estimada_minutos.max' =>
                'La duración no puede superar las 12 horas.',
            ]
        );

        /*
     * Volvemos a consultar el paciente en SIGH.
     * No confiamos en datos ocultos del formulario.
     */
        $paciente = DB::connection('sigh')
            ->table('vw_radiologia_cuenta_atencion')
            ->where(
                'IdCuentaAtencion',
                $datos['numero_cuenta']
            )
            ->orderByDesc('FechaIngreso')
            ->first();

        if (!$paciente) {
            throw ValidationException::withMessages([
                'numero_cuenta' =>
                'La cuenta ingresada ya no fue encontrada en SIGH.',
            ]);
        }

        $fechaNacimiento = $this->formatearFecha(
            $paciente->FechaNacimiento ?? null
        );

        $fechaIngreso = null;

        if (!empty($paciente->FechaIngreso)) {
            try {
                $fechaIngreso = Carbon::parse(
                    $paciente->FechaIngreso
                )->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $fechaIngreso = null;
            }
        }

        /*
     * Evitamos solicitudes activas duplicadas para la misma
     * cuenta y el mismo procedimiento.
     */
        $duplicada = QuirofanoSolicitud::query()
            ->activas()
            ->where('numero_cuenta', $datos['numero_cuenta'])
            ->where(
                'procedimiento_codigo',
                $datos['procedimiento_codigo']
            )
            ->exists();

        if ($duplicada) {
            throw ValidationException::withMessages([
                'numero_cuenta' =>
                'Esta cuenta ya tiene una solicitud activa para el mismo procedimiento.',
            ]);
        }

        $solicitud = DB::transaction(function () use (
            $datos,
            $paciente,
            $fechaNacimiento,
            $fechaIngreso
        ) {
            return QuirofanoSolicitud::create([
                'usuario_solicitante_id' => Auth::id(),

                'servicio_solicitante' =>
                trim($datos['servicio_solicitante']),

                'numero_cuenta' =>
                (string) $paciente->IdCuentaAtencion,

                'id_paciente_sigh' =>
                $paciente->IdPaciente ?? null,

                'historia_clinica' =>
                $paciente->NroHistoriaClinica ?? null,

                'paciente' =>
                $paciente->Paciente,

                'dni' =>
                $paciente->NroDocumento ?? null,

                'sexo' =>
                $paciente->Sexo ?? null,

                'fecha_nacimiento' =>
                $fechaNacimiento,

                'edad' =>
                is_numeric($paciente->Edad ?? null)
                    ? (int) $paciente->Edad
                    : null,

                'tipo_seguro' =>
                $paciente->FF ?? null,

                'servicio_actual' =>
                $paciente->NombreServicio ?? null,

                'cama' =>
                $paciente->Cama ?? null,

                'fecha_ingreso' =>
                $fechaIngreso,

                'medico_codigo' =>
                $datos['medico_codigo'],

                'medico_nombre' =>
                trim($datos['medico_nombre']),

                'medico_especialidad' =>
                $datos['medico_especialidad'] ?? null,

                'diagnostico_codigo' =>
                $datos['diagnostico_codigo'],

                'diagnostico_nombre' =>
                trim($datos['diagnostico_nombre']),

                'procedimiento_codigo' =>
                $datos['procedimiento_codigo'],

                'procedimiento_nombre' =>
                trim($datos['procedimiento_nombre']),

                'tipo_solicitud' =>
                $datos['tipo_solicitud'],

                'prioridad' =>
                $datos['prioridad'],

                'fecha_sugerida' =>
                $datos['fecha_sugerida'] ?? null,

                'duracion_estimada_minutos' =>
                $datos['duracion_estimada_minutos'] ?? null,

                'tipo_anestesia' =>
                $datos['tipo_anestesia'] ?? null,

                'requerimientos_especiales' =>
                $datos['requerimientos_especiales'] ?? null,

                'observaciones' =>
                $datos['observaciones'] ?? null,

                'estado' => 'ENVIADA',
                'fecha_envio' => now(),
            ]);
        });

        return redirect()
            ->route('quirofano.solicitudes.create')
            ->with(
                'success',
                'Solicitud quirúrgica N.° ' .
                    $solicitud->id .
                    ' enviada correctamente.'
            );
    }
    public function index(Request $request)
    {
        $estados = [
            'ENVIADA',
            'EN_REVISION',
            'OBSERVADA',
            'APROBADA',
            'PROGRAMADA',
            'CANCELADA',
            'RECHAZADA',
            'FINALIZADA',
        ];

        $tipos = [
            'ELECTIVA',
            'URGENCIA',
            'EMERGENCIA',
        ];

        $prioridades = [
            'BAJA',
            'MEDIA',
            'ALTA',
            'CRITICA',
        ];

        $consulta = QuirofanoSolicitud::query()
            ->with('usuarioSolicitante');

        /*
     * Búsqueda general.
     */
        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));

            $consulta->where(function ($query) use ($buscar) {
                $query
                    ->where(
                        'numero_cuenta',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhere(
                        'historia_clinica',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhere(
                        'paciente',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhere(
                        'medico_nombre',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhere(
                        'procedimiento_nombre',
                        'like',
                        "%{$buscar}%"
                    );
            });
        }

        if ($request->filled('servicio')) {
            $consulta->where(
                'servicio_solicitante',
                $request->input('servicio')
            );
        }

        if ($request->filled('estado')) {
            $consulta->where(
                'estado',
                $request->input('estado')
            );
        }

        if ($request->filled('tipo_solicitud')) {
            $consulta->where(
                'tipo_solicitud',
                $request->input('tipo_solicitud')
            );
        }

        if ($request->filled('prioridad')) {
            $consulta->where(
                'prioridad',
                $request->input('prioridad')
            );
        }

        if ($request->filled('fecha_desde')) {
            $consulta->whereDate(
                'fecha_envio',
                '>=',
                $request->input('fecha_desde')
            );
        }

        if ($request->filled('fecha_hasta')) {
            $consulta->whereDate(
                'fecha_envio',
                '<=',
                $request->input('fecha_hasta')
            );
        }

        $solicitudes = $consulta
            ->orderByDesc('fecha_envio')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $servicios = QuirofanoSolicitud::query()
            ->whereNotNull('servicio_solicitante')
            ->where('servicio_solicitante', '<>', '')
            ->distinct()
            ->orderBy('servicio_solicitante')
            ->pluck('servicio_solicitante');

        $resumen = [
            'total' => QuirofanoSolicitud::count(),

            'enviadas' => QuirofanoSolicitud::whereIn(
                'estado',
                ['ENVIADA', 'EN_REVISION']
            )->count(),

            'aprobadas' => QuirofanoSolicitud::where(
                'estado',
                'APROBADA'
            )->count(),

            'programadas' => QuirofanoSolicitud::where(
                'estado',
                'PROGRAMADA'
            )->count(),
        ];

        return view(
            'quirofano.solicitudes.index',
            compact(
                'solicitudes',
                'servicios',
                'estados',
                'tipos',
                'prioridades',
                'resumen'
            )
        );
    }
    public function create()
    {
        return view('quirofano.solicitudes.create');
    }

    /**
     * Busca los datos del paciente en SIGH mediante
     * su número de cuenta.
     */
    public function buscarCuenta(
        string $cuenta
    ): JsonResponse {
        try {
            $cuenta = trim($cuenta);

            if ($cuenta === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingrese el número de cuenta.',
                ], 422);
            }

            $paciente = DB::connection('sigh')
                ->table('vw_radiologia_cuenta_atencion')
                ->where('IdCuentaAtencion', $cuenta)
                ->orderByDesc('FechaIngreso')
                ->first();

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'No se encontró información para la cuenta ingresada.',
                ], 404);
            }

            $fechaNacimiento = $this->formatearFecha(
                $paciente->FechaNacimiento ?? null
            );

            $fechaIngreso = $this->formatearFechaHora(
                $paciente->FechaIngreso ?? null
            );

            return response()->json([
                'success' => true,

                'data' => [
                    'numero_cuenta' =>
                    (string) ($paciente->IdCuentaAtencion ?? ''),

                    'id_paciente_sigh' =>
                    $paciente->IdPaciente ?? null,

                    'historia_clinica' =>
                    $paciente->NroHistoriaClinica ?? '',

                    'paciente' =>
                    $paciente->Paciente ?? '',

                    /*
                     * Estos campos se devolverán cuando existan
                     * en la vista SQL Server.
                     */
                    'dni' =>
                    $paciente->NroDocumento ?? null,

                    'telefono' =>
                    $paciente->TelPaciente ?? null,

                    'sexo' =>
                    $paciente->Sexo ?? null,

                    'fecha_nacimiento' =>
                    $fechaNacimiento,

                    'edad' =>
                    $paciente->Edad ?? null,

                    'tipo_edad' =>
                    $paciente->TipoEdad ?? null,

                    'fecha_ingreso' =>
                    $fechaIngreso,

                    'hora_ingreso' =>
                    $paciente->HoraIngreso ?? null,

                    'tipo_servicio' =>
                    $paciente->TipoServicio ?? null,

                    'servicio_actual' =>
                    $paciente->NombreServicio ?? null,

                    'tipo_seguro' =>
                    $paciente->FF ?? null,

                    'cama' =>
                    $paciente->Cama ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                'No fue posible consultar la información en SIGH.',
            ], 500);
        }
    }

    /**
     * Convierte cualquier fecha válida al formato YYYY-MM-DD.
     */
    private function formatearFecha(
        mixed $fecha
    ): ?string {
        if (empty($fecha)) {
            return null;
        }

        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Convierte una fecha y hora al formato usado
     * por los campos datetime-local.
     */
    private function formatearFechaHora(
        mixed $fecha
    ): ?string {
        if (empty($fecha)) {
            return null;
        }

        try {
            return Carbon::parse($fecha)
                ->format('Y-m-d\TH:i');
        } catch (\Throwable $e) {
            return null;
        }
    }
    public function buscarMedico(
        string $dni
    ): JsonResponse {
        try {
            $dni = trim($dni);

            if ($dni === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingrese el DNI del médico.',
                ], 422);
            }

            $medico = DB::connection('sigh')
                ->table('vw_quirofano_medicos')
                ->where('CodigoMedico', $dni)
                ->first();

            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'No se encontró un médico con el DNI ingresado.',
                ], 404);
            }

            return response()->json([
                'success' => true,

                'data' => [
                    'id_empleado' =>
                    $medico->IdEmpleado ?? null,

                    'codigo' =>
                    $medico->CodigoMedico ?? '',

                    'nombre' =>
                    $medico->Medico ?? '',

                    'especialidad' =>
                    $medico->Especialidad ?? '',
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                'No fue posible consultar los datos del médico.',
            ], 500);
        }
    }
    public function buscarDiagnostico(
        string $codigo
    ): JsonResponse {
        try {
            $codigo = strtoupper(trim($codigo));

            if ($codigo === '') {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Ingrese el código CIE-10.',
                ], 422);
            }

            $diagnostico = DB::connection('sigh')
                ->table('vw_quirofano_diagnosticos')
                ->where('CodigoCIE10', $codigo)
                ->first();

            if (!$diagnostico) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'No se encontró el código CIE-10 ingresado.',
                ], 404);
            }

            return response()->json([
                'success' => true,

                'data' => [
                    'codigo' =>
                    $diagnostico->CodigoCIE10,

                    'diagnostico' =>
                    $diagnostico->Diagnostico,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                'No fue posible consultar el diagnóstico en SIGH.',
            ], 500);
        }
    }
    public function buscarProcedimiento(
        string $codigo
    ): JsonResponse {
        try {
            $codigo = strtoupper(trim($codigo));

            if ($codigo === '') {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Ingrese el código del procedimiento.',
                ], 422);
            }

            $procedimiento = DB::connection('sigh')
                ->table('vw_quirofano_procedimientos')
                ->where(
                    'CodigoProcedimiento',
                    $codigo
                )
                ->first();

            if (!$procedimiento) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'No se encontró el procedimiento ingresado.',
                ], 404);
            }

            return response()->json([
                'success' => true,

                'data' => [
                    'codigo' =>
                    $procedimiento->CodigoProcedimiento,

                    'procedimiento' =>
                    $procedimiento->Procedimiento,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                'No fue posible consultar el procedimiento en SIGH.',
            ], 500);
        }
    }
}
