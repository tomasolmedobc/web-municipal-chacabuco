<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras_procedimientos', function (Blueprint $table) {
            $table->id();
            $table->string('seccion', 30)->default('obras')->index();
            $table->string('codigo', 10);
            $table->string('titulo');
            $table->longText('contenido');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obras_procedimientos');
    }
};
