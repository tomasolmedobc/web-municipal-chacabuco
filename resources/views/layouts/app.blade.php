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

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui-feedback.js') }}"></script>
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
