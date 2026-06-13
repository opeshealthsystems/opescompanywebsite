@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

{{-- ── PAGE HEADER ───────────────────────────────────────────────── --}}
<div class="pi-header">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layout-grid" style="width:12px;height:12px"></i>
        All Products
    </div>
    <h1 class="pi-title">22 Healthcare Software Systems</h1>
    <p class="pi-sub">Purpose-built for the Cameroon and CEMAC health sector. Deploy one system or the full ecosystem — all connected through OPESCare Health ID.</p>

    <div class="pi-trust-bar">
        <span><i data-lucide="link" style="width:12px;height:12px;color:#00C896"></i> HL7 FHIR Interoperability</span>
        <span><i data-lucide="globe" style="width:12px;height:12px;color:#1A6FE8"></i> EN / FR bilingual</span>
        <span><i data-lucide="shield-check" style="width:12px;height:12px;color:#00C896"></i> MoH 2026–2030 aligned</span>
        <span><i data-lucide="cloud" style="width:12px;height:12px;color:#1A6FE8"></i> Cloud & on-premise</span>
    </div>
</div>

{{-- ── CORE PLATFORM ──────────────────────────────────────────────── --}}
<div class="section pi-section">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:#00C896"></div>
        <h2 class="pi-cat-title">Core Platform</h2>
        <span class="pi-cat-count">{{ count($grouped['Core Platform']) }} systems</span>
    </div>
    <p class="pi-cat-desc">Foundation systems that every OPES-connected facility starts with. Covers patient identity, clinical records, hospital management, and revenue.</p>
    <div class="pi-grid">
        @foreach($grouped['Core Platform'] as $p)
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
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:#475569"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── DIAGNOSTICS ─────────────────────────────────────────────────── --}}
<div class="section pi-section">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:#1A6FE8"></div>
        <h2 class="pi-cat-title">Diagnostics & Support</h2>
        <span class="pi-cat-count">{{ count($grouped['Diagnostics']) }} systems</span>
    </div>
    <p class="pi-cat-desc">Laboratory, pharmacy, radiology, documents, and revenue cycle management — the operational backbone of any modern health facility.</p>
    <div class="pi-grid">
        @foreach($grouped['Diagnostics'] as $p)
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
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:#475569"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── SPECIALIST SYSTEMS ──────────────────────────────────────────── --}}
<div class="section pi-section">
    <div class="pi-cat-header">
        <div class="pi-cat-dot" style="background:#94a3b8"></div>
        <h2 class="pi-cat-title">Specialist Systems</h2>
        <span class="pi-cat-count">{{ count($grouped['Specialist']) }} systems</span>
    </div>
    <p class="pi-cat-desc">Discipline-specific modules for specialist clinics and departments — each integrates natively with the Core Platform via OPESCare Health ID.</p>
    <div class="pi-grid">
        @foreach($grouped['Specialist'] as $p)
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
                <i data-lucide="arrow-right" style="width:14px;height:14px;color:#475569"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>

{{-- ── DEMO CTA ─────────────────────────────────────────────────── --}}
<div class="demo-section">
    <div class="section-label" style="justify-content:center;color:#00C896;margin-bottom:12px">
        <i data-lucide="calendar-check" style="width:13px;height:13px"></i>
        Book a Demo
    </div>
    <h2>See the full OPES ecosystem</h2>
    <p>Not sure where to start? Book a free 45-minute overview with our team. We'll recommend the right systems for your facility type and budget.</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        Book a Free Demo
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
