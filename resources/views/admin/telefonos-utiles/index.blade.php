@extends('layouts.app')

@section('title', 'Teléfonos Útiles — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Teléfonos Útiles</h2>
        <p class="admin-subtitle">Administrá el directorio de contactos y organismos.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.telefonos-utiles.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nueva entrada
        </a>
        <a href="{{ route('telefonos-utiles.index') }}" class="btn btn-secondary" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver página pública
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            ← Panel
        </a>
    </div>
</section>

@if(session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

<form method="GET" action="{{ route('admin.telefonos-utiles.index') }}" class="filtros">
    <input type="text"
           name="q"
           value="{{ $q }}"
           placeholder="Buscar por nombre, teléfono, email, dirección…"
           class="filtro-input filtro-input-busqueda">

    <select name="categoria" class="filtro-input">
        <option value="">Todas las categorías</option>
        @foreach($categorias as $cat)
            <option value="{{ $cat }}" {{ $categoria === $cat ? 'selected' : '' }}>
                {{ $cat }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="boton-filtro">Filtrar</button>
    <a href="{{ route('admin.telefonos-utiles.index') }}" class="boton-limpiar">Limpiar</a>
</form>

<div class="admin-list">
    @forelse($telefonos as $item)
        <div class="admin-list-item">
            <div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <strong>{{ $item->nombre }}</strong>
                    @if($item->categoria)
                        <span class="badge-categoria">{{ $item->categoria }}</span>
                    @endif
                    @if($item->estado === 'oculto')
                        <span class="badge-estado badge-oculto">⚠ Oculto</span>
                    @endif
                </div>
                <div class="meta-noticia" style="margin-top:4px;">
                    @if($item->telefono)
                        <span><i class="fa-solid fa-phone"></i> {{ $item->telefono }}</span>
                    @endif
                    @if($item->email)
                        <span><i class="fa-solid fa-envelope"></i> {{ $item->email }}</span>
                    @endif
                    @if($item->direccion)
                        <span><i class="fa-solid fa-location-dot"></i> {{ $item->direccion }}</span>
                    @endif
                </div>
            </div>

            <div class="admin-actions">
                <a href="{{ route('admin.telefonos-utiles.edit', $item) }}" class="btn btn-secondary">
                    Editar
                </a>
                <form action="{{ route('admin.telefonos-utiles.destroy', $item) }}"
                      method="POST"
                      data-confirm="¿Eliminar &quot;{{ $item->nombre }}&quot;?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <div class="admin-list-item">
            <p>No se encontraron entradas.</p>
        </div>
    @endforelse
</div>

<div class="paginacion">
    {{ $telefonos->links('vendor.pagination.custom') }}
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
