<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasas_grupos', function (Blueprint $table) {
            $table->string('codigo', 200)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasas_grupos', function (Blueprint $table) {
            $table->string('codigo', 40)->change();
        });
    }
};
