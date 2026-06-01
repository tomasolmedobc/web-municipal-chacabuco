@extends('layouts.app')

@section('title', 'Acceso Municipal')

@section('content')
<section class="noticias-hero acceso-municipal-hero">
    <div>
        <span class="section-badge">Area interna</span>
        <h1>Acceso Municipal</h1>
        <p>
            Accesos rapidos a plataformas y herramientas de uso municipal.
        </p>
    </div>

    <form action="{{ route('acceso-municipal.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-secondary">
            Salir
        </button>
    </form>
</section>

<section class="acceso-municipal-grid" aria-label="Accesos municipales">
    @foreach($links as $link)
        <a href="{{ $link['url'] ?: '#' }}"
           target="{{ ($link['url'] ?? '#') !== '#' ? '_blank' : '_self' }}"
           rel="noopener"
           class="acceso-municipal-card">
            <span class="acceso-municipal-card__icon">
                <i class="fa-solid {{ $link['icono'] }}"></i>
            </span>

            <div>
                <h2>{{ $link['titulo'] }}</h2>
                <p>{{ $link['descripcion'] }}</p>
            </div>
        </a>
    @endforeach
</section>
@endsection
