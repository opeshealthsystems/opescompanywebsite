@php
$locale = app()->getLocale();
$catColors = [
    'Digital Health in Cameroon' => '#00C896',
    'Healthcare Challenges'      => '#ef4444',
    'HMS Solutions'              => '#1A6FE8',
    "Buyer's Guide"              => '#8b5cf6',
    'AI & Technology'            => '#f59e0b',
    'Insights & Case Studies'    => 'var(--text-muted)',
];
$heroFeatured  = $featured->first();
$heroHeadlines = $featured->skip(1)->values();
@endphp

<x-layouts.app>

{{-- ═══════════════════════════════════════════════════════════════
     BLOG HERO — Health Technology Headlines
     ═══════════════════════════════════════════════════════════════ --}}
<section class="blog-hero">
    <div class="blog-hero-inner-wrap">

        {{-- Eyebrow bar --}}
        <div class="blog-hero-label">
            <div class="blog-hero-eyebrow">
                <i data-lucide="trending-up" style="width:12px;height:12px"></i>
                {{ __('blog.eyebrow') }}
                <span class="blog-hero-eyebrow-sep">·</span>
                <span class="blog-hero-date">{{ __('blog.eyebrow_region') }}</span>
            </div>
            <a href="{{ url($locale.'/blog') }}" style="display:inline-flex;align-items:center;gap:5px;font-size:var(--fs-xs);font-weight:600;color:var(--text-muted);text-decoration:none;transition:color 0.15s"
               onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-muted)'">
                {{ __('blog.all_articles_count', ['count' => $posts->total()]) }}
                <i data-lucide="arrow-right" style="width:12px;height:12px"></i>
            </a>
        </div>

        @if($heroFeatured)
        <div class="blog-hero-grid">

            {{-- Featured article --}}
            <a href="{{ url($locale.'/blog/'.$heroFeatured->slug) }}" class="blog-hero-featured">
                <div class="blog-hero-img">
                    @if($heroFeatured->cover_image)
                        <img src="{{ Storage::url($heroFeatured->cover_image) }}"
                             alt="{{ $heroFeatured->getLocalizedTitle($locale) }}">
                    @else
                        @php $heroColor = $catColors[$heroFeatured->category] ?? '#00C896'; @endphp
                        <div class="blog-hero-img-icon" style="background:linear-gradient(135deg,{{ $heroColor }}18,{{ $heroColor }}06)">
                            <i data-lucide="file-text" style="width:26px;height:26px;color:{{ $heroColor }};opacity:0.6"></i>
                        </div>
                    @endif
                    <span class="blog-hero-badge">{{ __('blog.badge_featured') }}</span>
                </div>
                <div class="blog-hero-content">
                    @php $heroColor = $catColors[$heroFeatured->category] ?? '#00C896'; @endphp
                    <span class="blog-cat" style="color:{{ $heroColor }}">
                        {{ $heroFeatured->category }}
                    </span>
                    <h2 class="blog-hero-title">
                        {{ $heroFeatured->getLocalizedTitle($locale) }}
                    </h2>
                    <p class="blog-hero-excerpt">
                        {{ $heroFeatured->getLocalizedExcerpt($locale) }}
                    </p>
                    <div class="blog-meta" style="margin-top:2px">
                        <span>{{ $heroFeatured->author }}</span>
                        <span>·</span>
                        <span>{{ $heroFeatured->published_at?->format('d M Y') }}</span>
                    </div>
                    <span class="blog-hero-cta">
                        {{ __('blog.read_article') }}
                        <i data-lucide="arrow-right" style="width:12px;height:12px"></i>
                    </span>
                </div>
            </a>

            {{-- Headlines panel --}}
            <div class="blog-hero-headlines">
                <div class="blog-headlines-header">
                    <span class="blog-headlines-live-dot"></span>
                    {{ __('blog.latest_heading') }}
                </div>
                @foreach($heroHeadlines as $i => $headline)
                <a href="{{ url($locale.'/blog/'.$headline->slug) }}" class="blog-headline-item">
                    <span class="blog-headline-num">0{{ $i + 2 }}</span>
                    @php $hlColor = $catColors[$headline->category] ?? 'var(--text-muted)'; @endphp
                    <span class="blog-cat" style="color:{{ $hlColor }};font-size:var(--fs-2xs)">
                        {{ $headline->category }}
                    </span>
                    <span class="blog-headline-title">
                        {{ $headline->getLocalizedTitle($locale) }}
                    </span>
                    <div class="blog-headline-meta">
                        <i data-lucide="calendar" style="width:10px;height:10px"></i>
                        {{ $headline->published_at?->format('d M Y') }}
                    </div>
                </a>
                @endforeach
            </div>

        </div>
        @endif

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     MAIN: SIDEBAR + BLOG GRID
     ═══════════════════════════════════════════════════════════════ --}}
<div class="blog-layout">

    {{-- ── Left Sidebar ──────────────────────────────────── --}}
    <aside class="blog-sidebar">

        {{-- Search --}}
        <form action="{{ url($locale.'/blog') }}" method="GET" class="blog-sb-search">
            <div class="blog-sb-search-wrap">
                <i data-lucide="search" style="width:13px;height:13px;color:var(--text-faint);flex-shrink:0"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="{{ __('blog.search_placeholder') }}"
                       class="blog-sb-search-input">
                @if(!empty($search))
                <a href="{{ url($locale.'/blog') }}" class="blog-sb-search-clear" title="Clear search">
                    <i data-lucide="x" style="width:11px;height:11px"></i>
                </a>
                @endif
            </div>
        </form>

        {{-- Browse by Topic --}}
        <div class="blog-sb-widget">
            <div class="blog-sb-widget-title">
                <i data-lucide="layout-grid" style="width:11px;height:11px"></i>
                {{ __('blog.browse_by_topic') }}
            </div>
            {{-- All articles link --}}
            <a href="{{ url($locale.'/blog') }}"
               class="blog-sb-cat {{ empty($activeCategory) ? 'sb-active' : '' }}">
                <span class="blog-sb-cat-dot" style="background:#00C896"></span>
                {{ __('blog.all_articles') }}
                <span class="blog-sb-cat-count">{{ $categories->sum('count') }}</span>
            </a>
            @foreach($categories as $cat)
            @php $dotColor = $catColors[$cat->category] ?? 'var(--text-muted)'; @endphp
            <a href="{{ url($locale.'/blog') }}?category={{ urlencode($cat->category) }}"
               class="blog-sb-cat {{ ($activeCategory ?? '') === $cat->category ? 'sb-active' : '' }}">
                <span class="blog-sb-cat-dot" style="background:{{ $dotColor }}"></span>
                {{ $cat->category }}
                <span class="blog-sb-cat-count">{{ $cat->count }}</span>
            </a>
            @endforeach
            @if($activeCategory)
            <a href="{{ url($locale.'/blog') }}" class="blog-sb-clear">
                <i data-lucide="x" style="width:11px;height:11px"></i>
                {{ __('blog.clear_filter') }}
            </a>
            @endif
        </div>

        {{-- CTA Widget --}}
        <div class="blog-sb-cta">
            <div class="blog-sb-cta-icon">
                <i data-lucide="building-2" style="width:22px;height:22px;color:#00C896"></i>
            </div>
            <h4>{{ __('blog.cta_heading') }}</h4>
            <p>{{ __('blog.cta_body') }}</p>
            <a href="{{ url($locale.'/contact') }}" class="blog-sb-cta-btn">
                {{ __('blog.cta_btn') }}
                <i data-lucide="arrow-right" style="width:12px;height:12px"></i>
            </a>
        </div>

        {{-- Quick Links --}}
        <div class="blog-sb-widget">
            <div class="blog-sb-widget-title">
                <i data-lucide="link-2" style="width:11px;height:11px"></i>
                {{ __('blog.explore_opes') }}
            </div>
            <a href="{{ url($locale.'/products') }}" class="blog-sb-link">
                <i data-lucide="package" style="width:12px;height:12px;flex-shrink:0"></i>
                {{ __('blog.link_products') }}
            </a>
            <a href="{{ url($locale.'/solutions') }}" class="blog-sb-link">
                <i data-lucide="lightbulb" style="width:12px;height:12px;flex-shrink:0"></i>
                {{ __('blog.link_solutions') }}
            </a>
            <a href="{{ url($locale.'/about') }}" class="blog-sb-link">
                <i data-lucide="users" style="width:12px;height:12px;flex-shrink:0"></i>
                {{ __('blog.link_about') }}
            </a>
            <a href="{{ url($locale.'/contact') }}" class="blog-sb-link">
                <i data-lucide="mail" style="width:12px;height:12px;flex-shrink:0"></i>
                {{ __('blog.link_contact') }}
            </a>
        </div>

    </aside>

    {{-- ── Blog Main ──────────────────────────────────────── --}}
    <div class="blog-main">

        {{-- Section header --}}
        <div class="blog-main-header">
            <div>
                <h2 class="blog-main-title">
                    @if(!empty($search))
                        <span style="display:inline-flex;align-items:center;gap:8px">
                            <i data-lucide="search" style="width:14px;height:14px;color:#00C896"></i>
                            "{{ $search }}"
                        </span>
                    @elseif($activeCategory)
                        <span style="display:inline-flex;align-items:center;gap:8px">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $catColors[$activeCategory] ?? '#00C896' }};display:inline-block;flex-shrink:0"></span>
                            {{ $activeCategory }}
                        </span>
                    @else
                        {{ __('blog.section_all_articles') }}
                    @endif
                </h2>
                <p class="blog-main-count">{{ trans_choice('blog.article_count', $posts->total(), ['count' => $posts->total()]) }}</p>
            </div>
            @if(!empty($search))
            <a href="{{ url($locale.'/blog') }}" style="font-size:var(--fs-xs);color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                <i data-lucide="x" style="width:11px;height:11px"></i>
                {{ $locale === 'fr' ? 'Effacer la recherche' : 'Clear search' }}
            </a>
            @endif
        </div>

        {{-- Grid --}}
        @if($posts->count())
        <div class="blog-grid">
            @foreach($posts as $post)
            @php $postColor = $catColors[$post->category] ?? 'var(--text-muted)'; @endphp
            <a href="{{ url($locale.'/blog/'.$post->slug) }}" class="blog-card">
                @if($post->cover_image)
                <div class="blog-card-img">
                    <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->getLocalizedTitle($locale) }}" style="width:100%;height:100%;object-fit:cover">
                </div>
                @else
                <div class="blog-card-img blog-card-img-gradient" style="background:linear-gradient(135deg,{{ $postColor }}22,{{ $postColor }}08)">
                    <i data-lucide="file-text" style="width:28px;height:28px;color:{{ $postColor }};opacity:0.5"></i>
                </div>
                @endif
                <div class="blog-card-body">
                    <span class="blog-cat" style="color:{{ $postColor }}">{{ $post->category }}</span>
                    <h3 class="blog-card-title">{{ $post->getLocalizedTitle($locale) }}</h3>
                    <p class="blog-card-excerpt">{{ $post->getLocalizedExcerpt($locale) }}</p>
                    <div class="blog-meta">
                        <span>{{ $post->author }}</span>
                        <span>·</span>
                        <span>{{ $post->published_at?->format('d M Y') }}</span>
                        @if($post->reading_time)
                        <span>·</span>
                        <span><i data-lucide="clock" style="width:10px;height:10px"></i> {{ $post->reading_time }} {{ $locale === 'fr' ? 'min de lecture' : 'min read' }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div style="margin-top:36px">
            {{ $posts->links() }}
        </div>
        @else
        <div style="text-align:center;padding:80px 24px">
            <div class="cs-icon" style="margin:0 auto 24px">
                <i data-lucide="search" style="width:28px;height:28px;color:#00C896"></i>
            </div>
            <h2 style="font-size:22px;font-weight:700;color:#e2e8f0;margin-bottom:10px">{{ __('blog.empty_heading') }}</h2>
            <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px">{{ __('blog.empty_body') }}</p>
            <a href="{{ url($locale.'/blog') }}" class="btn-secondary" style="display:inline-flex;gap:6px;align-items:center">
                <i data-lucide="arrow-left" style="width:14px;height:14px"></i>
                {{ __('blog.view_all') }}
            </a>
        </div>
        @endif

    </div>
</div>

</x-layouts.app>
