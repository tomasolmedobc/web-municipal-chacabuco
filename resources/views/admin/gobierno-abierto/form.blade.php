@extends('layouts.app')

@php
    $esEdicion = $modo === 'editar';
    $tituloPagina = $esEdicion ? 'Editar ' . $config['singular'] : 'Nuevo ' . $config['singular'];
    $accion = $esEdicion
        ? route('admin.gobierno-abierto.update', $documento)
        : route('admin.gobierno-abierto.store');
@endphp

@section('title', ucfirst($tituloPagina))

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">{{ ucfirst($tituloPagina) }}</h2>
        <p class="admin-subtitle">
            {{ $config['descripcion'] }}
        </p>
    </div>

    <a href="{{ route('admin.gobierno-abierto.index', ['categoria' => $categoriaActiva]) }}"
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

    <input type="hidden" name="categoria" value="{{ old('categoria', $categoriaActiva) }}">

    @if($categoriaActiva !== \App\Models\Licitacion::CATEGORIA_LICITACIONES)
        <input type="hidden" name="tipo" value="{{ old('tipo', $documento->tipo ?: 'publica') }}">
    @endif

    <div class="admin-form-grid">
        <div class="admin-form-group full">
            <label for="titulo">Titulo</label>
            <input
                type="text"
                name="titulo"
                id="titulo"
                value="{{ old('titulo', $documento->titulo) }}"
                required
            >
            @error('titulo') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="descripcion">Descripcion</label>
            <textarea
                name="descripcion"
                id="descripcion"
                rows="4"
            >{{ old('descripcion', $documento->descripcion) }}</textarea>
            @error('descripcion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($categoriaActiva === \App\Models\Licitacion::CATEGORIA_LICITACIONES)
            <div class="admin-form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" required>
                    <option value="publica" {{ old('tipo', $documento->tipo ?: 'publica') === 'publica' ? 'selected' : '' }}>
                        Publica
                    </option>
                    <option value="privada" {{ old('tipo', $documento->tipo) === 'privada' ? 'selected' : '' }}>
                        Privada
                    </option>
                </select>
                @error('tipo') <small class="auth-error">{{ $message }}</small> @enderror
            </div>
        @endif

        <div class="admin-form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="activa" {{ old('estado', $documento->estado ?: 'activa') === 'activa' ? 'selected' : '' }}>
                    Activa
                </option>
                <option value="finalizada" {{ old('estado', $documento->estado) === 'finalizada' ? 'selected' : '' }}>
                    Finalizada
                </option>
            </select>
            @error('estado') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="numero_expediente">Numero de expediente</label>
            <input
                type="text"
                name="numero_expediente"
                id="numero_expediente"
                value="{{ old('numero_expediente', $documento->numero_expediente) }}"
                placeholder="Ej: 4029-1234/2026"
            >
            @error('numero_expediente') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="anio">Anio</label>
            <input
                type="number"
                name="anio"
                id="anio"
                min="2000"
                max="{{ date('Y') + 1 }}"
                value="{{ old('anio', $documento->anio ?? date('Y')) }}"
            >
            @error('anio') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group">
            <label for="fecha_publicacion">Fecha de publicacion</label>
            <input
                type="date"
                name="fecha_publicacion"
                id="fecha_publicacion"
                value="{{ old('fecha_publicacion', $documento->fecha_publicacion?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            >
            @error('fecha_publicacion') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        <div class="admin-form-group full">
            <label for="archivos">Archivos PDF</label>
            <input
                type="file"
                name="archivos[]"
                id="archivos"
                multiple
                accept=".pdf"
            >
            <small class="fecha">
                Podes seleccionar uno o varios archivos. Maximo 10MB por PDF.
            </small>
            @error('archivos.*') <small class="auth-error">{{ $message }}</small> @enderror
        </div>

        @if($esEdicion && $documento->archivos->count())
            <div class="admin-form-group full">
                <label>Archivos PDF actuales</label>

                <div class="archivos-grid" id="archivos-grid">
                    @foreach($documento->archivos as $archivo)
                        <div class="archivo-card" id="archivo-{{ $archivo->id }}">
                            <div class="archivo-card__main">
                                <i class="fa-solid fa-file-pdf archivo-icono"></i>

                                <div class="archivo-info">
                                    <span class="archivo-nombre">
                                        {{ $archivo->nombre_original }}
                                    </span>

                                    <span class="archivo-meta">
                                        PDF - {{ $archivo->tamano_legible }}
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
                                    data-url="{{ route('admin.gobierno-abierto.archivos.destroy', $archivo) }}"
                                >
                                    Quitar
                                </button>
                            </div>
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
    <script src="{{ asset('js/admin-archivos.js') }}"></script>
@endpush
