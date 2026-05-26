<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('noticia_archivos')) {
            return;
        }

        Schema::create('noticia_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noticia_id')->constrained('noticias')->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticia_archivos');
    }
};
