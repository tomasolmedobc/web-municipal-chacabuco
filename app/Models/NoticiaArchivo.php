<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaArchivo extends Model
{
    protected $fillable = [
        'noticia_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'mime_type',
        'extension',
    ];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }

    public function getTamanoLegibleAttribute(): string
    {
        $rutaFisica = public_path(ltrim($this->ruta, '/'));

        if (!file_exists($rutaFisica)) {
            return 'Tamaño no disponible';
        }

        $bytes = filesize($rutaFisica);

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }

        return $bytes . ' bytes';
    }
}