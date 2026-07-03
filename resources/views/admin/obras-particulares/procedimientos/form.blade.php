@extends('layouts.app')

@section('title', 'Editar procedimiento — ' . $procedimiento->titulo)

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar: {{ $procedimiento->titulo }}</h2>
        <p class="admin-subtitle">
            Sección: <strong>{{ \App\Models\ObraProcedimiento::SECCIONES[$procedimiento->seccion]['titulo'] ?? $procedimiento->seccion }}</strong>
            &nbsp;·&nbsp; Código: <strong>{{ strtoupper($procedimiento->codigo) }}</strong>
        </p>
    </div>
    <a href="{{ route('admin.obras.procedimientos.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($anexos->isNotEmpty())
    <div class="ops-admin-tip" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-info"></i>
        <div>
            <strong>Formularios disponibles para vincular:</strong>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                @foreach($anexos as $anexo)
                    <span class="badge-categoria" title="{{ $anexo->archivo_ruta ?? 'Sin archivo' }}">
                        {{ $anexo->nombre }}
                        @if($anexo->archivo_ruta)
                            — <a href="{{ $anexo->archivo_ruta }}" target="_blank" style="color:inherit;">ver</a>
                        @endif
                    </span>
                @endforeach
            </div>
            <small class="fecha" style="margin-top:6px;display:block;">
                Podés copiar la URL de cada formulario (columna "Ver") y usarla como enlace dentro del editor de texto.
            </small>
        </div>
    </div>
@endif

<form action="{{ route('admin.obras.procedimientos.update', $procedimiento) }}"
      method="POST" class="admin-form-card">
    @csrf @method('PUT')

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="titulo">Título del accordion</label>
            <input type="text" name="titulo" id="titulo"
                   value="{{ old('titulo', $procedimiento->titulo) }}" required>
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="contenido">Contenido</label>
            <textarea name="contenido" id="contenido" rows="16">{{ old('contenido', $procedimiento->contenido) }}</textarea>
            <small class="fecha">Usá el editor para formatear listas, negritas y agregar links a los formularios.</small>
            @error('contenido') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999"
                   value="{{ old('orden', $procedimiento->orden) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="visible">Visibilidad</label>
            <select name="visible" id="visible">
                <option value="1" {{ old('visible', $procedimiento->visible) ? 'selected' : '' }}>Visible</option>
                <option value="0" {{ ! old('visible', $procedimiento->visible) ? 'selected' : '' }}>Oculto</option>
            </select>
            @error('visible') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>
@endsection

@push('scripts_head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    tinymce.init({
        selector: '#contenido',
        language: 'es',
        height: 420,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic | bullist numlist | link | removeformat',
        menubar: false,
        skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
        content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
        branding: false,
    });
});
</script>
@endpush
