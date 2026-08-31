@extends('layouts.app')

@section('title', 'Obras Particulares — Admin')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Dirección de Obras Particulares</h2>
        <p class="admin-subtitle">Gestioná normativas, formularios (anexos) y el contenido de cada procedimiento.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver al panel</a>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="ops-admin-tip" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-info"></i>
    <p>
        <strong>¿Cómo empezar?</strong> Seguí este orden para cargar el módulo por primera vez:<br>
        <strong>Paso 1 — Formularios / Anexos:</strong> subí los formularios primero. Sus URLs se insertan como links dentro del contenido de los Procedimientos.<br>
        <strong>Paso 2 — Categorías:</strong> creá las secciones de la página pública (ej: Obras, Balcones, Mensura). Podés agregarlas, editarlas o eliminarlas cuando quieras.<br>
        <strong>Paso 3 — Procedimientos:</strong> agregá los acordeones de cada categoría con su contenido y requisitos.<br>
        <strong>Paso 4 — Normativas:</strong> subí ordenanzas en PDF. Podés asignarlas a cualquier categoría.<br>
        <strong>Paso 5 — Registro de Profesionales:</strong> configurá el enlace del formulario de inscripción (opcional).
    </p>
</div>

<div class="ops-admin-hub">
    <a href="{{ route('admin.obras.anexos.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-file-contract"></i></div>
        <div class="ops-admin-card__info">
            <strong>Formularios / Anexos</strong>
            <span>{{ $totalAnexos }} {{ $totalAnexos === 1 ? 'formulario' : 'formularios' }}</span>
            <small style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Paso 1 — empezá aquí</small>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.categorias.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-folder-tree"></i></div>
        <div class="ops-admin-card__info">
            <strong>Categorías</strong>
            <span>{{ $totalCategorias }} {{ $totalCategorias === 1 ? 'categoría' : 'categorías' }}</span>
            <small style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Paso 2 — creá las secciones</small>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.procedimientos.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-list-check"></i></div>
        <div class="ops-admin-card__info">
            <strong>Procedimientos</strong>
            <span>{{ $totalProcedimientos }} {{ $totalProcedimientos === 1 ? 'procedimiento' : 'procedimientos' }}</span>
            <small style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Paso 3 — acordeones por categoría</small>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.normativas.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-scale-balanced"></i></div>
        <div class="ops-admin-card__info">
            <strong>Normativas</strong>
            <span>{{ $totalNormativas }} {{ $totalNormativas === 1 ? 'documento' : 'documentos' }}</span>
            <small style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Paso 4 — PDFs por categoría</small>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.config.edit') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-id-card-clip"></i></div>
        <div class="ops-admin-card__info">
            <strong>Registro de Profesionales</strong>
            <span>
                @if($config->registro_tipo === 'url' && $config->registro_url)
                    Enlace configurado
                @elseif($config->registro_tipo === 'archivo' && $config->registro_archivo_nombre)
                    Archivo: {{ $config->registro_archivo_nombre }}
                @else
                    Sin configurar
                @endif
            </span>
            <small style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Paso 5 — opcional</small>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>
</div>
@endsection
