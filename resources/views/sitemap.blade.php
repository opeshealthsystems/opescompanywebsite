<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- Static pages --}}
    @foreach($staticRoutes as $path)
    @foreach($locales as $locale)
    <url>
        <loc>{{ url($locale.$path) }}</loc>
        <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ url($locale.$path) }}"/>
        @foreach($locales as $alt)
        @if($alt !== $locale)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ url($alt.$path) }}"/>
        @endif
        @endforeach
        <changefreq>weekly</changefreq>
        <priority>{{ $path === '' ? '1.0' : '0.8' }}</priority>
    </url>
    @endforeach
    @endforeach

    {{-- Product detail pages --}}
    @foreach($products as $product)
    @foreach($locales as $locale)
    <url>
        <loc>{{ url($locale.'/products/'.$product['slug']) }}</loc>
        @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ url($alt.'/products/'.$product['slug']) }}"/>
        @endforeach
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
    @endforeach

    {{-- Blog posts --}}
    @foreach($posts as $post)
    @foreach($locales as $locale)
    <url>
        <loc>{{ url($locale.'/blog/'.$post->slug) }}</loc>
        @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ url($alt.'/blog/'.$post->slug) }}"/>
        @endforeach
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @endforeach

</urlset>
