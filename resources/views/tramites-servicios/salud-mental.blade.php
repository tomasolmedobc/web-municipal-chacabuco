@extends('layouts.app')

@section('title', 'Salud Mental — Salones de Contención')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Salud Mental'],
]" />

<section class="ga-hero">
    <div>
        <span class="section-badge">Salud Mental</span>
        <h1>Salones de Contención</h1>
        <p>
            Encontrá el punto de asistencia más cercano. Cada salón cuenta con profesionales
            disponibles en días y horarios establecidos para brindarte acompañamiento y contención.
        </p>
    </div>
</section>

<section class="sm-urgencia">
    <div class="sm-urgencia__icono">
        <i class="fa-solid fa-phone-volume"></i>
    </div>
    <div>
        <h3>¿Estás en una situación de crisis?</h3>
        <p>
            Llamá al <strong>0800-999-0091</strong> (Centro de Asistencia al Suicida, gratuito, 24 hs)
            o al <strong>107</strong> (SAME).
        </p>
    </div>
</section>

<section class="ga-search-card">
    <div>
        <h2>Buscá tu salón</h2>
        <p>Filtrá por nombre, dirección o profesional.</p>
    </div>
    <input type="search"
           id="sm-search"
           placeholder="Ej: Rawson, Patricia Daló, Junín..."
           autocomplete="off">
</section>

<section class="section-heading">
    <h2>Puntos de atención</h2>
    <p>{{ count($salones) }} salones disponibles en el partido de Chacabuco.</p>
</section>

<div class="sm-grid" id="sm-grid">
    @foreach($salones as $salon)
        @php
            $searchText = $salon['nombre'] . ' ' . $salon['direccion'];
            foreach ($salon['agenda'] as $dia => $turnos) {
                $searchText .= ' ' . $dia;
                foreach ($turnos as $turno) {
                    $searchText .= ' ' . $turno['profesional'];
                }
            }
        @endphp

        <article class="sm-card" data-search="{{ mb_strtolower($searchText) }}">
            <div class="sm-card__header">
                <div class="sm-card__icono">
                    <i class="fa-solid fa-house-medical"></i>
                </div>
                <div>
                    <h3 class="sm-card__nombre">{{ $salon['nombre'] }}</h3>
                    <span class="sm-card__direccion">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $salon['direccion'] }}
                    </span>
                </div>
            </div>

            @if(! empty($salon['agenda']))
                <ul class="sm-agenda">
                    @foreach($salon['agenda'] as $dia => $turnos)
                        <li class="sm-agenda__dia">
                            <span class="sm-agenda__dia-nombre">{{ $dia }}</span>
                            <ul class="sm-agenda__turnos">
                                @foreach($turnos as $turno)
                                    <li>
                                        <span class="sm-agenda__profesional">{{ $turno['profesional'] }}</span>
                                        <span class="sm-agenda__horario">{{ $turno['horario'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="sm-agenda__vacio">Horarios próximamente.</p>
            @endif
        </article>
    @endforeach
</div>

<div class="sm-empty" id="sm-empty" hidden>
    No se encontraron salones para la búsqueda ingresada.
</div>

<section class="sm-volver">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>

@endsection

@push('scripts')
<script @nonce>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('sm-search');
    const cards = document.querySelectorAll('#sm-grid .sm-card');
    const empty = document.getElementById('sm-empty');

    const normalize = (str) => str
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .trim();

    const filter = () => {
        const term = normalize(input.value);
        let visible = 0;

        cards.forEach(card => {
            const matches = !term || normalize(card.dataset.search || '').includes(term);
            card.hidden = !matches;
            if (matches) visible++;
        });

        empty.hidden = visible > 0;
    };

    input.addEventListener('input', filter);
    input.addEventListener('search', filter);
});
</script>
@endpush
