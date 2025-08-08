<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TipoDocumentoSeeder::class);
        
        $this->call(NivelesSeeder::class);
        $this->call(ModalidadesSeeder::class);
        $this->call(LineasInvestigacionSeeder::class);

        $this->call(TiposSolicitudesSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersSeeder::class);

        $this->call(CamposSeeder::class);

        $this->call(FechasSeeder::class);
    }
}
