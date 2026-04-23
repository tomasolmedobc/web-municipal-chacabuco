@extends('layouts.app')

@section('title', 'Noticias')

@section('content')
    <section class="section-heading">
        <h2>Noticias</h2>
        <p>Últimas novedades, comunicados e información municipal.</p>
    </section>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('noticias.index') }}" class="filtros">

        <input
            type="text"
            name="q"
            value="{{ $busqueda ?? '' }}"
            placeholder="Buscar noticias..."
            class="filtro-input filtro-input-busqueda"
        >

        <div class="filtro-fecha">
            <label for="categoria">Categoría</label>
            <select name="categoria" id="categoria" class="filtro-input">
                <option value="">Todas</option>

                @foreach($categoriasFiltro as $categoriaPadre)
                    <option value="{{ $categoriaPadre->slug }}"
                        {{ ($categoriaSlug ?? '') === $categoriaPadre->slug ? 'selected' : '' }}>
                        {{ $categoriaPadre->nombre }}
                    </option>

                    @foreach($categoriaPadre->hijas as $hija)
                        <option value="{{ $hija->slug }}"
                            {{ ($categoriaSlug ?? '') === $hija->slug ? 'selected' : '' }}>
                            — {{ $hija->nombre }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="filtro-fecha">
            <label for="desde">Desde</label>
            <input type="date" id="desde" name="desde" value="{{ $desde ?? '' }}" class="filtro-input">
        </div>

        <div class="filtro-fecha">
            <label for="hasta">Hasta</label>
            <input type="date" id="hasta" name="hasta" value="{{ $hasta ?? '' }}" class="filtro-input">
        </div>

        <div class="filtro-fecha">
            <label for="orden">Orden</label>
            <select name="orden" id="orden" class="filtro-input">
                <option value="nuevas" {{ ($orden ?? 'nuevas') === 'nuevas' ? 'selected' : '' }}>Más recientes</option>
                <option value="antiguas" {{ ($orden ?? '') === 'antiguas' ? 'selected' : '' }}>Más antiguas</option>
            </select>
        </div>

        <button type="submit" class="boton-filtro">Filtrar</button>
        <a href="{{ route('noticias.index') }}" class="boton-limpiar">Limpiar</a>
    </form>

    {{-- RESULTADOS --}}
    @if(($busqueda ?? '') !== '' || ($desde ?? '') || ($hasta ?? '') || ($orden ?? 'nuevas') !== 'nuevas' || ($categoriaSlug ?? ''))
        <p class="fecha" style="margin-bottom: 18px;">
            Se encontraron <strong>{{ $totalResultados }}</strong> resultado(s).
        </p>
    @endif


    {{-- DESTACADA --}}
    @if ($destacada)
        @php
            $resumenDestacada = \Illuminate\Support\Str::of($destacada->contenido)
                ->stripTags()
                ->squish()
                ->limit(220);
        @endphp

        <article class="hero">
            @if ($destacada->imagen_destacada)
                <a href="{{ route('noticias.show', $destacada->slug) }}" class="hero-media">
                    <img src="{{ $destacada->imagen_destacada }}" alt="{{ $destacada->titulo }}">
                </a>
            @endif

            <div class="hero-body">
                <span class="hero-badge">Noticia destacada</span>

                {{-- CATEGORÍAS --}}
                @if ($destacada->categorias->count())
                    <div class="categorias-list">
                        @foreach($destacada->categorias as $categoria)
                            <a href="{{ route('noticias.index', ['categoria' => $categoria->slug]) }}"
                               class="categoria-noticia">
                                {{ $categoria->nombre }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="fecha">{{ $destacada->fecha?->format('d/m/Y H:i') }}</div>

                <h1 class="hero-title">
                    <a href="{{ route('noticias.show', $destacada->slug) }}">
                        {{ $destacada->titulo }}
                    </a>
                </h1>

                <p class="hero-resumen">{{ $resumenDestacada }}</p>

                <a href="{{ route('noticias.show', $destacada->slug) }}" class="btn btn-primary">
                    Leer noticia
                </a>
            </div>
        </article>
    @endif


    {{-- VACÍO --}}
    @if ($noticias->count() === 0 && ! $destacada)
        <div class="detalle">
            <h2>No hay noticias disponibles</h2>
            <p>Probá con otros filtros o volvé más tarde.</p>
        </div>
    @endif


    {{-- LISTADO --}}
    <div class="grilla-noticias">
        @foreach ($noticias as $noticia)
            @php
                $resumen = \Illuminate\Support\Str::of($noticia->contenido)
                    ->stripTags()
                    ->squish()
                    ->limit(220);
            @endphp

            <article class="noticia">

                @if ($noticia->imagen_destacada)
                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="noticia-media">
                        <img src="{{ $noticia->imagen_destacada }}" alt="{{ $noticia->titulo }}">
                    </a>
                @endif

                <div class="noticia-body">

                    {{-- CATEGORÍAS --}}
                    @if ($noticia->categorias->count())
                        <div class="categorias-list">
                            @foreach($noticia->categorias as $categoria)
                                <a href="{{ route('noticias.index', ['categoria' => $categoria->slug]) }}"
                                   class="categoria-noticia">
                                    {{ $categoria->nombre }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="fecha">
                        {{ $noticia->fecha?->format('d/m/Y H:i') }}
                        @if ($noticia->autor)
                            · {{ $noticia->autor }}
                        @endif
                    </div>

                    <h2>
                        <a href="{{ route('noticias.show', $noticia->slug) }}">
                            {{ $noticia->titulo }}
                        </a>
                    </h2>

                    <p class="resumen">{{ $resumen }}</p>

                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="leer-mas">
                        Leer más
                    </a>
                </div>
            </article>
        @endforeach
    </div>


    {{-- PAGINACIÓN --}}
    @if ($noticias->hasPages())
        <div class="paginacion">
            {{ $noticias->links('vendor.pagination.custom') }}
        </div>
    @endif

@endsection


@push('scripts')
    <script src="{{ asset('js/noticias-filtros.js') }}"></script>
@endpush