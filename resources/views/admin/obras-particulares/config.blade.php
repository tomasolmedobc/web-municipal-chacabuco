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
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.obras.config.update') }}" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')

    <fieldset class="fieldset-clean mb-24">
        <legend class="legend-label">Tipo de enlace</legend>

        <label class="ops-radio-label">
            <input type="radio" name="registro_tipo" value="url"
                {{ old('registro_tipo', $config->registro_tipo) === 'url' ? 'checked' : '' }}
            >
            Enlace externo (URL)
        </label>

        <label class="ops-radio-label mt-8">
            <input type="radio" name="registro_tipo" value="archivo"
                {{ old('registro_tipo', $config->registro_tipo) === 'archivo' ? 'checked' : '' }}
            >
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
    <div id="cfg-archivo" class="ops-config-panel d-none">
        <label class="campo-label">Archivo PDF</label>
        <input type="file" name="registro_archivo" class="campo-input" accept=".pdf,.doc,.docx">
        <p class="campo-ayuda">PDF, DOC o DOCX. Máximo 50 MB.</p>

        @if($config->registro_tipo === 'archivo' && $config->registro_archivo_nombre)
            <div class="ops-archivo-actual">
                <i class="fa-regular fa-file-pdf"></i>
                <span>{{ $config->registro_archivo_nombre }}</span>
                <em class="text-muted-sm">(actual — subí uno nuevo para reemplazarlo)</em>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection

@push('scripts')
<script @nonce>
function toggleConfigType(val) {
    document.getElementById('cfg-url').classList.toggle('d-none', val !== 'url');
    document.getElementById('cfg-archivo').classList.toggle('d-none', val !== 'archivo');
}
document.querySelectorAll('input[name="registro_tipo"]').forEach(function (r) {
    r.addEventListener('change', function () { toggleConfigType(this.value); });
});
// Inicializar al cargar
toggleConfigType(document.querySelector('input[name="registro_tipo"]:checked')?.value ?? 'url');
</script>
@endpush
