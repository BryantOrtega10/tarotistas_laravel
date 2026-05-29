<?php

namespace App\Http\Utils;

use Google\Client;
use Google\Service\AndroidPublisher;

class AppPurchaseVerifier
{
    public static function verifyGooglePurchase(string $productId, string $purchaseToken)
    {
        try {
            // Ruta del archivo JSON de credenciales de Firebase
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('tarotistas-2acb6-68d41fffe32f.json'));

            $client = new Client();
            $client->setAuthConfig(storage_path('tarotistas-2acb6-68d41fffe32f.json'));
            $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

            $service = new AndroidPublisher($client);
            $packageName = 'com.mdccolombia.tarotsabila';
            $purchase = $service->purchases_products->get($packageName, $productId, $purchaseToken);

            return [
                'success' => true,
                'orderId' => $purchase->getOrderId(),
                'purchaseState' => $purchase->getPurchaseState(),
                'consumptionState' => $purchase->getConsumptionState(),
                'acknowledgementState' => $purchase->getAcknowledgementState(),
                'purchaseTimeMillis' => $purchase->getPurchaseTimeMillis(),
                'developerPayload' => $purchase->getDeveloperPayload(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' =>  $e->getMessage(),
            ];
        }
    }
}
