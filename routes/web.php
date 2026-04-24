<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\NoticiaAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\SistemaController;

/*
|--------------------------------------------------------------------------
| Frontend público
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
Route::get('/noticias/{slug}', [NoticiaController::class, 'show'])->name('noticias.show');


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Admin general
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');


    Route::get('/perfil', [PerfilController::class, 'edit'])
        ->name('admin.perfil.edit');

    Route::put('/perfil', [PerfilController::class, 'update'])
        ->name('admin.perfil.update');
    
        /*
    |--------------------------------------------------------------------------
    | Panel de noticias - Admin y Editor
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,editor')->group(function () {
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
    });


    /*
    |--------------------------------------------------------------------------
    | Panel de usuarios - Solo Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {
        Route::resource('usuarios', UserController::class)
            ->names('admin.usuarios');

        Route::post('/usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])
            ->name('admin.usuarios.resetPassword');
        
            Route::get('/sistema', [SistemaController::class, 'index'])
            ->name('admin.sistema.index');
    });
});