<?php

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;

if (!function_exists('config_sistema')) {
    function config_sistema($clave, $default = null)
    {
        return Cache::remember("config_sistema.{$clave}", 600, function () use ($clave) {
            return Configuracion::where('clave', $clave)->value('valor');
        }) ?? $default;
    }
}

if (!function_exists('video_embed_url')) {
    function video_embed_url(?string $url): ?string
    {
        if (!$url || !trim($url)) {
            return null;
        }
        $url = trim($url);

        // YouTube: youtube.com/watch?v=ID
        if (preg_match('/youtube\.com\/watch\?(?:[^&]+&)*v=([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // YouTube short: youtu.be/ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // YouTube Shorts: youtube.com/shorts/ID
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // Vimeo: vimeo.com/ID
        if (preg_match('/vimeo\.com\/(?:[^\/]+\/)*(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }
}

if (!function_exists('config_sistema_flush')) {
    function config_sistema_flush($clave = null)
    {
        if ($clave) {
            Cache::forget("config_sistema.{$clave}");
        } else {
            Cache::flush();
        }
    }
}