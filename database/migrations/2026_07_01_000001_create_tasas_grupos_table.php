<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasas_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 40)->unique();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('estado', 30)->default('visible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasas_grupos');
    }
};
