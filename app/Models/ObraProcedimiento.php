<?php

namespace App\Models;

use App\Traits\SanitizesRichText;
use Illuminate\Database\Eloquent\Model;

class ObraProcedimiento extends Model
{
    use SanitizesRichText;

    protected array $richTextFields = ['contenido'];

    protected $table = 'obras_procedimientos';

    protected $fillable = [
        'categoria_id', 'codigo', 'titulo', 'contenido', 'orden', 'visible',
    ];

    protected $casts = ['visible' => 'boolean'];

    public function categoria()
    {
        return $this->belongsTo(ObraCategoria::class, 'categoria_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }
}
