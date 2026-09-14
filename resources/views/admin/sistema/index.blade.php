@extends('layouts.app')

@section('title', 'Configuración del sistema')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Configuración del sistema</h2>
        <p class="admin-subtitle">Administrá opciones generales del portal.</p>
    </div>

    <a href="{{ route('admin.perfil.edit') }}" class="btn btn-secondary">Volver</a>
</section>

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

@if(session('error'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('error')), 'error');
        });
    </script>
@endif


<form action="{{ route('admin.sistema.update') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')

    <div class="admin-form-grid">

        {{-- LOGO --}}
        <div class="admin-form-group">
            <label>Logo del sitio</label>

            @if($logo)
                <div class="config-preview">
                    <img src="{{ $logo }}" alt="Logo actual">
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_logo" value="1">
                    <span>Quitar logo actual</span>
                </label>
            @endif

            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
            @error('logo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        {{-- PORTADA --}}
        <div class="admin-form-group full">
            <label>Imagen de portada</label>

            @if($portada)
                <div class="config-preview--portada" id="portada-preview-wrap">
                    <img
                        id="portada-preview-img"
                        src="{{ $portada }}"
                        alt="Portada actual"
                    >
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_portada" value="1">
                    <span>Quitar portada actual</span>
                </label>
            @endif

            <input type="file" name="portada" accept=".jpg,.jpeg,.png,.webp" id="portada-file-input">
            @error('portada') <small class="auth-error">{{ $message }}</small> @enderror

            {{-- Recorte de imagen --}}
            @if($portada)
            <div class="portada-ajustes portada-ajustes--crop">
                <span class="portada-ajuste-label">Recortar imagen</span>
                <p class="fecha mt-0 mb-8">Arrastrá los controles para eliminar el exceso de imagen en cada lado. El recorte se aplica sobre el original al guardar.</p>

                {{-- Preview de recorte (muestra el original con sombreado) --}}
                <div class="portada-crop-preview" id="portada-crop-preview">
                    <img id="portada-crop-img"
                         src="{{ $portada_orig ?? $portada }}"
                         alt="Original para recorte">
                    <div class="portada-crop-shade portada-crop-shade--top"    id="pcs-top"></div>
                    <div class="portada-crop-shade portada-crop-shade--bottom" id="pcs-bottom"></div>
                    <div class="portada-crop-shade portada-crop-shade--left"   id="pcs-left"></div>
                    <div class="portada-crop-shade portada-crop-shade--right"  id="pcs-right"></div>
                    <div class="portada-crop-border" id="portada-crop-border"></div>
                </div>

                <div class="portada-crop-sliders">
                    @foreach([
                        ['top',    'Arriba',    $portada_crop_top,    '↑'],
                        ['bottom', 'Abajo',     $portada_crop_bottom, '↓'],
                        ['left',   'Izquierda', $portada_crop_left,   '←'],
                        ['right',  'Derecha',   $portada_crop_right,  '→'],
                    ] as [$side, $label, $val, $arrow])
                    <div class="crop-slider-row">
                        <span class="crop-slider-label">
                            {{ $arrow }} {{ $label }}
                        </span>
                        <input
                            type="range"
                            class="crop-range"
                            id="crop-{{ $side }}"
                            name="portada_crop_{{ $side }}"
                            min="0" max="45" step="1"
                            value="{{ $val }}"
                        >
                        <span class="crop-slider-val" id="crop-{{ $side }}-val">{{ $val }}%</span>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary btn-sm" id="crop-reset-btn" style="margin-top:10px">
                    <i class="fa-solid fa-rotate-left"></i> Restablecer recorte
                </button>
            </div>
            @endif

            {{-- Control de zoom y altura --}}
            <div class="portada-ajustes portada-ajustes--doble">
                <div class="portada-ajuste-bloque">
                    <span class="portada-ajuste-label">Zoom</span>
                    <p class="fecha mt-0 mb-8">Acercá o alejá la imagen dentro del marco.</p>
                    <div class="portada-zoom-wrap">
                        <span class="portada-zoom-side">− Lejos</span>
                        <input
                            type="range"
                            id="portada-zoom-slider"
                            name="portada_zoom"
                            min="1" max="2" step="0.05"
                            value="{{ $portada_zoom }}"
                            class="portada-zoom-range"
                        >
                        <span class="portada-zoom-side">Cerca +</span>
                    </div>
                    <p class="portada-zoom-val-label">
                        Zoom actual: <strong id="portada-zoom-val">{{ round(($portada_zoom - 1) * 100) }}%</strong>
                    </p>
                    @error('portada_zoom') <small class="auth-error">{{ $message }}</small> @enderror
                </div>

                <div class="portada-ajuste-bloque">
                    <span class="portada-ajuste-label">Altura de la portada</span>
                    <p class="fecha mt-0 mb-8">Controlá qué tan alta aparece la imagen en la portada del sitio.</p>
                    <div class="portada-altura-opciones">
                        @foreach(['320' => 'Baja', '380' => 'Normal', '440' => 'Alta', '500' => 'Muy alta'] as $px => $etiqueta)
                        <label
                            class="portada-altura-btn {{ $portada_altura === $px ? 'portada-altura-btn--active' : '' }}"
                            data-altura="{{ $px }}"
                        >
                            <input type="radio" name="portada_altura" value="{{ $px }}" {{ $portada_altura === $px ? 'checked' : '' }}>
                            <span class="portada-altura-bar" style="height: {{ (int)$px / 10 }}px"></span>
                            <span>{{ $etiqueta }}</span>
                            <small>{{ $px }}px</small>
                        </label>
                        @endforeach
                    </div>
                    @error('portada_altura') <small class="auth-error">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        {{-- IMAGEN DEFAULT --}}
        <div class="admin-form-group full">
            <label>Imagen por defecto para noticias</label>

            @if($default_noticia)
                <div class="config-preview">
                    <img src="{{ $default_noticia }}" alt="Imagen por defecto actual">
                </div>

                <label class="config-remove">
                    <input type="checkbox" name="eliminar_default_noticia" value="1">
                    <span>Quitar imagen actual</span>
                </label>
            @endif

            <input type="file" name="default_noticia" accept=".jpg,.jpeg,.png,.webp">
            @error('default_noticia') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

    </div>

        {{-- WHATSAPP --}}
        <div class="admin-form-group full">
            <label class="form-label-block">Botón de WhatsApp</label>
            <p class="admin-subtitle" style="margin-bottom:12px">Muestra un botón flotante de contacto por WhatsApp en todo el sitio.</p>

            <label class="config-toggle-label">
                <input type="checkbox" name="whatsapp_activo" value="1" {{ $whatsapp_activo ? 'checked' : '' }}>
                <span>Activar botón de WhatsApp</span>
            </label>

            <div class="admin-form-group" style="margin-top:12px">
                <label for="whatsapp_url">URL de WhatsApp <small class="text-muted">(ej: https://wa.me/5492352000000)</small></label>
                <input
                    type="url"
                    id="whatsapp_url"
                    name="whatsapp_url"
                    value="{{ $whatsapp_url }}"
                    placeholder="https://wa.me/5492352XXXXXX"
                    class="filtro-input"
                    style="max-width:420px"
                >
                @error('whatsapp_url') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        </div>

    <button type="submit" class="btn btn-primary">
        Guardar configuración
    </button>
</form>
@endsection

@push('scripts')
<script @nonce>
(function () {
    var previewImg  = document.getElementById('portada-preview-img');
    var previewWrap = document.getElementById('portada-preview-wrap');
    var fileInput   = document.getElementById('portada-file-input');

    // ── Live preview al cargar nueva imagen ──────────────────────────
    if (fileInput && previewImg) {
        fileInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (e) { previewImg.src = e.target.result; };
            reader.readAsDataURL(file);
        });
    }

    // ── Zoom ─────────────────────────────────────────────────────────
    var zoomSlider  = document.getElementById('portada-zoom-slider');
    var zoomValEl   = document.getElementById('portada-zoom-val');

    if (zoomSlider) {
        zoomSlider.addEventListener('input', function () {
            var z = parseFloat(this.value);
            if (zoomValEl) zoomValEl.textContent = Math.round((z - 1) * 100) + '%';
            if (previewImg) previewImg.style.transform = 'scale(' + z + ')';
        });
    }

    // ── Recorte (sliders) ────────────────────────────────────────────
    var cropSides = ['top', 'bottom', 'left', 'right'];
    var cropShades = {
        top:    document.getElementById('pcs-top'),
        bottom: document.getElementById('pcs-bottom'),
        left:   document.getElementById('pcs-left'),
        right:  document.getElementById('pcs-right'),
    };
    var cropBorder = document.getElementById('portada-crop-border');

    function updateCropPreview() {
        var vals = {};
        cropSides.forEach(function (s) {
            var el = document.getElementById('crop-' + s);
            vals[s] = el ? parseInt(el.value, 10) : 0;
            var valEl = document.getElementById('crop-' + s + '-val');
            if (valEl) valEl.textContent = vals[s] + '%';
        });
        if (cropShades.top)    cropShades.top.style.height    = vals.top    + '%';
        if (cropShades.bottom) cropShades.bottom.style.height = vals.bottom + '%';
        if (cropShades.left)   cropShades.left.style.width    = vals.left   + '%';
        if (cropShades.right)  cropShades.right.style.width   = vals.right  + '%';
        // Mover el borde del recorte
        if (cropBorder) {
            cropBorder.style.top    = vals.top    + '%';
            cropBorder.style.left   = vals.left   + '%';
            cropBorder.style.right  = vals.right  + '%';
            cropBorder.style.bottom = vals.bottom + '%';
        }
    }

    cropSides.forEach(function (s) {
        var el = document.getElementById('crop-' + s);
        if (el) el.addEventListener('input', updateCropPreview);
    });

    // Botón restablecer
    var cropResetBtn = document.getElementById('crop-reset-btn');
    if (cropResetBtn) {
        cropResetBtn.addEventListener('click', function () {
            cropSides.forEach(function (s) {
                var el = document.getElementById('crop-' + s);
                if (el) el.value = 0;
            });
            updateCropPreview();
        });
    }

    updateCropPreview(); // inicializar con valores actuales

    // ── Botones de altura ────────────────────────────────────────────
    document.querySelectorAll('.portada-altura-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.portada-altura-btn').forEach(function (b) {
                b.classList.remove('portada-altura-btn--active');
            });
            btn.classList.add('portada-altura-btn--active');
            if (previewWrap) {
                var px = parseInt(btn.dataset.altura, 10);
                previewWrap.style.height = Math.round(px * 0.65) + 'px';
            }
        });
    });
})();
</script>
@endpush