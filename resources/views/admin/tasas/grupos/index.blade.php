@extends('layouts.app')

@section('title', 'Grupos de Tasas — Admin')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Grupos de tasas</h2>
        <p class="admin-subtitle">Administrá los grupos de tasas municipales.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.tasas.grupos.create') }}" class="btn btn-primary">Nuevo grupo</a>
        <a href="{{ route('admin.tasas.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

<div class="admin-list">
    @foreach($grupos as $grupo)
    <div class="admin-list-item">
        <div>
            <h3 style="margin:0 0 4px;">{{ $grupo->nombre }}</h3>
            <div class="meta-noticia">
                <span>Código: <strong>{{ $grupo->codigo }}</strong></span>
                <span>Orden: {{ $grupo->orden }}</span>
                <span class="badge-estado {{ $grupo->estado === 'visible' ? 'badge-publicado' : 'badge-oculto' }}">
                    {{ $grupo->estado === 'visible' ? 'Visible' : 'Oculto' }}
                </span>
            </div>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.tasas.grupos.show', $grupo) }}" class="btn btn-primary">
                Gestionar vencimientos
            </a>
            <a href="{{ route('admin.tasas.grupos.edit', $grupo) }}" class="btn btn-secondary">Editar</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
