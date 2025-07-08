<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EspecialidadesModel;
use Illuminate\Http\Request;

class EspecialidadesController extends Controller
{
    /**
     * Carga todas las especialidades por orden alfabetico
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function obtenerTodas(){
        $especialidades = EspecialidadesModel::select('id','nombre')->orderBy('nombre','asc')->get();

        return response()->json([
            "success" => true,
            "message" => "Especialidades consultadas correctamente",
            "data" => [
                "especialidades" => $especialidades,
            ]

        ]);
    }
}
