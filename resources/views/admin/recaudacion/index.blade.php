@extends('layouts.app')

@section('title', 'Recaudación — Admin')

@section('content')

<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Guía de Trámites de Recaudación</h2>
        <p class="admin-subtitle">Administrá los documentos PDF y el trámite online.</p>
    </div>
    <div class="admin-btn-bar">
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
    <script @nonce>
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('ok')), 'success');
        });
    </script>
@endif

{{-- Trámite Online --}}
<section class="admin-form-card mb-24">
    <div class="rc-admin-row">
        <div>
            <strong class="rc-admin-name">Trámite Online</strong>
            <p class="rc-admin-meta">{{ $tramite->titulo }}</p>
            @if($tramite->url)
                <a href="{{ $tramite->url }}" target="_blank" rel="noopener" class="rc-admin-link">
                    {{ $tramite->url }}
                </a>
            @else
                <span class="rc-admin-no-link">Sin link configurado</span>
            @endif
        </div>
        <a href="{{ route('admin.recaudacion.tramite.edit') }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Editar
        </a>
    </div>
</section>

{{-- Documentos PDF --}}
<div class="rc-admin-section-hd">
    <h3 class="rc-admin-section-h3">
        Documentos PDF
        <span class="rc-admin-count">({{ $documentos->count() }} en total)</span>
    </h3>
</div>

<div class="admin-list">
    @forelse($documentos as $doc)
        <div class="admin-list-item">
            <div class="rc-admin-doc-row">
                <i class="fa-solid fa-file-pdf rc-admin-icon"></i>
                <div>
                    <div class="rc-admin-doc-info">
                        <strong>{{ $doc->titulo }}</strong>
                        @if(! $doc->activo)
                            <span class="badge-estado badge-oculto">⚠ Desactivado</span>
                        @endif
                    </div>
                    @if($doc->url)
                        <a href="{{ $doc->url }}" target="_blank" rel="noopener" class="rc-admin-link">
                            {{ $doc->url }}
                        </a>
                    @else
                        <span class="rc-admin-no-link">Sin link</span>
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
            <p class="text-muted">No hay documentos cargados todavía.</p>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
