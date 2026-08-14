@extends('layouts.app')

@section('title', 'Guía de Trámites de Recaudación')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Recaudación'],
]" />

<section class="rc-hero">
    <div>
        <span class="section-badge">Trámites municipales</span>
        <h1>Guía de Trámites de Recaudación</h1>
        <p>
            Accedé a los trámites, certificados y gestiones del área de Recaudación
            de la Municipalidad de Chacabuco.
        </p>
    </div>
</section>

{{-- Documentos PDF --}}
<section class="rc-docs">
    <h2 class="rc-docs__titulo">Seleccione el tipo de trámite que necesite.</h2>

    <div class="rc-docs__grid">
        @foreach($documentos as $doc)
            <a href="{{ $doc['url'] }}"
               class="rc-doc-card"
               @if($doc['url'] !== '#') target="_blank" rel="noopener" @endif>
                <div class="rc-doc-card__icono">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span class="rc-doc-card__badge">PDF</span>
                </div>
                <span class="rc-doc-card__titulo">{{ $doc['titulo'] }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- Trámites online --}}
@if($tramiteOnline)
<section class="rc-online">
    <h2 class="rc-online__titulo">Trámites Online</h2>

    <div class="rc-online__item">
        <h3 class="rc-online__item-titulo">{{ strtoupper($tramiteOnline->titulo) }}</h3>
        @if($tramiteOnline->descripcion)
            <p class="rc-online__item-desc">{{ $tramiteOnline->descripcion }}</p>
        @endif
        @if($tramiteOnline->url)
            <a href="{{ $tramiteOnline->url }}"
               class="btn btn-outline rc-online__btn"
               target="_blank" rel="noopener">
                Ir a trámites
            </a>
        @else
            <span class="btn btn-outline rc-online__btn" style="opacity:.5; cursor:default;">
                Ir a trámites
            </span>
        @endif
    </div>
</section>
@endif

<section class="rc-volver">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>

@endsection
