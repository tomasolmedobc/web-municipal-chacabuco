<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_access_buttons', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('public_access_buttons', function (Blueprint $table) {
            $table->string('descripcion')->nullable()->change();
        });
    }
};
