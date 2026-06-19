@extends('layouts.app')

@section('title', 'Asientos — Baile de Egresados')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Baile de Egresados — Asientos</h2>
        <p class="admin-subtitle">Gestioná los asientos del salón.</p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.baile.asientos.create') }}" class="btn btn-primary">Nuevo asiento</a>
        <a href="{{ route('admin.baile.usuarios.index') }}" class="btn btn-secondary">Usuarios</a>
        <a href="{{ route('admin.baile.reservas.index') }}" class="btn btn-secondary">Reservas</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

<div class="baile-stats" style="margin-bottom: 20px;">
    <div class="baile-stat">
        <span class="baile-stat__numero">{{ $totales['total'] }}</span>
        <span class="baile-stat__label">Total</span>
    </div>
    <div class="baile-stat baile-stat--disponible">
        <span class="baile-stat__numero">{{ $totales['disponible'] }}</span>
        <span class="baile-stat__label">Disponibles</span>
    </div>
    <div class="baile-stat baile-stat--reservado">
        <span class="baile-stat__numero">{{ $totales['reservado'] }}</span>
        <span class="baile-stat__label">Reservados</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.baile.asientos.index') }}" class="filtros">
    <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por color o fila">

    <select name="disponible" class="filtro-input">
        <option value="">Todos los estados</option>
        <option value="1" @selected($filtro === '1')>Disponibles</option>
        <option value="0" @selected($filtro === '0')>Reservados</option>
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>
    @if($busqueda || $filtro !== '')
        <a href="{{ route('admin.baile.asientos.index') }}" class="boton-limpiar">Limpiar</a>
    @endif
</form>

@if(session('ok'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($asientos->count() === 0)
    <div class="admin-empty">
        <h3>No hay asientos cargados</h3>
        <p>Creá el primero desde el botón superior.</p>
    </div>
@endif

<div class="admin-list">
    @foreach($asientos as $asiento)
        <article class="admin-list-item">
            <div>
                <div class="hero-top" style="margin-bottom: 8px;">
                    <span class="licitacion-badge {{ $asiento->disponible ? 'badge-activa' : 'badge-finalizada' }}">
                        {{ $asiento->disponible ? 'Disponible' : 'Reservado' }}
                    </span>
                </div>
                <h3>
                    <i class="fa-solid fa-couch"></i>
                    {{ $asiento->color }} — Fila {{ $asiento->fila }}, N.º {{ $asiento->numero }}
                </h3>
            </div>

            <div class="admin-actions">
                <a href="{{ route('admin.baile.asientos.edit', $asiento) }}" class="btn btn-secondary">Editar</a>

                <form action="{{ route('admin.baile.asientos.destroy', $asiento) }}"
                      method="POST"
                      class="form-eliminar-gobierno-abierto"
                      data-confirm="¿Eliminar el asiento {{ $asiento->descripcion }}?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </article>
    @endforeach
</div>

@if($asientos->hasPages())
    <div class="paginacion">{{ $asientos->links('vendor.pagination.custom') }}</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
