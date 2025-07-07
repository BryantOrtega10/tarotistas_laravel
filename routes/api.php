<?php

use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\TarotistaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(["prefix" => "tarotista"], function () {
    Route::post("/registro", [TarotistaController::class, 'registroEmail']);
    Route::post("/login", [TarotistaController::class, 'login']);
    Route::post("/login-redes", [TarotistaController::class, 'loginRedes']);
});

Route::group(["prefix" => "cliente"], function () {
    Route::post("/registro", [ClienteController::class, 'registro']);
    Route::post("/login", [ClienteController::class, 'login']);
});