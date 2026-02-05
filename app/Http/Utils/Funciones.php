<?php

namespace App\Http\Utils;

use Illuminate\Support\Facades\Storage;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Funciones
{
    public static function resizeImage($directorio, $nombre, $prefijo, $ancho, $alto)
    {
        $rutaImagenOriginal = $directorio . $nombre;
        $tamanio = getimagesize($rutaImagenOriginal);
        $width_gen = $tamanio[0];
        $height_gen = $tamanio[1];

        if ($width_gen >= $height_gen) {
            $alto = ($ancho / $width_gen) * $height_gen;
        } else {
            $ancho = ($alto / $height_gen) * $width_gen;
        }

        $image_type = mime_content_type($rutaImagenOriginal);
        if ($image_type == "image/gif") {
            $img_original = imagecreatefromgif($rutaImagenOriginal);
            imagealphablending($img_original, false);
            imagesavealpha($img_original, true);
        }
        if ($image_type == "image/jpeg") {
            $img_original = imagecreatefromjpeg($rutaImagenOriginal);
        }
        if ($image_type == "image/png") {
            $img_original = imagecreatefrompng($rutaImagenOriginal);
            imagealphablending($img_original, false);
            imagesavealpha($img_original, true);
        }

        $max_ancho = $ancho;
        $max_alto = $alto;
        list($ancho, $alto) = getimagesize($rutaImagenOriginal);
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;
        if (($ancho <= $max_ancho) && ($alto <= $max_alto)) { //Si ancho 
            $ancho_final = $ancho;
            $alto_final = $alto;
        } elseif (($x_ratio * $alto) < $max_alto) {
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        } else {
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }
        $tmp = imagecreatetruecolor($ancho_final, $alto_final);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
        imagecolortransparent($tmp);
        imagedestroy($img_original);

        $nRuta = $directorio . $prefijo . "_" . $nombre;
        imagepng($tmp, $nRuta, 0);
        unlink($rutaImagenOriginal);
        return $nRuta;
    }

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
