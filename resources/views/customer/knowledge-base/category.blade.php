<x-layouts.customer title="{{ $category->name }} – Knowledge Base">
    {{-- Breadcrumb --}}
    <div style="margin-bottom:1rem;">
        <a href="{{ route('customer.knowledge-base.index', ['locale' => app()->getLocale()]) }}"
           style="color:#00C896;font-size:0.8125rem;text-decoration:none;"
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
            ← Knowledge Base
        </a>
    </div>

    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $category->name }}</h1>
            @if($category->description)
            <p class="cp-page-subtitle">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="cp-section-card" style="padding:0;">
        @forelse($articles as $article)
        <a href="{{ route('customer.knowledge-base.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}"
           style="display:block;padding:1rem;border-bottom:1px solid #1e293b;text-decoration:none;transition:background .15s;"
           onmouseover="this.style.background='rgba(0,200,150,0.05)'" onmouseout="this.style.background='transparent'">
            <p style="color:#e2e8f0;font-size:0.875rem;font-weight:500;margin:0 0 0.125rem;">{{ $article->title }}</p>
            <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $article->published_at?->format('d M Y') ?? 'Unpublished' }}</p>
        </a>
        @empty
        <div class="cp-empty-state" style="padding:3rem;text-align:center;">
            <i data-lucide="file-text" style="width:48px;height:48px;color:#334155;margin-bottom:0.75rem;"></i>
            <p style="color:#94a3b8;">No articles in this category yet.</p>
        </div>
        @endforelse
    </div>

    @if($articles->hasPages())
    <div style="margin-top:1rem;">
        {{ $articles->links() }}
    </div>
    @endif
</x-layouts.customer>
