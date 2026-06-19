<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'localidades';

    protected $fillable = [
        'nombre',
        'slug',
        'es_cabecera',
        'historia',
        'descripcion',
        'imagen_portada',
        'orden',
        'estado',
    ];

    protected $casts = [
        'es_cabecera' => 'boolean',
        'orden' => 'integer',
    ];

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }

    public function turismoItems()
    {
        return $this->hasMany(TurismoItem::class);
    }

    public function getImagenPortadaUrlAttribute(): string
    {
        return $this->imagen_portada ?: '/images/importantes/default-noticia.webp';
    }
}
