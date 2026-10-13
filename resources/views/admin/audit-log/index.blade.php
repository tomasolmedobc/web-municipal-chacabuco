@extends('layouts.app')

@section('title', 'Auditoría')

@php
    // Etiquetas legibles de las acciones (las de acceso se guardan como login, login_fallido, etc.)
    $etiquetas = [
        'login'           => 'Inicio de sesión',
        'logout'          => 'Cierre de sesión',
        'login_fallido'   => 'Intento fallido',
        'login_bloqueado' => 'Bloqueo',
    ];
    $etiqueta = fn ($accion) => $etiquetas[$accion] ?? ucfirst($accion);
@endphp

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Auditoría</h2>
            <p class="admin-subtitle">
                @if($tab === 'accesos')
                    Inicios y cierres de sesión, intentos fallidos y bloqueos de cuentas.
                @else
                    Registro de cambios realizados por los administradores.
                @endif
            </p>
        </div>

        <div class="admin-btn-bar">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    <nav class="admin-tabs" aria-label="Secciones de Auditoría">
        <a href="{{ route('admin.audit-log.index', ['tab' => 'cambios']) }}"
           class="admin-tab {{ $tab === 'cambios' ? 'is-active' : '' }}">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Cambios</span>
            <strong>{{ $totales['cambios'] }}</strong>
        </a>
        <a href="{{ route('admin.audit-log.index', ['tab' => 'accesos']) }}"
           class="admin-tab {{ $tab === 'accesos' ? 'is-active' : '' }}">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Accesos</span>
            <strong>{{ $totales['accesos'] }}</strong>
        </a>
    </nav>

    <form method="GET" action="{{ route('admin.audit-log.index') }}" class="filtros">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="{{ $tab === 'accesos' ? 'Buscar por correo, usuario o descripción...' : 'Buscar por descripción o usuario...' }}"
            class="filtro-input filtro-input-busqueda"
        >

        <select name="accion" class="filtro-input">
            <option value="">Todas las acciones</option>
            @foreach($acciones as $accion)
                <option value="{{ $accion }}" @selected(request('accion') === $accion)>
                    {{ $etiqueta($accion) }}
                </option>
            @endforeach
        </select>

        @if($tab === 'cambios')
            <select name="modelo" class="filtro-input">
                <option value="">Todos los modelos</option>
                @foreach($modelos as $modelo)
                    <option value="{{ $modelo }}" @selected(request('modelo') === $modelo)>
                        {{ $modelo }}
                    </option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="boton-filtro">Filtrar</button>
        <a href="{{ route('admin.audit-log.index', ['tab' => $tab]) }}" class="boton-limpiar">Limpiar</a>
    </form>

    @if($logs->count() === 0)
        <div class="admin-list-item">
            <div>
                <h3>No hay registros</h3>
                <p>
                    {{ $tab === 'accesos' ? 'Aún no se registraron accesos' : 'Aún no se registraron cambios' }},
                    o ninguno coincide con los filtros.
                </p>
            </div>
        </div>
    @endif

    <div class="admin-list">
        @foreach($logs as $log)
            <article class="admin-list-item">
                <div>
                    <div class="admin-meta-row mb-6">
                        <span class="audit-badge audit-badge--{{ $log->accion }}">
                            {{ $etiqueta($log->accion) }}
                        </span>
                        @if($tab === 'cambios')
                            <span class="badge-categoria">{{ $log->modelo }}</span>
                            @if($log->modelo_id)
                                <span class="audit-id">#{{ $log->modelo_id }}</span>
                            @endif
                        @endif
                    </div>

                    <p class="item-title-p">{{ $log->descripcion }}</p>

                    <div class="meta-noticia">
                        @if($log->user_nombre)
                            <span>
                                <i class="fa-solid fa-user" class="icon-sm"></i>
                                {{ $log->user_nombre }}
                            </span>
                        @endif

                        <span>
                            <i class="fa-solid fa-clock" class="icon-sm"></i>
                            {{ $log->created_at?->format('d/m/Y H:i:s') }}
                        </span>

                        @if($log->ip)
                            <span>
                                <i class="fa-solid fa-globe" class="icon-sm"></i>
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
