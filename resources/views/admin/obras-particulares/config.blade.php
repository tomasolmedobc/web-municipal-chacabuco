@extends('layouts.app')

@section('title', 'Registro de Profesionales — Configuración')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Registro de Profesionales</h2>
        <p class="admin-subtitle">Configurá el enlace del formulario de inscripción.</p>
    </div>
    <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if($errors->any())
    <div class="alert-error" style="margin-bottom:18px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.obras.config.update') }}" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')

    <fieldset style="border:none; padding:0; margin:0 0 24px;">
        <legend style="font-weight:600; margin-bottom:14px;">Tipo de enlace</legend>

        <label class="ops-radio-label">
            <input type="radio" name="registro_tipo" value="url"
                {{ old('registro_tipo', $config->registro_tipo) === 'url' ? 'checked' : '' }}
                onchange="toggleConfigType(this.value)">
            Enlace externo (URL)
        </label>

        <label class="ops-radio-label" style="margin-top:8px;">
            <input type="radio" name="registro_tipo" value="archivo"
                {{ old('registro_tipo', $config->registro_tipo) === 'archivo' ? 'checked' : '' }}
                onchange="toggleConfigType(this.value)">
            Archivo PDF
        </label>
    </fieldset>

    {{-- URL --}}
    <div id="cfg-url" class="ops-config-panel">
        <label class="campo-label">URL del formulario</label>
        <input type="url" name="registro_url"
               value="{{ old('registro_url', $config->registro_url) }}"
               placeholder="https://forms.google.com/..."
               class="campo-input">
        <p class="campo-ayuda">Puede ser un Google Form, enlace externo u otra página.</p>
    </div>

    {{-- Archivo --}}
    <div id="cfg-archivo" class="ops-config-panel" style="display:none;">
        <label class="campo-label">Archivo PDF</label>
        <input type="file" name="registro_archivo" class="campo-input" accept=".pdf,.doc,.docx">
        <p class="campo-ayuda">PDF, DOC o DOCX. Máximo 50 MB.</p>

        @if($config->registro_tipo === 'archivo' && $config->registro_archivo_nombre)
            <div class="ops-archivo-actual">
                <i class="fa-regular fa-file-pdf"></i>
                <span>{{ $config->registro_archivo_nombre }}</span>
                <em style="color:var(--muted); font-size:.8rem;">(actual — subí uno nuevo para reemplazarlo)</em>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection

@push('scripts')
<script>
function toggleConfigType(val) {
    document.getElementById('cfg-url').style.display    = val === 'url'     ? '' : 'none';
    document.getElementById('cfg-archivo').style.display = val === 'archivo' ? '' : 'none';
}
// Inicializar al cargar
toggleConfigType(document.querySelector('input[name="registro_tipo"]:checked')?.value ?? 'url');
</script>
@endpush
