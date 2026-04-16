<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('noticias')) {
            Schema::create('noticias', function (Blueprint $table) {
                $table->id();
                $table->text('titulo');
                $table->longText('contenido')->nullable();
                $table->dateTime('fecha');
                $table->string('autor')->nullable();
                $table->text('imagen_destacada')->nullable();
                $table->timestamps();
                $table->string('slug')->unique();
                $table->unsignedBigInteger('wp_id')->nullable()->unique();

                $table->index('fecha');
            });

            return;
        }

        $existingIndexes = collect(DB::select('SHOW INDEX FROM noticias'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        Schema::table('noticias', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('noticias_slug_unique', $existingIndexes, true)) {
                $table->unique('slug');
            }

            if (!in_array('noticias_wp_id_unique', $existingIndexes, true)) {
                $table->unique('wp_id');
            }

            if (!in_array('noticias_fecha_index', $existingIndexes, true)) {
                $table->index('fecha');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('noticias')) {
            return;
        }

        $existingIndexes = collect(DB::select('SHOW INDEX FROM noticias'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        Schema::table('noticias', function (Blueprint $table) use ($existingIndexes) {
            if (in_array('noticias_fecha_index', $existingIndexes, true)) {
                $table->dropIndex('noticias_fecha_index');
            }

            if (in_array('noticias_wp_id_unique', $existingIndexes, true)) {
                $table->dropUnique('noticias_wp_id_unique');
            }

            if (in_array('noticias_slug_unique', $existingIndexes, true)) {
                $table->dropUnique('noticias_slug_unique');
            }
        });
    }
};
