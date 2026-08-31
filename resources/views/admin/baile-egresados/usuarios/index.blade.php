@extends('layouts.app')

@section('title', 'Usuarios — Baile de Egresados')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Baile de Egresados — Usuarios</h2>
        <p class="admin-subtitle">Administrá los usuarios habilitados para reservar asientos.</p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.baile.usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
        <a href="{{ route('admin.baile.asientos.index') }}" class="btn btn-secondary">Asientos</a>
        <a href="{{ route('admin.baile.reservas.index') }}" class="btn btn-secondary">Reservas</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

<form method="GET" action="{{ route('admin.baile.usuarios.index') }}" class="filtros">
    <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre o DNI">
    <button type="submit" class="btn btn-primary">Buscar</button>
    @if($busqueda)
        <a href="{{ route('admin.baile.usuarios.index') }}" class="boton-limpiar">Limpiar</a>
    @endif
</form>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($usuarios->count() === 0)
    <div class="admin-empty">
        <h3>No hay usuarios cargados</h3>
        <p>Creá el primero desde el botón superior.</p>
    </div>
@endif

<div class="admin-list">
    @foreach($usuarios as $usuario)
        <article class="admin-list-item">
            <div>
                <h3>{{ $usuario->nombre_completo }}</h3>
                <div class="meta-noticia">
                    <span><i class="fa-solid fa-id-card"></i> DNI: {{ $usuario->dni }}</span>
                    <span><i class="fa-solid fa-key"></i> Código: {{ $usuario->codigo }}</span>
                    <span><i class="fa-solid fa-couch"></i> Disponibles: {{ $usuario->disponibles }}</span>
                    <span><i class="fa-solid fa-ticket"></i> Reservas: {{ $usuario->reservas_count }}</span>
                </div>
            </div>

            <div class="admin-actions">
                @if($usuario->reservas_count > 0)
                    <a href="{{ route('admin.baile.reservas.index', ['q' => $usuario->nombre_completo]) }}"
                       class="btn btn-secondary">
                        Ver reservas ({{ $usuario->reservas_count }})
                    </a>
                @endif
                <a href="{{ route('admin.baile.usuarios.edit', $usuario) }}" class="btn btn-secondary">Editar</a>

                <form action="{{ route('admin.baile.usuarios.destroy', $usuario) }}"
                      method="POST"
                      class="form-eliminar-gobierno-abierto"
                      data-confirm="¿Eliminar a {{ $usuario->nombre_completo }}? Esto borrará todas sus reservas.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </article>
    @endforeach
</div>

@if($usuarios->hasPages())
    <div class="paginacion">{{ $usuarios->links('vendor.pagination.custom') }}</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
