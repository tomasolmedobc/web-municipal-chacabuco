@extends('layouts.app')

@section('title', 'Recaudación — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Guía de Trámites de Recaudación</h2>
        <p class="admin-subtitle">Administrá los documentos PDF y el trámite online.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.recaudacion.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo documento
        </a>
        <a href="{{ route('recaudacion.index') }}" class="btn btn-secondary" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver página pública
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Panel</a>
    </div>
</section>

@if(session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

{{-- Trámite Online --}}
<section class="admin-form-card" style="margin-bottom:24px;">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:4px;">
        <div>
            <strong style="font-size:15px;">Trámite Online</strong>
            <p style="margin:4px 0 0; color:var(--muted); font-size:13px;">
                {{ $tramite->titulo }}
            </p>
            @if($tramite->url)
                <a href="{{ $tramite->url }}" target="_blank" rel="noopener"
                   style="font-size:12px; color:var(--primary); word-break:break-all;">
                    {{ $tramite->url }}
                </a>
            @else
                <span style="font-size:12px; color:var(--muted);">Sin link configurado</span>
            @endif
        </div>
        <a href="{{ route('admin.recaudacion.tramite.edit') }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Editar
        </a>
    </div>
</section>

{{-- Documentos PDF --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
    <h3 style="margin:0; font-size:16px; font-weight:700;">
        Documentos PDF
        <span style="font-weight:400; color:var(--muted); font-size:13px;">
            ({{ $documentos->count() }} en total)
        </span>
    </h3>
</div>

<div class="admin-list">
    @forelse($documentos as $doc)
        <div class="admin-list-item">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <i class="fa-solid fa-file-pdf" style="color:var(--primary); font-size:18px; flex-shrink:0;"></i>
                <div>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <strong>{{ $doc->titulo }}</strong>
                        @if(! $doc->activo)
                            <span class="badge-estado badge-oculto">⚠ Desactivado</span>
                        @endif
                    </div>
                    @if($doc->url)
                        <a href="{{ $doc->url }}" target="_blank" rel="noopener"
                           style="font-size:12px; color:var(--primary); word-break:break-all;">
                            {{ $doc->url }}
                        </a>
                    @else
                        <span style="font-size:12px; color:var(--muted);">Sin link</span>
                    @endif
                </div>
            </div>

            <div class="admin-actions">
                {{-- Toggle activo --}}
                <form action="{{ route('admin.recaudacion.toggle', $doc) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secondary"
                            title="{{ $doc->activo ? 'Desactivar' : 'Activar' }}">
                        @if($doc->activo)
                            <i class="fa-solid fa-eye-slash"></i> Desactivar
                        @else
                            <i class="fa-solid fa-eye"></i> Activar
                        @endif
                    </button>
                </form>

                <a href="{{ route('admin.recaudacion.edit', $doc) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>

                <form action="{{ route('admin.recaudacion.destroy', $doc) }}"
                      method="POST"
                      data-confirm="¿Eliminar &quot;{{ $doc->titulo }}&quot;?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-secondary">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="admin-list-item">
            <p style="color:var(--muted);">No hay documentos cargados todavía.</p>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
