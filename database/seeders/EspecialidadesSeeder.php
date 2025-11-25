<?php

namespace Database\Seeders;

use App\Models\EspecialidadesModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EspecialidadesModel::create(['nombre' => 'Visiones']);
        EspecialidadesModel::create(['nombre' => 'Lectura Cartas']);
        EspecialidadesModel::create(['nombre' => 'Lectura Tabaco']);
        EspecialidadesModel::create(['nombre' => 'Amor']);
        EspecialidadesModel::create(['nombre' => 'Dinero']);
    }
}
