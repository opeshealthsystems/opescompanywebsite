<x-layouts.customer title="{{ $article->title }}">
    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8125rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <a href="{{ route('customer.knowledge-base.index', ['locale' => app()->getLocale()]) }}"
           style="color:#00C896;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
            ← Knowledge Base
        </a>
        @if($article->category)
        <span style="color:#475569;">/</span>
        <a href="{{ route('customer.knowledge-base.category', ['locale' => app()->getLocale(), 'slug' => $article->category->slug]) }}"
           style="color:#00C896;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
            {{ $article->category->name }}
        </a>
        @endif
    </div>

    <div class="cp-section-card">
        <h1 style="color:#e2e8f0;font-size:1.5rem;font-weight:700;margin:0 0 0.5rem;">{{ $article->title }}</h1>
        <p style="color:#64748b;font-size:0.8125rem;margin:0 0 1.5rem;">
            {{ $article->published_at?->format('d M Y') ?? '' }}
            @if($article->published_at) &middot; @endif
            {{ number_format($article->views) }} view{{ $article->views !== 1 ? 's' : '' }}
        </p>

        <div style="color:#cbd5e1;font-size:0.9375rem;line-height:1.75;white-space:pre-wrap;">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>

    @if($related->isNotEmpty())
    <div class="cp-section-card" style="margin-top:1.5rem;">
        <h3 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">Related Articles</h3>
        <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:0.5rem;">
            @foreach($related as $r)
            <li>
                <a href="{{ route('customer.knowledge-base.show', ['locale' => app()->getLocale(), 'slug' => $r->slug]) }}"
                   style="color:#00C896;font-size:0.875rem;text-decoration:none;"
                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    {{ $r->title }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</x-layouts.customer>
