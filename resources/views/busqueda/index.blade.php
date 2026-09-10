@extends('layouts.app')
@section('title', $q ? 'Resultados para "' . $q . '"' : 'Búsqueda')
@section('meta_description', $q ? 'Resultados de búsqueda para "' . $q . '" en el portal municipal de Chacabuco.' : 'Buscá noticias, atractivos turísticos y más en el portal de la Municipalidad de Chacabuco.')

@php
function busquedaHighlight(string $texto, array $palabras): string {
    if (empty($palabras)) return e($texto);
    $patron = implode('|', array_map(fn($p) => preg_quote($p, '/'), $palabras));
    return preg_replace_callback(
        '/(' . $patron . ')/iu',
        fn($m) => '<mark class="busqueda-highlight">' . e($m[0]) . '</mark>',
        e($texto)
    );
}
@endphp

@section('content')
<section class="busqueda-header">
    <h1>Búsqueda</h1>

    <form action="{{ route('busqueda') }}" method="GET" class="busqueda-form" role="search">
        <div class="busqueda-input-wrap">
            <i class="fa-solid fa-magnifying-glass busqueda-input-icon"></i>
            <input
                type="search"
                name="q"
                value="{{ $q }}"
                placeholder="Buscá noticias, turismo, trámites…"
                class="busqueda-input"
                autofocus
                autocomplete="off"
                minlength="3"
                aria-label="Buscar en el portal"
            >
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
</section>

@if($q && mb_strlen($q) < 3)
    <div class="busqueda-aviso">
        <i class="fa-solid fa-circle-info"></i>
        Ingresá al menos 3 caracteres para buscar.
    </div>
@elseif($q && $total === 0)
    <div class="busqueda-vacia">
        <i class="fa-solid fa-magnifying-glass"></i>
        <p>No se encontraron resultados para <strong>"{{ $q }}"</strong>.</p>
        <p class="busqueda-vacia__hint">Probá con otras palabras o revisá la ortografía.</p>
    </div>
@elseif($q)
    <p class="busqueda-total">
        <strong>{{ number_format($total) }}</strong> {{ $total === 1 ? 'resultado' : 'resultados' }} para
        <strong>"{{ $q }}"</strong>
    </p>

    {{-- SECCIONES DEL PORTAL --}}
    @if($secciones->count())
    <section class="busqueda-seccion">
        <h2 class="busqueda-seccion__titulo">
            <i class="fa-solid fa-sitemap"></i>
            Secciones del portal
            <span class="busqueda-seccion__count">{{ $secciones->count() }}</span>
        </h2>

        <div class="busqueda-secciones-grid">
            @foreach($secciones as $seccion)
            <a href="{{ route($seccion['url']) }}" class="busqueda-seccion-card">
                <span class="busqueda-seccion-card__icon">
                    <i class="fa-solid {{ $seccion['icono'] }}"></i>
                </span>
                <div>
                    <strong>{{ $seccion['titulo'] }}</strong>
                    <span>{{ $seccion['desc'] }}</span>
                </div>
                <i class="fa-solid fa-chevron-right busqueda-seccion-card__arrow"></i>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ACCESOS RÁPIDOS --}}
    @if($accesos->count())
    <section class="busqueda-seccion">
        <h2 class="busqueda-seccion__titulo">
            <i class="fa-solid fa-bolt"></i>
            Accesos rápidos
            <span class="busqueda-seccion__count">{{ $accesos->count() }}</span>
        </h2>

        <div class="busqueda-secciones-grid">
            @foreach($accesos as $acceso)
            @php $url = $acceso->url_personalizada ?: $acceso->url; @endphp
            <a href="{{ $url }}"
               class="busqueda-seccion-card"
               {{ str_starts_with($url, 'http') ? 'target="_blank" rel="noopener"' : '' }}>
                <span class="busqueda-seccion-card__icon">
                    <i class="fa-solid {{ $acceso->icono ?? 'fa-link' }}"></i>
                </span>
                <div>
                    <strong>{!! busquedaHighlight($acceso->titulo, $palabras) !!}</strong>
                    @if($acceso->descripcion)
                        <span>{!! busquedaHighlight($acceso->descripcion, $palabras) !!}</span>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right busqueda-seccion-card__arrow"></i>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- NOTICIAS --}}
    @if($noticias->total() > 0)
    <section class="busqueda-seccion">
        <h2 class="busqueda-seccion__titulo">
            <i class="fa-solid fa-newspaper"></i>
            Noticias
            <span class="busqueda-seccion__count">{{ $noticias->total() }}</span>
        </h2>

        <div class="busqueda-lista">
            @foreach($noticias as $noticia)
            @php
                $excerpt = \Illuminate\Support\Str::of($noticia->contenido)->stripTags()->squish()->limit(160);
            @endphp
            <a href="{{ route('noticias.show', $noticia->slug) }}" class="busqueda-card">
                @if($noticia->imagen_destacada)
                <img src="{{ $noticia->imagen_destacada_url }}" alt="{{ $noticia->titulo }}" class="busqueda-card__img" loading="lazy">
                @endif
                <div class="busqueda-card__body">
                    <div class="busqueda-card__meta">
                        <span class="busqueda-badge busqueda-badge--noticia">
                            <i class="fa-solid fa-newspaper"></i> Noticia
                        </span>
                        <span class="busqueda-card__fecha">{{ $noticia->fecha?->format('d/m/Y') }}</span>
                    </div>
                    <h3 class="busqueda-card__titulo">{!! busquedaHighlight($noticia->titulo, $palabras) !!}</h3>
                    @if($excerpt)
                    <p class="busqueda-card__excerpt">{!! busquedaHighlight((string)$excerpt, $palabras) !!}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        @if($noticias->hasPages())
        <div class="paginacion">
            {{ $noticias->links('vendor.pagination.custom') }}
        </div>
        @endif
    </section>
    @endif

    {{-- TURISMO --}}
    @if($items->count())
    <section class="busqueda-seccion">
        <h2 class="busqueda-seccion__titulo">
            <i class="fa-solid fa-map-location-dot"></i>
            Turismo
            <span class="busqueda-seccion__count">{{ $items->count() }}</span>
        </h2>

        <div class="busqueda-lista">
            @foreach($items as $item)
            @php
                $excerpt = \Illuminate\Support\Str::of($item->descripcion)->stripTags()->squish()->limit(160);
                $config  = \App\Models\TurismoItem::configTipo($item->tipo);
            @endphp
            <a href="{{ route('turismo.show.item', [$item->localidad->slug, $item->id]) }}" class="busqueda-card">
                <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}" class="busqueda-card__img" loading="lazy">
                <div class="busqueda-card__body">
                    <div class="busqueda-card__meta">
                        <span class="busqueda-badge busqueda-badge--turismo">
                            <i class="fa-solid fa-map-location-dot"></i> {{ $config['titulo'] }}
                        </span>
                        @if($item->localidad)
                        <span class="busqueda-card__fecha">
                            <i class="fa-solid fa-location-dot"></i> {{ $item->localidad->nombre }}
                        </span>
                        @endif
                    </div>
                    <h3 class="busqueda-card__titulo">{!! busquedaHighlight($item->titulo, $palabras) !!}</h3>
                    @if($excerpt)
                    <p class="busqueda-card__excerpt">{!! busquedaHighlight((string)$excerpt, $palabras) !!}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
@endif
@endsection
