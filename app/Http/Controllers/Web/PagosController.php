<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Pagos\GenerarPagoRequest;
use App\Models\BancosModel;
use App\Models\PagosModel;
use App\Models\TarotistasModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagosController extends Controller
{

    public function lista()
    {
        return view('pagos.lista');
    }

    public function datatableAjax(Request $request)
    {
        $tarotistas = TarotistasModel::select(
            "tarotistas.*",
            "users.provider",
            "users.email",
            "paises.nombre as pais_n",
            DB::raw("(SELECT pagos.created_at FROM pagos WHERE fk_tarotista = tarotistas.id order by pagos.created_at desc limit 1) as fecha_ultimo_pago")
        )
            ->join("users", "users.id", "=", "tarotistas.fk_user")
            ->join("paises", "paises.id", "=", "tarotistas.fk_pais")
            ->where("tarotistas.estado", ">=", 2)
            ->where("tarotistas.saldo", ">", 0);

        if ($request->has('search') && $request->input('search')['value']) {
            $searchTxt = $request->input('search')['value'];
            $tarotistas->where(function ($query) use ($searchTxt) {
                $query->where("tarotistas.nombre", "like", "%{$searchTxt}%")
                    ->orWhere("users.email", "like", "%{$searchTxt}%")
                    ->orWhere("users.provider", "like", "%{$searchTxt}%")
                    ->orWhere("paises.nombre", "like", "%{$searchTxt}%")
                    ->orWhereRaw("CASE 
                                    WHEN tarotistas.estado = 1 THEN 'En Registro' 
                                    WHEN tarotistas.estado = 2 THEN 'Esperando aprobación' 
                                    WHEN tarotistas.estado = 3 THEN 'Activado' 
                                    WHEN tarotistas.estado = 4 THEN 'Rechazado' 
                                    ELSE 'Desconocido'
                                END LIKE '%{$searchTxt}%'");
            });
        }

        if ($request->has('order')) {
            $column = $request->input('order')[0]['column'];
            $direction = $request->input('order')[0]['dir'];
            switch ($column) {
                case '0':
                    $tarotistas->orderBy("tarotistas.nombre", $direction);
                    break;
                case '1':
                    $tarotistas->orderBy("users.email", $direction);
                    break;
                case '2':
                    $tarotistas->orderBy("tarotistas.fk_pais", $direction);
                    break;
                case '3':
                    $tarotistas->orderBy("tarotistas.saldo", $direction);
                    break;
                case '4':
                    $tarotistas->orderBy("fecha_ultimo_pago", $direction);
                    break;
            }
        } else {
            $tarotistas->orderBy("tarotistas.estado", "ASC");
        }

        $totalRecords = $tarotistas->count();
        $tarotistas = $tarotistas->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        $filteredRecords = array();

        foreach ($tarotistas as $tarotista) {
            $filteredRecord = array();
            $filteredRecord["nombre"] = $tarotista->nombre;
            $filteredRecord["email"] = $tarotista->user->email;
            $filteredRecord["pais"] = $tarotista->pais?->nombre ?? "";

            $filteredRecord["saldo"] = "$" . number_format($tarotista->saldo, 0, ".", ".");
            $filteredRecord["fecha_ultimo_pago"] = $tarotista->fecha_ultimo_pago ?? "Sin Pagos";

            $filteredRecord["accion"]["href"] = route('pagos.generar', ['idTarotista' => $tarotista->id]);
            $filteredRecord["accion"]["text"] = "Pagos";


            array_push($filteredRecords, $filteredRecord);
        }


        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $filteredRecords
        ]);
    }

    public function mostrarGenerar($idTarotista)
    {
        $pagos = PagosModel::where("fk_tarotista","=",$idTarotista)->orderBy("created_at","DESC")->get();
        $tarotista = TarotistasModel::find($idTarotista);
        $bancos = BancosModel::all();
        return view('pagos.generar', compact('pagos','tarotista', 'bancos'));
    }

    public function generar($idTarotista, GenerarPagoRequest $request)
    {
        $pago = new PagosModel();
        $pago->valor = $request->input("valor");
        $pago->descripcion = $request->input("descripcion");
        $pago->fk_entry_user = Auth::user()->id;
        $pago->fk_tarotista = $idTarotista;
        $pago->save();

        return redirect(route('pagos.generar',['idTarotista' => $idTarotista]))->with('message', 'El pago fue agregado');
    }
    
}
