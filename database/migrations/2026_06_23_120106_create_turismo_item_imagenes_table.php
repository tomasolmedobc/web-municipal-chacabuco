<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('turismo_item_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turismo_item_id')->constrained('turismo_items')->cascadeOnDelete();
            $table->string('imagen');
            $table->string('titulo')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turismo_item_imagenes');
    }
};
