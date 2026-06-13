@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="book-open" style="width:12px;height:12px"></i>
        Blog
    </div>
    <h1 class="sol-title">Digital Health Insights</h1>
    <p class="sol-sub">Perspectives on healthcare digitalization, EMR adoption, health policy, and technology for the Cameroon and CEMAC health sector.</p>
</div>

<div class="section">
    @if($posts->count())
    <div class="blog-grid">
        @foreach($posts as $post)
        <a href="{{ url($locale.'/blog/'.$post->slug) }}" class="blog-card">
            @if($post->cover_image)
            <div class="blog-card-img">
                <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->getLocalizedTitle($locale) }}">
            </div>
            @else
            <div class="blog-card-img blog-card-img-placeholder">
                <i data-lucide="file-text" style="width:32px;height:32px;color:#334155"></i>
            </div>
            @endif
            <div class="blog-card-body">
                <span class="blog-cat">{{ $post->category }}</span>
                <h3 class="blog-card-title">{{ $post->getLocalizedTitle($locale) }}</h3>
                <p class="blog-card-excerpt">{{ $post->getLocalizedExcerpt($locale) }}</p>
                <div class="blog-meta">
                    <span>{{ $post->author }}</span>
                    <span>·</span>
                    <span>{{ $post->published_at?->format('d M Y') }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div style="margin-top:40px">
        {{ $posts->links() }}
    </div>
    @else
    <div class="cs-wrap" style="padding:80px 48px">
        <div class="cs-icon">
            <i data-lucide="pencil" style="width:28px;height:28px;color:#00C896"></i>
        </div>
        <div class="section-label" style="justify-content:center;margin-bottom:16px">
            <i data-lucide="clock" style="width:11px;height:11px"></i>
            Coming Soon
        </div>
        <h2 class="cs-title">Blog launching soon</h2>
        <p class="cs-sub">We're preparing articles on healthcare digitalization, EMR adoption, and digital health policy in Cameroon and the CEMAC region. Check back soon.</p>
        <div class="cs-actions">
            <a href="{{ url($locale) }}" class="btn-secondary">
                <i data-lucide="arrow-left" style="width:14px;height:14px;color:#94a3b8"></i>
                Back to Home
            </a>
            <a href="{{ url($locale.'/contact') }}" class="btn-primary">
                Get notified <i data-lucide="bell" style="width:14px;height:14px"></i>
            </a>
        </div>
    </div>
    @endif
</div>

</x-layouts.app>
