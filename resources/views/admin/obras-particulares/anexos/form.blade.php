@extends('layouts.app')

@php $esEdicion = $modo === 'editar'; @endphp

@section('title', $esEdicion ? 'Editar formulario' : 'Nuevo formulario')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $esEdicion ? 'Editar formulario' : 'Nuevo formulario' }}</h2>
        <p class="admin-subtitle">Formularios / Anexos descargables referenciados en los procedimientos.</p>
    </div>
    <a href="{{ route('admin.obras.anexos.index') }}" class="btn btn-secondary">Volver</a>
</section>

<form action="{{ $esEdicion ? route('admin.obras.anexos.update', $anexo) : route('admin.obras.anexos.store') }}"
      method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @if($esEdicion) @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="nombre">Nombre del formulario</label>
            <input type="text" name="nombre" id="nombre"
                   value="{{ old('nombre', $anexo->nombre ?? '') }}"
                   placeholder="Ej: Anexo 1 — Solicitud de obra" required>
            <small class="fecha">Este nombre aparece en la página pública junto al botón de descarga.</small>
            @error('nombre') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción breve (opcional)</label>
            <input type="text" name="descripcion" id="descripcion"
                   value="{{ old('descripcion', $anexo->descripcion ?? '') }}"
                   placeholder="Ej: Nota de solicitud dirigida al intendente" maxlength="500">
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999"
                   value="{{ old('orden', $anexo->orden ?? 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivo">Archivo PDF</label>
            <input type="file" name="archivo" id="archivo"
                   accept=".pdf,.doc,.docx"
                   {{ $esEdicion ? '' : 'required' }}>
            <small class="fecha">PDF, DOC o DOCX. Máximo 20 MB.</small>
            @error('archivo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $anexo->archivo_ruta)
            <div class="admin-form-group full">
                <label>Archivo actual</label>
                <div class="archivo-card">
                    <div class="archivo-card__main">
                        <i class="fa-solid fa-file-pdf archivo-icono"></i>
                        <div class="archivo-info">
                            <span class="archivo-nombre">{{ $anexo->archivo_nombre }}</span>
                            <span class="archivo-meta">{{ $anexo->archivo_peso_legible }}</span>
                        </div>
                    </div>
                    <div class="archivo-card__actions">
                        <a href="{{ $anexo->archivo_ruta }}" target="_blank" class="btn btn-secondary">Ver</a>
                    </div>
                </div>
                <small class="fecha">URL pública: <code>{{ $anexo->archivo_ruta }}</code></small>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $esEdicion ? 'Actualizar' : 'Guardar' }}
    </button>
</form>
@endsection
