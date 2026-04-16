<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('noticias') || Schema::hasColumn('noticias', 'wp_modified_at')) {
            return;
        }

        Schema::table('noticias', function (Blueprint $table) {
            $table->dateTime('wp_modified_at')->nullable()->after('wp_id');
            $table->index('wp_modified_at');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('noticias') || !Schema::hasColumn('noticias', 'wp_modified_at')) {
            return;
        }

        Schema::table('noticias', function (Blueprint $table) {
            $table->dropIndex(['wp_modified_at']);
            $table->dropColumn('wp_modified_at');
        });
    }
};
