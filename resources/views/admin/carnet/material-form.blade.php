@extends('layouts.app')

@section('title', ($modo === 'crear' ? 'Nuevo material' : 'Editar: ' . $item->titulo) . ' — Carnet de Conducir')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">
            {{ $modo === 'crear' ? 'Nuevo material de descarga' : 'Editar: ' . $item->titulo }}
        </h2>
        <p class="admin-subtitle">Carnet de Conducir</p>
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

<form action="{{ $modo === 'crear' ? route('admin.carnet.materiales.store') : route('admin.carnet.materiales.update', $item) }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">
    @csrf
    @if($modo === 'editar') @method('PUT') @endif

    <div class="admin-form-grid">

        <div class="admin-form-group full">
            <label for="titulo">Título del material <span class="req">*</span></label>
            <input type="text" id="titulo" name="titulo"
                   value="{{ old('titulo', $item->titulo) }}"
                   required maxlength="255"
                   placeholder="Ej: Manual del conductor">
        </div>

        <div class="admin-form-group">
            <label for="subtitulo">Tamaño / subtítulo</label>
            <input type="text" id="subtitulo" name="subtitulo"
                   value="{{ old('subtitulo', $item->subtitulo) }}"
                   maxlength="100"
                   placeholder="Ej: 2.9 Mb">
            <small style="color:var(--muted);">Se muestra junto al título entre paréntesis.</small>
        </div>

        <div class="admin-form-group">
            <label for="tipo_boton">Texto del botón <span class="req">*</span></label>
            <select id="tipo_boton" name="tipo_boton">
                <option value="descargar" {{ old('tipo_boton', $item->tipo_boton) === 'descargar' ? 'selected' : '' }}>
                    DESCARGAR ARCHIVO
                </option>
                <option value="ver" {{ old('tipo_boton', $item->tipo_boton) === 'ver' ? 'selected' : '' }}>
                    VER INFORMACIÓN
                </option>
            </select>
        </div>

        {{-- Subir PDF --}}
        <div class="admin-form-group full">
            <label for="pdf">Subir archivo PDF</label>

            @if($modo === 'editar' && $item->url && !str_starts_with($item->url, 'http'))
                <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--primary-soft); border-radius:10px; border:1px solid var(--border);">
                    <i class="fa-solid fa-file-pdf" style="color:var(--primary); font-size:18px;"></i>
                    <div>
                        <span style="font-size:13px; font-weight:600;">Archivo actual:</span>
                        <a href="{{ $item->url }}" target="_blank" rel="noopener"
                           style="font-size:12px; color:var(--primary); margin-left:6px;">
                            Ver PDF <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                    <span style="font-size:11px; color:var(--muted); margin-left:auto;">Subir uno nuevo lo reemplazará</span>
                </div>
            @endif

            <input type="file" id="pdf" name="pdf" accept=".pdf,application/pdf">
            <small style="color:var(--muted);">Máximo 50 MB. Al subir un nuevo archivo reemplaza el anterior automáticamente.</small>
        </div>

        {{-- URL externa --}}
        <div class="admin-form-group full">
            <label for="url">O bien: URL externa</label>
            <input type="url" id="url" name="url"
                   value="{{ old('url', ($item->url && str_starts_with($item->url, 'http')) ? $item->url : '') }}"
                   maxlength="500"
                   placeholder="https://...">
            <small style="color:var(--muted);">Solo si el archivo está alojado en otro servidor. Si subís un PDF arriba, este campo se ignora.</small>
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden de aparición</label>
            <input type="number" id="orden" name="orden"
                   value="{{ old('orden', $item->orden ?? 0) }}"
                   min="0" max="9999">
            <small style="color:var(--muted);">Número más bajo aparece primero.</small>
        </div>

    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">
            {{ $modo === 'crear' ? 'Crear material' : 'Guardar cambios' }}
        </button>
        <a href="{{ route('admin.carnet.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

@endsection
