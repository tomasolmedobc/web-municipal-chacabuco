<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUGS = ['chacabuco', 'rawson', 'ohiggins', 'castilla', 'cucha-cucha'];

    public function up(): void
    {
        DB::table('localidades')->insert([
            ['nombre' => 'Chacabuco', 'slug' => 'chacabuco', 'es_cabecera' => true, 'orden' => 0, 'estado' => 'visible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Rawson', 'slug' => 'rawson', 'es_cabecera' => false, 'orden' => 1, 'estado' => 'visible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => "O'Higgins", 'slug' => 'ohiggins', 'es_cabecera' => false, 'orden' => 2, 'estado' => 'visible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Castilla', 'slug' => 'castilla', 'es_cabecera' => false, 'orden' => 3, 'estado' => 'visible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cucha-Cucha', 'slug' => 'cucha-cucha', 'es_cabecera' => false, 'orden' => 4, 'estado' => 'visible', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('localidades')->whereIn('slug', self::SLUGS)->delete();
    }
};
