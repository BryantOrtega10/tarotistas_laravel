<?php

namespace Database\Seeders;

use App\Models\ConfiguracionModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConfiguracionModel::create([
            'precio_min' => 100,
            'por_comision' => 0.1,
            'fk_last_user' => 1,
        ]);
    }
}
