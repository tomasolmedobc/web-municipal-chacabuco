@extends('layouts.app')

@section('title', 'Visibilidad de botones')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Visibilidad de botones</h2>
        <p class="admin-subtitle">Activá u ocultá accesos públicos sin eliminar ningún botón ni modificar archivos.</p>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        Volver al panel
    </a>
</section>

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

<form action="{{ route('admin.botones-visibilidad.update') }}" method="POST" class="admin-visibility-form">
    @csrf
    @method('PUT')

    <section class="visibility-summary">
        @foreach($secciones as $seccion => $botones)
            <article>
                <span>{{ $titulos[$seccion] ?? $seccion }}</span>
                <strong>{{ $botones->where('activo', true)->count() }}/{{ $botones->count() }}</strong>
                <small>botones visibles</small>
            </article>
        @endforeach
    </section>

    @foreach($secciones as $seccion => $botones)
        <section class="admin-form-card visibility-section">
            <div class="visibility-section__header">
                <div>
                    <h2>{{ $titulos[$seccion] ?? $seccion }}</h2>
                    <p>{{ $botones->where('activo', true)->count() }} visibles de {{ $botones->count() }} botones.</p>
                </div>

                <span class="visibility-section__count">
                    {{ $botones->count() }}
                </span>
            </div>

            <div class="visibility-list">
                @foreach($botones as $boton)
                    <article class="visibility-item {{ $boton->activo ? 'is-visible' : 'is-hidden' }}">
                        <div class="visibility-item__main">
                            <span class="visibility-item__icon">
                                <i class="fa-solid {{ $boton->icono ?: 'fa-link' }}"></i>
                            </span>

                            <div>
                                <div class="visibility-item__title">
                                    <h3>{{ $boton->titulo }}</h3>
                                    <em>{{ $boton->activo ? 'Visible' : 'Oculto' }}</em>
                                </div>
                                <p>{{ $boton->descripcion ?: 'Sin descripcion cargada.' }}</p>
                            </div>
                        </div>

                        <label class="visibility-link">
                            <span>Link personalizado</span>
                            <input type="text"
                                   name="links[{{ $boton->id }}]"
                                   value="{{ old('links.' . $boton->id, $boton->url_personalizada) }}"
                                   placeholder="{{ $boton->url ?: 'Usa el link original o dinamico' }}">
                        </label>

                        <label class="visibility-switch">
                            <input type="checkbox"
                                   name="activos[{{ $boton->id }}]"
                                   value="1"
                                   {{ $boton->activo ? 'checked' : '' }}>
                            <span></span>
                            <strong data-switch-label>{{ $boton->activo ? 'Activo' : 'Inactivo' }}</strong>
                        </label>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach

    <div class="admin-form-actions visibility-actions">
        <button type="submit" class="btn btn-primary">
            Guardar visibilidad
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script @nonce>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.visibility-switch input').forEach((input) => {
        input.addEventListener('change', () => {
            const item = input.closest('.visibility-item');
            const label = input.closest('.visibility-switch').querySelector('[data-switch-label]');
            const badge = item.querySelector('.visibility-item__title em');

            item.classList.toggle('is-visible', input.checked);
            item.classList.toggle('is-hidden', !input.checked);
            label.textContent = input.checked ? 'Activo' : 'Inactivo';
            badge.textContent = input.checked ? 'Visible' : 'Oculto';
        });
    });
});
</script>
@endpush
