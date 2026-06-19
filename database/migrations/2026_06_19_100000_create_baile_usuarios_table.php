<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baile_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('dni', 9)->unique();
            $table->string('codigo', 8);
            $table->unsignedTinyInteger('disponibles')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baile_usuarios');
    }
};
