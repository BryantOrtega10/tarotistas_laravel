<?php

namespace Database\Seeders;

use App\Models\BancosModel;
use App\Models\PaisesModel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserAdminSeeder::class,
            ConfigDefaultSeeder::class,
            EspecialidadesSeeder::class,
            PaisesSeeder::class,
            BancosSeeder::class,
            TarotistasPruebaSeeder::class,
        ]);
      
    }
}
