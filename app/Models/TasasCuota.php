<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TasasCuota extends Model
{
    protected $table = 'tasas_cuotas';

    protected $fillable = ['grupo_id', 'cuota_label', 'fecha_vencimiento', 'orden', 'estado'];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(TasasGrupo::class, 'grupo_id');
    }
}
