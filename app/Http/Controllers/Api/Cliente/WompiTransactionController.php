<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PaquetesModel;
use App\Models\WompiTransactionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class WompiTransactionController extends Controller
{
    public function index(Request $request)
    {

        $cliente = $request->attributes->get('cliente');

        $paquete = PaquetesModel::where("id", "=", $request->query("paquete"))->first();
        
        $transaction = new WompiTransactionsModel();
        $uuid = Str::uuid();
        $transaction->uuid = $uuid;
        $transaction->tokens = $paquete->tokens;
        $transaction->valor = $paquete->valor;
        $transaction->fk_paquete = $paquete->id;
        $transaction->status = 0;
        $transaction->fk_cliente = $cliente->id;
        $transaction->save();

        
        $url = URL::temporarySignedRoute(
            'web.wompi.generar',
            now()->addMinutes(10),
            ['uuid' => $uuid]
        );

        return response()->json([
            "success" => true,
            "message" => "Link de pago generado correctamente",
            "data" => [
                "url_pago" => $url
            ]
        ]);
    }
}
