@extends('layouts.app')

@section('title', $grupo->nombre . ' — Vencimientos')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $grupo->nombre }}</h2>
        <p class="admin-subtitle">
            Código: <strong>{{ $grupo->codigo }}</strong> &nbsp;·&nbsp;
            <span class="badge-estado {{ $grupo->estado === 'visible' ? 'badge-publicado' : 'badge-oculto' }}">
                {{ $grupo->estado === 'visible' ? 'Visible' : 'Oculto' }}
            </span>
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.tasas.grupos.edit', $grupo) }}" class="btn btn-secondary">Editar grupo</a>
        <a href="{{ route('admin.tasas.grupos.index') }}" class="btn btn-secondary">← Grupos</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($errors->any())
    <div class="alert-error" style="margin-bottom:16px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- ─── Tabla de cuotas ─────────────────────────────────────────────── --}}
<div class="admin-form-card" style="padding:0; overflow:hidden;">
    <table class="cuotas-tabla">
        <thead>
            <tr>
                <th>Etiqueta</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grupo->cuotas as $cuota)
            <tr>
                <td>{{ $cuota->cuota_label }}</td>
                <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                <td>
                    <span class="badge-estado {{ $cuota->estado === 'visible' ? 'badge-publicado' : 'badge-oculto' }}">
                        {{ $cuota->estado === 'visible' ? 'Visible' : 'Oculta' }}
                    </span>
                </td>
                <td style="text-align:right; white-space:nowrap;">
                    <a href="{{ route('admin.tasas.cuotas.edit', $cuota) }}" class="btn btn-secondary" style="padding:4px 12px; font-size:.85rem;">Editar</a>
                    <form action="{{ route('admin.tasas.cuotas.destroy', $cuota) }}" method="POST"
                          style="display:inline;"
                          data-confirm="¿Eliminar la cuota «{{ $cuota->cuota_label }}»?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="padding:4px 12px; font-size:.85rem;">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; color:#888; padding:24px 0;">
                    Todavía no hay vencimientos cargados para este grupo.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ─── Fila de carga rápida ──────────────────────────────────────── --}}
    <form method="POST" action="{{ route('admin.tasas.cuotas.store') }}" class="cuotas-quickadd">
        @csrf
        <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
        <input type="hidden" name="_from_grupo" value="1">

        <input type="text"
               name="cuota_label"
               class="campo-input"
               placeholder="Etiqueta (ej: Cuota 1/{{ date('Y') }})"
               value="{{ old('cuota_label') }}"
               style="flex:1; min-width:160px;">

        <input type="date"
               name="fecha_vencimiento"
               class="campo-input"
               value="{{ old('fecha_vencimiento') }}"
               required
               style="width:160px;">

        <select name="estado" class="campo-input" style="width:120px;">
            <option value="visible" {{ old('estado', 'visible') === 'visible' ? 'selected' : '' }}>Visible</option>
            <option value="oculto"  {{ old('estado') === 'oculto' ? 'selected' : '' }}>Oculta</option>
        </select>

        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
            + Agregar
        </button>
    </form>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    // Pre-sugiere etiqueta cuando el usuario elige fecha
    var fechaInput = document.querySelector('input[name="fecha_vencimiento"]');
    var labelInput = document.querySelector('input[name="cuota_label"]');
    if (!fechaInput || !labelInput) return;

    fechaInput.addEventListener('change', function () {
        if (labelInput.value.trim()) return; // no pisar si ya escribió algo
        var d = new Date(this.value + 'T12:00:00');
        if (isNaN(d)) return;
        labelInput.placeholder = 'Cuota /' + d.getFullYear();
    });
})();
</script>
@endpush
