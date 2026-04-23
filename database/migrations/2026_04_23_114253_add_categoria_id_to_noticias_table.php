<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('user_id')
                ->constrained('categorias')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
        });
    }
};