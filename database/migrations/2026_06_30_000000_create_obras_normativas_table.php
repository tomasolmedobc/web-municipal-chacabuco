<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras_normativas', function (Blueprint $table) {
            $table->id();
            $table->string('seccion', 30)->default('obras')->index();
            $table->string('nombre');
            $table->string('archivo_nombre')->nullable();
            $table->string('archivo_ruta', 500)->nullable();
            $table->string('archivo_mime', 100)->nullable();
            $table->unsignedInteger('archivo_peso')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obras_normativas');
    }
};
