<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campo;

class CamposPracticasComiteSeeder extends Seeder
{
    public function run(): void
    {
        // Este seeder ya no es necesario porque el campo se crea en CamposPracticasSeeder
        // Solo restauramos si existe y está eliminado
        Campo::withTrashed()
            ->where('name', 'respuesta_comite')
            ->where('tipo_solicitud_id', 9)
            ->restore();
    }
}