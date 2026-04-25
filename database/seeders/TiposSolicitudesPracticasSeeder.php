<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoSolicitud;
use Illuminate\Support\Carbon;

class TiposSolicitudesPracticasSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['nombre' => 'practicas_fase_0', 'descripcion' => 'Solicitud inicial de prácticas empresariales'],
            ['nombre' => 'practicas_fase_1', 'descripcion' => 'Envío de formato F-DC-126'],
            ['nombre' => 'practicas_fase_2', 'descripcion' => 'Pago de modalidad'],
            ['nombre' => 'practicas_fase_3', 'descripcion' => 'Propuesta de prácticas'],
            ['nombre' => 'practicas_fase_4', 'descripcion' => 'Documentos de prácticas'],
            ['nombre' => 'practicas_fase_5', 'descripcion' => 'Finalización de prácticas'],
        ];

        foreach ($tipos as $tipo) {
            TipoSolicitud::updateOrCreate(
                ['nombre' => $tipo['nombre']],
                [
                    'descripcion' => $tipo['descripcion'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}