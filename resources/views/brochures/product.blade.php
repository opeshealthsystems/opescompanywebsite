<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ $product['name'] }} — Product Brochure — OPES Health Systems</title>
<style>
/* ── Page setup ──────────────────────────────────────────────────── */
@page {
    margin-top: 140px;
    margin-bottom: 90px;
    margin-left: 50px;
    margin-right: 50px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px;
    color: #1e293b;
    background: #ffffff;
    line-height: 1.5;
}

/* ── Fixed letterhead (every page) ──────────────────────────────── */
#brochure-header {
    position: fixed;
    top: -140px;
    left: 0; right: 0;
    height: 140px;
}
.lh-accent-bar {
    height: 5px;
    background: {{ $product['color'] }};
    width: 100%;
}
.lh-inner {
    display: table;
    width: 100%;
    padding: 12px 0 8px;
    border-bottom: 1px solid #e2e8f0;
}
.lh-left {
    display: table-cell;
    vertical-align: middle;
    width: 60%;
}
.lh-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    width: 40%;
}
.lh-brand {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.3px;
}
.lh-brand span {
    color: {{ $product['color'] }};
}
.lh-brand-sub {
    font-size: 9px;
    color: #64748b;
    margin-top: 2px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.lh-doc-label {
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #94a3b8;
}
.lh-doc-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 2px;
}
.lh-contact-row {
    font-size: 8px;
    color: #94a3b8;
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}

/* ── Fixed footer (every page) ───────────────────────────────────── */
#brochure-footer {
    position: fixed;
    bottom: -90px;
    left: 0; right: 0;
    height: 90px;
    border-top: 2px solid {{ $product['color'] }};
    padding-top: 8px;
}
.ft-contact {
    font-size: 8.5px;
    color: #475569;
    text-align: center;
    padding: 0 0 5px;
}
.ft-contact strong {
    color: #0f172a;
}
.ft-sep {
    color: #cbd5e1;
    margin: 0 6px;
}
.ft-proprietary {
    font-size: 7.5px;
    color: #94a3b8;
    text-align: center;
    padding: 5px 0 0;
    border-top: 1px solid #f1f5f9;
    font-style: italic;
}

/* ── Page counter ────────────────────────────────────────────────── */
#page-number {
    position: fixed;
    bottom: -90px;
    right: 0;
    font-size: 7.5px;
    color: #94a3b8;
}
.page-number::after {
    content: counter(page);
}

/* ── Content ─────────────────────────────────────────────────────── */
#brochure-content {
    width: 100%;
}

/* Hero section */
.hero-table {
    display: table;
    width: 100%;
    margin-bottom: 22px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #f8fafc;
}
.hero-main {
    display: table-cell;
    vertical-align: top;
    padding: 20px 22px;
    width: 65%;
}
.hero-stats {
    display: table-cell;
    vertical-align: top;
    padding: 20px 18px;
    width: 35%;
    background: #0f172a;
    border-radius: 0 3px 3px 0;
}
.hero-category {
    display: inline-block;
    font-size: 7.5px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: {{ $product['color'] }};
    background: {{ $product['color'] }}18;
    padding: 3px 8px;
    border-radius: 10px;
    margin-bottom: 10px;
    border: 1px solid {{ $product['color'] }}30;
}
.hero-name {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 4px;
}
.hero-subtitle {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 12px;
}
.hero-tagline {
    font-size: 11.5px;
    color: #334155;
    line-height: 1.6;
    font-style: italic;
    border-left: 3px solid {{ $product['color'] }};
    padding-left: 10px;
    margin-bottom: 14px;
}
.hero-desc {
    font-size: 10px;
    color: #475569;
    line-height: 1.65;
}
.stats-title {
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 10px;
}
.stat-row {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #1e293b;
}
.stat-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
.stat-val {
    font-size: 16px;
    font-weight: 700;
    color: {{ $product['color'] }};
    line-height: 1;
}
.stat-lbl {
    font-size: 8.5px;
    color: #94a3b8;
    margin-top: 1px;
}

/* Section headings */
.section-heading {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    padding-bottom: 6px;
    border-bottom: 2px solid {{ $product['color'] }};
    margin-bottom: 14px;
    margin-top: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Modules grid (2-column table) */
.modules-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    margin-bottom: 6px;
}
.module-cell {
    vertical-align: top;
    width: 50%;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    border-top: 3px solid {{ $product['color'] }};
}
.module-name {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}
.module-desc {
    font-size: 9.5px;
    color: #64748b;
    margin-bottom: 8px;
    line-height: 1.5;
}
.module-features {
    margin: 0;
    padding: 0;
    list-style: none;
}
.module-features li {
    font-size: 9px;
    color: #475569;
    padding: 1.5px 0;
}
.module-features li::before {
    content: '▸ ';
    color: {{ $product['color'] }};
    font-size: 8px;
}

/* Benefits grid (2-column table) */
.benefits-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    margin-bottom: 6px;
}
.benefit-cell {
    vertical-align: top;
    width: 50%;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}
.benefit-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}
.benefit-desc {
    font-size: 9.5px;
    color: #64748b;
    line-height: 1.5;
}

/* Specs table */
.spec-group {
    margin-bottom: 14px;
}
.spec-group-title {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: #64748b;
    background: #f1f5f9;
    padding: 5px 10px;
    margin-bottom: 0;
}
.spec-table {
    width: 100%;
    border-collapse: collapse;
}
.spec-table td {
    padding: 5px 10px;
    font-size: 10px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}
.spec-key {
    color: #64748b;
    width: 38%;
    font-weight: 600;
}
.spec-val {
    color: #1e293b;
}

/* Target users */
.target-table {
    width: 100%;
    border-collapse: collapse;
}
.target-table td {
    padding: 5px 10px;
    font-size: 10px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}
.target-table td::before {
    content: '✓ ';
    color: {{ $product['color'] }};
    font-weight: 700;
}

/* Workflow strip */
.workflow-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 6px;
    background: #0f172a;
    border-radius: 4px;
}
.workflow-cell {
    text-align: center;
    padding: 10px 8px;
    border-right: 1px solid #1e293b;
    vertical-align: middle;
}
.workflow-cell:last-child {
    border-right: none;
}
.wf-step {
    font-size: 7.5px;
    color: {{ $product['color'] }};
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.wf-desc {
    font-size: 9px;
    color: #94a3b8;
    margin-top: 2px;
}
.wf-arrow {
    padding: 0 2px;
    color: #334155;
    font-size: 12px;
    vertical-align: middle;
}

/* CTA block */
.cta-block {
    background: {{ $product['color'] }}12;
    border: 2px solid {{ $product['color'] }}30;
    border-radius: 4px;
    padding: 16px 20px;
    margin-top: 20px;
    text-align: center;
}
.cta-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.cta-sub {
    font-size: 10px;
    color: #475569;
    margin-bottom: 10px;
    line-height: 1.5;
}
.cta-contact-row {
    display: table;
    width: 100%;
}
.cta-contact-cell {
    display: table-cell;
    text-align: center;
    padding: 6px 8px;
}
.cta-contact-label {
    font-size: 7.5px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: block;
    margin-bottom: 2px;
}
.cta-contact-value {
    font-size: 10px;
    font-weight: 700;
    color: #0f172a;
}
.cta-contact-value a {
    color: {{ $product['color'] }};
    text-decoration: none;
}

/* Integrations row */
.integrations-row {
    margin-top: 4px;
    font-size: 9.5px;
    color: #64748b;
}
.integration-badge {
    display: inline-block;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 2px 8px;
    margin: 2px 2px;
    font-size: 8.5px;
    color: #475569;
}

/* Proprietary watermark-style notice */
.confidential-notice {
    border: 1px solid #fcd34d;
    background: #fffbeb;
    padding: 6px 10px;
    margin-bottom: 16px;
    border-radius: 3px;
    font-size: 8.5px;
    color: #92400e;
}

/* Page break helpers */
.pb-before { page-break-before: always; }
.avoid-break { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- ── FIXED LETTERHEAD (repeats every page) ──────────────────── --}}
<div id="brochure-header">
    <div class="lh-accent-bar"></div>
    <div class="lh-inner">
        <div class="lh-left">
            <div class="lh-brand"><span>OPES</span> Health Systems SARL</div>
            <div class="lh-brand-sub">{{ $company['tagline'] }}</div>
        </div>
        <div class="lh-right">
            <div class="lh-doc-label">Product Brochure</div>
            <div class="lh-doc-name">{{ $product['name'] }}</div>
        </div>
    </div>
    <div class="lh-contact-row">
        {{ $company['website'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['email'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['phone'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['address'] }}
    </div>
</div>

{{-- ── FIXED FOOTER (repeats every page) ──────────────────────── --}}
<div id="brochure-footer">
    <div class="ft-contact">
        <strong>OPES Health Systems SARL</strong>
        <span class="ft-sep">|</span>
        {{ $company['website'] }}
        <span class="ft-sep">|</span>
        {{ $company['email'] }}
        <span class="ft-sep">|</span>
        {{ $company['phone'] }}
        <span class="ft-sep">|</span>
        {{ $company['address'] }}
    </div>
    <div class="ft-proprietary">
        PROPRIETARY &amp; CONFIDENTIAL — This document is the exclusive property of OPES Health Systems SARL.
        Unauthorized reproduction, distribution, or disclosure is strictly prohibited.
        &nbsp;&nbsp;|&nbsp;&nbsp;
        {{ $company['copyright'] }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        {{ $company['legal'] }}
    </div>
</div>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────── --}}
<div id="brochure-content">

    {{-- PROPRIETARY NOTICE --}}
    <div class="confidential-notice">
        &#9888;&nbsp; PROPRIETARY DOCUMENT — OPES Health Systems SARL &nbsp;·&nbsp; {{ $product['name'] }} Product Brochure &nbsp;·&nbsp; For authorised distribution only
    </div>

    {{-- HERO --}}
    <div class="hero-table avoid-break">
        <div class="hero-main">
            <div class="hero-category">{{ $product['category'] }}</div>
            <div class="hero-name">{{ $product['name'] }}</div>
            <div class="hero-subtitle">{{ $product['subtitle'] }}</div>
            <div class="hero-tagline">{{ $product['tagline'] }}</div>
            <div class="hero-desc">{{ $product['description'] }}</div>
        </div>
        <div class="hero-stats">
            <div class="stats-title">At a Glance</div>
            @foreach($product['stats'] ?? [] as $stat)
            <div class="stat-row">
                <div class="stat-val">{{ $stat['value'] }}</div>
                <div class="stat-lbl">{{ $stat['label'] }}</div>
            </div>
            @endforeach
            <div class="stat-row">
                <div class="stat-val" style="color:#00C896">EN/FR</div>
                <div class="stat-lbl">fully bilingual</div>
            </div>
            <div class="stat-row">
                <div class="stat-val" style="color:#1A6FE8">CEMAC</div>
                <div class="stat-lbl">regional coverage</div>
            </div>
        </div>
    </div>

    @if(!empty($product['description2']))
    <p style="font-size:10px;color:#475569;line-height:1.65;margin-bottom:16px;">{{ $product['description2'] }}</p>
    @endif

    {{-- WORKFLOW STRIP --}}
    @if(!empty($product['workflow']))
    <div class="section-heading">How It Works</div>
    <table class="workflow-table avoid-break">
        <tr>
            @foreach($product['workflow'] as $i => $step)
            @if($i > 0)
            <td class="wf-arrow">›</td>
            @endif
            <td class="workflow-cell">
                <div class="wf-step">{{ $step['step'] }}</div>
                <div class="wf-desc">{{ $step['desc'] }}</div>
            </td>
            @endforeach
        </tr>
    </table>
    @endif

    {{-- KEY MODULES --}}
    @if(!empty($product['modules']))
    <div class="section-heading">Key Modules &amp; Features</div>
    @php $modules = $product['modules']; @endphp
    @for($i = 0; $i < count($modules); $i += 2)
    <table class="modules-table avoid-break">
        <tr>
            <td class="module-cell">
                <div class="module-name">{{ $modules[$i]['name'] }}</div>
                <div class="module-desc">{{ $modules[$i]['desc'] }}</div>
                <ul class="module-features">
                    @foreach($modules[$i]['features'] ?? [] as $f)
                    <li>{{ $f }}</li>
                    @endforeach
                </ul>
            </td>
            @if(isset($modules[$i+1]))
            <td class="module-cell">
                <div class="module-name">{{ $modules[$i+1]['name'] }}</div>
                <div class="module-desc">{{ $modules[$i+1]['desc'] }}</div>
                <ul class="module-features">
                    @foreach($modules[$i+1]['features'] ?? [] as $f)
                    <li>{{ $f }}</li>
                    @endforeach
                </ul>
            </td>
            @else
            <td style="width:50%"></td>
            @endif
        </tr>
    </table>
    @endfor
    @endif

    {{-- PROBLEMS SOLVED --}}
    @if(!empty($product['problems_solved']))
    <div class="section-heading">Problems We Solve</div>
    <table style="width:100%;border-collapse:collapse;margin-bottom:6px">
        @foreach($product['problems_solved'] as $prob)
        <tr class="avoid-break">
            <td style="padding:6px 10px;border-bottom:1px solid #f1f5f9;vertical-align:top;width:32%">
                <span style="font-size:10px;font-weight:700;color:#0f172a">{{ $prob['title'] }}</span>
            </td>
            <td style="padding:6px 10px;border-bottom:1px solid #f1f5f9;vertical-align:top;font-size:9.5px;color:#475569">
                {{ $prob['desc'] }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- PAGE 2: BENEFITS + SPECS + TARGET USERS + CTA --}}
    <div class="pb-before"></div>

    {{-- BENEFITS --}}
    @if(!empty($product['benefits']))
    <div class="section-heading">Key Benefits</div>
    @php $benefits = $product['benefits']; @endphp
    @for($i = 0; $i < count($benefits); $i += 2)
    <table class="benefits-table avoid-break">
        <tr>
            <td class="benefit-cell">
                <div class="benefit-title" style="color:{{ $benefits[$i]['color'] ?? '#0f172a' }}">&#9670;&nbsp; {{ $benefits[$i]['title'] }}</div>
                <div class="benefit-desc">{{ $benefits[$i]['desc'] }}</div>
            </td>
            @if(isset($benefits[$i+1]))
            <td class="benefit-cell">
                <div class="benefit-title" style="color:{{ $benefits[$i+1]['color'] ?? '#0f172a' }}">&#9670;&nbsp; {{ $benefits[$i+1]['title'] }}</div>
                <div class="benefit-desc">{{ $benefits[$i+1]['desc'] }}</div>
            </td>
            @else
            <td style="width:50%"></td>
            @endif
        </tr>
    </table>
    @endfor
    @endif

    {{-- TECHNICAL SPECIFICATIONS --}}
    @if(!empty($product['specs']))
    <div class="section-heading">Technical Specifications</div>
    @foreach($product['specs'] as $spec)
    <div class="spec-group avoid-break">
        <div class="spec-group-title">{{ $spec['group'] }}</div>
        <table class="spec-table">
            @foreach($spec['rows'] as $row)
            <tr>
                <td class="spec-key">{{ $row['key'] }}</td>
                <td class="spec-val">{{ $row['val'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach
    @endif

    {{-- TARGET USERS --}}
    @if(!empty($product['target_users']))
    <div class="section-heading">Designed For</div>
    <table class="target-table avoid-break">
        @foreach($product['target_users'] as $user)
        <tr>
            <td>{{ $user }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- INTEGRATIONS --}}
    @if(!empty($product['integrations']))
    <div class="section-heading">Integrates With</div>
    <div class="integrations-row avoid-break">
        @foreach($product['integrations'] as $intSlug)
        @php
            $allProducts = array_merge(config('products', []), config('products_specialist', []));
            $intName = $allProducts[$intSlug]['name'] ?? strtoupper($intSlug);
        @endphp
        <span class="integration-badge">{{ $intName }}</span>
        @endforeach
        <span class="integration-badge" style="background:#e0f2fe;border-color:#7dd3fc;color:#0369a1">+ All 22 OPES Systems</span>
    </div>
    @endif

    {{-- CTA --}}
    <div class="cta-block avoid-break">
        <div class="cta-title">Ready to transform your facility with {{ $product['name'] }}?</div>
        <div class="cta-sub">
            Contact our team to schedule a live demonstration, request a pilot deployment,
            or get a custom quote tailored to your facility's needs.
        </div>
        <div class="cta-contact-row">
            <div class="cta-contact-cell">
                <span class="cta-contact-label">Website</span>
                <span class="cta-contact-value"><a href="{{ $company['website_full'] }}">{{ $company['website'] }}</a></span>
            </div>
            <div class="cta-contact-cell">
                <span class="cta-contact-label">Email</span>
                <span class="cta-contact-value"><a href="mailto:{{ $company['email'] }}">{{ $company['email'] }}</a></span>
            </div>
            <div class="cta-contact-cell">
                <span class="cta-contact-label">Phone</span>
                <span class="cta-contact-value">{{ $company['phone'] }}</span>
            </div>
            <div class="cta-contact-cell">
                <span class="cta-contact-label">Address</span>
                <span class="cta-contact-value">{{ $company['address'] }}</span>
            </div>
        </div>
    </div>

</div>{{-- /#brochure-content --}}
</body>
</html>
