<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ClienteTarotistaModel;
use App\Models\LlamadasModel;
use App\Models\TarotistasModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ConsultaTarotistasController extends Controller
{
    /**
     * Sirve para obtener los tarotistas por parte del cliente, tienen prioridad los tarotistas que esten disponibles y la calificacion que tengan.
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerTarotistas(Request $request)
    {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);

        $tarotistas = TarotistasModel::with(["user: id,name,photo"])
            ->where("estado", "=", "3")
            ->orderBy("estado_conexion", "desc")
            ->orderBy("calificacion", "desc")
            ->take($take)
            ->skip($skip)
            ->get();

        $idsTarotistas = $tarotistas->pluck('id');

        $resenas = LlamadasModel::select(DB::raw("COUNT(*) as cuenta_resenas"), "cliente_tarotista.fk_tarotista")
            ->join("cliente_tarotista", "cliente_tarotista.id", "=", "llamadas.fk_cliente_tarotista")
            ->where("llamadas.estado_llamada", "=", 4)
            ->whereIn("cliente_tarotista.fk_tarotista", $idsTarotistas)
            ->groupBy("cliente_tarotista.fk_tarotista")
            ->get()
            ->pluck('cuenta_resenas', 'fk_tarotista');


        $data = $tarotistas->map(function ($tarotista) use ($resenas) {
            $item = new stdClass;
            $item->nombre = $tarotista->nombre;
            $item->pais = $tarotista->pais?->bandera ?? null;
            $item->photo = $tarotista->user->photo;
            $item->estado_conexion = $tarotista->estado_conexion;
            $item->calificacion = $tarotista->calificacion;
            $item->cuentaResenas = $resenas[$tarotista->id] ?? 0;

            return $item;
        })->values();

        return response()->json([
            "success" => true,
            "message" => "Tarotistas consultados correctamente",
            "data" => $data
        ]);
    }

    /**
     * Sirve para obtener el perfil de un tarotista por parte de los clientes
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerTarotistaxId(int $id) {

        $tarotista = TarotistasModel::with(["user: id,name,photo"])
            ->where("estado", "=", "3")
            ->where("id","=",$id)
            ->first();

        $resumenLlamadas = LlamadasModel::select(DB::raw("SUM(tiempo_mins) as sum_mins"), DB::raw("COUNT(*) as cuenta_resenas"), "cliente_tarotista.fk_tarotista")
            ->join("cliente_tarotista", "cliente_tarotista.id", "=", "llamadas.fk_cliente_tarotista")
            ->where("llamadas.estado_llamada", "=", 4)
            ->where("cliente_tarotista.fk_tarotista", "=" , $id)
            ->groupBy("cliente_tarotista.fk_tarotista")
            ->first();

        $resumenClientes = ClienteTarotistaModel::select(DB::raw("COUNT(*) as cuenta_clientes"))
            ->where("cliente_tarotista.fk_tarotista", "=" , $id)
            ->first();


        return response()->json([
            "success" => true,
            "message" => "Perfil del tarotista consultado correctamente",
            "data" => [
                "photo" => $tarotista->user->photo,
                "nombre" => $tarotista->nombre,
                "pais" => $tarotista->pais?->bandera,
                "sum_mins" => $resumenLlamadas->sum_mins ?? 0,
                "estado_conexion" => $tarotista->estado_conexion,
                "descripcion_corta" => $tarotista->descripcion_corta,
                "calificacion" => $tarotista->calificacion,
                "cuenta_resenas" => $resumenLlamadas->cuenta_resenas ?? 0,
                "cuenta_clientes" => $resumenClientes->cuenta_clientes ?? 0,
                "anios_exp" => $tarotista->anios_exp,
                "horario" => $tarotista->horario,
                "especialidades" => $tarotista->especialidades
            ]
        ]);      

    }

    /**
     * Obtiene los comentarios mas recientes de un tarotista
     * 
     * @param int $id
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtenerComentarios(int $id, Request $request) {
        $skip = $request->input("skip", 0);
        $take = $request->input("take", 10);

        $comentarios = LlamadasModel::with(["cliente_tarotista.cliente.user:id,name,photo"])
            ->where("estado_llamada", "=", 4)
            ->whereNotNull("comentario")
            ->whereHas('cliente_tarotista', function ($query) use ($id) {
                $query->where('fk_tarotista', $id);
            })
            ->orderBy("calificacion", "desc")
            ->take($take)
            ->skip($skip)
            ->get();


        $data = $comentarios->map(function ($comentario) {
            $item = new stdClass;
            $item->cliente = $comentario->cliente_tarotista->cliente->user;
            $item->calificacion = $comentario->calificacion;
            $item->fecha = $comentario->created_at;
            $item->comentario = $comentario->comentario;
            $item->respuesta_com = $comentario->respuesta_com;
            return $item;
        })->values();
      
        return response()->json([
            "success" => true,
            "message" => "Comentarios del tarotista consultados correctamente",
            "data" => $data
        ]);
    }

    
}
