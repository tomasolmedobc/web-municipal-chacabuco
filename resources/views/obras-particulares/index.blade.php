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
        <p>Gestiones, consultas y requisitos para obras, balcones gastronómicos, mensura y libre deuda.</p>
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
            <p>Junto con las distintas direcciones que integran la secretaría se analizan los proyectos urbanísticos y nuevos desarrollos. Para estos trámites también se deberá cumplir con una serie de requisitos enumerados en la sección de Mensura y Subdivisión.</p>
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

    {{-- Navegación de secciones --}}
    <nav class="ops-seccion-nav" aria-label="Secciones de Obras Particulares">
        <a href="#obras" class="ops-seccion-nav__item">Obras</a>
        <a href="#balcones" class="ops-seccion-nav__item">Balcones Gastronómicos</a>
        <a href="#mensura" class="ops-seccion-nav__item">Mensura y Subdivisión</a>
        <a href="#libre-deuda" class="ops-seccion-nav__item">Libre Deuda</a>
    </nav>

    {{-- ===================== SECTION: OBRAS ===================== --}}
    <section id="obras" class="ops-seccion">
        <h2 class="ops-seccion__titulo"><i class="fa-solid fa-file-pdf"></i> Ordenanzas PDF</h2>

        @if($normativasObras->isNotEmpty())
            <div class="ops-normativas">
                <h3 class="ops-normativas__subtitulo">Normativa vigente</h3>
                <div class="ops-normativas__lista">
                    @foreach($normativasObras as $normativa)
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

        @if($procedimientosObras->isNotEmpty())
            <h3 class="ops-subseccion__titulo"><i class="fa-solid fa-building"></i> Obras</h3>
            <div class="ops-accordion">
                @foreach($procedimientosObras as $proc)
                    <details class="ops-accordion__item">
                        <summary class="ops-accordion__summary">
                            <span><strong>{{ $proc->codigo }}</strong> — {{ $proc->titulo }}</span>
                            <i class="fa-solid fa-chevron-down ops-accordion__icon"></i>
                        </summary>
                        <div class="ops-accordion__content">
                            {!! $proc->contenido !!}
                        </div>
                    </details>
                @endforeach
            </div>
        @endif

        @if($notasObras)
            <div class="ops-notas-box">
                <h4 class="ops-notas-box__titulo">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $notasObras->titulo }}
                </h4>
                {!! $notasObras->contenido !!}
            </div>
        @endif
    </section>

    {{-- ================ SECTION: BALCONES ==================== --}}
    <section id="balcones" class="ops-seccion">
        <h2 class="ops-seccion__titulo"><i class="fa-solid fa-store"></i> Balcones Gastronómicos</h2>

        @if($normativasBalcones->isNotEmpty())
            <div class="ops-normativas">
                <h3 class="ops-normativas__subtitulo">Normativa vigente</h3>
                <div class="ops-normativas__lista">
                    @foreach($normativasBalcones as $normativa)
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

        @if($procedimientosBalcones->isNotEmpty())
            <div class="ops-accordion">
                @foreach($procedimientosBalcones as $proc)
                    <details class="ops-accordion__item">
                        <summary class="ops-accordion__summary">
                            <span>{{ $proc->titulo }}</span>
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

    {{-- ================ SECTION: MENSURA ==================== --}}
    <section id="mensura" class="ops-seccion">
        <h2 class="ops-seccion__titulo"><i class="fa-solid fa-map"></i> Mensura y Subdivisión de Tierras</h2>

        @if($procedimientosMensura->isNotEmpty())
            <div class="ops-accordion">
                @foreach($procedimientosMensura as $proc)
                    <details class="ops-accordion__item">
                        <summary class="ops-accordion__summary">
                            <span><strong>{{ $proc->codigo }}</strong> — {{ $proc->titulo }}</span>
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

    {{-- ================ SECTION: LIBRE DEUDA ================ --}}
    <section id="libre-deuda" class="ops-seccion">
        <h2 class="ops-seccion__titulo"><i class="fa-solid fa-file-circle-check"></i> Libre Deuda</h2>

        @if($libreDeuda)
            <div class="ops-libre-deuda-box">
                {!! $libreDeuda->contenido !!}
            </div>
        @endif
    </section>

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
@endsection

@push('scripts')
<script>
(function () {
    const nav = document.querySelector('.ops-seccion-nav');
    if (!nav) return;

    const items = nav.querySelectorAll('.ops-seccion-nav__item');
    const secciones = Array.from(items).map(a => ({
        el: a,
        target: document.querySelector(a.getAttribute('href')),
    })).filter(x => x.target);

    function actualizarActivo() {
        const scroll = window.scrollY + 120;
        let activo = null;

        secciones.forEach(({ target }) => {
            if (target.offsetTop <= scroll) activo = target.id;
        });

        items.forEach(a => {
            const activa = a.getAttribute('href') === '#' + activo;
            a.classList.toggle('is-active', activa);
        });
    }

    window.addEventListener('scroll', actualizarActivo, { passive: true });
    actualizarActivo();
})();
</script>
@endpush
