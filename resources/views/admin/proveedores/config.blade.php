@extends('layouts.app')

@section('title', 'Configuración — Compras y Proveedores')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Configuración general</h2>
        <p class="admin-subtitle">Guía visual y contenido de los acordeones de la página de proveedores.</p>
    </div>
    <a href="{{ route('admin.proveedores.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.proveedores.config.update') }}" enctype="multipart/form-data" class="admin-form-card">
    @csrf @method('PUT')

    {{-- Guía visual --}}
    <h3 class="admin-section-h3">Guía visual</h3>

    <div class="admin-form-group full mb-24">
        <label class="campo-label" for="guia_video_url">Video (YouTube o Vimeo)</label>
        <input
            type="url"
            id="guia_video_url"
            name="guia_video_url"
            value="{{ old('guia_video_url', $config->guia_video_url) }}"
            placeholder="https://www.youtube.com/watch?v=..."
            class="campo-input"
        >
        <p class="campo-ayuda">Si se carga un video, se mostrará antes de las imágenes. Aceptá URLs de YouTube y Vimeo.</p>
        @error('guia_video_url') <small class="auth-error">{{ $message }}</small> @enderror
    </div>

    {{-- Imagen 1 --}}
    <div class="admin-form-group full mb-24">
        <label class="campo-label">Imagen 1</label>

        @if($config->guia_imagen_1_ruta)
            <div class="config-preview" style="margin-bottom:10px">
                <img src="{{ $config->guia_imagen_1_ruta }}" alt="Imagen 1 actual" style="max-height:120px">
            </div>
            <label class="config-remove">
                <input type="checkbox" name="eliminar_imagen_1" value="1">
                <span>Quitar imagen actual (vuelve a la imagen original)</span>
            </label>
        @endif

        <input type="file" name="guia_imagen_1" accept=".jpg,.jpeg,.png,.webp" style="margin-top:8px">
        @error('guia_imagen_1') <small class="auth-error">{{ $message }}</small> @enderror

        <div class="admin-form-group" style="margin-top:12px">
            <label for="guia_imagen_1_titulo">Título</label>
            <input type="text" id="guia_imagen_1_titulo" name="guia_imagen_1_titulo"
                value="{{ old('guia_imagen_1_titulo', $config->guia_imagen_1_titulo ?? 'Paso 1') }}"
                class="campo-input" style="max-width:340px">
        </div>
        <div class="admin-form-group" style="margin-top:8px">
            <label for="guia_imagen_1_desc">Descripción</label>
            <input type="text" id="guia_imagen_1_desc" name="guia_imagen_1_desc"
                value="{{ old('guia_imagen_1_desc', $config->guia_imagen_1_desc ?? '') }}"
                class="campo-input">
        </div>
    </div>

    {{-- Imagen 2 --}}
    <div class="admin-form-group full mb-32">
        <label class="campo-label">Imagen 2</label>

        @if($config->guia_imagen_2_ruta)
            <div class="config-preview" style="margin-bottom:10px">
                <img src="{{ $config->guia_imagen_2_ruta }}" alt="Imagen 2 actual" style="max-height:120px">
            </div>
            <label class="config-remove">
                <input type="checkbox" name="eliminar_imagen_2" value="1">
                <span>Quitar imagen actual (vuelve a la imagen original)</span>
            </label>
        @endif

        <input type="file" name="guia_imagen_2" accept=".jpg,.jpeg,.png,.webp" style="margin-top:8px">
        @error('guia_imagen_2') <small class="auth-error">{{ $message }}</small> @enderror

        <div class="admin-form-group" style="margin-top:12px">
            <label for="guia_imagen_2_titulo">Título</label>
            <input type="text" id="guia_imagen_2_titulo" name="guia_imagen_2_titulo"
                value="{{ old('guia_imagen_2_titulo', $config->guia_imagen_2_titulo ?? 'Paso 2') }}"
                class="campo-input" style="max-width:340px">
        </div>
        <div class="admin-form-group" style="margin-top:8px">
            <label for="guia_imagen_2_desc">Descripción</label>
            <input type="text" id="guia_imagen_2_desc" name="guia_imagen_2_desc"
                value="{{ old('guia_imagen_2_desc', $config->guia_imagen_2_desc ?? '') }}"
                class="campo-input">
        </div>
    </div>

    <hr class="section-divider">

    {{-- Requisitos --}}
    <div class="admin-form-group full mb-24">
        <label class="campo-label" for="texto_requisitos">Requisitos de inscripción</label>
        <textarea name="texto_requisitos" id="texto_requisitos" rows="10">{{ old('texto_requisitos', $config->texto_requisitos) }}</textarea>
        <p class="campo-ayuda">Contenido del acordeón "Requisitos de inscripción".</p>
    </div>

    {{-- Info bancaria --}}
    <div class="admin-form-group full mb-24">
        <label class="campo-label" for="texto_info_bancaria">Información bancaria</label>
        <textarea name="texto_info_bancaria" id="texto_info_bancaria" rows="8">{{ old('texto_info_bancaria', $config->texto_info_bancaria) }}</textarea>
        <p class="campo-ayuda">Contenido del acordeón "Información bancaria".</p>
    </div>

    {{-- Email contacto --}}
    <div class="admin-form-group full mb-24">
        <label class="campo-label" for="texto_email_contacto">Email de contacto y envío de documentación</label>
        <textarea name="texto_email_contacto" id="texto_email_contacto" rows="5">{{ old('texto_email_contacto', $config->texto_email_contacto) }}</textarea>
        <p class="campo-ayuda">Contenido del acordeón de contacto y envío de documentación.</p>
    </div>

    {{-- Creación de usuario --}}
    <div class="admin-form-group full mb-32">
        <label class="campo-label" for="texto_creacion_usuario">Creación de usuario para el sistema</label>
        <textarea name="texto_creacion_usuario" id="texto_creacion_usuario" rows="8">{{ old('texto_creacion_usuario', $config->texto_creacion_usuario) }}</textarea>
        <p class="campo-ayuda">Contenido del acordeón "Creación de usuario para el sistema de proveedores".</p>
    </div>

    <div class="mt-28">
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
    </div>
</form>
@endsection

@push('scripts_head')
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    var darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches
        || document.body.classList.contains('dark');

    ['texto_requisitos', 'texto_info_bancaria', 'texto_email_contacto', 'texto_creacion_usuario'].forEach(function (id) {
        tinymce.init({
            selector: '#' + id,
            nonce: '{{ $cspNonce }}',
            language: 'es',
            height: 320,
            plugins: 'lists link',
            toolbar: 'undo redo | bold italic | bullist numlist | link | removeformat',
            menubar: false,
            skin: darkMode ? 'oxide-dark' : 'oxide',
            content_css: darkMode ? 'dark' : 'default',
            branding: false,
        });
    });
});
</script>
@endpush
