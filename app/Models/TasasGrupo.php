<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TasasGrupo extends Model
{
    protected $table = 'tasas_grupos';

    protected $fillable = ['nombre', 'codigo', 'orden', 'estado'];

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(TasasCuota::class, 'grupo_id')->orderBy('orden');
    }

    public function cuotasVisibles(): HasMany
    {
        return $this->hasMany(TasasCuota::class, 'grupo_id')
            ->where('estado', 'visible')
            ->orderBy('orden');
    }
}
