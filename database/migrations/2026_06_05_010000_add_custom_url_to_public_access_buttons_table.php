<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_access_buttons', function (Blueprint $table) {
            $table->string('url_personalizada')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('public_access_buttons', function (Blueprint $table) {
            $table->dropColumn('url_personalizada');
        });
    }
};
