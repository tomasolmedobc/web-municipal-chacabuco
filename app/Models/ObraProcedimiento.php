<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObraProcedimiento extends Model
{
    const SECCIONES = [
        'obras'       => ['titulo' => 'Obras',                   'descripcion' => 'Tipos de obras y sus requisitos.'],
        'balcones'    => ['titulo' => 'Balcones Gastronómicos',   'descripcion' => 'Documentación y recorrido del expediente.'],
        'mensura'     => ['titulo' => 'Mensura y Subdivisión',    'descripcion' => 'Pasos para la subdivisión de tierras.'],
        'libre_deuda' => ['titulo' => 'Libre Deuda',              'descripcion' => 'Información sobre el certificado de libre deuda.'],
    ];

    protected $table = 'obras_procedimientos';

    protected $fillable = [
        'seccion', 'codigo', 'titulo', 'contenido', 'orden', 'visible',
    ];

    protected $casts = ['visible' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    public function scopeSeccion($query, string $seccion)
    {
        return $query->where('seccion', $seccion);
    }

    public function getEtiquetaAttribute(): string
    {
        $secciones = [
            'obras'       => 'Obras',
            'balcones'    => 'Balcones Gastronómicos',
            'mensura'     => 'Mensura y Subdivisión',
            'libre_deuda' => 'Libre Deuda',
        ];

        return ($secciones[$this->seccion] ?? $this->seccion) . ' — ' . strtoupper($this->codigo);
    }
}
