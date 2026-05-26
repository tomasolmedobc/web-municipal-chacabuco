<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicitacionArchivo extends Model
{
    protected $fillable = [
        'licitacion_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'mime_type',
        'extension',
        'tamano',
    ];

    public function licitacion()
    {
        return $this->belongsTo(Licitacion::class);
    }

    public function getTamanoLegibleAttribute(): string
    {
        if (! $this->tamano) {
            return 'Tamano no disponible';
        }

        if ($this->tamano >= 1048576) {
            return number_format($this->tamano / 1048576, 2) . ' MB';
        }

        if ($this->tamano >= 1024) {
            return number_format($this->tamano / 1024, 0) . ' KB';
        }

        return $this->tamano . ' bytes';
    }
}
