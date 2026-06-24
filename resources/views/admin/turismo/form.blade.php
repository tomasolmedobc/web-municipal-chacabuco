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
            <input type="text"
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

        <div class="admin-form-group">
            <label for="mostrar_detalle" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="mostrar_detalle" id="mostrar_detalle" value="1" {{ old('mostrar_detalle', $item->mostrar_detalle) ? 'checked' : '' }} style="width:auto;">
                Mostrar página de detalle completa
            </label>
            <small class="fecha">Si está activo, la card tendrá un link "Ver más" que abre el detalle.</small>
        </div>

        <div class="admin-form-group full">
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp">
            <small class="fecha">Formatos: JPG, PNG, WEBP. Máximo 4MB.</small>
            @error('imagen') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $item->imagen)
            <div class="admin-form-group full">
                <label>Imagen de portada actual</label>
                <img src="{{ $item->imagen_url }}" alt="{{ $item->titulo }}" class="preview-imagen-admin">
            </div>
        @endif

        @if($esEdicion)
            <div class="admin-form-group full">
                <label for="galeria">Galería de imágenes <small class="fecha">(podés subir varias a la vez)</small></label>
                <input type="file" name="galeria[]" id="galeria" accept=".jpg,.jpeg,.png,.webp" multiple>
                <small class="fecha">JPG, PNG, WEBP. Máximo 8MB por imagen.</small>
                @error('galeria.*') <small class="auth-error">{{ $message }}</small> @enderror
            </div>

            @if($item->galeria && $item->galeria->count())
                <div class="admin-form-group full">
                    <label>
                        Imágenes de galería actuales
                        <small class="fecha"> — <i class="fa-regular fa-star"></i> = usar como portada del header</small>
                    </label>
                    <div class="turismo-galeria-admin">
                        @foreach($item->galeria as $img)
                            <div class="turismo-galeria-admin__item {{ $img->es_header ? 'turismo-galeria-admin__item--header' : '' }}">
                                <img src="{{ $img->imagen_url }}" alt="Imagen galería">

                                @if($img->es_header)
                                    <span class="turismo-galeria-admin__badge-header" title="Portada del header activa">
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                @else
                                    <button type="button"
                                            class="turismo-galeria-admin__set-header js-accion-ajax"
                                            data-url="{{ route('admin.turismo.galeria.header', [$item, $img]) }}"
                                            data-method="PATCH"
                                            title="Usar como portada del header">
                                        <i class="fa-regular fa-star"></i>
                                    </button>
                                @endif

                                <button type="button"
                                        class="turismo-galeria-admin__borrar js-accion-ajax"
                                        data-url="{{ route('admin.turismo.galeria.destroy', [$item, $img]) }}"
                                        data-method="DELETE"
                                        data-confirm="¿Eliminar esta imagen de la galería?"
                                        title="Eliminar">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @endif

        <div class="admin-form-group full">
            <label for="adjuntos">Archivos adjuntos <small class="fecha">(PDF, Word, Excel, ZIP — podés subir varios a la vez)</small></label>
            <input type="file" name="adjuntos[]" id="adjuntos" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" multiple>
            <small class="fecha">Máximo 20MB por archivo.</small>
            @error('adjuntos.*') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $item->archivos && $item->archivos->count())
            <div class="admin-form-group full">
                <label>Archivos adjuntos actuales</label>
                <div class="turismo-archivos-admin">
                    @foreach($item->archivos as $archivo)
                        @php
                            $ext = strtolower($archivo->extension);
                            $icono = match($ext) {
                                'pdf'        => 'fa-file-pdf',
                                'doc','docx' => 'fa-file-word',
                                'xls','xlsx' => 'fa-file-excel',
                                'zip'        => 'fa-file-zipper',
                                default      => 'fa-paperclip',
                            };
                        @endphp
                        <div class="turismo-archivos-admin__item">
                            <i class="fa-solid {{ $icono }}"></i>
                            <span class="turismo-archivos-admin__nombre" title="{{ $archivo->nombre_original }}">
                                {{ Str::limit($archivo->nombre_original, 40) }}
                            </span>
                            <span class="turismo-archivos-admin__meta">{{ strtoupper($ext) }} · {{ $archivo->tamano_legible }}</span>
                            <button type="button"
                                    class="turismo-archivos-admin__borrar js-accion-ajax"
                                    data-url="{{ route('admin.turismo.archivos.destroy', [$item, $archivo]) }}"
                                    data-method="DELETE"
                                    data-confirm="¿Eliminar el archivo «{{ $archivo->nombre_original }}»?"
                                    title="Eliminar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
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

            // Acciones de galería y archivos sin formularios anidados
            document.querySelectorAll('.js-accion-ajax').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const msg = this.dataset.confirm;
                    if (msg && !window.confirm(msg)) return;

                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    const body  = new FormData();
                    body.append('_token', token);
                    body.append('_method', this.dataset.method || 'POST');

                    this.disabled = true;

                    try {
                        await fetch(this.dataset.url, { method: 'POST', body });
                        window.location.reload();
                    } catch (e) {
                        this.disabled = false;
                        alert('Error al procesar la acción. Intentá de nuevo.');
                    }
                });
            });
        });
    </script>
@endpush
