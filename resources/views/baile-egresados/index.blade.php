@extends('layouts.app')

@section('title', 'Baile de Egresados')

@section('content')
<section class="baile-hero">
    <div>
        <span class="section-badge">Municipalidad de Chacabuco</span>
        <h1>Baile de Egresados</h1>
        <p>Reservá tus asientos o consultá tu reserva existente ingresando tu DNI.</p>
    </div>
</section>

<div class="baile-stats">
    <div class="baile-stat">
        <span class="baile-stat__numero">{{ $totalAsientos }}</span>
        <span class="baile-stat__label">Asientos totales</span>
    </div>
    <div class="baile-stat baile-stat--disponible">
        <span class="baile-stat__numero">{{ $disponibles }}</span>
        <span class="baile-stat__label">Disponibles</span>
    </div>
    <div class="baile-stat baile-stat--reservado">
        <span class="baile-stat__numero">{{ $reservados }}</span>
        <span class="baile-stat__label">Reservados</span>
    </div>
</div>

<div class="baile-acciones">
    <a href="{{ route('baile-egresados.reservar') }}" class="baile-accion-card">
        <div class="baile-accion-card__icon">
            <i class="fa-solid fa-ticket"></i>
        </div>
        <h2>Reservar asientos</h2>
        <p>Elegí hasta 2 asientos ingresando tu DNI y código de validación.</p>
        <span class="btn btn-primary">Ir a reservar</span>
    </a>

    <a href="{{ route('baile-egresados.consultar') }}" class="baile-accion-card">
        <div class="baile-accion-card__icon baile-accion-card__icon--secondary">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h2>Consultar mi reserva</h2>
        <p>Verificá los asientos que tenés reservados ingresando tu DNI.</p>
        <span class="btn btn-secondary">Consultar</span>
    </a>
</div>
@endsection
