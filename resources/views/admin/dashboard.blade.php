@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Panel de administración</h2>
            <p class="admin-subtitle">Gestioná noticias, buscá posteos y administrá su estado.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.noticias.create') }}" class="btn btn-primary">Nueva noticia</a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">Cerrar sesión</button>
            </form>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.dashboard') }}" class="filtros">
        <input
            type="text"
            name="q"
            value="{{ $busqueda ?? '' }}"
            placeholder="Buscar por título, contenido, slug o autor..."
            class="filtro-input filtro-input-busqueda"
        >

        <button type="submit" class="boton-filtro">Buscar</button>
        <a href="{{ route('admin.dashboard') }}" class="boton-limpiar">Limpiar</a>
    </form>

    @if(session('ok'))
        <div class="admin-alert success">
            {{ session('ok') }}
        </div>
    @endif

    @if(($busqueda ?? null) && $noticias->count() > 0)
        <p class="fecha" style="margin-bottom: 18px;">
            Resultados para: <strong>{{ $busqueda }}</strong>
        </p>
    @endif

    @if($noticias->count() === 0)
        <div class="admin-list-item">
            <div>
                <h3>No se encontraron noticias</h3>
                <p>Probá con otra búsqueda.</p>
            </div>
        </div>
    @endif

    <div class="admin-list">
        @foreach($noticias as $noticia)
            <article class="admin-list-item">
                <div>
                    <h3>{{ $noticia->titulo }}</h3>
                    <p>
                        {{ $noticia->fecha?->format('d/m/Y H:i') }}
                        ·
                        <strong>
                            {{ $noticia->estado === 'publicado' ? 'Publicado' : 'Oculto' }}
                        </strong>
                        @if($noticia->autor)
                            · {{ $noticia->autor }}
                        @endif
                    </p>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="btn btn-secondary">Ver</a>

                    <form action="{{ route('admin.noticias.toggleStatus', $noticia) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-secondary">
                            {{ $noticia->estado === 'publicado' ? 'Ocultar' : 'Publicar' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.noticias.edit', $noticia) }}" class="btn btn-secondary">Editar</a>

                    <form action="{{ route('admin.noticias.destroy', $noticia) }}" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar esta noticia?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Eliminar</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>

    <div class="paginacion">
        {{ $noticias->links('vendor.pagination.custom') }}
    </div>
@endsection