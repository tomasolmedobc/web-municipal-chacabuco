@extends('layouts.app')

@section('title', ($modo === 'crear' ? 'Nueva entrada' : 'Editar: ' . $item->nombre) . ' — Teléfonos Útiles')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">
            {{ $modo === 'crear' ? 'Nueva entrada' : 'Editar: ' . $item->nombre }}
        </h2>
        <p class="admin-subtitle">Teléfonos Útiles</p>
    </div>
    <a href="{{ route('admin.telefonos-utiles.index') }}" class="btn btn-secondary">
        ← Volver
    </a>
</section>

@if($errors->any())
    <div class="admin-alert" style="background:#fef2f2; color:#991b1b; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $modo === 'crear' ? route('admin.telefonos-utiles.store') : route('admin.telefonos-utiles.update', $item) }}"
    method="POST"
    class="admin-form-card">

    @csrf
    @if($modo === 'editar') @method('PUT') @endif

    <div class="admin-form-grid">

        <div class="admin-form-group" style="grid-column: 1 / -1;">
            <label for="nombre">Nombre / Organismo <span class="req">*</span></label>
            <input type="text" id="nombre" name="nombre"
                   value="{{ old('nombre', $item->nombre) }}"
                   required maxlength="255">
        </div>

        <div class="admin-form-group">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">— Sin categoría —</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat }}" {{ old('categoria', $item->categoria) === $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="visible" {{ old('estado', $item->estado) === 'visible' ? 'selected' : '' }}>Visible</option>
                <option value="oculto"  {{ old('estado', $item->estado) === 'oculto'  ? 'selected' : '' }}>Oculto</option>
            </select>
        </div>

        <div class="admin-form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion"
                   value="{{ old('direccion', $item->direccion) }}"
                   maxlength="255"
                   placeholder="Ej: San Martín 121">
        </div>

        <div class="admin-form-group">
            <label for="telefono">Teléfono(s)</label>
            <input type="text" id="telefono" name="telefono"
                   value="{{ old('telefono', $item->telefono) }}"
                   maxlength="100"
                   placeholder="Ej: 470300 / 2352 123456">
        </div>

        <div class="admin-form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $item->email) }}"
                   maxlength="255"
                   placeholder="contacto@ejemplo.gob.ar">
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden dentro de la categoría</label>
            <input type="number" id="orden" name="orden"
                   value="{{ old('orden', $item->orden) }}"
                   min="0" max="9999">
        </div>

    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">
            {{ $modo === 'crear' ? 'Crear entrada' : 'Guardar cambios' }}
        </button>
        <a href="{{ route('admin.telefonos-utiles.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

@endsection
