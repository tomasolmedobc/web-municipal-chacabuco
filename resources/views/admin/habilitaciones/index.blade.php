@extends('layouts.app')

@section('title', 'Habilitaciones')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Habilitaciones</h2>
        <p class="admin-subtitle">
            Gestiona formularios, requisitos y ordenanzas que se muestran en la pagina publica.
        </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.habilitaciones.create', ['seccion' => $seccionActiva]) }}"
           class="btn btn-primary">
            Nuevo {{ $config['singular'] }}
        </a>

        <a href="{{ route('admin.habilitaciones.config') }}" class="btn btn-secondary">
            Configuración
        </a>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            Volver
        </a>
    </div>
</section>

<nav class="admin-tabs" aria-label="Secciones de Habilitaciones">
    @foreach($secciones as $seccion => $item)
        <a href="{{ route('admin.habilitaciones.index', ['seccion' => $seccion]) }}"
           class="admin-tab {{ $seccionActiva === $seccion ? 'is-active' : '' }}">
            <i class="fa-solid {{ $item['icono'] }}"></i>
            <span>{{ $item['titulo'] }}</span>
            <strong>{{ $totales[$seccion] ?? 0 }}</strong>
        </a>
    @endforeach
</nav>

<form method="GET" action="{{ route('admin.habilitaciones.index') }}" class="filtros">
    <input type="hidden" name="seccion" value="{{ $seccionActiva }}">

    <input type="text"
           name="q"
           value="{{ $busqueda }}"
           placeholder="{{ $seccionActiva === \App\Models\HabilitacionDocumento::SECCION_ORDENANZAS ? 'Buscar por nombre, numero o anio' : 'Buscar por titulo o categoria' }}">

    <button type="submit" class="btn btn-primary">Buscar</button>

    @if($busqueda)
        <a href="{{ route('admin.habilitaciones.index', ['seccion' => $seccionActiva]) }}" class="boton-limpiar">
            Limpiar
        </a>
    @endif
</form>

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

@if($documentos->count() === 0)
    <div class="admin-empty">
        <h3>No hay registros cargados</h3>
        <p>Crea el primer {{ $config['singular'] }} desde el boton superior.</p>
    </div>
@endif

<div class="admin-list">
    @foreach($documentos as $documento)
        <article class="admin-list-item">
            <div>
                <div class="hero-top" style="margin-bottom:10px;">
                    <span class="licitacion-badge badge-{{ $documento->estado === 'visible' ? 'activa' : 'finalizada' }}">
                        {{ ucfirst($documento->estado) }}
                    </span>

                    @if($documento->categoria)
                        <span class="hero-badge">{{ $documento->categoria }}</span>
                    @endif
                </div>

                <h3>{{ $documento->titulo }}</h3>

                @if($documento->descripcion)
                    <p class="admin-subtitle">
                        {{ \Illuminate\Support\Str::limit($documento->descripcion, 180) }}
                    </p>
                @endif

                <div class="meta-noticia">
                    @if($documento->archivo_ruta)
                        <span>
                            <i class="fa-solid fa-paperclip"></i>
                            {{ $documento->archivo_nombre }} - {{ $documento->archivo_peso_legible }}
                        </span>
                    @elseif($documento->link_externo)
                        <span>
                            <i class="fa-solid fa-link"></i>
                            Link externo
                        </span>
                    @endif

                    @if($documento->numero)
                        <span>
                            <i class="fa-solid fa-hashtag"></i>
                            Ordenanza {{ $documento->numero }}
                        </span>
                    @endif

                    @if($documento->anio)
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $documento->anio }}
                        </span>
                    @endif

                    <span>
                        <i class="fa-solid fa-arrow-down-1-9"></i>
                        Orden {{ $documento->orden }}
                    </span>
                </div>
            </div>

            <div class="admin-actions">
                @if($documento->archivo_ruta)
                    <a href="{{ $documento->archivo_ruta }}" target="_blank" class="btn btn-secondary">
                        Ver archivo
                    </a>
                @elseif($documento->link_externo)
                    <a href="{{ $documento->link_externo }}" target="_blank" class="btn btn-secondary">
                        Abrir link
                    </a>
                @endif

                <a href="{{ route('admin.habilitaciones.edit', $documento) }}"
                   class="btn btn-secondary">
                    Editar
                </a>

                <form action="{{ route('admin.habilitaciones.destroy', $documento) }}"
                      method="POST"
                      class="form-eliminar-gobierno-abierto"
                      data-confirm="Seguro que queres eliminar este registro?">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        Eliminar
                    </button>
                </form>
            </div>
        </article>
    @endforeach
</div>

@if($documentos->hasPages())
    <div class="paginacion">
        {{ $documentos->links('vendor.pagination.custom') }}
    </div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
