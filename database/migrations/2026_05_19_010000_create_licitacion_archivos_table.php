<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licitacion_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licitacion_id')->constrained('licitaciones')->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('nombre_archivo')->nullable();
            $table->string('ruta');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->default('pdf');
            $table->unsignedBigInteger('tamano')->nullable();
            $table->timestamps();
        });

        DB::table('licitaciones')
            ->whereNotNull('archivo_ruta')
            ->where('archivo_ruta', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($licitaciones) {
                foreach ($licitaciones as $licitacion) {
                    DB::table('licitacion_archivos')->insert([
                        'licitacion_id' => $licitacion->id,
                        'nombre_original' => $licitacion->archivo_nombre ?: 'Documento PDF',
                        'nombre_archivo' => $licitacion->archivo_nombre,
                        'ruta' => $licitacion->archivo_ruta,
                        'mime_type' => $licitacion->archivo_mime,
                        'extension' => 'pdf',
                        'tamano' => $licitacion->archivo_peso,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('licitacion_archivos');
    }
};
