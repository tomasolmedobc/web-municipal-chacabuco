<?php

return [
    'password' => env('ACCESO_MUNICIPAL_PASSWORD', ''),

    'links' => [
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_1_TITULO', 'Webmail municipal'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_1_DESCRIPCION', 'Ingreso al correo institucional del municipio.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_1_ICONO', 'fa-envelope-open-text'),
            'url' => env('ACCESO_MUNICIPAL_LINK_1_URL', 'https://webmail.webhostingmovistar.com.ar/'),
        ],
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_2_TITULO', 'Recibos de sueldo'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_2_DESCRIPCION', 'Consulta de recibos y documentacion laboral del personal.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_2_ICONO', 'fa-file-invoice-dollar'),
            'url' => env('ACCESO_MUNICIPAL_LINK_2_URL', 'https://personal.chacabuco.gob.ar/'),
        ],
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_3_TITULO', 'LabWin'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_3_DESCRIPCION', 'Acceso al sistema de laboratorio y gestion hospitalaria.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_3_ICONO', 'fa-microscope'),
            'url' => env('ACCESO_MUNICIPAL_LINK_3_URL', 'https://www.biodatasa.com.ar/lwcloud/hospchaca/login'),
        ],
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_4_TITULO', '147'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_4_DESCRIPCION', 'Gestion y seguimiento de reclamos ingresados al 147.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_4_ICONO', 'fa-comments'),
            'url' => env('ACCESO_MUNICIPAL_LINK_4_URL', 'https://reclamos.chacabuco.gob.ar/'),
        ],
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_5_TITULO', 'Sistemas de Combustibles'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_5_DESCRIPCION', 'Control y administracion de cargas de combustible.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_5_ICONO', 'fa-gas-pump'),
            'url' => env('ACCESO_MUNICIPAL_LINK_5_URL', 'https://combustibles.chacabuco.gob.ar/'),
        ],
        [
            'titulo' => env('ACCESO_MUNICIPAL_LINK_6_TITULO', 'Soporte tecnico'),
            'descripcion' => env('ACCESO_MUNICIPAL_LINK_6_DESCRIPCION', 'Mesa de ayuda para asistencia tecnica e informatica.'),
            'icono' => env('ACCESO_MUNICIPAL_LINK_6_ICONO', 'fa-screwdriver-wrench'),
            'url' => env('ACCESO_MUNICIPAL_LINK_6_URL', 'https://soporte.chacabuco.gob.ar/'),
        ],
    ],
];
