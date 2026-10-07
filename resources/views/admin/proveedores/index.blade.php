@extends('layouts.app')

@section('title', 'Compras y Proveedores — Admin')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Compras y Proveedores</h2>
        <p class="admin-subtitle">Gestioná el contenido de la página de proveedores.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver al panel</a>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="tasas-admin-hub">
    <a href="{{ route('admin.proveedores.config.edit') }}" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-gear"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Configuración general</strong>
            <span>Monto del sellado y contenido de las secciones</span>
        </div>
        <i class="fa-solid fa-chevron-right tasas-admin-card__arrow"></i>
    </a>

    <a href="{{ route('proveedores.index') }}" target="_blank" rel="noopener" class="tasas-admin-card">
        <div class="tasas-admin-card__icon"><i class="fa-solid fa-eye"></i></div>
        <div class="tasas-admin-card__info">
            <strong>Ver página pública</strong>
            <span>Abrir en nueva pestaña</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square tasas-admin-card__arrow"></i>
    </a>
</div>
@endsection
