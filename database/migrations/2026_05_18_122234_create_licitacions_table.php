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
        Schema::create('licitaciones', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descripcion')->nullable();

            $table->enum('tipo', ['publica', 'privada'])->default('publica');
            $table->enum('estado', ['activa', 'finalizada'])->default('activa');

            $table->string('numero_expediente')->nullable();
            $table->year('anio')->nullable();

            $table->string('archivo_nombre')->nullable();
            $table->string('archivo_ruta')->nullable();
            $table->string('archivo_mime')->nullable();
            $table->unsignedBigInteger('archivo_peso')->nullable();

            $table->date('fecha_publicacion')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licitaciones');
    }
};
