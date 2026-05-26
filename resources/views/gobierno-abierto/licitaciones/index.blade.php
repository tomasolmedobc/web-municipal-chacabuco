@extends('layouts.app')

@php
    $rutaIndex = match ($categoriaActiva) {
        \App\Models\Licitacion::CATEGORIA_GASTOS_RECURSOS_BALANCE => route('gastos-recursos-balance.index'),
        \App\Models\Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS => route('gobierno-abierto.botones.index'),
        default => route('licitaciones.index'),
    };
@endphp

@section('title', $config['titulo'])

@section('content')
<section class="noticias-hero">
    <div>
        <span class="section-badge">Gobierno abierto</span>

        <h1>{{ $config['titulo'] }}</h1>

        <p>{{ $config['descripcion'] }}</p>
    </div>
</section>

@if($ultimasLicitaciones->count())
    <section class="section-heading">
        <span class="section-badge">Nuevas</span>

        <h2>Ultimos documentos subidos</h2>

        <p>Las 4 publicaciones mas recientes de esta seccion.</p>
    </section>

    <section class="licitaciones-destacadas">
        @foreach($ultimasLicitaciones as $licitacion)
            <article class="licitacion-mini-card">
                <div class="licitacion-mini-card__icon">
                    <i class="fa-solid {{ $licitacion->archivos->count() ? ($esBotones ? 'fa-file-lines' : 'fa-file-pdf') : $config['icono'] }}"></i>
                </div>

                <div>
                    <div class="licitacion-mini-card__badges">
                        @if($usaFiltroTipo)
                            <span class="licitacion-badge badge-{{ $licitacion->tipo }}">
                                {{ $licitacion->tipo === 'publica' ? 'Publica' : 'Privada' }}
                            </span>
                        @endif

                        <span class="licitacion-badge badge-{{ $licitacion->estado }}">
                            {{ ucfirst($licitacion->estado) }}
                        </span>
                    </div>

                    <h3>{{ $licitacion->titulo }}</h3>

                    @if($licitacion->fecha_publicacion)
                        <small>{{ $licitacion->fecha_publicacion->format('d/m/Y') }}</small>
                    @endif

                    @if($licitacion->archivos->count())
                        <div class="licitacion-file-links">
                            @foreach($licitacion->archivos->take(2) as $archivo)
                                <a href="{{ $archivo->ruta }}" target="_blank">
                                    {{ $esBotones ? $archivo->nombre_original : ($loop->iteration === 1 ? 'Ver PDF' : 'PDF ' . $loop->iteration) }}
                                </a>
                            @endforeach

                            @if($licitacion->archivos->count() > 2)
                                <span>+{{ $licitacion->archivos->count() - 2 }}</span>
                            @endif
                        </div>
                    @elseif($licitacion->link_externo)
                        <div class="licitacion-file-links">
                            <a href="{{ $licitacion->link_externo }}" target="_blank">
                                Abrir link
                            </a>
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </section>
@endif

<form method="GET"
      action="{{ $rutaIndex }}"
      class="filtros noticias-search-card">
    <div class="noticias-search-card__intro">
        <div>
            <h2>Buscar {{ strtolower($config['titulo']) }}</h2>

            <p>{{ $esBotones ? 'Busca por titulo o descripcion.' : 'Busca por expediente, titulo o descripcion.' }}</p>
        </div>
    </div>

    <div class="filtros-grid">
        <div class="filtro-fecha">
            <label>Buscar</label>

            <input
                type="text"
                name="q"
                value="{{ $busqueda }}"
                placeholder="Buscar..."
                class="filtro-input"
            >
        </div>

        @if($usaFiltroTipo)
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
        @endif

        @unless($esBotones)
            <div class="filtro-fecha">
                <label for="desde">Desde</label>

                <input
                    type="date"
                    id="desde"
                    name="desde"
                    value="{{ $desde ?? '' }}"
                    class="filtro-input"
                >
            </div>

            <div class="filtro-fecha">
                <label for="hasta">Hasta</label>

                <input
                    type="date"
                    id="hasta"
                    name="hasta"
                    value="{{ $hasta ?? '' }}"
                    class="filtro-input"
                >
            </div>
        @endunless

        <div class="filtros-actions">
            <button type="submit" class="boton-filtro">
                Filtrar
            </button>

            <a href="{{ $rutaIndex }}" class="boton-limpiar">
                Limpiar
            </a>
        </div>
    </div>
</form>

<section class="section-heading section-heading--between licitaciones-list-heading">
    <div>
        <span class="section-badge">Listado</span>

        <h2>Todos los documentos</h2>

        <p>
            @if($busqueda !== '' || $tipo || ($desde ?? '') || ($hasta ?? ''))
                Se encontraron {{ $totalResultados }} resultado(s) para los filtros aplicados.
            @else
                Todas las publicaciones disponibles en esta seccion.
            @endif
        </p>
    </div>
</section>

<section class="news-home">
    <div class="news-home__grid">
        @forelse($licitaciones as $licitacion)
            <article class="licitacion-card">
                <div class="licitacion-card__top">
                    <div class="licitacion-card__badges">
                        @if($usaFiltroTipo)
                            <span class="licitacion-badge badge-{{ $licitacion->tipo }}">
                                {{ $licitacion->tipo === 'publica' ? 'Publica' : 'Privada' }}
                            </span>
                        @endif

                        <span class="licitacion-badge badge-{{ $licitacion->estado }}">
                            {{ ucfirst($licitacion->estado) }}
                        </span>
                    </div>
                </div>

                <div>
                    <h3>{{ $licitacion->titulo }}</h3>
                </div>

                @if($licitacion->descripcion)
                    <p>{{ \Illuminate\Support\Str::limit($licitacion->descripcion, 180) }}</p>
                @endif

                <div class="licitacion-meta">
                    @if(!$esBotones && $licitacion->numero_expediente)
                        <span>
                            <i class="fa-solid fa-folder-open"></i>
                            Expte: {{ $licitacion->numero_expediente }}
                        </span>
                    @endif

                    @if(!$esBotones && $licitacion->anio)
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $licitacion->anio }}
                        </span>
                    @endif

                    @if(!$esBotones && $licitacion->fecha_publicacion)
                        <span>
                            <i class="fa-solid fa-clock"></i>
                            {{ $licitacion->fecha_publicacion->format('d/m/Y') }}
                        </span>
                    @endif
                </div>

                <div class="licitacion-actions">
                    @if($licitacion->archivos->count())
                        @foreach($licitacion->archivos as $archivo)
                            <a href="{{ $archivo->ruta }}" target="_blank" class="btn btn-primary">
                                {{ $esBotones ? $archivo->nombre_original : ($licitacion->archivos->count() === 1 ? 'Ver PDF' : 'PDF ' . $loop->iteration) }}
                            </a>
                        @endforeach
                    @elseif($licitacion->link_externo)
                        <a href="{{ $licitacion->link_externo }}" target="_blank" class="btn btn-primary">
                            Abrir link
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="admin-empty">
                No hay publicaciones disponibles.
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
