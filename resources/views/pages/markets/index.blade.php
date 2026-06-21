@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Marchés CEMAC — Logiciel hospitalier par pays' : 'CEMAC Markets — Hospital Software by Country' }}"
    description="{{ $isFr ? 'OPES Health Systems à travers la CEMAC : pages dédiées par pays — Gabon, Congo, Tchad, RCA et Guinée équatoriale — avec le contexte local et la facturation adaptée.' : 'OPES Health Systems across CEMAC: dedicated country pages — Gabon, Congo, Chad, CAR and Equatorial Guinea — with local context and payer-ready billing.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="globe" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Couverture régionale CEMAC' : 'CEMAC regional coverage' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Solutions hospitalières par' : 'Hospital solutions across' }}
        <span class="gradient-text">{{ $isFr ? 'pays CEMAC' : 'CEMAC' }}</span>
    </h1>
    <p class="about-sub" style="max-width:740px">
        {{ $isFr
            ? 'OPES Health Systems est conçu pour le Cameroun et l\'ensemble de la zone CEMAC. Chaque marché a ses payeurs, ses langues et ses réalités d\'infrastructure — voici comment OPES s\'adapte, pays par pays.'
            : 'OPES Health Systems is built for Cameroon and the wider CEMAC zone. Every market has its own payers, languages, and infrastructure realities — here is how OPES fits, country by country.' }}
    </p>
</div>

{{-- ── HOME MARKET ──────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <a href="{{ url($locale.'/products') }}" style="display:flex;align-items:center;gap:16px;padding:20px 24px;background:linear-gradient(135deg,#0f1f2e,#0d1a14);border:1px solid rgba(0,200,150,0.25);border-radius:16px;text-decoration:none">
        <img src="{{ asset('flags/cm.svg') }}" alt="Cameroon" style="width:42px;height:28px;border-radius:4px;object-fit:cover">
        <div style="flex:1">
            <div style="font-weight:800;color:#00C896;font-size:15px;margin-bottom:3px">{{ $isFr ? 'Cameroun — marché d\'origine' : 'Cameroon — home market' }}</div>
            <div style="font-size:12.5px;color:#94a3b8;line-height:1.6">{{ $isFr ? 'Conçu et développé au Cameroun : bilingue EN/FR, mobile money (MoMo, Orange Money), facturation CNPS/mutuelles/CSU, et les 22 systèmes OPES.' : 'Designed and built in Cameroon: bilingual EN/FR, mobile money (MoMo, Orange Money), CNPS/mutuelles/CSU billing, and all 22 OPES systems.' }}</div>
        </div>
        <i data-lucide="arrow-right" style="width:18px;height:18px;color:#00C896;flex-shrink:0"></i>
    </a>
</div>

<div class="divider"></div>

{{-- ── OTHER CEMAC MARKETS ──────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Les autres marchés CEMAC' : 'The other CEMAC markets' }}
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;margin-top:24px">
        @foreach($markets as $m)
        @php $accent = $m['accent'] ?? '#00C896'; @endphp
        <a href="{{ url($locale.'/markets/'.$m['slug']) }}" style="display:block;background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px;text-decoration:none">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <img src="{{ asset('flags/'.$m['code'].'.svg') }}" alt="" style="width:36px;height:24px;border-radius:3px;object-fit:cover">
                <div>
                    <div style="font-weight:800;color:#e2e8f0;font-size:14px">{{ $m['name'] }}</div>
                    <div style="font-size:11px;color:#64748b">{{ $m['capital'] }} · {{ $m['currency'] }}</div>
                </div>
            </div>
            <p style="font-size:12px;color:#94a3b8;line-height:1.6;margin:0 0 14px">{{ $m['tagline'][$locale] }}</p>
            <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:10px;font-weight:800;color:{{ $accent }};text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? $m['driver']['title_fr'] : $m['driver']['title_en'] }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:12px;font-size:12px;font-weight:700;color:{{ $accent }}">
                {{ $isFr ? 'Voir le marché' : 'View market' }}
                <i data-lucide="arrow-right" style="width:13px;height:13px"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Un projet dans la zone CEMAC ?' : 'A project in the CEMAC zone?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe peut adapter une démonstration à votre pays, vos payeurs et votre contexte d\'infrastructure.'
        : 'Our team can tailor a demonstration to your country, your payers, and your infrastructure context.' }}</p>
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

</x-layouts.app>
