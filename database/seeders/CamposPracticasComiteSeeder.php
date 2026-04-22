<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CamposPracticasComiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\Campo::firstOrCreate(
            [
                'name'              => 'respuesta_comite',
                'tipo_solicitud_id' => 9,
            ],
            [
                'label'        => 'Respuesta del comité',
                'type'         => 'textarea',
                'required'     => 1,
                'placeholder'  => null,
                'instructions' => null,
            ]
        );
    }
}
