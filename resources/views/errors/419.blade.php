@extends('errors.base')

@section('codigo', '419')
@section('color', '#fef3c7')
@section('titulo', 'La página venció')
@section('mensaje')
    La sesión o el formulario estuvo abierto demasiado tiempo y ya no es válido.<br>
    Volvé a la página anterior, recargala e intentá de nuevo.
@endsection
@section('acciones')
    <a href="/" class="btn">Ir al inicio</a>
@endsection
