@extends('layouts.app')

@section('title', 'Noticias — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Noticias</h2>
        <p class="admin-subtitle">Administrá las noticias del sitio.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.noticias.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nueva noticia
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Panel</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<form method="GET" action="{{ route('admin.noticias.index') }}" class="filtros">
    <input type="text"
           name="q"
           value="{{ $busqueda }}"
           placeholder="Buscar por título, autor, slug…"
           class="filtro-input filtro-input-busqueda">

    <select name="estado" class="filtro-input">
        <option value="">Todos los estados</option>
        <option value="publicado" {{ $estado === 'publicado' ? 'selected' : '' }}>Publicado</option>
        <option value="oculto"    {{ $estado === 'oculto'    ? 'selected' : '' }}>Oculto</option>
    </select>

    <select name="categoria_id" class="filtro-input">
        <option value="">Todas las categorías</option>
        @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" {{ (string)$categoriaId === (string)$cat->id ? 'selected' : '' }}>
                {{ $cat->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="boton-filtro">Filtrar</button>
    @if($busqueda || $estado || $categoriaId)
        <a href="{{ route('admin.noticias.index') }}" class="boton-limpiar">Limpiar</a>
    @endif
</form>

@if($noticias->isEmpty())
    <div class="admin-list-item">
        <p>No se encontraron noticias{{ $busqueda ? ' para "' . e($busqueda) . '"' : '' }}.</p>
    </div>
@endif

<div class="admin-list">
    @foreach($noticias as $noticia)
        <article class="admin-list-item">
            <div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <h3 style="margin:0;">{{ $noticia->titulo }}</h3>
                    @if($noticia->categorias->count())
                        <span class="badge-categoria">{{ $noticia->categorias->first()->nombre }}</span>
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
                <a href="{{ route('noticias.show', $noticia->slug) }}" class="btn btn-secondary" target="_blank">Ver</a>

                <form action="{{ route('admin.noticias.toggleStatus', $noticia) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="btn {{ $noticia->estado === 'publicado' ? 'btn-estado-ocultar' : 'btn-estado-publicar' }}">
                        {{ $noticia->estado === 'publicado' ? 'Ocultar' : 'Publicar' }}
                    </button>
                </form>

                <a href="{{ route('admin.noticias.edit', $noticia) }}" class="btn btn-secondary">Editar</a>

                <form action="{{ route('admin.noticias.destroy', $noticia) }}" method="POST"
                      data-confirm="¿Eliminar «{{ $noticia->titulo }}»?">
                    @csrf @method('DELETE')
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
