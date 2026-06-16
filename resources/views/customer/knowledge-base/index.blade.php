<x-layouts.customer title="Knowledge Base">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Knowledge Base</h1>
            <p class="cp-page-subtitle">Browse articles, guides, and documentation</p>
        </div>
    </div>

    {{-- Recent articles --}}
    @if($recent->isNotEmpty())
    <div class="cp-section-card" style="margin-bottom:1.5rem;">
        <h2 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">Recent Articles</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:0.75rem;">
            @foreach($recent as $article)
            <a href="{{ route('customer.knowledge-base.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}"
               style="display:block;background:#1e293b;border:1px solid #334155;border-radius:8px;padding:1rem;text-decoration:none;transition:border-color .2s;"
               onmouseover="this.style.borderColor='#00C896'" onmouseout="this.style.borderColor='#334155'">
                <p style="color:#e2e8f0;font-size:0.875rem;font-weight:500;margin:0 0 0.25rem;">{{ $article->title }}</p>
                <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $article->category?->name ?? 'General' }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Categories --}}
    <div class="cp-section-card">
        <h2 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">Browse by Category</h2>
        @forelse($categories as $category)
        <a href="{{ route('customer.knowledge-base.category', ['locale' => app()->getLocale(), 'slug' => $category->slug]) }}"
           style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem;border-bottom:1px solid #1e293b;text-decoration:none;"
           onmouseover="this.style.background='rgba(0,200,150,0.05)'" onmouseout="this.style.background='transparent'">
            <div>
                <p style="color:#e2e8f0;font-size:0.875rem;font-weight:500;margin:0 0 0.125rem;">{{ $category->name }}</p>
                <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $category->articles_count }} article{{ $category->articles_count !== 1 ? 's' : '' }}</p>
            </div>
            <i data-lucide="chevron-right" style="width:16px;height:16px;color:#475569;flex-shrink:0;"></i>
        </a>
        @empty
        <div class="cp-empty-state" style="padding:3rem;text-align:center;">
            <i data-lucide="book-open" style="width:48px;height:48px;color:#334155;margin-bottom:0.75rem;"></i>
            <p style="color:#94a3b8;">No categories yet.</p>
        </div>
        @endforelse
    </div>
</x-layouts.customer>
