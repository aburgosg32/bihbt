<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumenOperativoController;
use App\Http\Controllers\EmergenciaController;
use App\Http\Controllers\HospitalizacionController;
use App\Http\Controllers\RadiologiaController;
use App\Http\Controllers\Camas\DepartamentoController;
use App\Http\Controllers\Camas\ServicioController;
use App\Http\Controllers\Camas\HabitacionController;
use App\Http\Controllers\Camas\CamaController;
use App\Http\Controllers\Camas\CensoCamaController;
use App\Http\Controllers\Camas\PacienteCamaController;
use App\Http\Controllers\ProcedimientoServicioController;
use App\Http\Controllers\ProcedimientoController;
use App\Http\Controllers\ProcedimientoMedicoController;
use App\Http\Controllers\ProcedimientoProgramacionController;
use App\Http\Controllers\ProcedimientoCupoController;
use App\Http\Controllers\ProcedimientoCitaController;
use App\Http\Controllers\ProcedimientoReporteController;
use App\Http\Controllers\QuirofanoSalaController;
use App\Http\Controllers\QuirofanoSolicitudController;
use App\Http\Controllers\QuirofanoProgramacionController;
use App\Http\Controllers\QuirofanoEstadoSalaController;
use App\Http\Controllers\QuirofanoReporteController;
use App\Http\Controllers\SegurosDisponibilidadController;
use App\Http\Controllers\SegurosImpresionFuaController;
use App\Http\Controllers\Seguridad\UsuarioController;
use App\Http\Controllers\SegurosReprogramacionesController;
use App\Http\Controllers\SegurosCodigoPrestacionController;
use App\Http\Controllers\SegurosSeguimientoController;
use App\Http\Controllers\SegurosCuposController;
use App\Http\Controllers\SegurosPacientesController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\SegurosPanelTvController;
use App\Http\Controllers\SegurosConsultoriosController;
/*
|--------------------------------------------------------------------------
| Página de inicio
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard principal
|--------------------------------------------------------------------------
| Breeze redirige aquí después del login.
| Nosotros lo enviamos a Emergencia.
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Perfil
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Consulta Externa
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:ver-consulta'])->group(function () {

    Route::get('/consulta-externa', [ResumenOperativoController::class, 'index'])
        ->name('consulta.dashboard');

    Route::get('/consulta-externa/pacientes-consultorio', [ResumenOperativoController::class, 'pacientesConsultorio'])
        ->name('consulta.pacientes-consultorio');
});

/*
|--------------------------------------------------------------------------
| Emergencia
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/emergencia/dashboard', [EmergenciaController::class, 'dashboard'])
        ->name('emergencia.dashboard');

    Route::get('/emergencia/pacientes-activos', [EmergenciaController::class, 'pacientesActivos'])
        ->name('emergencia.pacientes-activos');

    Route::get('/emergencia/camas-servicio', [EmergenciaController::class, 'camasServicio'])
        ->name('emergencia.camas-servicio');

    Route::get('/emergencia/consumo/{cuenta}', [EmergenciaController::class, 'consumo'])
        ->name('emergencia.consumo');

    Route::get('/emergencia/farmacia/{cuenta}', [EmergenciaController::class, 'farmacia'])
        ->name('emergencia.farmacia');

    Route::get('/emergencia/diagnosticos/{idAtencion}', [EmergenciaController::class, 'diagnosticos'])
        ->name('emergencia.diagnosticos');
});

/*
|--------------------------------------------------------------------------
| Hospitalización
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/hospitalizacion/dashboard', [HospitalizacionController::class, 'dashboard'])
        ->name('hospitalizacion.dashboard');

    Route::get('/hospitalizacion/pacientes-activos', [HospitalizacionController::class, 'pacientesActivos'])
        ->name('hospitalizacion.pacientes-activos');

    Route::get('/hospitalizacion/camas-servicio', [HospitalizacionController::class, 'camasServicio'])
        ->name('hospitalizacion.camas-servicio');

    Route::get('/hospitalizacion/consumo/{cuenta}', [HospitalizacionController::class, 'consumo'])
        ->name('hospitalizacion.consumo');

    Route::get('/hospitalizacion/farmacia/{cuenta}', [HospitalizacionController::class, 'farmacia'])
        ->name('hospitalizacion.farmacia');

    Route::get('/hospitalizacion/diagnosticos/{idAtencion}', [HospitalizacionController::class, 'diagnosticos'])
        ->name('hospitalizacion.diagnosticos');
});

/*
|--------------------------------------------------------------------------
| Radiologia
|--------------------------------------------------------------------------
*/

Route::prefix('radiologia')->name('radiologia.')->group(function () {
    Route::get('/citas', [RadiologiaController::class, 'citas'])->name('citas');
    Route::get('/buscar-cuenta/{cuenta}', [RadiologiaController::class, 'buscarCuenta'])
        ->name('buscarCuenta');
    Route::post('/citas/store', [RadiologiaController::class, 'store'])->name('store');
    Route::get('/citas/{id}/ticket', [RadiologiaController::class, 'ticket'])->name('ticket');
    Route::get('/dashboard', [RadiologiaController::class, 'dashboard'])->name('dashboard');
    Route::get('/rayos-x', [RadiologiaController::class, 'rayosx'])->name('rayosx');
    Route::get('/tomografias', [RadiologiaController::class, 'tomografias'])->name('tomografias');
    Route::get('/ecografias', [RadiologiaController::class, 'ecografias'])->name('ecografias');
    Route::get(
        '/buscar-procedimiento/{codigo}',
        [RadiologiaController::class, 'buscarProcedimiento']
    )
        ->name('buscarProcedimiento');
    Route::get('/horarios-disponibles', [RadiologiaController::class, 'horariosDisponibles'])
        ->name('horariosDisponibles');
    Route::post('/rayos-x/{id}/atender', [RadiologiaController::class, 'atenderRayosx'])
        ->name('rayosx.atender');
    Route::post('/tomografias/{id}/atender', [RadiologiaController::class, 'atenderTomografia'])
        ->name('tomografias.atender');
    Route::post('/ecografias/{id}/atender', [RadiologiaController::class, 'atenderEcografia'])
        ->name('ecografias.atender');
    Route::get('/reportes', [RadiologiaController::class, 'reportes'])
        ->name('reportes');
    Route::get('/reportes/excel', [RadiologiaController::class, 'reportesExcel'])
        ->name('reportes.excel');
    Route::get('/reportes/pdf', [RadiologiaController::class, 'reportesPdf'])
        ->name('reportes.pdf');
});


/*
|--------------------------------------------------------------------------
| Camas
|--------------------------------------------------------------------------
*/

Route::prefix('camas')->name('camas.')->group(function () {
    Route::resource('departamentos', DepartamentoController::class)->except(['show']);
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::get(
        'departamentos/{departamento}/servicios',
        [HabitacionController::class, 'serviciosPorDepartamento']
    )->name('departamentos.servicios');
    Route::resource('habitaciones', HabitacionController::class)->except(['show']);
    Route::get(
        'servicios/{servicio}/habitaciones',
        [CamaController::class, 'habitacionesPorServicio']
    )->name('servicios.habitaciones');
    Route::resource('camas', CamaController::class)->except(['show']);
    Route::get(
        'censo',
        [CensoCamaController::class, 'index']
    )->name('censo');
    Route::post(
        'censo/buscar-paciente',
        [CensoCamaController::class, 'buscarPaciente']
    )->name('censo.buscar-paciente');
    Route::post(
        'censo/asignar',
        [CensoCamaController::class, 'asignar']
    )->name('censo.asignar');

    Route::put(
        'censo/ocupaciones/{ocupacion}/alta',
        [CensoCamaController::class, 'alta']
    )->name('censo.alta');
    Route::get(
        'pacientes',
        [PacienteCamaController::class, 'index']
    )->name('pacientes.index');
    Route::get(
        'pacientes/pdf',
        [PacienteCamaController::class, 'pdf']
    )->name('pacientes.pdf');
});

/*
|--------------------------------------------------------------------------
| Procedimientos
|--------------------------------------------------------------------------
*/

Route::prefix('procedimientos')
    ->name('procedimientos.')
    ->group(function () {

        Route::get(
            '/servicios',
            [ProcedimientoServicioController::class, 'index']
        )->name('servicios.index');

        Route::post(
            '/servicios',
            [ProcedimientoServicioController::class, 'store']
        )->name('servicios.store');

        Route::put(
            '/servicios/{servicio}',
            [ProcedimientoServicioController::class, 'update']
        )->name('servicios.update');

        Route::patch(
            '/servicios/{servicio}/estado',
            [ProcedimientoServicioController::class, 'cambiarEstado']
        )->name('servicios.estado');

        Route::get(
            '/tipos',
            [ProcedimientoController::class, 'index']
        )->name('tipos.index');

        Route::post(
            '/tipos',
            [ProcedimientoController::class, 'store']
        )->name('tipos.store');

        Route::put(
            '/tipos/{procedimiento}',
            [ProcedimientoController::class, 'update']
        )->name('tipos.update');

        Route::patch(
            '/tipos/{procedimiento}/estado',
            [ProcedimientoController::class, 'cambiarEstado']
        )->name('tipos.estado');
        // Médicos
        Route::get(
            '/medicos',
            [ProcedimientoMedicoController::class, 'index']
        )->name('medicos.index');

        Route::post(
            '/medicos',
            [ProcedimientoMedicoController::class, 'store']
        )->name('medicos.store');

        Route::put(
            '/medicos/{medico}',
            [ProcedimientoMedicoController::class, 'update']
        )->name('medicos.update');

        Route::patch(
            '/medicos/{medico}/estado',
            [ProcedimientoMedicoController::class, 'cambiarEstado']
        )->name('medicos.estado');

        // Programaciones
        Route::get(
            '/programaciones',
            [ProcedimientoProgramacionController::class, 'index']
        )->name('programaciones.index');

        Route::post(
            '/programaciones',
            [ProcedimientoProgramacionController::class, 'store']
        )->name('programaciones.store');

        Route::put(
            '/programaciones/{programacion}',
            [ProcedimientoProgramacionController::class, 'update']
        )->name('programaciones.update');

        Route::patch(
            '/programaciones/{programacion}/estado',
            [ProcedimientoProgramacionController::class, 'cambiarEstado']
        )->name('programaciones.estado');

        // Cupos
        Route::get(
            '/programaciones/{programacion}/cupos',
            [ProcedimientoCupoController::class, 'index']
        )->name('cupos.index');

        Route::patch(
            '/cupos/{cupo}/bloquear',
            [ProcedimientoCupoController::class, 'bloquear']
        )->name('cupos.bloquear');

        Route::patch(
            '/cupos/{cupo}/habilitar',
            [ProcedimientoCupoController::class, 'habilitar']
        )->name('cupos.habilitar');
        // Citas de procedimientos
        Route::get(
            '/citas',
            [ProcedimientoCitaController::class, 'index']
        )->name('citas.index');

        Route::get(
            '/citas/fechas-disponibles',
            [ProcedimientoCitaController::class, 'fechasDisponibles']
        )->name('citas.fechas-disponibles');

        Route::get(
            '/citas/cupos-disponibles',
            [ProcedimientoCitaController::class, 'cuposDisponibles']
        )->name('citas.cupos-disponibles');
        Route::get(
            '/citas/buscar-cuenta/{cuenta}',
            [ProcedimientoCitaController::class, 'buscarCuenta']
        )->name('citas.buscar-cuenta');

        Route::post(
            '/citas',
            [ProcedimientoCitaController::class, 'store']
        )->name('citas.store');
        Route::patch(
            '/citas/{cita}/reprogramar',
            [ProcedimientoCitaController::class, 'reprogramar']
        )->name('citas.reprogramar');
        Route::get(
            '/citas/{cita}/ticket',
            [ProcedimientoCitaController::class, 'ticket']
        )->name('citas.ticket');
        // Reportes de citas
        Route::get(
            '/reportes',
            [ProcedimientoReporteController::class, 'index']
        )->name('reportes.index');

        Route::get(
            '/reportes/pdf',
            [ProcedimientoReporteController::class, 'pdf']
        )->name('reportes.pdf');
        Route::post(
            '/citas/cupos/{cupo}/apartar',
            [ProcedimientoCitaController::class, 'apartarCupo']
        )->name('citas.cupos.apartar');

        Route::post(
            '/citas/cupos/{cupo}/liberar',
            [ProcedimientoCitaController::class, 'liberarCupo']
        )->name('citas.cupos.liberar');
    });

/*
|--------------------------------------------------------------------------
| Seguros
|--------------------------------------------------------------------------
*/

Route::get('/seguros/disponibilidad', [SegurosDisponibilidadController::class, 'index'])
    ->name('seguros.disponibilidad');
Route::get(
    '/seguros/impresion-fua',
    [SegurosImpresionFuaController::class, 'index']
)->name('seguros.impresion-fua.index');

Route::post(
    '/seguros/impresion-fua/{idServicio}/cambiar',
    [SegurosImpresionFuaController::class, 'cambiar']
)->whereNumber('idServicio')
    ->name('seguros.impresion-fua.cambiar');

Route::post(
    '/seguros/impresion-fua/restablecer-todos',
    [SegurosImpresionFuaController::class, 'restablecerTodos']
)->name('seguros.impresion-fua.restablecer-todos');
Route::middleware(['auth'])
    ->prefix('seguros')
    ->name('seguros.')
    ->group(function () {
        Route::get('/reprogramaciones', [SegurosReprogramacionesController::class, 'index'])
            ->name('reprogramaciones.index');

        Route::get('/reprogramaciones/programaciones', [SegurosReprogramacionesController::class, 'programaciones'])
            ->name('reprogramaciones.programaciones');

        Route::get('/reprogramaciones/horas', [SegurosReprogramacionesController::class, 'horas'])
            ->name('reprogramaciones.horas');

        Route::post('/reprogramaciones', [SegurosReprogramacionesController::class, 'store'])
            ->name('reprogramaciones.store');
    });

Route::middleware(['auth'])
    ->prefix('seguros')
    ->name('seguros.')
    ->group(function () {
        Route::get(
            '/codigo-prestacion',
            [SegurosCodigoPrestacionController::class, 'index']
        )->name('codigo-prestacion.index');

        Route::post(
            '/codigo-prestacion',
            [SegurosCodigoPrestacionController::class, 'store']
        )->name('codigo-prestacion.store');
        Route::get(
            '/seguimiento',
            [SegurosSeguimientoController::class, 'index']
        )->name('seguimiento');
        Route::get(
            '/seguimiento/{cuenta}/detalle',
            [SegurosSeguimientoController::class, 'detalle']
        )->whereNumber('cuenta')
            ->name('seguimiento.detalle');
        Route::get('/cupos', [SegurosCuposController::class, 'index'])
            ->name('cupos.index');

        Route::post('/cupos', [SegurosCuposController::class, 'actualizar'])
            ->name('cupos.actualizar');
        Route::get('/pacientes', [SegurosPacientesController::class, 'index'])
            ->name('pacientes.index');

        Route::get('/pacientes/{paciente}/editar', [
            SegurosPacientesController::class,
            'edit',
        ])->whereNumber('paciente')
            ->name('pacientes.edit');

        Route::put('/pacientes/{paciente}', [
            SegurosPacientesController::class,
            'update',
        ])->whereNumber('paciente')
            ->name('pacientes.update');

        Route::get('/panel-tv', [SegurosPanelTvController::class, 'index'])
            ->name('panel-tv.index');

        Route::get('/panel-tv/datos', [SegurosPanelTvController::class, 'datos'])
            ->name('panel-tv.datos');
        Route::get('/asignar-consultorios', [
            SegurosConsultoriosController::class,
            'index',
        ])->name('consultorios.index');

        Route::post('/asignar-consultorios', [
            SegurosConsultoriosController::class,
            'store',
        ])->name('consultorios.store');
    });

/*
|--------------------------------------------------------------------------
| Quirofano
|--------------------------------------------------------------------------
*/

Route::prefix('quirofano')
    ->name('quirofano.')
    ->group(function () {
        Route::get(
            '/configuracion/salas',
            [QuirofanoSalaController::class, 'index']
        )->name('salas.index');

        Route::post(
            '/configuracion/salas',
            [QuirofanoSalaController::class, 'store']
        )->name('salas.store');

        Route::put(
            '/configuracion/salas/{sala}',
            [QuirofanoSalaController::class, 'update']
        )->name('salas.update');

        Route::patch(
            '/configuracion/salas/{sala}/operatividad',
            [QuirofanoSalaController::class, 'cambiarOperatividad']
        )->name('salas.operatividad');
        Route::get(
            '/solicitudes/nueva',
            [QuirofanoSolicitudController::class, 'create']
        )->name('solicitudes.create');

        Route::get(
            '/solicitudes/buscar-cuenta/{cuenta}',
            [QuirofanoSolicitudController::class, 'buscarCuenta']
        )->name('solicitudes.buscar-cuenta');
        Route::post(
            '/solicitudes',
            [QuirofanoSolicitudController::class, 'store']
        )->name('solicitudes.store');
        Route::get(
            '/solicitudes/buscar-medico/{dni}',
            [QuirofanoSolicitudController::class, 'buscarMedico']
        )->name('solicitudes.buscar-medico');
        Route::get(
            '/solicitudes/buscar-diagnostico/{codigo}',
            [
                QuirofanoSolicitudController::class,
                'buscarDiagnostico',
            ]
        )->name('solicitudes.buscar-diagnostico');
        Route::get(
            '/solicitudes/buscar-procedimiento/{codigo}',
            [
                QuirofanoSolicitudController::class,
                'buscarProcedimiento',
            ]
        )->name('solicitudes.buscar-procedimiento');
        Route::get(
            '/solicitudes',
            [QuirofanoSolicitudController::class, 'index']
        )->name('solicitudes.index');
        Route::get(
            '/programacion',
            [
                QuirofanoProgramacionController::class,
                'index',
            ]
        )->name('programacion.index');

        /*Route::get(
            '/programacion/sugerir/{solicitud}',
            [
                QuirofanoProgramacionController::class,
                'sugerir',
            ]
        )->name('programacion.sugerir');*/

        Route::post(
            '/programacion',
            [
                QuirofanoProgramacionController::class,
                'store',
            ]
        )->name('programacion.store');
        Route::get(
            '/estado-salas',
            [
                QuirofanoEstadoSalaController::class,
                'index',
            ]
        )->name('estado-salas.index');

        Route::patch(
            '/estado-salas/{programacion}/estado',
            [
                QuirofanoEstadoSalaController::class,
                'cambiarEstado',
            ]
        )->name('estado-salas.cambiar-estado');
        Route::get(
            '/panel-tv',
            [
                QuirofanoEstadoSalaController::class,
                'panelTv',
            ]
        )->name('panel-tv');
        Route::get(
            '/reportes',
            [QuirofanoReporteController::class, 'index']
        )->name('reportes.index');
        Route::get(
            '/reportes/excel',
            [QuirofanoReporteController::class, 'excel']
        )->name('reportes.excel');

        Route::get(
            '/reportes/pdf',
            [QuirofanoReporteController::class, 'pdf']
        )->name('reportes.pdf');
    });
/*
|--------------------------------------------------------------------------
| Seguridad: usuarios y permisos
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'can:administrar-seguridad'])
    ->prefix('seguridad')
    ->name('seguridad.')
    ->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->name('usuarios.index');

        Route::get('/usuarios/nuevo', [UsuarioController::class, 'create'])
            ->name('usuarios.create');

        Route::post('/usuarios', [UsuarioController::class, 'store'])
            ->name('usuarios.store');

        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
            ->name('usuarios.edit');

        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])
            ->name('usuarios.update');
    });
/*
|--------------------------------------------------------------------------
| Reportes y Auditoria
|--------------------------------------------------------------------------
*/

Route::prefix('reportes-auditoria')
    ->name('auditoria.')
    ->group(function () {
        Route::get('/', [AuditoriaController::class, 'index'])
            ->name('index');

        Route::get('/exportar', [AuditoriaController::class, 'exportar'])
            ->name('exportar');
    });


require __DIR__ . '/auth.php';
