@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="{{ __('products.meta_title') }}"
    description="{{ __('products.meta_desc') }}">

{{-- ── PAGE HEADER ───────────────────────────────────────────────── --}}
<div class="pi-header">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layout-grid" style="width:12px;height:12px"></i>
        {{ __('products.index_eyebrow') }}
    </div>
    <h1 class="pi-title">{{ __('products.index_title') }}</h1>
    <p class="pi-sub">{{ __('products.index_sub') }}</p>
    <p class="brand-notice">{{ $locale === 'fr' ? count($all).' produits au catalogue. Les fonctionnalités, intégrations et disponibilités sont à confirmer pour chaque projet. Une fiche produit ne constitue pas une preuve de certification ni de déploiement.' : count($all).' products in the catalogue. Confirm features, integrations and availability for your project with our team. A product listing is not evidence of certification or deployment.' }}</p>

    <div class="pi-trust-bar">
        <span><i data-lucide="link" style="width:12px;height:12px;color:var(--brand-accent)"></i> {{ __('products.trust_fhir') }}</span>
        <span><i data-lucide="globe" style="width:12px;height:12px;color:var(--brand-accent)"></i> {{ __('products.trust_bilingual') }}</span>
        <span><i data-lucide="shield-check" style="width:12px;height:12px;color:var(--brand-accent)"></i> {{ __('products.trust_moh') }}</span>
        <span><i data-lucide="cloud" style="width:12px;height:12px;color:var(--brand-accent)"></i> {{ __('products.trust_deploy') }}</span>
    </div>
</div>

{{-- ── CORE PLATFORM ──────────────────────────────────────────────── --}}
<div class="section pi-section" id="core-platform">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:#69D5F5"></div>
        <h2 class="pi-cat-title">{{ __('products.cat_core') }}</h2>
        <span class="pi-cat-count">{{ trans_choice('products.systems_count', count($grouped['Core Platform'] ?? []), ['count' => count($grouped['Core Platform'] ?? [])]) }}</span>
    </div>
    <p class="pi-cat-desc">{{ __('products.cat_core_desc') }}</p>
    <div class="pi-grid">
        @foreach(($grouped['Core Platform'] ?? []) as $p)
        <a href="{{ url($locale.'/products/'.$p['slug']) }}" class="pi-card">
            <div class="pi-card-icon" style="background:rgba(0,200,150,0.1);border-color:rgba(0,200,150,0.2)">
                <i data-lucide="{{ $p['icon'] }}" style="width:24px;height:24px;color:{{ $p['color'] }}"></i>
            </div>
            <div class="pi-card-body">
                <div class="pi-card-name">{{ $p['name'] }}</div>
                <div class="pi-card-sub">{{ $p['subtitle'] }}</div>
                <p class="pi-card-tagline">{{ Str::limit($p['tagline'], 90) }}</p>
            </div>
            <div class="pi-card-arrow">
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:var(--text-faint)"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── DIAGNOSTICS ─────────────────────────────────────────────────── --}}
<div class="section pi-section" id="diagnostics">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:#8AC5ED"></div>
        <h2 class="pi-cat-title">{{ __('products.cat_diagnostics') }}</h2>
        <span class="pi-cat-count">{{ trans_choice('products.systems_count', count($grouped['Diagnostics'] ?? []), ['count' => count($grouped['Diagnostics'] ?? [])]) }}</span>
    </div>
    <p class="pi-cat-desc">{{ __('products.cat_diagnostics_desc') }}</p>
    <div class="pi-grid">
        @foreach(($grouped['Diagnostics'] ?? []) as $p)
        <a href="{{ url($locale.'/products/'.$p['slug']) }}" class="pi-card">
            <div class="pi-card-icon" style="background:rgba(26,111,232,0.1);border-color:rgba(26,111,232,0.2)">
                <i data-lucide="{{ $p['icon'] }}" style="width:24px;height:24px;color:{{ $p['color'] }}"></i>
            </div>
            <div class="pi-card-body">
                <div class="pi-card-name">{{ $p['name'] }}</div>
                <div class="pi-card-sub">{{ $p['subtitle'] }}</div>
                <p class="pi-card-tagline">{{ Str::limit($p['tagline'], 90) }}</p>
            </div>
            <div class="pi-card-arrow">
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:var(--text-faint)"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── SPECIALIST SYSTEMS ──────────────────────────────────────────── --}}
<div class="section pi-section" id="specialist-systems">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:var(--text-muted)"></div>
        <h2 class="pi-cat-title">{{ __('products.cat_specialist') }}</h2>
        <span class="pi-cat-count">{{ trans_choice('products.systems_count', count($grouped['Specialist'] ?? []), ['count' => count($grouped['Specialist'] ?? [])]) }}</span>
    </div>
    <p class="pi-cat-desc">{{ __('products.cat_specialist_desc') }}</p>
    <div class="pi-grid">
        @foreach(($grouped['Specialist'] ?? []) as $p)
        <a href="{{ url($locale.'/products/'.$p['slug']) }}" class="pi-card">
            <div class="pi-card-icon" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1)">
                <i data-lucide="{{ $p['icon'] }}" style="width:24px;height:24px;color:{{ $p['color'] }}"></i>
            </div>
            <div class="pi-card-body">
                <div class="pi-card-name">{{ $p['name'] }}</div>
                <div class="pi-card-sub">{{ $p['subtitle'] }}</div>
                <p class="pi-card-tagline">{{ Str::limit($p['tagline'], 90) }}</p>
            </div>
            <div class="pi-card-arrow">
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:var(--text-faint)"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

{{-- ── DEMO CTA ─────────────────────────────────────────────────── --}}
@if(!empty($grouped['Medicine Safety']))
<section class="section pi-section" id="medicine-safety">
    <h2 class="section-title">{{ $locale === 'fr' ? 'Sécurité du médicament' : 'Medicine Safety' }}</h2>
    <p class="section-sub">{{ $locale === 'fr' ? 'Traçabilité pharmaceutique' : 'Pharmaceutical traceability' }} · {{ count($grouped['Medicine Safety']) }} {{ $locale === 'fr' ? 'produit' : 'product' }}</p>
    <div class="pi-grid">@foreach($grouped['Medicine Safety'] as $p)
        <a href="{{ url($locale.'/products/'.$p['slug']) }}" class="pi-card"><div class="pi-card-body"><h3 class="pi-card-name">{{ $p['name'] }}</h3><p class="pi-card-tagline">{{ $locale === 'fr' ? 'Disponible — demander une démonstration et confirmer le périmètre du projet.' : 'Released and available — request a demonstration and confirm your project scope.' }}</p></div></a>
    @endforeach</div>
</section>
@endif
<div class="demo-section">
    <div class="section-label" style="justify-content:center;color:var(--brand-accent);margin-bottom:12px">
        <i data-lucide="calendar-check" style="width:13px;height:13px"></i>
        {{ __('products.demo_eyebrow') }}
    </div>
    <h2>{{ __('products.demo_heading') }}</h2>
    <p>{{ __('products.demo_body') }}</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        {{ __('products.demo_btn') }}
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
