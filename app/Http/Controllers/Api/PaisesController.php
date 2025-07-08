<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaisesModel;
use Illuminate\Http\Request;

class PaisesController extends Controller
{
     /**
     * Carga todas los paises por orden alfabetico
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function obtenerTodos(){
        $paises = PaisesModel::select('id','nombre','bandera')->orderBy('nombre','asc')->get();

        return response()->json([
            "success" => true,
            "message" => "Paises consultados correctamente",
            "data" => [
                "paises" => $paises,
            ]

        ]);
    }
}
