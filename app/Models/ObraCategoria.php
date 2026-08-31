<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObraCategoria extends Model
{
    protected $table = 'obras_categorias';

    protected $fillable = ['nombre', 'descripcion', 'orden', 'visible'];

    protected $casts = ['visible' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    public function procedimientos()
    {
        return $this->hasMany(ObraProcedimiento::class, 'categoria_id');
    }

    public function normativas()
    {
        return $this->hasMany(ObraNormativa::class, 'categoria_id');
    }
}
