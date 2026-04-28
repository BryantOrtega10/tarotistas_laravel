<?php

namespace Database\Seeders;

use App\Models\ChatsModel;
use App\Models\ClientesModel;
use App\Models\ClienteTarotistaModel;
use App\Models\LlamadasModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarotistaId = 4;
        $initialId = 11;
        for ($i = $initialId; $i < $initialId + 30; $i++) {
            $user = User::create([
                'name' => 'Cliente Prueba '.$i,
                'email' => 'test-'.$i.'@test-cliente.com',
                'password' => Hash::make("1900"),
                'role' => 'cliente',
                'photo' => '1764108171_photo.jpg'
            ]);

            $client = ClientesModel::create([
                'nombre' => 'Cliente '.$i.' Prueba',
                'fecha_nacimiento' => '1994-10-10',
                'tokens' => 10,
                'fk_user' => $user->id,
            ]);

            $relation = ClienteTarotistaModel::create([
                'mensajes_gratis' => '3',
                'fk_cliente' => $client->id,
                'fk_tarotista' => $tarotistaId,
            ]);

            ChatsModel::create([
                "mensaje" => 'Hola estoy interesado en tus servicios '.$i,
                "origen" => '1',
                "tipo" => '1',
                "leido" => '2025-11-26 15:38',
                "fk_cliente_tarotista" => $relation->id,
            ]);

            ChatsModel::create([
                "mensaje" => 'Hola, en que puedo ayudarte? '.$i,
                "origen" => '2',
                "tipo" => '1',
                "leido" => '2025-11-26 15:40',
                "fk_cliente_tarotista" => $relation->id,
            ]);

            ChatsModel::create([
                "mensaje" => 'Quiero una lectura de cartas puede ser ya? '.$i,
                "origen" => '1',
                "tipo" => '1',
                "fk_cliente_tarotista" => $relation->id,
            ]);

            LlamadasModel::create([
                'fecha_inicio' => '2025-11-26 16:00',
                'fecha_fin' => '2025-11-26 16:30',
                'tarifa_valor_min' => '100',
                'tarifa_token_min' => '1',
                'por_comision' => '0.10',
                'tiempo_mins' => '30',
                'subtotal' => '3000',
                'comision' => '300',
                'total' => '2700',
                'estado_llamada' => '4',
                'estado_pago_tar' => '0',
                'respuesta_payu' => '',
                'calificacion' => '4.5',
                'comentario' => 'Buena lectura espero tener mas!',
                'respuesta_com' => '',
                'fk_cliente_tarotista' => $relation->id,
            ]);
        }
    }
}
