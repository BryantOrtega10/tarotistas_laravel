<?php

namespace Database\Seeders;

use App\Models\BancosModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BancosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BancosModel::create([
            'nombre' => 'BANCO DAVIENDA',
            'ap_tipo_cuenta' => '1',
            'fk_pais' => '1',
        ]);
        BancosModel::create([
            'nombre' => 'BANCO BANCOLOMBIA',
            'ap_tipo_cuenta' => '1',
            'fk_pais' => '1',
        ]);
        BancosModel::create([
            'nombre' => 'NEQUI',
            'ap_tipo_cuenta' => '0',
            'fk_pais' => '1',
        ]);
        BancosModel::create([
            'nombre' => 'DAVIPLATA',
            'ap_tipo_cuenta' => '0',
            'fk_pais' => '1',
        ]);
    }
}
