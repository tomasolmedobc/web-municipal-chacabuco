@extends('layouts.app')

@section('title', 'Reservas — Baile de Egresados')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Baile de Egresados — Reservas</h2>
        <p class="admin-subtitle">Listado de todas las reservas registradas.</p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.baile.usuarios.index') }}" class="btn btn-secondary">Usuarios</a>
        <a href="{{ route('admin.baile.asientos.index') }}" class="btn btn-secondary">Asientos</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

<div class="baile-stats" style="margin-bottom: 20px;">
    <div class="baile-stat">
        <span class="baile-stat__numero">{{ $totales['total'] }}</span>
        <span class="baile-stat__label">Total reservas</span>
    </div>
    <div class="baile-stat baile-stat--disponible">
        <span class="baile-stat__numero">{{ $totales['pagado'] }}</span>
        <span class="baile-stat__label">Pagadas</span>
    </div>
    <div class="baile-stat baile-stat--reservado">
        <span class="baile-stat__numero">{{ $totales['pendiente'] }}</span>
        <span class="baile-stat__label">Pendientes de pago</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.baile.reservas.index') }}" class="filtros">
    <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre o DNI">

    <select name="pago" class="filtro-input">
        <option value="">Todos los estados</option>
        <option value="1" @selected($filtroPago === '1')>Pagadas</option>
        <option value="0" @selected($filtroPago === '0')>Pendientes</option>
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>
    @if($busqueda || $filtroPago !== '')
        <a href="{{ route('admin.baile.reservas.index') }}" class="boton-limpiar">Limpiar</a>
    @endif
</form>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($reservas->count() === 0)
    <div class="admin-empty">
        <h3>No hay reservas registradas</h3>
    </div>
@endif

<div class="admin-list">
    @foreach($reservas as $reserva)
        <article class="admin-list-item">
            <div>
                <div class="hero-top" style="margin-bottom: 8px;">
                    @if($reserva->pago)
                        <span class="licitacion-badge badge-activa">Pagado</span>
                    @else
                        <span class="licitacion-badge badge-finalizada">Pendiente de pago</span>
                    @endif
                </div>

                <h3>{{ $reserva->usuario?->nombre_completo }}</h3>

                <div class="meta-noticia">
                    <span><i class="fa-solid fa-id-card"></i> DNI: {{ $reserva->usuario?->dni }}</span>
                    <span>
                        <i class="fa-solid fa-couch"></i>
                        {{ $reserva->lugar?->color }} — Fila {{ $reserva->lugar?->fila }}, N.º {{ $reserva->lugar?->numero }}
                    </span>
                    <span><i class="fa-solid fa-clock"></i> {{ $reserva->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="admin-actions">
                <form action="{{ route('admin.baile.reservas.pago', $reserva) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-secondary">
                        {{ $reserva->pago ? 'Marcar pendiente' : 'Marcar pagado' }}
                    </button>
                </form>

                <form action="{{ route('admin.baile.reservas.destroy', $reserva) }}"
                      method="POST"
                      class="form-eliminar-gobierno-abierto"
                      data-confirm="¿Eliminar esta reserva? El asiento quedará disponible nuevamente.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </article>
    @endforeach
</div>

@if($reservas->hasPages())
    <div class="paginacion">{{ $reservas->links('vendor.pagination.custom') }}</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
