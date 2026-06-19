<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaileUsuario extends Model
{
    protected $table = 'baile_usuarios';

    protected $fillable = ['nombre_completo', 'dni', 'codigo', 'disponibles'];

    public function reservas()
    {
        return $this->hasMany(BaileReserva::class, 'id_usuario');
    }
}
