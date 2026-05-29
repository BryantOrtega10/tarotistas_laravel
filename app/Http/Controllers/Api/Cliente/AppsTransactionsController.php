<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Utils\AppPurchaseVerifier;
use App\Models\AppsTransactions;
use App\Models\PaquetesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AppsTransactionsController extends Controller
{
    public function create(Request $request)
    {
        $payload = $request->all();
        Log::info('App Webhook Payload:', $payload);
        $cliente = $request->attributes->get('cliente');

        DB::beginTransaction();
        try {

            $transaction = AppsTransactions::lockForUpdate()
                ->where("purchase_token", $payload["purchase_token"])
                ->first();

            if (!$transaction) {
                $paquete = PaquetesModel::where("estado", 1);
                if ($payload["platform"] == "android-playstore") {
                    $paquete->where("google_token", $payload["product_id"]);
                }
                if ($payload["platform"] == "ios-appstore") {
                    $paquete->where("apple_token", $payload["product_id"]);
                }
                $paquete = $paquete->first();

                $transaction = AppsTransactions::create([
                    "platform" => $payload["platform"],
                    "purchase_token" => $payload["purchase_token"],
                    "order_id" => $payload["order_id"],
                    "tokens" => $paquete->tokens,
                    "valor" => $paquete->valor,
                    "fk_paquete" => $paquete->id,
                    "status" => 0,
                    "fk_cliente" => $cliente->id
                ]);
            }

            if ($payload["platform"] == "android-playstore") {

                $responseGoogle = AppPurchaseVerifier::verifyGooglePurchase($payload["product_id"], $payload["purchase_token"]);
                if (!$responseGoogle['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => "No se pudo validar el pago",
                        'data' => $responseGoogle
                    ]);
                }

                if ($responseGoogle['purchaseState'] === 0 && $transaction->status === 0) {
                    $transaction->status = 2;
                    $transaction->save();
                    $cliente = $transaction->cliente;
                    $cliente->increment('tokens', $transaction->tokens);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                "message" => "Transacción procesada correctamente",
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return response()->json([
                'success' => false
            ]);
        }

        /*$paquete = PaquetesModel::where("estado", 1);
        if ($payload["platform"] == "android-playstore") {
            $paquete->where("google_token", $payload["product_id"]);
        } else if ($payload["platform"] == "ios-appstore") {
            $paquete->where("apple_token", $payload["product_id"]);
        }
        $paquete = $paquete->first();


        $transaction = AppsTransactions::where("order_id", $payload["order_id"])
            ->where("platform", $payload["platform"])
            ->first();
        if (!isset($transaction)) {
            $transaction = new AppsTransactions();
            $transaction->platform = $payload["platform"];
            $transaction->purchase_token = $payload["purchase_token"];
            $transaction->order_id = $payload["order_id"];
            $transaction->tokens = $paquete->tokens;
            $transaction->valor = $paquete->valor;
            $transaction->fk_paquete = $paquete->id;
            $transaction->status = 0;
            $transaction->fk_cliente = $cliente->id;
            $transaction->save();
        }


        if ($payload["platform"] == "android-playstore") {
            $responseGoogle = AppPurchaseVerifier::verifyGooglePurchase($payload["product_id"], $payload["purchase_token"]);
            if (!$responseGoogle['success']) {
                return response()->json([
                    'success' => false,
                    'message' => "No se pudo validar el pago",
                    'data' => $responseGoogle
                ], 400);
            }
            if ($responseGoogle['purchaseState'] === 0 && $transaction->status === 0) {
                //Compra valida y la transaccion estaba en otro estado
                $transaction->status = 2; //APROBADA
                $transaction->save();
                $cliente = $transaction->cliente;
                $cliente->tokens += $transaction->tokens;
                $cliente->save();
            } else if ($responseGoogle['purchaseState'] === 1) {
                $transaction->status = 4; //CANCELADA GOOGLE
                $transaction->save();
            }
        }


        return response()->json([
            "success" => true,
            "message" => "Transacción procesada correctamente",

        ]);*/
    }
}
