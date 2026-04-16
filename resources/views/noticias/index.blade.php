@extends('layouts.app')

@section('title', 'Listado de noticias')

@section('content')
    @if($destacada)
        @php
            $resumenDestacado = \Illuminate\Support\Str::of($destacada->contenido)
                ->stripTags()
                ->squish()
                ->limit(420);
        @endphp

        <section class="hero">
            <div class="hero-media">
                @if($destacada->imagen_destacada)
                    <img src="{{ $destacada->imagen_destacada }}" alt="{{ $destacada->titulo }}">
                @endif
            </div>

            <div class="hero-body">
                <span class="hero-badge">Noticia destacada</span>

                <h2 class="hero-title">
                    <a href="{{ route('noticias.show', $destacada->slug) }}">
                        {{ $destacada->titulo }}
                    </a>
                </h2>

                <div class="fecha">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($destacada->fecha)->format('d/m/Y - H:i') }} hs
                </div>

                <p class="hero-resumen">{{ $resumenDestacado }}</p>

                <a class="leer-mas" href="{{ route('noticias.show', $destacada->slug) }}">
                    Leer noticia completa →
                </a>
            </div>
        </section>
    @endif

    <form method="GET" action="{{ url('/noticias') }}" class="filtros">
    <input
        type="text"
        name="q"
        value="{{ $busqueda }}"
        placeholder="Buscar noticias..."
        class="filtro-input filtro-input-busqueda"
    >

    <div class="filtro-fecha">
        <label for="desde">Desde</label>
        <input
            type="date"
            id="desde"
            name="desde"
            value="{{ $desde }}"
            class="filtro-input"
        >
    </div>

    <div class="filtro-fecha">
        <label for="hasta">Hasta</label>
        <input
            type="date"
            id="hasta"
            name="hasta"
            value="{{ $hasta }}"
            class="filtro-input"
        >
    </div>

    <button type="submit" class="boton-filtro">Filtrar</button>

    <a href="{{ url('/noticias') }}" class="boton-limpiar">Limpiar</a>
</form>

    <h2 class="seccion-titulo">Más noticias</h2>
    @if ($noticias->count() === 0)
    <div class="noticia">
        <h2>No se encontraron noticias</h2>
        <p>Probá con otra búsqueda o cambiá el rango de fechas.</p>
    </div>
@endif
    @foreach ($noticias as $noticia)
        @php
            $contenidoLimpio = \Illuminate\Support\Str::of($noticia->contenido)
                ->stripTags()
                ->squish()
                ->limit(300);
        @endphp

        <article class="noticia">
            <div class="noticia-media">
                @if($noticia->imagen_destacada)
                    <img src="{{ $noticia->imagen_destacada }}" alt="{{ $noticia->titulo }}">
                @endif
            </div>

            <div class="noticia-body">
                <h2>
                    <a href="{{ route('noticias.show', $noticia->slug) }}">
                        {{ $noticia->titulo }}
                    </a>
                </h2>

                <div class="fecha">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y - H:i') }} hs
                </div>

                <p class="resumen">{{ $contenidoLimpio }}</p>

                <a class="leer-mas" href="{{ route('noticias.show', $noticia->slug) }}">
                    Leer más →
                </a>
            </div>
        </article>
    @endforeach

    <div class="paginacion">
        {{ $noticias->links('vendor.pagination.custom') }}
    </div>
@endsection