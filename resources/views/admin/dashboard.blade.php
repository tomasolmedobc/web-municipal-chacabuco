@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
<div class="admin-stats">
    <div class="admin-stat-card">
        <h3>Total noticias</h3>
        <p>{{ $stats['noticias_total'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Publicadas</h3>
        <p>{{ $stats['noticias_publicadas'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Ocultas</h3>
        <p>{{ $stats['noticias_ocultas'] }}</p>
    </div>

    @if(auth()->user()->rol === 'admin')
    <div class="admin-stat-card">
        <h3>Gobierno abierto</h3>
        <p>{{ $stats['gobierno_abierto_total'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Habilitaciones</h3>
        <p>{{ $stats['habilitaciones_total'] }}</p>
    </div>

    <div class="admin-stat-card">
        <h3>Usuarios</h3>
        <p>{{ $stats['usuarios_total'] }}</p>
    </div>
    @endif
</div>

    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Panel de administración</h2>
            <p class="admin-subtitle">Gestioná el contenido y la configuración del sitio municipal.</p>
        </div>
    </section>

    <div class="admin-modules">

        <section class="admin-module-group">
            <h3 class="admin-module-group__title"><i class="fa-solid fa-newspaper"></i> Contenido</h3>
            <div class="admin-module-grid">
                <a href="{{ route('admin.noticias.index') }}" class="admin-module-card admin-module-card--primary">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-newspaper"></i></span>
                    <span class="admin-module-card__label">Noticias</span>
                    <span class="admin-module-card__desc">Listado, filtros y gestión de noticias</span>
                </a>
                <a href="{{ route('admin.noticias.create') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-file-pen"></i></span>
                    <span class="admin-module-card__label">Nueva noticia</span>
                    <span class="admin-module-card__desc">Publicá un comunicado o novedad municipal</span>
                </a>
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('admin.gobierno-abierto.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-landmark"></i></span>
                    <span class="admin-module-card__label">Gobierno abierto</span>
                    <span class="admin-module-card__desc">Licitaciones, nóminas y documentos institucionales</span>
                </a>
                @endif
                <a href="{{ route('admin.turismo.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-map-location-dot"></i></span>
                    <span class="admin-module-card__label">Turismo</span>
                    <span class="admin-module-card__desc">Localidades y eventos del municipio</span>
                </a>
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('admin.baile.usuarios.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-champagne-glasses"></i></span>
                    <span class="admin-module-card__label">Baile de Egresados</span>
                    <span class="admin-module-card__desc">Inscripciones y gestión del evento</span>
                </a>
                @endif
            </div>
        </section>

        @if(auth()->user()->rol === 'admin')
        <section class="admin-module-group">
            <h3 class="admin-module-group__title"><i class="fa-solid fa-folder-open"></i> Gestión</h3>
            <div class="admin-module-grid">
                <a href="{{ route('admin.habilitaciones.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-store"></i></span>
                    <span class="admin-module-card__label">Habilitaciones</span>
                    <span class="admin-module-card__desc">Permisos y habilitaciones comerciales</span>
                </a>
                <a href="{{ route('admin.obras.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-helmet-safety"></i></span>
                    <span class="admin-module-card__label">Obras Particulares</span>
                    <span class="admin-module-card__desc">Solicitudes de obras y planos</span>
                </a>
                <a href="{{ route('admin.tasas.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span class="admin-module-card__label">Tasas Municipales</span>
                    <span class="admin-module-card__desc">Información y vencimientos de tasas</span>
                </a>
                <a href="{{ route('admin.recaudacion.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span class="admin-module-card__label">Recaudación</span>
                    <span class="admin-module-card__desc">Estadísticas y datos de recaudación</span>
                </a>
            </div>
        </section>
        @endif

        <section class="admin-module-group">
            <h3 class="admin-module-group__title"><i class="fa-solid fa-sliders"></i> Configuración del sitio</h3>
            <div class="admin-module-grid">
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('admin.popup.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-bullhorn"></i></span>
                    <span class="admin-module-card__label">Popup anuncio</span>
                    <span class="admin-module-card__desc">Cartel de anuncio en la página principal</span>
                </a>
                @endif
                <a href="{{ route('admin.telefonos-utiles.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-phone"></i></span>
                    <span class="admin-module-card__label">Teléfonos Útiles</span>
                    <span class="admin-module-card__desc">Contactos de emergencia y dependencias</span>
                </a>
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('admin.carnet.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-id-card"></i></span>
                    <span class="admin-module-card__label">Carnet de Conducir</span>
                    <span class="admin-module-card__desc">Materiales y documentación para tramitar el carnet</span>
                </a>
                @endif
            </div>
        </section>

        @if(auth()->user()->rol === 'admin')
        <section class="admin-module-group">
            <h3 class="admin-module-group__title"><i class="fa-solid fa-shield-halved"></i> Sistema</h3>
            <div class="admin-module-grid">
                <a href="{{ route('admin.audit-log.index') }}" class="admin-module-card">
                    <span class="admin-module-card__icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    <span class="admin-module-card__label">Auditoría</span>
                    <span class="admin-module-card__desc">Registro de todas las acciones en el sistema</span>
                </a>
            </div>
        </section>
        @endif

    </div>

    <form method="GET" action="{{ route('admin.dashboard') }}" class="filtros">
        <input
            type="text"
            name="q"
            value="{{ $busqueda ?? '' }}"
            placeholder="Buscar por título, contenido, slug o autor..."
            class="filtro-input filtro-input-busqueda"
        >

        <button type="submit" class="boton-filtro">Buscar</button>
        <a href="{{ route('admin.dashboard') }}" class="boton-limpiar">Limpiar</a>
    </form>

    @if(session('ok'))
        <script @nonce>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('ok')), 'success');
            });
        </script>
    @endif

    @if(($busqueda ?? null) && $noticias->count() > 0)
        <p class="fecha" style="margin-bottom: 18px;">
            Resultados para: <strong>{{ $busqueda }}</strong>
        </p>
    @endif

    @if($noticias->count() === 0)
        <div class="admin-list-item">
            <div>
                <h3>No se encontraron noticias</h3>
                <p>Probá con otra búsqueda.</p>
            </div>
        </div>
    @endif

    <div class="admin-list">
        @foreach($noticias as $noticia)
            <article class="admin-list-item">
                <div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <h3 style="margin:0;">{{ $noticia->titulo }}</h3>

                @if($noticia->categorias->count())
                    <span class="badge-categoria">
                        {{ $noticia->categorias->first()->nombre }}
                    </span>
                @endif
            </div>
                    <div class="meta-noticia">
                        <span>{{ $noticia->fecha?->format('d/m/Y H:i') }}</span>

                        <span class="badge-estado {{ $noticia->estado === 'publicado' ? 'badge-publicado' : 'badge-oculto' }}">
                            {{ $noticia->estado === 'publicado' ? '✔ Publicado' : '⚠ Oculto' }}
                        </span>

                        @if($noticia->autor)
                            <span>{{ $noticia->autor }}</span>
                        @endif
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('noticias.show', $noticia->slug) }}" class="btn btn-secondary">Ver</a>

                    <form action="{{ route('admin.noticias.toggleStatus', $noticia) }}" method="POST" class="form-toggle-estado">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="btn {{ $noticia->estado === 'publicado' ? 'btn-estado-ocultar' : 'btn-estado-publicar' }}"
                        >
                            {{ $noticia->estado === 'publicado' ? 'Ocultar' : 'Publicar' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.noticias.edit', $noticia) }}" class="btn btn-secondary">Editar</a>

                    <form action="{{ route('admin.noticias.destroy', $noticia) }}" method="POST" class="form-eliminar-noticia" data-confirm="¿Seguro que querés eliminar esta noticia?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary">Eliminar</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>

    <div class="paginacion">
        {{ $noticias->links('vendor.pagination.custom') }}
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
