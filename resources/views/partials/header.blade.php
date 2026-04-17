<header class="site-header">
    <div class="site-header__top">
        <a href="{{ url('/') }}" class="site-brand">
            <div class="site-brand__logo">
                <img src="{{ asset('images/importantes/Escudo1_resultado.webp') }}" alt="Municipalidad de Chacabuco">
            </div>

            <div class="site-brand__text">
                <span class="site-brand__eyebrow">Municipalidad de Chacabuco</span>
                <h1>Portal Oficial</h1>
            </div>
        </a>

        <button class="theme-toggle" id="theme-toggle" type="button">
            🌙 Oscuro
        </button>
    </div>

    <nav class="site-nav">
        <a href="{{ url('/') }}">Inicio</a>
        <a href="{{ route('noticias.index') }}">Noticias</a>
        <a href="#">Trámites</a>
        <a href="#">Servicios</a>
        <a href="#">Áreas</a>
        <a href="#">Contacto</a>
    </nav>
</header>