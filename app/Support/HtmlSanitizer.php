<?php

namespace App\Support;

class HtmlSanitizer
{
    private static ?\HTMLPurifier $purifier = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        return self::purifier()->purify($html);
    }

    private static function purifier(): \HTMLPurifier
    {
        if (self::$purifier !== null) {
            return self::$purifier;
        }

        $config = \HTMLPurifier_Config::createDefault();

        // Cache para no reconstruir la definición en cada llamada
        $cacheDir = storage_path('app/htmlpurifier');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cacheDir);

        // Allowlist de elementos que TinyMCE puede generar
        $config->set('HTML.Allowed',
            'p[style],h1,h2,h3,h4,h5,h6,' .
            'ul,ol,li,' .
            'blockquote,pre,code,hr,br,' .
            'strong,b,em,i,u,s,sub,sup,' .
            'a[href|title|target|rel],' .
            'img[src|alt|width|height|loading],' .
            'table[style],thead,tbody,tfoot,' .
            'tr,td[colspan|rowspan|style],th[colspan|rowspan|style],' .
            'span[class|style],div[class|style]'
        );

        // Solo http, https, mailto — bloquea javascript:, vbscript:, data: automáticamente
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        // Permitir target="_blank" en links (TinyMCE lo usa)
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // ID para cachear la definición personalizada
        $config->set('HTML.DefinitionID', 'chacabuco-tinymce');
        $config->set('HTML.DefinitionRev', 1);

        // Restringir estilos inline a propiedades no peligrosas
        $config->set('CSS.AllowedProperties', [
            'color', 'background-color',
            'font-size', 'font-weight', 'font-style', 'font-family',
            'text-align', 'text-decoration',
            'margin', 'margin-top', 'margin-bottom', 'margin-left', 'margin-right',
            'padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
            'width', 'height', 'max-width',
            'border', 'border-collapse', 'border-color', 'border-style', 'border-width',
            'vertical-align', 'list-style-type',
        ]);

        // Registrar atributo HTML5 'loading' (lazy loading) que HTMLPurifier no conoce nativamente
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addAttribute('img', 'loading', 'Enum#lazy,eager,auto');
        }

        self::$purifier = new \HTMLPurifier($config);

        return self::$purifier;
    }
}
