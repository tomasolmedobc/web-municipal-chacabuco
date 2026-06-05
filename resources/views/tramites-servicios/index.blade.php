@extends('layouts.app')

@section('title', 'Tramites y Servicios')

@section('content')
<section class="ts-hero">
    <span class="section-badge">Atencion ciudadana</span>
    <h1>Tramites y Servicios</h1>
    <p>
        Accesos rapidos a gestiones, consultas y servicios municipales para vecinos y vecinas de Chacabuco.
    </p>
</section>

<section class="ts-panel" data-ts-panel>
    <div class="ts-panel__top">
        <div class="ts-tabs" role="tablist" aria-label="Tramites y servicios">
            <button class="ts-tab is-active"
                    type="button"
                    role="tab"
                    aria-selected="true"
                    data-ts-tab="tramites">
                <i class="fa-solid fa-file-signature"></i>
                Tramites
                <span data-ts-count>{{ $tramites->count() }}</span>
            </button>

            <button class="ts-tab"
                    type="button"
                    role="tab"
                    aria-selected="false"
                    data-ts-tab="servicios">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                Servicios
                <span data-ts-count>{{ $servicios->count() }}</span>
            </button>
        </div>

        <label class="ts-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search"
                   placeholder="Buscar por nombre o descripcion"
                   data-ts-search
                   autocomplete="off"
                   aria-label="Buscar tramites y servicios">
        </label>
    </div>

    <div class="ts-empty" data-ts-empty hidden>
        No se encontraron accesos para la busqueda ingresada.
    </div>

    <div class="ts-content is-active" data-ts-content="tramites">
        <div class="ts-grid">
            @foreach($tramites as $item)
                @include('tramites-servicios.partials.card', ['item' => $item, 'tipo' => 'tramite'])
            @endforeach
        </div>
    </div>

    <div class="ts-content" data-ts-content="servicios">
        <div class="ts-grid">
            @foreach($servicios as $item)
                @include('tramites-servicios.partials.card', ['item' => $item, 'tipo' => 'servicio'])
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script src="{{ asset('js/tramites-servicios.js') }}"></script>
@endpush
