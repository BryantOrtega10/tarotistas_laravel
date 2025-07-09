<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tarotista\Comentarios\ResponderComentarioRequest;
use App\Models\ClienteTarotistaModel;
use App\Models\LlamadasModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ComentariosTarotistaController extends Controller
{
    /**
     * Obtiene los ultimos comentarios del tarotista con skip y take
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerComentarios(Request $request)
    {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);
        $tarotista = $request->attributes->get('tarotista');

        $sub = LlamadasModel::select('fk_cliente_tarotista', DB::raw('MAX(created_at) as fecha_ultima_llamada'))
            ->where("estado_llamada", "=", 4)
            ->whereNotNull("comentario")
            ->groupBy('fk_cliente_tarotista');
        //TODO: Arreglar para que traiga todos los comentarios no solo el ultimo.

        $ultimosComentarios = ClienteTarotistaModel::query()
            ->joinSub($sub, 'ultimos_comentarios', function ($join) {
                $join->on('cliente_tarotista.id', '=', 'ultimos_comentarios.fk_cliente_tarotista');
            })
            ->with([
                'cliente.user:id,name,photo',
                'ultimoComentario',
            ])
            ->where("cliente_tarotista.fk_tarotista", "=", $tarotista->id)
            ->orderBy('ultimos_comentarios.fecha_ultima_llamada', 'desc')
            ->take($take)
            ->skip($skip)
            ->get();

        $data = [];
        foreach ($ultimosComentarios as $itemComentario) {
            $item = new stdClass;
            $item->cliente = $itemComentario->cliente->user;
            $item->comentario = $itemComentario->ultimoComentario->comentario;
            $item->respuesta_com = $itemComentario->ultimoComentario->respuesta_com;
            array_push($data, $item);
        }

        return response()->json([
            "success" => true,
            "message" => "Comentarios consultados correctamente",
            "data" => $data
        ]);
    }


    /**
     * Obtiene un comentario y su respuesta por el id enviado
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerComentarioId($id, Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        $comentario = LlamadasModel::select("id", "comentario", "respuesta_com")
            ->with([
                'cliente_tarotista.cliente.user:id,name,photo',
            ])
            ->whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })
            ->where("llamadas.estado_llamada", "=", 4)
            ->whereNotNull("llamadas.comentario")
            ->where("llamadas.id", "=", $id)
            ->first();

        if (!isset($comentario)) {
            return response()->json([
                'success' => false,
                'message' => 'No existe comentario con este ID',
                'errors' => [
                    'id' => 'No existe comentario con este ID'
                ],
            ], 422);
        }

        return response()->json([
            "success" => true,
            "message" => "Comentario consultado correctamente",
            "data" => [
                "comentario" => $comentario->comentario,
                "respuesta_com" => $comentario->respuesta_com,
                "user" => $comentario->cliente_tarotista->cliente->user ?? null
            ]
        ]);
    }


    /**
     * Responde a un comentario de acuerdo al id enviado
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function responderComentarioId($id, ResponderComentarioRequest $request)
    {
        
        $tarotista = $request->attributes->get('tarotista');

        $comentario = LlamadasModel::whereHas('cliente_tarotista', function ($query) use ($tarotista) {
                $query->where('fk_tarotista', $tarotista->id);
            })            
            ->where("llamadas.estado_llamada", "=", 4)
            ->whereNotNull("llamadas.comentario")
            ->where("llamadas.id", "=", $id)
            ->first();
        
        if (!isset($comentario)) {
            return response()->json([
                'success' => false,
                'message' => 'No existe comentario con este ID',
                'errors' => [
                    'id' => 'No existe comentario con este ID'
                ],
            ], 422);
        }

        $comentario->respuesta_com = $request->input("respuesta");
        $comentario->save();

        return response()->json([
            "success" => true,
            "message" => "Se ha respondido el comentario correctamente",
            "data" => null
        ]);

    }

    
}
