@extends('layouts.app')

@section('title', 'Consultar reserva — Baile de Egresados')

@section('content')
<section class="baile-hero">
    <div>
        <span class="section-badge">Baile de Egresados</span>
        <h1>Consultar mi reserva</h1>
        <p>Ingresá tu DNI para ver los asientos que tenés reservados.</p>
    </div>
</section>

<form action="{{ route('baile-egresados.consultar.post') }}" method="POST" class="baile-form baile-form--consulta">
    @csrf
    <div class="baile-form__grid baile-form__grid--1col">
        <div class="admin-form-group">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni"
                   value="{{ old('dni') }}"
                   inputmode="numeric" pattern="[0-9]*"
                   minlength="7" maxlength="9"
                   placeholder="Ej: 38500000"
                   required autofocus>
            @error('dni') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="baile-form__actions">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i>
            Consultar
        </button>
        <a href="{{ route('baile-egresados.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</form>

@isset($reservas)
    @if($reservas->isEmpty())
        <div class="baile-alert baile-alert--info" style="margin-top: 24px;">
            <i class="fa-solid fa-circle-info"></i>
            @if($usuario)
                No tenés asientos reservados aún, <strong>{{ $usuario->nombre_completo }}</strong>.
            @else
                No se encontró ningún usuario con ese DNI.
            @endif
        </div>
    @else
        <div class="baile-resultados">
            <h2>
                Asientos reservados para
                <strong>{{ $usuario->nombre_completo }}</strong>
            </h2>

            <div class="baile-tabla">
                <div class="baile-tabla__header">
                    <span>Color / Sector</span>
                    <span>Fila</span>
                    <span>Asiento N.º</span>
                    <span>Estado pago</span>
                </div>

                @foreach($reservas as $reserva)
                    <div class="baile-tabla__fila">
                        <span>{{ $reserva->lugar->color }}</span>
                        <span>{{ $reserva->lugar->fila }}</span>
                        <span>{{ $reserva->lugar->numero }}</span>
                        <span>
                            @if($reserva->pago)
                                <span class="baile-badge baile-badge--pagado">
                                    <i class="fa-solid fa-check"></i> Pagado
                                </span>
                            @else
                                <span class="baile-badge baile-badge--pendiente">
                                    <i class="fa-solid fa-clock"></i> Pendiente de pago
                                </span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <p class="baile-resultados__aviso">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Para completar el pago, dirigite a la Dirección de Juventud con tu DNI.
            </p>
        </div>
    @endif
@endisset
@endsection
