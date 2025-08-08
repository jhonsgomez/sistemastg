<?php

namespace Database\Seeders;

use App\Models\LineaInvestigacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LineasInvestigacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LineaInvestigacion::create([
            'nombre' => 'Arquitectura y diseño de software',
            'descripcion' => 'Línea de investiacion relacionada a Arquitectura y diseño de software'
        ]);

        LineaInvestigacion::create([
            'nombre' => 'Internet de las cosas',
            'descripcion' => 'Línea de investiacion relacionada a Internet de las cosas'
        ]);

        LineaInvestigacion::create([
            'nombre' => 'Realidad aumentada',
            'descripcion' => 'Línea de investiacion relacionada a Realidad aumentada'
        ]);

        LineaInvestigacion::create([
            'nombre' => 'Transformación digital',
            'descripcion' => 'Línea de investiacion relacionada a Transformación digital'
        ]);
    }
}
