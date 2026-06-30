<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ts = now();

        $datos = [
            // Gobierno Municipal
            ['nombre' => 'Gobierno de Chacabuco',                     'categoria' => 'Gobierno Municipal', 'direccion' => 'Reconquista 26',                       'telefono' => '(02352) 470300',             'email' => 'municipalidad@chacabuco.gob.ar',         'orden' => 1],
            ['nombre' => 'Consejo Deliberante (HCD)',                  'categoria' => 'Gobierno Municipal', 'direccion' => null,                                   'telefono' => '470353',                     'email' => null,                                     'orden' => 2],
            ['nombre' => 'Bromatología',                               'categoria' => 'Gobierno Municipal', 'direccion' => 'Villegas 550',                         'telefono' => '470355',                     'email' => 'bromatologiachacabuco@yahoo.com',         'orden' => 3],
            ['nombre' => 'Corralón Municipal',                         'categoria' => 'Gobierno Municipal', 'direccion' => 'Villegas y Sob. Nac.',                  'telefono' => '470379',                     'email' => null,                                     'orden' => 4],
            ['nombre' => 'Habilitaciones',                             'categoria' => 'Gobierno Municipal', 'direccion' => null,                                   'telefono' => '470348',                     'email' => null,                                     'orden' => 5],
            ['nombre' => 'Licencia de Conducir',                       'categoria' => 'Gobierno Municipal', 'direccion' => 'Alberdi 64',                           'telefono' => '470350',                     'email' => null,                                     'orden' => 6],
            ['nombre' => 'Educación Vial',                             'categoria' => 'Gobierno Municipal', 'direccion' => 'San Martín 121',                       'telefono' => '470309',                     'email' => 'monitoreo@chacabuco.gob.ar',              'orden' => 7],
            ['nombre' => 'Centro de Prevención Ciudadana - Botón Antipánico', 'categoria' => 'Gobierno Municipal', 'direccion' => 'San Martín 121',                'telefono' => '2352 463375',                'email' => null,                                     'orden' => 8],
            ['nombre' => 'Producción y Medio Ambiente',                'categoria' => 'Gobierno Municipal', 'direccion' => 'R. Escalada de San Martín 236',        'telefono' => '470316',                     'email' => 'medioambientechacabuco@gmail.com',        'orden' => 9],
            ['nombre' => 'Registro Civil',                             'categoria' => 'Gobierno Municipal', 'direccion' => 'Padre Doglia 101',                     'telefono' => '470339 / 424048',            'email' => null,                                     'orden' => 10],
            ['nombre' => 'Registro del Automotor',                     'categoria' => 'Gobierno Municipal', 'direccion' => null,                                   'telefono' => '451357',                     'email' => null,                                     'orden' => 11],
            ['nombre' => 'Casa de Campo',                              'categoria' => 'Gobierno Municipal', 'direccion' => 'Alvear 60',                            'telefono' => '470312',                     'email' => 'guiachacabuco@gmail.com',                 'orden' => 12],
            ['nombre' => 'Cementerio',                                 'categoria' => 'Gobierno Municipal', 'direccion' => null,                                   'telefono' => '2352 401256',                'email' => null,                                     'orden' => 13],

            // Salud
            ['nombre' => 'Hospital Municipal Chacabuco',               'categoria' => 'Salud', 'direccion' => 'Av. Garay 224',                                    'telefono' => '430828 / 15442086',          'email' => null,                                     'orden' => 1],
            ['nombre' => 'Hospital "Tomás Keating" Castilla',          'categoria' => 'Salud', 'direccion' => 'Calle 102 y 105 - Castilla',                       'telefono' => '(2324) 492015',              'email' => 'castillahospital@gmail.com',              'orden' => 2],
            ['nombre' => 'Dirección de Salud',                         'categoria' => 'Salud', 'direccion' => null,                                               'telefono' => '430828 / 430799 / 430832',   'email' => null,                                     'orden' => 3],
            ['nombre' => 'Tomógrafo Hospital Chacabuco',               'categoria' => 'Salud', 'direccion' => null,                                               'telefono' => '471232',                     'email' => null,                                     'orden' => 4],
            ['nombre' => 'CAPS "Madre Teresa de Calcuta"',             'categoria' => 'Salud', 'direccion' => 'Cucha Cucha',                                      'telefono' => null,                         'email' => null,                                     'orden' => 5],
            ['nombre' => 'CAPS "Mataderos"',                           'categoria' => 'Salud', 'direccion' => 'Acc. H. Irigoyen y Calle 159',                     'telefono' => null,                         'email' => null,                                     'orden' => 6],
            ['nombre' => 'CAPS "San Cayetano"',                        'categoria' => 'Salud', 'direccion' => 'Mateo Muro y Av. Perón',                           'telefono' => null,                         'email' => null,                                     'orden' => 7],
            ['nombre' => 'CAPS "Santa Clara de Asís"',                 'categoria' => 'Salud', 'direccion' => 'Acc. Juan XXIII Continuación',                     'telefono' => '2352 464555',                'email' => null,                                     'orden' => 8],
            ['nombre' => 'CAPS "Ubaldo Martínez"',                     'categoria' => 'Salud', 'direccion' => 'Padre Doglia 639',                                 'telefono' => '2352 528834',                'email' => null,                                     'orden' => 9],
            ['nombre' => 'CAPS "9 de Julio"',                          'categoria' => 'Salud', 'direccion' => '9 de Julio y Casasco',                             'telefono' => '429112',                     'email' => null,                                     'orden' => 10],
            ['nombre' => 'CAPS "La Unión"',                            'categoria' => 'Salud', 'direccion' => 'San Isidro Labrador 120',                          'telefono' => '429458',                     'email' => null,                                     'orden' => 11],
            ['nombre' => 'CAPS "Rivadavia"',                           'categoria' => 'Salud', 'direccion' => 'Rivadavia 650',                                    'telefono' => '470354 / 2352 525560',       'email' => null,                                     'orden' => 12],
            ['nombre' => 'CAPS "Las Palmeras"',                        'categoria' => 'Salud', 'direccion' => 'Mateo Barón 440',                                  'telefono' => '428582',                     'email' => null,                                     'orden' => 13],
            ['nombre' => 'CAPS "René Favaloro"',                       'categoria' => 'Salud', 'direccion' => 'Ituzaingó 1100',                                   'telefono' => '2352 468426',                'email' => null,                                     'orden' => 14],
            ['nombre' => 'Centro Comunitario de Salud Mental',         'categoria' => 'Salud', 'direccion' => 'Cervantes y San Luis',                             'telefono' => '2352 486409 / 470305',       'email' => 'ccsmchacabuco@gmail.com',                 'orden' => 15],
            ['nombre' => 'Unidad Sanitaria O\'Higgins',                'categoria' => 'Salud', 'direccion' => 'Av. Chacabuco y Mitre',                            'telefono' => '2364 492158',                'email' => null,                                     'orden' => 16],
            ['nombre' => 'I.O.M.A',                                    'categoria' => 'Salud', 'direccion' => 'R. Escalada de San Martín 83',                     'telefono' => '431741',                     'email' => 'deleg_chacabuco@ioma.gba.gov.ar',         'orden' => 17],

            // Seguridad
            ['nombre' => 'Policía - Urgencias',                        'categoria' => 'Seguridad', 'direccion' => 'San Juan 25',                                  'telefono' => '101',                        'email' => null,                                     'orden' => 1],
            ['nombre' => 'Comisaría Chacabuco',                        'categoria' => 'Seguridad', 'direccion' => 'San Juan 24',                                  'telefono' => '431166 / 431120',            'email' => 'criachacabuco@gmail.com',                 'orden' => 2],
            ['nombre' => 'Comisaría de la Mujer y Familia',            'categoria' => 'Seguridad', 'direccion' => 'Alberdi 62',                                   'telefono' => '428471 / 431292',            'email' => 'criamujeryfliachacabuco@gmail.com',       'orden' => 3],
            ['nombre' => 'Bomberos Chacabuco',                         'categoria' => 'Seguridad', 'direccion' => 'Zapiola 276',                                  'telefono' => '427969 / 428222',            'email' => 'guardiabvchacabuco@hotmail.com',          'orden' => 4],
            ['nombre' => 'Bomberos O\'Higgins',                        'categoria' => 'Seguridad', 'direccion' => 'Bartolomé Mitre 175',                          'telefono' => '(2364) 492222',              'email' => 'bomberosohiggins@yahoo.com.ar',           'orden' => 5],
            ['nombre' => 'Defensa Civil',                              'categoria' => 'Seguridad', 'direccion' => 'Elguea Román 902',                             'telefono' => '470340 (emerg.) / 2352 417765', 'email' => 'defensacivilchacabuco@gmail.com',      'orden' => 6],

            // Servicios Públicos
            ['nombre' => 'Urgencias Agua Corriente',                   'categoria' => 'Servicios Públicos', 'direccion' => 'Av. Alsina Cont.',                    'telefono' => '470344',                     'email' => 'usinaosanitariaschacabuco@gmail.com',     'orden' => 1],
            ['nombre' => 'Urgencias Cloacas',                          'categoria' => 'Servicios Públicos', 'direccion' => 'Entre Ríos y Junín',                  'telefono' => '470405',                     'email' => null,                                     'orden' => 2],
            ['nombre' => 'Obras Sanitarias (agua y cloacas)',          'categoria' => 'Servicios Públicos', 'direccion' => 'Entre Ríos y Junín',                  'telefono' => '2352-559136 / 470344',       'email' => null,                                     'orden' => 3],
            ['nombre' => 'Cooperativa Eléctrica',                      'categoria' => 'Servicios Públicos', 'direccion' => 'Laprida 277',                         'telefono' => '470800',                     'email' => null,                                     'orden' => 4],
            ['nombre' => 'Coopenet',                                   'categoria' => 'Servicios Públicos', 'direccion' => null,                                  'telefono' => '470000',                     'email' => null,                                     'orden' => 5],

            // Educación y Cultura
            ['nombre' => 'Biblioteca Municipal',                       'categoria' => 'Educación y Cultura', 'direccion' => 'Moreno 178',                          'telefono' => '470313 (int. 104)',          'email' => 'culturachacabuco2024@gmail.com',          'orden' => 1],
            ['nombre' => 'Casa de la Cultura',                         'categoria' => 'Educación y Cultura', 'direccion' => 'Moreno 178',                          'telefono' => '470314 / 470315',            'email' => 'admchacabucocultura@gmail.com',           'orden' => 2],
            ['nombre' => 'Teatro Italiano',                            'categoria' => 'Educación y Cultura', 'direccion' => 'Av. Alsina 29',                       'telefono' => '428776',                     'email' => 'teatroitalianochacabuco@hotmail.com',     'orden' => 3],
            ['nombre' => 'Galería de Arte',                            'categoria' => 'Educación y Cultura', 'direccion' => null,                                  'telefono' => '429024',                     'email' => null,                                     'orden' => 4],
            ['nombre' => 'Escuela de Actividades Culturales',          'categoria' => 'Educación y Cultura', 'direccion' => 'Avellaneda 127',                      'telefono' => '470307 / 2352 440982',       'email' => 'chacabucoeacprensa@gmail.com',            'orden' => 5],
            ['nombre' => 'Centro de Formación Profesional 401',        'categoria' => 'Educación y Cultura', 'direccion' => 'Almafuerte 294',                      'telefono' => '431651',                     'email' => null,                                     'orden' => 6],
            ['nombre' => 'Educación (Dirección)',                      'categoria' => 'Educación y Cultura', 'direccion' => '25 de Mayo, 1er piso of. 9',          'telefono' => '470323',                     'email' => 'educacion@chacabuco.gob.ar',              'orden' => 7],
            ['nombre' => 'Consejo Escola',                             'categoria' => 'Educación y Cultura', 'direccion' => 'Reconquista 48',                      'telefono' => '431703 / 431668',            'email' => null,                                     'orden' => 8],
            ['nombre' => 'UTN',                                        'categoria' => 'Educación y Cultura', 'direccion' => null,                                  'telefono' => '2352 559085',                'email' => null,                                     'orden' => 9],
            ['nombre' => 'SAD',                                        'categoria' => 'Educación y Cultura', 'direccion' => 'Pueyrredón 239',                      'telefono' => '431516',                     'email' => 'sad026@abc.gob.ar',                       'orden' => 10],

            // Social
            ['nombre' => 'Desarrollo Social',                          'categoria' => 'Social', 'direccion' => 'Alberdi 28',                                      'telefono' => '470303 / 470302',            'email' => 'desarrollo.social.chacabuco2@gmail.com', 'orden' => 1],
            ['nombre' => 'ANSES',                                      'categoria' => 'Social', 'direccion' => 'Sarmiento y Cervantes',                           'telefono' => '(02352) 432342',             'email' => null,                                     'orden' => 2],
            ['nombre' => 'APRID',                                      'categoria' => 'Social', 'direccion' => 'Casela 404',                                      'telefono' => '470338',                     'email' => null,                                     'orden' => 3],
            ['nombre' => 'Asilo de Ancianos',                          'categoria' => 'Social', 'direccion' => 'Av. Solís y San Martín',                          'telefono' => '(02352) 428512',             'email' => null,                                     'orden' => 4],
            ['nombre' => 'C.I.C La Ilusión',                          'categoria' => 'Social', 'direccion' => 'La Rioja 552',                                    'telefono' => '2352 528656',                'email' => null,                                     'orden' => 5],
            ['nombre' => 'C.I.C Los Nogales',                         'categoria' => 'Social', 'direccion' => 'Duberty y Conesa',                                'telefono' => '427484',                     'email' => null,                                     'orden' => 6],
            ['nombre' => 'C.I.C Los Pioneros / San Antonio',          'categoria' => 'Social', 'direccion' => 'Sgo. del Estero y C. Veronelli',                  'telefono' => '2352 468433',                'email' => null,                                     'orden' => 7],
            ['nombre' => 'C.I.C Alcira de la Peña',                   'categoria' => 'Social', 'direccion' => 'Junín 700',                                       'telefono' => '2352 444214 / 2352 401583',  'email' => null,                                     'orden' => 8],
            ['nombre' => 'Casa de Tierra',                             'categoria' => 'Social', 'direccion' => 'Pueyrredón 83',                                   'telefono' => '470325',                     'email' => 'casadetierrachacabuco@gmail.com',         'orden' => 9],
            ['nombre' => 'I.P.S',                                      'categoria' => 'Social', 'direccion' => 'Pueyrredón 83',                                   'telefono' => '470331',                     'email' => 'prevision.chacabuco@gmail.com',           'orden' => 10],
            ['nombre' => 'Servicio Local',                             'categoria' => 'Social', 'direccion' => 'Alberdi 127',                                     'telefono' => '452837',                     'email' => null,                                     'orden' => 11],
            ['nombre' => 'Oficina de Empleo',                          'categoria' => 'Social', 'direccion' => 'San Juan 65',                                     'telefono' => '428192',                     'email' => 'busquedaoe@chacabuco.gob.ar',             'orden' => 12],

            // Justicia
            ['nombre' => 'Juzgado de Paz',                             'categoria' => 'Justicia', 'direccion' => 'Moreno 65',                                     'telefono' => '428427',                     'email' => 'jpchacabuco@jusbuenosaires.gov.ar',       'orden' => 1],
            ['nombre' => 'Juzgado de Faltas',                          'categoria' => 'Justicia', 'direccion' => 'San Martín 81',                                 'telefono' => '470308',                     'email' => null,                                     'orden' => 2],
            ['nombre' => 'Fiscalía Descentralizada y UFI N°11',        'categoria' => 'Justicia', 'direccion' => 'Av. Saavedra 170',                              'telefono' => '432220',                     'email' => 'ufijn11.ju@mpba.gov.ar',                  'orden' => 3],
            ['nombre' => 'Defensa al Consumidor (OMIC)',               'categoria' => 'Justicia', 'direccion' => 'Alberdi 64',                                    'telefono' => '470351',                     'email' => 'omicchacabuco@gmail.com',                 'orden' => 4],
            ['nombre' => 'Defensor del Pueblo',                        'categoria' => 'Justicia', 'direccion' => 'San Juan 23',                                   'telefono' => '427723',                     'email' => 'defensoria@chacabuco.org.ar',             'orden' => 5],

            // Comercio y Trabajo
            ['nombre' => 'Cámara de Comercio',                         'categoria' => 'Comercio y Trabajo', 'direccion' => 'Av. Saavedra 51',                     'telefono' => '2352 431675',                'email' => 'certificadocamarachacabuco@hotmail.com',  'orden' => 1],
            ['nombre' => 'Cámara de Transporte',                       'categoria' => 'Comercio y Trabajo', 'direccion' => 'Av. Saavedra 61',                     'telefono' => '431675',                     'email' => 'certificadoscamarachacabuco@hotmail.com', 'orden' => 2],
            ['nombre' => 'Cooperativa de Transportistas',              'categoria' => 'Comercio y Trabajo', 'direccion' => 'Av. Vieytes 220',                     'telefono' => '427176',                     'email' => 'cooptranschacabuco@gmail.com',            'orden' => 3],
            ['nombre' => 'Secretaría de Trabajo',                      'categoria' => 'Comercio y Trabajo', 'direccion' => null,                                  'telefono' => '452445',                     'email' => null,                                     'orden' => 4],

            // Deportes
            ['nombre' => 'Deportes (Dirección)',                       'categoria' => 'Deportes', 'direccion' => 'Solís 215',                                     'telefono' => '470326',                     'email' => 'deportes@chacabuco.gob.ar',               'orden' => 1],
            ['nombre' => 'Agrupación Atlética',                        'categoria' => 'Deportes', 'direccion' => null,                                            'telefono' => '430350',                     'email' => null,                                     'orden' => 2],
            ['nombre' => 'C.E.F Nro. 20',                             'categoria' => 'Deportes', 'direccion' => 'Solís 215',                                     'telefono' => '470310 / 2352 446219',       'email' => 'cef20chacabuco@abc.gob.ar',               'orden' => 3],

            // Delegaciones
            ['nombre' => 'Delegación de Castilla',                     'categoria' => 'Delegaciones', 'direccion' => 'Calle 10 Nº 77 - Castilla',                 'telefono' => '(2324) 492007',              'email' => 'delegacion_castilla@hotmail.com',         'orden' => 1],
            ['nombre' => 'Delegación O\'Higgins',                      'categoria' => 'Delegaciones', 'direccion' => 'Levalle 176 - O\'Higgins',                  'telefono' => '(2364) 492038',              'email' => 'oh.delegacion@chacabuco.gob.ar',          'orden' => 2],
            ['nombre' => 'Delegación Rawson',                          'categoria' => 'Delegaciones', 'direccion' => 'Cirilo Sangiani 27 - Rawson',               'telefono' => '(2352) 491509 / 491512',     'email' => 'delegacion-rawson2018@hotmail.com',       'orden' => 3],
        ];

        $rows = array_map(
            fn ($r) => array_merge($r, ['estado' => 'visible', 'created_at' => $ts, 'updated_at' => $ts]),
            $datos
        );

        DB::table('telefonos_utiles')->insert($rows);
    }

    public function down(): void
    {
        DB::table('telefonos_utiles')->truncate();
    }
};
