@extends('layouts.app')

@section('title', $contenido['titulo'] ?? 'Habilitaciones')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Habilitaciones Comerciales'],
]" />
<section class="hab-hero">
    <div>
        <span class="section-badge">Tramites</span>
        <h1>{{ $contenido['titulo'] }}</h1>
        <p>{{ $contenido['bajada'] }}</p>
    </div>

    <a href="#documentacion" class="btn btn-primary">
        <i class="fa-solid fa-file-arrow-down"></i>
        Ver documentacion
    </a>
</section>

<section class="hab-card hab-intro">
    <span class="section-badge">Informacion</span>

    @foreach($contenido['intro'] as $parrafo)
        <p>{{ $parrafo }}</p>
    @endforeach

    <div class="hab-alert">
        <strong>{{ $contenido['alerta']['titulo'] }}</strong>
        <p>{{ $contenido['alerta']['texto'] }}</p>
    </div>

    <div class="hab-contact-strip">
        <span><i class="fa-solid fa-location-dot"></i> {{ $contenido['contacto']['direccion'] }}</span>
        <span><i class="fa-solid fa-clock"></i> {{ $contenido['contacto']['horario'] }}</span>
        <span><i class="fa-solid fa-phone"></i> {{ $contenido['contacto']['telefono'] }}</span>
        <span><i class="fa-solid fa-envelope"></i> {{ $contenido['contacto']['mail'] }}</span>
    </div>
</section>

<section class="hab-online">
    <div class="section-heading">
        <h2>Tramites Online</h2>
        <p>Accesos principales para iniciar gestiones digitales vinculadas a habilitaciones.</p>
    </div>

    <div class="hab-online__grid">
        @foreach($contenido['tramites_online'] as $tramite)
            <a href="{{ $tramite['url'] }}"
               class="hab-online-card"
               target="{{ $tramite['url'] !== '#' ? '_blank' : '_self' }}"
               rel="{{ $tramite['url'] !== '#' ? 'noopener' : '' }}">
                <span>
                    <i class="fa-solid {{ $tramite['icono'] }}"></i>
                </span>

                <strong>{{ $tramite['titulo'] }}</strong>
                <p>{{ $tramite['descripcion'] }}</p>
            </a>
        @endforeach
    </div>
</section>

<section class="hab-accordion-section">
    <div class="section-heading">
        <h2>Tramites presenciales obligatorios</h2>
        <p>Requisitos orientativos por tipo de gestion. Cada bloque se puede abrir para consultar el detalle.</p>
    </div>

    <div class="hab-accordion">
        @foreach($contenido['acordeones'] as $acordeon)
            <details class="hab-collapse" @if($acordeon['abierto'] ?? false) open @endif>
                <summary>
                    <span>{{ $acordeon['titulo'] }}</span>
                    <i class="fa-solid fa-circle-plus"></i>
                </summary>

                <div class="hab-collapse__body">
                    <p>Requisitos:</p>

                    <ul>
                        @foreach($acordeon['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </details>
        @endforeach
    </div>
</section>

<section class="hab-docs" id="documentacion">
    <div class="section-heading">
        <span class="section-badge">Descargas</span>
        <h2>Formularios y documentacion</h2>
        <p>Botonera de archivos para iniciar, completar o actualizar tramites de habilitaciones.</p>
    </div>

    <div class="hab-file-grid">
        @if($archivosAdmin->count())
            @foreach($archivosAdmin as $archivo)
                <a href="{{ $archivo->archivo_ruta ?: $archivo->link_externo }}"
                   class="hab-file"
                   target="_blank"
                   rel="noopener">
                    <span class="hab-file__icon">
                        <i class="fa-solid {{ $archivo->archivo_ruta ? (str_contains((string) $archivo->archivo_mime, 'pdf') ? 'fa-file-pdf' : 'fa-file-lines') : 'fa-link' }}"></i>
                    </span>

                    <strong>{{ $archivo->titulo }}</strong>
                </a>
            @endforeach
        @else
            @foreach($contenido['archivos'] as $archivo)
                <a href="{{ asset(ltrim($archivo['url'], '/')) }}"
                   class="hab-file"
                   target="_blank"
                   rel="noopener">
                    <span class="hab-file__icon">
                        <i class="fa-solid fa-file-pdf"></i>
                    </span>

                    <strong>{{ $archivo['titulo'] }}</strong>
                </a>
            @endforeach
        @endif
    </div>
</section>

<section class="hab-ordinances" id="normativas">
    <div class="section-heading">
        <h2>Normativas Vigentes</h2>
        <p>Ordenanzas, leyes y disposiciones vinculadas a habilitaciones comerciales.</p>
    </div>

    <form method="GET" action="{{ route('habilitaciones.index') }}#normativas" class="hab-ordinances-search">
        <label>
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search"
                   name="ordenanza"
                   value="{{ $busquedaOrdenanzas }}"
                   placeholder="Buscar normativa...">
        </label>

        <button type="submit" class="btn btn-primary">Buscar</button>

        @if($busquedaOrdenanzas)
            <a href="{{ route('habilitaciones.index') }}#normativas" class="btn btn-secondary">
                Limpiar
            </a>
        @endif
    </form>

    @if($ordenanzasAdmin->count())
        <ul class="hab-normativas-lista">
            @foreach($ordenanzasAdmin as $normativa)
                @php $href = $normativa->archivo_ruta ?: $normativa->link_externo; @endphp
                <li class="hab-normativas-item">
                    @if($href)
                        <a href="{{ $href }}" target="_blank" rel="noopener" class="hab-normativas-link">
                            <i class="fa-regular fa-file-pdf"></i>
                            <span>{{ $normativa->titulo }}@if($normativa->numero) <span class="hab-normativas-codigo">({{ $normativa->numero }})</span>@endif</span>
                        </a>
                    @else
                        <span class="hab-normativas-link hab-normativas-link--sin-archivo">
                            <i class="fa-regular fa-file-pdf"></i>
                            <span>{{ $normativa->titulo }}@if($normativa->numero) <span class="hab-normativas-codigo">({{ $normativa->numero }})</span>@endif</span>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @elseif($busquedaOrdenanzas)
        <div class="hab-empty">No se encontraron normativas con esa búsqueda.</div>
    @else
        <ul class="hab-normativas-lista">
            @foreach($contenido['ordenanzas'] as $ord)
                <li class="hab-normativas-item">
                    <span class="hab-normativas-link hab-normativas-link--sin-archivo">
                        <i class="fa-regular fa-file-pdf"></i>
                        <span>{{ $ord }}</span>
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
</section>

<section class="volver-ts">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>
@endsection
