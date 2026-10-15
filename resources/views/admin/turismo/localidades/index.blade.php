@extends('layouts.app')

@section('title', 'Localidades - Turismo')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Localidades</h2>
            <p class="admin-subtitle">Editá la historia, descripción e imagen de portada de cada localidad.</p>
        </div>

        <div class="admin-btn-bar">
            <a href="{{ route('admin.turismo.localidades.create') }}" class="btn btn-primary">
                Crear localidad
            </a>

            <a href="{{ route('admin.turismo.index') }}" class="btn btn-secondary">
                Turismo
            </a>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </section>

    @if(session('ok'))
        <script @nonce>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('ok')), 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script @nonce>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('error')), 'error');
            });
        </script>
    @endif

    <div class="admin-list">
        @foreach($localidades as $localidad)
            <article class="admin-list-item">
                <div>
                    <div class="admin-meta-row mb-6">
                        <h3 class="h3-flush">{{ $localidad->nombre }}</h3>

                        @if($localidad->es_cabecera)
                            <span class="badge-categoria">Ciudad cabecera</span>
                        @endif

                        <span class="badge-estado {{ $localidad->estado === 'visible' ? 'badge-publicado' : 'badge-oculto' }}">
                            {{ $localidad->estado === 'visible' ? '✔ Visible' : '⚠ Oculto' }}
                        </span>
                    </div>

                    <div class="meta-noticia">
                        <span>
                            <i class="fa-solid fa-arrow-down-1-9"></i>
                            Orden {{ $localidad->orden }}
                        </span>
                        <span>
                            <i class="fa-solid fa-map-location-dot"></i>
                            {{ $localidad->turismo_items_count }} atractivo(s) o evento(s)
                        </span>
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('admin.turismo.index', ['localidad_id' => $localidad->id]) }}" class="btn btn-secondary">
                        Ver atractivos
                    </a>
                    <a href="{{ route('admin.turismo.localidades.edit', $localidad) }}" class="btn btn-secondary">
                        Editar
                    </a>

                    @if(! $localidad->es_cabecera)
                        <form action="{{ route('admin.turismo.localidades.destroy', $localidad) }}"
                              method="POST"
                              data-confirm="¿Eliminar la localidad &quot;{{ $localidad->nombre }}&quot;?">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger"
                                    @if($localidad->turismo_items_count > 0)
                                        disabled
                                        title="Tiene atractivos o eventos: eliminalos o pasalos a otra localidad primero."
                                    @endif>
                                Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endsection
