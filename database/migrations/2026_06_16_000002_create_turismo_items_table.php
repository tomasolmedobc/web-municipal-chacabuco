<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turismo_items', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 40)->index();
            $table->foreignId('localidad_id')->constrained('localidades')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable()->index();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('link_externo')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('imagen')->nullable();
            $table->string('estado', 30)->default('visible')->index();
            $table->boolean('destacado')->default(false);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turismo_items');
    }
};
