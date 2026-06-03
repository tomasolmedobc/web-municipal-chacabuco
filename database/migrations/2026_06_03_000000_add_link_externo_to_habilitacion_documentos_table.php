<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habilitacion_documentos', function (Blueprint $table) {
            $table->string('link_externo', 2048)->nullable()->after('archivo_peso');
        });
    }

    public function down(): void
    {
        Schema::table('habilitacion_documentos', function (Blueprint $table) {
            $table->dropColumn('link_externo');
        });
    }
};
