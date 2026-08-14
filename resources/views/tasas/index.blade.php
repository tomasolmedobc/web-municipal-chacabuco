@extends('layouts.app')

@section('title', 'Tasas Municipales — Municipalidad de Chacabuco')
@section('meta_description', 'Información sobre tasas municipales, fechas de vencimiento, formas de pago y ordenanzas impositivas. Municipalidad de Chacabuco.')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',            'url' => route('home')],
    ['label' => 'Tasas Municipales'],
]" />

{{-- Hero --}}
<div class="tasas-hero">
    <h1>Tasas Municipales</h1>
</div>

<div class="tasas-container">

    {{-- Banner promocional --}}
    @if($config->banner_imagen_url || $config->banner_url)
        @if($config->banner_imagen_url)
            <a href="{{ $config->banner_url ?? '#' }}" target="_blank" rel="noopener" class="tasas-banner">
                <img src="{{ $config->banner_imagen_url }}" alt="Información de pago">
            </a>
        @endif
    @endif

    {{-- Botones de acción --}}
    <div class="tasas-acciones">
        @if($config->btn_consulta_href)
            <a href="{{ $config->btn_consulta_href }}" class="btn btn-outline" target="_blank" rel="noopener">
                <i class="fa-solid fa-magnifying-glass-dollar"></i>
                <span>Consulta y formas de pago de tasas</span>
            </a>
        @else
            <span class="btn btn-outline" style="opacity:.5; cursor:default;">
                <i class="fa-solid fa-magnifying-glass-dollar"></i>
                <span>Consulta y formas de pago de tasas</span>
            </span>
        @endif

        @if($config->btn_ordenanza_href)
            <a href="{{ $config->btn_ordenanza_href }}" class="btn btn-outline" target="_blank" rel="noopener">
                <i class="fa-solid fa-book-open"></i>
                <span>Ordenanza Impositiva Vigente</span>
            </a>
        @else
            <span class="btn btn-outline" style="opacity:.5; cursor:default;">
                <i class="fa-solid fa-book-open"></i>
                <span>Ordenanza Impositiva Vigente</span>
            </span>
        @endif
    </div>

    {{-- Pago Anual --}}
    @if($config->texto_pago_anual)
    <div class="tasas-accordion">
        <details class="tasas-accordion__item">
            <summary class="tasas-accordion__summary">
                <span>Pago Anual</span>
                <i class="fa-solid fa-chevron-down tasas-accordion__icon"></i>
            </summary>
            <div class="tasas-accordion__content">
                {!! $config->texto_pago_anual !!}
            </div>
        </details>
    </div>
    @endif

    {{-- Plan de Facilidades --}}
    @if($config->texto_plan_facilidades)
    <div class="tasas-accordion">
        <details class="tasas-accordion__item">
            <summary class="tasas-accordion__summary">
                <span>Plan de Facilidades</span>
                <i class="fa-solid fa-chevron-down tasas-accordion__icon"></i>
            </summary>
            <div class="tasas-accordion__content">
                {!! $config->texto_plan_facilidades !!}
            </div>
        </details>
    </div>
    @endif

    {{-- Formas de pago --}}
    <h2 class="tasas-formas-titulo">Formas de pago</h2>
    <div class="tasas-formas-grid">
        <div class="tasas-forma-item">
            <i class="fa-solid fa-building-columns"></i>
            <span>Cámara Argentina de Comercio</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-brands fa-cc-visa"></i>
            <span>Visa</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-brands fa-cc-mastercard"></i>
            <span>MasterCard</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-bolt"></i>
            <span>RapiPago</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-link"></i>
            <span>LINK</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-credit-card"></i>
            <span>BANELCO</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-money-bill-wave"></i>
            <span>Cobro Express</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-clock"></i>
            <span>Pronto Pago</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-landmark"></i>
            <span>Provincia NET</span>
        </div>
        <div class="tasas-forma-item">
            <i class="fa-solid fa-building"></i>
            <span>Transferencia o Depósito Bancario</span>
        </div>
    </div>

    {{-- Info bancaria --}}
    @if($config->texto_info_bancaria)
    <div class="tasas-accordion" style="margin-bottom:40px;">
        <details class="tasas-accordion__item">
            <summary class="tasas-accordion__summary">
                <span>Información para pagos por transferencia bancaria o depósitos</span>
                <i class="fa-solid fa-chevron-down tasas-accordion__icon"></i>
            </summary>
            <div class="tasas-accordion__content">
                {!! $config->texto_info_bancaria !!}
            </div>
        </details>
    </div>
    @endif

    {{-- Fechas de vencimiento --}}
    @if($grupos->isNotEmpty())
    <h2 class="tasas-vencimientos-titulo">Fechas de vencimiento</h2>

    @foreach($grupos as $grupo)
        @if($grupo->cuotasVisibles->isNotEmpty())
        <details class="tasas-grupo-item" {{ $loop->first ? 'open' : '' }}>
            <summary class="tasas-grupo-summary">
                <span>{{ $grupo->nombre }}</span>
                <i class="fa-solid fa-chevron-down tasas-grupo-summary__icon"></i>
            </summary>
            <table class="tasas-cuotas-table">
                <thead>
                    <tr>
                        <th>Cuota</th>
                        <th>Fecha de Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupo->cuotasVisibles as $cuota)
                    <tr>
                        <td>{{ $cuota->cuota_label }}</td>
                        <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </details>
        @endif
    @endforeach
    @endif

</div>

@endsection
