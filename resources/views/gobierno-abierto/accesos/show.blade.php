@extends('layouts.app')

@section('title', $documento->titulo)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',          'url' => route('home')],
    ['label' => 'Gobierno Abierto','url' => route('gobierno-abierto.index')],
    ['label' => $documento->titulo],
]" />
<section class="noticias-hero">
    <div>
        <span class="section-badge">Gobierno abierto</span>

        <h1>{{ $documento->titulo }}</h1>

        @if($documento->descripcion)
            <p>{{ $documento->descripcion }}</p>
        @endif
    </div>
</section>

<section class="section-heading">
    <span class="section-badge">Archivos disponibles</span>

    <h2>Elegí el documento que querés abrir</h2>
</section>

<section class="news-home">
    <div class="news-home__grid">
        @forelse($archivos as $archivo)
            <article class="licitacion-card">
                <div class="licitacion-card__top">
                    <div class="licitacion-card__badges">
                        <span class="licitacion-badge badge-activa">
                            {{ strtoupper($archivo->extension) }}
                        </span>
                    </div>
                </div>

                <div>
                    <h3>{{ $archivo->nombre_original }}</h3>
                </div>

                <div class="licitacion-meta">
                    <span>
                        <i class="fa-solid fa-paperclip"></i>
                        {{ $archivo->tamano_legible }}
                    </span>
                </div>

                <div class="licitacion-actions">
                    <a href="{{ $archivo->ruta }}" target="_blank" class="btn btn-primary">
                        Abrir archivo
                    </a>
                </div>
            </article>
        @empty
            <div class="admin-empty">
                No hay archivos disponibles.
            </div>
        @endforelse
    </div>
</section>

<section class="volver-ts">
    <a href="{{ route('gobierno-abierto.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Gobierno Abierto
    </a>
</section>
@endsection
