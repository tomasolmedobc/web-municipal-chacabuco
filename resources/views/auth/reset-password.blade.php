@extends('layouts.app')

@section('title', 'Nueva contraseña')

@section('content')
    <section class="auth-wrap">
        <div class="auth-card">
            <h2>Nueva contraseña</h2>
            <p class="auth-texto">Elegí una contraseña segura para tu cuenta.</p>

            <form action="{{ route('password.update') }}" method="POST" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="auth-group">
                    <label for="email">Correo</label>
                    <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required>
                    @error('email')
                        <small class="auth-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="auth-group">
                    <label for="password">Nueva contraseña</label>
                    <input type="password" name="password" id="password" required minlength="8">
                    @error('password')
                        <small class="auth-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="auth-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
            </form>
        </div>
    </section>
@endsection
