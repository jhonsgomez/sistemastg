<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campo;
use App\Models\TipoSolicitud;



class CamposPracticasSeeder extends Seeder
{
    
    public function run(): void
    {
        
        //Campos para Practicas Fase 0
        $practicas_fase_0 = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        Campo::where('tipo_solicitud_id', $practicas_fase_0->id)->delete();
        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'nombre_completo',
            'label' => 'Nombre completo',
            'type' => 'text',
            'required' => true,
            'instructions' => 'Nombre completo del estudiante.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'correo',
            'label' => 'Correo institucional',
            'type' => 'text',
            'required' => true,
            'instructions' => 'Correo institucional',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'nivel',
            'label' => 'Nivel académico',
            'type' => 'text',
            'required' => true,
            'instructions' => 'Nivel académico',
            
        ]);

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'documento',
            'label' => 'Número de documento',
            'type' => 'number',
            'required' => true,
            'instructions' => 'Número de documento',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'celular',
            'label' => 'Número de celular',
            'type' => 'number',
            'required' => true,
            'instructions' => 'Número de celular',
        ]);
        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'tiene_empresa',
            'label' => '¿Cuenta con empresa?',
            'type' => 'checkbox',
            'required' => true,
            'instructions' => 'Marque esta casilla si ya cuenta con una empresa para realizar sus prácticas.',
        ]);


        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name' => 'hoja_vida',
            'type' => 'file',
            'required' => false, 
            'instructions' => 'Debe subir la hoja de vida si NO cuenta con empresa.',
        ]);

    }
}
