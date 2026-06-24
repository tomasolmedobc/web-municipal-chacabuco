<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurismoItemImagen extends Model
{
    protected $table = 'turismo_item_imagenes';

    protected $fillable = ['turismo_item_id', 'imagen', 'titulo', 'orden', 'es_header'];

    protected $casts = ['es_header' => 'boolean'];

    public function item()
    {
        return $this->belongsTo(TurismoItem::class, 'turismo_item_id');
    }

    public function getImagenUrlAttribute(): string
    {
        return asset('storage/' . $this->imagen);
    }
}
