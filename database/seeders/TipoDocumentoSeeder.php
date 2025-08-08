<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDocumento::create([
            'nombre' => 'Tarjeta de identidad',
            'tag' => 'T.I'
        ]);

        TipoDocumento::create([
            'nombre' => 'Cédula de ciudadanía',
            'tag' => 'C.C'
        ]);

        TipoDocumento::create([
            'nombre' => 'Cédula de extranjería',
            'tag' => 'Ext.'
        ]);
    }
}
