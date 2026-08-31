@extends('layouts.app')

@section('title', 'Tasas Municipales — Admin')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Tasas Municipales</h2>
        <p class="admin-subtitle">Gestioná el contenido, botones, banner y fechas de vencimiento de tasas.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver al panel</a>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="tasas-admin-hub">
    <a href="{{ route('admin.tasas.config.edit') }}" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-gear"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Configuración general</strong>
            <span>Textos, botones y banner</span>
        </div>
        <i class="fa-solid fa-chevron-right tasas-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.tasas.grupos.index') }}" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-layer-group"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Grupos de tasas</strong>
            <span>{{ $totalGrupos }} {{ $totalGrupos === 1 ? 'grupo' : 'grupos' }}</span>
        </div>
        <i class="fa-solid fa-chevron-right tasas-admin-card__arrow"></i>
    </a>

    <a href="{{ route('admin.tasas.cuotas.index') }}" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-calendar-days"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Fechas de vencimiento</strong>
            <span>{{ $totalCuotas }} {{ $totalCuotas === 1 ? 'cuota' : 'cuotas' }} cargadas</span>
        </div>
        <i class="fa-solid fa-chevron-right tasas-admin-card__arrow"></i>
    </a>

    <a href="{{ route('tasas.index') }}" target="_blank" rel="noopener" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-eye"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Ver página pública</strong>
            <span>Abrir en nueva pestaña</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square tasas-admin-card__arrow"></i>
    </a>
</div>
@endsection
