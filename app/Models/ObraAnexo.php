<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObraAnexo extends Model
{
    protected $table = 'obras_anexos';

    protected $fillable = [
        'nombre', 'descripcion',
        'archivo_nombre', 'archivo_ruta', 'archivo_mime', 'archivo_peso',
        'orden',
    ];

    public function getArchivoPesoLegibleAttribute(): string
    {
        if (! $this->archivo_peso) {
            return '';
        }

        $kb = $this->archivo_peso / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1) . ' MB'
            : number_format($kb, 0) . ' KB';
    }
}
