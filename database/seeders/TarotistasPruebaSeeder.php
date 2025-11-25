<?php

namespace Database\Seeders;

use App\Models\EspecialidadesTatoristaModel;
use App\Models\TarotistasModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TarotistasPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTarotista1 = User::create([
            'name' => 'Tarotista 1',
            'email' => 'tarotista1@test.com',
            'password' => Hash::make("1900"),
            'role' => 'tarotista'
        ]);

        $userTarotista2 = User::create([
            'name' => 'Tarotista 2',
            'email' => 'tarotista2@test.com',
            'password' => Hash::make("1900"),
            'role' => 'tarotista'
        ]);

        $userTarotista3 = User::create([
            'name' => 'Tarotista 3',
            'email' => 'tarotista3@test.com',
            'password' => Hash::make("1900"),
            'role' => 'tarotista'
        ]);

        TarotistasModel::create([
            'nombre' => $userTarotista1->name,
            'estado' => 1,
            'fk_user' => $userTarotista1->id
        ]);

        $tarotista2 = TarotistasModel::create([
            'nombre' => $userTarotista2->name,
            'estado' => 2,
            'descripcion_corta' => 'Esta es una descripcion de prueba',
            'horario' => '9:00 am - 6:00 pm',
            'anios_exp' => '5',
            'fk_pais' => 1,
            'fk_user' => $userTarotista2->id
        ]);

        EspecialidadesTatoristaModel::create([
            'fk_especialidad' => 1,
            'fk_tarotista' => $tarotista2->id,
        ]);
        EspecialidadesTatoristaModel::create([
            'fk_especialidad' => 2,
            'fk_tarotista' => $tarotista2->id,
        ]);
        EspecialidadesTatoristaModel::create([
            'fk_especialidad' => 3,
            'fk_tarotista' => $tarotista2->id,
        ]);


         $tarotista3 = TarotistasModel::create([
            'nombre' => $userTarotista3->name,
            'estado' => 3,
            'descripcion_corta' => 'PRUEBA Esta es una descripcion de prueba',
            'horario' => '10:00 am - 4:00 pm',
            'anios_exp' => '2',
            'fk_pais' => 1,
            'fk_user' => $userTarotista3->id,
            'tipo_cuenta' => '1',
            'cuenta' => '3154861174',
            'fk_banco' => 3,
        ]);

        EspecialidadesTatoristaModel::create([
            'fk_especialidad' => 4,
            'fk_tarotista' => $tarotista3->id,
        ]);
        EspecialidadesTatoristaModel::create([
            'fk_especialidad' => 5,
            'fk_tarotista' => $tarotista3->id,
        ]);
  

        

    }
}
