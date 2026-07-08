<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recaudacion_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('url')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        DB::table('recaudacion_documentos')->insert([
            ['titulo' => 'Certificado de baja de Vehículos', 'url' => null, 'activo' => true, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['titulo' => 'Certificado de baja de motos',     'url' => null, 'activo' => true, 'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['titulo' => 'Libre deuda Municipal',            'url' => null, 'activo' => true, 'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('recaudacion_documentos');
    }
};
