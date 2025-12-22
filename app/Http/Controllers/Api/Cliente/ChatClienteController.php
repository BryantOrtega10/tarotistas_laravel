<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Events\NuevoMensajeEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cliente\Chat\EnviarChatClienteRequest;
use App\Http\Utils\Funciones;
use App\Models\ChatsModel;
use App\Models\ClienteTarotistaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChatClienteController extends Controller
{

    /**
     * Obtiene los ultimos chats del cliente con skip y take
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerUltimosChats(Request $request)
    {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);
        $cliente = $request->attributes->get('cliente');

        $sub = ChatsModel::select('fk_cliente_tarotista', DB::raw('MAX(created_at) as fecha_ultimo_chat'))
            ->groupBy('fk_cliente_tarotista');

        $unReadSub = ChatsModel::select('fk_cliente_tarotista', DB::raw('COUNT(id) as chats_unread'))
            ->whereNull("leido")
            ->where("origen", "=", 2)
            ->groupBy('fk_cliente_tarotista');

        $ultimosChats = ClienteTarotistaModel::query('unread_counts.chats_unread')
            ->joinSub($sub, 'ultimos_chats', function ($join) {
                $join->on('cliente_tarotista.id', '=', 'ultimos_chats.fk_cliente_tarotista');
            })
            ->leftJoinSub($unReadSub, 'unread_counts', function ($join) {
                $join->on('cliente_tarotista.id', '=', 'unread_counts.fk_cliente_tarotista');
            })
            ->with([
                'tarotista.user:id,name,photo',
                'ultimoChat',
            ])
            ->where("cliente_tarotista.fk_cliente", "=", $cliente->id)
            ->orderBy('ultimos_chats.fecha_ultimo_chat', 'desc')
            ->take($take)
            ->skip($skip)
            ->get();

        $totalChats = ClienteTarotistaModel::where("fk_cliente", "=", $cliente->id)->count();

        $data = [];
        foreach ($ultimosChats as $itemsChat) {
            $item = new stdClass;
            $item->idChat = $itemsChat->ultimoChat->fk_cliente_tarotista;
            $item->tarotista = $itemsChat->tarotista->user;
            $item->mensaje = $itemsChat->ultimoChat->mensaje;
            $item->unread = $itemsChat->chats_unread;
            $created = Carbon::parse($itemsChat->ultimoChat->created_at);
            $fecha = $created->isToday() ? 'Hoy' : ($created->isYesterday() ? 'Ayer' : $created->format('Y-m-d'));
            $hora = $created->format('h:i a');
            $item->fecha = $fecha . " " . $hora;
            array_push($data, $item);
        }

        return response()->json([
            "success" => true,
            "message" => "Chats consultados correctamente",
            "data" => $data,
            "total" => $totalChats
        ]);
    }

    /**
     * Obtiene el id de cliente-tarotista para poder enviar mensajes o llamadas
     * 
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerId($idTarotista, Request $request)
    {

        $cliente = $request->attributes->get('cliente');

        $clienteTarotista = ClienteTarotistaModel::where("fk_cliente", "=", $cliente->id)
            ->where("fk_tarotista", "=", $idTarotista)
            ->first();
        if (!isset($clienteTarotista)) {
            $clienteTarotista = new ClienteTarotistaModel();
            $clienteTarotista->fk_cliente = $cliente->id;
            $clienteTarotista->fk_tarotista = $idTarotista;
            $clienteTarotista->save();
        }

        return response()->json([
            "success" => true,
            "message" => "Se ha obtenido el id de chat/llamada correctamente",
            "data" => [
                'id' => $clienteTarotista->id
            ]
        ]);
    }

    /**
     * Obtiene los ultimos mensajes del chat con fk_cliente_tarotista = {id} con skip y take
     * 
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerChats($id, Request $request)
    {
        $beforeId = $request->input("before_id");
        $take = $request->input("take", 10);
        $cliente = $request->attributes->get('cliente');

        $relacion = ClienteTarotistaModel::where('id', $id)
            ->where('fk_cliente', $cliente->id)
            ->first();

        if (!isset($relacion)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró esta relación cliente-tarotista',
            ], 404);
        }


        $mensajes = ChatsModel::where("fk_cliente_tarotista", "=", $id);
        if ($beforeId) {
            $mensajes->where('id', '<', $beforeId);
        }

        $mensajes = $mensajes->orderBy('id', 'desc')
            ->take($take)
            ->get()
            ->reverse()
            ->values();

        foreach ($mensajes as $m) {
            if ($m->origen == 2) {
                $m->update([
                    'leido' => date("Y-m-d H:i:s")
                ]);
            }
        }

        $primerId = ChatsModel::where("fk_cliente_tarotista", "=", $id);
        $primerId = $primerId->orderBy('id', 'asc')->first();


        return response()->json([
            "success" => true,
            "message" => "Mensajes de chat consultados correctamente",
            "data" => [
                "mensajes" => $mensajes,
                "mensajes_gratis" => $relacion->mensajes_gratis,
                "tarotista" => [
                    "nombre" => $relacion->tarotista->nombre,
                    "estado_conexion" => $relacion->tarotista->estado_conexion,
                    "user" => [
                        "photo" => $relacion->tarotista->user->photo
                    ]
                ],
                "primer_id" => $primerId->id ?? 0
            ]
        ]);
    }

    /**
     * Agrega un nuevo mensaje al chat por parte del cliente
     * 
     * @param int $id
     * @param App\Http\Requests\Api\Cliente\Chat\EnviarChatRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarMensajeChat($id, EnviarChatClienteRequest $request)
    {
        $cliente = $request->attributes->get('cliente');
        $relacion = ClienteTarotistaModel::where('id', $id)
            ->where('fk_cliente', $cliente->id)
            ->first();

        if (!isset($relacion)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró esta relación cliente-tarotista',
            ], 404);
        }

        if ($relacion->mensajes_gratis <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ya no tienes mensajes gratis disponibles, luego de 1h de llamada se desbloquearan nuevos mensajes',
            ], 402);
        }

        $relacion->mensajes_gratis -= 1;
        $relacion->save();

        $chat = new ChatsModel();
        $chat->mensaje = $request->input("mensaje");
        $chat->origen = 1;
        $chat->tipo = 1;
        $chat->fk_cliente_tarotista = $id;
        $chat->save();

       
        $tarotista = $relacion->tarotista;
        if (isset($tarotista->user->token_push)) {
            Funciones::sendNotification($tarotista->user->token_push, "Nuevo mensaje desde un cliente", "Tienes un nuevo mensaje ingresa al app para verlo", [
                "message_id" => $id,
                "chat_id" => $chat->id,
                "mensaje" => $chat->mensaje
            ]);
        }
        $user = $request->user();
        broadcast(new NuevoMensajeEvent($chat, $user))->toOthers();

        return response()->json([
            "success" => true,
            "message" => "Mensaje agregado correctamente",
            "data" => [
                "chat" => $chat,
                "mensajes_gratis" => $relacion->mensajes_gratis
            ]
        ]);
    }
}
