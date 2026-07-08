<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarnetConfiguracion extends Model
{
    protected $table = 'carnet_configuracion';

    protected $fillable = [
        'intro_texto', 'alerta_info', 'aviso_ubicacion',
        'paso1_titulo', 'paso1_contenido',
        'paso2_titulo', 'paso2_contenido',
        'paso3_titulo', 'paso3_contenido',
        'paso4_titulo', 'paso4_contenido',
        'licencia_digital_contenido',
    ];

    public static function instancia(): static
    {
        return static::firstOrCreate([], [
            'paso1_titulo' => 'Curso Teórico-Práctico y Examen Teórico',
            'paso2_titulo' => 'Examen Práctico de Idoneidad Conductiva',
            'paso3_titulo' => 'Examen Psicofísico',
            'paso4_titulo' => 'Control de la Documentación y Recopilación de Datos Personales',
        ]);
    }
}
