@extends('layouts.app')

@php $esEdicion = $modo === 'editar'; @endphp

@section('title', $esEdicion ? 'Editar procedimiento — ' . $procedimiento->titulo : 'Nuevo procedimiento')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $esEdicion ? 'Editar: ' . $procedimiento->titulo : 'Nuevo procedimiento' }}</h2>
        @if($esEdicion)
            <p class="admin-subtitle">
                Categoría: <strong>{{ $procedimiento->categoria->nombre ?? '—' }}</strong>
            </p>
        @endif
    </div>
    <a href="{{ route('admin.obras.procedimientos.index', $esEdicion ? ['categoria' => $procedimiento->categoria_id] : []) }}"
       class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

@if($anexos->isNotEmpty())
    <div class="ops-admin-tip mb-20">
        <i class="fa-solid fa-circle-info"></i>
        <div class="flex-1 item-text">
            <strong>Formularios disponibles para vincular:</strong>
            <small class="fecha d-block mb-10">
                Hacé clic en "Copiar URL" y luego pegala como enlace dentro del editor de texto.
            </small>
            <div class="ops-anexos-list">
                @foreach($anexos as $anexo)
                    <div class="ops-anexo-item">
                        <span class="ops-anexo-name">{{ $anexo->nombre }}</span>
                        @if($anexo->archivo_ruta)
                            <code class="ops-anexo-url">{{ $anexo->archivo_ruta }}</code>
                            <button type="button"
                                    class="btn btn-secondary btn-copiar-url ops-btn-copy"
                                    data-copy-url="{{ $anexo->archivo_ruta }}">
                                Copiar URL
                            </button>
                        @else
                            <span class="ops-anexo-empty">Sin archivo cargado</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<form action="{{ $esEdicion ? route('admin.obras.procedimientos.update', $procedimiento) : route('admin.obras.procedimientos.store') }}"
      method="POST" class="admin-form-card">
    @csrf
    @if($esEdicion) @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label class="campo-label" for="categoria_id">Categoría</label>
            <select name="categoria_id" id="categoria_id" required class="campo-input">
                <option value="">— Seleccioná una categoría —</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('categoria_id', $procedimiento->categoria_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
            @error('categoria_id') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="codigo">Código (opcional)</label>
            <input type="text" name="codigo" id="codigo"
                   value="{{ old('codigo', $procedimiento->codigo ?? '') }}"
                   placeholder="Ej: A, B, 1, 2" maxlength="10" class="campo-input">
            <small class="fecha">Etiqueta corta que aparece antes del título en el acordeón.</small>
            @error('codigo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label class="campo-label" for="titulo">Título del acordeón</label>
            <input type="text" name="titulo" id="titulo"
                   value="{{ old('titulo', $procedimiento->titulo ?? '') }}"
                   required class="campo-input">
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label class="campo-label" for="contenido">Contenido</label>
            <textarea name="contenido" id="contenido" rows="16">{{ old('contenido', $procedimiento->contenido ?? '') }}</textarea>
            <small class="fecha">Usá el editor para formatear listas, negritas y agregar links a los formularios.</small>
            @error('contenido') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999"
                   value="{{ old('orden', $procedimiento->orden ?? 0) }}" class="campo-input">
            <small class="fecha">Número menor aparece antes. Usá 0 para que quede al principio.</small>
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label class="campo-label" for="visible">Visibilidad</label>
            <select name="visible" id="visible" class="campo-input">
                <option value="1" {{ old('visible', $procedimiento->visible ?? true) ? 'selected' : '' }}>Visible</option>
                <option value="0" {{ ! old('visible', $procedimiento->visible ?? true) ? 'selected' : '' }}>Oculto</option>
            </select>
            @error('visible') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $esEdicion ? 'Guardar cambios' : 'Crear procedimiento' }}
    </button>
</form>
@endsection

@push('scripts')
<script @nonce>
document.querySelectorAll('.btn-copiar-url').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var url = this.getAttribute('data-copy-url');
        var self = this;

        function marcarCopiado() {
            self.textContent = '¡Copiado!';
            setTimeout(function () { self.textContent = 'Copiar URL'; }, 2000);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(marcarCopiado).catch(function () { copiarFallback(url, marcarCopiado); });
        } else {
            copiarFallback(url, marcarCopiado);
        }
    });
});

function copiarFallback(texto, callback) {
    var ta = document.createElement('textarea');
    ta.value = texto;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0;';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); callback(); } catch (e) { alert('No se pudo copiar: ' + texto); }
    document.body.removeChild(ta);
}
</script>
@endpush

@push('scripts_head')
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    tinymce.init({
        selector: '#contenido',
        nonce: '{{ $cspNonce }}',
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
