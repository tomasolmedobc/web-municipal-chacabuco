<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('estado')->default('borrador')->after('imagen_destacada');
            $table->foreignId('user_id')->nullable()->after('estado')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('estado');
        });
    }
};