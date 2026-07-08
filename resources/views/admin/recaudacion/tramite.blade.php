@extends('layouts.app')

@section('title', 'Editar Trámite Online — Recaudación')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Editar Trámite Online</h2>
        <p class="admin-subtitle">Guía de Trámites de Recaudación</p>
    </div>
    <a href="{{ route('admin.recaudacion.index') }}" class="btn btn-secondary">← Volver</a>
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

<form action="{{ route('admin.recaudacion.tramite.update') }}"
      method="POST"
      class="admin-form-card">
    @csrf
    @method('PUT')

    <div class="admin-form-grid">

        <div class="admin-form-group full">
            <label for="titulo">Título del trámite <span class="req">*</span></label>
            <input type="text" id="titulo" name="titulo"
                   value="{{ old('titulo', $tramite->titulo) }}"
                   required maxlength="255">
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"
                      rows="3" maxlength="1000"
                      placeholder="Texto informativo que aparece debajo del título...">{{ old('descripcion', $tramite->descripcion) }}</textarea>
        </div>

        <div class="admin-form-group full">
            <label for="url">Link del trámite online</label>
            <input type="url" id="url" name="url"
                   value="{{ old('url', $tramite->url) }}"
                   maxlength="500"
                   placeholder="https://tramites.chacabuco.gob.ar/...">
            <small style="color:var(--muted);">Dejá en blanco si aún no está disponible online.</small>
        </div>

    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('admin.recaudacion.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

@endsection
