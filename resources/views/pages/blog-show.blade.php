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

// AEO/GEO: extract FAQ pairs (question H3 + answer) under an FAQ H2, for FAQPage schema
$faqs = [];
if (preg_match('/<h2[^>]*>[^<]*(?:Frequently Asked Questions|FAQ|Questions?\s+Fr[ée]quentes|Foire\s+aux\s+Questions)[^<]*<\/h2>(.*)$/is', $body, $faqBlock)) {
    $faqHtml = $faqBlock[1];
    if (preg_match('/^(.*?)<h2[^>]*>/is', $faqHtml, $cut)) {
        $faqHtml = $cut[1]; // stop at the next H2
    }
    preg_match_all('/<h3[^>]*>(.*?)<\/h3>(.*?)(?=<h3[^>]*>|$)/is', $faqHtml, $pairs, PREG_SET_ORDER);
    foreach ($pairs as $p) {
        $q = trim(html_entity_decode(strip_tags($p[1]), ENT_QUOTES));
        $a = trim(html_entity_decode(strip_tags($p[2]), ENT_QUOTES));
        if ($q !== '' && $a !== '') {
            $faqs[] = ['question' => $q, 'answer' => $a];
        }
    }
}

$shareUrl   = urlencode(request()->url());
$shareTitle = urlencode($title);
$isFr       = $locale === 'fr';
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

{{-- AEO/GEO: FAQ rich data for answer engines & generative search --}}
@if(!empty($faqs))
<script type="application/ld+json"><?php echo json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => [
        '@type'          => 'Question',
        'name'           => $f['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
    ], $faqs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></script>
@endif

{{-- Breadcrumb trail for SERP + AI navigation --}}
<script type="application/ld+json"><?php echo json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => ($isFr ? 'Accueil' : 'Home'), 'item' => url($locale)],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url($locale . '/blog')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => request()->url()],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></script>
@endpush

<style>
/* ── Blog engagement bar ──────────────────────────────────── */
.blog-engage-bar {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 14px 0;
    border-top: 1px solid #1e293b;
    border-bottom: 1px solid #1e293b;
    margin: 28px 0 32px;
    flex-wrap: wrap;
}
.blog-engage-stat {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-size: 0.82rem;
}
.blog-engage-stat svg { opacity: 0.6; }
.blog-engage-stat span { font-weight: 600; color: #e2e8f0; }

/* ── Like button ──────────────────────────────────────────── */
.blog-like-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: transparent;
    border: 1px solid #1e293b;
    border-radius: 20px;
    padding: 7px 16px;
    color: var(--text-muted);
    font-size: 0.82rem;
    cursor: pointer;
    transition: border-color .2s, color .2s, background .2s;
    margin-left: auto;
}
.blog-like-btn:hover, .blog-like-btn.liked {
    border-color: #e11d48;
    color: #e11d48;
    background: #e11d481a;
}
.blog-like-btn .like-count { font-weight: 700; }

/* ── Comments section ─────────────────────────────────────── */
.blog-comments {
    margin-top: 52px;
    padding-top: 40px;
    border-top: 1px solid #1e293b;
}
.blog-comments-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #f1f5f9;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.blog-comment-item {
    background: #0f1624;
    border: 1px solid #1e293b;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 14px;
}
.blog-comment-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.blog-comment-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00C896, #1A6FE8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.blog-comment-name { font-weight: 600; color: #e2e8f0; font-size: 0.88rem; }
.blog-comment-date { color: var(--text-muted); font-size: 0.75rem; margin-left: auto; }
.blog-comment-body { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
.blog-no-comments { color: var(--text-muted); font-size: 0.88rem; padding: 20px 0; }

/* ── Comment form ─────────────────────────────────────────── */
.blog-comment-form {
    margin-top: 36px;
    background: #080e1a;
    border: 1px solid #1e293b;
    border-radius: 12px;
    padding: 28px;
}
.blog-comment-form h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #f1f5f9;
    margin-bottom: 20px;
}
.bcf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.bcf-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.bcf-group label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); letter-spacing: .04em; text-transform: uppercase; }
.bcf-group input, .bcf-group textarea {
    background: #0f172a;
    border: 1px solid #1e293b;
    border-radius: 7px;
    padding: 10px 14px;
    color: #e2e8f0;
    font-size: 0.88rem;
    font-family: inherit;
    width: 100%;
    transition: border-color .2s;
}
.bcf-group input:focus, .bcf-group textarea:focus {
    outline: none;
    border-color: #00C896;
}
.bcf-group textarea { min-height: 110px; resize: vertical; }
.bcf-submit {
    background: #00C896;
    color: #0f172a;
    border: none;
    border-radius: 8px;
    padding: 11px 28px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s;
}
.bcf-submit:hover { background: #00b386; }
.bcf-success {
    background: #00C89618;
    border: 1px solid #00C89640;
    border-radius: 8px;
    padding: 14px 18px;
    color: #00C896;
    font-size: 0.88rem;
    margin-bottom: 20px;
}
@media (max-width: 600px) {
    .bcf-row { grid-template-columns: 1fr; }
    .blog-engage-bar { gap: 14px; }
    .blog-like-btn { margin-left: 0; }
}
</style>

<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">{{ $locale === 'fr' ? 'Accueil' : 'Home' }}</a>
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

            {{-- ── Engagement stats bar ─────────────────────────── --}}
            <div class="blog-engage-bar">
                <div class="blog-engage-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span id="view-count">{{ number_format($post->views) }}</span>
                    {{ $isFr ? 'vues' : 'views' }}
                </div>
                <div class="blog-engage-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>{{ $comments->count() }}</span>
                    {{ $isFr ? 'commentaires' : 'comments' }}
                </div>
                <div class="blog-engage-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <span id="share-count">{{ number_format($post->shares) }}</span>
                    {{ $isFr ? 'partages' : 'shares' }}
                </div>

                {{-- Like button --}}
                <button class="blog-like-btn" id="like-btn"
                    data-url="{{ route('blog.like', [$locale, $post->slug]) }}"
                    data-csrf="{{ csrf_token() }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <span class="like-count">{{ number_format($post->likes) }}</span>
                    {{ $isFr ? 'J\'aime' : 'Like' }}
                </button>
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
            <span class="blog-share-label">{{ $locale === 'fr' ? 'Partager cet article' : 'Share this article' }}</span>
            <div class="blog-share-links">
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-linkedin"
                   data-track-share="1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    LinkedIn
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-twitter"
                   data-track-share="1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    X / Twitter
                </a>
                <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                   target="_blank" rel="noopener noreferrer" class="blog-share-btn blog-share-whatsapp"
                   data-track-share="1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>
        </div>

        {{-- ── Comments section ─────────────────────────────── --}}
        <div class="blog-comments">
            <div class="blog-comments-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                {{ $isFr ? 'Commentaires' : 'Comments' }}
                <span style="background:#1e293b;color:var(--text-muted);font-size:0.75rem;padding:2px 9px;border-radius:10px;font-weight:600">{{ $comments->count() }}</span>
            </div>

            @if($comments->isEmpty())
                <p class="blog-no-comments">{{ $isFr ? 'Aucun commentaire pour l\'instant. Soyez le premier à commenter !' : 'No comments yet. Be the first to comment!' }}</p>
            @else
                @foreach($comments as $comment)
                <div class="blog-comment-item">
                    <div class="blog-comment-meta">
                        <div class="blog-comment-avatar">{{ strtoupper(substr($comment->name, 0, 1)) }}</div>
                        <span class="blog-comment-name">{{ $comment->name }}</span>
                        <span class="blog-comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="blog-comment-body">{{ $comment->content }}</div>
                </div>
                @endforeach
            @endif

            {{-- Comment form --}}
            <div class="blog-comment-form">
                <h3>{{ $isFr ? 'Laisser un commentaire' : 'Leave a comment' }}</h3>

                @if(session('comment_submitted'))
                <div class="bcf-success">
                    <strong>{{ $isFr ? 'Merci !' : 'Thank you!' }}</strong>
                    {{ $isFr ? 'Votre commentaire est en attente de modération.' : 'Your comment is awaiting moderation.' }}
                </div>
                @endif

                <form action="{{ route('blog.comment', [$locale, $post->slug]) }}" method="POST">
                    @csrf
                    <div class="bcf-row">
                        <div class="bcf-group">
                            <label>{{ $isFr ? 'Nom' : 'Name' }} *</label>
                            <input type="text" name="name" required maxlength="100"
                                   value="{{ old('name') }}"
                                   placeholder="{{ $isFr ? 'Votre nom' : 'Your name' }}">
                            @error('name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                        </div>
                        <div class="bcf-group">
                            <label>Email *</label>
                            <input type="email" name="email" required maxlength="150"
                                   value="{{ old('email') }}"
                                   placeholder="{{ $isFr ? 'votre@email.com' : 'your@email.com' }}">
                            @error('email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="bcf-group">
                        <label>{{ $isFr ? 'Commentaire' : 'Comment' }} *</label>
                        <textarea name="content" required maxlength="2000"
                                  placeholder="{{ $isFr ? 'Partagez votre avis...' : 'Share your thoughts...' }}">{{ old('content') }}</textarea>
                        @error('content')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="bcf-submit">
                        {{ $isFr ? 'Envoyer le commentaire' : 'Post comment' }}
                    </button>
                </form>
            </div>
        </div>

        @if($related->count())
        <div class="divider" style="margin:48px 0"></div>
        <h2 class="section-title" style="margin-bottom:24px">{{ $locale === 'fr' ? 'Articles connexes' : 'Related articles' }}</h2>
        <div class="blog-grid" style="grid-template-columns:repeat(3,1fr)">
            @foreach($related as $rp)
            @php
            $relColors = [
                'Digital Health in Cameroon' => '#00C896',
                'Healthcare Challenges'      => '#ef4444',
                'HMS Solutions'              => '#1A6FE8',
                "Buyer's Guide"              => '#8b5cf6',
                'AI & Technology'            => '#f59e0b',
                'Insights & Case Studies'    => 'var(--text-muted)',
            ];
            $relColor = $relColors[$rp->category] ?? 'var(--text-muted)';
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
            {{ $locale === 'fr' ? 'Dans cet article' : 'In this article' }}
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

<script>
(function () {
    // ── Like button ──────────────────────────────────────────
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        const storageKey = 'liked_{{ $post->id }}';
        if (localStorage.getItem(storageKey)) {
            likeBtn.classList.add('liked');
        }
        likeBtn.addEventListener('click', function () {
            if (likeBtn.classList.contains('liked')) return;
            likeBtn.classList.add('liked');
            localStorage.setItem(storageKey, '1');
            fetch(likeBtn.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': likeBtn.dataset.csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                likeBtn.querySelector('.like-count').textContent = data.likes.toLocaleString();
            })
            .catch(() => {});
        });
    }

    // ── Track shares ─────────────────────────────────────────
    const shareUrl = '{{ route("blog.share", [$locale, $post->slug]) }}';
    const csrfToken = '{{ csrf_token() }}';
    document.querySelectorAll('[data-track-share]').forEach(function (link) {
        link.addEventListener('click', function () {
            const shareCountEl = document.getElementById('share-count');
            fetch(shareUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (shareCountEl) shareCountEl.textContent = data.shares.toLocaleString();
            })
            .catch(() => {});
        });
    });
})();
</script>

</x-layouts.app>
