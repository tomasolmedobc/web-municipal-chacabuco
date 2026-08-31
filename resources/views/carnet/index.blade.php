@extends('layouts.app')

@section('title', 'Carnet de Conducir — Municipalidad de Chacabuco')
@section('meta_description', 'Requisitos, pasos y materiales para obtener o renovar el carnet de conducir en Chacabuco.')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Carnet de Conducir'],
]" />

<section class="carnet-hero">
    <div>
        <span class="section-badge">Trámites y Servicios</span>
        <h1>Carnet de Conducir</h1>
        <p>Requisitos, pasos y materiales para obtener o renovar la Licencia Nacional de Conducir en Chacabuco.</p>
    </div>
</section>

<div class="carnet-layout">

    {{-- Columna principal --}}
    <div class="carnet-main">

        @if($config->intro_texto)
            <div class="carnet-intro">{!! $config->intro_texto !!}</div>
        @endif

        @if($config->alerta_info)
            <div class="carnet-alerta">{!! $config->alerta_info !!}</div>
        @endif

        @if($config->aviso_ubicacion)
            <div class="carnet-aviso">{!! $config->aviso_ubicacion !!}</div>
        @endif

        {{-- Pasos --}}
        @foreach([1,2,3,4] as $n)
            @php
                $titulo    = $config->{"paso{$n}_titulo"};
                $contenido = $config->{"paso{$n}_contenido"};
            @endphp
            @if($titulo || $contenido)
                <div class="carnet-paso">
                    <div class="carnet-paso__header">
                        <span class="carnet-paso__numero">{{ $n }}</span>
                        <h2 class="carnet-paso__titulo">{{ $titulo }}</h2>
                    </div>
                    @if($contenido)
                        <div class="carnet-paso__contenido">{!! $contenido !!}</div>
                    @endif
                </div>
            @endif
        @endforeach

    </div>

    {{-- Sidebar materiales --}}
    @if($materiales->isNotEmpty())
        <aside class="carnet-sidebar">
            <div class="carnet-materiales">
                <h3 class="carnet-materiales__titulo">
                    <i class="fa-solid fa-folder-open"></i>
                    Materiales de descarga
                </h3>
                <p class="carnet-materiales__intro">
                    Si vas a manejar un vehículo particular o profesional, usted debe aprobar su examen de conducir.
                    <strong>Descarga e imprima los manuales necesarios para aprobar su examen escrito.</strong>
                </p>

                <div class="carnet-materiales__lista">
                    @foreach($materiales as $material)
                        <div class="carnet-material-item">
                            <div class="carnet-material-item__info">
                                <span class="carnet-material-item__titulo">{{ $material->titulo }}</span>
                                @if($material->subtitulo)
                                    <span class="carnet-material-item__size">{{ $material->subtitulo }}</span>
                                @endif
                            </div>
                            @if($material->url)
                                <a href="{{ $material->url }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-primary carnet-material-item__btn">
                                    @if($material->tipo_boton === 'ver')
                                        VER INFORMACIÓN
                                    @else
                                        DESCARGAR ARCHIVO
                                    @endif
                                </a>
                            @else
                                <span class="btn btn-primary carnet-material-item__btn carnet-material-item__btn--disabled">
                                    @if($material->tipo_boton === 'ver')
                                        VER INFORMACIÓN
                                    @else
                                        DESCARGAR ARCHIVO
                                    @endif
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    @endif

</div>

{{-- Sección Licencia Digital --}}
@if($config->licencia_digital_contenido)
    <div class="carnet-digital">
        <div class="carnet-digital__contenido">
            {!! $config->licencia_digital_contenido !!}
        </div>
    </div>
@endif

<section class="volver-ts">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>
@endsection
