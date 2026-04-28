<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Events\LlamadaEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Request\Cliente\CalificarLlamadaRequest;
use App\Http\Utils\Funciones;
use App\Models\ClienteTarotistaModel;
use App\Models\ConfiguracionModel;
use App\Models\LlamadasModel;
use App\Models\SegmentosModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LlamadaClienteController extends Controller
{
    /**
     * Sirve para solicitar una nueva llamada a un tarotista.
     * 
     * @param int $idRelacion
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function solicitar($idTarotista, Request $request)
    {
        $cliente = $request->attributes->get('cliente');
        if (!isset($cliente->tokens) || $cliente->tokens <= 0) {
            return response()->json([
                "success" => false,
                "message" => "No tienes tokens disponibles para realizar la llamada",
            ], 402);
        }


        $relacion = ClienteTarotistaModel::where('fk_tarotista', $idTarotista)
            ->where('fk_cliente', $cliente->id)
            ->first();

        if (!isset($relacion)) {
            $relacion = new ClienteTarotistaModel();
            $relacion->fk_cliente = $cliente->id;
            $relacion->fk_tarotista = $idTarotista;
            $relacion->save();
        }

        //Validar que el cliente no este en una llamada
        $existenLlamadasActivas = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
        })->whereIn("estado_llamada", [1, 3])
            ->first();

        if (isset($existenLlamadasActivas)) {
            return response()->json([
                "success" => false,
                "message" => "Ya estas en una llamada o esperando una respuesta",
            ], 400);
        }

        //Validar que el tarotista no este en una llamada
        $tarotista = $relacion->tarotista;
        if ($tarotista->estado !== 3 || $tarotista->estado_conexion !== 3) {
            return response()->json([
                "success" => false,
                "message" => "El tarotista no esta disponible en este momento",
            ], 400);
        }

        $config = ConfiguracionModel::find(1);


        $llamada = new LlamadasModel();
        $llamada->tarifa_valor_min = $config->valor_min;
        $llamada->tarifa_token_min = $config->token_min;
        $llamada->por_comision = $config->por_comision;
        $llamada->estado_llamada = 1;
        $llamada->estado_pago_tar = 1;
        $llamada->fk_cliente_tarotista = $relacion->id;
        $llamada->save();

        //Enviar notificacion push al tarotista
        if (isset($tarotista->user->token_push)) {
            Funciones::sendNotification($tarotista->user->token_push, "Nueva llamada", "Tienes una nueva llamada ingresa al app para verla", [
                "relacion_id" => $relacion->id,
                "llamada_id" => $llamada->id,
                "accion" => "solicitar",
            ]);
        }

        $user = $request->user();
        broadcast(new LlamadaEvent($llamada, $user))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada solicitada correctamente",
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
     * Sirve para iniciar el timer de un segmento de una llamada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function iniciarSegmento($idLlamada, Request $request)
    {
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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

        $segmento = SegmentosModel::where("fk_llamada", "=", $llamada->id)
            ->whereNull("fecha_fin")
            ->first();
        if (!isset($segmento)) {
            $segmento = new SegmentosModel();
            $segmento->fecha_inicio = date("Y-m-d H:i:s");
            $segmento->fk_llamada = $llamada->id;
            $segmento->save();
        }

        return response()->json([
            "success" => true,
            "message" => "Segmento de llamada iniciado correctamente",
            "data" => [
                "llamada" => $llamada,
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
     * Sirve para cancelar una llamada que este en estado solicitada
     * 
     * @param int $idLlamada
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelar($idTarotista, Request $request)
    {
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente, $idTarotista) {
            $query->where('fk_cliente', $cliente->id);
            $query->where('fk_tarotista', $idTarotista);
        })
            ->whereIn("estado_llamada", [1])
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }
        Log::info("Llamada encontrada ". $llamada->id);
        $llamada->estado_llamada = 2;
        $llamada->save();

        $tarotista = $llamada->cliente_tarotista->tarotista;
        //$user = $request->user();
        if (isset($tarotista->user->token_push)) {
            Log::info("El token se envio a ". $tarotista->user->token_push);
            Funciones::sendNotification($tarotista->user->token_push, "Nueva llamada cancelada", "El cliente canceló la llamada antes de que la aceptaras", [
                "relacion_id" => $llamada->fk_cliente_tarotista,
                "llamada_id" => $llamada->id,
                "accion" => "cancelada",
            ]);
        }
        else{
            Log::info("No se recibio el token de ". $tarotista->user);
        }

        //broadcast(new LlamadaEvent($llamada, $user))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada cancelada correctamente",
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
        $llamada->subtotal = $sumaTiempoMins * $llamada->tarifa_valor_min;
        $llamada->comision = $llamada->subtotal * $llamada->por_comision / 100;
        $llamada->total = $llamada->subtotal - $llamada->comision;
        $llamada->estado_llamada = 4;
        $llamada->tokens_gastados = intval($sumaTiempoMins * $llamada->tarifa_token_min);
        $llamada->save();

        $cliente->tokens = $cliente->tokens - $llamada->tokens_gastados;
        $cliente->save();

        $tarotista = $llamada->cliente_tarotista->tarotista;
        $tarotista->estado_conexion = 3;
        $tarotista->save();

        $user = $request->user();
        broadcast(new LlamadaEvent($llamada, $user, [
            "type" => 'call-end'
        ]));

        if (isset($tarotista->user->token_push)) {        
            Funciones::sendNotification($tarotista->user->token_push, "Llamada finalizada", "Llamada finalizada por el cliente", [
                "relacion_id" => $llamada->cliente_tarotista->id,
                "llamada_id" => $llamada->id,
                "accion" => "finalizada",
            ]);
        }


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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
        Log::info("Consultando segmento de la llamada con id = ".$llamada->id);
        $segmento = SegmentosModel::where("fk_llamada", "=", $llamada->id)
            ->whereNull("fecha_fin")
            ->first();
        Log::info("Segmento consultado id = ".$segmento?->id ?? "No hay nada");
        
        $sumaSegmentos = SegmentosModel::selectRaw("SUM(tiempo_seg) as sumaTiempo")
            ->where("fk_llamada", "=", $llamada->id)
            ->whereNotNull("fecha_fin")
            ->first();
        
        return response()->json([
            "success" => true,
            "message" => "Llamada terminada correctamente",
            "data" => [
                "segmento" => $segmento,
                "tiempoActual" => round($sumaSegmentos?->sumaTiempo ?? 0)
            ]
        ]);
    }

    /**
     * Sirve para ver el detalle de una llamada 
     * 
     * @param int $idLlamada
     * @param App\Http\Requests\Api\Request\Cliente\CalificarLlamadaRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function calificar($idLlamada, CalificarLlamadaRequest $request)
    {
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
        })
            ->where("id", $idLlamada)
            ->first();

        if (!isset($llamada)) {
            return response()->json([
                "success" => false,
                "message" => "No se encuentra ninguna llamada con este ID",
            ], 404);
        }

        $llamada->calificacion = $request->input("calificacion");
        $llamada->comentario = $request->input("comentario");
        $llamada->save();


        return response()->json([
            "success" => true,
            "message" => "Llamada calificada correctamente",

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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::with([
            "cliente_tarotista.cliente",
            "cliente_tarotista.cliente.user",
            "cliente_tarotista.tarotista",
            "cliente_tarotista.tarotista.user",
        ])
            ->whereHas('cliente_tarotista', function ($query) use ($cliente) {
                $query->where('fk_cliente', $cliente->id);
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
     * Sirve para ver si el cliente tiene una llamada activa
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function activa(Request $request)
    {
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
        })
            ->where("estado_llamada", 3)
            ->first();

        if (!isset($llamada)) {
            $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
                $query->where('fk_tarotista', $cliente->id);
            })
                ->where("estado_llamada", 4)
                ->whereNull("calificacion")
                ->first();

            if (!isset($llamada)) {
                return response()->json([
                    "success" => false,
                    "message" => "No se encuentra ninguna llamada activa",
                ]);
            }
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
