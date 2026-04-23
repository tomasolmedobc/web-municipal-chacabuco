@extends('layouts.app')

@section('title', $noticia->titulo)

@section('content')
    <div class="detalle">
        <a href="{{ url('/noticias') }}" class="volver">← Volver al listado</a>

        <h1>{{ $noticia->titulo }}</h1>

        <div class="fecha">
            <strong>Fecha:</strong> {{ $noticia->fecha->format('d/m/Y - H:i') }} hs
        </div>

        @auth
            @if(in_array(auth()->user()->rol, ['admin', 'editor']) && $noticia->estado === 'oculto')
                <div class="admin-alert" style="background:#fff7ed; color:#9a3412; margin:20px 0;">
                    ⚠️ Esta noticia está en modo <strong>oculto</strong>. Solo es visible para administradores y editores.
                </div>
            @endif
        @endauth

        @if($noticia->imagen_destacada)
            <div class="imagen">
                <img src="{{ $noticia->imagen_destacada }}" alt="{{ $noticia->titulo }}">
            </div>
        @endif

        @php
            $contenido = preg_replace('/class="[^"]*"/', '', $noticia->contenido);
        @endphp

        <div class="contenido">
            {!! $contenido !!}
        </div>

        @if($noticia->archivos->count())
    <div style="margin-top: 30px;">
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

@push('scripts')
    <script src="{{ asset('js/noticia-show.js') }}"></script>
@endpush