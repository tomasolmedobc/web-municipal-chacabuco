@extends('layouts.app')

@section('title', 'Noticias')

@section('content')
    <section class="section-heading">
        <h2>Noticias</h2>
        <p>Ultimas novedades, comunicados e informacion municipal.</p>
    </section>

    <form method="GET" action="{{ route('noticias.index') }}" class="filtros">
        <input
            type="text"
            name="q"
            value="{{ $busqueda ?? '' }}"
            placeholder="Buscar noticias..."
            class="filtro-input filtro-input-busqueda"
        >

        <div class="filtro-fecha">
            <label for="desde">Desde</label>
            <input type="date" id="desde" name="desde" value="{{ $desde ?? '' }}" class="filtro-input">
        </div>

        <div class="filtro-fecha">
            <label for="hasta">Hasta</label>
            <input type="date" id="hasta" name="hasta" value="{{ $hasta ?? '' }}" class="filtro-input">
        </div>

        <button type="submit" class="boton-filtro">Filtrar</button>
        <a href="{{ route('noticias.index') }}" class="boton-limpiar">Limpiar</a>
    </form>

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
                <div class="fecha">{{ $destacada->fecha?->format('d/m/Y H:i') }}</div>
                <h1 class="hero-title">
                    <a href="{{ route('noticias.show', $destacada->slug) }}">{{ $destacada->titulo }}</a>
                </h1>
                <p class="hero-resumen">{{ $resumenDestacada }}</p>
                <a href="{{ route('noticias.show', $destacada->slug) }}" class="btn btn-primary">Leer noticia</a>
            </div>
        </article>
    @endif

    @if ($noticias->count() === 0 && ! $destacada)
        <div class="detalle">
            <h2>No hay noticias disponibles</h2>
            <p>Proba con otros filtros o volve mas tarde para ver nuevas publicaciones.</p>
        </div>
    @endif

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
                    <div class="fecha">
                        {{ $noticia->fecha?->format('d/m/Y H:i') }}
                        @if ($noticia->autor)
                            · {{ $noticia->autor }}
                        @endif
                    </div>

                    <h2>
                        <a href="{{ route('noticias.show', $noticia->slug) }}">{{ $noticia->titulo }}</a>
                    </h2>

                    <p class="resumen">{{ $resumen }}</p>

                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="leer-mas">Leer mas</a>
                </div>
            </article>
        @endforeach
    </div>

    @if ($noticias->hasPages())
        <div class="paginacion">
            {{ $noticias->links('vendor.pagination.custom') }}
        </div>
    @endif
@endsection
