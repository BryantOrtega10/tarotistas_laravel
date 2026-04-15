<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionModel;
use App\Models\WompiTransactionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WompiTransactionController extends Controller
{
    public function generarForm($uuid, Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                "success" => false,
                "message" => 'Firma inválida o URL modificada',
            ], 403);
        }


        $transaction = WompiTransactionsModel::where("uuid", "=", $uuid)->first();
        if (!isset($transaction)) {
            return response()->json([
                "success" => false,
                "message" => 'Transacción no encontrada con id:'.$uuid,
            ], 403);
        }
        $transaction->status = 1; //Pagando
        $transaction->save();

        $referencia = $transaction->uuid;
        $monto = $transaction->valor . "00";
        $moneda = "COP";
        $secreto = env("WOMPI_INTEGRITY_KEY");

        $firmaIntegridad = $referencia . $monto . $moneda . $secreto;
        $firmaHasheada = hash("sha256", $firmaIntegridad);
        
        $cliente = $transaction->cliente;


        $wompiFormData = [
            "public-key" => env("WOMPI_PUBLIC_KEY"),
            "currency" => $moneda,
            "amount-in-cents" => $monto,
            "reference" => $referencia,
            "signature" => $firmaHasheada,
            "redirect-url" => route('web.wompi.respuesta'),
            "customer-data" => [
                "email" => $cliente->user->email,
                "full-name" => $cliente->nombre
            ]
        ];


        return view('transacciones.generarForm',[
            'wompiFormData' => $wompiFormData,
            'paquete' => $transaction->paquete
        ]);
    }


    public function respuesta(Request $request)
    {
        $transactionId = $request->query('id');

        if (!$transactionId) {
            return view('transacciones.respuesta', [
                'error' => 'No se recibió el ID de la transacción'
            ]);
        }

        $isWompiTest = env("WOMPI_TEST");
        $url = "https://production.wompi.co/v1/transactions/" . $transactionId;

        if ($isWompiTest === "true") {
            $url = "https://sandbox.wompi.co/v1/transactions/" . $transactionId;
        }


        $response = Http::get($url);

        if (!$response->successful()) {
            return view('transacciones.respuesta', [
                'error' => 'Error consultando Wompi',
                'details' => $response->body()
            ]);
        }
        $responseJson = $response->json();
        $wompiData = $responseJson['data'];
        $reference = $wompiData['reference'];

        $transaction = WompiTransactionsModel::where("uuid", "=", $reference)->first();
        if (!isset($transaction)) {
            return response()->json([
                "success" => false,
                "message" => 'Transacción no encontrada',
            ], 403);
        }


        return view('transacciones.respuesta', $wompiData);
    }
}
