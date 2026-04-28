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
            'token_min' => 1,
            'valor_min' => 100,
            'por_comision' => 10,
            'fk_last_user' => 1,
        ]);
    }
}
