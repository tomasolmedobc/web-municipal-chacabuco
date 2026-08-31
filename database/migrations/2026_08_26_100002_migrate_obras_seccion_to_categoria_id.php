<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add categoria_id FK to obras_procedimientos
        Schema::table('obras_procedimientos', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('id')
                ->constrained('obras_categorias')
                ->nullOnDelete();
        });

        // Add categoria_id FK to obras_normativas
        Schema::table('obras_normativas', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('id')
                ->constrained('obras_categorias')
                ->nullOnDelete();
        });

        // Migrate existing data from seccion string to categoria_id FK
        $map = [
            'obras'       => DB::table('obras_categorias')->where('nombre', 'Obras Particulares')->value('id'),
            'balcones'    => DB::table('obras_categorias')->where('nombre', 'Balcones Gastronómicos')->value('id'),
            'mensura'     => DB::table('obras_categorias')->where('nombre', 'Mensura y Subdivisión')->value('id'),
            'libre_deuda' => DB::table('obras_categorias')->where('nombre', 'Libre Deuda')->value('id'),
        ];

        foreach ($map as $seccion => $categoriaId) {
            if ($categoriaId) {
                DB::table('obras_procedimientos')->where('seccion', $seccion)->update(['categoria_id' => $categoriaId]);
                DB::table('obras_normativas')->where('seccion', $seccion)->update(['categoria_id' => $categoriaId]);
            }
        }

        // Drop seccion column from both tables (index is auto-dropped on MySQL)
        Schema::table('obras_procedimientos', function (Blueprint $table) {
            $table->dropColumn('seccion');
        });

        Schema::table('obras_normativas', function (Blueprint $table) {
            $table->dropColumn('seccion');
        });
    }

    public function down(): void
    {
        Schema::table('obras_procedimientos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
            $table->string('seccion', 30)->default('obras')->after('id')->index();
        });

        Schema::table('obras_normativas', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
            $table->string('seccion', 30)->default('obras')->after('id')->index();
        });
    }
};
