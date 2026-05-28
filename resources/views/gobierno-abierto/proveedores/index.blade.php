@extends('layouts.app')

@section('title', 'Compras y proveedores')

@section('content')

<section class="noticias-hero">

    <div>

        <span class="section-badge">
            Portal proveedores
        </span>

        <h1>
            Compras y Proveedores
        </h1>

        <p>
            Accedé a licitaciones, registro de proveedores,
            documentación municipal y herramientas de consulta.
        </p>

    </div>

</section>


<section class="quick-access">

    <div class="section-heading">
        <h2>Accesos rápidos</h2>

        <p>
            Herramientas y documentación importante para proveedores.
        </p>
    </div>

    <div class="quick-access__grid">

        <a href="#"
           class="quick-card">

            <i class="fa-solid fa-building-columns"></i>

            <h3>Intranet proveedores</h3>

            <p>
                Acceso al sistema interno de proveedores municipales.
            </p>

        </a>

        <a href="#"
           class="quick-card">

            <i class="fa-solid fa-file-pdf"></i>

            <h3>Formulario inscripción</h3>

            <p>
                Descargá el formulario oficial de inscripción.
            </p>

        </a>

        <a href="#"
           class="quick-card">

            <i class="fa-solid fa-scale-balanced"></i>

            <h3>Licitaciones</h3>

            <p>
                Consultá licitaciones públicas y privadas vigentes.
            </p>

        </a>

    </div>

</section>


<section class="proveedores-info-card">

    <div class="section-heading">
        <h2>Información importante</h2>

        <p>
            Requisitos y documentación necesaria para proveedores.
        </p>
    </div>

    <div class="accordion-list">

        <details class="accordion-item" open>

            <summary>
                Requisitos de inscripción
            </summary>

            <div class="accordion-content">

                <ul>
                    <li>Solicitud (Formulario adjunto)</li>
                    <li>Constancia de inscripción ante AFIP (CUIT)</li>
                    <li>Certificado de Inscripción en Ingresos Brutos</li>
                    <li>Copia del contrato social (de corresponder)</li>
                    <li>Copia del Certificado de Habilitación Municipal</li>
                    <li>Sellado de inscripción ($46.400,00)</li>
                    <li>Certificado de exención (en caso de estar exento en algún impuesto)</li>
                    <li>Presentar constancia Libre Deuda Alimentario, el cual podrá obtener desde el "Registro de deudores morosos alimentarios”, del Ministerio de Justicia de la provincia de Buenos Aires.</li>
                </ul>

            </div>

        </details>

        <details class="accordion-item">

            <summary>
                Información bancaria
            </summary>

            <div class="accordion-content">

                <p>
                    Cuenta bancaria para el depósito del arancel:

                </p>

                <strong><p>
                    MUNICIPALIDAD DE CHACABUCO

                        CUIT: 30-99908326-5

                        BANCO DE LA PROVINCIA DE BS. AS. 

                        Nº DE CUENTA: 6441-50733/4

                        CBU Nº: 01403303 01644105073340
                </p></strong>

                <p>
                    Una vez efectuado, enviar la boleta de depósito escaneada, para poder verificar el ingreso y emitir el recibo correspondiente.
                </p>

            </div>

        </details>

        <details class="accordion-item">

            <summary>
                Email de contacto y envío de documentación
            </summary>

            <div class="accordion-content">

                <p>
                    La documentación debe enviarse al mail: compras@chacabuco.gob.ar

                    FORMULARIO DE REGISTRO PARA PROVEEDORES
                </p>

            </div>

        </details>

    </div>

</section>


<section class="news-home">

    <div class="section-heading">
        <h2>Guía visual</h2>

        <p>
            Pasos rápidos para acceder a facturas y retenciones.
        </p>
    </div>

    <div class="news-home__grid">

        <article class="news-card">

            <img src="{{ asset('images/guias/proveedores-1.webp') }}"
                alt="Paso 1">

            <div class="news-card__body">

                <h3>Paso 1</h3>

                <p>
                    Seleccioná la factura dentro del sistema.
                </p>

            </div>

        </article>

        <article class="news-card">

            <img src="{{ asset('images/guias/proveedores-2.webp') }}"
                    alt="Paso 2">

            <div class="news-card__body">

                <h3>Paso 2</h3>

                <p>
                    Accedé a la sección de retenciones.
                </p>

            </div>

        </article>

    </div>

</section>

@endsection