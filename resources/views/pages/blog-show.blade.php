@php
$locale  = app()->getLocale();
$title   = $post->getLocalizedTitle($locale);
$excerpt = $post->getLocalizedExcerpt($locale);
$body    = ($locale === 'fr' && $post->body_fr) ? $post->body_fr : $post->body;
@endphp

<x-layouts.app :title="$title" :description="$excerpt" ogType="article">

@push('schema')
<script type="application/ld+json"><?php echo json_encode([
    '@context'         => 'https://schema.org',
    '@type'            => 'Article',
    'headline'         => $title,
    'description'      => $excerpt,
    'author'           => ['@type' => 'Organization', 'name' => $post->author],
    'publisher'        => ['@type' => 'Organization', 'name' => 'OPES Health Systems', 'url' => config('app.url')],
    'datePublished'    => $post->published_at?->toIso8601String(),
    'dateModified'     => ($post->updated_at ?? $post->published_at)?->toIso8601String(),
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => request()->url()],
    'articleSection'   => $post->category,
    'inLanguage'       => app()->getLocale() === 'fr' ? 'fr-CM' : 'en-CM',
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
@endpush

<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">Home</a>
    <span>›</span>
    <a href="{{ url($locale.'/blog') }}">Blog</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">{{ $title }}</span>
</div>

<article class="section blog-article">
    <div class="blog-art-header">
        <span class="blog-cat">{{ $post->category }}</span>
        <h1 class="blog-art-title">{{ $title }}</h1>
        <div class="blog-meta" style="justify-content:center;margin-top:12px">
            <span><i data-lucide="user" style="width:12px;height:12px"></i> {{ $post->author }}</span>
            <span>·</span>
            <span><i data-lucide="calendar" style="width:12px;height:12px"></i> {{ $post->published_at?->format('d M Y') }}</span>
        </div>
    </div>

    @if($post->cover_image)
    <div class="blog-art-cover">
        <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $title }}">
    </div>
    @endif

    <div class="blog-art-body prose">
        {!! $body !!}
    </div>

    @if($related->count())
    <div class="divider" style="margin:48px 0"></div>
    <h2 class="section-title" style="margin-bottom:24px">Related articles</h2>
    <div class="blog-grid" style="grid-template-columns:repeat(3,1fr)">
        @foreach($related as $rp)
        <a href="{{ url($locale.'/blog/'.$rp->slug) }}" class="blog-card">
            <div class="blog-card-img blog-card-img-placeholder">
                <i data-lucide="file-text" style="width:24px;height:24px;color:#334155"></i>
            </div>
            <div class="blog-card-body">
                <span class="blog-cat">{{ $rp->category }}</span>
                <h3 class="blog-card-title">{{ $rp->getLocalizedTitle($locale) }}</h3>
                <div class="blog-meta"><span>{{ $rp->published_at?->format('d M Y') }}</span></div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</article>

</x-layouts.app>
