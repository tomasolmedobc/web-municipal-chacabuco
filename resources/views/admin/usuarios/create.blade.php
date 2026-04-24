@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Nuevo usuario</h2>
            <p class="admin-subtitle">Creá un usuario para administrar el portal.</p>
        </div>

        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
    </section>

    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="admin-form-card">
        @csrf

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                @error('name') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
                @error('password') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <div class="admin-form-group full">
                <label for="rol">Rol</label>
                <select name="rol" id="rol" required>
                    <option value="editor" {{ old('rol', 'editor') === 'editor' ? 'selected' : '' }}>Editor</option>
                    <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
                @error('rol') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Crear usuario</button>
    </form>
@endsection