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

        <div class="site-header__actions">
            <button class="theme-toggle" id="theme-toggle" type="button">
                🌙 Oscuro
            </button>

            @auth
                <div class="user-box">
                    <div class="user-box__info">
                        <span class="user-box__name">{{ auth()->user()->name }}</span>
                        <span class="user-box__rol">{{ ucfirst(auth()->user()->rol) }}</span>
                    </div>

                    <div class="user-box__actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Panel</a>
                        <a href="{{ route('admin.perfil.edit') }}" class="btn btn-secondary btn-sm">Perfil</a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Salir</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a>
            @endauth
        </div>
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