@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <section class="municipal-hero">
        <div class="municipal-hero__content">
            <span class="section-badge">Portal oficial del municipio</span>

            <h2 class="municipal-hero__title">
                Información pública, noticias y servicios para la comunidad
            </h2>

            <p class="municipal-hero__text">
                Accedé a las últimas novedades, trámites, servicios municipales e información útil
                para vecinos y vecinas de Chacabuco.
            </p>

            <div class="municipal-hero__actions">
                <a href="{{ route('noticias.index') }}" class="btn btn-primary">Ver noticias</a>
                <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">Tramites</a>
                <a href="{{ route('gobierno-abierto.index') }}" class="btn btn-secondary">Gobierno Abierto</a>
            </div>
        </div>

        <div class="municipal-hero__media">
            <img
                src="{{ config_sistema('portada', asset('images/importantes/tu-imagen-default.webp')) }}"
                alt="Municipalidad de Chacabuco"
            >
        </div>
    </section>

    <div id="clima-widget" class="clima-widget" hidden aria-label="Temperatura actual en Chacabuco">
        <span class="clima-widget__icon" id="clima-icon">—</span>
        <div class="clima-widget__data">
            <span class="clima-widget__temp" id="clima-temp">--°</span>
            <span class="clima-widget__lugar">Chacabuco, BA</span>
        </div>
        <button type="button" class="clima-widget__cerrar" id="clima-cerrar" aria-label="Cerrar widget de clima">×</button>
    </div>

    <section class="quick-access">
        <div class="section-heading">
            <h2>Accesos rápidos</h2>
            <p>Consultá los principales módulos del portal municipal.</p>
        </div>

        <div class="quick-access__grid">
            <a href="{{ route('noticias.index') }}" class="quick-card">
                <span class="quick-card__icon">📰</span>
                <h3>Noticias</h3>
                <p>Novedades, comunicados y actualidad municipal.</p>
            </a>

            <a href="{{ route('tramites-servicios.index') }}" class="quick-card">
                <span class="quick-card__icon">📄</span>
                <h3>Trámites</h3>
                <p>Accedé a gestiones y trámites municipales.</p>
            </a>

            <a href="{{ route('tramites-servicios.index') }}" class="quick-card">
                <span class="quick-card__icon">🛠️</span>
                <h3>Servicios</h3>
                <p>Información útil para vecinos y vecinas.</p>
            </a>

            <a href="{{ route('gobierno-abierto.index') }}" class="quick-card">
                <span class="quick-card__icon">🏛️</span>
                <h3>Gobierno Abierto</h3>
                <p>Información pública, licitaciones, nóminas, datos institucionales y documentación del Municipio de Chacabuco.</p>
            </a>

            <a href="{{ route('telefonos-utiles.index') }}" class="quick-card">
                <span class="quick-card__icon">📞</span>
                <h3>Teléfonos útiles</h3>
                <p>Contactos y canales de atención municipal.</p>
            </a>

            <a href="{{ route('proveedores.index') }}" class="quick-card">
                <span class="quick-card__icon">🏪</span>
                <h3>Proveedores</h3>
                <p>Información para proveedores del municipio de Chacabuco.</p>
            </a>
        </div>
    </section>

    @if ($noticiaDestacada)
        @php
            $resumenDestacada = \Illuminate\Support\Str::of($noticiaDestacada->contenido)
                ->stripTags()
                ->squish()
                ->limit(180);
        @endphp

        <section class="home-featured">
            <div class="section-heading section-heading--between">
                <div>
                    <span class="section-badge">Noticia destacada</span>
                    <h2>Información destacada</h2>
                    <p>El comunicado principal seleccionado por el municipio.</p>
                </div>
            </div>

            <article class="hero">
                <a href="{{ route('noticias.show', $noticiaDestacada->slug) }}" class="hero-media">
                    <img src="{{ $noticiaDestacada->imagen_destacada_url }}" alt="{{ $noticiaDestacada->titulo }}">
                </a>

                <div class="hero-body">
                    <div class="hero-top">
                        <span class="badge-destacada">DESTACADA</span>
                        <span class="hero-badge">Principal</span>
                    </div>

                    @if ($noticiaDestacada->categorias->count())
                        <div class="categorias-list">
                            @foreach($noticiaDestacada->categorias as $categoria)
                                <a href="{{ route('noticias.index', ['categoria' => $categoria->slug]) }}"
                                   class="categoria-noticia">
                                    {{ $categoria->nombre }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="fecha">
                        {{ $noticiaDestacada->fecha?->format('d/m/Y H:i') }}
                    </div>

                    <h1 class="hero-title">
                        <a href="{{ route('noticias.show', $noticiaDestacada->slug) }}">
                            {{ $noticiaDestacada->titulo }}
                        </a>
                    </h1>

                    <p class="hero-resumen hero-resumen-destacado">
                        {{ strlen(trim($resumenDestacada)) > 20 ? $resumenDestacada : 'Sin descripción disponible.' }}
                    </p>

                    <a href="{{ route('noticias.show', $noticiaDestacada->slug) }}" class="btn btn-primary">
                        Leer noticia
                    </a>
                </div>
            </article>
        </section>
    @endif

    <section class="news-home">
        <div class="section-heading section-heading--between">
            <div>
                <h2>Últimas noticias</h2>
                <p>Las novedades más recientes del municipio.</p>
            </div>

            <a href="{{ route('noticias.index') }}" class="section-link">Ver todas</a>
        </div>

        <div class="news-home__grid">
            @foreach ($ultimasNoticias as $noticia)
                @php
                    $contenidoLimpio = \Illuminate\Support\Str::of($noticia->contenido)
                        ->stripTags()
                        ->squish()
                        ->limit(140);
                @endphp

                <article class="news-card">
                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="news-card__image">
                        <img src="{{ $noticia->imagen_destacada_url }}" alt="{{ $noticia->titulo }}">
                    </a>

                    <div class="news-card__body">
                        <div class="news-card__meta">
                            {{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y') }}
                        </div>

                        <h3>
                            <a href="{{ route('noticias.show', $noticia->slug) }}">
                                {{ $noticia->titulo }}
                            </a>
                        </h3>

                        <p>{{ $contenidoLimpio }}</p>

                        <a href="{{ route('noticias.show', $noticia->slug) }}" class="news-card__link">
                            Leer más →
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="info-strip">
        <div class="section-heading">
            <h2>Información útil</h2>
            <p>Canales principales para atención e información institucional.</p>
        </div>

        <div class="info-strip__grid">
            <div class="info-box">
                <h3>Dirección</h3>
                <p>Reconquista 26, Chacabuco, Buenos Aires</p>
            </div>

            <div class="info-box">
                <h3>Horarios</h3>
                <p>Lunes a viernes de 7:00 a 13:00 hs</p>
            </div>

            <div class="info-box">
                <h3>Contacto</h3>
                <p>(02352) 470300 · contacto@chacabuco.gob.ar</p>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    const STORAGE_KEY = 'chacabuco_clima_oculto';
    const widget = document.getElementById('clima-widget');
    if (!widget) return;

    if (localStorage.getItem(STORAGE_KEY) === '1') return;

    const WMO = {
        0:'☀️', 1:'🌤️', 2:'⛅', 3:'☁️',
        45:'🌫️', 48:'🌫️',
        51:'🌦️', 53:'🌦️', 55:'🌦️',
        61:'🌧️', 63:'🌧️', 65:'🌧️',
        71:'🌨️', 73:'🌨️', 75:'🌨️', 77:'🌨️',
        80:'🌦️', 81:'🌦️', 82:'🌦️',
        85:'🌨️', 86:'🌨️',
        95:'⛈️', 96:'⛈️', 99:'⛈️'
    };

    fetch('https://api.open-meteo.com/v1/forecast?latitude=-34.6435&longitude=-60.4737&current=temperature_2m,weathercode&timezone=America%2FArgentina%2FBuenos_Aires&forecast_days=1')
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            const temp = Math.round(data.current.temperature_2m);
            const code = data.current.weathercode;
            document.getElementById('clima-temp').textContent = temp + '°C';
            document.getElementById('clima-icon').textContent = WMO[code] ?? '🌡️';
            widget.hidden = false;
        })
        .catch(() => { /* silencioso — no mostrar si falla la API */ });

    document.getElementById('clima-cerrar').addEventListener('click', function () {
        widget.hidden = true;
        localStorage.setItem(STORAGE_KEY, '1');
    });
})();
</script>
@endpush

