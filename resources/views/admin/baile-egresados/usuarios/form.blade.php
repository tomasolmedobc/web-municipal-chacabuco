@extends('layouts.app')

@section('title', ($modo === 'crear' ? 'Nuevo usuario' : 'Editar usuario') . ' — Baile de Egresados')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ $modo === 'crear' ? 'Nuevo usuario' : 'Editar usuario' }}</h2>
        <p class="admin-subtitle">Datos del egresado habilitado para reservar asientos.</p>
    </div>
    <a href="{{ route('admin.baile.usuarios.index') }}" class="btn btn-secondary">Volver</a>
</section>

<form action="{{ $modo === 'crear' ? route('admin.baile.usuarios.store') : route('admin.baile.usuarios.update', $usuario) }}"
      method="POST"
      class="admin-form-card">
    @csrf
    @if($modo === 'editar') @method('PUT') @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" name="nombre_completo" id="nombre_completo"
                   value="{{ old('nombre_completo', $usuario->nombre_completo) }}" required>
            @error('nombre_completo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni"
                   value="{{ old('dni', $usuario->dni) }}"
                   inputmode="numeric" maxlength="9" required>
            @error('dni') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="codigo">Código de validación <small class="fecha">(8 caracteres)</small></label>
            <div style="display:flex; gap:8px;">
                <input type="text" name="codigo" id="codigo"
                       value="{{ old('codigo', $usuario->codigo) }}"
                       minlength="8" maxlength="8" required style="flex:1;">
                <button type="button" id="btn-generar-codigo" class="btn btn-secondary" title="Generar código aleatorio">
                    Generar
                </button>
            </div>
            @error('codigo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="disponibles">Asientos disponibles</label>
            <input type="number" name="disponibles" id="disponibles"
                   value="{{ old('disponibles', $usuario->disponibles ?? 2) }}"
                   min="0" max="10" required>
            @error('disponibles') <small class="auth-error">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $modo === 'crear' ? 'Guardar' : 'Actualizar' }}
    </button>
</form>
@endsection

@push('scripts')
<script @nonce>
document.getElementById('btn-generar-codigo').addEventListener('click', function () {
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    var code = '';
    var arr = new Uint8Array(8);
    crypto.getRandomValues(arr);
    arr.forEach(function (b) { code += chars[b % chars.length]; });
    document.getElementById('codigo').value = code;
});
</script>
@endpush
