<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->boolean('es_cabecera')->default(false);
            $table->text('historia')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen_portada')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('estado', 30)->default('visible')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localidades');
    }
};
