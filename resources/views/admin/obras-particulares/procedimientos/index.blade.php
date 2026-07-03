@extends('layouts.app')

@section('title', 'Procedimientos — Obras Particulares')

@section('content')
<section class="admin-header">
    <div>
        <h2 class="seccion-titulo">Procedimientos</h2>
        <p class="admin-subtitle">Contenido de cada accordion de la página pública. Editá el texto y los requisitos de cada sección.</p>
    </div>
    <a href="{{ route('admin.obras.index') }}" class="btn btn-secondary">Volver</a>
</section>

@if(session('ok'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('ok')), 'success'));</script>
@endif

@foreach($secciones as $seccion => $config)
    @php $items = $procedimientos->get($seccion, collect()); @endphp
    @if($items->isNotEmpty())
        <div class="ops-proc-grupo">
            <h3 class="ops-proc-grupo__titulo">{{ $config['titulo'] }}</h3>
            <div class="admin-list">
                @foreach($items as $proc)
                    <article class="admin-list-item">
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span class="badge-categoria">{{ strtoupper($proc->codigo) }}</span>
                                <h3 style="margin:0;">{{ $proc->titulo }}</h3>
                                @if(! $proc->visible)
                                    <span class="badge-estado badge-oculto">Oculto</span>
                                @endif
                            </div>
                            <div class="meta-noticia">
                                <span>Orden: {{ $proc->orden }}</span>
                            </div>
                        </div>
                        <div class="admin-actions">
                            <a href="{{ route('admin.obras.procedimientos.edit', $proc) }}" class="btn btn-secondary">Editar</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
@endsection
