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
        Schema::create('campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_solicitud_id')->constrained('tipos_solicitudes')->onDelete('cascade');
            $table->string('label')->nullable(true);
            $table->string('name');
            $table->enum('type', ['text', 'textarea', 'email', 'number', 'date', 'select', 'file', 'checkbox', 'hidden']);
            $table->string('placeholder')->nullable(true);
            $table->boolean('required')->default(true);
            $table->text('instructions')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campos');
    }
};
