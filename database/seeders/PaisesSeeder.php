<?php

namespace Database\Seeders;

use App\Models\PaisesModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaisesModel::create(['nombre' => 'COLOMBIA', 'bandera' => '']);
        PaisesModel::create(['nombre' => 'PANAMA', 'bandera' => '']);
        PaisesModel::create(['nombre' => 'ESPAÑA', 'bandera' => '']);

    }
}
