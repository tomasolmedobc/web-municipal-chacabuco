<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('localidades', function (Blueprint $table) {
            $table->text('mapa_embed')->nullable()->after('imagen_portada');
        });
    }

    public function down(): void
    {
        Schema::table('localidades', function (Blueprint $table) {
            $table->dropColumn('mapa_embed');
        });
    }
};
