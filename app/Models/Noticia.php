<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticias';

    protected $fillable = [
        'wp_id',
        'titulo',
        'contenido',
        'fecha',
        'autor',
        'slug',
        'imagen_destacada',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'wp_id' => 'integer',
            'wp_modified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
