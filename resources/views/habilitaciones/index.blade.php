@extends('layouts.app')

@section('title', $contenido['titulo'] ?? 'Habilitaciones')

@section('content')
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

<section class="hab-ordinances">
    <div class="section-heading">
        <h2>Listado de ordenanzas de referencia</h2>
        <p>Normativa vinculada a rubros y actividades comerciales.</p>
    </div>

    <form method="GET" action="{{ route('habilitaciones.index') }}#ordenanzas" class="hab-ordinances-search" id="ordenanzas">
        <label>
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search"
                   name="ordenanza"
                   value="{{ $busquedaOrdenanzas }}"
                   placeholder="Buscar por nombre, numero o año">
        </label>

        <button type="submit" class="btn btn-primary">Buscar</button>

        @if($busquedaOrdenanzas)
            <a href="{{ route('habilitaciones.index') }}#ordenanzas" class="btn btn-secondary">
                Limpiar
            </a>
        @endif
    </form>

    @if($ordenanzasAdmin->count())
        <div class="hab-ordinances-grid">
            @foreach($ordenanzasAdmin as $ordenanza)
                <a href="{{ $ordenanza->archivo_ruta ?: $ordenanza->link_externo }}"
                   class="hab-ordinance-card"
                   target="_blank"
                   rel="noopener">
                    <span class="hab-ordinance-card__number">
                        {{ $ordenanza->numero }}
                    </span>

                    <strong>{{ $ordenanza->titulo }}</strong>

                    <span>
                        {{ $ordenanza->categoria ?: 'Ordenanza' }}
                        @if($ordenanza->anio)
                            - {{ $ordenanza->anio }}
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    @elseif($busquedaOrdenanzas)
        <div class="hab-empty">
            No se encontraron ordenanzas con esa busqueda.
        </div>
    @else
        <ul>
            @foreach($contenido['ordenanzas'] as $ordenanza)
                <li>{{ $ordenanza }}</li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
