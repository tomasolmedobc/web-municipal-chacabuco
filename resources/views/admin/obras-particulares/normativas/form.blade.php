@extends('layouts.app')

@php $esEdicion = $modo === 'editar'; @endphp

@section('title', $esEdicion ? 'Editar normativa' : 'Nueva normativa')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $esEdicion ? 'Editar normativa' : 'Nueva normativa' }}</h2>
        <p class="admin-subtitle">{{ \App\Models\ObraNormativa::SECCIONES[$seccionActiva] ?? '' }}</p>
    </div>
    <a href="{{ route('admin.obras.normativas.index', ['seccion' => $seccionActiva]) }}" class="btn btn-secondary">Volver</a>
</section>

<form action="{{ $esEdicion ? route('admin.obras.normativas.update', $normativa) : route('admin.obras.normativas.store') }}"
      method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @if($esEdicion) @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="seccion">Sección</label>
            <select name="seccion" id="seccion" required>
                @foreach($secciones as $sec => $label)
                    <option value="{{ $sec }}" {{ old('seccion', $normativa->seccion ?? $seccionActiva) === $sec ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('seccion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="nombre">Nombre del documento</label>
            <input type="text" name="nombre" id="nombre"
                   value="{{ old('nombre', $normativa->nombre ?? '') }}"
                   placeholder="Ej: Ordenanza de edificación" required>
            @error('nombre') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999"
                   value="{{ old('orden', $normativa->orden ?? 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="visible">Visibilidad</label>
            <select name="visible" id="visible">
                <option value="1" {{ old('visible', $normativa->visible ?? true) ? 'selected' : '' }}>Visible</option>
                <option value="0" {{ ! old('visible', $normativa->visible ?? true) ? 'selected' : '' }}>Oculta</option>
            </select>
            @error('visible') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivo">Archivo PDF</label>
            <input type="file" name="archivo" id="archivo"
                   accept=".pdf,.doc,.docx"
                   {{ $esEdicion ? '' : 'required' }}>
            <small class="fecha">PDF, DOC o DOCX. Máximo 20 MB.</small>
            @error('archivo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $normativa->archivo_ruta)
            <div class="admin-form-group full">
                <label>Archivo actual</label>
                <div class="archivo-card">
                    <div class="archivo-card__main">
                        <i class="fa-solid fa-file-pdf archivo-icono"></i>
                        <div class="archivo-info">
                            <span class="archivo-nombre">{{ $normativa->archivo_nombre }}</span>
                            <span class="archivo-meta">{{ $normativa->archivo_peso_legible }}</span>
                        </div>
                    </div>
                    <div class="archivo-card__actions">
                        <a href="{{ $normativa->archivo_ruta }}" target="_blank" class="btn btn-secondary">Ver</a>
                    </div>
                </div>
                <small class="fecha">Subir un nuevo archivo reemplaza al actual.</small>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $esEdicion ? 'Actualizar' : 'Guardar' }}
    </button>
</form>
@endsection
