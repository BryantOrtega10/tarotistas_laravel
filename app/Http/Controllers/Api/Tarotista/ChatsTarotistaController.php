<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tarotista\Chats\EnviarChatRequest;
use App\Models\ChatsModel;
use App\Models\ClienteTarotistaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChatsTarotistaController extends Controller
{

    /**
     * Obtiene los ultimos chats del tarotista con skip y take
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerChats(Request $request)
    {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);
        $tarotista = $request->attributes->get('tarotista');

        $sub = ChatsModel::select('fk_cliente_tarotista', DB::raw('MAX(created_at) as fecha_ultimo_chat'))
            ->groupBy('fk_cliente_tarotista');

        $ultimosChats = ClienteTarotistaModel::query()
            ->joinSub($sub, 'ultimos_chats', function ($join) {
                $join->on('cliente_tarotista.id', '=', 'ultimos_chats.fk_cliente_tarotista');
            })
            ->with([
                'cliente.user:id,name,photo',
                'ultimoChat',
            ])
            ->where("cliente_tarotista.fk_tarotista", "=", $tarotista->id)
            ->orderBy('ultimos_chats.fecha_ultimo_chat', 'desc')
            ->take($take)
            ->skip($skip)
            ->get();

        $data = [];
        foreach ($ultimosChats as $itemsChat) {
            $item = new stdClass;
            $item->idChat = $itemsChat->ultimoChat->fk_cliente_tarotista;
            $item->cliente = $itemsChat->cliente->user;
            $item->mensaje = $itemsChat->ultimoChat->mensaje;
            $created = Carbon::parse($itemsChat->ultimoChat->created_at);
            $fecha = $created->isToday() ? 'Hoy' : ($created->isYesterday() ? 'Ayer' : $created->format('Y-m-d'));
            $hora = $created->format('h:i a');
            $item->fecha = $fecha . " " . $hora;
            array_push($data, $item);
        }

        return response()->json([
            "success" => true,
            "message" => "Chats consultados correctamente",
            "data" => $data
        ]);
    }

    /**
     * Obtiene los ultimos mensajes del chat con fk_cliente_tarotista = {id} con skip y take
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMensajesChat($id, Request $request)
    {

        $beforeId = $request->input("before_id");
        $take = $request->input("take", 10);
        $tarotista = $request->attributes->get('tarotista');

        $relacion = ClienteTarotistaModel::where('id', $id)
            ->where('fk_tarotista', $tarotista->id)
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

        $mensajes = $mensajes->orderBy('created_at', 'asc')
            ->take($take)
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Mensajes de chat consultados correctamente",
            "data" => [
                "mensajes" => $mensajes,
                "cliente" => [
                    "nombre" => $relacion->cliente->nombre,
                    "fecha_nacimiento" => $relacion->cliente->fecha_nacimiento,
                    "user" => [
                        "photo" => $relacion->cliente->user->photo
                    ]
                ]
            ]
        ]);
    }

    /**
     * Agrega un nuevo mensaje al chat por parte del tarotista
     * 
     * @param int $id
     * @param App\Http\Requests\Api\Tarotista\Chats\EnviarChatRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarMensajeChat($id, EnviarChatRequest $request)
    {
        $tarotista = $request->attributes->get('tarotista');
        $relacion = ClienteTarotistaModel::where('id', $id)
            ->where('fk_tarotista', $tarotista->id)
            ->first();

        if (!isset($relacion)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró esta relación cliente-tarotista',
            ], 404);
        }

        $chat = new ChatsModel();
        $chat->mensaje = $request->input("mensaje");
        $chat->origen = 2;
        $chat->tipo = 1;
        $chat->fk_cliente_tarotista = $id;
        $chat->save();

        //TODO: Enviar notificacion push al cliente

        return response()->json([
            "success" => true,
            "message" => "Mensaje agregado correctamente",
            "data" => [
                "chat" => $chat
            ]
        ]);
    }
}
