@extends('layouts.app')

@section('title', 'Reservar asientos — Baile de Egresados')

@section('content')
<section class="baile-hero">
    <div>
        <span class="section-badge">Baile de Egresados</span>
        <h1>Reservar asientos</h1>
        <p>Seleccioná hasta 2 asientos e ingresá tu DNI y código de validación para confirmar la reserva.</p>
    </div>
</section>

@if($errors->any())
    <div class="baile-alert baile-alert--error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($lugares->isEmpty())
    <div class="baile-alert baile-alert--info">
        <i class="fa-solid fa-circle-info"></i>
        No quedan asientos disponibles en este momento.
    </div>
@else
    <form action="{{ route('baile-egresados.guardar') }}" method="POST" class="baile-form">
        @csrf

        <div class="baile-form__grid">
            <div class="admin-form-group">
                <label for="asiento1">1.º Asiento <span class="baile-requerido">*</span></label>
                <select name="asiento1" id="asiento1" required>
                    <option value="">— Seleccioná un asiento —</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}" {{ old('asiento1') == $lugar->id ? 'selected' : '' }}>
                            {{ $lugar->color }} — Fila {{ $lugar->fila }}, Asiento {{ $lugar->numero }}
                        </option>
                    @endforeach
                </select>
                @error('asiento1') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="asiento2">2.º Asiento <span class="baile-opcional">(opcional)</span></label>
                <select name="asiento2" id="asiento2">
                    <option value="">— Sin segundo asiento —</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}" {{ old('asiento2') == $lugar->id ? 'selected' : '' }}>
                            {{ $lugar->color }} — Fila {{ $lugar->fila }}, Asiento {{ $lugar->numero }}
                        </option>
                    @endforeach
                </select>
                @error('asiento2') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="dni">DNI <span class="baile-requerido">*</span></label>
                <input type="text" name="dni" id="dni"
                       value="{{ old('dni') }}"
                       inputmode="numeric" pattern="[0-9]*"
                       minlength="7" maxlength="9"
                       placeholder="Ej: 38500000"
                       required>
                @error('dni') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="codigo">Código de validación <span class="baile-requerido">*</span></label>
                <input type="text" name="codigo" id="codigo"
                       value="{{ old('codigo') }}"
                       minlength="8" maxlength="8"
                       placeholder="8 caracteres"
                       required>
                @error('codigo') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="baile-form__actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-ticket"></i>
                Confirmar reserva
            </button>
            <a href="{{ route('baile-egresados.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
@endif
@endsection
