<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chacabuco Noticias')</title>

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
                    <p><a href="#">Trámites</a></p>
                    <p><a href="#">Servicios</a></p>
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
    <div id="toast-container"></div>
<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui-feedback.js') }}"></script>
@stack('scripts')
</body>
</html>