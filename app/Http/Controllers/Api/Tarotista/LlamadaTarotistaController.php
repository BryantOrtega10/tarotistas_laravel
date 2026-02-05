<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Events\LlamadaEvent;
use App\Http\Controllers\Controller;
use App\Http\Utils\Funciones;
use App\Models\LlamadasModel;
use App\Models\SegmentosModel;
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
        })->where("id", $idLlamada)
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
                "message" => "No se puede aceptar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->estado_llamada = 3;
        $llamada->type = 'call-start';
        $llamada->fecha_inicio = date("Y-m-d H:i:s");
        $llamada->save();

        $tarotista->estado_conexion = 2;
        $tarotista->save();

        $user = $request->user();
        $relacion = $llamada->cliente_tarotista;

        $cliente = $relacion->cliente;
        //Enviar notificacion push al cliente
        if (isset($cliente->user->token_push)) {
            Funciones::sendNotification($cliente->user->token_push, "Llamada aceptada", "Llamada aceptada por el tarotista ingresa a verla", [
                "relacion_id" => $relacion->id,
                "llamada_id" => $llamada->id,
                "accion" => "aceptada",
            ]);
        }

        broadcast(new LlamadaEvent($llamada, $user))->toOthers();
        return response()->json([
            "success" => true,
            "message" => "Llamada aceptada correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);
    }


    /**
     * Sirve para enviar el offer de una llamada que este en estado aceptada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOffer($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede enviar el id esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->type = 'webrtc-offer';
        $llamada->save();

        $user = $request->user();

        broadcast(new LlamadaEvent($llamada, $user, [
            'type' => 'webrtc-offer',
            'offer' => $request->offer
        ]))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada offer enviado correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);
    }


    /**
     * Sirve para enviar el id de una llamada que este en estado aceptada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function ice($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede enviar el id esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->type = 'webrtc-ice';
        $llamada->save();

        $user = $request->user();

        broadcast(new LlamadaEvent($llamada, $user, [
            'type' => 'webrtc-ice',
            'candidate' => $request->candidate
        ]))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada ice candidate enviado correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);
    }


    /**
     * Sirve para enviar la respuesta de una llamada que este en estado aceptada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function answer($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede enviar la respuesta de esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->type = 'webrtc-answer';
        $llamada->save();

        $user = $request->user();

        broadcast(new LlamadaEvent($llamada, $user, [
            'type' => 'webrtc-answer',
            'answer' => $request->answer
        ]))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada answer enviado correctamente",
            "data" => [
                "llamada" => $llamada,
            ]
        ]);
    }

    /**
     * Sirve para cancelar una llamada que este en estado solicitada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelar($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede aceptar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->estado_llamada = 2;
        $llamada->save();

        $user = $request->user();
        $relacion = $llamada->cliente_tarotista;

        $cliente = $relacion->cliente;
        //Enviar notificacion push al cliente
        if (isset($cliente->user->token_push)) {
            Funciones::sendNotification($cliente->user->token_push, "Llamada rechazada", "Llamada rechazada por el tarotista vuelve a intentarlo mas tarde", [
                "relacion_id" => $relacion->id,
                "llamada_id" => $llamada->id,
                "accion" => "cancelada",
            ]);
        }

        broadcast(new LlamadaEvent($llamada, $user))->toOthers();
        return response()->json([
            "success" => true,
            "message" => "Llamada rechazada correctamente",
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
            ->where("id", $idLlamada)
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }

        if ($llamada->estado_llamada !== 3) {
            return response()->json([
                "success" => true,
                "message" => "Llamada terminada correctamente anteriormente",
                "data" => [
                    "llamada" => $llamada,
                ]
            ]);
        }

        $llamada->fecha_fin = date("Y-m-d H:i:s");
        $segmento = SegmentosModel::where("fk_llamada", "=", $llamada->id)
            ->whereNull("fecha_fin")
            ->first();
        if (isset($segmento)) {
            $segmento->fecha_fin = date("Y-m-d H:i:s");
            $fechaInicio = new DateTime($segmento->fecha_inicio);
            $fechaFin = new DateTime($segmento->fecha_fin);
            $intervalo = $fechaInicio->diff($fechaFin);
            $tiempoSeg = ($intervalo->days * 24 * 60 * 60) + ($intervalo->h * 60 * 60) + ($intervalo->i * 60) + $intervalo->s;
            $segmento->tiempo_seg = $tiempoSeg;
            $segmento->save();
        }


        $sumaSegmentos = SegmentosModel::selectRaw("SUM(tiempo_seg) as sumaTiempo")
            ->where("fk_llamada", "=", $llamada->id)
            ->whereNotNull("fecha_fin")
            ->first();

        $sumaTiempoMins = ($sumaSegmentos?->sumaTiempo ?? 0) / 60;
        $llamada->tiempo_mins = round($sumaTiempoMins, 2);
        $llamada->subtotal = $sumaTiempoMins * $llamada->tarifa;
        $llamada->comision = $llamada->subtotal * $llamada->por_comision;
        $llamada->total = $llamada->subtotal - $llamada->comision;
        $llamada->estado_llamada = 4;
        $llamada->save();

        $tarotista->estado_conexion = 3;
        $tarotista->save();
        $user = $request->user();

        broadcast(new LlamadaEvent($llamada, $user, [
            "type" => 'call-end'
        ]));

        //TODO: Enviar notificacion push al cliente
        $relacion = $llamada->cliente_tarotista;
        if (isset($cliente->user->token_push)) {
            Funciones::sendNotification($cliente->user->token_push, "Llamada finalizada", "Llamada finalizada por el tarotista", [
                "relacion_id" => $relacion->id,
                "llamada_id" => $llamada->id,
                "accion" => "finalizada",
            ]);
        }

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
     * Sirve para consultar cuanto tiempo ha durado una llamada y el segmento activo
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function tiempo($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede finalizar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $segmento = SegmentosModel::where("fk_llamada", "=", $llamada->id)
            ->whereNull("fecha_fin")
            ->first();

        $sumaSegmentos = SegmentosModel::selectRaw("SUM(tiempo_seg) as sumaTiempo")
            ->where("fk_llamada", "=", $llamada->id)
            ->whereNotNull("fecha_fin")
            ->first();

        return response()->json([
            "success" => true,
            "message" => "Llamada terminada correctamente",
            "data" => [
                "segmento" => $segmento,
                "tiempoActual" => $sumaSegmentos?->sumaTiempo ?? 0
            ]
        ]);
    }


    /**
     * Sirve para iniciar el timer de un segmento de una llamada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function terminarSegmento($idLlamada, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("id", $idLlamada)
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
                "message" => "No se puede finalizar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $segmento = SegmentosModel::where("fk_llamada", "=", $llamada->id)
            ->whereNull("fecha_fin")
            ->first();
        if (isset($segmento)) {
            $segmento->fecha_fin = date("Y-m-d H:i:s");
            $fechaInicio = new DateTime($segmento->fecha_inicio);
            $fechaFin = new DateTime($segmento->fecha_fin);
            $intervalo = $fechaInicio->diff($fechaFin);
            $tiempoSeg = ($intervalo->days * 24 * 60 * 60) + ($intervalo->h * 60 * 60) + ($intervalo->i * 60) + $intervalo->s;
            $segmento->tiempo_seg = $tiempoSeg;
            $segmento->save();
        }

        return response()->json([
            "success" => true,
            "message" => "Segmento de llamada terminado correctamente",
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

        $llamada = LlamadasModel::with([
            "cliente_tarotista.cliente",
            "cliente_tarotista.cliente.user",
            "cliente_tarotista.tarotista",
            "cliente_tarotista.tarotista.user",
        ])
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->where("id", $idLlamada)
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

    /**
     * Sirve para ver si el tarotista tiene una llamada activa
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function activa(Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
            $query->where('fk_tarotista', $tarotista->id);
        })
            ->where("estado_llamada", 3)
            ->first();

        if (!isset($llamada)) {

            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada activa",
            ]);
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
