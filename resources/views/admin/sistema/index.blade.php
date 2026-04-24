@extends('layouts.app')

@section('title', 'Configuración del sistema')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Configuración del sistema</h2>
        <p class="admin-subtitle">Administrá opciones generales del portal.</p>
    </div>

    <a href="{{ route('admin.perfil.edit') }}" class="btn btn-secondary">Volver</a>
</section>

@if(session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

<form action="{{ route('admin.sistema.update') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')

    <div class="admin-form-grid">

        {{-- LOGO --}}
        <div class="admin-form-group">
            <label>Logo del sitio</label>

            @if($logo)
                <div class="config-preview">
                    <img src="{{ $logo }}" alt="Logo actual">
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_logo" value="1">
                    <span>Quitar logo actual</span>
                </label>
            @endif

            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
            @error('logo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        {{-- PORTADA --}}
        <div class="admin-form-group">
            <label>Imagen de portada</label>

            @if($portada)
                <div class="config-preview">
                    <img src="{{ $portada }}" alt="Portada actual">
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_portada" value="1">
                    <span>Quitar portada actual</span>
                </label>
            @endif

            <input type="file" name="portada" accept=".jpg,.jpeg,.png,.webp">
            @error('portada') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        {{-- IMAGEN DEFAULT --}}
        <div class="admin-form-group full">
            <label>Imagen por defecto para noticias</label>

            @if($default_noticia)
                <div class="config-preview">
                    <img src="{{ $default_noticia }}" alt="Imagen por defecto actual">
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_default_noticia" value="1">
                    <span>Quitar imagen actual</span>
                </label>
            @endif

            <input type="file" name="default_noticia" accept=".jpg,.jpeg,.png,.webp">
            @error('default_noticia') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        Guardar configuración
    </button>
</form>
@endsection