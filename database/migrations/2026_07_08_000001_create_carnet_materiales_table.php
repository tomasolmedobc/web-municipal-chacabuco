<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carnet_materiales', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('subtitulo', 100)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('tipo_boton', 30)->default('descargar');
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        DB::table('carnet_materiales')->insert([
            ['titulo' => 'Manual del conductor',                          'subtitulo' => '2.9 Mb', 'url' => null, 'tipo_boton' => 'descargar', 'activo' => true, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['titulo' => 'Manual del conductor profesional',              'subtitulo' => '2.9 Mb', 'url' => null, 'tipo_boton' => 'descargar', 'activo' => true, 'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['titulo' => 'Libro señales de tránsito',                     'subtitulo' => '3.1 Mb', 'url' => null, 'tipo_boton' => 'descargar', 'activo' => true, 'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['titulo' => 'Tipo de clases y vigencias de carnet de conducir', 'subtitulo' => null,  'url' => null, 'tipo_boton' => 'ver',       'activo' => true, 'orden' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('carnet_materiales');
    }
};
