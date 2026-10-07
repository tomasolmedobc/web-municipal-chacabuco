<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores_configuracion', function (Blueprint $table) {
            $table->string('guia_video_url', 500)->nullable()->after('sellado_monto');
            $table->string('guia_imagen_1_ruta', 500)->nullable()->after('guia_video_url');
            $table->string('guia_imagen_1_titulo', 200)->nullable()->default('Paso 1')->after('guia_imagen_1_ruta');
            $table->string('guia_imagen_1_desc', 500)->nullable()->default('Seleccioná la factura dentro del sistema.')->after('guia_imagen_1_titulo');
            $table->string('guia_imagen_2_ruta', 500)->nullable()->after('guia_imagen_1_desc');
            $table->string('guia_imagen_2_titulo', 200)->nullable()->default('Paso 2')->after('guia_imagen_2_ruta');
            $table->string('guia_imagen_2_desc', 500)->nullable()->default('Accedé a la sección de retenciones.')->after('guia_imagen_2_titulo');
        });
    }

    public function down(): void
    {
        Schema::table('proveedores_configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'guia_video_url',
                'guia_imagen_1_ruta', 'guia_imagen_1_titulo', 'guia_imagen_1_desc',
                'guia_imagen_2_ruta', 'guia_imagen_2_titulo', 'guia_imagen_2_desc',
            ]);
        });
    }
};
