<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'parent_id',
    ];

    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function hijas()
    {
        return $this->hasMany(Categoria::class, 'parent_id')->orderBy('nombre');
    }

    public function noticias()
    {
        return $this->belongsToMany(Noticia::class, 'categoria_noticia');
    }
}