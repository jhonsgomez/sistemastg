<?php

namespace Database\Seeders;

use App\Models\TipoSolicitud;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposSolicitudesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoSolicitud::create([
            'nombre' => 'solicitud_banco',
            'descripcion' => 'Propuesta para el banco de ideas',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_0',
            'descripcion' => 'Solicitud Fase 0 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_1',
            'descripcion' => 'Solicitud Fase 1 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_2',
            'descripcion' => 'Solicitud Fase 2 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_3',
            'descripcion' => 'Solicitud Fase 3 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_4',
            'descripcion' => 'Solicitud Fase 4 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_5',
            'descripcion' => 'Solicitud Fase 5 para proyecto de grado',
        ]);

        TipoSolicitud::create([
            'nombre' => 'fase_final',
            'descripcion' => 'Solicitud Fase Final para proyecto de grado',
        ]);
    }
}
