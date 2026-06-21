@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; $accent = $market['accent'] ?? '#00C896'; @endphp

<x-layouts.app
    title="{{ $market['name'] }} — {{ $isFr ? 'Logiciel de gestion hospitalière' : 'Hospital Management Software' }}"
    description="{{ $market['tagline'][$locale] }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="map-pin" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Marché CEMAC' : 'CEMAC market' }} · {{ $market['flag'] }} {{ $market['name'] }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Logiciel de gestion hospitalière' : 'Hospital management software' }}
        <span class="gradient-text">— {{ $market['name'] }}</span>
    </h1>
    <p class="about-sub" style="max-width:760px">{{ $market['tagline'][$locale] }}</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:24px">
        <a href="{{ url($locale.'/book-demo') }}" class="btn-primary">
            {{ $isFr ? 'Demander une démo' : 'Book a demo' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/products') }}" class="btn-secondary">
            {{ $isFr ? 'Voir les 22 systèmes' : 'See the 22 systems' }}
            <i data-lucide="layout-grid" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:880px;margin:0 auto">
        @foreach($market['stats'] as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $isFr ? $s[2] : $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── INTRO + DRIVER HIGHLIGHT ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start">
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="globe-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Le marché en bref' : 'The market in brief' }}
            </div>
            <p style="font-size:14px;line-height:1.8;color:#94a3b8">{{ $market['intro'][$locale] }}</p>
        </div>
        {{-- Local driver highlight --}}
        <div style="background:linear-gradient(135deg,#0f1f2e,#0d1a14);border:1px solid {{ $accent }}33;border-radius:16px;padding:24px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $accent }}1f;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $market['driver']['icon'] }}" style="width:18px;height:18px;color:{{ $accent }}"></i>
                </div>
                <div style="font-weight:800;color:#e2e8f0;font-size:14px">{{ $isFr ? $market['driver']['title_fr'] : $market['driver']['title_en'] }}</div>
            </div>
            <p style="font-size:12.5px;line-height:1.7;color:#94a3b8;margin:0">{{ $isFr ? $market['driver']['desc_fr'] : $market['driver']['desc_en'] }}</p>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── WHY DIGITALISE HERE ──────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="list-checks" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Pourquoi digitaliser ici' : 'Why digitalise here' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Ce qui rend ce marché spécifique' : 'What makes this market specific' }}</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:24px">
        @foreach($market['context'][$locale] as $item)
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;background:#0F172A;border-radius:10px;border-left:2px solid {{ $accent }}">
            <i data-lucide="check-circle" style="width:13px;height:13px;color:{{ $accent }};flex-shrink:0;margin-top:3px"></i>
            <span style="font-size:13px;color:#94a3b8;line-height:1.6">{{ $item }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── HOW OPES FITS ────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="check-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Comment OPES s\'adapte' : 'How OPES fits' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Conçu pour les réalités de '.$market['name'] : 'Built for '.$market['name'].'\'s realities' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @foreach($market['fit'] as $f)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $accent }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $f[0] }}" style="width:16px;height:16px;color:{{ $accent }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $isFr ? $f[2] : $f[1] }}</div>
            </div>
            <p style="font-size:12px;color:#64748b;line-height:1.6;margin:0">{{ $isFr ? $f[4] : $f[3] }}</p>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── QUICK FACTS ──────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="info" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Repères' : 'Quick facts' }}
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px">
        @php $facts = [
            ['map-pin', $isFr ? 'Capitale' : 'Capital', $market['capital']],
            ['building', $isFr ? 'Villes clés' : 'Key cities', $market['cities']],
            ['languages', $isFr ? 'Langue' : 'Language', $market['language'][$locale]],
            ['coins', $isFr ? 'Monnaie' : 'Currency', $market['currency']],
            ['clipboard-list', $isFr ? 'Plan national' : 'National plan', $market['plan']],
            ['hospital', $isFr ? 'Établissements de référence' : 'Reference facilities', $market['facilities']],
        ]; @endphp
        @foreach($facts as $fact)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px 16px">
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:6px">
                <i data-lucide="{{ $fact[0] }}" style="width:12px;height:12px;color:{{ $accent }}"></i>
                <span style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.06em">{{ $fact[1] }}</span>
            </div>
            <div style="font-size:12.5px;color:#cbd5e1;line-height:1.5">{{ $fact[2] }}</div>
        </div>
        @endforeach
    </div>
    <p style="font-size:11px;color:#475569;margin-top:14px">
        {{ $isFr
            ? 'Pour une analyse approfondie du système de santé, lisez notre article :'
            : 'For an in-depth look at the health system, read our article:' }}
        <a href="{{ url($locale.'/blog/'.$market['related_blog']) }}" style="color:{{ $accent }};text-decoration:none;font-weight:600">{{ $isFr ? 'Santé numérique — '.$market['name'] : 'Digital health — '.$market['name'] }} →</a>
    </p>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Vous opérez au '.$market['name'].' ?' : 'Operating in '.$market['name'].'?' }}</h2>
    <p>{{ $isFr
        ? 'Découvrez comment OPES s\'adapte à votre établissement et à votre contexte réglementaire local.'
        : 'See how OPES adapts to your facility and your local regulatory context.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/book-demo') }}" class="btn-primary">
            {{ $isFr ? 'Demander une démo' : 'Book a demo' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter notre équipe' : 'Contact our team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

{{-- ── OTHER CEMAC MARKETS ──────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;padding-top:8px">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="globe" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Autres marchés CEMAC' : 'Other CEMAC markets' }}
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
        @foreach($others as $o)
        <a href="{{ url($locale.'/markets/'.$o['slug']) }}" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#0F172A;border:1px solid #1e293b;border-radius:10px;text-decoration:none;transition:border-color .15s">
            <span style="font-size:20px">{{ $o['flag'] }}</span>
            <div>
                <div style="font-weight:700;font-size:12.5px;color:#e2e8f0">{{ $o['name'] }}</div>
                <div style="font-size:10px;color:#64748b">{{ $o['capital'] }}</div>
            </div>
        </a>
        @endforeach
        <a href="{{ url($locale.'/markets') }}" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#0F172A;border:1px dashed #334155;border-radius:10px;text-decoration:none">
            <i data-lucide="layout-grid" style="width:18px;height:18px;color:{{ $accent }}"></i>
            <div style="font-weight:700;font-size:12.5px;color:#e2e8f0">{{ $isFr ? 'Tous les marchés' : 'All markets' }}</div>
        </a>
    </div>
</div>

</x-layouts.app>
