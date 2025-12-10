<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionModel;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function obtenerConfig(){
        $configuracion = ConfiguracionModel::all();
        
        return response()->json([
            "success" => true,
            "message" => "Configuración cargada correctamente",
            "data" => $configuracion[0]
        ]);
    }
}
