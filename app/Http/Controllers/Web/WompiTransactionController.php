<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionModel;
use App\Models\WompiTransactionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                "message" => 'Transacción no encontrada con id:' . $uuid,
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


        return view('transacciones.generarForm', [
            'wompiFormData' => $wompiFormData,
            'paquete' => $transaction->paquete
        ]);
    }


    public function respuesta(Request $request)
    {
        $transactionId = $request->query('id');

        if (!$transactionId) {
            return view('transacciones.error', [
                'error' => 'No se recibió el ID de la transacción'
            ]);
        }

        $isWompiTest = env("WOMPI_TEST");
        $url = "https://production.wompi.co/v1/transactions/" . $transactionId;

        if ($isWompiTest) {
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



        return view('transacciones.respuesta', [
            "wompiData" => $wompiData,
            "transaction" => $transaction,
        ]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();

        $properties = $payload['signature']['properties'] ?? [];
        $data = $payload['data'] ?? [];
        $timestamp = $payload['timestamp'] ?? null;
        $checksumSignature = $payload['signature']['checksum'] ?? "";

        //Paso 1
        $concatenated = '';
        foreach ($properties as $property) {
            $keys = explode('.', $property);
            $value = $data;
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    $value = null;
                    break;
                }
                $value = $value[$key];
            }

            $concatenated .= (string) $value;
        }
        //Paso 2
        $concatenated .= $timestamp;
        //Paso 3
        $concatenated .= env('WOMPI_EVENTS_KEY');
        //Paso 4
        $checksum = hash("sha256", $concatenated);

        if (!hash_equals($checksumSignature, $checksum)) {
            return response()->json(['error' => 'Firma no valida'], 403);
        }


        $event = $payload['event'] ?? null;

        if ($event !== 'transaction.updated') {
            return response()->json(['message' => 'Evento ignorado']);
        }

        $data = $payload['data']['transaction'] ?? null;

        if (!$data) {
            return response()->json(['error' => 'Sin datos'], 400);
        }

        $reference = $data['reference'];

        $transaction = WompiTransactionsModel::where('uuid', $reference)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transacción no encontrada'], 404);
        }

        // Mapear estado Wompi
        $statusMap = [
            'PENDING' => 0,
            'APPROVED' => 2,
            'DECLINED' => 3,
            'VOIDED' => 4,
            'ERROR' => 5,
        ];

        $transaction->status = $statusMap[$data['status']] ?? 5;

        // Guardar respuesta completa
        $transaction->last_wompi_response = json_encode($data, JSON_UNESCAPED_UNICODE);

        $transaction->save();

        return response()->json(['success' => true]);
    }

    public function webhookSandbox(Request $request)
    {
        $payload = $request->all();
        Log::info('Wompi Webhook Payload:', $payload);
        Log::info('Wompi Webhook Debug', [
            'event' => $payload['event'] ?? null,
            'reference' => $payload['data']['transaction']['reference'] ?? null,
            'status' => $payload['data']['transaction']['status'] ?? null,
            'transaction_id' => $payload['data']['transaction']['id'] ?? null,
            'timestamp' => $payload['timestamp'] ?? null,
            'full_payload' => $payload
        ]);
        $properties = $payload['signature']['properties'] ?? [];
        $data = $payload['data'] ?? [];
        $timestamp = $payload['timestamp'] ?? null;
        $checksumSignature = $payload['signature']['checksum'] ?? "";

        //Paso 1
        $concatenated = '';
        foreach ($properties as $property) {
            $keys = explode('.', $property);
            $value = $data;
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    $value = null;
                    break;
                }
                $value = $value[$key];
            }

            $concatenated .= (string) $value;
        }
        //Paso 2
        $concatenated .= $timestamp;
        //Paso 3
        $concatenated .= env('WOMPI_EVENTS_KEY');
        //Paso 4
        $checksum = hash("sha256", $concatenated);

        if (!hash_equals($checksumSignature, $checksum)) {
            return response()->json(['error' => 'Firma no valida'], 403);
        }


        $event = $payload['event'] ?? null;

        if ($event !== 'transaction.updated') {
            return response()->json(['message' => 'Evento ignorado']);
        }

        $data = $payload['data']['transaction'] ?? null;

        if (!$data) {
            return response()->json(['error' => 'Sin datos'], 400);
        }

        $reference = $data['reference'];

        $transaction = WompiTransactionsModel::where('uuid', $reference)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transacción no encontrada'], 404);
        }

        // Mapear estado Wompi
        $statusMap = [
            'PENDING' => 0,
            'APPROVED' => 2,
            'DECLINED' => 3,
            'VOIDED' => 4,
            'ERROR' => 5,
        ];

        $transaction->status = $statusMap[$data['status']] ?? 5;

        // Guardar respuesta completa
        $transaction->last_wompi_response = json_encode($data, JSON_UNESCAPED_UNICODE);

        $transaction->save();

        if($transaction->status === 2){
            //Agregar tokens a los clientes
            $cliente = $transaction->cliente;
            $cliente->tokens += $transaction->tokens;
            $cliente->save();
        }

        

        return response()->json(['success' => true]);
    }
}
