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
            <label for="mapa_embed">Mapa de Google Maps</label>
            <input type="url"
                   name="mapa_embed"
                   id="mapa_embed"
                   class="campo-input"
                   placeholder="https://www.google.com/maps/embed?pb=..."
                   value="{{ old('mapa_embed', $localidad->mapa_embed) }}">
            <small class="fecha">
                ⚠️ <strong>No uses el link de "Compartir".</strong>
                Para obtener la URL correcta: Google Maps → buscá la localidad →
                <strong>Compartir</strong> → pestaña <strong>"Insertar un mapa"</strong>
                → del código <code>&lt;iframe src="<u>https://www.google.com/maps/embed?pb=…</u>"&gt;</code>
                copiá únicamente el valor dentro de <code>src="..."</code>.
                La URL debe empezar con <code>https://www.google.com/maps/embed</code>.
            </small>
            @error('mapa_embed') <small class="auth-error">{{ $message }}</small> @enderror

            <div id="mapa-preview-wrap" class="mt-12"
                @if(!old('mapa_embed', $localidad->mapa_embed)) hidden @endif>
                <iframe
                    id="mapa-preview"
                    src="{{ old('mapa_embed', $localidad->mapa_embed) }}"
                    width="100%"
                    height="300"
                    class="admin-mapa-preview"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
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
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    const initEditor = (selector) => {
        if (!document.querySelector(selector)) return;
        tinymce.init({
            selector,
            nonce: '{{ $cspNonce }}',
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
    };

    initEditor('#historia');

    // Preview en vivo del mapa
    const mapaInput   = document.getElementById('mapa-embed');
    const mapaWrap    = document.getElementById('mapa-preview-wrap');
    const mapaIframe  = document.getElementById('mapa-preview');

    if (mapaInput) {
        mapaInput.addEventListener('input', function () {
            var url = this.value.trim();
            if (url.startsWith('https://www.google.com/maps/embed')) {
                mapaIframe.src = url;
                mapaWrap.hidden = false;
            } else {
                mapaWrap.hidden = true;
                mapaIframe.src = '';
            }
        });
    }
});
</script>
@endpush
