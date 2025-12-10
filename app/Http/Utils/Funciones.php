<?php

namespace App\Http\Utils;

use Illuminate\Support\Facades\Storage;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Funciones
{
    public static function imagenBase64($base64_string, $output_file)
    {
        $base64_string = str_replace('data:image/png;base64,', '', $base64_string);
        $base64_string = str_replace(' ', '+', $base64_string);
        Storage::disk('local')->put($output_file, base64_decode($base64_string));
        return $output_file;
    }
    public static function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $projectId = 'tarotistas-2acb6'; // Cambia esto por tu ID de Firebase
        $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

        // Ruta del archivo JSON de credenciales de Firebase
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('tarotistas-2acb6-68d41fffe32f.json'));

        // Obtener el token de acceso
        $auth = ApplicationDefaultCredentials::getCredentials(['https://www.googleapis.com/auth/firebase.messaging']);
        $authHttp = $auth->fetchAuthToken();
        $accessToken = $authHttp['access_token'];
        $formattedData = [];
        foreach ($data as $key => $value) {
            $formattedData[$key] = strval($value); // Convierte todo a string
        }

        // Datos del mensaje
        $message = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body
                ],
                'data' => $formattedData,
                "android" => [
                    "priority" => "high"
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10"
                    ]
                ]
            ]
        ];

        try {
            $client = new Client();
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type'  => 'application/json'
                ],
                'json' => $message
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null
            ];
        }
    }
}
