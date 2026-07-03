<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $procedimientos = [
            /* ── OBRAS ──────────────────────────────────────── */
            [
                'seccion' => 'obras',
                'codigo'  => 'A',
                'titulo'  => 'Obras nuevas',
                'orden'   => 1,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Planos municipales visados por colegio con código QR impreso en plano. <strong>(Anexo 2)</strong></li>
<li>Desarrollo del visado y QR del Colegio, con sello y firma del visador del colegio correspondiente.</li>
<li>Título de propiedad, certificado de título en trámite o boleto certificado.</li>
<li>Plancheta de parcela o plano de mensura aprobado.</li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>Nota de compromiso con firma del propietario y profesional. <strong>(Anexo 4)</strong></li>
<li>Documentación complementaria en caso que se requiera.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'B',
                'titulo'  => 'A declarar / incorporar',
                'orden'   => 2,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Planos municipales e informe técnico visados por colegio. <strong>(Anexo 2)</strong></li>
<li>Informe técnico visado por colegio.</li>
<li>Desarrollo del visado y QR del Colegio con sello y firma del visador.</li>
<li>Título de propiedad, certificado de título en trámite o boleto certificado.</li>
<li>Copia de planos Antecedente.</li>
<li>Plancheta de parcela o plano de mensura aprobado.</li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>Nota de compromiso con firma del propietario y profesional. <strong>(Anexo 4)</strong></li>
<li>Documentación complementaria en caso que se requiera.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'C',
                'titulo'  => 'Ampliación / modificación de obra',
                'orden'   => 3,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Planos municipales e informe técnico visados por colegio.</li>
<li>Desarrollo del visado y QR del Colegio con sello y firma del visador.</li>
<li>Título de propiedad, certificado de título en trámite o boleto certificado.</li>
<li>Copia de planos Antecedente.</li>
<li>Plancheta de parcela o plano de mensura aprobado.</li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>Nota de compromiso con firma del propietario y profesional. <strong>(Anexo 4)</strong></li>
<li>Documentación complementaria en caso que se requiera.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'D',
                'titulo'  => 'Obras existentes sin terminar',
                'orden'   => 4,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Planos municipales e informe técnico visados por colegio.</li>
<li>Desarrollo del visado y QR del Colegio con sello y firma del visador.</li>
<li>Título de propiedad, certificado de título en trámite o boleto certificado.</li>
<li>Copia de planos Antecedente.</li>
<li>Plancheta de parcela o plano de mensura aprobado.</li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>Nota de compromiso con firma del propietario y profesional. <strong>(Anexo 4)</strong></li>
<li>Documentación complementaria en caso que se requiera.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'E',
                'titulo'  => 'Certificado de final de obra',
                'orden'   => 5,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Plano general y plano de instalación sanitaria conforme a obra.</li>
<li>Desarrollo del visado y QR del Colegio con sello y firma del visador.</li>
<li>Título de propiedad. <em>(SI CAMBIA TITULAR)</em></li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>En construcciones de PB y 3 pisos o más: informe antisiniestral.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'F',
                'titulo'  => 'Demolición',
                'orden'   => 6,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente. <strong>(Anexo 1)</strong></li>
<li>Planos municipales visados por colegio.</li>
<li>Desarrollo del visado y QR del Colegio con sello y firma del visador.</li>
<li>Título de propiedad, certificado de título en trámite o boleto certificado.</li>
<li>Plancheta de parcela o plano de mensura aprobado.</li>
<li>Nota de compromiso con firma del propietario y profesional. <strong>(Anexo 4)</strong></li>
<li>Planilla de estadística de Arba por triplicado. <strong>(Anexo 3)</strong></li>
<li>Documentación complementaria en caso que se requiera.</li>
</ul>',
            ],
            [
                'seccion' => 'obras',
                'codigo'  => 'notas',
                'titulo'  => 'Aclaraciones generales',
                'orden'   => 99,
                'contenido' => '<p><strong>Viviendas multifamiliares:</strong> Para obras de PB y PA o más niveles, y casas de más de un baño, se debe adjuntar plano de agua con detalle de tanque cisterna.</p>
<p>Cuando la obra sea de PB y más de 1 piso, también debe presentarse:</p>
<ul>
<li>Estudio de suelos.</li>
<li>Cálculo de estructura.</li>
<li>Planos de instalaciones de desagüies.</li>
<li>Planos de instalaciones sanitarias y detalle de cisterna.</li>
</ul>
<p>El municipio puede solicitar esto también cuando lo considere pertinente aunque la obra sea de menor envergadura.</p>
<p><strong>Obras anteriores a 1955:</strong> Lo declarado anterior al año 55 (Ordenanza 579/1955) no paga derecho. Se debe incorporar copia de la Plancheta al expediente. Especificar en plano y en superficies los m² que son anteriores al 55.</p>
<p><strong>Régimen de PH:</strong> Se deberá presentar el reglamento de copropiedad donde se especifica los m² que puede construir cada copropietario, o nota de asamblea donde se autorice la construcción. En cualquier caso deberá especificarse los metros cuadrados permitidos para la obra. Todo deberá llevar las firmas certificadas.</p>',
            ],

            /* ── BALCONES GASTRONÓMICOS ───────────────────── */
            [
                'seccion' => 'balcones',
                'codigo'  => '1',
                'titulo'  => 'Documentación requerida',
                'orden'   => 1,
                'contenido' => '<ul>
<li>Solicitud de obra / nota al intendente, indicando número de habilitación. <strong>(Anexo 1)</strong></li>
<li>Constancia de habilitación o prefactibilidad aprobada.</li>
<li>Croquis del balcón firmado por profesional matriculado, conteniendo planta, corte y vista con medidas, niveles y materiales. <strong>(Anexo 2)</strong></li>
<li>Documentación complementaria en caso que se requiera. Para los casos en que el balcón se extienda en propiedades vecinas, deberá contar con un permiso firmado por el propietario y con firma certificada.</li>
<li>Póliza de seguro de responsabilidad civil anual o ampliación de la presentada para el local.</li>
<li>Pago del derecho de uso del espacio público — Recaudación (se entrega luego que se inicie el trámite).</li>
<li>Señalización: cartelería correspondiente y demarcación horizontal. <strong>(Anexo 4)</strong></li>
</ul>
<p><strong>Nota:</strong> Cuando la habilitación sea en un PB, también debe presentar consentimiento de los condóminos.</p>',
            ],
            [
                'seccion' => 'balcones',
                'codigo'  => '2',
                'titulo'  => 'Recorrido del expediente',
                'orden'   => 2,
                'contenido' => '<ol>
<li><strong>Mesa de entrada:</strong> el solicitante deberá presentar la documentación en una carpeta y dejar un contacto.</li>
<li><strong>Obras Particulares:</strong> se controla la documentación y se emite una habilitación provisoria para comenzar tareas en la vía pública y colocación de señalética. Se entrega el pago del derecho. Recaudación emite pago de la liquidación y registra al usuario.</li>
<li><strong>Finalización:</strong> con toda la documentación y la obra ejecutada, el solicitante da aviso, se constata la misma y se emite el permiso de uso del espacio público.</li>
</ol>
<p>Si no cumple con la documentación o la ejecución de la obra y su correspondiente señalización, no se emitirá el permiso.</p>
<p><strong>Tiempo mínimo del trámite:</strong> 1 mes.</p>',
            ],

            /* ── MENSURA Y SUBDIVISIÓN ─────────────────────── */
            [
                'seccion' => 'mensura',
                'codigo'  => 'A',
                'titulo'  => 'Prefactibilidad / consulta previa',
                'orden'   => 1,
                'contenido' => '<ul>
<li>Nota dirigida al intendente y Obras Particulares.</li>
<li>Anteproyecto de subdivisión con ubicación del espacio verde libre y equipamiento comunitario en caso que corresponda.</li>
<li>Se solicita la factibilidad de los servicios de infraestructura — si cumple o no con indicadores y la ubicación del espacio verde y libre.</li>
<li>Se debe presentar por mesa de entrada, en donde se le genera un número de expediente para su seguimiento.</li>
</ul>',
            ],
            [
                'seccion' => 'mensura',
                'codigo'  => 'B',
                'titulo'  => 'Factibilidad',
                'orden'   => 2,
                'contenido' => '<ul>
<li>Nota al intendente.</li>
<li>Adjuntar nota de prefactibilidad emitida por esta dirección.</li>
<li>Factibilidad de COOP. Eléctrica.</li>
<li>Presentar proyecto de agua, cloaca, escurrimiento de aguas, niveles del terreno y mejorado de calles.</li>
<li>Copia de título de propiedad.</li>
</ul>',
            ],
            [
                'seccion' => 'mensura',
                'codigo'  => 'C',
                'titulo'  => 'Visado municipal',
                'orden'   => 3,
                'contenido' => '<p>Se podrá presentar el trámite para el visado una vez realizadas las obras de infraestructura (se adjuntará con el mismo número de expediente de las consultas anteriores).</p>
<ul>
<li>Nota dirigida al intendente.</li>
<li>Documentación de factibilidad aprobada.</li>
<li>Contrato con Coop. Eléctrica.</li>
<li>Planos de mensura y subdivisión (mínimo 3 copias; 2 quedan en nuestro registro).</li>
</ul>
<p><strong>Nota:</strong> Se deben realizar las obras de infraestructura en su totalidad. El municipio constatará las mismas, y en caso que no estén terminadas, el visado se expedirá con restricciones, que luego de realizadas las obras podrá ser levantado.</p>
<p><strong>Tiempo mínimo del trámite:</strong> 3 meses.</p>',
            ],

            /* ── LIBRE DEUDA ─────────────────────────────── */
            [
                'seccion' => 'libre_deuda',
                'codigo'  => 'info',
                'titulo'  => 'Libre Deuda',
                'orden'   => 1,
                'contenido' => '<p>Es un certificado que lo solicita un agente de retención del estado, para realizar trámites en relación al dominio. En nuestra área se valida la superficie declarada con la construida. También se detalla si tiene o no conexión de agua y cloaca, realizando los pases internos por las áreas de obras y luego el pase a la cooperativa eléctrica.</p>
<p><strong>Tiempo mínimo del trámite:</strong> 1 mes (desde que llega a la oficina).</p>',
            ],
        ];

        foreach ($procedimientos as $p) {
            DB::table('obras_procedimientos')->insert([
                'seccion'    => $p['seccion'],
                'codigo'     => $p['codigo'],
                'titulo'     => $p['titulo'],
                'contenido'  => $p['contenido'],
                'orden'      => $p['orden'],
                'visible'    => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('obras_procedimientos')->truncate();
    }
};
