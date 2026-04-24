<?php

use App\Models\Configuracion;

if (!function_exists('config_sistema')) {
    function config_sistema($clave, $default = null)
    {
        return Configuracion::where('clave', $clave)->value('valor') ?? $default;
    }
}