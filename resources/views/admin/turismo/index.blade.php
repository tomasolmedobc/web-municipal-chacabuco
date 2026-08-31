@extends('layouts.app')

@section('title', 'Turismo')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Turismo</h2>
        <p class="admin-subtitle">
            Gestiona atractivos turísticos, gastronomía y eventos por localidad.
        </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.turismo.create', array_filter(['tipo' => $tipoActivo, 'localidad_id' => $localidadId])) }}"
           class="btn btn-primary">
            Nuevo {{ $config['singular'] }}
        </a>

<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            Volver
        </a>
    </div>
</section>

<div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
    <a href="{{ route('admin.turismo.localidades.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-map-pin"></i> Gestionar localidades
    </a>
</div>

<nav class="admin-tabs" aria-label="Tipos de Turismo">
    @foreach($tipos as $tipo => $item)
        <a href="{{ route('admin.turismo.index', ['tipo' => $tipo]) }}"
           class="admin-tab {{ $tipoActivo === $tipo ? 'is-active' : '' }}">
            <i class="fa-solid {{ $item['icono'] }}"></i>
            <span>{{ $item['titulo'] }}</span>
            <strong>{{ $totales[$tipo] ?? 0 }}</strong>
        </a>
    @endforeach
</nav>

<form method="GET" action="{{ route('admin.turismo.index') }}" class="filtros">
    <input type="hidden" name="tipo" value="{{ $tipoActivo }}">

    <input type="text"
           name="q"
           value="{{ $busqueda }}"
           placeholder="Buscar por título, categoría o descripción">

    <select name="localidad_id" class="filtro-input">
        <option value="">Todas las localidades</option>
        @foreach($localidades as $localidad)
            <option value="{{ $localidad->id }}" @selected((string) $localidadId === (string) $localidad->id)>
                {{ $localidad->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>

    @if($busqueda || $localidadId)
        <a href="{{ route('admin.turismo.index', ['tipo' => $tipoActivo]) }}" class="boton-limpiar">
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

@if($items->count() === 0)
    <div class="admin-empty">
        <h3>No hay registros cargados</h3>
        <p>Crea el primer {{ $config['singular'] }} desde el botón superior.</p>
    </div>
@endif

<div class="admin-list">
    @foreach($items as $item)
        <article class="admin-list-item">
            <div>
                <div class="hero-top" style="margin-bottom:10px;">
                    <span class="licitacion-badge badge-{{ $item->estado === 'visible' ? 'activa' : 'finalizada' }}">
                        {{ ucfirst($item->estado) }}
                    </span>

                    @if($item->categoria)
                        <span class="hero-badge">{{ $item->categoria }}</span>
                    @endif

                    @if($item->destacado)
                        <span class="hero-badge">Destacado</span>
                    @endif
                </div>

                <h3>{{ $item->titulo }}</h3>

                @if($item->descripcion)
                    <p class="admin-subtitle">
                        {{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($item->descripcion), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 180) }}
                    </p>
                @endif

                <div class="meta-noticia">
                    <span>
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $item->localidad?->nombre }}
                    </span>

                    @if($item->tipo === \App\Models\TurismoItem::TIPO_EVENTO && $item->fecha_inicio)
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $item->fecha_inicio->format('d/m/Y') }}
                            @if($item->hora_inicio) {{ substr($item->hora_inicio, 0, 5) }} hs @endif
                            @if($item->fecha_fin) — {{ $item->fecha_fin->format('d/m/Y') }} @endif
                        </span>
                    @endif

                    <span>
                        <i class="fa-solid fa-arrow-down-1-9"></i>
                        Orden {{ $item->orden }}
                    </span>
                </div>
            </div>

            <div class="admin-actions">
                <a href="{{ route('admin.turismo.edit', $item) }}" class="btn btn-secondary">
                    Editar
                </a>

                <form action="{{ route('admin.turismo.destroy', $item) }}"
                      method="POST"
                      class="form-eliminar-gobierno-abierto"
                      data-confirm="¿Seguro que querés eliminar este registro?">
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

@if($items->hasPages())
    <div class="paginacion">
        {{ $items->links('vendor.pagination.custom') }}
    </div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
