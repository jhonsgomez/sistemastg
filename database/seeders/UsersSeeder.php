<?php

namespace Database\Seeders;

use App\Models\Nivel;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;

class UsersSeeder extends Seeder
{
    public $nombre_programa;
    public $correo_programa;
    public $correo_coordinacion;

    public function __construct()
    {
        $this->nombre_programa = env('NOMBRE_PROGRAMA');
        $this->correo_programa = env('CORREO_SISTEMAS');
        $this->correo_coordinacion = env('CORREO_COORDINACION');
    }

    public function run(): void
    {
        $super_admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmintrabajosdegrado@correo.uts.edu.co',
            'password' => Hash::make(''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $super_admin->assignRole('super_admin');

        $admin = User::create([
            'name' => 'Comité - ' . ucwords($this->nombre_programa),
            'email' => $this->correo_programa,
            'password' => Hash::make(''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $admin->assignRole('admin');

        $coordinador = User::create([
            'name' => 'Coordinador - ' . ucwords($this->nombre_programa),
            'email' => $this->correo_coordinacion,
            'password' => Hash::make(''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $coordinador->assignRole('coordinador');

        $tipo_documento = TipoDocumento::findOrFail(2);

        // ======= DOCENTES =======

        // Cargar el archivo Excel
        $filePath = public_path('formatos/listado_docentes.csv');

        // Leer el archivo CSV usando League CSV
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        // Obtener todas las filas como un array asociativo
        $records = $csv->getRecords();

        foreach ($records as $row) {
            $nombre = $row['Nombre'];
            $correo = $row['Correo'];
            $documento = $row['Documento'];

            // Crear el usuario
            $docente = User::create([
                'name' => $nombre,
                'email' => $correo,
                'tipo_documento_id' => $tipo_documento->id,
                'nro_documento' => $documento,
                'password' => Hash::make(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar el rol de 'docente'
            $docente->assignRole('docente');
        }
    }
}
