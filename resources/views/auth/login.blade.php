@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <h2>Ingresar al panel</h2>
            <p class="auth-texto">Acceso para administradores y editores.</p>

            <form action="{{ route('login.post') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-group">
                    <label for="email">Correo</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    @error('email')
                        <small class="auth-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="auth-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="auth-group">
                    <label for="captcha">
                        ¿Cuánto es <span class="captcha-pregunta">{{ $captcha_pregunta }}</span>?
                    </label>
                    <input type="number" name="captcha" id="captcha" min="0" max="18"
                           autocomplete="off" required inputmode="numeric">
                    @error('captcha')
                        <small class="auth-error">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
        </div>
    </section>
@endsection