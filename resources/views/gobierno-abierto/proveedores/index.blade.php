@extends('layouts.app')

@section('title', 'Compras y Proveedores')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',          'url' => route('home')],
    ['label' => 'Gobierno Abierto','url' => route('gobierno-abierto.index')],
    ['label' => 'Compras y Proveedores'],
]" />

<section class="noticias-hero proveedores-hero">
    <div>
        <span class="section-badge">
            Portal proveedores
        </span>

        <h1>
            Compras y Proveedores
        </h1>

        <p>
            Accedé a licitaciones, registro de proveedores, documentación municipal
            y herramientas de consulta para operar con el municipio.
        </p>
    </div>
</section>

<section class="quick-access">
    <div class="section-heading section-heading--between">
        <div>
            <span class="section-badge">Accesos</span>
            <h2>Herramientas para proveedores</h2>
            <p>Consultas frecuentes y documentación importante en un solo lugar.</p>
        </div>
    </div>

    <div class="quick-access__grid">
        <a href="http://138.122.158.232:8084/login"
           class="quick-card proveedores-quick-card">
            <span class="quick-card__icon proveedores-quick-card__icon">
                <i class="fa-solid fa-building-columns"></i>
            </span>

            <h3>Intranet proveedores</h3>

            <p>
                Acceso al sistema interno de proveedores municipales.
            </p>
        </a>

        <a href="{{ asset('images/proveedorespdf/formulario_inscripcion_proveedores.pdf') }}"
           target="_blank"
           class="quick-card proveedores-quick-card">
            <span class="quick-card__icon proveedores-quick-card__icon">
                <i class="fa-solid fa-file-pdf"></i>
            </span>

            <h3>Formulario inscripción</h3>

            <p>
                Descargá el formulario oficial para iniciar el registro.
            </p>
        </a>

        <a href="{{ route('licitaciones.index') }}"
           class="quick-card proveedores-quick-card">
            <span class="quick-card__icon proveedores-quick-card__icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </span>

            <h3>Licitaciones</h3>

            <p>
                Consultá licitaciones públicas y privadas vigentes.
            </p>
        </a>
    </div>
</section>

<section class="proveedores-info-card">
    <div class="section-heading">
        <span class="section-badge">Registro</span>
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

            <div class="accordion-content contenido">
                {!! $config->texto_requisitos !!}
            </div>
        </details>

        <details class="accordion-item">
            <summary>
                Información bancaria
            </summary>

            <div class="accordion-content contenido">
                {!! $config->texto_info_bancaria !!}
            </div>
        </details>

        <details class="accordion-item">
            <summary>
                Email de contacto y envío de documentación
            </summary>

            <div class="accordion-content contenido">
                {!! $config->texto_email_contacto !!}
            </div>
        </details>

        <details class="accordion-item">
            <summary>
                Creación de usuario para el sistema de proveedores
            </summary>

            <div class="accordion-content contenido">
                {!! $config->texto_creacion_usuario !!}
            </div>
        </details>
    </div>
</section>

<section class="proveedores-steps">
    <div class="section-heading">
        <span class="section-badge">Cómo avanzar</span>
        <h2>Pasos rápidos</h2>

        <p>
            Una guía simple para preparar la documentación y completar la inscripción.
        </p>
    </div>

    <div class="proveedores-steps__grid">
        <article class="proveedores-step">
            <span>1</span>
            <i class="fa-solid fa-list-check"></i>
            <h3>Reuní la documentación</h3>
            <p>Prepará constancias, certificados y documentación respaldatoria.</p>
        </article>

        <article class="proveedores-step">
            <span>2</span>
            <i class="fa-solid fa-money-check-dollar"></i>
            <h3>Realizá el sellado</h3>
            <p>Aboná el arancel correspondiente y conservá el comprobante.</p>
        </article>

        <article class="proveedores-step">
            <span>3</span>
            <i class="fa-solid fa-envelope-open-text"></i>
            <h3>Enviá el trámite</h3>
            <p>Remití la documentación por email para su revisión administrativa.</p>
        </article>
    </div>
</section>

<section class="proveedores-gallery">
    <div class="section-heading">
        <span class="section-badge">Guía visual</span>
        <h2>Facturas y retenciones</h2>

        <p>
            Capturas de referencia para ubicar las opciones dentro del sistema.
        </p>
    </div>

    @if($config->guia_video_url && video_embed_url($config->guia_video_url))
        <div class="video-embed" style="margin-bottom: 2rem">
            <div class="video-embed__wrap">
                <iframe src="{{ video_embed_url($config->guia_video_url) }}"
                        title="Guía visual de proveedores"
                        allowfullscreen
                        loading="lazy"></iframe>
            </div>
        </div>
    @endif

    <div class="proveedores-gallery__grid">
        <article class="proveedores-photo-card">
            <button
                type="button"
                class="proveedores-photo-card__button"
                data-image="{{ $config->guia_imagen_1_url }}"
                data-title="{{ $config->guia_imagen_1_titulo ?? 'Paso 1' }}"
            >
                <img src="{{ $config->guia_imagen_1_url }}" alt="{{ $config->guia_imagen_1_titulo ?? 'Paso 1' }}">
            </button>

            <div class="proveedores-photo-card__body">
                <h3>{{ $config->guia_imagen_1_titulo ?? 'Paso 1' }}</h3>
                @if($config->guia_imagen_1_desc)
                    <p>{{ $config->guia_imagen_1_desc }}</p>
                @endif
            </div>
        </article>

        <article class="proveedores-photo-card">
            <button
                type="button"
                class="proveedores-photo-card__button"
                data-image="{{ $config->guia_imagen_2_url }}"
                data-title="{{ $config->guia_imagen_2_titulo ?? 'Paso 2' }}"
            >
                <img src="{{ $config->guia_imagen_2_url }}" alt="{{ $config->guia_imagen_2_titulo ?? 'Paso 2' }}">
            </button>

            <div class="proveedores-photo-card__body">
                <h3>{{ $config->guia_imagen_2_titulo ?? 'Paso 2' }}</h3>
                @if($config->guia_imagen_2_desc)
                    <p>{{ $config->guia_imagen_2_desc }}</p>
                @endif
            </div>
        </article>
    </div>
</section>

<div class="proveedores-image-modal" id="proveedores-image-modal" hidden>
    <div class="proveedores-image-modal__overlay" data-close-modal></div>

    <div class="proveedores-image-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="proveedores-modal-title">
        <div class="proveedores-image-modal__header">
            <h3 id="proveedores-modal-title">Vista ampliada</h3>

            <div class="proveedores-image-modal__controls">
                <button type="button" data-zoom-out aria-label="Alejar">
                    <i class="fa-solid fa-minus"></i>
                </button>

                <button type="button" data-zoom-reset aria-label="Restaurar zoom">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>

                <button type="button" data-zoom-in aria-label="Acercar">
                    <i class="fa-solid fa-plus"></i>
                </button>

                <button type="button" class="proveedores-image-modal__close" data-close-modal aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="proveedores-image-modal__body">
            <img src="" alt="" id="proveedores-modal-image">
        </div>
    </div>
</div>

<section class="volver-ts">
    <a href="{{ route('gobierno-abierto.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Gobierno Abierto
    </a>
</section>

@endsection

@push('scripts')
<script @nonce>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('proveedores-image-modal');
    const modalImage = document.getElementById('proveedores-modal-image');
    const modalTitle = document.getElementById('proveedores-modal-title');
    const buttons = document.querySelectorAll('.proveedores-photo-card__button');
    const zoomIn = modal?.querySelector('[data-zoom-in]');
    const zoomOut = modal?.querySelector('[data-zoom-out]');
    const zoomReset = modal?.querySelector('[data-zoom-reset]');
    let zoom = 1;

    if (!modal || !modalImage || !modalTitle || !buttons.length) return;

    const applyZoom = () => {
        modalImage.style.width = `${zoom * 100}%`;
        modalImage.style.maxWidth = 'none';
    };

    const closeModal = () => {
        modal.hidden = true;
        modalImage.src = '';
        modalImage.alt = '';
        zoom = 1;
        applyZoom();
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            modalImage.src = button.dataset.image;
            modalImage.alt = button.dataset.title || 'Vista ampliada';
            modalTitle.textContent = button.dataset.title || 'Vista ampliada';
            zoom = 1;
            applyZoom();
            modal.hidden = false;
        });
    });

    modal.querySelectorAll('[data-close-modal]').forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    zoomIn?.addEventListener('click', () => {
        zoom = Math.min(zoom + 0.25, 3);
        applyZoom();
    });

    zoomOut?.addEventListener('click', () => {
        zoom = Math.max(zoom - 0.25, 0.5);
        applyZoom();
    });

    zoomReset?.addEventListener('click', () => {
        zoom = 1;
        applyZoom();
    });
});
</script>
@endpush
