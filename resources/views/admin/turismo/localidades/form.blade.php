@extends('layouts.app')

@section('title', 'Editar ' . $localidad->nombre)

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar {{ $localidad->nombre }}</h2>
        <p class="admin-subtitle">Actualizá la historia, descripción e imagen de portada de esta localidad.</p>
    </div>

    <a href="{{ route('admin.turismo.localidades.index') }}" class="btn btn-secondary">
        Volver
    </a>
</section>

<form action="{{ route('admin.turismo.localidades.update', $localidad) }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">
    @csrf
    @method('PUT')

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label>Nombre</label>
            <input type="text" value="{{ $localidad->nombre }}" disabled>
        </div>

        <div class="admin-form-group">
            <label>Slug</label>
            <input type="text" value="{{ $localidad->slug }}" disabled>
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3">{{ old('descripcion', $localidad->descripcion) }}</textarea>
            <small class="fecha">Resumen breve usado en las tarjetas de la página de Turismo.</small>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="historia">Historia</label>
            <textarea name="historia" id="historia" rows="8">{{ old('historia', $localidad->historia) }}</textarea>
            @error('historia') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="visible" {{ old('estado', $localidad->estado ?: 'visible') === 'visible' ? 'selected' : '' }}>
                    Visible
                </option>
                <option value="oculto" {{ old('estado', $localidad->estado) === 'oculto' ? 'selected' : '' }}>
                    Oculto
                </option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999" value="{{ old('orden', $localidad->orden ?? 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="imagen_portada">Imagen de portada</label>
            <input type="file" name="imagen_portada" id="imagen_portada" accept=".jpg,.jpeg,.png,.webp">
            <small class="fecha">Formatos: JPG, PNG, WEBP. Máximo 4MB.</small>
            @error('imagen_portada') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($localidad->imagen_portada)
            <div class="admin-form-group full">
                <label>Imagen actual</label>
                <img src="{{ $localidad->imagen_portada_url }}" alt="{{ $localidad->nombre }}" class="preview-imagen-admin">
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    const initEditor = (selector) => {
        if (!document.querySelector(selector)) return;
        tinymce.init({
            selector,
            height: 380,
            menubar: false,
            plugins: 'lists link code wordcount',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link | code',
            language: 'es',
            language_url: '/js/tinymce/langs/es.js',
            branding: false,
            promotion: false,
            license_key: 'gpl',
            setup: (editor) => {
                editor.on('change keyup', () => tinymce.triggerSave());
            }
        });
    };

    initEditor('#historia');
});
</script>
@endpush
