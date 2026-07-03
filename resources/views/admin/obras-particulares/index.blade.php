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
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="ops-admin-hub">
    <a href="{{ route('admin.obras.normativas.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-scale-balanced"></i></div>
        <div class="ops-admin-card__info">
            <strong>Normativas</strong>
            <span>{{ $totalNormativas }} {{ $totalNormativas === 1 ? 'documento' : 'documentos' }}</span>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.anexos.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-file-contract"></i></div>
        <div class="ops-admin-card__info">
            <strong>Formularios / Anexos</strong>
            <span>{{ $totalAnexos }} {{ $totalAnexos === 1 ? 'formulario' : 'formularios' }}</span>
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.obras.procedimientos.index') }}" class="ops-admin-card">
        <div class="ops-admin-card__icon"><i class="fa-solid fa-list-check"></i></div>
        <div class="ops-admin-card__info">
            <strong>Procedimientos</strong>
            <span>{{ $totalProcedimientos }} {{ $totalProcedimientos === 1 ? 'sección' : 'secciones' }}</span>
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
        </div>
        <i class="fa-solid fa-chevron-right ops-admin-card__arrow"></i>
    </a>
</div>

<div class="ops-admin-tip">
    <i class="fa-solid fa-circle-info"></i>
    <p>
        <strong>Normativas:</strong> ordenanzas y leyes descargables (PDF) para las secciones Obras y Balcones.<br>
        <strong>Formularios / Anexos:</strong> los formularios compartidos (Anexo 1 al 4) que se referencian en los procedimientos.<br>
        <strong>Procedimientos:</strong> el contenido de cada accordion (Obras A–F, Balcones, Mensura y Subdivisión, Libre Deuda).
    </p>
</div>
@endsection
