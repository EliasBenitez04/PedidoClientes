<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SolicitudClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/solicitudes', [SolicitudClienteController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/nueva', [SolicitudClienteController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudClienteController::class, 'store'])->name('solicitudes.store');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');

    Route::redirect('/pedidos', '/solicitudes');
    Route::redirect('/pedidos/nuevo', '/solicitudes/nueva');

    Route::middleware('role:ADMIN,SUPERVISOR')->group(function () {
        // Importadores: mantener sin cambios.
        Route::get('/catalogo/importar', [CatalogoImportController::class, 'index'])->name('catalogo.importar');
        Route::post('/catalogo/importar', [CatalogoImportController::class, 'importar'])->name('catalogo.importar.post');

        Route::patch('/solicitudes/revisar-masivo', [SolicitudClienteController::class, 'revisarMasivo'])
            ->name('solicitudes.revisar-masivo');

        Route::patch('/solicitudes/{solicitud}/revisar', [SolicitudClienteController::class, 'revisar'])
            ->name('solicitudes.revisar');

        Route::patch('/solicitudes/{solicitud}/estado', [SolicitudClienteController::class, 'updateEstado'])
            ->name('solicitudes.estado');
    });

    Route::middleware('role:ADMIN')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');

        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
        Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update');
    });
});
