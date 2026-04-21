<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
            Schema::create('practica_valores_campos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('practica_id')->constrained()->onDelete('cascade');
        $table->foreignId('campo_id')->constrained()->onDelete('cascade');
        $table->text('valor')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('practica_valores_campos');
    }
};
