@extends('layouts.app')

@section('title', 'Localidades - Turismo')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Localidades</h2>
            <p class="admin-subtitle">Editá la historia, descripción e imagen de portada de cada localidad.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.turismo.index') }}" class="btn btn-secondary">
                Turismo
            </a>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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

    <div class="admin-list">
        @foreach($localidades as $localidad)
            <article class="admin-list-item">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
                        <h3 style="margin:0;">{{ $localidad->nombre }}</h3>

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
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('admin.turismo.localidades.edit', $localidad) }}" class="btn btn-secondary">
                        Editar
                    </a>
                </div>
            </article>
        @endforeach
    </div>
@endsection
