<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Chacabuco Noticias')</title>

    <meta name="description" content="@yield('meta_description', 'Noticias de la Municipalidad de Chacabuco')">

    <meta property="og:title" content="@yield('title', 'Chacabuco Noticias')">
    <meta property="og:description" content="@yield('meta_description', 'Noticias de la Municipalidad de Chacabuco')">
    <meta property="og:image" content="@yield('og_image', asset('images/importantes/default-noticia.webp'))">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

    @stack('scripts_head')
    <script>
    (function () {
        var s = localStorage.getItem('chacabuco_font_size');
        if (s && s !== 'normal') document.documentElement.classList.add('font-' + s);
    })();
    </script>
</head>
<body>
    <div class="contenedor">
        @include('partials.header')

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="site-footer__grid">
                <div>
                    <h3>Municipalidad de Chacabuco</h3>
                    <p>
                        Portal oficial del municipio. Información institucional, noticias, trámites y servicios
                        para la comunidad.
                    </p>
                </div>

                <div>
                    <h4>Contacto</h4>
                    <p>Reconquista 26, Chacabuco</p>
                    <p>02352 470300</p>
                    <p>contacto@chacabuco.gob.ar</p>
                </div>

                <div>
                    <h4>Enlaces útiles</h4>
                    <p><a href="{{ route('noticias.index') }}">Noticias</a></p>
                    <p><a href="{{ route('tramites-servicios.index') }}">Tramites</a></p>
                    <p><a href="{{ route('tramites-servicios.index') }}">Servicios</a></p>
                </div>
            </div>

            <div class="site-footer__bottom">
                © {{ date('Y') }} Municipalidad de Chacabuco - Todos los derechos reservados
            </div>
        </footer>
    </div>
    <div id="app-toast" class="app-toast">
        <span id="app-toast-message"></span>
    </div>

    <div id="app-confirm-modal" class="app-confirm-modal" hidden>
        <div class="app-confirm-overlay" id="app-confirm-overlay"></div>

        <div class="app-confirm-dialog">
            <h3>Confirmar acción</h3>
            <p id="app-confirm-message">¿Seguro?</p>

            <div class="app-confirm-actions">
                <button type="button" class="btn btn-secondary" id="app-confirm-cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="app-confirm-ok">Aceptar</button>
            </div>
        </div>
    </div>

@include('partials.evento-reminder')
@include('partials.chatbot')

@if(request()->routeIs('home'))
    @php
        $__popupActivo = config_sistema('popup_activo') === '1';
        $__popupImagen = config_sistema('popup_imagen');
        $__popupBotonTexto = config_sistema('popup_boton_texto');
        $__popupBotonUrl   = config_sistema('popup_boton_url');
    @endphp
    @if($__popupActivo && $__popupImagen)
        <div id="anuncio-popup"
             class="anuncio-popup"
             hidden
             role="dialog"
             aria-modal="true"
             aria-label="Anuncio del municipio">
            <div class="anuncio-popup__dialog">
                <button type="button" class="anuncio-popup__cerrar" id="anuncio-popup-cerrar" aria-label="Cerrar anuncio">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="anuncio-popup__imagen">
                    <img src="{{ $__popupImagen }}" alt="Anuncio Municipalidad de Chacabuco">
                </div>
                @if($__popupBotonTexto)
                    <div class="anuncio-popup__footer">
                        <a href="{{ $__popupBotonUrl ?: '#' }}"
                           class="btn btn-primary anuncio-popup__btn"
                           {{ $__popupBotonUrl ? 'target="_blank" rel="noopener"' : '' }}>
                            {{ $__popupBotonTexto }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <script>
        (function () {
            const popup = document.getElementById('anuncio-popup');
            if (!popup) return;

            var cerrado = false;

            var cerrar = function () {
                cerrado = true;
                popup.classList.remove('is-visible');
                setTimeout(function () { popup.hidden = true; }, 280);
            };

            setTimeout(function () {
                if (cerrado) return;
                popup.hidden = false;
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () { popup.classList.add('is-visible'); });
                });
            }, 900);

            document.getElementById('anuncio-popup-cerrar').addEventListener('click', cerrar);
            popup.addEventListener('click', function (e) { if (e.target === popup) cerrar(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') cerrar(); });
        })();
        </script>
    @endif
@endif

<div class="theme-control">
    <button type="button" id="theme-toggle" class="theme-toggle" title="Cambiar tema visual">
        🌙 Oscuro
    </button>
</div>
{{-- Barra de utilidades para móvil (≤600px) --}}
<div class="mobile-util-bar" aria-label="Controles de accesibilidad">
    <button type="button" id="mobile-theme-btn" class="mobile-util__theme" title="Cambiar tema">🌙</button>
    <div class="mobile-util__sep"></div>
    <span class="mobile-util__label">Texto</span>
    <button type="button" class="font-control__btn mobile-util__font-btn" data-size="normal" title="Normal">A-</button>
    <button type="button" class="font-control__btn mobile-util__font-btn" data-size="grande" title="Grande">A</button>
    <button type="button" class="font-control__btn mobile-util__font-btn" data-size="extra"  title="Muy grande">A+</button>
</div>

<div id="font-control" class="font-control" aria-label="Ajustar tamaño de texto">
    <span class="font-control__label">Texto</span>
    <button type="button" class="font-control__btn" data-size="normal" title="Tamaño normal">A-</button>
    <button type="button" class="font-control__btn" data-size="grande" title="Texto grande">A</button>
    <button type="button" class="font-control__btn" data-size="extra"  title="Texto muy grande">A+</button>
</div>
<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui-feedback.js') }}"></script>
<script>
(function () {
    var STORAGE_KEY = 'chacabuco_font_size';

    function applySize(size) {
        var html = document.documentElement;
        html.classList.remove('font-grande', 'font-extra');
        if (size !== 'normal') html.classList.add('font-' + size);
    }

    function markActive(size) {
        document.querySelectorAll('.font-control__btn').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.dataset.size === size);
        });
    }

    var current = localStorage.getItem(STORAGE_KEY) || 'normal';
    applySize(current);
    markActive(current);

    document.querySelectorAll('.font-control__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            current = btn.dataset.size;
            applySize(current);
            markActive(current);
            localStorage.setItem(STORAGE_KEY, current);
        });
    });
})();
(function () {
    var mobileBtn = document.getElementById('mobile-theme-btn');
    if (!mobileBtn) return;

    function syncIconMobile() {
        mobileBtn.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
    }

    syncIconMobile();

    mobileBtn.addEventListener('click', function () {
        var desktop = document.getElementById('theme-toggle');
        if (desktop) {
            desktop.click();
        } else {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }
        syncIconMobile();
    });
})();
</script>
<script>
(function () {
    const ENDPOINT   = '/turismo/eventos-proximos';
    const INTERVALO  = 5 * 60 * 1000;
    const DEMORA_INI = 2 * 1000;
    const DURACION   = 17 * 1000;

    const card      = document.getElementById('evento-reminder');
    const body      = document.getElementById('evento-reminder-body');
    const btnCerrar = document.getElementById('evento-reminder-close');

    if (!card || !body || !btnCerrar) return;

    let ocultarTimer = null;

    function formatFecha(inicio, fin) {
        if (!inicio) return '';
        return fin && fin !== inicio ? inicio + ' al ' + fin : inicio;
    }

    function mostrar(eventos) {
        if (!eventos.length) return;

        body.innerHTML = eventos.map(ev => {
            const fecha = formatFecha(ev.fecha_inicio, ev.fecha_fin);
            const hora  = ev.hora_inicio ? ev.hora_inicio + ' hs' : '';
            const href  = '/turismo/' + (ev.slug_localidad || '') + '?tipo=evento';
            return `<a href="${href}" class="evento-reminder__item">
                        <span class="evento-reminder__nombre">${ev.titulo}</span>
                        <span class="evento-reminder__meta">
                            <span class="evento-reminder__localidad">
                                <i class="fa-solid fa-location-dot"></i> ${ev.localidad}
                            </span>
                            ${fecha ? `<span class="evento-reminder__fecha">${hora ? hora + ' · ' : ''}${fecha}</span>` : (hora ? `<span class="evento-reminder__fecha">${hora}</span>` : '')}
                        </span>
                    </a>`;
        }).join('');

        card.hidden = false;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => card.classList.add('is-visible'));
        });

        clearTimeout(ocultarTimer);
        ocultarTimer = setTimeout(ocultar, DURACION);
    }

    function ocultar() {
        card.classList.remove('is-visible');
        card.addEventListener('transitionend', () => {
            card.hidden = true;
        }, { once: true });
    }

    async function verificarEventos() {
        try {
            const res = await fetch(ENDPOINT);
            if (!res.ok) return;
            const eventos = await res.json();
            if (eventos.length) mostrar(eventos);
        } catch (_) {}
    }

    btnCerrar.addEventListener('click', ocultar);

    setTimeout(verificarEventos, DEMORA_INI);
    setInterval(verificarEventos, INTERVALO);
})();
</script>
@stack('scripts')
</body>
</html>
