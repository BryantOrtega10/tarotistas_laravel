<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Models\LlamadasModel;
use App\Models\PagosModel;
use Carbon\Carbon;
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
        $skip = $request->input("skip",0);
        $take = $request->input("take",10);


        $fechasLimite = LlamadasModel::selectRaw("max(fecha_fin) as max_fecha, min(fecha_fin) as min_fecha")
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->first();
            
        $maxFecha = Carbon::parse($fechasLimite->max_fecha);
        $minFecha = Carbon::parse($fechasLimite->min_fecha);
        $total = $minFecha->diffInMonths($maxFecha) + 1;
        $data = [];

        for ($i = $skip; $i < ($skip + $take) && $i <= $total; $i++){

            $fechaIterada = $maxFecha->copy()->subMonths($i);

            $mes = $fechaIterada->month;
            $anio = $fechaIterada->year;

            $mesNombre = $fechaIterada->translatedFormat('F');

            $ganancias = LlamadasModel::select(DB::raw("sum(subtotal) as sum_subtotal"), DB::raw("sum(comision) as sum_comision"), DB::raw("sum(total) as sum_total"))
                ->whereMonth("fecha_fin", $mes)
                ->whereYear("fecha_fin", $anio)
                ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                    $query->where('fk_tarotista', $tarotista->id);
                })
                ->first();

            $pagosTotales = PagosModel::select(DB::raw("sum(valor) as sum_valor"))
                ->whereMonth("created_at", $mes)
                ->whereYear("created_at", $anio)
                ->where("fk_tarotista", $tarotista->id)
                ->first();

            $pagos = PagosModel::whereMonth("created_at", $mes)
                ->whereYear("created_at", $anio)
                ->where("fk_tarotista", $tarotista->id)
                ->get();

            array_push($data, [
                "ganancias" => [
                    "subtotal" => round($ganancias->sum_subtotal ?? 0),
                    "comision" => round($ganancias->sum_comision ?? 0),
                    "total" => round($ganancias->sum_total ?? 0),                    
                ],
                "pagosTotales" => round($pagosTotales->sum_valor ?? 0),
                "pagos" => $pagos,
                "mes" => $mesNombre,
                "anio" => $anio
            ]);
           
        }

        return response()->json([
            "success" => true,
            "message" => "Pagos consultados correctamente",
            "data" => [
                "historial" => $data,
                "total" => $total,
                "take" => $take,
                "skip" => $skip
            ]                
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

        $ganancias = LlamadasModel::select(DB::raw("sum(total) as sum_total"))
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->first();

        $pagos = PagosModel::select(DB::raw("sum(valor) as sum_valor"))
            ->where("fk_tarotista", "=", $tarotista->id)
            ->first();

        return response()->json([
            "success" => true,
            "message" => "Se ha consultado el resumen de tus pagos correctamente",
            "data" => [
                "ganancias" => round($ganancias->sum_total ?? 0),
                "pagos" => round($pagos->sum_valor ?? 0),
                "saldo" => round($tarotista->saldo ?? 0),
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

        $pago = PagosModel::with(["llamadas:id,tarifa_valor_min,por_comision,tiempo_mins,subtotal,comision,total"])
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
