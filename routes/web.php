<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumenOperativoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmergenciaController;
use App\Http\Controllers\HospitalizacionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ResumenOperativoController::class, 'index'])->name('dashboard');
});

Route::get('/emergencia/dashboard', [EmergenciaController::class, 'dashboard'])
    ->name('emergencia.dashboard');

Route::get('/emergencia/pacientes-activos', [EmergenciaController::class, 'pacientesActivos'])
    ->name('emergencia.pacientes-activos');
Route::get('/hospitalizacion/dashboard', [HospitalizacionController::class, 'dashboard'])
    ->name('hospitalizacion.dashboard');
Route::get('/hospitalizacion/diagnosticos/{idAtencion}', [HospitalizacionController::class, 'diagnosticos'])
    ->name('hospitalizacion.diagnosticos');

Route::get('/hospitalizacion/pacientes-activos', [HospitalizacionController::class, 'pacientesActivos'])
    ->name('hospitalizacion.pacientes-activos');

Route::get('/emergencia/diagnosticos/{idAtencion}', [EmergenciaController::class, 'diagnosticos'])
    ->name('emergencia.diagnosticos');
require __DIR__ . '/auth.php';
