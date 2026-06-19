@extends('layouts.app')

@section('title', ($modo === 'crear' ? 'Nuevo asiento' : 'Editar asiento') . ' — Baile de Egresados')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $modo === 'crear' ? 'Nuevo asiento' : 'Editar asiento' }}</h2>
        <p class="admin-subtitle">Color, fila y número del asiento en el salón.</p>
    </div>
    <a href="{{ route('admin.baile.asientos.index') }}" class="btn btn-secondary">Volver</a>
</section>

<form action="{{ $modo === 'crear' ? route('admin.baile.asientos.store') : route('admin.baile.asientos.update', $asiento) }}"
      method="POST"
      class="admin-form-card">
    @csrf
    @if($modo === 'editar') @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="color">Color / Sector</label>
            <input type="text" name="color" id="color"
                   value="{{ old('color', $asiento->color) }}"
                   placeholder="Ej: Rojo, Azul, Verde"
                   required>
            @error('color') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="fila">Fila</label>
            <input type="text" name="fila" id="fila"
                   value="{{ old('fila', $asiento->fila) }}"
                   placeholder="Ej: A, B, C"
                   maxlength="10" required>
            @error('fila') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="numero">Número de asiento</label>
            <input type="number" name="numero" id="numero"
                   value="{{ old('numero', $asiento->numero) }}"
                   min="1" max="999" required>
            @error('numero') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="disponible" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="disponible" id="disponible" value="1"
                       {{ old('disponible', $asiento->disponible ?? true) ? 'checked' : '' }}
                       style="width:auto;">
                Disponible para reserva
            </label>
            @error('disponible') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $modo === 'crear' ? 'Guardar' : 'Actualizar' }}
    </button>
</form>
@endsection
