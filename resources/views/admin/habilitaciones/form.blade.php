@extends('layouts.app')

@php
    $esEdicion = $modo === 'editar';
    $esOrdenanza = $seccionActiva === \App\Models\HabilitacionDocumento::SECCION_ORDENANZAS;
    $tituloPagina = $esEdicion ? 'Editar ' . $config['singular'] : 'Nuevo ' . $config['singular'];
    $accion = $esEdicion
        ? route('admin.habilitaciones.update', $documento)
        : route('admin.habilitaciones.store');
@endphp

@section('title', ucfirst($tituloPagina))

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ ucfirst($tituloPagina) }}</h2>
        <p class="admin-subtitle">{{ $config['descripcion'] }}</p>
    </div>

    <a href="{{ route('admin.habilitaciones.index', ['seccion' => $seccionActiva]) }}"
       class="btn btn-secondary">
        Volver
    </a>
</section>

<form action="{{ $accion }}"
      method="POST"
      enctype="multipart/form-data"
      class="admin-form-card">
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <input type="hidden" name="seccion" value="{{ old('seccion', $seccionActiva) }}">

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="titulo">Titulo</label>
            <input type="text"
                   name="titulo"
                   id="titulo"
                   value="{{ old('titulo', $documento->titulo) }}"
                   required>
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripcion</label>
            <textarea name="descripcion"
                      id="descripcion"
                      rows="4"
                      placeholder="{{ $esOrdenanza ? 'Si lo dejas vacio se usara una descripcion generica para ordenanzas.' : 'Si lo dejas vacio se usara una descripcion generica para documentos de habilitaciones.' }}">{{ old('descripcion', $documento->descripcion) }}</textarea>
            <small class="fecha">
                Opcional. Si no completas este campo, se cargara una descripcion por defecto.
            </small>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="categoria">{{ $esOrdenanza ? 'Rubro / Categoria' : 'Categoria' }}</label>
            <input type="text"
                   name="categoria"
                   id="categoria"
                   value="{{ old('categoria', $documento->categoria) }}"
                   placeholder="{{ $esOrdenanza ? 'Ej: Food truck' : 'Ej: Formulario, Requisito' }}">
            @error('categoria') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esOrdenanza)
            <div class="admin-form-group">
                <label for="numero">Numero de ordenanza</label>
                <input type="text"
                       name="numero"
                       id="numero"
                       value="{{ old('numero', $documento->numero) }}"
                       placeholder="Ej: 7427/17"
                       required>
                @error('numero') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        @endif

        <div class="admin-form-group">
            <label for="anio">Año</label>
            <input type="number"
                   name="anio"
                   id="anio"
                   min="1900"
                   max="{{ date('Y') + 1 }}"
                   value="{{ old('anio', $documento->anio) }}">
            @error('anio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="visible" {{ old('estado', $documento->estado ?: 'visible') === 'visible' ? 'selected' : '' }}>
                    Visible
                </option>
                <option value="oculto" {{ old('estado', $documento->estado) === 'oculto' ? 'selected' : '' }}>
                    Oculto
                </option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number"
                   name="orden"
                   id="orden"
                   min="0"
                   max="999"
                   value="{{ old('orden', $documento->orden ?? 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivo">{{ $esOrdenanza ? 'Archivo PDF' : 'Archivo' }}</label>
            <input type="file"
                   name="archivo"
                   id="archivo"
                   accept="{{ $esOrdenanza ? '.pdf' : '.pdf,.doc,.docx' }}"
                   {{ $esEdicion || old('link_externo', $documento->link_externo) ? '' : 'required' }}>
            <small class="fecha">
                {{ $esOrdenanza ? 'Solo PDF.' : 'Formatos permitidos: PDF, DOC, DOCX.' }} Maximo 10MB. Tambien podes usar solo un link externo.
            </small>
            @error('archivo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="link_externo">Link externo opcional</label>
            <input type="url"
                   name="link_externo"
                   id="link_externo"
                   value="{{ old('link_externo', $documento->link_externo) }}"
                   placeholder="https://...">
            <small class="fecha">
                Si no cargas archivo, el documento puede abrir este link. Si cargas ambos, en la pagina se prioriza el archivo.
            </small>
            @error('link_externo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $documento->archivo_ruta)
            <div class="admin-form-group full">
                <label>Archivo actual</label>

                <div class="archivo-card">
                    <div class="archivo-card__main">
                        <i class="fa-solid fa-file-pdf archivo-icono"></i>

                        <div class="archivo-info">
                            <span class="archivo-nombre">{{ $documento->archivo_nombre }}</span>
                            <span class="archivo-meta">{{ $documento->archivo_peso_legible }}</span>
                        </div>
                    </div>

                    <div class="archivo-card__actions">
                        <a href="{{ $documento->archivo_ruta }}" target="_blank" class="btn btn-secondary">
                            Ver
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $esEdicion ? 'Actualizar' : 'Guardar' }}
    </button>
</form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const archivo = document.getElementById('archivo');
            const link = document.getElementById('link_externo');

            if (!archivo || !link) {
                return;
            }

            const actualizarArchivoRequerido = () => {
                if (link.value.trim()) {
                    archivo.removeAttribute('required');
                    return;
                }

                if (!@json($esEdicion)) {
                    archivo.setAttribute('required', 'required');
                }
            };

            link.addEventListener('input', actualizarArchivoRequerido);
            actualizarArchivoRequerido();
        });
    </script>
@endpush
