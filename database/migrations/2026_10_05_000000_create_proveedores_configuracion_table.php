<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('sellado_monto', 100)->default('$49.600,00');
            $table->longText('texto_requisitos')->nullable();
            $table->longText('texto_info_bancaria')->nullable();
            $table->longText('texto_email_contacto')->nullable();
            $table->longText('texto_creacion_usuario')->nullable();
            $table->timestamps();
        });

        DB::table('proveedores_configuracion')->insert([
            'sellado_monto'          => '$49.600,00',
            'texto_requisitos'       => '<ul><li>Solicitud de inscripción mediante formulario.</li><li>Constancia de inscripción ante AFIP (CUIT).</li><li>Certificado de inscripción en Ingresos Brutos.</li><li>Copia del contrato social, si corresponde.</li><li>Copia del certificado de habilitación municipal.</li><li>Sellado de inscripción: $49.600,00.</li><li>Certificado de exención, si corresponde.</li><li>Constancia de Libre Deuda Alimentario emitida por el Registro de Deudores Morosos Alimentarios.</li></ul>',
            'texto_info_bancaria'    => '<p>Cuenta bancaria para el depósito del arancel:</p><p><strong>Municipalidad de Chacabuco</strong><br>CUIT: 30-99908326-5<br>Banco de la Provincia de Buenos Aires<br>Cuenta: 6441-50733/4<br>CBU: 01403303 01644105073340</p><p>Una vez efectuado el depósito, enviá la boleta escaneada para verificar el ingreso y emitir el recibo correspondiente.</p>',
            'texto_email_contacto'   => '<p>La documentación debe enviarse a <a href="mailto:compras@chacabuco.gob.ar">compras@chacabuco.gob.ar</a>.</p>',
            'texto_creacion_usuario' => '<p>Para solicitar el alta de usuario en el sistema, enviá los siguientes datos:</p><p><strong>CUIT:</strong><br><strong>RAZÓN SOCIAL:</strong><br><strong>MAIL:</strong><br><strong>TELÉFONO:</strong></p><p>Enviá la información a cualquiera de estas direcciones:<br><a href="mailto:desistemas_chacabuco@gmail.com">desistemas_chacabuco@gmail.com</a><br><a href="mailto:sistemas@chacabuco.gob.ar">sistemas@chacabuco.gob.ar</a></p>',
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores_configuracion');
    }
};
