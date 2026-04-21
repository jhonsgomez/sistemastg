<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE practicas MODIFY COLUMN estado ENUM('Aprobada','Pendiente','Rechazada','Fase 1','Fase 2','Fase 3','Fase 4','Fase 5','Finalizado') NOT NULL DEFAULT 'Pendiente'");
    }
    public function down()
    {
        DB::statement("ALTER TABLE practicas MODIFY COLUMN estado ENUM('Pendiente','Aprobada','Rechazada','Fase 1','Fase 2','Finalizado') NOT NULL DEFAULT 'Pendiente'");
    }
};
