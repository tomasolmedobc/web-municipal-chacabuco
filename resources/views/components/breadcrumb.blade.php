@props(['items' => []])

@if(count($items) > 1)
<nav aria-label="Ruta de navegación" class="breadcrumb">
    <ol class="breadcrumb__list">
        @foreach($items as $item)
            @if(!$loop->last)
                <li class="breadcrumb__item">
                    <a href="{{ $item['url'] }}" class="breadcrumb__link">{{ $item['label'] }}</a>
                    <i class="fa-solid fa-chevron-right breadcrumb__sep" aria-hidden="true"></i>
                </li>
            @else
                <li class="breadcrumb__item breadcrumb__item--current">
                    <span aria-current="page">{{ $item['label'] }}</span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
