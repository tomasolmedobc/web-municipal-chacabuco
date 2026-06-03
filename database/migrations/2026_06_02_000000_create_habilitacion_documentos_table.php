<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habilitacion_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('seccion', 40)->index();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable()->index();
            $table->string('numero')->nullable()->index();
            $table->unsignedSmallInteger('anio')->nullable()->index();
            $table->string('estado', 30)->default('visible')->index();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('archivo_nombre')->nullable();
            $table->string('archivo_ruta')->nullable();
            $table->string('archivo_mime')->nullable();
            $table->unsignedBigInteger('archivo_peso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habilitacion_documentos');
    }
};
