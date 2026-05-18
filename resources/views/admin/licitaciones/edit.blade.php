@extends('layouts.app')

@section('title', 'Editar licitación')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar licitación</h2>
        <p class="admin-subtitle">
            Modificá los datos de la licitación seleccionada.
        </p>
    </div>

    <a href="{{ route('admin.licitaciones.index') }}" class="btn btn-secondary">
        Volver
    </a>
</section>

<form action="{{ route('admin.licitaciones.update', $licitacion) }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">

    @csrf
    @method('PUT')

    <div class="admin-form-grid">

        <div class="admin-form-group full">
            <label for="titulo">Título</label>
            <input
                type="text"
                name="titulo"
                id="titulo"
                value="{{ old('titulo', $licitacion->titulo) }}"
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
            >{{ old('descripcion', $licitacion->descripcion) }}</textarea>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo" required>
                <option value="publica" {{ old('tipo', $licitacion->tipo) === 'publica' ? 'selected' : '' }}>
                    Pública
                </option>
                <option value="privada" {{ old('tipo', $licitacion->tipo) === 'privada' ? 'selected' : '' }}>
                    Privada
                </option>
            </select>
            @error('tipo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="activa" {{ old('estado', $licitacion->estado) === 'activa' ? 'selected' : '' }}>
                    Activa
                </option>
                <option value="finalizada" {{ old('estado', $licitacion->estado) === 'finalizada' ? 'selected' : '' }}>
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
                value="{{ old('numero_expediente', $licitacion->numero_expediente) }}"
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
                value="{{ old('anio', $licitacion->anio ?? date('Y')) }}"
            >
            @error('anio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="fecha_publicacion">Fecha de publicación</label>
            <input
                type="date"
                name="fecha_publicacion"
                id="fecha_publicacion"
                value="{{ old('fecha_publicacion', $licitacion->fecha_publicacion?->format('Y-m-d')) }}"
            >
            @error('fecha_publicacion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivo_pdf">Reemplazar PDF</label>
            <input
                type="file"
                name="archivo_pdf"
                id="archivo_pdf"
                accept=".pdf"
            >
            <small class="fecha">Dejalo vacío si no querés cambiar el PDF actual.</small>
            @error('archivo_pdf') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($licitacion->archivo_ruta)
            <div class="admin-form-group full">
                <label>PDF actual</label>

                <div class="archivo-card">
                    <div class="archivo-card__main">
                        <i class="fa-solid fa-file-pdf archivo-icono"></i>

                        <div class="archivo-info">
                            <span class="archivo-nombre">
                                {{ $licitacion->archivo_nombre ?? 'Documento PDF' }}
                            </span>

                            <span class="archivo-meta">
                                PDF
                                @if($licitacion->archivo_peso)
                                    · {{ number_format($licitacion->archivo_peso / 1024, 1) }} KB
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="archivo-card__actions">
                        <a href="{{ $licitacion->archivo_ruta }}"
                           target="_blank"
                           class="btn btn-secondary">
                            Ver PDF
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <button type="submit" class="btn btn-primary">
        Actualizar licitación
    </button>
</form>

@endsection