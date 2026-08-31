@extends('layouts.app')

@section('title', $cuota ? 'Editar cuota — ' . $cuota->cuota_label : 'Nueva cuota')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $cuota ? 'Editar cuota' : 'Nueva cuota' }}</h2>
        <p class="admin-subtitle">Fecha de vencimiento para un grupo de tasa.</p>
    </div>
    @if($cuota)
        <a href="{{ route('admin.tasas.grupos.show', $cuota->grupo_id) }}" class="btn btn-secondary">Volver</a>
    @else
        <a href="{{ route('admin.tasas.grupos.index') }}" class="btn btn-secondary">Volver</a>
    @endif
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ $cuota ? route('admin.tasas.cuotas.update', $cuota) : route('admin.tasas.cuotas.store') }}"
      class="admin-form-card">
    @csrf
    @if($cuota) @method('PUT') @endif
    <input type="hidden" name="orden" value="{{ $cuota?->orden ?? 0 }}">

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label class="campo-label" for="grupo_id">Grupo de tasa</label>
            <select name="grupo_id" id="grupo_id" required class="campo-input">
                <option value="">— Seleccioná un grupo —</option>
                @foreach($grupos as $g)
                    <option value="{{ $g->id }}"
                        {{ old('grupo_id', $cuota?->grupo_id) == $g->id ? 'selected' : '' }}>
                        {{ $g->nombre }}
                    </option>
                @endforeach
            </select>
            @error('grupo_id') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="cuota_label">Etiqueta de cuota</label>
            <input type="text" name="cuota_label" id="cuota_label"
                   value="{{ old('cuota_label', $cuota?->cuota_label) }}"
                   placeholder="Cuota 1/2026" required class="campo-input">
            @error('cuota_label') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="fecha_vencimiento">Fecha de vencimiento</label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                   value="{{ old('fecha_vencimiento', $cuota?->fecha_vencimiento?->format('Y-m-d')) }}"
                   required class="campo-input">
            @error('fecha_vencimiento') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="estado">Visibilidad</label>
            <select name="estado" id="estado" class="campo-input">
                <option value="visible" {{ old('estado', $cuota?->estado ?? 'visible') === 'visible' ? 'selected' : '' }}>Visible</option>
                <option value="oculto"  {{ old('estado', $cuota?->estado) === 'oculto' ? 'selected' : '' }}>Oculta</option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $cuota ? 'Guardar cambios' : 'Crear cuota' }}
    </button>
</form>
@endsection
