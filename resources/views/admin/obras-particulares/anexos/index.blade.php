@extends('layouts.app')

@section('title', 'Formularios / Anexos — Obras Particulares')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Formularios / Anexos</h2>
        <p class="admin-subtitle">Formularios descargables que se referencian en los procedimientos (Anexo 1 al 4 y más).</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.obras.anexos.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo formulario
        </a>
        <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@if($anexos->isEmpty())
    <div class="admin-list-item">
        <p>No hay formularios cargados. <a href="{{ route('admin.obras.anexos.create') }}">Crear el primero</a>.</p>
    </div>
@else
    <div class="admin-list">
        @foreach($anexos as $anexo)
            <article class="admin-list-item">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <h3 style="margin:0;">{{ $anexo->nombre }}</h3>
                    </div>
                    <div class="meta-noticia">
                        <span>Orden: {{ $anexo->orden }}</span>
                        @if($anexo->archivo_nombre)
                            <span><i class="fa-solid fa-file-pdf fa-xs"></i> {{ $anexo->archivo_nombre }}</span>
                            <span>{{ $anexo->archivo_peso_legible }}</span>
                        @endif
                        @if($anexo->descripcion)
                            <span>{{ $anexo->descripcion }}</span>
                        @endif
                    </div>
                    @if($anexo->archivo_ruta)
                        <small class="fecha" style="margin-top:4px;display:block;">
                            URL: <code>{{ $anexo->archivo_ruta }}</code>
                        </small>
                    @endif
                </div>
                <div class="admin-actions">
                    @if($anexo->archivo_ruta)
                        <a href="{{ $anexo->archivo_ruta }}" target="_blank" class="btn btn-secondary">Ver</a>
                    @endif
                    <a href="{{ route('admin.obras.anexos.edit', $anexo) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('admin.obras.anexos.destroy', $anexo) }}" method="POST"
                          data-confirm="¿Eliminar este formulario?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Eliminar</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="paginacion">{{ $anexos->links('vendor.pagination.custom') }}</div>
@endif
@endsection
