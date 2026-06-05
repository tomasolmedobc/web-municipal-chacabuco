<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicAccessButton extends Model
{
    protected $fillable = [
        'seccion',
        'clave',
        'titulo',
        'descripcion',
        'url',
        'url_personalizada',
        'icono',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];
}
