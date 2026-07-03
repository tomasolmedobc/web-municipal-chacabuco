@extends('layouts.app')

@section('title', 'Normativas — Obras Particulares')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Normativas</h2>
        <p class="admin-subtitle">Ordenanzas y leyes vinculadas a Obras Particulares y Balcones Gastronómicos.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.obras.normativas.create', ['seccion' => $seccionActiva]) }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nueva normativa
        </a>
        <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

{{-- Tabs sección --}}
<div class="admin-tabs">
    @foreach($secciones as $sec => $label)
        <a href="{{ route('admin.obras.normativas.index', ['seccion' => $sec]) }}"
           class="admin-tab {{ $seccionActiva === $sec ? 'admin-tab--active' : '' }}">
            {{ $label }}
            <span class="admin-tab__count">{{ $totales[$sec] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<form method="GET" action="{{ route('admin.obras.normativas.index') }}" class="filtros">
    <input type="hidden" name="seccion" value="{{ $seccionActiva }}">
    <input type="text" name="q" value="{{ $busqueda ?? '' }}" placeholder="Buscar por nombre..." class="filtro-input filtro-input-busqueda">
    <button type="submit" class="boton-filtro">Buscar</button>
    <a href="{{ route('admin.obras.normativas.index', ['seccion' => $seccionActiva]) }}" class="boton-limpiar">Limpiar</a>
</form>

@if($normativas->isEmpty())
    <div class="admin-list-item">
        <p>No hay normativas en esta sección. <a href="{{ route('admin.obras.normativas.create', ['seccion' => $seccionActiva]) }}">Crear la primera</a>.</p>
    </div>
@else
    <div class="admin-list">
        @foreach($normativas as $normativa)
            <article class="admin-list-item">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <h3 style="margin:0;">{{ $normativa->nombre }}</h3>
                        <span class="badge-estado {{ $normativa->visible ? 'badge-publicado' : 'badge-oculto' }}">
                            {{ $normativa->visible ? 'Visible' : 'Oculta' }}
                        </span>
                    </div>
                    <div class="meta-noticia">
                        <span>Orden: {{ $normativa->orden }}</span>
                        @if($normativa->archivo_nombre)
                            <span><i class="fa-solid fa-file-pdf fa-xs"></i> {{ $normativa->archivo_nombre }}</span>
                        @endif
                    </div>
                </div>
                <div class="admin-actions">
                    @if($normativa->archivo_ruta)
                        <a href="{{ $normativa->archivo_ruta }}" target="_blank" class="btn btn-secondary">Ver</a>
                    @endif
                    <a href="{{ route('admin.obras.normativas.edit', $normativa) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('admin.obras.normativas.destroy', $normativa) }}" method="POST"
                          data-confirm="¿Eliminar esta normativa?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Eliminar</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="paginacion">{{ $normativas->links('vendor.pagination.custom') }}</div>
@endif
@endsection
