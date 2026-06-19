@extends('layouts.app')

@php
    $esEdicion = $modo === 'editar';
    $tituloPagina = $esEdicion ? 'Editar ' . $config['singular'] : 'Nuevo ' . $config['singular'];
    $accion = $esEdicion
        ? route('admin.turismo.update', $item)
        : route('admin.turismo.store');
@endphp

@section('title', ucfirst($tituloPagina))

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ ucfirst($tituloPagina) }}</h2>
        <p class="admin-subtitle">{{ $config['descripcion'] }}</p>
    </div>

    <a href="{{ route('admin.turismo.index', ['tipo' => $tipoActivo]) }}" class="btn btn-secondary">
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

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo" required>
                @foreach($tipos as $tipo => $config_tipo)
                    <option value="{{ $tipo }}" {{ old('tipo', $tipoActivo) === $tipo ? 'selected' : '' }}>
                        {{ $config_tipo['titulo'] }}
                    </option>
                @endforeach
            </select>
            @error('tipo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="localidad_id">Localidad</label>
            <select name="localidad_id" id="localidad_id" required>
                <option value="">Seleccioná una localidad</option>
                @foreach($localidades as $localidad)
                    <option value="{{ $localidad->id }}" {{ (string) old('localidad_id', $item->localidad_id) === (string) $localidad->id ? 'selected' : '' }}>
                        {{ $localidad->nombre }}
                    </option>
                @endforeach
            </select>
            @error('localidad_id') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $item->titulo) }}" required>
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4">{{ old('descripcion', $item->descripcion) }}</textarea>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="categoria">Categoría</label>
            <input type="text"
                   name="categoria"
                   id="categoria"
                   value="{{ old('categoria', $item->categoria) }}"
                   placeholder="Ej: Museo, Restaurante, Festival">
            @error('categoria') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $item->direccion) }}">
            @error('direccion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $item->telefono) }}">
            @error('telefono') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="link_externo">Link externo</label>
            <input type="url"
                   name="link_externo"
                   id="link_externo"
                   value="{{ old('link_externo', $item->link_externo) }}"
                   placeholder="https://...">
            @error('link_externo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group turismo-campo-evento" id="campo-fecha-inicio">
            <label for="fecha_inicio">Fecha de inicio</label>
            <input type="date"
                   name="fecha_inicio"
                   id="fecha_inicio"
                   value="{{ old('fecha_inicio', optional($item->fecha_inicio)->format('Y-m-d')) }}">
            @error('fecha_inicio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group turismo-campo-evento" id="campo-fecha-fin">
            <label for="fecha_fin">Fecha de fin</label>
            <input type="date"
                   name="fecha_fin"
                   id="fecha_fin"
                   value="{{ old('fecha_fin', optional($item->fecha_fin)->format('Y-m-d')) }}">
            @error('fecha_fin') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group turismo-campo-evento" id="campo-hora-inicio">
            <label for="hora_inicio">Hora de comienzo</label>
            <input type="time"
                   name="hora_inicio"
                   id="hora_inicio"
                   value="{{ old('hora_inicio', $item->hora_inicio ? substr($item->hora_inicio, 0, 5) : '') }}">
            @error('hora_inicio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="visible" {{ old('estado', $item->estado ?: 'visible') === 'visible' ? 'selected' : '' }}>
                    Visible
                </option>
                <option value="oculto" {{ old('estado', $item->estado) === 'oculto' ? 'selected' : '' }}>
                    Oculto
                </option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="orden">Orden</label>
            <input type="number" name="orden" id="orden" min="0" max="999" value="{{ old('orden', $item->orden ?? 0) }}">
            @error('orden') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="destacado" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="destacado" id="destacado" value="1" {{ old('destacado', $item->destacado) ? 'checked' : '' }} style="width:auto;">
                Destacado en la portada de Turismo
            </label>
        </div>

        <div class="admin-form-group full">
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp">
            <small class="fecha">Formatos: JPG, PNG, WEBP. Máximo 4MB.</small>
            @error('imagen') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $item->imagen)
            <div class="admin-form-group full">
                <label>Imagen actual</label>
                <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}" class="preview-imagen-admin">
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
            if (typeof tinymce !== 'undefined' && document.getElementById('descripcion')) {
                tinymce.init({
                    selector: '#descripcion',
                    height: 280,
                    menubar: false,
                    plugins: 'lists link code wordcount',
                    toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
                    language: 'es',
                    language_url: '/js/tinymce/langs/es.js',
                    branding: false,
                    promotion: false,
                    license_key: 'gpl',
                    setup: (editor) => {
                        editor.on('change keyup', () => tinymce.triggerSave());
                    }
                });
            }

            const tipoSelect = document.getElementById('tipo');
            const fechaInicio = document.getElementById('fecha_inicio');
            const camposEvento = document.querySelectorAll('.turismo-campo-evento');

            const actualizarCamposEvento = () => {
                const esEvento = tipoSelect.value === '{{ \App\Models\TurismoItem::TIPO_EVENTO }}';

                camposEvento.forEach((campo) => {
                    campo.style.display = esEvento ? '' : 'none';
                });

                if (esEvento) {
                    fechaInicio.setAttribute('required', 'required');
                } else {
                    fechaInicio.removeAttribute('required');
                }
            };

            tipoSelect.addEventListener('change', actualizarCamposEvento);
            actualizarCamposEvento();
        });
    </script>
@endpush
