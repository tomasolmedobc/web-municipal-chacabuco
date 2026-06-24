@extends('layouts.app')

@section('title', 'Turismo')

@section('content')
<section class="turismo-hero">
    <div>
        <span class="section-badge">Turismo</span>
        <h1>Chacabuco y sus delegaciones</h1>
        <p>Descubrí atractivos turísticos, gastronomía y eventos en Chacabuco, Rawson, O'Higgins, Castilla y Cucha-Cucha.</p>
    </div>
</section>

<section class="turismo-localidades">
    <div class="section-heading">
        <h2>Localidades</h2>
        <p>Conocé la historia y la propuesta turística de cada localidad del municipio.</p>
    </div>

    <div class="turismo-localidad-grid">
        @foreach($localidades as $localidad)
            <a href="{{ route('turismo.show', $localidad->slug) }}" class="turismo-localidad-card">
                <img src="{{ $localidad->imagen_portada_url }}" alt="{{ $localidad->nombre }}">

                <div class="turismo-localidad-card__body">
                    @if($localidad->es_cabecera)
                        <span class="hero-badge">Ciudad cabecera</span>
                    @endif

                    <h3>{{ $localidad->nombre }}</h3>

                    @if($localidad->descripcion)
                        <p>{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($localidad->descripcion), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 120) }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>

@if($destacados->count())
    <section class="turismo-destacados">
        <div class="section-heading">
            <h2>Destacados</h2>
            <p>Una selección de atractivos, gastronomía y eventos de todo el municipio.</p>
        </div>

        <div class="turismo-item-grid">
            @foreach($destacados as $item)
                @php
                    $url = $item->mostrar_detalle
                        ? route('turismo.show.item', [$item->localidad->slug, $item->id])
                        : route('turismo.show', ['localidad' => $item->localidad->slug, 'tipo' => $item->tipo]);
                @endphp
                <a href="{{ $url }}" class="turismo-item-card">
                    <span class="turismo-item-card__badge">Destacado</span>
                    <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}">

                    <div class="turismo-item-card__body">
                        <span class="hero-badge">{{ \App\Models\TurismoItem::configTipo($item->tipo)['titulo'] }}</span>
                        <h3>{{ $item->titulo }}</h3>

                        @if($item->descripcion)
                            <p>{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($item->descripcion), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 120) }}</p>
                        @endif

                        <span class="turismo-item-card__localidad">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $item->localidad->nombre }}
                        </span>

                        @if($item->mostrar_detalle)
                            <span class="turismo-item-card__ver-mas">Ver más →</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

@if($eventosFinalizados->count())
    <section class="turismo-destacados">
        <div class="section-heading">
            <h2>Eventos finalizados</h2>
            <p>Historial de eventos que ya tuvieron lugar en Chacabuco y sus delegaciones.</p>
        </div>

        <div class="turismo-item-grid">
            @foreach($eventosFinalizados as $item)
                @php
                    $urlFin = $item->mostrar_detalle
                        ? route('turismo.show.item', [$item->localidad->slug, $item->id])
                        : route('turismo.show', ['localidad' => $item->localidad->slug, 'tipo' => 'evento']);
                @endphp
                <a href="{{ $urlFin }}" class="turismo-item-card turismo-item-card--finalizado">
                    <span class="turismo-item-card__badge turismo-item-card__badge--finalizado">Finalizado</span>
                    <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}">

                    <div class="turismo-item-card__body">
                        <h3>{{ $item->titulo }}</h3>

                        @if($item->descripcion)
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->descripcion), 120) }}</p>
                        @endif

                        <span class="turismo-evento-fecha">
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ $item->fecha_inicio->format('d/m/Y') }}
                            @if($item->fecha_fin) — {{ $item->fecha_fin->format('d/m/Y') }} @endif
                        </span>

                        <span class="turismo-item-card__localidad">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $item->localidad->nombre }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
