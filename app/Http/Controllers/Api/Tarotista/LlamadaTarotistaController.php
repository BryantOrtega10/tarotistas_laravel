<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Events\LlamadaEvent;
use App\Http\Controllers\Controller;
use App\Models\LlamadasModel;
use DateTime;
use Illuminate\Http\Request;

class LlamadaTarotistaController extends Controller
{
    /**
     * Sirve para aceptar una llamada que este en estado solicitada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function aceptar($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->whereIn("id", $idLlamada)
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }

        if ($llamada->estado_llamada !== 1) {
            return response()->json([
                "success" => false,
                "message" => "No se puede aceptar esta llamada actualmente esta en estado: ".$llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->estado_llamada = 3;
        $llamada->fecha_inicio = date("Y-m-d H:i:s");
        $llamada->save();

        $user = $request->user(); 
        broadcast(new LlamadaEvent($llamada, $user->id))->toOthers();
        //TODO: Enviar notificacion push al cliente
        return response()->json([
            "success" => true,
            "message" => "Llamada aceptada correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);

    }


    /**
     * Sirve para finalizar una llamada que este en estado solicitada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizar($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->whereIn("id", $idLlamada)
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }

        if ($llamada->estado_llamada !== 3) {
            return response()->json([
                "success" => false,
                "message" => "No se puede finalizar esta llamada actualmente esta en estado: ".$llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->fecha_fin = date("Y-m-d H:i:s");

        $fechaInicio = new DateTime($llamada->fecha_inicio);
        $fechaFin = new DateTime($llamada->fecha_fin);
        $intervalo = $fechaInicio->diff($fechaFin);
        $tiempoMins = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        $llamada->tiempo_mins = $tiempoMins;
        $llamada->subtotal = $tiempoMins * $llamada->tarifa;
        $llamada->comision = $llamada->subtotal * $llamada->por_comision;
        $llamada->total = $llamada->subtotal - $llamada->comision;
        $llamada->estado_llamada = 4;
        $llamada->save();

        $user = $request->user(); 
        broadcast(new LlamadaEvent($llamada, $user->id));
       
        //TODO: Enviar notificacion push al cliente
        //TODO: Job de Braintree Paypal

        return response()->json([
            "success" => true,
            "message" => "Llamada terminada correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);

    }

    /**
     * Sirve para ver el detalle de una llamada 
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function detalle($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->whereIn("id", $idLlamada)
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Llamada consultada correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);
    }
}
