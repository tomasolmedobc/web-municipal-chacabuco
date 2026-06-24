<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurismoItemArchivo extends Model
{
    protected $table = 'turismo_item_archivos';

    protected $fillable = [
        'turismo_item_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'mime_type',
        'extension',
    ];

    public function item()
    {
        return $this->belongsTo(TurismoItem::class, 'turismo_item_id');
    }

    public function getTamanoLegibleAttribute(): string
    {
        $rutaFisica = public_path(ltrim($this->ruta, '/'));

        if (! file_exists($rutaFisica)) {
            return '';
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
