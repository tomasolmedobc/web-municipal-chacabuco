@extends('layouts.app')

@section('title', 'Nueva localidad')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Nueva localidad</h2>
        <p class="admin-subtitle">Creá una nueva localidad para el módulo de turismo.</p>
    </div>

    <a href="{{ route('admin.turismo.localidades.index') }}" class="btn btn-secondary">
        Volver
    </a>
</section>

<form action="{{ route('admin.turismo.localidades.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">
    @csrf

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required>
            <small class="fecha">El slug se genera automáticamente a partir del nombre.</small>
            @error('nombre') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999" value="{{ old('orden', 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            <small class="fecha">Resumen breve usado en las tarjetas de la página de Turismo.</small>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="historia">Historia</label>
            <textarea name="historia" id="historia" rows="8">{{ old('historia') }}</textarea>
            @error('historia') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="visible" {{ old('estado', 'visible') === 'visible' ? 'selected' : '' }}>Visible</option>
                <option value="oculto"  {{ old('estado') === 'oculto' ? 'selected' : '' }}>Oculto</option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="imagen_portada">Imagen de portada</label>
            <input type="file" name="imagen_portada" id="imagen_portada" accept=".jpg,.jpeg,.png,.webp">
            <small class="fecha">Formatos: JPG, PNG, WEBP. Máximo 4MB.</small>
            @error('imagen_portada') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Crear localidad</button>
</form>
@endsection

@push('scripts_head')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
@endpush

@push('scripts')
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;
    tinymce.init({
        selector: '#historia',
        height: 380,
        menubar: false,
        plugins: 'lists link code wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link | code',
        language: 'es',
        language_url: '/js/tinymce/langs/es.js',
        branding: false,
        setup: (editor) => {
            editor.on('change keyup', () => tinymce.triggerSave());
        }
    });
});
</script>
@endpush
