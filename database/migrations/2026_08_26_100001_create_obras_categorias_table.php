<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('descripcion', 500)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        // Seed the 4 sections that existed as hardcoded values
        DB::table('obras_categorias')->insert([
            ['nombre' => 'Obras Particulares',     'descripcion' => null, 'orden' => 1, 'visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Balcones Gastronómicos', 'descripcion' => null, 'orden' => 2, 'visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Mensura y Subdivisión',  'descripcion' => null, 'orden' => 3, 'visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Libre Deuda',            'descripcion' => null, 'orden' => 4, 'visible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('obras_categorias');
    }
};
