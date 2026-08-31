@extends('layouts.app')

@php $esEdicion = $modo === 'editar'; @endphp

@section('title', $esEdicion ? 'Editar categoría' : 'Nueva categoría')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $esEdicion ? 'Editar categoría' : 'Nueva categoría' }}</h2>
        <p class="admin-subtitle">Las categorías se muestran como secciones en la página pública de Obras Particulares.</p>
    </div>
    <a href="{{ route('admin.obras.categorias.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0;padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<form action="{{ $esEdicion ? route('admin.obras.categorias.update', $categoria) : route('admin.obras.categorias.store') }}"
      method="POST" class="admin-form-card">
    @csrf
    @if($esEdicion) @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label class="campo-label" for="nombre">Nombre de la categoría</label>
            <input type="text" name="nombre" id="nombre"
                   value="{{ old('nombre', $categoria->nombre ?? '') }}"
                   placeholder="Ej: Balcones Gastronómicos" required class="campo-input">
            <small class="fecha">Se muestra como título de sección en la página pública.</small>
            @error('nombre') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label class="campo-label" for="descripcion">Descripción breve (opcional)</label>
            <input type="text" name="descripcion" id="descripcion"
                   value="{{ old('descripcion', $categoria->descripcion ?? '') }}"
                   placeholder="Ej: Documentación y recorrido del expediente."
                   maxlength="500" class="campo-input">
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999"
                   value="{{ old('orden', $categoria->orden ?? 0) }}" class="campo-input">
            <small class="fecha">Número menor aparece antes. Usá 0 para que quede al principio.</small>
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="visible">Visibilidad</label>
            <select name="visible" id="visible" class="campo-input">
                <option value="1" {{ old('visible', $categoria->visible ?? true) ? 'selected' : '' }}>Visible</option>
                <option value="0" {{ ! old('visible', $categoria->visible ?? true) ? 'selected' : '' }}>Oculta</option>
            </select>
            @error('visible') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $esEdicion ? 'Guardar cambios' : 'Crear categoría' }}
    </button>
</form>
@endsection
