@extends('layouts.app')

@section('title', 'Ingresar')

@push('scripts_head')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <h2>Ingresar al panel</h2>
            <p class="auth-texto">Acceso para administradores y editores.</p>

            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

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
                    <a href="{{ route('password.request') }}" class="auth-forgot">¿Olvidaste tu contraseña?</a>
                </div>

                <div class="cf-turnstile"
                     data-sitekey="{{ $turnstileSiteKey }}"
                     data-theme="auto"
                     data-language="es">
                </div>
                @error('captcha')
                    <small class="auth-error">{{ $message }}</small>
                @enderror

                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
        </div>
    </section>
@endsection