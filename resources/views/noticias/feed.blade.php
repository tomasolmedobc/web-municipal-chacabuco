<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>Noticias — Municipalidad de Chacabuco</title>
    <link>{{ route('noticias.index') }}</link>
    <description>Novedades, comunicados y actualidad de la Municipalidad de Chacabuco, Buenos Aires.</description>
    <language>es-ar</language>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
    <atom:link href="{{ route('rss') }}" rel="self" type="application/rss+xml"/>

    @foreach($noticias as $noticia)
    <item>
        <title><![CDATA[{{ $noticia->titulo }}]]></title>
        <link>{{ route('noticias.show', $noticia->slug) }}</link>
        <guid isPermaLink="true">{{ route('noticias.show', $noticia->slug) }}</guid>
        <pubDate>{{ \Carbon\Carbon::parse($noticia->fecha)->toRfc2822String() }}</pubDate>
        @if($noticia->imagen_destacada)
        <enclosure url="{{ url($noticia->imagen_destacada) }}" type="image/webp"/>
        @endif
        <description><![CDATA[{{ \Illuminate\Support\Str::of($noticia->contenido)->stripTags()->squish()->limit(300) }}]]></description>
    </item>
    @endforeach

</channel>
</rss>
