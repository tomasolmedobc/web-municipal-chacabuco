<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasas_cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('tasas_grupos')->cascadeOnDelete();
            $table->string('cuota_label');
            $table->date('fecha_vencimiento');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('estado', 30)->default('visible');
            $table->timestamps();

            $table->index(['grupo_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasas_cuotas');
    }
};
