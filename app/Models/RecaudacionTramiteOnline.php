<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecaudacionTramiteOnline extends Model
{
    protected $table = 'recaudacion_tramites_online';

    protected $fillable = ['titulo', 'descripcion', 'url'];

    public static function instancia(): self
    {
        return static::firstOrCreate([], [
            'titulo'      => 'Trámite de cambio de titularidad de Vehículos/Propiedades',
            'descripcion' => 'Usted puede iniciar su trámite de manera totalmente online, recuerde revisar su correo electrónico para recibir las notificaciones del trámite.',
            'url'         => null,
        ]);
    }
}
