<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Events\LlamadaEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Request\Cliente\CalificarLlamadaRequest;
use App\Models\ClienteTarotistaModel;
use App\Models\ConfiguracionModel;
use App\Models\LlamadasModel;
use DateTime;
use Illuminate\Http\Request;

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
    public function solicitar($idRelacion, Request $request)
    {
        $cliente = $request->attributes->get('cliente');
        //TODO: Validar con braintree que el metodo de pago sea valido
        $medioPago = $cliente->token_payu !== null;

        //Validar que el cliente tenga un metodo de pago
        if (!$medioPago) {
            return response()->json([
                "success" => false,
                "message" => "El medio de pago no es valido, actualizalo para continuar",
            ], 402);
        }


        $relacion = ClienteTarotistaModel::where('id', $idRelacion)
            ->where('fk_cliente', $cliente->id)
            ->first();

        if (!isset($relacion)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró esta relación cliente-tarotista',
            ], 404);
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
        if ($tarotista->estado_conexion !== 3 || $tarotista->estado === 3) {
            return response()->json([
                "success" => false,
                "message" => "El tarotista no esta disponible en este momento",
            ], 400);
        }

        $config = ConfiguracionModel::find(1);


        $llamada = new LlamadasModel();
        $llamada->tarifa = $config->precio_min;
        $llamada->por_comision = $config->por_comision;
        $llamada->estado_llamada = 1;
        $llamada->estado_pago_cli = 1;
        $llamada->estado_pago_tar = 1;
        $llamada->fk_cliente_tarotista = $idRelacion;
        $llamada->save();

        //TODO: Enviar notificacion push al tarotista

        $user = $request->user();
        broadcast(new LlamadaEvent($llamada, $user->id))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Llamada solicitada correctamente",
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
        $cliente = $request->attributes->get('cliente');

        $llamada = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($cliente) {
            $query->where('fk_cliente', $cliente->id);
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
                "message" => "No se puede cancelar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
            ], 400);
        }

        $llamada->estado_llamada = 2;
        $llamada->save();

        $user = $request->user();
        broadcast(new LlamadaEvent($llamada, $user->id))->toOthers();
        //TODO: Enviar notificacion push al tarotista

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
                "message" => "No se puede finalizar esta llamada actualmente esta en estado: " . $llamada->txt_estado_llamada,
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

        //TODO: Enviar notificacion push al tarotista
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
            ->whereIn("id", $idLlamada)
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

    
}
