@php
    $url = $item['url'] ?? '#';
    $hasUrl = $url !== '#';
    $isExternal = $hasUrl && \Illuminate\Support\Str::startsWith($url, ['http://', 'https://']);
@endphp

<a href="{{ $url }}"
   class="ts-card"
   data-ts-card
   data-ts-type="{{ $tipo }}"
   data-ts-search-text="{{ \Illuminate\Support\Str::lower(($item['titulo'] ?? '') . ' ' . ($item['descripcion'] ?? '')) }}"
   target="{{ $isExternal ? '_blank' : '_self' }}"
   rel="{{ $isExternal ? 'noopener' : '' }}">
    <span class="ts-card__icon">
        <i class="fa-solid {{ $item['icono'] ?? 'fa-circle-info' }}"></i>
    </span>

    <span class="ts-card__body">
        <span class="ts-card__tag">{{ $tipo === 'tramite' ? 'Tramite' : 'Servicio' }}</span>
        <strong>{{ $item['titulo'] }}</strong>
        <span>{{ $item['descripcion'] }}</span>
    </span>

    <span class="ts-card__arrow" aria-hidden="true">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
    </span>
</a>
