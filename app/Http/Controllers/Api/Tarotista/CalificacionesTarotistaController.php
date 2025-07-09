<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Models\LlamadasModel;
use Illuminate\Http\Request;
use stdClass;

class CalificacionesTarotistaController extends Controller
{
    /**
     * Obtiene las calificaciones de un tarotista
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerCalificaciones(Request $request)
    {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);
        $tarotista = $request->attributes->get('tarotista');

        $calificaciones = LlamadasModel::with("cliente_tarotista.cliente.user:id,name,photo")
            ->where("llamadas.estado_llamada", "=", 4)
            ->whereNotNull("llamadas.calificacion")
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->take($take)
            ->skip($skip)
            ->orderBy("llamadas.create_at", "desc")
            ->get();

        $data = [];
        foreach($calificaciones as $itemCalificacion){
            $item = new stdClass;
            $item->cliente = $itemCalificacion->cliente_tarotista->cliente->user;
            $item->fecha = $itemCalificacion->created_at;
            $item->calificacion = $itemCalificacion->calificacion;
            array_push($data, $item);
        }

         return response()->json([
            "success" => true,
            "message" => "Calificaciones consultadas correctamente",
            "data" => $data
        ]);
        
    }
}
