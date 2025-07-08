<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Models\BancosModel;
use Illuminate\Http\Request;

class BancosController extends Controller
{
    /**
     * Carga todos los bancos de acuerdo al pais registrado en orden alfabetico
     * 
     * @param App\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerBancosPorPais(Request $request){
        $tarotista = $request->attributes->get('tarotista');
        return response()->json([
            "success" => true,
            "message" => "Has consultado los bancos correctamente",
            "data" => [
                "bancos" => $tarotista->pais?->bancos ?? [],
            ]
        ]);
    }
}
