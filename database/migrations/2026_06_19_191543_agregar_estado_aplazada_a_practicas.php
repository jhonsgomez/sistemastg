<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE practicas 
            MODIFY estado ENUM(
                'Pendiente',
                'Fase 1',
                'Fase 2',
                'Fase 3',
                'Fase 4',
                'Fase 5',
                'Fase 6',
                'Finalizado',
                'Aplazada',
                'Rechazada'
            ) NOT NULL DEFAULT 'Pendiente'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE practicas 
            SET estado = 'Rechazada' 
            WHERE estado = 'Aplazada'
        ");

        DB::statement("
            ALTER TABLE practicas 
            MODIFY estado ENUM(
                'Pendiente',
                'Fase 1',
                'Fase 2',
                'Fase 3',
                'Fase 4',
                'Fase 5',
                'Fase 6',
                'Finalizado',
                'Rechazada'
            ) NOT NULL DEFAULT 'Pendiente'
        ");
    }
};