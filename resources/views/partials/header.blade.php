<header class="site-header">
    <div class="site-header__top">
        <a href="{{ url('/') }}" class="site-brand">
            <div class="site-brand__logo">
                <img src="{{ config_sistema('logo', asset('images/importantes/Escudo1_resultado.webp')) }}"
                        alt="Municipalidad de Chacabuco">
            </div>

            <div class="site-brand__text">
                <span class="site-brand__eyebrow">Municipalidad de Chacabuco</span>
                <h1>Portal Oficial</h1>
            </div>
        </a>

        <div class="site-header__actions">
            @auth
                <div class="user-box">
                    <div class="user-box__info">
                        <span class="user-box__name">{{ auth()->user()->name }}</span>
                        <span class="user-box__rol">{{ ucfirst(auth()->user()->rol) }}</span>
                    </div>

                    <div class="user-box__actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Panel</a>
                        <a href="{{ route('admin.perfil.edit') }}" class="btn btn-secondary btn-sm">Perfil</a>
                        <a href="{{ route('acceso-municipal.index') }}" class="btn btn-secondary btn-sm">Acceso</a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Salir</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('acceso-municipal.index') }}" class="btn btn-secondary btn-sm">Acceso Empleados</a>
            @endauth

        </div>
    </div>

    @php
        $navTramites = request()->routeIs(
            'tramites-servicios.*', 'infracciones.*', 'expedientes.*',
            'habilitaciones.*', 'reclamos.*', 'omic.*', 'recaudacion.*'
        );
        $navGobierno = request()->routeIs(
            'gobierno-abierto.*', 'licitaciones.*', 'gastos-recursos-balance.*', 'proveedores.*'
        );
    @endphp
    <nav class="site-nav">
        <a href="{{ url('/') }}"                          @class(['is-active' => request()->routeIs('home')])>Inicio</a>
        <a href="{{ route('noticias.index') }}"           @class(['is-active' => request()->routeIs('noticias.*')])>Noticias</a>
        <a href="{{ route('gobierno-abierto.index') }}"   @class(['is-active' => $navGobierno])>Gobierno Abierto</a>
        <a href="{{ route('tramites-servicios.index') }}" @class(['is-active' => $navTramites])>Trámites y Servicios</a>
        <a href="{{ route('turismo.index') }}"            @class(['is-active' => request()->routeIs('turismo.*')])>Turismo</a>
        <a href="{{ route('tasas.index') }}"             @class(['is-active' => request()->routeIs('tasas.*')])>Tasas Municipales</a>
    </nav>
</header>
