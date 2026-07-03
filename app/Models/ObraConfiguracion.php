<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ObraConfiguracion extends Model
{
    protected $table = 'obras_configuracion';

    protected $fillable = [
        'registro_tipo',
        'registro_url',
        'registro_archivo_nombre',
        'registro_archivo_ruta',
        'registro_archivo_mime',
        'registro_archivo_peso',
    ];

    public static function instancia(): static
    {
        return static::firstOrCreate([], ['registro_tipo' => 'url']);
    }

    public function getRegistroHrefAttribute(): ?string
    {
        if ($this->registro_tipo === 'archivo' && $this->registro_archivo_ruta) {
            return Storage::disk('public')->url($this->registro_archivo_ruta);
        }

        return $this->registro_url ?: null;
    }
}
