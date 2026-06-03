<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumenOperativoController;
use App\Http\Controllers\EmergenciaController;
use App\Http\Controllers\HospitalizacionController;

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
    return redirect()->route('emergencia.dashboard');
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

Route::middleware(['auth'])->group(function () {

    Route::get('/consulta-externa', [ResumenOperativoController::class, 'index'])
        ->name('consulta.dashboard');
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

    Route::get('/hospitalizacion/diagnosticos/{idAtencion}', [HospitalizacionController::class, 'diagnosticos'])
        ->name('hospitalizacion.diagnosticos');
});

require __DIR__ . '/auth.php';
