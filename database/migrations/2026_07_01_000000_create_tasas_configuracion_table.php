<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasas_configuracion', function (Blueprint $table) {
            $table->id();
            $table->longText('texto_pago_anual')->nullable();
            $table->longText('texto_plan_facilidades')->nullable();
            $table->longText('texto_info_bancaria')->nullable();
            $table->string('banner_url', 500)->nullable();
            $table->string('banner_imagen_ruta', 500)->nullable();
            $table->string('btn_consulta_tipo', 20)->default('url');
            $table->string('btn_consulta_url', 500)->nullable();
            $table->string('btn_consulta_archivo_nombre')->nullable();
            $table->string('btn_consulta_archivo_ruta', 500)->nullable();
            $table->string('btn_ordenanza_tipo', 20)->default('url');
            $table->string('btn_ordenanza_url', 500)->nullable();
            $table->string('btn_ordenanza_archivo_nombre')->nullable();
            $table->string('btn_ordenanza_archivo_ruta', 500)->nullable();
            $table->timestamps();
        });

        DB::table('tasas_configuracion')->insert([
            'btn_consulta_tipo' => 'url',
            'btn_ordenanza_tipo' => 'url',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tasas_configuracion');
    }
};
