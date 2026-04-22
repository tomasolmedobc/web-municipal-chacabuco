<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\NoticiaArchivo;

class Noticia extends Model
{
    protected $table = 'noticias';

    protected $fillable = [
        'wp_id',
        'wp_modified_at',
        'titulo',
        'contenido',
        'fecha',
        'autor',
        'slug',
        'imagen_destacada',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'wp_id' => 'integer',
        'wp_modified_at' => 'datetime',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function archivos()
    {
        return $this->hasMany(NoticiaArchivo::class);
    }
}