@extends('layouts.app')

@section('title', 'Procedimientos — Obras Particulares')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Procedimientos</h2>
        <p class="admin-subtitle">
            Acordeones de cada categoría en la página pública.<br>
            <small style="color:var(--text-muted);">Las categorías son dinámicas — podés agregar, editar o eliminar categorías desde el módulo de Categorías.</small>
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.obras.procedimientos.create', $categoriaActiva ? ['categoria' => $categoriaActiva->id] : []) }}"
           class="btn btn-primary">
            Nuevo procedimiento
        </a>
        <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($categorias->isEmpty())
    <div class="admin-list-item">
        <p>No hay categorías creadas. <a href="{{ route('admin.obras.categorias.create') }}">Crear la primera categoría</a>.</p>
    </div>
@else
    {{-- Tabs por categoría --}}
    <div class="ops-admin-tabs">
        @foreach($categorias as $cat)
            <a href="{{ route('admin.obras.procedimientos.index', ['categoria' => $cat->id]) }}"
               class="ops-admin-tab {{ $categoriaActiva && $categoriaActiva->id === $cat->id ? 'ops-admin-tab--active' : '' }}">
                {{ $cat->nombre }}
                <span class="ops-admin-tab__count">{{ $cat->procedimientos()->count() }}</span>
            </a>
        @endforeach
    </div>

    @if($procedimientos->isEmpty())
        <div class="admin-list-item">
            <p>No hay procedimientos en esta categoría.
                <a href="{{ route('admin.obras.procedimientos.create', $categoriaActiva ? ['categoria' => $categoriaActiva->id] : []) }}">
                    Crear el primero
                </a>.
            </p>
        </div>
    @else
        <div class="admin-list">
            @foreach($procedimientos as $proc)
                <article class="admin-list-item">
                    <div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            @if($proc->codigo)
                                <span class="badge-categoria">{{ strtoupper($proc->codigo) }}</span>
                            @endif
                            <h3 style="margin:0;">{{ $proc->titulo }}</h3>
                            @if(! $proc->visible)
                                <span class="badge-estado badge-oculto">Oculto</span>
                            @endif
                        </div>
                        <div class="meta-noticia">
                            <span>Orden: {{ $proc->orden }}</span>
                        </div>
                    </div>
                    <div class="admin-actions">
                        <a href="{{ route('admin.obras.procedimientos.edit', $proc) }}" class="btn btn-secondary">Editar</a>
                        <form action="{{ route('admin.obras.procedimientos.destroy', $proc) }}" method="POST"
                              data-confirm="¿Eliminar el procedimiento &quot;{{ $proc->titulo }}&quot;?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endif
@endsection
