@extends('layouts.app')

@section('title', 'Configuración — Tasas Municipales')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Configuración general</h2>
        <p class="admin-subtitle">Textos, botones de acción y banner promocional.</p>
    </div>
    <a href="{{ route('admin.tasas.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.tasas.config.update') }}"
      enctype="multipart/form-data" class="admin-form-card">
    @csrf @method('PUT')

    {{-- Textos TinyMCE --}}
    <div class="admin-form-group full" style="margin-bottom:24px;">
        <label class="campo-label" for="texto_pago_anual">Texto — Pago Anual</label>
        <textarea name="texto_pago_anual" id="texto_pago_anual" rows="8">{{ old('texto_pago_anual', $config->texto_pago_anual) }}</textarea>
        <p class="campo-ayuda">Se muestra dentro del accordion "Pago Anual".</p>
    </div>

    <div class="admin-form-group full" style="margin-bottom:24px;">
        <label class="campo-label" for="texto_plan_facilidades">Texto — Plan de Facilidades</label>
        <textarea name="texto_plan_facilidades" id="texto_plan_facilidades" rows="8">{{ old('texto_plan_facilidades', $config->texto_plan_facilidades) }}</textarea>
        <p class="campo-ayuda">Se muestra dentro del accordion "Plan de Facilidades".</p>
    </div>

    <div class="admin-form-group full" style="margin-bottom:32px;">
        <label class="campo-label" for="texto_info_bancaria">Texto — Información Bancaria</label>
        <textarea name="texto_info_bancaria" id="texto_info_bancaria" rows="8">{{ old('texto_info_bancaria', $config->texto_info_bancaria) }}</textarea>
        <p class="campo-ayuda">Datos para pagos por transferencia o depósito bancario.</p>
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Banner --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Banner promocional</h3>

    <div class="admin-form-group" style="margin-bottom:16px;">
        <label class="campo-label">URL del banner (destino del clic)</label>
        <input type="url" name="banner_url" value="{{ old('banner_url', $config->banner_url) }}"
               placeholder="https://..." class="campo-input">
    </div>

    <div class="admin-form-group full" style="margin-bottom:32px;">
        <label class="campo-label">Imagen del banner</label>
        <input type="file" name="banner_imagen" class="campo-input" accept="image/*">
        <p class="campo-ayuda">JPG, PNG o WEBP. Máx. 5 MB. Reemplaza la imagen actual si se sube una nueva.</p>
        @if($config->banner_imagen_url)
            <div class="tasas-archivo-actual" style="margin-top:10px;">
                <i class="fa-solid fa-image"></i>
                <span>Banner actual cargado</span>
                <a href="{{ $config->banner_imagen_url }}" target="_blank" style="font-size:.8rem;">ver</a>
            </div>
        @endif
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:0 0 28px;">

    {{-- Botón: Consulta y formas de pago --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Botón "Consulta y formas de pago de tasas"</h3>

    <fieldset style="border:none; padding:0; margin:0 0 16px;">
        <label class="tasas-radio-label">
            <input type="radio" name="btn_consulta_tipo" value="url"
                {{ old('btn_consulta_tipo', $config->btn_consulta_tipo) === 'url' ? 'checked' : '' }}
            >
            Enlace externo (URL)
        </label>
        <label class="tasas-radio-label" style="margin-top:8px;">
            <input type="radio" name="btn_consulta_tipo" value="archivo"
                {{ old('btn_consulta_tipo', $config->btn_consulta_tipo) === 'archivo' ? 'checked' : '' }}
            >
            Archivo PDF
        </label>
    </fieldset>

    <div id="btn-consulta-url" class="tasas-config-panel">
        <input type="url" name="btn_consulta_url"
               value="{{ old('btn_consulta_url', $config->btn_consulta_url) }}"
               placeholder="https://..." class="campo-input">
    </div>
    <div id="btn-consulta-archivo" class="tasas-config-panel" style="display:none;">
        <input type="file" name="btn_consulta_archivo" class="campo-input" accept=".pdf,.doc,.docx">
        <p class="campo-ayuda">PDF, DOC o DOCX. Máx. 50 MB.</p>
        @if($config->btn_consulta_tipo === 'archivo' && $config->btn_consulta_archivo_nombre)
            <div class="tasas-archivo-actual">
                <i class="fa-regular fa-file-pdf"></i>
                <span>{{ $config->btn_consulta_archivo_nombre }}</span>
            </div>
        @endif
    </div>

    <hr style="border:none; border-top:1px solid var(--border); margin:16px 0 28px;">

    {{-- Botón: Ordenanza Impositiva --}}
    <h3 style="font-size:1rem; margin:0 0 16px;">Botón "Ordenanza Impositiva Vigente"</h3>

    <fieldset style="border:none; padding:0; margin:0 0 16px;">
        <label class="tasas-radio-label">
            <input type="radio" name="btn_ordenanza_tipo" value="url"
                {{ old('btn_ordenanza_tipo', $config->btn_ordenanza_tipo) === 'url' ? 'checked' : '' }}
            >
            Enlace externo (URL)
        </label>
        <label class="tasas-radio-label" style="margin-top:8px;">
            <input type="radio" name="btn_ordenanza_tipo" value="archivo"
                {{ old('btn_ordenanza_tipo', $config->btn_ordenanza_tipo) === 'archivo' ? 'checked' : '' }}
            >
            Archivo PDF
        </label>
    </fieldset>

    <div id="btn-ordenanza-url" class="tasas-config-panel">
        <input type="url" name="btn_ordenanza_url"
               value="{{ old('btn_ordenanza_url', $config->btn_ordenanza_url) }}"
               placeholder="https://..." class="campo-input">
    </div>
    <div id="btn-ordenanza-archivo" class="tasas-config-panel" style="display:none;">
        <input type="file" name="btn_ordenanza_archivo" class="campo-input" accept=".pdf,.doc,.docx">
        <p class="campo-ayuda">PDF, DOC o DOCX. Máx. 50 MB.</p>
        @if($config->btn_ordenanza_tipo === 'archivo' && $config->btn_ordenanza_archivo_nombre)
            <div class="tasas-archivo-actual">
                <i class="fa-regular fa-file-pdf"></i>
                <span>{{ $config->btn_ordenanza_archivo_nombre }}</span>
            </div>
        @endif
    </div>

    <div style="margin-top:28px;">
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
    </div>
</form>
@endsection

@push('scripts_head')
<script @nonce>
function toggleBtn(btn, val) {
    var urlPanel     = document.getElementById('btn-' + btn + '-url');
    var archivoPanel = document.getElementById('btn-' + btn + '-archivo');
    var urlInput     = urlPanel.querySelector('input');

    if (val === 'url') {
        urlPanel.style.display     = '';
        archivoPanel.style.display = 'none';
        urlInput.disabled          = false;
    } else {
        urlPanel.style.display     = 'none';
        archivoPanel.style.display = '';
        urlInput.disabled          = true;
    }
}
document.addEventListener('DOMContentLoaded', function () {
    ['consulta', 'ordenanza'].forEach(function (btn) {
        document.querySelectorAll('input[name="btn_' + btn + '_tipo"]').forEach(function (r) {
            r.addEventListener('change', function () { toggleBtn(btn, this.value); });
        });
        var checked = document.querySelector('input[name="btn_' + btn + '_tipo"]:checked');
        if (checked) toggleBtn(btn, checked.value);
    });

    if (typeof tinymce === 'undefined') return;
    ['texto_pago_anual', 'texto_plan_facilidades', 'texto_info_bancaria'].forEach(function (id) {
        tinymce.init({
            selector: '#' + id,
            language: 'es',
            height: 320,
            plugins: 'lists link',
            toolbar: 'undo redo | bold italic | bullist numlist | link | removeformat',
            menubar: false,
            skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
            content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
            branding: false,
        });
    });
});
</script>
@endpush
