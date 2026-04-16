@extends('layouts.app')

@section('title', $noticia->titulo)

@section('content')
    <div class="detalle">
        <a href="{{ url('/noticias') }}" class="volver">← Volver al listado</a>

        <h1>{{ $noticia->titulo }}</h1>

        <div class="fecha">
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y - H:i') }} hs
        </div>

        @if($noticia->imagen_destacada)
            <div class="imagen">
                <img src="{{ $noticia->imagen_destacada }}" alt="{{ $noticia->titulo }}">
            </div>
        @endif
                    @php
            $contenido = preg_replace('/class="[^"]*"/', '', $noticia->contenido);
            @endphp

            <div class="contenido">
                {!! $contenido !!}
            </div>
    </div>
@endsection