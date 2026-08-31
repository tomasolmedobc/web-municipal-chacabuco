@extends('layouts.app')

@section('title', 'Editar contenido — Carnet de Conducir')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar contenido</h2>
        <p class="admin-subtitle">Carnet de Conducir — Textos, alertas y pasos del trámite.</p>
    </div>
    <a href="{{ route('admin.carnet.index') }}" class="btn btn-secondary">← Volver</a>
</section>

@if($errors->any())
    <div class="admin-alert" style="background:#fef2f2; color:#991b1b; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.carnet.config.update') }}" class="admin-form-card">
    @csrf @method('PUT')

    {{-- Introducción --}}
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Texto de introducción</label>
        <textarea name="intro_texto" id="intro_texto" rows="4">{{ old('intro_texto', $config->intro_texto) }}</textarea>
        <p class="campo-ayuda">Párrafo que aparece al inicio de la página.</p>
    </div>

    {{-- Alertas --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Cajas de alerta</h3>

    <div class="admin-form-group full" style="margin-bottom:16px;">
        <label class="campo-label" for="alerta_info">Alerta informativa (caja azul)</label>
        <textarea name="alerta_info" id="alerta_info" rows="3" maxlength="500">{{ old('alerta_info', $config->alerta_info) }}</textarea>
        <p class="campo-ayuda">Texto de la caja azul de información. Permite HTML básico (strong, em).</p>
    </div>

    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label" for="aviso_ubicacion">Aviso de ubicación (caja amarilla)</label>
        <textarea name="aviso_ubicacion" id="aviso_ubicacion" rows="3" maxlength="500">{{ old('aviso_ubicacion', $config->aviso_ubicacion) }}</textarea>
        <p class="campo-ayuda">Texto del aviso sobre dónde se realizan los pasos. Permite HTML básico (strong, em).</p>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Paso 1 --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Paso 1</h3>
    <div class="admin-form-group full" style="margin-bottom:12px;">
        <label class="campo-label" for="paso1_titulo">Título del paso 1</label>
        <input type="text" id="paso1_titulo" name="paso1_titulo"
               value="{{ old('paso1_titulo', $config->paso1_titulo) }}"
               required maxlength="255">
    </div>
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Contenido del paso 1</label>
        <textarea name="paso1_contenido" id="paso1_contenido" rows="10">{{ old('paso1_contenido', $config->paso1_contenido) }}</textarea>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Paso 2 --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Paso 2</h3>
    <div class="admin-form-group full" style="margin-bottom:12px;">
        <label class="campo-label" for="paso2_titulo">Título del paso 2</label>
        <input type="text" id="paso2_titulo" name="paso2_titulo"
               value="{{ old('paso2_titulo', $config->paso2_titulo) }}"
               required maxlength="255">
    </div>
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Contenido del paso 2</label>
        <textarea name="paso2_contenido" id="paso2_contenido" rows="10">{{ old('paso2_contenido', $config->paso2_contenido) }}</textarea>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Paso 3 --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Paso 3</h3>
    <div class="admin-form-group full" style="margin-bottom:12px;">
        <label class="campo-label" for="paso3_titulo">Título del paso 3</label>
        <input type="text" id="paso3_titulo" name="paso3_titulo"
               value="{{ old('paso3_titulo', $config->paso3_titulo) }}"
               required maxlength="255">
    </div>
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Contenido del paso 3</label>
        <textarea name="paso3_contenido" id="paso3_contenido" rows="8">{{ old('paso3_contenido', $config->paso3_contenido) }}</textarea>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Paso 4 --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Paso 4</h3>
    <div class="admin-form-group full" style="margin-bottom:12px;">
        <label class="campo-label" for="paso4_titulo">Título del paso 4</label>
        <input type="text" id="paso4_titulo" name="paso4_titulo"
               value="{{ old('paso4_titulo', $config->paso4_titulo) }}"
               required maxlength="255">
    </div>
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Contenido del paso 4</label>
        <textarea name="paso4_contenido" id="paso4_contenido" rows="12">{{ old('paso4_contenido', $config->paso4_contenido) }}</textarea>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Licencia digital --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Sección Licencia Digital</h3>
    <div class="admin-form-group full" style="margin-bottom:28px;">
        <label class="campo-label">Contenido de la sección Licencia Digital</label>
        <textarea name="licencia_digital_contenido" id="licencia_digital_contenido" rows="14">{{ old('licencia_digital_contenido', $config->licencia_digital_contenido) }}</textarea>
        <p class="campo-ayuda">Sección que aparece debajo de los 4 pasos. Puede incluir subtítulos, pasos y listas.</p>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('admin.carnet.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

@endsection

@push('scripts_head')
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    var ids = [
        'intro_texto',
        'paso1_contenido', 'paso2_contenido',
        'paso3_contenido', 'paso4_contenido',
        'licencia_digital_contenido',
    ];

    ids.forEach(function (id) {
        tinymce.init({
            selector: '#' + id,
            language: 'es',
            height: id === 'licencia_digital_contenido' ? 400 : 320,
            plugins: 'lists link table',
            toolbar: 'undo redo | bold italic | bullist numlist | table | link | removeformat',
            table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
            menubar: false,
            skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
            content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
            branding: false,
            valid_elements: '*[*]',
        });
    });

    var idsSimples = ['alerta_info', 'aviso_ubicacion'];
    idsSimples.forEach(function (id) {
        tinymce.init({
            selector: '#' + id,
            language: 'es',
            height: 120,
            plugins: 'link',
            toolbar: 'bold italic | link | removeformat',
            menubar: false,
            skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
            content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
            branding: false,
        });
    });
});
</script>
@endpush
