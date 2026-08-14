@extends('layouts.app')

@section('title', 'Teléfonos Útiles — Chacabuco')
@section('meta_description', 'Directorio de teléfonos y contactos útiles de la Municipalidad de Chacabuco y organismos locales.')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',          'url' => route('home')],
    ['label' => 'Teléfonos Útiles'],
]" />

<section class="tel-hero">
    <div class="tel-hero__inner">
        <span class="section-eyebrow">
            <i class="fa-solid fa-phone"></i> Directorio
        </span>
        <h1>Teléfonos Útiles</h1>
        <p>Encontrá rápidamente los contactos de organismos municipales, salud, seguridad y más.</p>
    </div>
</section>

<div class="tel-toolbar">
    <form method="GET" action="{{ route('telefonos-utiles.index') }}" class="tel-search-form" id="tel-form">
        <input type="hidden" name="categoria" id="tel-cat-hidden" value="{{ $categoria }}">
        <div class="tel-search-wrap">
            <i class="fa-solid fa-magnifying-glass tel-search-ico"></i>
            <input type="search"
                   name="q"
                   id="tel-search-input"
                   class="tel-search-input"
                   value="{{ $q }}"
                   placeholder="Buscar por nombre, teléfono, dirección…"
                   autocomplete="off">
            @if($q || $categoria)
                <a href="{{ route('telefonos-utiles.index') }}" class="tel-search-clear" title="Limpiar">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <div class="tel-cats" id="tel-cats">
        <button type="button"
                class="tel-cat-btn {{ !$categoria ? 'is-active' : '' }}"
                data-cat="">
            Todos
        </button>
        @foreach($categorias as $cat)
            @if(isset($iconos[$cat]))
                <button type="button"
                        class="tel-cat-btn {{ $categoria === $cat ? 'is-active' : '' }}"
                        data-cat="{{ $cat }}">
                    <i class="fa-solid {{ $iconos[$cat] }}"></i>
                    {{ $cat }}
                </button>
            @endif
        @endforeach
    </div>
</div>

@if($total === 0)
    <div class="tel-empty">
        <i class="fa-solid fa-phone-slash"></i>
        <p>No se encontraron resultados para <strong>{{ $q }}</strong>.</p>
        <a href="{{ route('telefonos-utiles.index') }}" class="btn btn-secondary">Ver todos</a>
    </div>
@else
    <div class="tel-grupos">
        @foreach($porCategoria as $cat => $items)
            @php $icono = $iconos[$cat] ?? 'fa-phone'; @endphp
            <div class="tel-grupo">
                <h2 class="tel-grupo__titulo">
                    <i class="fa-solid {{ $icono }}"></i>
                    {{ $cat }}
                    <span class="tel-grupo__count">{{ $items->count() }}</span>
                </h2>

                <div class="tel-tabla-wrap">
                    <table class="tel-tabla">
                        <thead>
                            <tr>
                                <th>Organismo / Servicio</th>
                                <th><i class="fa-solid fa-location-dot"></i> Dirección</th>
                                <th><i class="fa-solid fa-phone"></i> Teléfono</th>
                                <th><i class="fa-solid fa-envelope"></i> Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="tel-nombre">{{ $item->nombre }}</td>
                                    <td class="tel-dir">{{ $item->direccion ?: '—' }}</td>
                                    <td class="tel-tel">
                                        @if($item->telefono)
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->telefono) }}"
                                               class="tel-link">
                                                {{ $item->telefono }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="tel-email">
                                        @if($item->email)
                                            <a href="mailto:{{ $item->email }}" class="tel-link">
                                                {{ $item->email }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const catBtns = document.querySelectorAll('.tel-cat-btn');
    const hidden  = document.getElementById('tel-cat-hidden');
    const form    = document.getElementById('tel-form');

    catBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            catBtns.forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            hidden.value = btn.dataset.cat;
            form.submit();
        });
    });
})();
</script>
@endpush
