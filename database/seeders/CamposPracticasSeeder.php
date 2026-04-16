<?php
namespace Database\Seeders;

use App\Models\Campo;
use App\Models\TipoSolicitud;
use Illuminate\Database\Seeder;

class CamposPracticasSeeder extends Seeder
{

    public function run(): void
    {
        $practicas_fase_0 = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();

        if (! $practicas_fase_0) {
            return;
        }

        Campo::where('tipo_solicitud_id', $practicas_fase_0->id)->delete();

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name'              => 'tiene_empresa',
            'label'             => '¿Cuenta con empresa?',
            'type'              => 'checkbox',
            'required'          => true,
            'order'             => 1,
            'instructions'      => 'Marque si ya cuenta con empresa.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $practicas_fase_0->id,
            'name'              => 'hoja_vida',
            'label'             => 'Hoja de vida',
            'type'              => 'file',
            'required'          => false,
            'order'             => 2,
            'instructions'      => 'Suba la hoja de vida si NO cuenta con empresa.',
        ]);
    }
}
