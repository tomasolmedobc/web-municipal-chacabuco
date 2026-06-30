<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telefonos_utiles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('categoria')->nullable()->index();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('estado', 30)->default('visible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telefonos_utiles');
    }
};
