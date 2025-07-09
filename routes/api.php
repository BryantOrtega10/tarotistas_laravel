<?php

use App\Http\Controllers\Api\EspecialidadesController;
use App\Http\Controllers\Api\PaisesController;
use App\Http\Controllers\Api\Tarotista\BancosController;
use App\Http\Controllers\Api\Tarotista\ChatsTarotistaController;
use App\Http\Controllers\Api\Tarotista\ComentariosTarotistaController;
use App\Http\Controllers\Api\Tarotista\LoginTarotistaController;
use App\Http\Controllers\Api\Tarotista\PerfilTarotistaController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "tarotista"], function () {
    Route::post("/registro", [LoginTarotistaController::class, 'registroEmail']);
    Route::post("/login", [LoginTarotistaController::class, 'login']);
    Route::post("/login-redes", [LoginTarotistaController::class, 'loginRedes']);

    Route::group(["prefix" => "/", "middleware" => ["auth:sanctum","load.tarotista"]], function () {
        Route::post("/completar-perfil", [PerfilTarotistaController::class, 'completarPerfil']);
        Route::post("/completar-cuenta", [PerfilTarotistaController::class, 'completarCuenta']);
        
        Route::get("/bancos", [BancosController::class, 'obtenerBancosPorPais']);

        Route::group(["prefix" => "/", "middleware" => ["tarotista.approved"]], function () {
            Route::post("/conexion/{status}", [PerfilTarotistaController::class, 'estadoConexion']);

            Route::group(["prefix" => "/chats"], function () {
                Route::get('/',[ChatsTarotistaController::class, 'obtenerChats']);
                Route::get('/{id}',[ChatsTarotistaController::class, 'obtenerMensajesChat']);
                Route::post('/{id}',[ChatsTarotistaController::class, 'enviarMensajeChat']);
            });

            Route::group(["prefix" => "/comentarios"], function () {
                Route::get('/',[ComentariosTarotistaController::class, 'obtenerComentarios']);
                Route::get('/{id}',[ComentariosTarotistaController::class, 'obtenerComentarioId']);
                Route::post('/{id}/responder',[ComentariosTarotistaController::class, 'responderComentarioId']);
            });


        });
    });   
});


Route::get("/especialidades", [EspecialidadesController::class, 'obtenerTodas']);
Route::get("/paises", [PaisesController::class, 'obtenerTodos']);



