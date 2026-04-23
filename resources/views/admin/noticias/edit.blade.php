@extends('layouts.app')

@section('title', 'Editar noticia')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Editar noticia</h2>
            <p class="admin-subtitle">Modifica los datos de la noticia seleccionada.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
    </section>

    <form id="form-noticia" action="{{ route('admin.noticias.update', $noticia) }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="admin-form-group full">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $noticia->titulo) }}" required>
                @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="fecha">Fecha</label>
                <input
                    type="datetime-local"
                    name="fecha"
                    id="fecha"
                    value="{{ old('fecha', $noticia->fecha?->format('Y-m-d\TH:i')) }}"
                    required
                >
                @error('fecha') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" required>
                    <option value="oculto" {{ old('estado', $noticia->estado) === 'oculto' ? 'selected' : '' }}>Oculto</option>
                    <option value="publicado" {{ old('estado', $noticia->estado) === 'publicado' ? 'selected' : '' }}>Publicado</option>
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
                @php
                    $categoriasSeleccionadas = old('categorias', $noticia->categorias->pluck('id')->toArray());
                @endphp

                <select name="categorias[]" id="categorias" multiple class="filtro-input categorias-select">
                    @foreach($categoriasPadre as $categoriaPadre)
                        <option value="{{ $categoriaPadre->id }}"
                            {{ in_array($categoriaPadre->id, $categoriasSeleccionadas) ? 'selected' : '' }}>
                            {{ $categoriaPadre->nombre }}
                        </option>

                        @foreach($categoriaPadre->hijas as $hija)
                            <option value="{{ $hija->id }}"
                                {{ in_array($hija->id, $categoriasSeleccionadas) ? 'selected' : '' }}>
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

            @if ($noticia->imagen_destacada)
                <div class="admin-form-group full">
                    <label>Imagen actual</label>
                    <img src="{{ $noticia->imagen_destacada }}" alt="{{ $noticia->titulo }}" class="preview-imagen-admin">
                </div>
            @endif

            <div class="admin-form-group full">
                <label for="contenido">Contenido</label>
                <textarea name="contenido" id="contenido" rows="12">{{ old('contenido', $noticia->contenido) }}</textarea>
                @error('contenido') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            <div class="admin-form-group full">
                <label for="archivos">Archivos adjuntos</label>
                <input type="file" name="archivos[]" id="archivos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                @error('archivos.*') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

        @if($noticia->archivos->count())
            <div class="admin-form-group full">
                <label>Archivos adjuntos actuales</label>

                <div class="archivos-grid" id="archivos-grid">
                    @foreach($noticia->archivos as $archivo)
                        @php
                            $ext = strtolower($archivo->extension);
                            $icono = match($ext) {
                                'pdf' => 'fa-file-pdf',
                                'doc', 'docx' => 'fa-file-word',
                                'xls', 'xlsx' => 'fa-file-excel',
                                default => 'fa-paperclip',
                            };
                        @endphp

                        <div class="archivo-card" id="archivo-{{ $archivo->id }}">
                            <div class="archivo-card__main">
                                <i class="fa-solid {{ $icono }} archivo-icono"></i>

                                <div class="archivo-info">
                                    <span class="archivo-nombre">{{ $archivo->nombre_original }}</span>
                                    <span class="archivo-meta">
                                        {{ strtoupper($ext) }} · {{ $archivo->tamano_legible }}
                                    </span>
                                </div>
                            </div>

                            <div class="archivo-card__actions">
                                <a href="{{ $archivo->ruta }}" target="_blank" class="btn btn-secondary">
                                    Ver
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-eliminar-archivo"
                                    data-id="{{ $archivo->id }}"
                                    data-url="{{ route('admin.noticias.archivos.destroy', $archivo) }}"
                                >
                                    Quitar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Actualizar noticia</button>
    </form>
@endsection

@push('scripts_head')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-noticias.js') }}"></script>
    <script src="{{ asset('js/admin-archivos.js') }}"></script>
    <script src="{{ asset('js/admin-editor.js') }}"></script>
@endpush