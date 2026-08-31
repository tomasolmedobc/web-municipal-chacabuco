@extends('layouts.app')

@section('title', 'Dirección de Obras Particulares — Municipalidad de Chacabuco')
@section('meta_description', 'Información sobre obras particulares, balcones gastronómicos, mensura y subdivisión de tierras. Municipalidad de Chacabuco.')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Obras Particulares'],
]" />

{{-- Hero --}}
<section class="ops-hero">
    <div>
        <span class="section-badge">Trámites</span>
        <h1>Dirección de Obras Particulares</h1>
        <p>Gestiones, consultas y requisitos para obras particulares en el partido de Chacabuco.</p>
    </div>
</section>

<div class="contenedor-interno">

    {{-- Intro + sidebar --}}
    <div class="ops-intro">
        <div class="ops-intro__main">
            <h2 class="ops-intro__titulo">¿Qué hace la dirección?</h2>
            <ul class="ops-intro__lista">
                <li>Analizar la documentación de las obras particulares: planos de proyectos a construir, refaccionar, ampliar, demoler y construidas.</li>
                <li>Los casos particulares serán atendidos y analizados para dar respuestas concretas y ajustadas a las normativas vigentes.</li>
                <li>Detección de obras clandestinas.</li>
                <li>Inspección de obras.</li>
                <li>Visado y aprobación de los proyectos.</li>
            </ul>
            <p>Junto con las distintas direcciones que integran la secretaría se analizan los proyectos urbanísticos y nuevos desarrollos.</p>
        </div>

        <div class="ops-intro__sidebar">
            <div class="ops-registro-card">
                <div class="ops-registro-card__header">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <h3>Registro de Profesionales</h3>
                </div>
                <p>Inscribite en el Registro de Profesionales de la Dirección de Obras Particulares y completá los datos solicitados.</p>
                @if($config->registro_href)
                    <a href="{{ $config->registro_href }}" class="btn btn-primary" target="_blank" rel="noopener">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Formulario de inscripción</span>
                    </a>
                @else
                    <span class="btn btn-primary" style="opacity:.5; cursor:default;">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Formulario de inscripción</span>
                    </span>
                @endif
            </div>

            {{-- Formularios (Anexos) --}}
            @if($anexos->isNotEmpty())
            <div class="ops-registro-card" style="margin-top:16px;">
                <div class="ops-registro-card__header">
                    <i class="fa-solid fa-file-contract"></i>
                    <h3>Formularios (Anexos)</h3>
                </div>
                <div class="ops-anexos-lista">
                    @foreach($anexos as $anexo)
                        @if($anexo->archivo_ruta)
                            <a href="{{ $anexo->archivo_ruta }}" target="_blank" rel="noopener" class="ops-anexo-item">
                                <i class="fa-regular fa-file-pdf"></i>
                                <span>{{ $anexo->nombre }}</span>
                                <i class="fa-solid fa-download fa-xs"></i>
                            </a>
                        @else
                            <div class="ops-anexo-item ops-anexo-item--pending">
                                <i class="fa-regular fa-file-pdf"></i>
                                <span>{{ $anexo->nombre }}</span>
                                <span class="ops-pronto">Próximamente</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Navegación de categorías (dinámica) --}}
    @if($categorias->isNotEmpty())
    <nav class="ops-seccion-nav" aria-label="Secciones de Obras Particulares">
        @foreach($categorias as $categoria)
            <a href="#cat-{{ $categoria->id }}" class="ops-seccion-nav__item">
                {{ $categoria->nombre }}
            </a>
        @endforeach
    </nav>
    @endif

    {{-- Secciones dinámicas por categoría --}}
    @foreach($categorias as $categoria)
    <section id="cat-{{ $categoria->id }}" class="ops-seccion">
        <h2 class="ops-seccion__titulo">{{ $categoria->nombre }}</h2>

        @if($categoria->descripcion)
            <p style="color:var(--text-muted);margin-bottom:16px;">{{ $categoria->descripcion }}</p>
        @endif

        {{-- Normativas de esta categoría --}}
        @if($categoria->normativas->isNotEmpty())
            <div class="ops-normativas">
                <h3 class="ops-normativas__subtitulo">Normativa vigente</h3>
                <div class="ops-normativas__lista">
                    @foreach($categoria->normativas as $normativa)
                        @if($normativa->archivo_ruta)
                            <a href="{{ $normativa->archivo_ruta }}" target="_blank" rel="noopener" class="ops-normativa-item">
                                <i class="fa-regular fa-file-pdf"></i>
                                <span>{{ $normativa->nombre }}</span>
                                <i class="fa-solid fa-download fa-xs"></i>
                            </a>
                        @else
                            <div class="ops-normativa-item ops-normativa-item--pending">
                                <i class="fa-regular fa-file-pdf"></i>
                                <span>{{ $normativa->nombre }}</span>
                                <span class="ops-pronto">Próximamente</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Procedimientos (acordeones) de esta categoría --}}
        @if($categoria->procedimientos->isNotEmpty())
            <div class="ops-accordion">
                @foreach($categoria->procedimientos as $proc)
                    <details class="ops-accordion__item">
                        <summary class="ops-accordion__summary">
                            <span>
                                @if($proc->codigo)
                                    <strong>{{ $proc->codigo }}</strong> —
                                @endif
                                {{ $proc->titulo }}
                            </span>
                            <i class="fa-solid fa-chevron-down ops-accordion__icon"></i>
                        </summary>
                        <div class="ops-accordion__content">
                            {!! $proc->contenido !!}
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </section>
    @endforeach

    {{-- Contacto --}}
    <div class="ops-contacto">
        <h3 class="ops-contacto__titulo">Datos de contacto</h3>
        <div class="ops-contacto__grid">
            <div class="ops-contacto__item">
                <i class="fa-solid fa-clock"></i>
                <div>
                    <strong>Horarios</strong>
                    <span>Lunes a viernes de 7:00 a 13:00</span>
                </div>
            </div>
            <div class="ops-contacto__item">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <strong>Teléfono</strong>
                    <span>430473 / 430493</span>
                </div>
            </div>
            <div class="ops-contacto__item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <strong>Mail</strong>
                    <a href="mailto:obrasparticulares.direccion@gmail.com">obrasparticulares.direccion@gmail.com</a>
                </div>
            </div>
        </div>
    </div>

</div>

<section class="volver-ts">
    <a href="{{ route('tramites-servicios.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a Trámites y Servicios
    </a>
</section>
@endsection

@push('scripts')
<script @nonce>
(function () {
    const nav = document.querySelector('.ops-seccion-nav');
    if (!nav) return;

    const items = nav.querySelectorAll('.ops-seccion-nav__item');
    const secciones = Array.from(items).map(function (a) {
        return { el: a, target: document.querySelector(a.getAttribute('href')) };
    }).filter(function (x) { return x.target; });

    function actualizarActivo() {
        const scroll = window.scrollY + 120;
        let activo = null;
        secciones.forEach(function (s) {
            if (s.target.offsetTop <= scroll) activo = s.target.id;
        });
        items.forEach(function (a) {
            a.classList.toggle('is-active', a.getAttribute('href') === '#' + activo);
        });
    }

    window.addEventListener('scroll', actualizarActivo, { passive: true });
    actualizarActivo();
})();
</script>
@endpush
