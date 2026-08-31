@extends('layouts.app')

@section('title', 'Gobierno Abierto')

@section('content')
<section class="ga-hero">
    <div>
        <span class="section-badge">Transparencia municipal</span>
        <h1>Gobierno Abierto</h1>
        <p>
            Accedé a información pública, licitaciones, normativa, datos institucionales
            y documentación del Municipio de Chacabuco.
        </p>
    </div>
</section>

<section class="ga-search-card">
    <div>
        <h2>¿Qué estás buscando?</h2>
        <p>Ingresá una palabra clave para encontrar accesos rápidamente.</p>
    </div>

    <input type="search"
           id="ga-search"
           placeholder="Buscar por licitaciones, proveedores, ordenanzas..."
           autocomplete="off">
</section>

<section class="section-heading section-heading--between">
    <div>
        <h2>Accesos de Gobierno Abierto</h2>
        <p>Información pública organizada para vecinos, instituciones y proveedores.</p>
    </div>
</section>

<section class="ga-grid" id="ga-grid">
    @foreach($items as $item)
        <a href="{{ $item['url'] }}" class="ga-card" data-title="{{ strtolower($item['titulo'] . ' ' . $item['descripcion']) }}">
            @if($item['destacado'])
                <span class="ga-card__badge">Destacado</span>
            @endif

            <div class="ga-card__icon">
                <i class="fa-solid {{ $item['icono'] }}"></i>
            </div>

            <h3>{{ $item['titulo'] }}</h3>
            <p>{{ $item['descripcion'] }}</p>
        </a>
    @endforeach
</section>

<div class="ga-empty" id="ga-empty" hidden>
    No se encontraron accesos para la busqueda ingresada.
</div>
@endsection

@push('scripts')
<script @nonce>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('ga-search');
    const cards = document.querySelectorAll('.ga-card');
    const empty = document.getElementById('ga-empty');

    if (!input) return;

    const normalize = (value) => value
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();

    const filterCards = () => {
        const term = normalize(input.value);
        let visible = 0;

        cards.forEach(card => {
            const matches = !term || normalize(card.dataset.title || '').includes(term);
            card.hidden = !matches;

            if (matches) {
                visible += 1;
            }
        });

        if (empty) {
            empty.hidden = visible > 0;
        }
    };

    input.addEventListener('input', filterCards);
    input.addEventListener('search', filterCards);
    filterCards();
});
</script>
@endpush
