<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chacabuco Noticias')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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

    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>



