<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurismoItem extends Model
{
    protected $table = 'turismo_items';

    public const TIPO_ATRACTIVO = 'atractivo';
    public const TIPO_GASTRONOMIA = 'gastronomia';
    public const TIPO_EVENTO = 'evento';

    public const TIPOS = [
        self::TIPO_ATRACTIVO => [
            'titulo' => 'Atractivos Turisticos',
            'singular' => 'atractivo',
            'descripcion' => 'Lugares para visitar y conocer en Chacabuco y sus delegaciones.',
            'icono' => 'fa-mountain-sun',
        ],
        self::TIPO_GASTRONOMIA => [
            'titulo' => 'Gastronomia',
            'singular' => 'lugar gastronomico',
            'descripcion' => 'Restaurantes, bares y productores locales de Chacabuco y sus delegaciones.',
            'icono' => 'fa-utensils',
        ],
        self::TIPO_EVENTO => [
            'titulo' => 'Eventos y Agenda',
            'singular' => 'evento',
            'descripcion' => 'Fiestas, ferias y actividades programadas en Chacabuco y sus delegaciones.',
            'icono' => 'fa-calendar-days',
        ],
    ];

    protected $fillable = [
        'tipo',
        'localidad_id',
        'titulo',
        'descripcion',
        'categoria',
        'direccion',
        'telefono',
        'link_externo',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'imagen',
        'estado',
        'destacado',
        'orden',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'destacado' => 'boolean',
        'orden' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (TurismoItem $item) {
            $item->tipo = self::normalizarTipo($item->tipo);
        });
    }

    public static function normalizarTipo(?string $tipo): string
    {
        return array_key_exists((string) $tipo, self::TIPOS)
            ? (string) $tipo
            : self::TIPO_ATRACTIVO;
    }

    public static function configTipo(?string $tipo): array
    {
        $tipo = self::normalizarTipo($tipo);

        return self::TIPOS[$tipo] + ['key' => $tipo];
    }

    public function scopeTipo($query, ?string $tipo)
    {
        return $query->where('tipo', self::normalizarTipo($tipo));
    }

    public function scopeVisible($query)
    {
        return $query->where('estado', 'visible');
    }

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function getImagenUrlAttribute(): string
    {
        return $this->imagen ?: '/images/importantes/default-noticia.webp';
    }
}
