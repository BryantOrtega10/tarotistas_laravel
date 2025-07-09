<?php

use App\Http\Controllers\Api\Cliente\ChatClienteController;
use App\Http\Controllers\Api\Cliente\CobrosController;
use App\Http\Controllers\Api\Cliente\ConsultaTarotistasController;
use App\Http\Controllers\Api\Cliente\LoginClienteController;
use App\Http\Controllers\Api\Cliente\PerfilClienteController;
use App\Http\Controllers\Api\EspecialidadesController;
use App\Http\Controllers\Api\PaisesController;
use App\Http\Controllers\Api\Tarotista\BancosController;
use App\Http\Controllers\Api\Tarotista\CalificacionesTarotistaController;
use App\Http\Controllers\Api\Tarotista\ChatsTarotistaController;
use App\Http\Controllers\Api\Tarotista\ComentariosTarotistaController;
use App\Http\Controllers\Api\Tarotista\LoginTarotistaController;
use App\Http\Controllers\Api\Tarotista\PagosTarotistaController;
use App\Http\Controllers\Api\Tarotista\PerfilTarotistaController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "tarotista"], function () {
    Route::post("/registro", [LoginTarotistaController::class, 'registroEmail']);
    Route::post("/login", [LoginTarotistaController::class, 'login']);
    Route::post("/login-redes", [LoginTarotistaController::class, 'loginRedes']);

    Route::group(["prefix" => "/", "middleware" => ["auth:sanctum", "load.tarotista"]], function () {
        Route::post("/completar-perfil", [PerfilTarotistaController::class, 'completarPerfil']);
        Route::post("/completar-cuenta", [PerfilTarotistaController::class, 'completarCuenta']);

        Route::get("/bancos", [BancosController::class, 'obtenerBancosPorPais']);

        Route::group(["prefix" => "/", "middleware" => ["tarotista.approved"]], function () {
            Route::post("/conexion/{status}", [PerfilTarotistaController::class, 'estadoConexion']);

            Route::group(["prefix" => "/chats"], function () {
                Route::get('/', [ChatsTarotistaController::class, 'obtenerChats']);
                Route::get('/{id}', [ChatsTarotistaController::class, 'obtenerMensajesChat']);
                Route::post('/{id}', [ChatsTarotistaController::class, 'enviarMensajeChat']);
            });

            Route::group(["prefix" => "/comentarios"], function () {
                Route::get('/', [ComentariosTarotistaController::class, 'obtenerComentarios']);
                Route::get('/{id}', [ComentariosTarotistaController::class, 'obtenerComentarioId']);
                Route::post('/{id}/responder', [ComentariosTarotistaController::class, 'responderComentarioId']);
            });

            Route::group(["prefix" => "/calificaciones"], function () {
                Route::get('/', [CalificacionesTarotistaController::class, 'obtenerCalificaciones']);
            });

            Route::group(["prefix" => "/mi-perfil"], function () {
                Route::get('/', [PerfilTarotistaController::class, 'obtenerMiPerfil']);
                Route::post('/', [PerfilTarotistaController::class, 'actualizarMiPerfil']);

                Route::get('/cuenta', [PerfilTarotistaController::class, 'obtenerMiCuenta']);
                Route::post('/cuenta', [PerfilTarotistaController::class, 'modificarMiCuenta']);
            });

            Route::group(["prefix" => "/pagos"], function () {
                Route::get('/', [PagosTarotistaController::class, 'obtenerPagos']);
                Route::get('/resumen', [PagosTarotistaController::class, 'obtenerResumen']);
                Route::get('/unico/{id}', [PagosTarotistaController::class, 'obtenerPagoxId']);
            });
        });
    });
});

Route::group(["prefix" => "cliente"], function () {
    Route::post("/registro", [LoginClienteController::class, 'registroEmail']);
    Route::post("/login", [LoginClienteController::class, 'login']);
    Route::post("/login-redes", [LoginClienteController::class, 'loginRedes']);


    Route::group(["prefix" => "/tarotistas"], function () {
        Route::get("/", [ConsultaTarotistasController::class, 'obtenerTarotistas']);
        Route::get("/{id}", [ConsultaTarotistasController::class, 'obtenerTarotistaxId']);
        Route::get("/{id}/comentarios", [ConsultaTarotistasController::class, 'obtenerComentarios']);
    });

    Route::group(["prefix" => "/chat", "middleware" => ["auth:sanctum", "load.cliente"]], function () {
        Route::get("/", [ChatClienteController::class, 'obtenerUltimosChats']);
        Route::post("/obtener-id/{idTarotista}", [ChatClienteController::class, 'obtenerId']);
        Route::get("/{id}", [ChatClienteController::class, 'obtenerChats']);
        Route::post("/{id}", [ChatClienteController::class, 'enviarMensajeChat']);
    });

    Route::group(["prefix" => "/mi-perfil", "middleware" => ["auth:sanctum", "load.cliente"]], function () {
        Route::get('/', [PerfilClienteController::class, 'obtenerMiPerfil']);
        Route::post('/', [PerfilClienteController::class, 'actualizarMiPerfil']);
    });

    Route::group(["prefix" => "/cobros", "middleware" => ["auth:sanctum", "load.cliente"]], function () {
        Route::get('/', [CobrosController::class, 'obtenerCobros']);
        Route::get('/{id}', [CobrosController::class, 'obtenerCobrosxId']);
    });
});


Route::get("/especialidades", [EspecialidadesController::class, 'obtenerTodas']);
Route::get("/paises", [PaisesController::class, 'obtenerTodos']);
