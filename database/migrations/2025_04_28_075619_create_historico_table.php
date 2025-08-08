<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historico', function (Blueprint $table) {
            $table->id();
            $table->string('periodo_academico')->nullable();
            $table->string('codigo_tg')->nullable();
            $table->string('nivel')->nullable();
            $table->string('estudiante')->nullable();
            $table->string('correo')->nullable();
            $table->string('documento')->nullable();
            $table->string('celular')->nullable();
            $table->string('modalidad')->nullable();
            $table->text('titulo')->nullable();
            $table->string('director')->nullable();
            $table->string('evaluador')->nullable();
            $table->text('autores')->nullable();
            $table->date('inicio_tg')->nullable();
            $table->date('aprobacion_propuesta')->nullable();
            $table->date('final_tg')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico');
    }
};
