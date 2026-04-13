<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campos', function (Blueprint $table) {
            $table->foreignId('tipo_solicitud_practica_id')
                  ->nullable()
                  ->after('tipo_solicitud_id')
                  ->constrained('tipos_solicitudes_practicas')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('campos', function (Blueprint $table) {
            $table->dropForeign(['tipo_solicitud_practica_id']);
            $table->dropColumn('tipo_solicitud_practica_id');
        });
    }
};