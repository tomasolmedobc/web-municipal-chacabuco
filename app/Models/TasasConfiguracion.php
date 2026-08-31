<?php

namespace App\Models;

use App\Traits\SanitizesRichText;
use Illuminate\Database\Eloquent\Model;

class TasasConfiguracion extends Model
{
    use SanitizesRichText;

    protected $table = 'tasas_configuracion';

    protected array $richTextFields = [
        'texto_pago_anual', 'texto_plan_facilidades', 'texto_info_bancaria',
    ];

    protected $fillable = [
        'texto_pago_anual', 'texto_plan_facilidades', 'texto_info_bancaria',
        'banner_url', 'banner_imagen_ruta',
        'btn_consulta_tipo', 'btn_consulta_url', 'btn_consulta_archivo_nombre', 'btn_consulta_archivo_ruta',
        'btn_ordenanza_tipo', 'btn_ordenanza_url', 'btn_ordenanza_archivo_nombre', 'btn_ordenanza_archivo_ruta',
    ];

    public static function instancia(): static
    {
        return static::firstOrCreate([], [
            'btn_consulta_tipo' => 'url',
            'btn_ordenanza_tipo' => 'url',
        ]);
    }

    public function getBtnConsultaHrefAttribute(): ?string
    {
        if ($this->btn_consulta_tipo === 'archivo' && $this->btn_consulta_archivo_ruta) {
            return $this->btn_consulta_archivo_ruta;
        }
        return $this->btn_consulta_url ?: null;
    }

    public function getBtnOrdenanzaHrefAttribute(): ?string
    {
        if ($this->btn_ordenanza_tipo === 'archivo' && $this->btn_ordenanza_archivo_ruta) {
            return $this->btn_ordenanza_archivo_ruta;
        }
        return $this->btn_ordenanza_url ?: null;
    }

    public function getBannerImagenUrlAttribute(): ?string
    {
        return $this->banner_imagen_ruta ?: null;
    }
}
