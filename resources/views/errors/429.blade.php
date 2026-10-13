@php
    $espera = (int) (isset($exception) && method_exists($exception, 'getHeaders') ? ($exception->getHeaders()['Retry-After'] ?? 0) : 0);
@endphp
@extends('errors.base')

@section('codigo', '429')
@section('color', '#fee2e2')
@section('titulo', 'Demasiados intentos')
@section('mensaje')
    Realizaste muchas solicitudes seguidas.<br>
    @if($espera > 0)
        Esperá {{ $espera >= 60 ? ceil($espera / 60) . ' minuto(s)' : $espera . ' segundos' }} e intentá de nuevo.
    @else
        Esperá un momento e intentá de nuevo.
    @endif
@endsection
@section('acciones')
    <a href="/" class="btn">Ir al inicio</a>
@endsection
