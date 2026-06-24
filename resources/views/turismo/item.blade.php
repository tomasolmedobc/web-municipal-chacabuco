@extends('layouts.app')

@section('title', $item->titulo . ' — Turismo')

@section('content')
@php
    $imagenHeader = $item->galeria->firstWhere('es_header', true);
    $heroUrl = $imagenHeader
        ? $imagenHeader->imagen_url
        : ($item->imagen ? $item->imagen_url : $localidad->imagen_portada_url);
@endphp

<section class="turismo-show-hero"
    style="background-image: linear-gradient(135deg, rgba(15,23,42,.55), rgba(15,23,42,.35)), url('{{ $heroUrl }}');">
    <div>
        <span class="section-badge">
            <i class="fa-solid {{ $config['icono'] }}"></i>
            {{ $config['titulo'] }}
        </span>
        <h1>{{ $item->titulo }}</h1>
        @if($item->categoria)
            <p>{{ $item->categoria }}</p>
        @endif
    </div>
</section>

<div style="display:flex; gap:10px; margin-bottom: 20px; flex-wrap:wrap;">
    <a href="{{ route('turismo.show', ['localidad' => $localidad->slug, 'tipo' => $item->tipo]) }}"
       class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a {{ $localidad->nombre }}
    </a>
</div>

@php
    $tieneAside = ($item->tipo === \App\Models\TurismoItem::TIPO_EVENTO && $item->fecha_inicio)
        || $item->direccion
        || $item->telefono
        || $item->link_externo;
@endphp

<div class="turismo-item-detalle {{ $tieneAside ? '' : 'turismo-item-detalle--full' }}">

    {{-- Panel lateral con metadatos --}}
    @if($tieneAside)
    <aside class="turismo-item-detalle__aside">
        @if($item->tipo === \App\Models\TurismoItem::TIPO_EVENTO && $item->fecha_inicio)
            <div class="turismo-item-detalle__meta-bloque">
                <span class="turismo-item-detalle__meta-label">
                    <i class="fa-solid fa-calendar-days"></i> Fecha
                </span>
                <span class="turismo-item-detalle__meta-valor">
                    {{ $item->fecha_inicio->format('d/m/Y') }}
                    @if($item->hora_inicio) · {{ substr($item->hora_inicio, 0, 5) }} hs @endif
                    @if($item->fecha_fin && $item->fecha_fin != $item->fecha_inicio)
                        <br>al {{ $item->fecha_fin->format('d/m/Y') }}
                    @endif
                </span>
            </div>
        @endif

        @if($item->direccion)
            <div class="turismo-item-detalle__meta-bloque">
                <span class="turismo-item-detalle__meta-label">
                    <i class="fa-solid fa-location-dot"></i> Dirección
                </span>
                <span class="turismo-item-detalle__meta-valor">{{ $item->direccion }}</span>
            </div>
        @endif

        @if($item->telefono)
            <div class="turismo-item-detalle__meta-bloque">
                <span class="turismo-item-detalle__meta-label">
                    <i class="fa-solid fa-phone"></i> Teléfono
                </span>
                <a href="tel:{{ $item->telefono }}" class="turismo-item-detalle__meta-valor">
                    {{ $item->telefono }}
                </a>
            </div>
        @endif

        @if($item->link_externo)
            <a href="{{ $item->link_externo }}" target="_blank" rel="noopener" class="btn btn-primary" style="width:100%;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Más información
            </a>
        @endif
    </aside>
    @endif

    {{-- Contenido principal --}}
    <div class="turismo-item-detalle__main">

        @if($item->descripcion)
            <div class="turismo-historia">
                <div class="contenido">{!! $item->descripcion !!}</div>
            </div>
        @endif

        {{-- Galería --}}
        @php
            $todasLasImagenes = collect();
            if ($item->imagen) {
                $todasLasImagenes->push(['url' => $item->imagen_url, 'titulo' => $item->titulo]);
            }
            foreach ($item->galeria as $img) {
                $todasLasImagenes->push(['url' => $img->imagen_url, 'titulo' => $img->titulo ?: $item->titulo]);
            }
        @endphp

        @if($todasLasImagenes->count())
            <div class="turismo-galeria">
                @if($todasLasImagenes->count() > 1)
                    <h3 class="turismo-galeria__titulo">
                        <i class="fa-solid fa-images"></i>
                        Galería de imágenes
                    </h3>
                @endif

                <div class="turismo-galeria__grid">
                    @foreach($todasLasImagenes as $i => $img)
                        <button
                            type="button"
                            class="turismo-galeria__thumb js-galeria-btn"
                            data-src="{{ $img['url'] }}"
                            data-titulo="{{ $img['titulo'] }}"
                            data-index="{{ $i }}"
                            aria-label="Ver imagen: {{ $img['titulo'] }}">
                            <img src="{{ $img['url'] }}" alt="{{ $img['titulo'] }}" loading="lazy">
                            <span class="turismo-galeria__overlay">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Archivos adjuntos --}}
        @if($item->archivos->count())
            <div class="turismo-adjuntos">
                <h3 class="turismo-galeria__titulo">
                    <i class="fa-solid fa-paperclip"></i>
                    Archivos adjuntos
                </h3>

                <div class="archivos-grid">
                    @foreach($item->archivos as $archivo)
                        @php
                            $ext = strtolower($archivo->extension);
                            $icono = match($ext) {
                                'pdf'        => 'fa-file-pdf',
                                'doc','docx' => 'fa-file-word',
                                'xls','xlsx' => 'fa-file-excel',
                                'zip'        => 'fa-file-zipper',
                                default      => 'fa-paperclip',
                            };
                        @endphp
                        <div class="archivo-card">
                            <div class="archivo-card__main">
                                <i class="fa-solid {{ $icono }} archivo-icono"></i>
                                <div class="archivo-info">
                                    <span class="archivo-nombre">{{ $archivo->nombre_original }}</span>
                                    <span class="archivo-meta">
                                        {{ strtoupper($ext) }}
                                        @if($archivo->tamano_legible) · {{ $archivo->tamano_legible }} @endif
                                    </span>
                                </div>
                            </div>
                            <div class="archivo-card__actions">
                                @if($ext === 'pdf')
                                    <button type="button"
                                            class="btn btn-secondary btn-preview-pdf"
                                            data-pdf="{{ $archivo->ruta }}"
                                            data-title="{{ $archivo->nombre_original }}">
                                        Ver PDF
                                    </button>
                                @endif
                                <a href="{{ $archivo->ruta }}" download class="btn btn-primary">
                                    <i class="fa-solid fa-download"></i>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Lightbox modal --}}
<div class="turismo-lightbox" id="turismo-lightbox" hidden>
    <div class="turismo-lightbox__overlay" id="turismo-lightbox-overlay"></div>

    <div class="turismo-lightbox__dialog">
        <div class="turismo-lightbox__header">
            <span class="turismo-lightbox__titulo" id="turismo-lightbox-titulo"></span>
            <div class="turismo-lightbox__controles">
                <button type="button" id="lb-prev" aria-label="Anterior">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" id="lb-zoom-out" aria-label="Alejar">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <button type="button" id="lb-zoom-reset" aria-label="Tamaño original">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
                <button type="button" id="lb-zoom-in" aria-label="Acercar">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button type="button" id="lb-next" aria-label="Siguiente">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button type="button" id="lb-cerrar" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="turismo-lightbox__body" id="turismo-lightbox-body">
            <img src="" alt="" id="turismo-lightbox-img">
        </div>

        <div class="turismo-lightbox__counter" id="turismo-lightbox-counter"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const modal   = document.getElementById('turismo-lightbox');
    const overlay = document.getElementById('turismo-lightbox-overlay');
    const img     = document.getElementById('turismo-lightbox-img');
    const titulo  = document.getElementById('turismo-lightbox-titulo');
    const counter = document.getElementById('turismo-lightbox-counter');
    const btns    = Array.from(document.querySelectorAll('.js-galeria-btn'));

    if (!modal || !btns.length) return;

    let zoom = 1;
    let current = 0;

    const imagenes = btns.map(b => ({ src: b.dataset.src, titulo: b.dataset.titulo }));

    function applyZoom() {
        img.style.transform = `scale(${zoom})`;
        img.style.transformOrigin = 'center center';
    }

    function abrir(index) {
        current = index;
        const item = imagenes[index];
        img.src = item.src;
        img.alt = item.titulo;
        titulo.textContent = item.titulo;
        counter.textContent = imagenes.length > 1 ? `${index + 1} / ${imagenes.length}` : '';
        zoom = 1;
        applyZoom();
        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        document.getElementById('lb-prev').style.display = imagenes.length > 1 ? '' : 'none';
        document.getElementById('lb-next').style.display = imagenes.length > 1 ? '' : 'none';
    }

    function cerrar() {
        modal.hidden = true;
        img.src = '';
        document.body.style.overflow = '';
        zoom = 1;
        applyZoom();
    }

    function navegar(dir) {
        current = (current + dir + imagenes.length) % imagenes.length;
        abrir(current);
    }

    btns.forEach((btn, i) => btn.addEventListener('click', () => abrir(i)));

    overlay.addEventListener('click', cerrar);
    document.getElementById('lb-cerrar').addEventListener('click', cerrar);

    document.getElementById('lb-prev').addEventListener('click', () => navegar(-1));
    document.getElementById('lb-next').addEventListener('click', () => navegar(1));

    document.getElementById('lb-zoom-in').addEventListener('click',    () => { zoom = Math.min(zoom + 0.25, 4); applyZoom(); });
    document.getElementById('lb-zoom-out').addEventListener('click',   () => { zoom = Math.max(zoom - 0.25, 0.5); applyZoom(); });
    document.getElementById('lb-zoom-reset').addEventListener('click', () => { zoom = 1; applyZoom(); });

    document.addEventListener('keydown', e => {
        if (modal.hidden) return;
        if (e.key === 'Escape')      cerrar();
        if (e.key === 'ArrowLeft')   navegar(-1);
        if (e.key === 'ArrowRight')  navegar(1);
        if (e.key === '+')           { zoom = Math.min(zoom + 0.25, 4); applyZoom(); }
        if (e.key === '-')           { zoom = Math.max(zoom - 0.25, 0.5); applyZoom(); }
    });
})();
</script>
@endpush
