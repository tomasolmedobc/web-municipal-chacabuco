@extends('layouts.app')

@section('title', 'Categorías — Obras Particulares')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Categorías</h2>
        <p class="admin-subtitle">Secciones de la página pública de Obras Particulares. Cada categoría agrupa procedimientos y normativas.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.obras.categorias.create') }}" class="btn btn-primary">Nueva categoría</a>
        <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="ops-admin-tip" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-info"></i>
    <p>
        Cada categoría se muestra como una sección en la página pública con su propia área de normativas y sus acordeones de procedimientos.
        Eliminar una categoría también elimina todos sus procedimientos y normativas asociados.
    </p>
</div>

@if($categorias->isEmpty())
    <div class="admin-list-item">
        <p>No hay categorías creadas. <a href="{{ route('admin.obras.categorias.create') }}">Crear la primera</a>.</p>
    </div>
@else
    <div class="admin-list">
        @foreach($categorias as $categoria)
            <article class="admin-list-item">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <h3 style="margin:0;">{{ $categoria->nombre }}</h3>
                        <span class="badge-estado {{ $categoria->visible ? 'badge-publicado' : 'badge-oculto' }}">
                            {{ $categoria->visible ? 'Visible' : 'Oculta' }}
                        </span>
                    </div>
                    @if($categoria->descripcion)
                        <p class="admin-subtitle" style="margin-top:4px;">{{ $categoria->descripcion }}</p>
                    @endif
                    <div class="meta-noticia">
                        <span>Orden: {{ $categoria->orden }}</span>
                        <span>
                            <i class="fa-solid fa-list-check"></i>
                            {{ $categoria->procedimientos_count }} {{ $categoria->procedimientos_count === 1 ? 'procedimiento' : 'procedimientos' }}
                        </span>
                        <span>
                            <i class="fa-solid fa-scale-balanced"></i>
                            {{ $categoria->normativas_count }} {{ $categoria->normativas_count === 1 ? 'normativa' : 'normativas' }}
                        </span>
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('admin.obras.procedimientos.index', ['categoria' => $categoria->id]) }}"
                       class="btn btn-secondary">
                        Procedimientos
                    </a>
                    <a href="{{ route('admin.obras.normativas.index', ['categoria' => $categoria->id]) }}"
                       class="btn btn-secondary">
                        Normativas
                    </a>
                    <a href="{{ route('admin.obras.categorias.edit', $categoria) }}" class="btn btn-secondary">
                        Editar
                    </a>
                    <form action="{{ route('admin.obras.categorias.destroy', $categoria) }}" method="POST"
                          data-confirm="¿Eliminar &quot;{{ $categoria->nombre }}&quot;? También se eliminarán sus {{ $categoria->procedimientos_count }} procedimientos y {{ $categoria->normativas_count }} normativas.">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
