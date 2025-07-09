<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\LlamadasModel;
use Illuminate\Http\Request;
use stdClass;

class CobrosController extends Controller
{
    /**
     * Obtiene los ultimos cobros realizados a un cliente
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerCobros(Request $request){

        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);
        $cliente = $request->attributes->get('cliente');
        
        $llamadas = LlamadasModel::with(["cliente_tarotista.tarotista.user:id,name,photo"])
            ->where("estado_llamada","=",4)
            ->where("estado_pago_cli","=",3)
             ->whereHas('cliente_tarotista', function ($query) use ($cliente) {
                $query->where('fk_cliente', $cliente->id);
            })
            ->take($take)
            ->skip($skip)
            ->get();

        $data = $llamadas->map(function ($llamada) {
            $item = new stdClass;
            $item->tarifa = $llamada->tarifa;
            $item->tiempo_mins = $llamada->tiempo_mins;
            $item->total = $llamada->total;
            $item->tarotista = $llamada->tarotista->user;
            return $item;
        })->values();

        return response()->json([
            "success" => true,
            "message" => "Cobros consultados correctamente",
            "data" => $data
        ]);

    }

    /**
     * Obtiene el detalle del cobro 
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerCobrosxId(int $id, Request $request){

        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::with(["cliente_tarotista.tarotista.user:id,name,photo"])
            ->where("id","=",$id)
             ->whereHas('cliente_tarotista', function ($query) use ($cliente) {
                $query->where('fk_cliente', $cliente->id);
            })
            ->first();



        return response()->json([
            "success" => true,
            "message" => "Cobro consultado correctamente",
            "data" => [
                "tarifa" => $llamada->tarifa,
                "tiempo_mins" => $llamada->tiempo_mins,
                "total" => $llamada->total,
                "tarotista" => $llamada->tarotista->user,
            ]
        ]);
    }
}
