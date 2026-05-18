@extends('layouts.app')

@section('title', 'Licitaciones')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Licitaciones</h2>

        <p class="admin-subtitle">
            Gestioná licitaciones públicas y privadas del municipio.
        </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.licitaciones.create') }}"
           class="btn btn-primary">
            Nueva licitación
        </a>

        <a href="{{ route('admin.dashboard') }}"
           class="btn btn-secondary">
            Volver
        </a>
    </div>
</section>

@if(session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

@if($licitaciones->count() === 0)

    <div class="admin-empty">
        <h3>No hay licitaciones cargadas</h3>

        <p>
            Creá la primera licitación desde el botón superior.
        </p>
    </div>

@endif

<div class="admin-list">

    @foreach($licitaciones as $licitacion)

        <article class="admin-list-item">

            <div>

                <div class="hero-top" style="margin-bottom:10px;">

                    <span class="licitacion-badge badge-{{ $licitacion->tipo }}">
                        {{ ucfirst($licitacion->tipo) }}
                    </span>

                    <span class="licitacion-badge badge-{{ $licitacion->estado }}">
                        {{ ucfirst($licitacion->estado) }}
                    </span>

                </div>

                <h3>
                    {{ $licitacion->titulo }}
                </h3>

                @if($licitacion->descripcion)
                    <p class="admin-subtitle">
                        {{ \Illuminate\Support\Str::limit($licitacion->descripcion, 180) }}
                    </p>
                @endif

                <div class="meta-noticia">

                    @if($licitacion->numero_expediente)
                        <span>
                            📁 Expte:
                            {{ $licitacion->numero_expediente }}
                        </span>
                    @endif

                    @if($licitacion->anio)
                        <span>
                            📅 {{ $licitacion->anio }}
                        </span>
                    @endif

                    @if($licitacion->fecha_publicacion)
                        <span>
                            🕒 {{ $licitacion->fecha_publicacion->format('d/m/Y') }}
                        </span>
                    @endif

                </div>

            </div>

            <div class="admin-actions">

                @if($licitacion->archivo_ruta)
                    <a href="{{ $licitacion->archivo_ruta }}"
                       target="_blank"
                       class="btn btn-secondary">
                        PDF
                    </a>
                @endif

                <a href="{{ route('admin.licitaciones.edit', $licitacion) }}"
                   class="btn btn-secondary">
                    Editar
                </a>

                <form action="{{ route('admin.licitaciones.destroy', $licitacion) }}"
                      method="POST"
                      class="form-eliminar-licitacion"
                      data-confirm="Â¿Seguro que querÃ©s eliminar esta licitaciÃ³n?">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">
                        Eliminar
                    </button>

                </form>

            </div>

        </article>

    @endforeach

</div>

@if($licitaciones->hasPages())

    <div class="paginacion">
        {{ $licitaciones->links('vendor.pagination.custom') }}
    </div>

@endif

@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
