@extends('layouts.app')

@section('title', 'Licitaciones')

@section('content')
<section class="noticias-hero">
    <div>
        <span class="section-badge">Gobierno abierto</span>

        <h1>Licitaciones</h1>

        <p>
            Consulta licitaciones publicas y privadas del Municipio de Chacabuco.
        </p>
    </div>
</section>

@if($ultimasLicitaciones->count())
    <section class="section-heading">
        <span class="section-badge">Destacadas</span>

        <h2>Ultimas licitaciones destacadas</h2>

        <p>
            Las 4 publicaciones mas recientes, publicas o privadas.
        </p>
    </section>

    <section class="licitaciones-destacadas">
        @foreach($ultimasLicitaciones as $licitacion)
            <a href="{{ $licitacion->archivo_ruta ?: route('licitaciones.index') }}"
               target="{{ $licitacion->archivo_ruta ? '_blank' : '_self' }}"
               class="licitacion-mini-card">
                <div class="licitacion-mini-card__icon">
                    <i class="fa-solid {{ $licitacion->archivo_ruta ? 'fa-file-pdf' : 'fa-folder-open' }}"></i>
                </div>

                <div>
                    <div class="licitacion-mini-card__badges">
                        <span class="licitacion-badge badge-{{ $licitacion->tipo }}">
                            {{ $licitacion->tipo === 'publica' ? 'Publica' : 'Privada' }}
                        </span>

                        <span class="licitacion-badge badge-{{ $licitacion->estado }}">
                            {{ ucfirst($licitacion->estado) }}
                        </span>
                    </div>

                    <h3>{{ $licitacion->titulo }}</h3>

                    @if($licitacion->fecha_publicacion)
                        <small>{{ $licitacion->fecha_publicacion->format('d/m/Y') }}</small>
                    @endif
                </div>
            </a>
        @endforeach
    </section>
@endif

<form method="GET"
      action="{{ route('licitaciones.index') }}"
      class="filtros noticias-search-card">
    <div class="noticias-search-card__intro">
        <div>
            <h2>Buscar licitaciones</h2>

            <p>
                Busca por expediente, titulo o descripcion.
            </p>
        </div>
    </div>

    <div class="filtros-grid">
        <div class="filtro-fecha">
            <label>Buscar</label>

            <input
                type="text"
                name="q"
                value="{{ $busqueda }}"
                placeholder="Buscar licitacion..."
                class="filtro-input"
            >
        </div>

        <div class="filtro-fecha">
            <label>Tipo</label>

            <select name="tipo" class="filtro-input">
                <option value="">Todas</option>

                <option value="publica" {{ $tipo === 'publica' ? 'selected' : '' }}>
                    Publicas
                </option>

                <option value="privada" {{ $tipo === 'privada' ? 'selected' : '' }}>
                    Privadas
                </option>
            </select>
        </div>
    </div>

    <div class="filtros-actions">
        <button type="submit" class="boton-filtro">
            Filtrar
        </button>

        <a href="{{ route('licitaciones.index') }}" class="boton-limpiar">
            Limpiar
        </a>
    </div>
</form>

<section class="section-heading section-heading--between licitaciones-list-heading">
    <div>
        <span class="section-badge">Listado</span>

        <h2>Todas las licitaciones</h2>

        <p>
            @if($busqueda !== '' || $tipo)
                Se encontraron {{ $totalResultados }} resultado(s) para los filtros aplicados.
            @else
                Todas las licitaciones publicadas en el portal.
            @endif
        </p>
    </div>
</section>

<section class="news-home">
    <div class="news-home__grid">
        @forelse($licitaciones as $licitacion)
            <a href="{{ $licitacion->archivo_ruta ?: route('licitaciones.index') }}"
               target="{{ $licitacion->archivo_ruta ? '_blank' : '_self' }}"
               class="licitacion-card">
                <div class="licitacion-card__top">
                    <div class="licitacion-card__badges">
                        <span class="licitacion-badge badge-{{ $licitacion->tipo }}">
                            {{ $licitacion->tipo === 'publica' ? 'Publica' : 'Privada' }}
                        </span>

                        <span class="licitacion-badge badge-{{ $licitacion->estado }}">
                            {{ ucfirst($licitacion->estado) }}
                        </span>
                    </div>
                </div>

                <div>
                    <h3>{{ $licitacion->titulo }}</h3>
                </div>

                @if($licitacion->descripcion)
                    <p>
                        {{ \Illuminate\Support\Str::limit($licitacion->descripcion, 180) }}
                    </p>
                @endif

                <div class="licitacion-meta">
                    @if($licitacion->numero_expediente)
                        <span>
                            <i class="fa-solid fa-folder-open"></i>
                            Expte: {{ $licitacion->numero_expediente }}
                        </span>
                    @endif

                    @if($licitacion->anio)
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $licitacion->anio }}
                        </span>
                    @endif

                    @if($licitacion->fecha_publicacion)
                        <span>
                            <i class="fa-solid fa-clock"></i>
                            {{ $licitacion->fecha_publicacion->format('d/m/Y') }}
                        </span>
                    @endif
                </div>

                <div class="licitacion-actions">
                    @if($licitacion->archivo_ruta)
                        <span class="btn btn-primary">
                            Ver PDF
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="admin-empty">
                No hay licitaciones disponibles.
            </div>
        @endforelse
    </div>

    @if($licitaciones->hasPages())
        <div class="paginacion">
            {{ $licitaciones->links('vendor.pagination.custom') }}
        </div>
    @endif
</section>
@endsection
