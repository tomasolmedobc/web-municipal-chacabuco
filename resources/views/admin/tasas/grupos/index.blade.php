@extends('layouts.app')

@section('title', 'Grupos de Tasas — Admin')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Grupos de tasas</h2>
        <p class="admin-subtitle">Los 5 grupos son fijos. Solo podés editar su nombre, orden y visibilidad.</p>
    </div>
    <a href="{{ route('admin.tasas.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if(session('ok'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
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
            <a href="{{ route('admin.tasas.cuotas.index', ['grupo_id' => $grupo->id]) }}" class="btn btn-secondary">
                Ver cuotas
            </a>
            <a href="{{ route('admin.tasas.grupos.edit', $grupo) }}" class="btn btn-secondary">Editar</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
