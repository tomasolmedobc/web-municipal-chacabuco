@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
<div class="admin-stats">
    <div class="admin-stat-card">
        <h3>Total noticias</h3>
        <p>{{ $stats['noticias_total'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Publicadas</h3>
        <p>{{ $stats['noticias_publicadas'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Ocultas</h3>
        <p>{{ $stats['noticias_ocultas'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Usuarios</h3>
        <p>{{ $stats['usuarios_total'] }}</p>
    </div>
</div>

    <section class="admin-header">
        
        <div>
            <h2 class="seccion-titulo">Panel de administración</h2>
            <p class="admin-subtitle">Gestioná noticias, buscá posteos y administrá su estado.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.noticias.create') }}" class="btn btn-primary">
                Nueva noticia
            </a>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('ok')), 'success');
            });
        </script>
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
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <h3 style="margin:0;">{{ $noticia->titulo }}</h3>

                @if($noticia->categoria)
                    <span class="badge-categoria">
                        {{ $noticia->categoria->nombre }}
                    </span>
                @endif
            </div>
                    <div class="meta-noticia">
                        <span>{{ $noticia->fecha?->format('d/m/Y H:i') }}</span>

                        <span class="badge-estado {{ $noticia->estado === 'publicado' ? 'badge-publicado' : 'badge-oculto' }}">
                            {{ $noticia->estado === 'publicado' ? '✔ Publicado' : '⚠ Oculto' }}
                        </span>

                        @if($noticia->autor)
                            <span>{{ $noticia->autor }}</span>
                        @endif
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="btn btn-secondary">Ver</a>

                    <form action="{{ route('admin.noticias.toggleStatus', $noticia) }}" method="POST" class="form-toggle-estado">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="btn {{ $noticia->estado === 'publicado' ? 'btn-estado-ocultar' : 'btn-estado-publicar' }}"
                        >
                            {{ $noticia->estado === 'publicado' ? 'Ocultar' : 'Publicar' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.noticias.edit', $noticia) }}" class="btn btn-secondary">Editar</a>

                    <form action="{{ route('admin.noticias.destroy', $noticia) }}" method="POST" class="form-eliminar-noticia" data-confirm="¿Seguro que querés eliminar esta noticia?">
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

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush