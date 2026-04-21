<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actas_practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practica_id')->constrained('practicas')->onDelete('cascade');
            $table->integer('numero');
            $table->date('fecha');
            $table->text('descripcion');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('actas_practicas');
    }
};
