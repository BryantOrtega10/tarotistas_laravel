<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Models\LlamadasModel;
use App\Models\PagosModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagosTarotistaController extends Controller
{
    /**
     * Sirve para obtener todos los pagos realizado al tarotista con un take y skip
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerPagos(Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);

        $pagos = PagosModel::where("fk_tarotista", "=", $tarotista->id)
            ->orderBy("created_at", "desc")
            ->take($take)
            ->skip($skip)
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Pagos consultados correctamente",
            "data" => $pagos
        ]);
    }

    /**
     * Sirve para obtener el saldo que se le debe al tarotista y un resumen de cuanto ha generado en llamadas discriminado por subtotal, comision y total.
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerResumen(Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $resumen = LlamadasModel::select(DB::raw("sum(subtotal) as sum_subtotal"), DB::raw("sum(comision) as sum_comision"), DB::raw("sum(total) as sum_total"))
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->groupBy("fk_cliente_tarotista")
            ->first();



        return response()->json([
            "success" => true,
            "message" => "Se ha consultado el resumen de tus pagos correctamente",
            "data" => [
                "subtotal" => $resumen->sum_subtotal ?? 0,
                "comision" => $resumen->sum_comision ?? 0,
                "total" => $resumen->sum_total ?? 0,
                "saldo" => $tarotista->saldo ?? 0,
            ]
        ]);
    }

    /**
     * Sirve para obtener un pago en especifico junto con el detalle de las llamadas que comprenden ese pago
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerPagoxId($id, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $pago = PagosModel::with(["llamadas:id,tarifa,por_comision,tiempo_mins,subtotal,comision,total"])
            ->where("id", "=", $id)
            ->where("fk_tarotista", "=", $tarotista->id)
            ->first();

        return response()->json([
            "success" => true,
            "message" => "Se ha consultado el detalle de un pago correctamente",
            "data" => $pago

        ]);
    }
}
