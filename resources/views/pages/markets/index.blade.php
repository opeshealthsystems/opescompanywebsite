@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Guides pays CEMAC et projets de santé en Afrique | OPES' : 'CEMAC Country Guides and African Health Projects | OPES' }}"
    description="{{ $isFr ? 'Guides pays initiaux pour le Cameroun et la CEMAC. OPES Health Systems accueille aussi des projets de santé dans toute l’Afrique et des partenariats internationaux, selon le contexte local.' : 'Initial country guides for Cameroon and CEMAC. OPES Health Systems also welcomes health projects across Africa and international partnerships, scoped to local context.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="globe" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Guides pays · Point de départ : Cameroun et CEMAC' : 'Country guides · Starting with Cameroon and CEMAC' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Des solutions de santé pour' : 'Health solutions for' }}
        <span class="gradient-text">{{ $isFr ? 'l’Afrique et au-delà' : 'Africa and beyond' }}</span>
    </h1>
    <p class="about-sub" style="max-width:740px">
        {{ $isFr
            ? 'Le Cameroun est notre point de départ et les pages ci-dessous sont nos premiers guides pays CEMAC. Notre marché cible est l’Afrique dans son ensemble, avec des partenariats possibles dans le monde entier. Chaque projet est défini selon les langues, les règles, les priorités et les réalités d’infrastructure locales.'
            : 'Cameroon is our starting point, and the pages below are our initial CEMAC country guides. Our target market is Africa as a whole, with partnership opportunities worldwide. Each project is scoped to local languages, rules, priorities and infrastructure realities.' }}
    </p>
</div>

{{-- ── HOME MARKET ──────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <a href="{{ url($locale.'/products') }}" style="display:flex;align-items:center;gap:16px;padding:20px 24px;background:var(--surface);border:1px solid rgba(0,200,150,0.25);border-radius:16px;text-decoration:none">
        <img src="{{ asset('flags/cm.svg') }}" alt="Cameroon" style="width:42px;height:28px;border-radius:4px;object-fit:cover">
        <div style="flex:1">
            <div style="font-weight:800;color:var(--brand-accent);font-size:15px;margin-bottom:3px">{{ $isFr ? 'Cameroun — marché d\'origine' : 'Cameroon — home market' }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">{{ $isFr ? 'Conçu et développé au Cameroun : bilingue EN/FR, mobile money (MoMo, Orange Money), facturation CNPS/mutuelles/CSU, et les 22 systèmes OPES.' : 'Designed and built in Cameroon: bilingual EN/FR, mobile money (MoMo, Orange Money), CNPS/mutuelles/CSU billing, and all 22 OPES systems.' }}</div>
        </div>
        <i data-lucide="arrow-right" style="width:18px;height:18px;color:var(--brand-accent);flex-shrink:0"></i>
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
        @php $accent = $m['accent'] ?? '#69D5F5'; @endphp
        <a href="{{ url($locale.'/markets/'.$m['slug']) }}" style="display:block;background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px;text-decoration:none">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <img src="{{ asset('flags/'.$m['code'].'.svg') }}" alt="" style="width:36px;height:24px;border-radius:3px;object-fit:cover">
                <div>
                    <div style="font-weight:800;color:#e2e8f0;font-size:14px">{{ $m['name'] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $m['capital'] }} · {{ $m['currency'] }}</div>
                </div>
            </div>
            <p style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6;margin:0 0 14px">{{ $m['tagline'][$locale] }}</p>
            <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:var(--fs-2xs);font-weight:800;color:{{ $accent }};text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? $m['driver']['title_fr'] : $m['driver']['title_en'] }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:12px;font-size:var(--fs-xs);font-weight:700;color:{{ $accent }}">
                {{ $isFr ? 'Voir le marché' : 'View market' }}
                <i data-lucide="arrow-right" style="width:13px;height:13px"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="section" style="max-width:960px;margin:0 auto">
    <div style="padding:24px;background:var(--surface);border:1px solid #29445e;border-radius:14px">
        <div class="section-label" style="margin-bottom:10px"><i data-lucide="globe-2" style="width:12px;height:12px"></i>{{ $isFr ? 'Au-delà des guides CEMAC' : 'Beyond the CEMAC guides' }}</div>
        <h2 style="margin:0 0 10px;color:#eaf2f8;font-size:clamp(1.2rem,2.6vw,1.6rem)">{{ $isFr ? 'Afrique entière. Collaboration internationale.' : 'Across Africa. Open to global collaboration.' }}</h2>
        <p style="margin:0;color:var(--text-muted);line-height:1.7">{{ $isFr ? 'Nous invitons les gouvernements, établissements de santé, universités, organismes de recherche et partenaires technologiques de tous les pays africains et d’autres régions du monde à nous présenter leurs besoins. La disponibilité, la conformité, les intégrations et le déploiement sont évalués pour chaque pays et chaque projet.' : 'We invite governments, health organizations, universities, research institutions and technology partners in every African country and other regions to discuss their needs. Availability, compliance, integrations and deployment are assessed for each country and project.' }}</p>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Un projet de santé au Cameroun, en Afrique ou ailleurs ?' : 'A health project in Cameroon, Africa or elsewhere?' }}</h2>
    <p>{{ $isFr
        ? 'Présentez-nous votre pays, vos objectifs et votre contexte. Nous discuterons de l’adéquation, des exigences locales et des prochaines étapes.'
        : 'Tell us about your country, goals and operating context. We will discuss fit, local requirements and possible next steps.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/book-demo') }}" class="btn-primary">
            {{ $isFr ? 'Demander une démo' : 'Book a demo' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter notre équipe' : 'Contact our team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>
