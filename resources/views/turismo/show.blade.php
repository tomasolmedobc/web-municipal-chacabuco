@extends('layouts.app')

@section('title', $localidad->nombre . ' - Turismo')

@section('content')
<section class="turismo-show-hero" style="background-image: linear-gradient(135deg, rgba(15,23,42,.55), rgba(15,23,42,.35)), url('{{ $localidad->imagen_portada_url }}');">
    <div>
        <span class="section-badge">{{ $localidad->es_cabecera ? 'Ciudad cabecera' : 'Delegación' }}</span>
        <h1>{{ $localidad->nombre }}</h1>
        @if($localidad->descripcion)
            <p>{{ html_entity_decode(strip_tags($localidad->descripcion), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</p>
        @endif
    </div>
</section>

@if($localidad->historia)
    <section class="hab-card turismo-historia">
        <span class="section-badge">Historia</span>
        <div class="contenido">{!! $localidad->historia !!}</div>
    </section>
@endif

<section class="turismo-tabs-section">
    <nav class="turismo-tabs" aria-label="Tipos de turismo">
        <a href="{{ route('turismo.show', $localidad->slug) }}"
           class="turismo-tab js-turismo-tab {{ ! $tipoActivo ? 'is-active' : '' }}"
           data-tipo="">
            Todos
        </a>

        @foreach($tipos as $tipo => $config)
            <a href="{{ route('turismo.show', ['localidad' => $localidad->slug, 'tipo' => $tipo]) }}"
               class="turismo-tab js-turismo-tab {{ $tipoActivo === $tipo ? 'is-active' : '' }}"
               data-tipo="{{ $tipo }}">
                <i class="fa-solid {{ $config['icono'] }}"></i>
                {{ $config['titulo'] }}
            </a>
        @endforeach
    </nav>

    @if($items->flatten()->count() === 0)
        <div class="hab-empty">
            Todavía no hay contenido cargado para esta localidad.
        </div>
    @endif

    @foreach($tipos as $tipo => $config)
        @if($items->has($tipo))
            <div class="turismo-grupo js-turismo-grupo" data-tipo="{{ $tipo }}">
                <h2>{{ $config['titulo'] }}</h2>

                <div class="turismo-item-grid">
                    @foreach($items[$tipo] as $item)
                        @php
                            $hoy = now()->toDateString();
                            $eventoFinalizado = $tipo === \App\Models\TurismoItem::TIPO_EVENTO
                                && $item->fecha_inicio
                                && (
                                    ($item->fecha_fin && $item->fecha_fin->toDateString() < $hoy)
                                    || (!$item->fecha_fin && $item->fecha_inicio->toDateString() < $hoy)
                                );
                        @endphp
                        <div class="turismo-item-card {{ $item->imagen ? '' : 'turismo-item-card--icon' }} {{ $eventoFinalizado ? 'turismo-item-card--finalizado' : '' }}">
                            @if($eventoFinalizado)
                                <span class="turismo-item-card__badge turismo-item-card__badge--finalizado">Finalizado</span>
                            @elseif($item->destacado)
                                <span class="turismo-item-card__badge">Destacado</span>
                            @endif

                            @if($item->imagen)
                                <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}">
                            @else
                                <div class="turismo-item-card__icon">
                                    <i class="fa-solid {{ $config['icono'] }}"></i>
                                </div>
                            @endif

                            <div class="turismo-item-card__body">
                                @if($item->categoria)
                                    <span class="hero-badge">{{ $item->categoria }}</span>
                                @endif

                                <h3>{{ $item->titulo }}</h3>

                                @if($item->descripcion)
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->descripcion), 140) }}</p>
                                @endif

                                @if($tipo === \App\Models\TurismoItem::TIPO_EVENTO && $item->fecha_inicio)
                                    <span class="turismo-evento-fecha">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        {{ $item->fecha_inicio->format('d/m/Y') }}
                                        @if($item->hora_inicio)
                                            · {{ substr($item->hora_inicio, 0, 5) }} hs
                                        @endif
                                        @if($item->fecha_fin) — {{ $item->fecha_fin->format('d/m/Y') }} @endif
                                    </span>
                                @endif

                                @if($item->direccion)
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $item->direccion }}</span>
                                @endif

                                @if($item->telefono)
                                    <span><i class="fa-solid fa-phone"></i> {{ $item->telefono }}</span>
                                @endif

                                @if($item->link_externo)
                                    <a href="{{ $item->link_externo }}" target="_blank" rel="noopener" class="btn btn-secondary">
                                        Más información
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</section>

@push('scripts')
<script>
(function () {
    const tabs   = document.querySelectorAll('.js-turismo-tab');
    const grupos = document.querySelectorAll('.js-turismo-grupo');

    function filtrar(tipo) {
        tabs.forEach(t => t.classList.toggle('is-active', t.dataset.tipo === tipo));
        grupos.forEach(g => {
            g.style.display = (!tipo || g.dataset.tipo === tipo) ? '' : 'none';
        });

        const url = new URL(window.location);
        if (tipo) url.searchParams.set('tipo', tipo);
        else url.searchParams.delete('tipo');
        history.replaceState({ tipo }, '', url);
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            filtrar(this.dataset.tipo);
        });
    });

    // Aplicar filtro inicial según URL al cargar
    const tipoInicial = new URLSearchParams(window.location.search).get('tipo') || '';
    filtrar(tipoInicial);
})();
</script>
@endpush
@endsection
