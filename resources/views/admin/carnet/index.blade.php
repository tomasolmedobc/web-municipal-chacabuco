@extends('layouts.app')

@section('title', 'Carnet de Conducir — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Carnet de Conducir</h2>
        <p class="admin-subtitle">Editar contenido, pasos y materiales de descarga.</p>
    </div>
    <div class="admin-btn-bar">
        <a href="{{ route('admin.carnet.config.edit') }}" class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square"></i> Editar contenido
        </a>
        <a href="{{ route('admin.carnet.materiales.create') }}" class="btn btn-secondary">
            <i class="fa-solid fa-plus"></i> Nuevo material
        </a>
        <a href="{{ route('carnet.index') }}" target="_blank" class="btn btn-secondary">
            <i class="fa-solid fa-eye"></i> Ver página
        </a>
    </div>
</section>

@if(session('ok'))
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

{{-- Resumen de contenido --}}
<div class="admin-form-card mb-28">
    <h3 class="admin-section-h3">Estado del contenido</h3>
    <div class="admin-stat-mini">
        @foreach([
            ['campo' => 'intro_texto',               'label' => 'Introducción'],
            ['campo' => 'alerta_info',               'label' => 'Alerta info'],
            ['campo' => 'aviso_ubicacion',            'label' => 'Aviso ubicación'],
            ['campo' => 'paso1_contenido',            'label' => 'Paso 1'],
            ['campo' => 'paso2_contenido',            'label' => 'Paso 2'],
            ['campo' => 'paso3_contenido',            'label' => 'Paso 3'],
            ['campo' => 'paso4_contenido',            'label' => 'Paso 4'],
            ['campo' => 'licencia_digital_contenido', 'label' => 'Licencia digital'],
        ] as $item)
            <div class="admin-stat-row">
                @if($config->{$item['campo']})
                    <i class="fa-solid fa-circle-check text-success"></i>
                @else
                    <i class="fa-solid fa-circle-xmark text-danger"></i>
                @endif
                {{ $item['label'] }}
            </div>
        @endforeach
    </div>
</div>

{{-- Lista de materiales --}}
<div class="admin-card-header">
    <h3>Materiales de descarga</h3>
    <span class="badge-count">{{ $materiales->count() }} en total</span>
</div>

@if($materiales->isEmpty())
    <div class="admin-list-item">
        <p>No hay materiales cargados. Hacé clic en "Nuevo material" para agregar uno.</p>
    </div>
@else
    @foreach($materiales as $mat)
        <div class="admin-list-item">
            <div class="item-main-row">
                <div class="icon-badge">
                    <i class="fa-solid {{ $mat->tipo_boton === 'ver' ? 'fa-link' : 'fa-file-pdf' }}"></i>
                </div>
                <div class="item-text">
                    <div class="item-title">
                        {{ $mat->titulo }}
                        @if($mat->subtitulo)
                            <span class="item-subtitle">({{ $mat->subtitulo }})</span>
                        @endif
                    </div>
                    <div class="item-meta">
                        Botón: <strong>{{ $mat->tipo_boton === 'ver' ? 'VER INFORMACIÓN' : 'DESCARGAR ARCHIVO' }}</strong>
                        · Orden: {{ $mat->orden }}
                        @if($mat->url)
                            · <a href="{{ $mat->url }}" target="_blank" class="link-primary-sm">Ver archivo</a>
                        @else
                            · <span class="text-danger">Sin archivo</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="admin-list-item__actions">
                <form method="POST" action="{{ route('admin.carnet.materiales.toggle', $mat) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secondary btn-sm">
                        {{ $mat->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>

                <a href="{{ route('admin.carnet.materiales.edit', $mat) }}" class="btn btn-secondary btn-sm">
                    Editar
                </a>

                <form method="POST" action="{{ route('admin.carnet.materiales.destroy', $mat) }}" class="d-inline"
                      data-confirm="¿Eliminar el material «{{ $mat->titulo }}»? Esta acción no se puede deshacer.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </div>
        </div>
    @endforeach
@endif

@endsection
