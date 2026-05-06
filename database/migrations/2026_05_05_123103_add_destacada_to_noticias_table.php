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
        Schema::table('noticias', function (Blueprint $table) {
            $table->boolean('destacada')->default(false);
            $table->timestamp('destacada_hasta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            if (Schema::hasColumn('noticias', 'destacada')) {
                $table->dropColumn('destacada');
            }

            if (Schema::hasColumn('noticias', 'destacada_hasta')) {
                $table->dropColumn('destacada_hasta');
            }
        });
}
};
