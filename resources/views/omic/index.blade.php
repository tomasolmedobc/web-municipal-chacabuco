@extends('layouts.app')

@section('title', 'OMIC')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'OMIC'],
]" />
<section class="omic-hero">
    <span class="section-badge">Servicios</span>
    <h1>OMIC</h1>
    <p>Complete el formulario para iniciar un reclamo de defensa del consumidor ante la Oficina Municipal de Informacion al Consumidor.</p>
</section>

@if($codigoCreado)
    <section class="omic-success">
        <i class="fa-solid fa-circle-check"></i>
        <div>
            <h2>Su reclamo fue enviado</h2>
            <p>Numero de Reclamo: <strong>{{ $codigoCreado }}</strong>. Guarde este codigo para futuras consultas.</p>
        </div>
    </section>
@endif

@if($mensaje)
    <section class="omic-message">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $mensaje }}</span>
    </section>
@endif

<form action="{{ route('omic.store') }}" method="POST" enctype="multipart/form-data" class="omic-form">
    @csrf

    <section class="omic-panel">
        <div class="omic-panel__header">
            <span><i class="fa-solid fa-user"></i></span>
            <div>
                <h2>Datos del reclamante</h2>
                <p>Ingrese los datos de la persona que realiza el reclamo.</p>
            </div>
        </div>

        <div class="omic-grid">
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
                <span>DNI</span>
                <input type="text" name="dni" value="{{ old('dni') }}" minlength="7" maxlength="9" required>
                @error('dni') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Fecha de nacimiento</span>
                <input type="date" name="nacimiento" value="{{ old('nacimiento') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                @error('nacimiento') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Telefono</span>
                <input type="text" name="telefono" value="{{ old('telefono') }}" maxlength="20">
                @error('telefono') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Celular</span>
                <input type="text" name="celular" value="{{ old('celular') }}" maxlength="20" required>
                @error('celular') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Direccion</span>
                <input type="text" name="direccion" value="{{ old('direccion') }}" required>
                @error('direccion') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Localidad</span>
                <select name="localidad" required>
                    <option value="">Seleccionar localidad</option>
                    @foreach($localidades as $localidad)
                        <option value="{{ $localidad->id_localidad }}" {{ old('localidad') == $localidad->id_localidad ? 'selected' : '' }}>{{ $localidad->descripcion }}</option>
                    @endforeach
                </select>
                @error('localidad') <small>{{ $message }}</small> @enderror
            </label>

            <label>
                <span>Codigo postal</span>
                <input type="number" name="cp" value="{{ old('cp') }}" min="1000" max="9999">
                @error('cp') <small>{{ $message }}</small> @enderror
            </label>
        </div>
    </section>

    @foreach([1, 2, 3] as $numero)
        <section class="omic-panel">
            <div class="omic-panel__header">
                <span><i class="fa-solid fa-building"></i></span>
                <div>
                    <h2>Datos del denunciado {{ $numero }}</h2>
                    <p>{{ $numero === 1 ? 'El primer denunciado es obligatorio.' : 'Complete esta seccion solo si corresponde.' }}</p>
                </div>
            </div>

            <div class="omic-grid">
                <label>
                    <span>Nombre o razon social</span>
                    <input type="text" name="nombre{{ $numero }}" value="{{ old('nombre' . $numero) }}" {{ $numero === 1 ? 'required' : '' }}>
                    @error('nombre' . $numero) <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Telefono</span>
                    <input type="text" name="telefono{{ $numero }}" value="{{ old('telefono' . $numero) }}" maxlength="20">
                    @error('telefono' . $numero) <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Direccion</span>
                    <input type="text" name="direccion{{ $numero }}" value="{{ old('direccion' . $numero) }}">
                    @error('direccion' . $numero) <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Localidad</span>
                    <input type="text" name="localidad{{ $numero }}" value="{{ old('localidad' . $numero) }}" {{ $numero === 1 ? 'required' : '' }}>
                    @error('localidad' . $numero) <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="omic-grid omic-grid--text">
                <label>
                    <span>Reclamo</span>
                    <textarea name="reclamo{{ $numero }}" rows="5" {{ $numero === 1 ? 'required' : '' }}>{{ old('reclamo' . $numero) }}</textarea>
                    @error('reclamo' . $numero) <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Pretension</span>
                    <textarea name="pretension{{ $numero }}" rows="5" {{ $numero === 1 ? 'required' : '' }}>{{ old('pretension' . $numero) }}</textarea>
                    @error('pretension' . $numero) <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="omic-grid">
                <label>
                    <span>Autoriza a OMIC a divulgar los datos al denunciado</span>
                    <select name="autorizado{{ $numero }}">
                        <option value="No" {{ old('autorizado' . $numero, 'No') === 'No' ? 'selected' : '' }}>No</option>
                        <option value="Si" {{ old('autorizado' . $numero) === 'Si' ? 'selected' : '' }}>Si</option>
                    </select>
                    @error('autorizado' . $numero) <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Observaciones</span>
                    <input type="text" name="observaciones{{ $numero }}" value="{{ old('observaciones' . $numero) }}">
                    @error('observaciones' . $numero) <small>{{ $message }}</small> @enderror
                </label>
            </div>
        </section>
    @endforeach

    <section class="omic-panel">
        <div class="omic-panel__header">
            <span><i class="fa-solid fa-paperclip"></i></span>
            <div>
                <h2>Archivos adjuntos</h2>
                <p>Puede agregar hasta tres archivos en formato PDF, DOC, DOCX, JPG o PNG.</p>
            </div>
        </div>

        <div class="omic-grid">
            @foreach([1, 2, 3] as $numero)
                <label>
                    <span>Archivo {{ $numero }}</span>
                    <input type="file" name="archivo{{ $numero }}" accept=".jpg,.jpeg,.png,.doc,.docx,.pdf">
                    @error('archivo' . $numero) <small>{{ $message }}</small> @enderror
                </label>
            @endforeach
        </div>
    </section>

    <div class="omic-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane"></i>
            Enviar reclamo OMIC
        </button>
    </div>
</form>

<section class="volver-ts">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>
@endsection
