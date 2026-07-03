<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $grupos = [
            ['nombre' => 'Tasas Unificada (AB Y SS)', 'codigo' => 'unificada',          'orden' => 0],
            ['nombre' => 'Red Vial (RV)',              'codigo' => 'red_vial',           'orden' => 1],
            ['nombre' => 'Seguridad e Higiene (SH)',   'codigo' => 'seguridad_higiene',  'orden' => 2],
            ['nombre' => 'Impuesto Automotor',         'codigo' => 'automotor',          'orden' => 3],
            ['nombre' => "Delegaciones Castilla, Rawson, O'Higgins Y Cucha Cucha",
                                                       'codigo' => 'delegaciones',       'orden' => 4],
        ];

        foreach ($grupos as $grupo) {
            DB::table('tasas_grupos')->insert([
                ...$grupo,
                'estado'     => 'visible',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tasas_grupos')->whereIn('codigo', [
            'unificada', 'red_vial', 'seguridad_higiene', 'automotor', 'delegaciones',
        ])->delete();
    }
};
