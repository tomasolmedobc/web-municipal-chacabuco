@extends('layouts.app')

@section('title', 'Reserva confirmada — Baile de Egresados')

@section('content')
<section class="baile-hero">
    <div>
        <span class="section-badge">Baile de Egresados</span>
        <h1>¡Reserva confirmada!</h1>
    </div>
</section>

<div class="baile-confirmacion">
    <div class="baile-confirmacion__icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <h2>Tu reserva fue registrada correctamente</h2>
    <p>Hola, <strong>{{ session('nombre') }}</strong>. Tus asientos están reservados.</p>

    @if(session('asientos'))
        <div class="baile-confirmacion__asientos">
            @foreach(session('asientos') as $asiento)
                <div class="baile-confirmacion__asiento">
                    <i class="fa-solid fa-couch"></i>
                    {{ $asiento }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="baile-confirmacion__aviso">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Debés dirigirte a la Dirección de Juventud con tu DNI para completar el pago y confirmar tu lugar.
    </div>

    <div class="baile-confirmacion__actions">
        <a href="{{ route('baile-egresados.consultar') }}" class="btn btn-secondary">
            Consultar mis reservas
        </a>
        <a href="{{ route('home') }}" class="btn btn-primary">
            Volver al inicio
        </a>
    </div>
</div>
@endsection
