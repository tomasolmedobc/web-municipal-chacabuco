<?php

return [
    'titulo' => 'Habilitaciones',
    'bajada' => 'Informacion, requisitos y formularios para iniciar, actualizar o consultar tramites de habilitacion comercial e industrial.',

    'contacto' => [
        'area' => 'Area de Habilitaciones',
        'direccion' => 'Remedio de Escalada de San Martin 236',
        'horario' => 'Lunes a viernes de 7:30 a 12:30 hs',
        'telefono' => '452714',
        'mail' => 'habilitacioneschacabuco@gmail.com',
    ],

    'intro' => [
        'La oficina de Habilitaciones es la encargada de coordinar y otorgar las habilitaciones correspondientes a locales comerciales, prestaciones de servicios, radicaciones e industrias del partido de Chacabuco.',
        'La solicitud de prefactibilidad permite evaluar de manera simple si un emprendimiento cumple con los requisitos vigentes para iniciar una habilitacion.',
        'El area articula su labor junto a otras dependencias municipales que observan ubicacion, procedimientos, riesgos y normativa aplicable para concluir con la entrega del certificado.',
        'El objetivo es culminar el tramite en tiempo y forma, cumpliendo ordenanzas y normativas vigentes.',
    ],

    'alerta' => [
        'titulo' => 'Guia para iniciar la habilitacion etapa por etapa',
        'texto' => 'No inicie actividades ni se comprometa con alquileres, compra de inmuebles u obras hasta contar con la prefactibilidad o certificado correspondiente. El incumplimiento de normas y obligaciones puede dar lugar a sanciones.',
    ],

    'tramites_online' => [
        [
            'titulo' => 'Tramite Online Prefactibilidad Comercial',
            'descripcion' => 'Evalua si el emprendimiento cumple con los requisitos vigentes antes de iniciar la habilitacion.',
            'icono' => 'fa-clipboard-check',
            'url' => '#',
        ],
        [
            'titulo' => 'Tramite Online Habilitacion en General',
            'descripcion' => 'Inicia el tramite de habilitacion comercial de manera digital.',
            'icono' => 'fa-store',
            'url' => '#',
        ],
    ],

    'acordeones' => [
        [
            'titulo' => 'Tramite de Prefactibilidad',
            'abierto' => true,
            'items' => [
                'Completar nota Solicitud de Prefactibilidad.',
                'Adjuntar fotocopia de DNI del solicitante.',
                'Adjuntar fotocopia del recibo de impuesto municipal o recibo inmobiliario de ARBA del inmueble, con datos catastrales.',
                'Completar, sellar y firmar la solicitud por personal de Catastro.',
                'Adjuntar copia de escritura, contrato de alquiler o comodato, segun corresponda.',
                'Cumplir los pasos anteriores debera abonar tasa de prefactibilidad segun ordenanza impositiva vigente.',
                'Presentar la documentacion por Mesa de Entrada del palacio municipal.',
                'Se otorgara numero de expediente para seguir el tramite desde la consulta de expedientes.',
                'No iniciar actividades, obras, alquileres o compras hasta contar con la prefactibilidad de habilitacion.',
            ],
        ],
        [
            'titulo' => 'Tramite de Habilitacion Comercial',
            'items' => [
                'Presentar formulario de habilitacion completo.',
                'Adjuntar documentacion del titular, razon social o responsable legal.',
                'Incorporar constancia de CUIT e inscripciones correspondientes.',
                'Presentar documentacion del inmueble y requisitos especificos segun rubro.',
                'Esperar la intervencion de las areas municipales que correspondan.',
            ],
        ],
        [
            'titulo' => 'Tramite Cambio de Rubro',
            'items' => [
                'Presentar nota de solicitud de cambio de rubro.',
                'Adjuntar documentacion respaldatoria del comercio.',
                'Indicar rubro actual y rubro solicitado.',
                'Esperar evaluacion de las areas tecnicas correspondientes.',
            ],
        ],
        [
            'titulo' => 'Tramite Cambio de Razon Social',
            'items' => [
                'Presentar nota de cambio de razon social.',
                'Adjuntar documentacion del titular anterior y nuevo titular.',
                'Incluir constancias fiscales y documentacion comercial actualizada.',
                'Esperar evaluacion y autorizacion municipal.',
            ],
        ],
        [
            'titulo' => 'Tramite de Transferencia de Negocio',
            'items' => [
                'Presentar solicitud de transferencia.',
                'Adjuntar documentacion de ambas partes.',
                'Acreditar situacion del comercio y del inmueble.',
                'Cumplir requisitos complementarios si el rubro lo requiere.',
            ],
        ],
        [
            'titulo' => 'Tramite Traslado de Negocio',
            'items' => [
                'Presentar nota de traslado indicando domicilio actual y nuevo domicilio.',
                'Adjuntar documentacion del nuevo inmueble.',
                'Solicitar evaluacion de prefactibilidad del nuevo lugar.',
                'Esperar autorizacion antes de iniciar actividad en el nuevo domicilio.',
            ],
        ],
        [
            'titulo' => 'Cese de Habilitacion Comercial',
            'items' => [
                'Presentar nota solicitando el cese de actividad.',
                'Indicar datos del comercio, titular y fecha de cese.',
                'Adjuntar documentacion que corresponda segun el caso.',
                'Esperar registracion municipal del cese.',
            ],
        ],
    ],

    'archivos' => [
        [
            'titulo' => 'Solicitud de Pre Factibilidad',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/03 SOLICITUD DE PREFACTIBILIDAD 2021.pdf',
        ],
        [
            'titulo' => 'Formulario 01 Habilitaciones en General',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/Formulario de Habilitacion General 2021.pdf',
        ],
        [
            'titulo' => 'Solicitud de Habilitaciones de Comercios e Industrias',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/Solicitud de Habilitacion de Comercios e Industrias Anexo Formulario 01.pdf',
        ],
        [
            'titulo' => 'Formulario Tasa por Inspeccion de Seguridad e Higiene',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/Formulario Tasa por Inspeccion de Seg. e Hig. 2021.pdf',
        ],
        [
            'titulo' => 'Radicacion',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Solicitud de renovacion de radicacion.pdf',
        ],
        [
            'titulo' => 'Requisitos RAN Guia practica',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Requisitos-RAN-Guía-práctica-para-Comerciantes-y-Ciudadanos-_1_.pdf',
        ],
        [
            'titulo' => 'Cambio de Razon Social',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/prefactibilidad/Cambio de razon social.pdf',
        ],
        [
            'titulo' => 'Requisito para ReBA',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Solicitud ReBA.pdf',
        ],
        [
            'titulo' => 'Cambio de Rubros',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Cambio de rubros.pdf',
        ],
        [
            'titulo' => 'Cese de Actividades Comerciales',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/prefactibilidad/Cese de actividades comerciales.pdf',
        ],
        [
            'titulo' => 'Transferencia de Negocios',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/prefactibilidad/transferencia de negocio.pdf',
        ],
        [
            'titulo' => 'Cambio de Domicilio',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/prefactibilidad/traslado de negocio.pdf',
        ],
        [
            'titulo' => 'Requisitos de Medio Ambiente',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/REQUISITOS de MEDIO AMBIENTE.pdf',
        ],
        [
            'titulo' => 'Requisitos de Bromatologia',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/REQUISITOS de BROMATOLOGIA.pdf',
        ],
        [
            'titulo' => 'Requisitos de Defensa Civil',
            'tipo' => 'PDF',
            'url' => '/images/habilitacionespdf/Botonera/REQUISITOS de DEFENSA CIVIL.pdf',
        ],
    ],

    'ordenanzas' => [
        'AGROQUIMICOS - Ordenanza 5243/10',
        'ANTENAS TELEFONIA CELULAR - Ordenanza 5231/10',
        'BALCONES GASTRONOMICOS - Ordenanza 8969/21',
        'BEBIDAS ALCOHOLICAS - PROHIBICION DE VENTA EN EVENTOS - Ordenanza 4915/09',
        'CABARETS, DANCING, NIGHT CLUB Y BOITES - Ordenanza 4811/09',
        'CARROS GASTRONOMICOS / FOOD TRUCK - Ordenanza 7427/17',
        'DISTANCIAS MINIMAS DE COMERCIOS - Ordenanza 3269/03',
        'ESTABLECIMIENTOS DE DIVERSION NOCTURNA - Ordenanza 4914/09',
        'FARMACIAS - Ordenanza 8154/19',
        'GIMNASIOS - Ordenanza 3952/05',
    ],
];
