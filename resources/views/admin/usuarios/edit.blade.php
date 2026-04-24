@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Editar usuario</h2>
            <p class="admin-subtitle">Modificá los datos, rol o contraseña del usuario.</p>
        </div>

        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
    </section>

    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="admin-form-card">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required>
                @error('name') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required>
                @error('email') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="rol">Rol</label>
                <select name="rol" id="rol" required>
                    <option value="editor" {{ old('rol', $usuario->rol) === 'editor' ? 'selected' : '' }}>Editor</option>
                    <option value="admin" {{ old('rol', $usuario->rol) === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
                @error('rol') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <hr style="width:100%; border:none; border-top:1px solid var(--border); margin:10px 0;">
                <p class="fecha" style="margin:0;">
                    Dejá los campos de contraseña vacíos si no querés cambiarla.
                </p>
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

        <button type="submit" class="btn btn-primary">Actualizar usuario</button>
    </form>
@endsection