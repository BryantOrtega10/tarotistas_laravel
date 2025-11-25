<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\BancosController;
use App\Http\Controllers\Web\EspecialidadesController;
use App\Http\Controllers\Web\HistorialPagosController;
use App\Http\Controllers\Web\PagosController;
use App\Http\Controllers\Web\PaisesController;
use App\Http\Controllers\Web\TarotistaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes(['login' => false]);

Route::get('/', function () {
    if (Auth::check()) {
        switch (strtolower(Auth::user()->role)) {
            case 'superadmin':
                return redirect(route('tarotistas.lista'));
                break;
        }
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);



Route::get("storage-link", function () {
    File::link(
        storage_path('app/public'),
        public_path('storage')
    );
});

Route::get("phpinfo", function () {
    phpinfo();
});

Route::group(['prefix' => 'tarotistas', 'middleware' => ['auth', 'user-role:superadmin']], function () {

    Route::get("/", [TarotistaController::class, 'lista'])->name("tarotistas.lista");
    Route::post("/datatable", [TarotistaController::class, 'datatableAjax'])->name("tarotistas.datatable");

    Route::get("/aprobar/{id}", [TarotistaController::class, 'mostrarAprobar'])->name("tarotistas.aprobar");
    Route::post("/aprobar/{id}", [TarotistaController::class, 'aprobar']);
    Route::post("/rechazar/{id}", [TarotistaController::class, 'rechazar'])->name("tarotistas.rechazar");

    Route::get("/editar/{id}", [TarotistaController::class, 'mostrarEditar'])->name("tarotistas.editar");
    Route::post("/editar/{id}", [TarotistaController::class, 'editar']);
});

Route::group(['prefix' => 'pagos', 'middleware' => ['auth', 'user-role:superadmin']], function () {
    Route::get("/pendientes", [PagosController::class, 'verPendientes'])->name("pagos.pendientes");
    Route::post("/pendientes/datatable", [PagosController::class, 'datatablePendientes'])->name("pagos.generar.datatable");
    Route::get("/generar/{idTarotista}", [PagosController::class, 'mostrarGenerar'])->name("pagos.generar");
    Route::post("/generar/{idTarotista}", [PagosController::class, 'generar']);
});


Route::group(['prefix' => 'historial-pagos', 'middleware' => ['auth', 'user-role:superadmin']], function () {

    Route::get("/historial", [HistorialPagosController::class, 'mostrarHistorial'])->name("historialPagos.lista");
    Route::post("/historial/datatable", [HistorialPagosController::class, 'datatableHistorial'])->name("historialPagos.datatable");
    Route::get("/historial/{idPago}", [HistorialPagosController::class, 'mostrarDetalleHistorial'])->name("historialPagos.detalle");
});

Route::group(['prefix' => 'especialidades', 'middleware' => ['auth', 'user-role:superadmin']], function () {
    Route::get("/", [EspecialidadesController::class, 'lista'])->name("especialidades.lista");
    Route::get("/crear", [EspecialidadesController::class, 'mostrarCrear'])->name("especialidades.crear");
    Route::post("/crear", [EspecialidadesController::class, 'crear']);
    Route::get("/modificar/{id}", [EspecialidadesController::class, 'mostrarModificar'])->name("especialidades.modificar");
    Route::post("/modificar/{id}", [EspecialidadesController::class, 'modificar']);
    Route::post("/eliminar/{id}", [EspecialidadesController::class, 'eliminar'])->name("especialidades.eliminar");
});

Route::group(['prefix' => 'paises', 'middleware' => ['auth', 'user-role:superadmin']], function () {
    Route::get("/", [PaisesController::class, 'lista'])->name("paises.lista");
    Route::get("/crear", [PaisesController::class, 'mostrarCrear'])->name("paises.crear");
    Route::post("/crear", [PaisesController::class, 'crear']);
    Route::get("/modificar/{id}", [PaisesController::class, 'mostrarModificar'])->name("paises.modificar");
    Route::post("/modificar/{id}", [PaisesController::class, 'modificar']);
    Route::post("/eliminar/{id}", [PaisesController::class, 'eliminar'])->name("paises.eliminar");


    Route::group(['prefix' => 'bancos', 'middleware' => ['auth', 'user-role:superadmin']], function () {
        Route::get("/{idPais}", [BancosController::class, 'lista'])->name("bancos.lista");
        Route::get("/{idPais}/crear", [BancosController::class, 'mostrarCrear'])->name("bancos.crear");
        Route::post("/{idPais}/crear", [BancosController::class, 'crear']);
        Route::get("/modificar/{id}", [BancosController::class, 'mostrarModificar'])->name("bancos.modificar");
        Route::post("/modificar/{id}", [BancosController::class, 'modificar']);
        Route::post("/eliminar/{id}", [BancosController::class, 'eliminar'])->name("bancos.eliminar");
    });

    
});
