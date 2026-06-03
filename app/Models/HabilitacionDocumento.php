<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabilitacionDocumento extends Model
{
    protected $table = 'habilitacion_documentos';

    public const SECCION_ARCHIVOS = 'archivos';
    public const SECCION_ORDENANZAS = 'ordenanzas';

    public const SECCIONES = [
        self::SECCION_ARCHIVOS => [
            'titulo' => 'Archivos de Habilitaciones',
            'singular' => 'archivo',
            'descripcion' => 'Formularios, requisitos y documentos para tramites de habilitaciones.',
            'icono' => 'fa-file-pdf',
        ],
        self::SECCION_ORDENANZAS => [
            'titulo' => 'Ordenanzas',
            'singular' => 'ordenanza',
            'descripcion' => 'Normativa de referencia para rubros y habilitaciones.',
            'icono' => 'fa-scale-balanced',
        ],
    ];

    protected $fillable = [
        'seccion',
        'titulo',
        'descripcion',
        'categoria',
        'numero',
        'anio',
        'estado',
        'orden',
        'archivo_nombre',
        'archivo_ruta',
        'archivo_mime',
        'archivo_peso',
        'link_externo',
    ];

    protected $casts = [
        'anio' => 'integer',
        'orden' => 'integer',
        'archivo_peso' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (HabilitacionDocumento $documento) {
            $documento->seccion = self::normalizarSeccion($documento->seccion);
        });
    }

    public static function normalizarSeccion(?string $seccion): string
    {
        return array_key_exists((string) $seccion, self::SECCIONES)
            ? (string) $seccion
            : self::SECCION_ARCHIVOS;
    }

    public static function configSeccion(?string $seccion): array
    {
        $seccion = self::normalizarSeccion($seccion);

        return self::SECCIONES[$seccion] + ['key' => $seccion];
    }

    public function scopeSeccion($query, ?string $seccion)
    {
        return $query->where('seccion', self::normalizarSeccion($seccion));
    }

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }

    public function getArchivoPesoLegibleAttribute(): string
    {
        if (! $this->archivo_peso) {
            return 'Tamano no disponible';
        }

        if ($this->archivo_peso >= 1048576) {
            return number_format($this->archivo_peso / 1048576, 2) . ' MB';
        }

        if ($this->archivo_peso >= 1024) {
            return number_format($this->archivo_peso / 1024, 0) . ' KB';
        }

        return $this->archivo_peso . ' bytes';
    }
}
