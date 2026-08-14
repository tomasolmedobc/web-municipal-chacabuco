@extends('layouts.app')

@section('title', 'Gestion de Reclamos')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Reclamos'],
]" />
<section class="rec-hero">
    <span class="section-badge">Tramites</span>
    <h1>Gestion de Reclamos</h1>
    <p>Cargue un reclamo municipal o consulte el estado de una solicitud existente con su codigo.</p>
</section>

@if($codigoCreado)
    <section class="rec-success">
        <i class="fa-solid fa-circle-check"></i>
        <div>
            <h2>Su reclamo fue enviado</h2>
            <p>Numero de Reclamo: <strong>{{ $codigoCreado }}</strong>. Use este numero para consultar el estado.</p>
        </div>
    </section>
@endif

@if($mensaje)
    <section class="rec-message">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $mensaje }}</span>
    </section>
@endif

<section class="rec-layout">
    <form action="{{ route('reclamos.store') }}" method="POST" enctype="multipart/form-data" class="rec-form" data-reclamos-form>
        @csrf

        <div class="rec-form__header">
            <span class="rec-form__icon"><i class="fa-solid fa-comments"></i></span>
            <div>
                <h2>Nuevo reclamo</h2>
                <p>Complete sus datos, ubicacion y detalle del reclamo.</p>
            </div>
        </div>

        <div class="rec-grid">
            <label>
                <span>Apellido</span>
                <input type="text" name="apellido" value="{{ old('apellido') }}" required>
                @error('apellido') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Nombre</span>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                @error('nombre') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Telefono</span>
                <input type="text" name="telefono" value="{{ old('telefono') }}" maxlength="10" required>
                @error('telefono') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>DNI</span>
                <input type="text" name="dni" value="{{ old('dni') }}" maxlength="9" required>
                @error('dni') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Area</span>
                <select name="area" data-area-select required>
                    <option value="">Seleccionar area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id_area }}" {{ old('area') == $area->id_area ? 'selected' : '' }}>{{ $area->descripcion }}</option>
                    @endforeach
                </select>
                @error('area') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Tipo</span>
                <select name="tipo" data-tipo-select required>
                    <option value="">Seleccionar tipo</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tipo_reclamo }}" data-area="{{ $tipo->id_area }}" {{ old('tipo') == $tipo->id_tipo_reclamo ? 'selected' : '' }}>{{ $tipo->descripcion }}</option>
                    @endforeach
                </select>
                @error('tipo') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Calle</span>
                <select name="calle">
                    <option value="0">Sin especificar</option>
                    @foreach($calles as $calle)
                        <option value="{{ $calle->id_calle }}" {{ old('calle') == $calle->id_calle ? 'selected' : '' }}>{{ $calle->descripcion }}</option>
                    @endforeach
                </select>
                @error('calle') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Altura</span>
                <input type="number" name="altura" value="{{ old('altura') }}" min="0" max="99999">
                @error('altura') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        <label class="rec-wide">
            <span>Imagen</span>
            <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp">
            @error('imagen') <small>{{ $message }}</small> @enderror
        </label>

        <label class="rec-wide">
            <span>Detalles de su reclamo</span>
            <textarea name="detalles" rows="5" required>{{ old('detalles') }}</textarea>
            @error('detalles') <small>{{ $message }}</small> @enderror
        </label>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane"></i>
            Enviar reclamo
        </button>
    </form>

    <aside class="rec-consulta">
        <h2>Consultar reclamo</h2>
        <p>Ingrese el codigo generado al cargar su reclamo.</p>

        <form action="{{ route('reclamos.consultar') }}" method="POST">
            @csrf
            <label>
                <span>Codigo de Reclamo</span>
                <input type="number" name="idreclamo" value="{{ old('idreclamo', $consultaCodigo) }}" min="1" required>
                @error('idreclamo') <small>{{ $message }}</small> @enderror
            </label>

            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i>
                Consultar
            </button>
        </form>
    </aside>
</section>

@if($reclamo)
    <section class="rec-result">
        <div class="section-heading">
            <h2>Informacion de su Reclamo</h2>
            <p>Codigo consultado: <strong>{{ $consultaCodigo }}</strong></p>
        </div>

        <div class="rec-detail">
            <div><span>Fecha</span><strong>{{ $reclamo->fecha }}</strong></div>
            <div><span>Area</span><strong>{{ $reclamo->area ?: '-' }}</strong></div>
            <div><span>Tipo</span><strong>{{ $reclamo->tipo ?: '-' }}</strong></div>
            <div><span>Estado</span><strong>{{ $reclamo->estado ?: '-' }}</strong></div>
            <div class="rec-detail__wide"><span>Observaciones</span><strong>{{ $reclamo->observaciones ?: '-' }}</strong></div>
        </div>
    </section>

    <section class="rec-result">
        <div class="section-heading">
            <h2>Movimientos</h2>
            <p>Recorrido registrado para el reclamo.</p>
        </div>

        @forelse($pases as $pase)
            <article class="rec-pass">
                <strong>{{ $pase->fecha }}</strong>
                <span>{{ $pase->area ?: 'Area no informada' }}</span>
                <p>{{ $pase->observaciones ?: 'Sin observaciones' }}</p>
            </article>
        @empty
            <div class="rec-empty">No hay movimientos asociados a este reclamo.</div>
        @endforelse
    </section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-reclamos-form]');
    if (!form) return;

    const area = form.querySelector('[data-area-select]');
    const tipo = form.querySelector('[data-tipo-select]');
    const options = Array.from(tipo.options);

    const refreshTipos = () => {
        const areaId = area.value;
        options.forEach((option) => {
            if (!option.value) return;
            const visible = option.dataset.area === areaId || option.textContent.trim().toUpperCase() === 'OTROS';
            option.hidden = !visible;
            option.disabled = !visible;
        });

        if (tipo.selectedOptions[0]?.disabled) {
            tipo.value = '';
        }
    };

    area.addEventListener('change', refreshTipos);
    refreshTipos();
});
</script>
@endpush
