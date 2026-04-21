<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoSolicitud;

class TiposSolicitudPracticasSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['nombre' => 'practicas_fase_1', 'descripcion' => 'Fase 1: Envío de formato F-DC-126'],
            ['nombre' => 'practicas_fase_2', 'descripcion' => 'Fase 2: Pago de modalidad'],
            ['nombre' => 'practicas_fase_3', 'descripcion' => 'Fase 3: Propuesta (ARL, F-DC-127, F-DC-195)'],
            ['nombre' => 'practicas_fase_4', 'descripcion' => 'Fase 4: Documentos de prácticas (F-DC-128, F-DC-129, F-DC-196)'],
            ['nombre' => 'practicas_fase_5', 'descripcion' => 'Fase 5: Finalización'],
        ];

        foreach ($tipos as $tipo) {
            TipoSolicitud::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                [
                    'descripcion' => $tipo['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}