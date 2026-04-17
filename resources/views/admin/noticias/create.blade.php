@extends('layouts.app')

@section('title', 'Nueva noticia')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Nueva noticia</h2>
            <p class="admin-subtitle">Crea una noticia manualmente para el portal.</p>
        </div>

        <a href="{{ route('admin.noticias.index') }}" class="btn btn-secondary">Volver</a>
    </section>

    <form action="{{ route('admin.noticias.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
        @csrf

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="titulo">Titulo</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required>
                @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="fecha">Fecha</label>
                <input
                    type="datetime-local"
                    name="fecha"
                    id="fecha"
                    value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}"
                    required
                >
                @error('fecha') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" required>
                    <option value="borrador" {{ old('estado', 'borrador') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="publicado" {{ old('estado') === 'publicado' ? 'selected' : '' }}>Publicado</option>
                </select>
                @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="imagen_destacada">Imagen destacada</label>
                <input type="file" name="imagen_destacada" id="imagen_destacada" accept=".jpg,.jpeg,.png,.webp">
                @error('imagen_destacada') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="contenido">Contenido</label>
                <textarea name="contenido" id="contenido" rows="12" required>{{ old('contenido') }}</textarea>
                @error('contenido') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar noticia</button>
    </form>
@endsection
