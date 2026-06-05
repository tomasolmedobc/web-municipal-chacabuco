<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_access_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('seccion')->index();
            $table->string('clave');
            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->string('url')->nullable();
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['seccion', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_access_buttons');
    }
};
