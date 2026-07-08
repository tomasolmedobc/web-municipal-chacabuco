@extends('layouts.app')

@section('title', 'Carnet de Conducir — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Carnet de Conducir</h2>
        <p class="admin-subtitle">Editar contenido, pasos y materiales de descarga.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

{{-- Resumen de contenido --}}
<div class="admin-form-card" style="margin-bottom:28px;">
    <h3 style="margin:0 0 14px; font-size:1rem;">Estado del contenido</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px;">
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
            <div style="display:flex; align-items:center; gap:8px; padding:10px 12px; background:var(--bg); border:1px solid var(--border); border-radius:8px; font-size:.875rem;">
                @if($config->{$item['campo']})
                    <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
                @else
                    <i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i>
                @endif
                {{ $item['label'] }}
            </div>
        @endforeach
    </div>
</div>

{{-- Lista de materiales --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
    <h3 style="margin:0; font-size:1rem;">Materiales de descarga</h3>
    <span class="badge" style="background:var(--primary-soft); color:var(--primary); padding:4px 10px; border-radius:20px; font-size:.8rem;">
        {{ $materiales->count() }} en total
    </span>
</div>

@if($materiales->isEmpty())
    <div class="admin-list-item">
        <p style="margin:0; color:var(--muted);">No hay materiales cargados. Hacé clic en "Nuevo material" para agregar uno.</p>
    </div>
@else
    @foreach($materiales as $mat)
        <div class="admin-list-item">
            <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                <div style="width:36px; height:36px; border-radius:8px; background:var(--primary-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid {{ $mat->tipo_boton === 'ver' ? 'fa-link' : 'fa-file-pdf' }}" style="color:var(--primary);"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $mat->titulo }}
                        @if($mat->subtitulo)
                            <span style="font-weight:400; color:var(--muted); font-size:.85rem;">({{ $mat->subtitulo }})</span>
                        @endif
                    </div>
                    <div style="font-size:.8rem; color:var(--muted); margin-top:2px;">
                        Botón: <strong>{{ $mat->tipo_boton === 'ver' ? 'VER INFORMACIÓN' : 'DESCARGAR ARCHIVO' }}</strong>
                        · Orden: {{ $mat->orden }}
                        @if($mat->url)
                            · <a href="{{ $mat->url }}" target="_blank" style="color:var(--primary); font-size:.8rem;">Ver archivo</a>
                        @else
                            · <span style="color:#ef4444;">Sin archivo</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="admin-list-item__actions">
                <form method="POST" action="{{ route('admin.carnet.materiales.toggle', $mat) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secondary btn-sm">
                        {{ $mat->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>

                <a href="{{ route('admin.carnet.materiales.edit', $mat) }}" class="btn btn-secondary btn-sm">
                    Editar
                </a>

                <form method="POST" action="{{ route('admin.carnet.materiales.destroy', $mat) }}" style="display:inline;"
                      data-confirm="¿Eliminar el material «{{ $mat->titulo }}»? Esta acción no se puede deshacer.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </div>
        </div>
    @endforeach
@endif

@endsection
