<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE practicas 
            MODIFY COLUMN estado ENUM(
                'Aprobada',
                'Pendiente',
                'Rechazada',
                'Fase 1',
                'Fase 2',
                'Fase 3',
                'Fase 4',
                'Fase 5',
                'Fase 6',
                'Finalizado'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE practicas 
            MODIFY COLUMN estado ENUM(
                'Aprobada',
                'Pendiente',
                'Rechazada',
                'Fase 1',
                'Fase 2',
                'Fase 3',
                'Fase 4',
                'Fase 5',
                'Finalizado'
            ) NOT NULL
        ");
    }
};