@extends('layouts.app')
@section('title', $noticia->titulo)
@section('meta_description', \Illuminate\Support\Str::of($noticia->contenido)->stripTags()->squish()->limit(160))
@section('og_image', $noticia->imagen_destacada_url)
@section('content')
    <div class="detalle">
        <x-breadcrumb :items="[
            ['label' => 'Inicio',  'url' => route('home')],
            ['label' => 'Noticias','url' => route('noticias.index')],
            ['label' => $noticia->titulo],
        ]" />

        <h1>{{ $noticia->titulo }}</h1>

        @if($noticia->categorias->count())
            <div class="categorias-list">
                @foreach($noticia->categorias as $categoria)
                    <span class="categoria-noticia">
                        {{ $categoria->nombre }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="fecha">
            <strong>Fecha:</strong> {{ $noticia->fecha->format('d/m/Y - H:i') }} hs
            <span class="noticia-vistas" title="Cantidad de visitas">
                <i class="fa-regular fa-eye"></i> {{ number_format($noticia->vistas) }}
            </span>
        </div>

        @auth
            @if(in_array(auth()->user()->rol, ['admin', 'editor']) && $noticia->estado === 'oculto')
                <div class="admin-alert admin-alert--warning">
                    ⚠️ Esta noticia está en modo <strong>oculto</strong>. Solo es visible para administradores y editores.
                </div>
            @endif
        @endauth

        <div class="imagen">
            <img src="{{ $noticia->imagen_destacada_url }}" alt="{{ $noticia->titulo }}">
        </div>

        @php
            $contenido = preg_replace('/class="[^"]*"/', '', $noticia->contenido);
        @endphp

        <div class="contenido">
            {!! $contenido !!}
        </div>

        @if($noticia->video_url && video_embed_url($noticia->video_url))
            <div class="video-embed">
                <h3 class="video-embed__titulo">
                    <i class="fa-brands fa-youtube"></i>
                    Video
                </h3>
                <div class="video-embed__wrap">
                    <iframe src="{{ video_embed_url($noticia->video_url) }}"
                            title="Video de la noticia"
                            allowfullscreen
                            loading="lazy"></iframe>
                </div>
            </div>
        @endif

        @if($noticia->archivos->count())
            <div class="mt-30">
                <h3>Archivos adjuntos</h3>

                <div class="archivos-grid">
                    @foreach($noticia->archivos as $archivo)
                        @php
                            $extension = strtolower($archivo->extension);
                            $icono = match($extension) {
                                'pdf' => 'fa-file-pdf',
                                'doc', 'docx' => 'fa-file-word',
                                'xls', 'xlsx' => 'fa-file-excel',
                                default => 'fa-paperclip',
                            };
                        @endphp

                        <div class="archivo-card">
                            <div class="archivo-card__main">
                                <i class="fa-solid {{ $icono }} archivo-icono"></i>

                                <div class="archivo-info">
                                    <span class="archivo-nombre">{{ $archivo->nombre_original }}</span>
                                    <span class="archivo-meta">
                                        {{ strtoupper($extension) }} · {{ $archivo->tamano_legible }}
                                    </span>
                                </div>
                            </div>

                            <div class="archivo-card__actions">
                                @if($extension === 'pdf')
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-preview-pdf"
                                        data-pdf="{{ $archivo->ruta }}"
                                        data-title="{{ $archivo->nombre_original }}"
                                    >
                                        Ver PDF
                                    </button>
                                @endif

                                <a href="{{ $archivo->ruta }}" target="_blank" class="btn btn-secondary" download>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div id="pdfModal" class="pdf-modal" hidden>
            <div class="pdf-modal__overlay" id="pdfModalOverlay"></div>

            <div class="pdf-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="pdfModalTitle">
                <div class="pdf-modal__header">
                    <h3 id="pdfModalTitle">Vista previa PDF</h3>
                    <button type="button" class="pdf-modal__close" id="pdfModalClose">✕</button>
                </div>

                <div class="pdf-modal__body">
                    <iframe id="pdfViewer" src="" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_head')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": {{ Js::from($noticia->titulo) }},
    "description": {{ Js::from(\Illuminate\Support\Str::of($noticia->contenido)->stripTags()->squish()->limit(160)->toString()) }},
    "datePublished": "{{ $noticia->fecha?->toIso8601String() }}",
    "dateModified": "{{ $noticia->updated_at?->toIso8601String() }}",
    "image": {{ Js::from($noticia->imagen_destacada_url) }},
    "author": {
        "@type": "Organization",
        "name": "Municipalidad de Chacabuco"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Municipalidad de Chacabuco",
        "url": "{{ url('/') }}"
    },
    "url": "{{ route('noticias.show', $noticia->slug) }}"
}
</script>
@endpush

@push('scripts')
    <script src="{{ asset('js/noticia-show.js') }}"></script>
@endpush
