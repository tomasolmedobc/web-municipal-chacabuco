<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_noticia', function (Blueprint $table) {
            $table->id();

            $table->integer('noticia_id'); // 👈 igual que noticias.id
            $table->unsignedBigInteger('categoria_id');

            $table->timestamps();

            $table->unique(['noticia_id', 'categoria_id']);

            $table->foreign('noticia_id')
                ->references('id')
                ->on('noticias')
                ->cascadeOnDelete();

            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->cascadeOnDelete();
        });
    }
        public function down(): void
    {
        Schema::dropIfExists('categoria_noticia');
    }
};