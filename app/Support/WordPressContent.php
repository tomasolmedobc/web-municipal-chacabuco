<?php

namespace App\Support;

/**
 * Limpieza del contenido que llega desde WordPress.
 */
class WordPressContent
{
    /**
     * Quita los shortcodes del maquetador Divi ([et_pb_section], [et_pb_row], [et_pb_column], [et_pb_text]...)
     * conservando el texto de adentro. Los [et_pb_image src="..."] se convierten en <img>, porque la imagen
     * vive en el atributo src y se perdería al borrar el código.
     */
    public static function quitarShortcodesDivi(string $html): string
    {
        if (! str_contains($html, '[et_pb_') && ! str_contains($html, '[/et_pb_')) {
            return $html;
        }

        // [et_pb_image _builder_version="3.6" src="/ruta/foto.webp" /]  ->  <p><img src="/ruta/foto.webp" alt=""></p>
        $html = preg_replace_callback(
            '/\[et_pb_image\b([^\]]*?)\/?\](?:\s*\[\/et_pb_image\])?/i',
            function (array $m) {
                $src = self::atributo($m[1], 'src');

                if ($src === null || $src === '') {
                    return '';
                }

                $alt = self::atributo($m[1], 'alt') ?? self::atributo($m[1], 'title_text') ?? '';

                return '<p><img src="' . htmlspecialchars($src, ENT_QUOTES) . '" alt="' . htmlspecialchars($alt, ENT_QUOTES) . '"></p>';
            },
            $html
        );

        // Cualquier otra apertura o cierre de Divi: [et_pb_text ...] y [/et_pb_text]
        $html = preg_replace('/\[\/?et_pb_[a-z0-9_]+\b[^\]]*\]/i', '', $html);

        return trim($html);
    }

    private static function atributo(string $atributos, string $nombre): ?string
    {
        if (preg_match('/\b' . preg_quote($nombre, '/') . '="([^"]*)"/i', $atributos, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES);
        }

        return null;
    }
}
