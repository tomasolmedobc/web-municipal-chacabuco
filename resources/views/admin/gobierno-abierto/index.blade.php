@extends('layouts.app')

@section('title', 'Gobierno Abierto')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Gobierno Abierto</h2>
        <p class="admin-subtitle">
            Gestiona licitaciones, gastos, recursos, balances y accesos directos desde un mismo modulo.
        </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.gobierno-abierto.create', ['categoria' => $categoriaActiva]) }}"
           class="btn btn-primary">
            Nuevo {{ $config['singular'] }}
        </a>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            Volver
        </a>
    </div>
</section>

<nav class="admin-tabs" aria-label="Secciones de Gobierno Abierto">
    @foreach($categorias as $categoria => $item)
        <a href="{{ route('admin.gobierno-abierto.index', ['categoria' => $categoria]) }}"
           class="admin-tab {{ $categoriaActiva === $categoria ? 'is-active' : '' }}">
            <i class="fa-solid {{ $item['icono'] }}"></i>
            <span>{{ $item['titulo'] }}</span>
            <strong>{{ $totales[$categoria] ?? 0 }}</strong>
        </a>
    @endforeach
</nav>

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
                    @if($categoriaActiva === \App\Models\Licitacion::CATEGORIA_LICITACIONES)
                        <span class="licitacion-badge badge-{{ $documento->tipo }}">
                            {{ $documento->tipo === 'publica' ? 'Publica' : 'Privada' }}
                        </span>
                    @endif

                    <span class="licitacion-badge badge-{{ $documento->estado }}">
                        {{ ucfirst($documento->estado) }}
                    </span>
                </div>

                <h3>{{ $documento->titulo }}</h3>

                @if($documento->descripcion)
                    <p class="admin-subtitle">
                        {{ \Illuminate\Support\Str::limit($documento->descripcion, 180) }}
                    </p>
                @endif

                <div class="meta-noticia">
                    @if($documento->archivos->count())
                        <span>
                            <i class="fa-solid fa-paperclip"></i>
                            {{ $documento->archivos->count() }} archivo(s)
                        </span>
                    @endif

                    @if($categoriaActiva === \App\Models\Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS && $documento->link_externo)
                        <span>
                            <i class="fa-solid fa-link"></i>
                            Link externo
                        </span>
                    @endif

                    @if($documento->numero_expediente)
                        <span>
                            <i class="fa-solid fa-folder-open"></i>
                            Expte: {{ $documento->numero_expediente }}
                        </span>
                    @endif

                    @if($documento->anio)
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $documento->anio }}
                        </span>
                    @endif

                    @if($documento->fecha_publicacion)
                        <span>
                            <i class="fa-solid fa-clock"></i>
                            {{ $documento->fecha_publicacion->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="admin-actions">
                @if($documento->archivos->count())
                    <a href="{{ $documento->archivos->first()->ruta }}" target="_blank" class="btn btn-secondary">
                        Ver archivo
                    </a>
                @elseif($documento->link_externo)
                    <a href="{{ $documento->link_externo }}" target="_blank" class="btn btn-secondary">
                        Abrir link
                    </a>
                @endif

                <a href="{{ route('admin.gobierno-abierto.edit', $documento) }}"
                   class="btn btn-secondary">
                    Editar
                </a>

                <form action="{{ route('admin.gobierno-abierto.destroy', $documento) }}"
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
