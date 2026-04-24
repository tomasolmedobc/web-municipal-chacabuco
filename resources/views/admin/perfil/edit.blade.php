@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Mi perfil</h2>
            <p class="admin-subtitle">Actualizá tus datos personales y contraseña.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </section>

    @if(session('ok'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('ok')), 'success');
            });
        </script>
    @endif
    @auth
    @if(auth()->user()->rol === 'admin')
        <div class="admin-form-card" style="margin-bottom: 24px;">
            <h3 style="margin-top: 0;">Administración</h3>
            <p class="admin-subtitle">
                Accesos disponibles solo para usuarios administradores.
            </p>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary">
                    Gestionar usuarios
                </a>

                <a href="{{ route('admin.sistema.index') }}" class="btn btn-secondary">
                    Configuración del sistema
                </a>
            </div>
        </div>
    @endif
@endauth

    <form action="{{ route('admin.perfil.update') }}" method="POST" class="admin-form-card">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required>
                @error('name') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label>Email</label>
                <input type="text" value="{{ $usuario->email }}" disabled>
            </div>

            <div class="admin-form-group full">
                <label>Rol</label>
                <input type="text" value="{{ ucfirst($usuario->rol) }}" disabled>
            </div>

            <div class="admin-form-group full">
                <hr style="border:none; border-top:1px solid var(--border); margin:10px 0;">
                <p class="fecha">Dejá la contraseña vacía si no querés cambiarla.</p>
            </div>

            <div class="admin-form-group">
                <label for="password">Nueva contraseña</label>
                <input type="password" name="password" id="password">
                @error('password') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
@endsection