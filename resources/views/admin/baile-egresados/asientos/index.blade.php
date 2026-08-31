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
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

{{-- ── Carga masiva ──────────────────────────────────────────────────── --}}
<details class="masivo-panel">
    <summary class="masivo-panel__toggle">
        <i class="fa-solid fa-layer-group"></i> Carga masiva de asientos
    </summary>

    <form method="POST" action="{{ route('admin.baile.asientos.masivo') }}" class="masivo-form">
        @csrf

        @if($errors->hasAny(['color','fila_desde','fila_hasta','num_desde','num_hasta']))
            <div class="alert-error" style="margin-bottom:14px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->only(['color','fila_desde','fila_hasta','num_desde','num_hasta']) as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="masivo-grid">
            <div class="admin-form-group">
                <label class="campo-label">Color / Sector</label>
                <input type="text" name="color" class="campo-input"
                       value="{{ old('color') }}" placeholder="Ej: Rojo" required>
            </div>

            <div class="admin-form-group">
                <label class="campo-label">Fila desde</label>
                <input type="text" name="fila_desde" class="campo-input"
                       value="{{ old('fila_desde') }}" placeholder="A" maxlength="1" required style="text-transform:uppercase;">
            </div>

            <div class="admin-form-group">
                <label class="campo-label">Fila hasta</label>
                <input type="text" name="fila_hasta" class="campo-input"
                       value="{{ old('fila_hasta') }}" placeholder="H" maxlength="1" required style="text-transform:uppercase;">
            </div>

            <div class="admin-form-group">
                <label class="campo-label">Número desde</label>
                <input type="number" name="num_desde" class="campo-input"
                       value="{{ old('num_desde', 1) }}" min="1" max="999" required>
            </div>

            <div class="admin-form-group">
                <label class="campo-label">Número hasta</label>
                <input type="number" name="num_hasta" class="campo-input"
                       value="{{ old('num_hasta') }}" min="1" max="999" required>
            </div>

            <div class="admin-form-group" style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Crear asientos</button>
            </div>
        </div>

        <p class="masivo-hint">
            Genera todos los asientos del bloque seleccionado. Los que ya existen se omiten sin error.
        </p>
    </form>
</details>

@if($asientos->count() === 0 && !request()->hasAny(['q','disponible']))
    <div class="admin-empty">
        <h3>No hay asientos cargados</h3>
        <p>Usá "Carga masiva" para cargar un bloque completo, o "Nuevo asiento" para agregar uno individual.</p>
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
