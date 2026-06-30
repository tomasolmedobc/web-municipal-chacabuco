<?php

use Illuminate\Support\Facades\Route;

/* Controllers */
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GobiernoAbiertoController;
use App\Http\Controllers\LicitacionController;
use App\Http\Controllers\AccesoMunicipalController;
use App\Http\Controllers\TramitesServiciosController;
use App\Http\Controllers\ExpedienteConsultaController;
use App\Http\Controllers\HabilitacionesController;
use App\Http\Controllers\InfraccionesController;
use App\Http\Controllers\ReclamosController;
use App\Http\Controllers\OmicController;
use App\Http\Controllers\TurismoController;
use App\Http\Controllers\BaileEgresadosController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TelefonoUtilController;

/* Admin Controllers */
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\NoticiaAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\SistemaController;
use App\Http\Controllers\Admin\LicitacionAdminController;
use App\Http\Controllers\Admin\HabilitacionAdminController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PublicAccessButtonController;
use App\Http\Controllers\Admin\TurismoAdminController;
use App\Http\Controllers\Admin\LocalidadAdminController;
use App\Http\Controllers\Admin\BaileUsuariosAdminController;
use App\Http\Controllers\Admin\BaileAsientosAdminController;
use App\Http\Controllers\Admin\BaileReservasAdminController;
use App\Http\Controllers\Admin\PopupAdminController;
use App\Http\Controllers\Admin\TelefonoUtilAdminController;

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

Route::get('/gobierno-abierto/gastos-recursos-balance', [LicitacionController::class, 'gastosRecursosBalance'])
    ->name('gastos-recursos-balance.index');

Route::get('/gobierno-abierto/botones', [LicitacionController::class, 'botones'])
    ->name('gobierno-abierto.botones.index');

Route::get('/gobierno-abierto/accesos/{licitacion}', [GobiernoAbiertoController::class, 'showAcceso'])
    ->name('gobierno-abierto.accesos.show');

Route::get('/gobierno-abierto/proveedores', function () {
    return view('gobierno-abierto.proveedores.index');
})->name('proveedores.index');

Route::get('/tramites-y-servicios', [TramitesServiciosController::class, 'index'])
    ->name('tramites-servicios.index');

Route::get('/tramites-y-servicios/salud-mental', [TramitesServiciosController::class, 'saludMental'])
    ->name('tramites-servicios.salud-mental');

Route::get('/tramites-y-servicios/expedientes', [ExpedienteConsultaController::class, 'index'])
    ->name('expedientes.index');

Route::post('/tramites-y-servicios/expedientes', [ExpedienteConsultaController::class, 'consultar'])
    ->name('expedientes.consultar');

Route::get('/tramites-y-servicios/habilitaciones', [HabilitacionesController::class, 'index'])
    ->name('habilitaciones.index');

Route::get('/tramites-y-servicios/infracciones', [InfraccionesController::class, 'index'])
    ->name('infracciones.index');

Route::post('/tramites-y-servicios/infracciones', [InfraccionesController::class, 'consultar'])
    ->name('infracciones.consultar');

Route::get('/tramites-y-servicios/infracciones/cedula/{falta}', [InfraccionesController::class, 'cedula'])
    ->whereNumber('falta')
    ->name('infracciones.cedula');

Route::post('/tramites-y-servicios/infracciones/libre-deuda', [InfraccionesController::class, 'libreDeuda'])
    ->name('infracciones.libre-deuda');

Route::get('/tramites-y-servicios/reclamos', [ReclamosController::class, 'index'])
    ->name('reclamos.index');

Route::post('/tramites-y-servicios/reclamos', [ReclamosController::class, 'store'])
    ->name('reclamos.store');

Route::post('/tramites-y-servicios/reclamos/consulta', [ReclamosController::class, 'consultar'])
    ->name('reclamos.consultar');

Route::get('/tramites-y-servicios/omic', [OmicController::class, 'index'])
    ->name('omic.index');

Route::post('/tramites-y-servicios/omic', [OmicController::class, 'store'])
    ->name('omic.store');

Route::get('/turismo/eventos-proximos', [TurismoController::class, 'eventosProximos'])
    ->name('turismo.eventos-proximos');

Route::get('/turismo', [TurismoController::class, 'index'])
    ->name('turismo.index');

Route::get('/turismo/{localidad}/{item}', [TurismoController::class, 'showItem'])
    ->whereNumber('item')
    ->name('turismo.show.item');

Route::get('/turismo/{localidad}', [TurismoController::class, 'show'])
    ->name('turismo.show');

Route::prefix('baile-egresados')->name('baile-egresados.')->group(function () {
    Route::get('/', [BaileEgresadosController::class, 'index'])->name('index');
    Route::get('/reservar', [BaileEgresadosController::class, 'reservarForm'])->name('reservar');
    Route::post('/reservar', [BaileEgresadosController::class, 'guardarReserva'])->name('guardar');
    Route::get('/confirmacion', [BaileEgresadosController::class, 'confirmacion'])->name('confirmacion');
    Route::get('/consultar', [BaileEgresadosController::class, 'consultarForm'])->name('consultar');
    Route::post('/consultar', [BaileEgresadosController::class, 'consultar'])->name('consultar.post');
});

Route::get('/telefonos-utiles', [TelefonoUtilController::class, 'index'])
    ->name('telefonos-utiles.index');

Route::post('/chatbot', [ChatbotController::class, 'responder'])
    ->name('chatbot.responder');

Route::get('/acceso-municipal', [AccesoMunicipalController::class, 'show'])
    ->name('acceso-municipal.index');

Route::post('/acceso-municipal', [AccesoMunicipalController::class, 'authenticate'])
    ->middleware('throttle:5,1')
    ->name('acceso-municipal.authenticate');

Route::post('/acceso-municipal/salir', [AccesoMunicipalController::class, 'logout'])
    ->name('acceso-municipal.logout');


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/acceso-interno', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/acceso-interno', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
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
        | Gobierno Abierto
        |--------------------------------------------------------------------------
        */

        Route::get('/gobierno-abierto', [LicitacionAdminController::class, 'index'])
            ->name('admin.gobierno-abierto.index');

        Route::get('/gobierno-abierto/crear', [LicitacionAdminController::class, 'create'])
            ->name('admin.gobierno-abierto.create');

        Route::post('/gobierno-abierto', [LicitacionAdminController::class, 'store'])
            ->name('admin.gobierno-abierto.store');

        Route::get('/gobierno-abierto/{licitacion}/editar', [LicitacionAdminController::class, 'edit'])
            ->name('admin.gobierno-abierto.edit');

        Route::put('/gobierno-abierto/{licitacion}', [LicitacionAdminController::class, 'update'])
            ->name('admin.gobierno-abierto.update');

        Route::delete('/gobierno-abierto/{licitacion}', [LicitacionAdminController::class, 'destroy'])
            ->name('admin.gobierno-abierto.destroy');

        Route::delete('/gobierno-abierto/archivos/{archivo}', [LicitacionAdminController::class, 'destroyArchivo'])
            ->name('admin.gobierno-abierto.archivos.destroy');

        Route::get('/licitaciones', function () {
            return redirect()->route('admin.gobierno-abierto.index', ['categoria' => 'licitaciones']);
        })->name('admin.licitaciones.index');

        Route::get('/licitaciones/crear', function () {
            return redirect()->route('admin.gobierno-abierto.create', ['categoria' => 'licitaciones']);
        })->name('admin.licitaciones.create');

        Route::post('/licitaciones', [LicitacionAdminController::class, 'store'])
            ->name('admin.licitaciones.store');

        Route::get('/licitaciones/{licitacion}/editar', [LicitacionAdminController::class, 'edit'])
            ->name('admin.licitaciones.edit');

        Route::put('/licitaciones/{licitacion}', [LicitacionAdminController::class, 'update'])
            ->name('admin.licitaciones.update');

        Route::delete('/licitaciones/{licitacion}', [LicitacionAdminController::class, 'destroy'])
            ->name('admin.licitaciones.destroy');

        /*
        |--------------------------------------------------------------------------
        | Habilitaciones
        |--------------------------------------------------------------------------
        */

        Route::get('/habilitaciones', [HabilitacionAdminController::class, 'index'])
            ->name('admin.habilitaciones.index');

        Route::get('/habilitaciones/crear', [HabilitacionAdminController::class, 'create'])
            ->name('admin.habilitaciones.create');

        Route::post('/habilitaciones', [HabilitacionAdminController::class, 'store'])
            ->name('admin.habilitaciones.store');

        Route::get('/habilitaciones/{habilitacion}/editar', [HabilitacionAdminController::class, 'edit'])
            ->name('admin.habilitaciones.edit');

        Route::put('/habilitaciones/{habilitacion}', [HabilitacionAdminController::class, 'update'])
            ->name('admin.habilitaciones.update');

        Route::delete('/habilitaciones/{habilitacion}', [HabilitacionAdminController::class, 'destroy'])
            ->name('admin.habilitaciones.destroy');


        /*
        |--------------------------------------------------------------------------
        | Turismo
        |--------------------------------------------------------------------------
        */

        Route::prefix('baile-egresados')->name('admin.baile.')->group(function () {
            Route::get('/usuarios', [BaileUsuariosAdminController::class, 'index'])->name('usuarios.index');
            Route::get('/usuarios/crear', [BaileUsuariosAdminController::class, 'create'])->name('usuarios.create');
            Route::post('/usuarios', [BaileUsuariosAdminController::class, 'store'])->name('usuarios.store');
            Route::get('/usuarios/{usuario}/editar', [BaileUsuariosAdminController::class, 'edit'])->name('usuarios.edit');
            Route::put('/usuarios/{usuario}', [BaileUsuariosAdminController::class, 'update'])->name('usuarios.update');
            Route::delete('/usuarios/{usuario}', [BaileUsuariosAdminController::class, 'destroy'])->name('usuarios.destroy');

            Route::get('/asientos', [BaileAsientosAdminController::class, 'index'])->name('asientos.index');
            Route::get('/asientos/crear', [BaileAsientosAdminController::class, 'create'])->name('asientos.create');
            Route::post('/asientos', [BaileAsientosAdminController::class, 'store'])->name('asientos.store');
            Route::get('/asientos/{asiento}/editar', [BaileAsientosAdminController::class, 'edit'])->name('asientos.edit');
            Route::put('/asientos/{asiento}', [BaileAsientosAdminController::class, 'update'])->name('asientos.update');
            Route::delete('/asientos/{asiento}', [BaileAsientosAdminController::class, 'destroy'])->name('asientos.destroy');

            Route::get('/reservas', [BaileReservasAdminController::class, 'index'])->name('reservas.index');
            Route::patch('/reservas/{reserva}/pago', [BaileReservasAdminController::class, 'togglePago'])->name('reservas.pago');
            Route::delete('/reservas/{reserva}', [BaileReservasAdminController::class, 'destroy'])->name('reservas.destroy');
        });

        Route::get('/turismo/localidades', [LocalidadAdminController::class, 'index'])
            ->name('admin.turismo.localidades.index');

        Route::get('/turismo/localidades/{localidad}/editar', [LocalidadAdminController::class, 'edit'])
            ->name('admin.turismo.localidades.edit');

        Route::put('/turismo/localidades/{localidad}', [LocalidadAdminController::class, 'update'])
            ->name('admin.turismo.localidades.update');

        Route::get('/turismo', [TurismoAdminController::class, 'index'])
            ->name('admin.turismo.index');

        Route::get('/turismo/crear', [TurismoAdminController::class, 'create'])
            ->name('admin.turismo.create');

        Route::post('/turismo', [TurismoAdminController::class, 'store'])
            ->name('admin.turismo.store');

        Route::get('/turismo/{turismo}/editar', [TurismoAdminController::class, 'edit'])
            ->name('admin.turismo.edit');

        Route::put('/turismo/{turismo}', [TurismoAdminController::class, 'update'])
            ->name('admin.turismo.update');

        Route::delete('/turismo/{turismo}', [TurismoAdminController::class, 'destroy'])
            ->name('admin.turismo.destroy');

        Route::delete('/turismo/{turismo}/galeria/{imagen}', [TurismoAdminController::class, 'destroyImagen'])
            ->name('admin.turismo.galeria.destroy');

        Route::patch('/turismo/{turismo}/galeria/{imagen}/header', [TurismoAdminController::class, 'setHeader'])
            ->name('admin.turismo.galeria.header');

        Route::delete('/turismo/{turismo}/archivos/{archivo}', [TurismoAdminController::class, 'destroyArchivo'])
            ->name('admin.turismo.archivos.destroy');


        /*
        |--------------------------------------------------------------------------
        | Teléfonos Útiles
        |--------------------------------------------------------------------------
        */

        Route::get('/telefonos-utiles', [TelefonoUtilAdminController::class, 'index'])
            ->name('admin.telefonos-utiles.index');

        Route::get('/telefonos-utiles/crear', [TelefonoUtilAdminController::class, 'create'])
            ->name('admin.telefonos-utiles.create');

        Route::post('/telefonos-utiles', [TelefonoUtilAdminController::class, 'store'])
            ->name('admin.telefonos-utiles.store');

        Route::get('/telefonos-utiles/{telefonoUtil}/editar', [TelefonoUtilAdminController::class, 'edit'])
            ->name('admin.telefonos-utiles.edit');

        Route::put('/telefonos-utiles/{telefonoUtil}', [TelefonoUtilAdminController::class, 'update'])
            ->name('admin.telefonos-utiles.update');

        Route::delete('/telefonos-utiles/{telefonoUtil}', [TelefonoUtilAdminController::class, 'destroy'])
            ->name('admin.telefonos-utiles.destroy');

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
            ->except(['show'])
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

        Route::get('/popup', [PopupAdminController::class, 'index'])
            ->name('admin.popup.index');

        Route::put('/popup', [PopupAdminController::class, 'update'])
            ->name('admin.popup.update');

        Route::get('/botones-visibilidad', [PublicAccessButtonController::class, 'index'])
            ->name('admin.botones-visibilidad.index');

        Route::put('/botones-visibilidad', [PublicAccessButtonController::class, 'update'])
            ->name('admin.botones-visibilidad.update');

        Route::get('/audit-log', [AuditLogController::class, 'index'])
            ->name('admin.audit-log.index');

    });

});
