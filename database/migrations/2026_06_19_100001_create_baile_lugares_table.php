<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baile_lugares', function (Blueprint $table) {
            $table->id();
            $table->string('color', 60);
            $table->string('fila', 10);
            $table->unsignedSmallInteger('numero');
            $table->boolean('disponible')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baile_lugares');
    }
};
