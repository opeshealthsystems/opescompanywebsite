@php
use Illuminate\Support\Str;
$locale  = app()->getLocale();
$title   = $post->getLocalizedTitle($locale);
$excerpt = $post->getLocalizedExcerpt($locale);
$body    = ($locale === 'fr' && $post->body_fr) ? $post->body_fr : $post->body;

// Auto-generate ToC from H2/H3 headings in body HTML
$toc = [];
preg_match_all('/<h([23])[^>]*>(.*?)<\/h\1>/i', $body, $headings, PREG_SET_ORDER);
foreach ($headings as $h) {
    $level = (int) $h[1];
    $text  = strip_tags($h[2]);
    $id    = 'section-' . Str::slug($text);
    $toc[] = compact('level', 'text', 'id');
    // Inject id attribute into heading tag in body
    $body = preg_replace(
        '/<h' . $level . '(' . preg_quote(ltrim(strstr($h[0], ' ', false) ?: '', ' '), '/') . ')?>' . preg_quote($h[2], '/') . '<\/h' . $level . '>/i',
        '<h' . $level . ' id="' . $id . '">' . $h[2] . '</h' . $level . '>',
        $body,
        1
    );
}

$shareUrl   = urlencode(request()->url());
$shareTitle = urlencode($title);
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

<div class="blog-show-layout">

    <article class="section blog-article">
        <div class="blog-art-header">
            <span class="blog-cat">{{ $post->category }}</span>
            <h1 class="blog-art-title">{{ $title }}</h1>
            <div class="blog-meta" style="justify-content:center;margin-top:12px">
                <span><i data-lucide="user" style="width:12px;height:12px"></i> {{ $post->author }}</span>
                <span>·</span>
                <span><i data-lucide="calendar" style="width:12px;height:12px"></i> {{ $post->published_at?->format('d M Y') }}</span>
                @if($post->reading_time)
                <span>·</span>
                <span><i data-lucide="clock" style="width:12px;height:12px"></i> {{ $post->reading_time }} min read</span>
                @endif
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

        {{-- ── Social Share ──────────────────────────────────── --}}
        <div class="blog-share">
            <span class="blog-share-label">Share this article</span>
            <div class="blog-share-links">
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-linkedin">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    LinkedIn
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-twitter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    X / Twitter
                </a>
                <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-whatsapp">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>
        </div>

        @if($related->count())
        <div class="divider" style="margin:48px 0"></div>
        <h2 class="section-title" style="margin-bottom:24px">Related articles</h2>
        <div class="blog-grid" style="grid-template-columns:repeat(3,1fr)">
            @foreach($related as $rp)
            @php
            $relColors = [
                'Digital Health in Cameroon' => '#00C896',
                'Healthcare Challenges'      => '#ef4444',
                'HMS Solutions'              => '#1A6FE8',
                "Buyer's Guide"              => '#8b5cf6',
                'AI & Technology'            => '#f59e0b',
                'Insights & Case Studies'    => '#94a3b8',
            ];
            $relColor = $relColors[$rp->category] ?? '#64748b';
            @endphp
            <a href="{{ url($locale.'/blog/'.$rp->slug) }}" class="blog-card">
                <div class="blog-card-img blog-card-img-gradient" style="background:linear-gradient(135deg,{{ $relColor }}22,{{ $relColor }}08)">
                    <i data-lucide="file-text" style="width:24px;height:24px;color:{{ $relColor }};opacity:0.5"></i>
                </div>
                <div class="blog-card-body">
                    <span class="blog-cat" style="color:{{ $relColor }}">{{ $rp->category }}</span>
                    <h3 class="blog-card-title">{{ $rp->getLocalizedTitle($locale) }}</h3>
                    <div class="blog-meta">
                        <span>{{ $rp->published_at?->format('d M Y') }}</span>
                        @if($rp->reading_time)
                        <span>·</span>
                        <span>{{ $rp->reading_time }} min</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </article>

    {{-- ── Table of Contents Sidebar ──────────────────────────── --}}
    @if(count($toc) > 3)
    <aside class="blog-toc">
        <div class="blog-toc-title">
            <i data-lucide="list" style="width:12px;height:12px"></i>
            In this article
        </div>
        <ul class="blog-toc-list">
            @foreach($toc as $item)
            <li class="blog-toc-item{{ $item['level'] === 3 ? ' blog-toc-h3' : '' }}">
                <a href="#{{ $item['id'] }}" class="blog-toc-link">{{ $item['text'] }}</a>
            </li>
            @endforeach
        </ul>
    </aside>
    @endif

</div>

</x-layouts.app>
