<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turismo_items', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable()->after('fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::table('turismo_items', function (Blueprint $table) {
            $table->dropColumn('hora_inicio');
        });
    }
};
