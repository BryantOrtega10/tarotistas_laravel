<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PaquetesModel;
use Illuminate\Http\Request;

class PaquetesController extends Controller
{
     /**
     * Sirve para obtener los paquetes de tokens del cliente.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(){
        $paquetes = PaquetesModel::where("estado","=","1")->orderBy("tokens")->get();
        
        return response()->json([
            "success" => true,
            "message" => "Paquetes consultados correctamente",
            "data" => $paquetes
        ]);
    }
}
