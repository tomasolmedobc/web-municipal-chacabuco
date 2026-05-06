@extends('layouts.app')

@section('title', 'Nueva noticia')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Nueva noticia</h2>
            <p class="admin-subtitle">Crea una noticia manualmente para el portal.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </section>

    <form id="form-noticia" action="{{ route('admin.noticias.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
        @csrf

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required>
                @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        {{-- DESTACADA --}}
        <div class="admin-form-group full">
            <label class="check-destacada">
                <input
                    type="checkbox"
                    name="destacada"
                    value="1"
                    {{ old('destacada', $noticia->destacada ?? false) ? 'checked' : '' }}
                >

                <span>
                    <strong>Noticia destacada</strong>
                    <small>Se mostrará como principal durante el tiempo configurado.</small>
                </span>
            </label>
        </div>

        <div class="admin-form-group full" id="destacada-config" style="display:none;">
            <label for="destacada_dias">Duración destacada</label>

            <input
                type="number"
                name="destacada_dias"
                id="destacada_dias"
                min="1"
                max="30"
                value="{{ old('destacada_dias', 12) }}"
            >

            <small class="fecha">
                Cantidad de días que se mostrará como noticia principal. Máximo 30 días.
            </small>
        </div>

            <div class="admin-form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" required>
                    <option value="oculto" {{ old('estado', 'oculto') === 'oculto' ? 'selected' : '' }}>Oculto</option>
                    <option value="publicado" {{ old('estado') === 'publicado' ? 'selected' : '' }}>Publicado</option>
                </select>
                @error('estado') <small class="auth-error">{{ $message }}</small> @enderror

                <small id="estado-alerta-oculto" class="estado-warning" style="display:none;">
                    ⚠️ Esta noticia no será visible públicamente.
                </small>

                <small id="estado-alerta-publicado" class="estado-success" style="display:none;">
                    ✔ Esta noticia será visible en el portal.
                </small>
            </div>
            

            <div class="admin-form-group full">
                <label for="categorias">Categorías</label>
                <select name="categorias[]" id="categorias" multiple class="filtro-input categorias-select">
                    @foreach($categoriasPadre as $categoriaPadre)
                        <option value="{{ $categoriaPadre->id }}"
                            {{ collect(old('categorias', []))->contains($categoriaPadre->id) ? 'selected' : '' }}>
                            {{ $categoriaPadre->nombre }}
                        </option>

                        @foreach($categoriaPadre->hijas as $hija)
                            <option value="{{ $hija->id }}"
                                {{ collect(old('categorias', []))->contains($hija->id) ? 'selected' : '' }}>
                                — {{ $hija->nombre }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                <small class="fecha">Podés seleccionar una o más categorías.</small>
                @error('categorias') <small class="auth-error">{{ $message }}</small> @enderror
                @error('categorias.*') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="imagen_destacada">Imagen destacada</label>
                <input type="file" name="imagen_destacada" id="imagen_destacada" accept=".jpg,.jpeg,.png,.webp">
                @error('imagen_destacada') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="contenido">Contenido</label>
                <textarea name="contenido" id="contenido" rows="12">{{ old('contenido') }}</textarea>
                @error('contenido') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="archivos">Archivos adjuntos</label>
                <input type="file" name="archivos[]" id="archivos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                @error('archivos.*') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar noticia</button>
    </form>
@endsection

@push('scripts_head')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-noticias.js') }}"></script>
    <script src="{{ asset('js/admin-editor.js') }}"></script>
@endpush