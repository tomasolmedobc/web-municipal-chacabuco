@extends('layouts.app')

@section('title', 'Fechas de vencimiento — Tasas')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Fechas de vencimiento</h2>
        <p class="admin-subtitle">Cuotas y fechas de vencimiento por grupo de tasa.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.tasas.cuotas.create') }}" class="btn btn-primary">Nueva cuota</a>
        <a href="{{ route('admin.tasas.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<form method="GET" action="{{ route('admin.tasas.cuotas.index') }}" class="filtros">
    <select id="grupo-filter" name="grupo_id" class="filtro-input">
        <option value="">Todos los grupos</option>
        @foreach($grupos as $g)
            <option value="{{ $g->id }}" {{ (string)$grupoId === (string)$g->id ? 'selected' : '' }}>
                {{ $g->nombre }}
            </option>
        @endforeach
    </select>
    <a href="{{ route('admin.tasas.cuotas.index') }}" class="boton-limpiar">Ver todos</a>
</form>

@if($cuotas->isEmpty())
    <div class="admin-list-item">
        <div><h3>No hay cuotas cargadas</h3><p>Usá "Nueva cuota" para agregar fechas de vencimiento.</p></div>
    </div>
@else
<div class="admin-list">
    @foreach($cuotas as $cuota)
    <div class="admin-list-item">
        <div>
            <h3 style="margin:0 0 4px;">{{ $cuota->cuota_label }}</h3>
            <div class="meta-noticia">
                <span>{{ $cuota->grupo->nombre }}</span>
                <span>Vence: <strong>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</strong></span>
                <span>Orden: {{ $cuota->orden }}</span>
                <span class="badge-estado {{ $cuota->estado === 'visible' ? 'badge-publicado' : 'badge-oculto' }}">
                    {{ $cuota->estado === 'visible' ? 'Visible' : 'Oculta' }}
                </span>
            </div>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.tasas.cuotas.edit', $cuota) }}" class="btn btn-secondary">Editar</a>
            <form action="{{ route('admin.tasas.cuotas.destroy', $cuota) }}" method="POST"
                  data-confirm="¿Eliminar la cuota «{{ $cuota->cuota_label }}»?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-secondary">Eliminar</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div class="paginacion">{{ $cuotas->links('vendor.pagination.custom') }}</div>
@endif
@endsection

@push('scripts')
<script @nonce>
document.getElementById('grupo-filter').addEventListener('change', function () {
    this.form.submit();
});
</script>
@endpush
