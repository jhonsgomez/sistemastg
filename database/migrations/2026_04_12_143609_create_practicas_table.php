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
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('tipo_solicitud_id')
                ->constrained('tipos_solicitudes')
                ->onDelete('cascade');
            $table->string('estado')->default('pendiente');
            $table->json('data');
            $table->timestamp('enviada_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('tipo_solicitud_id');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};
