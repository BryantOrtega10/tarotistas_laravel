<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\BancosController;
use App\Http\Controllers\Web\ConfiguracionController;
use App\Http\Controllers\Web\EspecialidadesController;
use App\Http\Controllers\Web\FrontWompiTransactionController;
use App\Http\Controllers\Web\HistorialPagosController;
use App\Http\Controllers\Web\PagosController;
use App\Http\Controllers\Web\PaisesController;
use App\Http\Controllers\Web\PaquetesController;
use App\Http\Controllers\Web\TarotistaController;
use App\Http\Controllers\Web\WompiTransactionController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate');

    return '<h3>Migraci&oacute;n completada ' . $exitCode . '</h3>';
});

Route::get("storage-link", function () {
    File::link(
        storage_path('app/public'),
        public_path('storage')
    );
});

Route::get("phpinfo", function () {
    phpinfo();
});

Route::get('/cache', function () {
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    return '<h3>Cache eliminado</h3>';
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
    Route::get("/pendientes", [PagosController::class, 'lista'])->name("pagos.pendientes");
    Route::post("/pendientes/datatable", [PagosController::class, 'datatableAjax'])->name("pagos.pendientes.datatable");
    Route::get("/generar/{idTarotista}", [PagosController::class, 'mostrarGenerar'])->name("pagos.generar");
    Route::post("/generar/{idTarotista}", [PagosController::class, 'generar']);
});


Route::group(['prefix' => 'historial-pagos', 'middleware' => ['auth', 'user-role:superadmin']], function () {

    Route::get("/historial", [HistorialPagosController::class, 'lista'])->name("historialPagos.lista");
    Route::post("/historial/datatable", [HistorialPagosController::class, 'datatableAjax'])->name("historialPagos.datatable");
    Route::get("/historial/{idTarotista}", [HistorialPagosController::class, 'mostrarDetalleHistorial'])->name("historialPagos.detalle");
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


Route::group(['prefix' => 'paquetes', 'middleware' => ['auth', 'user-role:superadmin']], function () {
    Route::get("/", [PaquetesController::class, 'lista'])->name("paquetes.lista");
    Route::get("/crear", [PaquetesController::class, 'mostrarCrear'])->name("paquetes.crear");
    Route::post("/crear", [PaquetesController::class, 'crear']);
    Route::get("/modificar/{id}", [PaquetesController::class, 'mostrarModificar'])->name("paquetes.modificar");
    Route::post("/modificar/{id}", [PaquetesController::class, 'modificar']);
    Route::post("/eliminar/{id}", [PaquetesController::class, 'eliminar'])->name("paquetes.eliminar");
});

Route::group(['prefix' => 'configuracion', 'middleware' => ['auth', 'user-role:superadmin']], function () {
    Route::get("/", [ConfiguracionController::class, 'index'])->name("configuracion.index");
    Route::post("/", [ConfiguracionController::class, 'modificar']);
});


Route::group(['prefix' => 'transacciones'], function () {
    Route::get("/generar/{uuid}", [WompiTransactionController::class, 'generarForm'])->name("web.wompi.generar");
    Route::get("/respuesta", [WompiTransactionController::class, 'respuesta'])->name("web.wompi.respuesta");


    Route::post('/webhook', [WompiTransactionController::class, 'webhook'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
    Route::post('/webhook-sandbox', [WompiTransactionController::class, 'webhookSandbox'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
});


Route::get('/terminos', function () {
    return view('web.terminos');
});