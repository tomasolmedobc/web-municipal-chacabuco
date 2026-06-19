<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaileLugar extends Model
{
    protected $table = 'baile_lugares';

    protected $fillable = ['color', 'fila', 'numero', 'disponible'];

    protected $casts = ['disponible' => 'boolean'];

    public function reserva()
    {
        return $this->hasOne(BaileReserva::class, 'id_lugar');
    }

    public function getDescripcionAttribute(): string
    {
        return "{$this->color} — Fila {$this->fila}, Asiento {$this->numero}";
    }
}
