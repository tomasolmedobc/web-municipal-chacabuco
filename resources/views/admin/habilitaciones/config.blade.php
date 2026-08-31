@extends('layouts.app')

@section('title', 'Configuración — Habilitaciones')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Configuración — Trámites Online</h2>
        <p class="admin-subtitle">URLs de los botones de acceso a trámites digitales de habilitaciones.</p>
    </div>
    <a href="{{ route('admin.habilitaciones.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

<form method="POST" action="{{ route('admin.habilitaciones.config.update') }}" class="admin-form-card">
    @csrf @method('PUT')

    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label" for="url_prefactibilidad">
            URL — Trámite Online Prefactibilidad Comercial
        </label>
        <input
            type="text"
            id="url_prefactibilidad"
            name="url_prefactibilidad"
            value="{{ old('url_prefactibilidad', $url_prefactibilidad) }}"
            placeholder="https://... o /ruta-interna"
            class="campo-input"
        >
        <p class="campo-ayuda">
            Dejá vacío para que el botón aparezca deshabilitado (<code>#</code>). Aceptá URLs completas o rutas internas que comiencen con <code>/</code>.
        </p>
    </div>

    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label" for="url_habilitacion">
            URL — Trámite Online Habilitación en General
        </label>
        <input
            type="text"
            id="url_habilitacion"
            name="url_habilitacion"
            value="{{ old('url_habilitacion', $url_habilitacion) }}"
            placeholder="https://... o /ruta-interna"
            class="campo-input"
        >
        <p class="campo-ayuda">
            Dejá vacío para que el botón aparezca deshabilitado (<code>#</code>). Aceptá URLs completas o rutas internas que comiencen con <code>/</code>.
        </p>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
    </div>
</form>
@endsection
