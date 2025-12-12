<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ConsultasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;

// Redirección inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Perfil (cualquier usuario autenticado)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard: todos los cargos válidos
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([
        'auth',
        'verified',
        'role:Administrador TI,Doctor,Recepcionista,Personal de Facturación y Cobranza,Personal de Mantenimiento',
    ])
    ->name('dashboard');

// Rutas protegidas por cargo
Route::middleware(['auth'])->group(function () {

    // Empleados / gestión usuarios/doctores -> solo Admin TI
    Route::middleware('role:Administrador TI')->group(function () {
        Route::resource('empleados', EmpleadoController::class)->except(['show']);
        Route::get('empleados/{id}/crear-usuario', [EmpleadoController::class, 'crearUsuario'])
            ->name('empleados.crear-usuario');
        Route::post('empleados/{id}/crear-usuario', [EmpleadoController::class, 'storeUsuario'])
            ->name('empleados.store-usuario');
    });

    // Pacientes -> Recepcionista + Admin TI
    Route::middleware('role:Recepcionista,Administrador TI')->group(function () {
        Route::resource('pacientes', PacienteController::class);
        Route::put('pacientes/{id}/activate', [PacienteController::class, 'activate'])
            ->name('pacientes.activate');
    });

    // Citas -> Recepcionista + Admin TI
    Route::middleware('role:Recepcionista,Administrador TI')->group(function () {
        Route::resource('citas', CitaController::class);
        Route::post('citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
        Route::post('citas/{cita}/confirmar', [CitaController::class, 'confirmar'])->name('citas.confirmar');
        Route::post('citas/{cita}/completar', [CitaController::class, 'completar'])->name('citas.completar');
        Route::post('citas/{cita}/iniciar', [CitaController::class, 'iniciar'])->name('citas.iniciar');
        Route::get('citas-horarios-disponibles', [CitaController::class, 'horariosDisponibles'])->name('citas.horarios');
        Route::get('citas-estadisticas', [CitaController::class, 'estadisticas'])->name('citas.estadisticas');
        Route::get('citas-hoy', [CitaController::class, 'citasHoy'])->name('citas.hoy');
        Route::get('citas-calendario', [CitaController::class, 'calendario'])->name('citas.calendario');
    });

    // Pagos -> Finanzas (Personal de Facturación y Cobranza) + Admin TI
    Route::middleware('role:Personal de Facturación y Cobranza,Administrador TI')->group(function () {
        Route::resource('pagos', PagoController::class);
        Route::put('pagos/{pago}/anular', [PagoController::class, 'anular'])->name('pagos.anular');
    });

    // Consultas -> Doctor + Admin TI
    Route::middleware('role:Doctor,Administrador TI')->group(function () {
        Route::get('/consultas', [ConsultasController::class, 'index'])->name('consultas.index');
        Route::post('/consultas/{id}/finalizar', [ConsultasController::class, 'finalizarConsulta'])
            ->name('consultas.finalizar');
        Route::post('/consultas/{id}/iniciar', [ConsultasController::class, 'iniciarConsulta'])
            ->name('consultas.iniciar');
        Route::get('/consultas/{cita}/atender', [ConsultasController::class, 'create'])
            ->name('consultas.create');
        Route::post('/consultas/{cita}', [ConsultasController::class, 'store'])
            ->name('consultas.store');
    });

    // Horarios: si quieres que solo Doctor + Admin TI gestionen horarios
    Route::middleware('role:Doctor,Administrador TI')->group(function () {
        Route::get('/horarios/index', [HorarioController::class, 'index'])
            ->name('horarios.index');
    });
});

// Rutas de autenticación Breeze
require __DIR__.'/auth.php';
