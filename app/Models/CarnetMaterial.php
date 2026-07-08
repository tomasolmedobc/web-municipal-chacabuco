<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetMaterial extends Model
{
    protected $table = 'carnet_materiales';

    protected $fillable = ['titulo', 'subtitulo', 'url', 'tipo_boton', 'activo', 'orden'];

    protected $casts = ['activo' => 'boolean'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
