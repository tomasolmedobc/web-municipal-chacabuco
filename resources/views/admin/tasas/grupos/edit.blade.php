@extends('layouts.app')

@section('title', 'Editar grupo — ' . $grupo->nombre)

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar grupo</h2>
        <p class="admin-subtitle">Código: <strong>{{ $grupo->codigo }}</strong> (no editable)</p>
    </div>
    <a href="{{ route('admin.tasas.grupos.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.tasas.grupos.update', $grupo) }}" class="admin-form-card">
    @csrf @method('PUT')

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label class="campo-label" for="nombre">Nombre del grupo</label>
            <input type="text" name="nombre" id="nombre"
                   value="{{ old('nombre', $grupo->nombre) }}" required class="campo-input">
            @error('nombre') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="99"
                   value="{{ old('orden', $grupo->orden) }}" class="campo-input">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="estado">Visibilidad</label>
            <select name="estado" id="estado" class="campo-input">
                <option value="visible" {{ old('estado', $grupo->estado) === 'visible' ? 'selected' : '' }}>Visible</option>
                <option value="oculto"  {{ old('estado', $grupo->estado) === 'oculto'  ? 'selected' : '' }}>Oculto</option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>
@endsection
