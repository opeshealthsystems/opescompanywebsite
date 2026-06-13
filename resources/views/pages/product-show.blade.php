@php
$locale = app()->getLocale();
$color  = $product['color'];
$isCore = $product['category'] === 'Core Platform';
$isDiag = $product['category'] === 'Diagnostics';
$accentBg = $isCore ? 'rgba(0,200,150,0.1)' : ($isDiag ? 'rgba(26,111,232,0.1)' : 'rgba(255,255,255,0.06)');
@endphp

<x-layouts.app>

{{-- ── BREADCRUMB ──────────────────────────────────────────────── --}}
<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">Home</a>
    <span>›</span>
    <a href="{{ url($locale.'/products') }}">Products</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">{{ $product['name'] }}</span>
</div>

{{-- ── PRODUCT HERO ────────────────────────────────────────────── --}}
<div class="pd-hero">
    <div class="pd-hero-inner">
        <div>
            <div class="pd-badge">
                <i data-lucide="tag" style="width:10px;height:10px"></i>
                {{ $product['category'] }}
            </div>
            <div class="pd-hero-icon" style="background:{{ $accentBg }};border-color:{{ $color }}30">
                <i data-lucide="{{ $product['icon'] }}" style="width:30px;height:30px;color:{{ $color }}"></i>
            </div>
            <h1 class="pd-hero-name">{{ $product['name'] }}
                <span class="pd-hero-sub">{{ $product['subtitle'] }}</span>
            </h1>
            <p class="pd-tagline">{{ $product['tagline'] }}</p>
            <div class="pd-hero-ctas">
                <a href="{{ url($locale.'/contact') }}" class="btn-primary">
                    <i data-lucide="calendar-check" style="width:15px;height:15px"></i>
                    Book a Free Demo
                </a>
                <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
                    <i data-lucide="download" style="width:15px;height:15px;color:#94a3b8"></i>
                    Download Brochure
                </a>
            </div>
        </div>

        {{-- Stats box --}}
        <div class="pd-stats-box">
            <div class="pd-stats-title">At a glance</div>
            @foreach($product['stats'] ?? [] as $stat)
            <div class="pd-stat-row">
                <div class="pd-stat-val" style="color:{{ $color }}">{{ $stat['value'] }}</div>
                <div class="pd-stat-label">{{ $stat['label'] }}</div>
            </div>
            @endforeach
            <div class="pd-stat-row" style="border-top:1px solid rgba(255,255,255,0.07);margin-top:8px;padding-top:12px">
                <div class="pd-stat-val" style="color:#00C896">EN/FR</div>
                <div class="pd-stat-label">Fully bilingual interface</div>
            </div>
            <div class="pd-stat-row">
                <div class="pd-stat-val" style="color:#1A6FE8">HL7 FHIR</div>
                <div class="pd-stat-label">Interoperability standard</div>
            </div>
        </div>
    </div>
</div>

{{-- ── TAB NAV ─────────────────────────────────────────────────── --}}
<div class="pd-tab-nav">
    <a href="#overview"      class="pd-tab active">Overview</a>
    <a href="#modules"       class="pd-tab">Modules</a>
    <a href="#workflow"      class="pd-tab">Workflow</a>
    <a href="#integrations"  class="pd-tab">Integrations</a>
    <a href="#specs"         class="pd-tab">Technical Specs</a>
</div>

{{-- ── OVERVIEW ─────────────────────────────────────────────────── --}}
<div id="overview" class="section pd-overview">
    <div class="pd-overview-grid">
        <div>
            <div class="section-label" style="color:{{ $color }}">
                <i data-lucide="info" style="width:12px;height:12px"></i> Overview
            </div>
            <h2 class="section-title">{{ $product['name'] }}</h2>
            <p class="pd-desc">{{ $product['description'] }}</p>
            @if(!empty($product['description2']))
            <p class="pd-desc">{{ $product['description2'] }}</p>
            @endif

            @if(!empty($product['target_users']))
            <h4 class="pd-subhead">Who is {{ $product['name'] }} for?</h4>
            <ul class="pd-target-list">
                @foreach($product['target_users'] as $user)
                <li>
                    <span class="pd-check" style="background:{{ $accentBg }}">
                        <i data-lucide="check" style="width:9px;height:9px;color:{{ $color }}"></i>
                    </span>
                    {{ $user }}
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        @if(!empty($product['problems_solved']))
        <div class="pd-problems-box">
            <h4 class="pd-problems-title">Problems {{ $product['name'] }} solves</h4>
            @foreach($product['problems_solved'] as $i => $p)
            <div class="pd-problem">
                <div class="pd-problem-num" style="background:{{ $color }}">{{ $i + 1 }}</div>
                <p><strong>{{ $p['title'] }}</strong> — {{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="divider"></div>

{{-- ── MODULES ──────────────────────────────────────────────────── --}}
@if(!empty($product['modules']))
<div id="modules" class="section">
    <div class="section-label" style="color:{{ $color }}">
        <i data-lucide="layout-grid" style="width:12px;height:12px"></i> Modules
    </div>
    <h2 class="section-title">Everything in one system</h2>
    <p class="section-sub">Each module integrates natively — start with what you need today, expand as you grow.</p>
    <div class="pd-modules-grid">
        @foreach($product['modules'] as $mod)
        <div class="pd-module-card">
            <div class="pd-module-icon" style="background:{{ $accentBg }}">
                <i data-lucide="{{ $mod['icon'] }}" style="width:22px;height:22px;color:{{ $color }}"></i>
            </div>
            <h4>{{ $mod['name'] }}</h4>
            <p>{{ $mod['desc'] }}</p>
            @if(!empty($mod['features']))
            <ul class="pd-module-features">
                @foreach($mod['features'] as $feat)
                <li><span class="pd-feat-dot" style="background:{{ $color }}"></span>{{ $feat }}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>
@endif

{{-- ── WORKFLOW ─────────────────────────────────────────────────── --}}
@if(!empty($product['workflow']))
<div id="workflow" class="section">
    <div class="section-label" style="color:{{ $color }}">
        <i data-lucide="git-branch" style="width:12px;height:12px"></i> Patient Workflow
    </div>
    <h2 class="section-title">How {{ $product['name'] }} works</h2>
    <p class="section-sub">Step-by-step through the clinical workflow — every stage integrated, every handover seamless.</p>

    <div class="pd-workflow">
        @foreach($product['workflow'] as $i => $step)
        <div class="pd-wf-step">
            <div class="pd-wf-node" style="border-color:{{ $color }};{{ $i === 0 ? 'background:'.$color.';' : ($i === count($product['workflow'])-1 ? 'background:#1e293b;border-color:#475569;' : '') }}">
                @if($i === 0)
                <span style="color:#fff;font-weight:700;font-size:12px">{{ $i+1 }}</span>
                @else
                <span style="color:{{ $i === count($product['workflow'])-1 ? '#94a3b8' : $color }};font-weight:700;font-size:12px">{{ $i+1 }}</span>
                @endif
            </div>
            <div class="pd-wf-label">{{ $step['step'] }}</div>
            <div class="pd-wf-desc">{{ $step['desc'] }}</div>
        </div>
        @if($i < count($product['workflow'])-1)
        <div class="pd-wf-arrow" style="background:linear-gradient(to right,{{ $color }},{{ $isDiag ? '#00C896' : '#1A6FE8' }})"></div>
        @endif
        @endforeach
    </div>
</div>

<div class="divider"></div>
@endif

{{-- ── BENEFITS ─────────────────────────────────────────────────── --}}
@if(!empty($product['benefits']))
<div class="section" style="background:rgba(255,255,255,0.02);border-radius:16px">
    <div class="section-label" style="color:{{ $color }};justify-content:center">
        <i data-lucide="star" style="width:12px;height:12px"></i> Key Benefits
    </div>
    <h2 class="section-title" style="text-align:center">Why facilities choose {{ $product['name'] }}</h2>
    <div class="pd-benefits-grid">
        @foreach($product['benefits'] as $ben)
        <div class="pd-benefit">
            <div class="pd-benefit-icon" style="background:{{ $accentBg }}">
                <i data-lucide="{{ $ben['icon'] }}" style="width:26px;height:26px;color:{{ $ben['color'] }}"></i>
            </div>
            <h4>{{ $ben['title'] }}</h4>
            <p>{{ $ben['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>
@endif

{{-- ── INTEGRATIONS ─────────────────────────────────────────────── --}}
@if(!empty($product['integrations']))
@php
$allProducts = array_merge(config('products'), config('products_specialist'));
$integrationData = [];
foreach($product['integrations'] as $slug) {
    if (isset($allProducts[$slug])) {
        $integrationData[] = $allProducts[$slug];
    }
}
@endphp
<div id="integrations" class="section">
    <div class="section-label" style="color:{{ $color }}">
        <i data-lucide="share-2" style="width:12px;height:12px"></i> OPES Ecosystem
    </div>
    <h2 class="section-title">{{ $product['name'] }} connects to</h2>
    <p class="section-sub">Every OPES system is built to interoperate. {{ $product['name'] }} exchanges data with these systems natively through OPESCare Health ID.</p>
    <div class="pd-int-grid">
        @foreach($integrationData as $int)
        <a href="{{ url($locale.'/products/'.$int['slug']) }}" class="pd-int-card">
            <div class="pd-int-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="{{ $int['icon'] }}" style="width:18px;height:18px;color:{{ $int['color'] }}"></i>
            </div>
            <div>
                <div class="pd-int-name">{{ $int['name'] }}</div>
                <div class="pd-int-sub">{{ $int['subtitle'] }}</div>
            </div>
            <i data-lucide="arrow-right" style="width:13px;height:13px;color:#475569;margin-left:auto"></i>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>
@endif

{{-- ── TECHNICAL SPECS ──────────────────────────────────────────── --}}
@if(!empty($product['specs']))
<div id="specs" class="section">
    <div class="section-label" style="color:{{ $color }}">
        <i data-lucide="server" style="width:12px;height:12px"></i> Technical Specifications
    </div>
    <h2 class="section-title">Built to enterprise standards</h2>
    <div class="pd-specs-grid">
        @foreach($product['specs'] as $specGroup)
        <div class="pd-specs-group">
            <h4 class="pd-specs-group-title" style="border-bottom-color:{{ $color }}">{{ $specGroup['group'] }}</h4>
            @foreach($specGroup['rows'] as $row)
            <div class="pd-spec-row">
                <span class="pd-spec-key">{{ $row['key'] }}</span>
                <span class="pd-spec-val">{{ $row['val'] }}</span>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>
@endif

{{-- ── RELATED PRODUCTS ─────────────────────────────────────────── --}}
@if(!empty($related))
<div class="section">
    <div class="section-label" style="color:{{ $color }}">
        <i data-lucide="grid-3x3" style="width:12px;height:12px"></i> Also from OPES
    </div>
    <h2 class="section-title">Systems that work alongside {{ $product['name'] }}</h2>
    <div class="pd-related-grid">
        @foreach($related as $rel)
        <a href="{{ url($locale.'/products/'.$rel['slug']) }}" class="pd-related-card">
            <div class="pd-related-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="{{ $rel['icon'] }}" style="width:22px;height:22px;color:{{ $rel['color'] }}"></i>
            </div>
            <h4>{{ $rel['name'] }}</h4>
            <p>{{ $rel['subtitle'] }}</p>
            <div class="pd-related-link">
                View {{ $rel['name'] }}
                <i data-lucide="arrow-right" style="width:13px;height:13px"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── DEMO CTA ─────────────────────────────────────────────────── --}}
<div class="demo-section" style="margin-top:0">
    <div class="section-label" style="justify-content:center;color:{{ $color }};margin-bottom:12px">
        <i data-lucide="calendar-check" style="width:13px;height:13px"></i>
        Book a Demo
    </div>
    <h2>See {{ $product['name'] }} in your facility</h2>
    <p>Book a free 45-minute demo tailored to your facility type. We'll show you exactly how {{ $product['name'] }} would work in your context.</p>
    <form action="{{ url($locale.'/contact') }}" method="POST" class="demo-form">
        @csrf
        <input type="hidden" name="product" value="{{ $product['slug'] }}">
        <input class="demo-input" type="text" name="name" placeholder="Your name">
        <input class="demo-input" type="email" name="email" placeholder="Email address">
        <select class="demo-select" name="facility_type">
            <option value="">Facility type…</option>
            <option>Clinic</option>
            <option>Hospital</option>
            <option>Laboratory</option>
            <option>Pharmacy</option>
            <option>Government / NGO</option>
        </select>
        <button type="submit" class="demo-btn">
            Book Free Demo for {{ $product['name'] }}
            <i data-lucide="send" style="width:15px;height:15px"></i>
        </button>
    </form>
</div>

</x-layouts.app>
