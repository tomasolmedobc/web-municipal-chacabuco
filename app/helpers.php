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