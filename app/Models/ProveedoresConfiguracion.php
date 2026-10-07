<?php

namespace App\Models;

use App\Traits\SanitizesRichText;
use Illuminate\Database\Eloquent\Model;

class ProveedoresConfiguracion extends Model
{
    use SanitizesRichText;

    protected $table = 'proveedores_configuracion';

    protected array $richTextFields = [
        'texto_requisitos', 'texto_info_bancaria', 'texto_email_contacto', 'texto_creacion_usuario',
    ];

    protected $fillable = [
        'sellado_monto',
        'guia_video_url',
        'guia_imagen_1_ruta', 'guia_imagen_1_titulo', 'guia_imagen_1_desc',
        'guia_imagen_2_ruta', 'guia_imagen_2_titulo', 'guia_imagen_2_desc',
        'texto_requisitos',
        'texto_info_bancaria',
        'texto_email_contacto',
        'texto_creacion_usuario',
    ];

    public static function instancia(): static
    {
        return static::firstOrCreate([], [
            'sellado_monto'      => '$49.600,00',
            'guia_imagen_1_titulo' => 'Paso 1',
            'guia_imagen_1_desc'   => 'Seleccioná la factura dentro del sistema.',
            'guia_imagen_2_titulo' => 'Paso 2',
            'guia_imagen_2_desc'   => 'Accedé a la sección de retenciones.',
        ]);
    }

    public function getGuiaImagen1UrlAttribute(): string
    {
        return $this->guia_imagen_1_ruta ?: asset('images/guias/proveedores-1.webp');
    }

    public function getGuiaImagen2UrlAttribute(): string
    {
        return $this->guia_imagen_2_ruta ?: asset('images/guias/proveedores-2.webp');
    }
}
