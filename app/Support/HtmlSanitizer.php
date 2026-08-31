<?php

namespace App\Support;

class HtmlSanitizer
{
    // Tags que se eliminan junto con su contenido interno
    private const STRIP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'form'];

    // Tags auto-cerrantes peligrosos que se eliminan
    private const STRIP_VOID = ['input', 'meta', 'link', 'base'];

    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        // 1. Eliminar tags con su contenido interno (script, iframe, etc.)
        foreach (self::STRIP_WITH_CONTENT as $tag) {
            $html = preg_replace('/<\s*' . $tag . '[\s>][\s\S]*?<\s*\/\s*' . $tag . '\s*>/i', '', $html);
            // También la variante auto-cerrada <script/>
            $html = preg_replace('/<\s*' . $tag . '\b[^>]*\/>/i', '', $html);
        }

        // 2. Eliminar void tags peligrosos
        foreach (self::STRIP_VOID as $tag) {
            $html = preg_replace('/<\s*' . $tag . '\b[^>]*\/?>/i', '', $html);
        }

        // 3. Eliminar manejadores de eventos en cualquier atributo (onclick, onload, onerror…)
        $html = preg_replace('/\s+on\w+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*\'[^\']*\'/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $html);

        // 4. Eliminar protocolo javascript: en href y src
        $html = preg_replace('/(\bhref\s*=\s*["\']?)\s*javascript:[^"\'>\s]*/i', '$1#', $html);
        $html = preg_replace('/(\bsrc\s*=\s*["\']?)\s*javascript:[^"\'>\s]*/i', '$1#', $html);

        // 5. Eliminar data: URIs en src (posible vector de XSS en navegadores viejos)
        $html = preg_replace('/(\bsrc\s*=\s*["\']?)\s*data:[^"\'>\s]*/i', '$1#', $html);

        return $html;
    }
}
