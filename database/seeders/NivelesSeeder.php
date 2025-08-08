<?php

namespace Database\Seeders;

use App\Models\Nivel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nivel::create([
            'nombre' => 'Tecnológico',
            'descripcion' => 'Ciclo tecnológico del programa académico',
        ]);

        Nivel::create([
            'nombre' => 'Profesional',
            'descripcion' => 'Ciclo profesional del programa académico',
        ]);
    }
}
