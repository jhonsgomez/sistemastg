<?php

namespace Database\Seeders;

use App\Models\Modalidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidades = [
            'Proyecto de investigación',
            'Desarrollo tecnológico',
            'Monografía',
            'Seminario'
        ];

        foreach ($modalidades as $nombreModalidad) {
            Modalidad::create([
                'nombre' => $nombreModalidad,
                'descripcion' => $nombreModalidad
            ]);
        }
    }
}
