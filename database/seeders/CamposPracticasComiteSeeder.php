<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campo;

class CamposPracticasComiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campo::updateOrCreate(
            [
                'name'              => 'respuesta_comite',
                'tipo_solicitud_id' => 9,
            ],
            [
                'label'        => 'Respuesta del comité',
                'type'         => 'textarea',
                'required'     => 1,
                'placeholder'  => null,
                'instructions' => 'Respuesta del comité a la solicitud de práctica',
            ]
        );
        
        // Restaurar si estaba eliminado lógicamente
        Campo::withTrashed()
            ->where('name', 'respuesta_comite')
            ->where('tipo_solicitud_id', 9)
            ->restore();
    }
}