@extends('layouts.app')

@section('title', 'Nueva licitación')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Nueva licitación</h2>
        <p class="admin-subtitle">
            Cargá una licitación pública o privada para Gobierno Abierto.
        </p>
    </div>

    <a href="{{ route('admin.licitaciones.index') }}" class="btn btn-secondary">
        Volver
    </a>
</section>

<form action="{{ route('admin.licitaciones.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">

    @csrf

    <div class="admin-form-grid">

        <div class="admin-form-group full">
            <label for="titulo">Título</label>
            <input
                type="text"
                name="titulo"
                id="titulo"
                value="{{ old('titulo') }}"
                required
            >
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción</label>
            <textarea
                name="descripcion"
                id="descripcion"
                rows="4"
            >{{ old('descripcion') }}</textarea>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo" required>
                <option value="publica" {{ old('tipo', 'publica') === 'publica' ? 'selected' : '' }}>
                    Pública
                </option>
                <option value="privada" {{ old('tipo') === 'privada' ? 'selected' : '' }}>
                    Privada
                </option>
            </select>
            @error('tipo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="activa" {{ old('estado', 'activa') === 'activa' ? 'selected' : '' }}>
                    Activa
                </option>
                <option value="finalizada" {{ old('estado') === 'finalizada' ? 'selected' : '' }}>
                    Finalizada
                </option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="numero_expediente">Número de expediente</label>
            <input
                type="text"
                name="numero_expediente"
                id="numero_expediente"
                value="{{ old('numero_expediente') }}"
                placeholder="Ej: 4029-1234/2026"
            >
            @error('numero_expediente') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="anio">Año</label>
            <input
                type="number"
                name="anio"
                id="anio"
                min="2000"
                max="{{ date('Y') + 1 }}"
                value="{{ old('anio', date('Y')) }}"
            >
            @error('anio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="fecha_publicacion">Fecha de publicación</label>
            <input
                type="date"
                name="fecha_publicacion"
                id="fecha_publicacion"
                value="{{ old('fecha_publicacion', now()->format('Y-m-d')) }}"
            >
            @error('fecha_publicacion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivo_pdf">Archivo PDF</label>
            <input
                type="file"
                name="archivo_pdf"
                id="archivo_pdf"
                accept=".pdf"
            >
            <small class="fecha">Máximo 10MB. Solo archivos PDF.</small>
            @error('archivo_pdf') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        Guardar licitación
    </button>
</form>

@endsection