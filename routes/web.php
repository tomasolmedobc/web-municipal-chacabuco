<?php

use Illuminate\Support\Facades\Route;

/* Controllers */
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GobiernoAbiertoController;
use App\Http\Controllers\LicitacionController;

/* Admin Controllers */
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\NoticiaAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\SistemaController;
use App\Http\Controllers\Admin\LicitacionAdminController;

/*
|--------------------------------------------------------------------------
| Frontend público
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/noticias', [NoticiaController::class, 'index'])
    ->name('noticias.index');

Route::get('/noticias/{slug}', [NoticiaController::class, 'show'])
    ->name('noticias.show');

Route::get('/gobierno-abierto', [GobiernoAbiertoController::class, 'index'])
    ->name('gobierno-abierto.index');

Route::get('/gobierno-abierto/licitaciones', [LicitacionController::class, 'index'])
    ->name('licitaciones.index');


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/acceso-interno', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/acceso-interno', [AuthController::class, 'login'])
        ->name('login.post');

});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Panel Admin
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    Route::get('/perfil', [PerfilController::class, 'edit'])
        ->name('admin.perfil.edit');

    Route::put('/perfil', [PerfilController::class, 'update'])
        ->name('admin.perfil.update');


    /*
    |--------------------------------------------------------------------------
    | Noticias + Licitaciones
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,editor')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Noticias
        |--------------------------------------------------------------------------
        */

        Route::get('/noticias', [NoticiaAdminController::class, 'index'])
            ->name('admin.noticias.index');

        Route::get('/noticias/crear', [NoticiaAdminController::class, 'create'])
            ->name('admin.noticias.create');

        Route::post('/noticias', [NoticiaAdminController::class, 'store'])
            ->name('admin.noticias.store');

        Route::get('/noticias/{noticia}/editar', [NoticiaAdminController::class, 'edit'])
            ->name('admin.noticias.edit');

        Route::put('/noticias/{noticia}', [NoticiaAdminController::class, 'update'])
            ->name('admin.noticias.update');

        Route::patch('/noticias/{noticia}/estado', [NoticiaAdminController::class, 'toggleStatus'])
            ->name('admin.noticias.toggleStatus');

        Route::delete('/noticias/{noticia}', [NoticiaAdminController::class, 'destroy'])
            ->name('admin.noticias.destroy');

        Route::delete('/archivos/{archivo}', [NoticiaAdminController::class, 'destroyArchivo'])
            ->name('admin.noticias.archivos.destroy');


        /*
        |--------------------------------------------------------------------------
        | Licitaciones
        |--------------------------------------------------------------------------
        */

        Route::get('/licitaciones', [LicitacionAdminController::class, 'index'])
            ->name('admin.licitaciones.index');

        Route::get('/licitaciones/crear', [LicitacionAdminController::class, 'create'])
            ->name('admin.licitaciones.create');

        Route::post('/licitaciones', [LicitacionAdminController::class, 'store'])
            ->name('admin.licitaciones.store');

        Route::get('/licitaciones/{licitacion}/editar', [LicitacionAdminController::class, 'edit'])
            ->name('admin.licitaciones.edit');

        Route::put('/licitaciones/{licitacion}', [LicitacionAdminController::class, 'update'])
            ->name('admin.licitaciones.update');

        Route::delete('/licitaciones/{licitacion}', [LicitacionAdminController::class, 'destroy'])
            ->name('admin.licitaciones.destroy');

    });


    /*
    |--------------------------------------------------------------------------
    | Usuarios + Sistema
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        */

        Route::resource('usuarios', UserController::class)
            ->names('admin.usuarios');

        Route::post('/usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])
            ->name('admin.usuarios.resetPassword');


        /*
        |--------------------------------------------------------------------------
        | Sistema
        |--------------------------------------------------------------------------
        */

        Route::get('/sistema', [SistemaController::class, 'index'])
            ->name('admin.sistema.index');

        Route::put('/sistema', [SistemaController::class, 'update'])
            ->name('admin.sistema.update');

    });

});