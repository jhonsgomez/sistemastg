<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->enum('estado', ['Pendiente', 'Aprobada', 'Rechazada', 'Fase 1', 'Fase 2', 'Finalizado'])
                  ->default('Pendiente')
                  ->after('data');
            $table->boolean('vencido')->default(false)->after('estado');
            $table->boolean('deshabilitado')->default(false)->after('vencido');
        });
    }

    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'vencido', 'deshabilitado']);
        });
    }
};