@extends('layouts.app')

@section('title', 'Recuperar contraseña')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <h2>Recuperar contraseña</h2>
            <p class="auth-texto">Ingresá tu correo y te enviamos un enlace para restablecer tu contraseña.</p>

            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-group">
                    <label for="email">Correo</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <small class="auth-error">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Enviar enlace</button>
            </form>

            <p class="auth-link"><a href="{{ route('login') }}">← Volver al acceso</a></p>
        </div>
    </section>
@endsection
