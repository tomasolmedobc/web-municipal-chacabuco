<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObraNormativa extends Model
{
    protected $table = 'obras_normativas';

    protected $fillable = [
        'categoria_id', 'nombre',
        'archivo_nombre', 'archivo_ruta', 'archivo_mime', 'archivo_peso',
        'orden', 'visible',
    ];

    protected $casts = ['visible' => 'boolean'];

    public function categoria()
    {
        return $this->belongsTo(ObraCategoria::class, 'categoria_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    public function getArchivoPesoLegibleAttribute(): string
    {
        if (! $this->archivo_peso) {
            return '';
        }

        $kb = $this->archivo_peso / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1) . ' MB'
            : number_format($kb, 0) . ' KB';
    }
}
