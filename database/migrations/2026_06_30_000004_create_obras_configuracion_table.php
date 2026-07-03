<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('registro_tipo', 20)->default('url'); // 'url' | 'archivo'
            $table->string('registro_url', 500)->nullable();
            $table->string('registro_archivo_nombre')->nullable();
            $table->string('registro_archivo_ruta', 500)->nullable();
            $table->string('registro_archivo_mime', 100)->nullable();
            $table->unsignedInteger('registro_archivo_peso')->nullable();
            $table->timestamps();
        });

        DB::table('obras_configuracion')->insert([
            'registro_tipo' => 'url',
            'registro_url'  => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('obras_configuracion');
    }
};
