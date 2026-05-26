<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licitacion extends Model
{
    protected $table = 'licitaciones';

    public const CATEGORIA_LICITACIONES = 'licitaciones';
    public const CATEGORIA_GASTOS_RECURSOS_BALANCE = 'gastos_recursos_balance';
    public const CATEGORIA_BOTONES_ARCHIVOS_LINKS = 'botones_archivos_links';

    public const CATEGORIAS = [
        self::CATEGORIA_LICITACIONES => [
            'titulo' => 'Licitaciones',
            'singular' => 'licitacion',
            'descripcion' => 'Licitaciones publicas y privadas del Municipio de Chacabuco.',
            'icono' => 'fa-file-contract',
        ],
        self::CATEGORIA_GASTOS_RECURSOS_BALANCE => [
            'titulo' => 'Gastos, Recursos y Balance',
            'singular' => 'documento',
            'descripcion' => 'Informacion presupuestaria, gastos, recursos y balances municipales.',
            'icono' => 'fa-calculator',
        ],
        self::CATEGORIA_BOTONES_ARCHIVOS_LINKS => [
            'titulo' => 'Botones con archivos/links',
            'singular' => 'boton',
            'descripcion' => 'Accesos directos de Gobierno Abierto a documentos o enlaces externos.',
            'icono' => 'fa-link',
        ],
    ];

    protected $fillable = [
        'categoria',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'numero_expediente',
        'anio',
        'archivo_nombre',
        'archivo_ruta',
        'archivo_mime',
        'archivo_peso',
        'link_externo',
        'orden',
        'fecha_publicacion',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'anio' => 'integer',
        'archivo_peso' => 'integer',
        'orden' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Licitacion $licitacion) {
            $licitacion->categoria = self::normalizarCategoria($licitacion->categoria);
        });

        static::saving(function (Licitacion $licitacion) {
            $licitacion->categoria = self::normalizarCategoria($licitacion->categoria);
        });
    }

    public static function normalizarCategoria(?string $categoria): string
    {
        return array_key_exists((string) $categoria, self::CATEGORIAS)
            ? (string) $categoria
            : self::CATEGORIA_LICITACIONES;
    }

    public static function configCategoria(?string $categoria): array
    {
        $categoria = self::normalizarCategoria($categoria);

        return self::CATEGORIAS[$categoria] + ['key' => $categoria];
    }

    public function scopeCategoria($query, ?string $categoria)
    {
        return $query->where('categoria', self::normalizarCategoria($categoria));
    }

    public function archivos()
    {
        return $this->hasMany(LicitacionArchivo::class);
    }
}
