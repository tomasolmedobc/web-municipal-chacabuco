<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carnet_configuracion', function (Blueprint $table) {
            $table->id();
            $table->text('intro_texto')->nullable();
            $table->string('alerta_info', 500)->nullable();
            $table->string('aviso_ubicacion', 500)->nullable();
            $table->string('paso1_titulo')->default('Curso Teórico-Práctico y Examen Teórico');
            $table->text('paso1_contenido')->nullable();
            $table->string('paso2_titulo')->default('Examen Práctico de Idoneidad Conductiva');
            $table->text('paso2_contenido')->nullable();
            $table->string('paso3_titulo')->default('Examen Psicofísico');
            $table->text('paso3_contenido')->nullable();
            $table->string('paso4_titulo')->default('Control de la Documentación y Recopilación de Datos Personales');
            $table->text('paso4_contenido')->nullable();
            $table->text('licencia_digital_contenido')->nullable();
            $table->timestamps();
        });

        DB::table('carnet_configuracion')->insert([
            'intro_texto'   => '<p>La Licencia Nacional de Conducir es un documento único que la autoridad competente de cada jurisdicción provincial, municipal y de la Ciudad Autónoma de Buenos Aires, otorga a un ciudadano con el objeto de habilitarlo legalmente a conducir un vehículo, sea con carácter particular o profesional.</p>',
            'alerta_info'   => 'Ahora las licencias de conducir se pueden obtener en <strong>24 horas</strong>',
            'aviso_ubicacion' => '<strong>Atención</strong> <em>los siguientes pasos para obtener la <strong>Licencia Nacional de Conducir</strong> se realizan en la <strong>Oficina de Licencias de Conducir. Tel. 470350 Alberdi 64</strong></em>',
            'paso1_titulo'  => 'Curso Teórico-Práctico y Examen Teórico',
            'paso1_contenido' => '<p>Este curso de asistencia obligatoria, tiene como su principal objetivo educar, de un modo íntegro, a los postulantes a la obtención de dicha Licencia, en todo lo concerniente a seguridad vial, ética ciudadana, conducción, señalamiento y legislación.</p><p>Los aspirantes deben obtener una calificación promedio igual o superior al noventa por ciento (90%) para ser considerados aptos para obtener la licencia. Recuerde que para la realización del Cursos Teórico-Práctico y Examen Teórico deberá solicitar turno según el cronograma a continuación.</p><table><thead><tr><th colspan="2" style="text-align:center">Cronograma de solicitud de turnos para Curso y Examen Teórico</th></tr></thead><tbody><tr><td>Aspirantes a obtener por primera vez la licencia de conducir</td><td>aprox. 15 días de anticipación</td></tr><tr><td>Quienes renueven la licencia cuyo plazo de validez hubiera expirado al momento de solicitarlo</td><td>aprox. 7 días de anticipación</td></tr><tr><td>Mayores de 65 años que renueven su licencia</td><td>aprox. 3 días de anticipación</td></tr></tbody></table><table><thead><tr><th colspan="3" style="text-align:center">Información sobre días y horarios de dictado de Cursos.</th></tr><tr><th>Aspirantes</th><th>Días y horarios</th><th>Duración aprox.</th></tr></thead><tbody><tr><td>Quienes lo hagan por primera vez</td><td>Jueves 8 y 18 horas</td><td>3 horas, 45 minutos</td></tr><tr><td>Quienes renueven la licencia cuyo plazo de validez hubiera expirado al momento de solicitarlo</td><td>Miércoles 8 horas</td><td>1 hora y media</td></tr><tr><td>Mayores de 65 años que renueven su licencia</td><td>Martes y Viernes 8 horas</td><td>20 minutos</td></tr></tbody></table><div class="carnet-doc-recibir"><strong>Documento a recibir:</strong> Constancia de asistencia al Curso y Examen teórico aprobado.</div>',
            'paso2_titulo'  => 'Examen Práctico de Idoneidad Conductiva',
            'paso2_contenido' => '<p>El examen práctico de idoneidad conductiva (prueba de manejo) de carácter obligatorio y eliminatorio tiene como finalidad comprobar la idoneidad, capacidad y conocimientos básicos y necesarios para conducir aquel tipo de vehículo para el cual se solicite una licencia habilitante. El mismo, se realiza en zona urbana de bajo riesgo. Es requisito fundamental haber aprobado el Examen Teórico.</p><table><thead><tr><th colspan="2" style="text-align:center">Días y horarios para la realización del Examen Práctico de Conducción.</th></tr></thead><tbody><tr><td colspan="2" style="text-align:center">De Martes a Viernes de 8,30 a 9,30 horas</td></tr></tbody></table><div class="carnet-doc-recibir"><strong>Documento a recibir:</strong> Examen práctico (prueba de manejo) aprobado.</div>',
            'paso3_titulo'  => 'Examen Psicofísico',
            'paso3_contenido' => '<p>En este examen, se debe comprobar la idoneidad psicofísica del futuro conductor, para un desempeño seguro en la conducción de vehículos en la vía pública. El mismo estará a cargo de un Profesional Médico matriculado, quien podrá hacer uso de medios tecnológicos sistematizados y digitalizados, como instrumentos de medición. Por último, se debe dejar constancia en la Licencia Nacional de Conducir de cualquier observación, restricción o limitación de tiempo de vigencia, que surja como resultado de este examen.</p><div class="carnet-aviso"><strong>Atención!</strong> <em>El examen médico se realiza también en las Oficinas de Carnet de Conducir en Alberdi 64. Una mejora sustancial que facilita los tiempos de realización del trámite.</em></div>',
            'paso4_titulo'  => 'Control de la Documentación y Recopilación de Datos Personales',
            'paso4_contenido' => '<p>En este punto se deberá entregar la documentación recopilada en la Oficina de Licencias de Conducir. En dichas oficinas se registrarán sus huellas, su firma y se le tomará una fotografía. Estos procesos son totalmente digitales.</p><p>Se ruega a la comunidad asistir con la documentación descriptos a continuación. Requisitos:</p><ul><li>Contar con la edad correspondiente para la clase solicitada</li><li>AUTORIZACIÓN DEL REPRESENTANTE LEGAL, si fuere menor (Ley Nacional 26.579).</li><li>Examen teórico aprobado.</li><li>Examen práctico (prueba de manejo) aprobado.</li><li>Examen médico psicofísico aprobado.</li><li>Original y Copia de DNI.</li><li>Declaración jurada completa sobre el padecimiento de afecciones.</li><li>Grupo y factor sanguíneo del titular solicitado en entidad de salud competente.</li><li>Certificado expedido por el Juzgado de Faltas, sito en calle San Martín 26, sobre infracciones y sanciones penales en ocasión del tránsito, más los informes específicos para la categoría solicitada.</li></ul><table><thead><tr><th colspan="2" style="text-align:center">Días y horarios para la realización la presentación de documentación.</th></tr></thead><tbody><tr><td colspan="2" style="text-align:center">Lunes a Viernes de 7 a 13 horas</td></tr></tbody></table><div class="carnet-doc-recibir"><strong>Documento a recibir:</strong> Impuesto Municipal e Impuesto Provincial para ser abonado en las entidades de cobro: Municipalidad y Cámara de Comercio.</div><div class="carnet-aviso"><strong>Atención!</strong> <em>Efectuado el pago deberá presentar ambos comprobantes en la Oficina de Licencias de Conducir.</em></div>',
            'licencia_digital_contenido' => '<h2 style="text-align:center">Licencia Nacional de Conducir Digital</h2><div class="carnet-alerta"><p>Ahora podés acceder a tu cédula digital con la misma validez que la física, también gestionar tus vencimientos.</p><p><strong>Descárgate la aplicación, creá tu cuenta y validá tu identidad. Llevala siempre con vos en tu teléfono.</strong></p></div><h3 style="text-align:center">¿Cómo hago para tenerla?</h3><p><strong>Paso 1 de 4</strong><br>Para descargar la licencia, los usuarios deberán estar registrados con una cuenta en la App Mi Argentina, que se encuentra disponible para descargar.</p><p><strong>Paso 2 de 4</strong><br>Crear una cuenta en Mi Argentina.</p><p><strong>Paso 3 de 4</strong><br>Activar la cuenta y validar la información.</p><p><strong>Paso 4 de 4</strong><br>Validar el DNI y tomar foto del rostro.</p><h3>Cómo usar la licencia digital</h3><p>La <strong>Licencia Nacional de Conducir digital</strong> tiene la misma validez que la física y podés circular con ella por las rutas y calles del país.</p><ul><li>Presentás la APP con tu licencia digital.</li><li>La autoridad fiscaliza tu licencia digital y el código.</li><li>Al escanear el QR de tu licencia verifica su estado.</li></ul>',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('carnet_configuracion');
    }
};
