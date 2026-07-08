<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecaudacionDocumento extends Model
{
    protected $table = 'recaudacion_documentos';

    protected $fillable = ['titulo', 'url', 'activo', 'orden'];

    protected $casts = ['activo' => 'boolean'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
