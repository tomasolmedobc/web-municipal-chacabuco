@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Auditoría</h2>
            <p class="admin-subtitle">Registro de acciones realizadas por los administradores.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.audit-log.index') }}" class="filtros">
        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Buscar por descripción o usuario..."
            class="filtro-input filtro-input-busqueda"
        >

        <select name="accion" class="filtro-input">
            <option value="">Todas las acciones</option>
            @foreach($acciones as $accion)
                <option value="{{ $accion }}" @selected(request('accion') === $accion)>
                    {{ ucfirst($accion) }}
                </option>
            @endforeach
        </select>

        <select name="modelo" class="filtro-input">
            <option value="">Todos los modelos</option>
            @foreach($modelos as $modelo)
                <option value="{{ $modelo }}" @selected(request('modelo') === $modelo)>
                    {{ $modelo }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="boton-filtro">Filtrar</button>
        <a href="{{ route('admin.audit-log.index') }}" class="boton-limpiar">Limpiar</a>
    </form>

    @if($logs->count() === 0)
        <div class="admin-list-item">
            <div>
                <h3>No hay registros</h3>
                <p>Aún no se registraron acciones, o ninguna coincide con los filtros.</p>
            </div>
        </div>
    @endif

    <div class="admin-list">
        @foreach($logs as $log)
            <article class="admin-list-item">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
                        <span class="audit-badge audit-badge--{{ $log->accion }}">
                            {{ ucfirst($log->accion) }}
                        </span>
                        <span class="badge-categoria">{{ $log->modelo }}</span>
                        @if($log->modelo_id)
                            <span class="audit-id">#{{ $log->modelo_id }}</span>
                        @endif
                    </div>

                    <p style="margin:0 0 4px; font-weight:600; color:var(--text);">
                        {{ $log->descripcion }}
                    </p>

                    <div class="meta-noticia">
                        @if($log->user_nombre)
                            <span>
                                <i class="fa-solid fa-user" style="font-size:11px;"></i>
                                {{ $log->user_nombre }}
                            </span>
                        @endif

                        <span>
                            <i class="fa-solid fa-clock" style="font-size:11px;"></i>
                            {{ $log->created_at?->format('d/m/Y H:i:s') }}
                        </span>

                        @if($log->ip)
                            <span>
                                <i class="fa-solid fa-globe" style="font-size:11px;"></i>
                                {{ $log->ip }}
                            </span>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    @if($logs->hasPages())
        <div class="paginacion">
            {{ $logs->links('vendor.pagination.custom') }}
        </div>
    @endif
@endsection
