<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recaudacion_tramites_online', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });

        DB::table('recaudacion_tramites_online')->insert([
            'titulo'      => 'Trámite de cambio de titularidad de Vehículos/Propiedades',
            'descripcion' => 'Usted puede iniciar su trámite de manera totalmente online, recuerde revisar su correo electrónico para recibir las notificaciones del trámite.',
            'url'         => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('recaudacion_tramites_online');
    }
};
