<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licitacion extends Model
{
    protected $table = 'licitaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'numero_expediente',
        'anio',
        'archivo_nombre',
        'archivo_ruta',
        'archivo_mime',
        'archivo_peso',
        'fecha_publicacion',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'anio' => 'integer',
        'archivo_peso' => 'integer',
    ];
}