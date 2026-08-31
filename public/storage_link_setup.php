<?php
// ARCHIVO DE USO ÚNICO — ELIMINARLO DESPUÉS DE EJECUTAR
// Acceder una sola vez desde el navegador: https://tudominio.com/storage_link_setup.php

$target = dirname(__DIR__) . '/storage/app/public';
$link   = __DIR__ . '/storage';

if (file_exists($link) || is_link($link)) {
    echo 'El symlink ya existe o ya hay una carpeta /public/storage. No se hizo nada.';
    exit;
}

if (symlink($target, $link)) {
    echo 'OK: symlink creado. Eliminá este archivo ahora.';
} else {
    echo 'ERROR: no se pudo crear el symlink. El servidor puede no permitirlo. Pedíselo al proveedor.';
}
