<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelefonoUtil extends Model
{
    protected $table = 'telefonos_utiles';

    protected $fillable = [
        'nombre',
        'categoria',
        'direccion',
        'telefono',
        'email',
        'orden',
        'estado',
    ];

    public const CATEGORIAS = [
        'Gobierno Municipal',
        'Salud',
        'Seguridad',
        'Servicios Públicos',
        'Educación y Cultura',
        'Social',
        'Justicia',
        'Comercio y Trabajo',
        'Deportes',
        'Delegaciones',
    ];

    public const ICONOS = [
        'Gobierno Municipal'  => 'fa-building-columns',
        'Salud'               => 'fa-heart-pulse',
        'Seguridad'           => 'fa-shield-halved',
        'Servicios Públicos'  => 'fa-wrench',
        'Educación y Cultura' => 'fa-book-open',
        'Social'              => 'fa-people-group',
        'Justicia'            => 'fa-scale-balanced',
        'Comercio y Trabajo'  => 'fa-briefcase',
        'Deportes'            => 'fa-medal',
        'Delegaciones'        => 'fa-map-location-dot',
    ];

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }
}
