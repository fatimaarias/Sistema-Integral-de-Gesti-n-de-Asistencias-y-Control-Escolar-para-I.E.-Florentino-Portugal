<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\UserAdminController;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureCanManageSeccion;

// Redirección al login
Route::get('/', fn() => redirect()->route('login'));

// ================== AUTENTICACIÓN ==================
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================== QR PÚBLICO (sin auth) ==================
Route::get('/a/{token}', [AsistenciaController::class, 'marcarPorQr'])
    ->middleware('throttle:60,1')
    ->name('asistencia.qr');

// ================== RUTAS PROTEGIDAS ==================
Route::middleware('auth')->group(function () {

    Route::view('/dashboard', 'alumnos.index')->name('dashboard');

    // ================== SOLO ADMIN ==================
    Route::middleware(EnsureRole::class . ':admin')->group(function () {

        // Registro de usuarios
        Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');

        // Gestión de usuarios
        Route::get('/usuarios', [UserAdminController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/eliminar', [UserAdminController::class, 'eliminar'])->name('usuarios.eliminar');
        Route::delete('/usuarios/{user}', [UserAdminController::class, 'destroy'])->name('usuarios.destroy');
    });

    // ================== ALUMNOS ==================
Route::prefix('alumnos')->name('alumnos.')->group(function () {

    Route::get('/', [AlumnoController::class, 'index'])->name('index');
    Route::get('/seleccionar', [AlumnoController::class, 'seleccionar'])->name('seleccionar');
    Route::get('/create', [AlumnoController::class, 'create'])->name('create');
    Route::post('/', [AlumnoController::class, 'store'])->name('store');
    Route::post('/importar', [AlumnoController::class, 'importar'])->name('importar');
    Route::get('/ver/{grado}/{seccion}', [AlumnoController::class, 'ver'])->name('ver');

    // 👇 AGREGA ESTA
    Route::put('/{id}', [AlumnoController::class, 'update'])->name('update');

    Route::delete('/eliminar-multiples', [AlumnoController::class, 'destroyMany'])->name('destroyMany');
    Route::delete('/{id}', [AlumnoController::class, 'destroy'])->name('destroy');


});


    // ================== QRs ==================
    Route::get('/alumnos/credenciales', [AsistenciaController::class, 'credenciales'])
        ->name('alumnos.credenciales');

    Route::post('/alumnos/qr/generar-faltantes', [AsistenciaController::class, 'generarQRFaltantes'])
        ->name('alumnos.qr.generar');

    Route::post('/alumnos/{alumno}/qr/emitir', [AsistenciaController::class, 'emitirQR'])
        ->name('alumnos.qr.emitir');


    // ================== ASISTENCIAS ==================
    Route::prefix('asistencias')->name('asistencias.')->group(function () {

        Route::get('/', [AsistenciaController::class, 'index'])->name('index');
        Route::post('/', [AsistenciaController::class, 'store'])->name('store');

        Route::get('/seleccionar', [AsistenciaController::class, 'seleccionar'])->name('seleccionar');
        Route::get('/agregar', [AsistenciaController::class, 'seleccionarAgregar'])->name('agregarSeleccionar');

        Route::get('/ver/{grado}/{seccion}', [AsistenciaController::class, 'ver'])
            ->middleware(EnsureCanManageSeccion::class)->name('ver');

        Route::get('/ver-solo/{grado}/{seccion}', [AsistenciaController::class, 'verSolo'])
            ->middleware(EnsureCanManageSeccion::class)->name('verSolo');

        Route::get('/marcar/{grado}/{seccion}', [AsistenciaController::class, 'marcar'])
            ->middleware(EnsureCanManageSeccion::class)->name('marcar');

        Route::get('/exportar', [AsistenciaController::class, 'exportar'])->name('exportar');

        Route::get('/imprimir/{grado}/{seccion}', [AsistenciaController::class, 'imprimir'])
            ->middleware(EnsureCanManageSeccion::class)->name('imprimir');

        // Escáner QR
        Route::get('/escaner', [AsistenciaController::class, 'escaner'])->name('escaner');
    });


    // ================== ESTADÍSTICAS ==================
    Route::get('/estadisticas', [EstadisticaController::class, 'index'])
        ->name('estadisticas.index');

});

// =========================================================
//  CHATBOT BOTMAN (fuera del auth, para que cualquiera pueda usarlo)
// =========================================================
Route::match(['GET', 'POST'], '/botman', [ChatbotController::class, 'handle'])
    ->name('botman.handle');

