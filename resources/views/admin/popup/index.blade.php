@extends('layouts.app')

@section('title', 'Popup de anuncio')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Popup de anuncio</h2>
        <p class="admin-subtitle">Configurá el mensaje emergente que verán los visitantes del portal.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
</section>

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

<form action="{{ route('admin.popup.update') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')

    <div class="admin-form-grid">

        {{-- ESTADO --}}
        <div class="admin-form-group full">
            <label class="check-label">
                <input type="checkbox" name="popup_activo" value="1" {{ $popup_activo ? 'checked' : '' }}>
                <span>Mostrar popup en el portal</span>
            </label>
            <small class="fecha">Si está activo y tiene imagen, el popup se mostrará a los visitantes. Se puede desactivar sin borrar la imagen.</small>
        </div>

        {{-- IMAGEN --}}
        <div class="admin-form-group full">
            <label>Imagen del popup</label>

            @if($popup_imagen)
                <div class="config-preview config-preview--wide">
                    <img src="{{ $popup_imagen }}" alt="Imagen del popup actual">
                </div>
                <label class="config-remove">
                    <input type="checkbox" name="eliminar_popup_imagen" value="1">
                    <span>Quitar imagen actual</span>
                </label>
            @else
                <p class="fecha mb-8">No hay imagen cargada. Se usará la imagen por defecto del municipio como previsualización.</p>
            @endif

            <input type="file" name="popup_imagen" accept=".jpg,.jpeg,.png,.webp">
            <small class="fecha">JPG, PNG o WEBP. Máximo 4 MB. Al subir una nueva imagen, la anterior se elimina automáticamente.</small>
            @error('popup_imagen') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        {{-- BOTÓN: TEXTO --}}
        <div class="admin-form-group">
            <label for="popup_boton_texto">Texto del botón <small class="fecha">(opcional)</small></label>
            <input type="text"
                   name="popup_boton_texto"
                   id="popup_boton_texto"
                   value="{{ old('popup_boton_texto', $popup_boton_texto) }}"
                   placeholder="Ej: Registrarse, Ingresar, Ver más…"
                   maxlength="60">
            <small class="fecha">Si lo dejás vacío, no se mostrará botón.</small>
            @error('popup_boton_texto') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        {{-- BOTÓN: URL --}}
        <div class="admin-form-group">
            <label for="popup_boton_url">URL del botón <small class="fecha">(opcional)</small></label>
            <input type="text"
                   name="popup_boton_url"
                   id="popup_boton_url"
                   value="{{ old('popup_boton_url', $popup_boton_url) }}"
                   placeholder="https://...">
            @error('popup_boton_url') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>

{{-- Previsualización --}}
@php
    $prevImg = $popup_imagen ?: asset('images/importantes/tu-imagen-default.webp');
    $prevTexto = $popup_boton_texto ?: 'Ver más';
    $prevUrl = $popup_boton_url ?: '#';
@endphp

<section class="admin-form-card mt-24">
    <h3 class="admin-section-h3">Previsualización</h3>
    <p class="admin-subtitle mb-20">
        Así verán el popup los visitantes. El botón solo aparece si tiene texto configurado.
    </p>

    <div class="popup-preview-wrap">
        <div class="anuncio-popup is-preview">
            <div class="anuncio-popup__dialog">
                <button type="button" class="anuncio-popup__cerrar" tabindex="-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="anuncio-popup__imagen">
                    <img src="{{ $prevImg }}" alt="Anuncio">
                </div>
                @if($popup_boton_texto)
                    <div class="anuncio-popup__footer">
                        <a href="{{ $prevUrl }}" class="btn btn-primary anuncio-popup__btn" target="_blank" rel="noopener">
                            {{ $popup_boton_texto }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
