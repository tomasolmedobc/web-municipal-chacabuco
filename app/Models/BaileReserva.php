<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaileReserva extends Model
{
    protected $table = 'baile_reservas';

    protected $fillable = ['id_usuario', 'id_lugar', 'pago', 'payment_id', 'payment_type'];

    protected $casts = ['pago' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(BaileUsuario::class, 'id_usuario');
    }

    public function lugar()
    {
        return $this->belongsTo(BaileLugar::class, 'id_lugar');
    }
}
