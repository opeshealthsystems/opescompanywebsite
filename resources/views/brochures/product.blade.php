@php
    // ── OPES brand palette — validated mockup: teal · gold · navy ──
    $teal      = '#007A87';
    $tealLight = '#009DAD';
    $tealDark  = '#005F6B';
    $gold      = '#C8962E';
    $goldLight = '#E8B84B';
    $navy      = '#0F2B4C';
    $navyLight = '#1A3D6B';
    $ink       = '#1A2332';
    $muted     = '#6B7A8D';
    $light     = '#94A3B8';
    $border    = '#E2E8F0';
    $borderSm  = '#F1F5F9';
    $soft      = '#F8FAFB';
    $white     = '#FFFFFF';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ $product['name'] }} — Product Brochure — OPES Health Systems</title>
<style>
/* ── Page setup ─────────────────────────────────────────────── */
@page { margin-top: 130px; margin-bottom: 84px; margin-left: 44px; margin-right: 44px; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px; color: {{ $ink }}; background: {{ $white }}; line-height: 1.55;
}

/* ── Fixed letterhead ────────────────────────────────────────── */
#brochure-header { position: fixed; top: -130px; left: 0; right: 0; height: 130px; }
.lh-teal-bar  { height: 5px; background: {{ $teal }}; }
.lh-gold-bar  { height: 2px; background: {{ $gold }}; }
.lh-row { display: table; width: 100%; padding: 12px 0 8px; border-bottom: 1px solid {{ $border }}; }
.lh-left  { display: table-cell; vertical-align: middle; width: 58%; }
.lh-right { display: table-cell; vertical-align: middle; text-align: right; width: 42%; }
.lh-logobox { display: inline-block; width: 26px; height: 26px; background: {{ $teal }}; border-radius: 6px; text-align: center; vertical-align: middle; }
.lh-logobox span { color: #fff; font-size: 14px; font-weight: 700; line-height: 26px; }
.lh-brand { display: inline-block; vertical-align: middle; margin-left: 8px; }
.lh-name  { font-size: 15px; font-weight: 700; color: {{ $navy }}; }
.lh-name b { color: {{ $teal }}; }
.lh-sub   { font-size: 8px; color: {{ $muted }}; letter-spacing: 0.4px; text-transform: uppercase; }
.lh-doclabel { font-size: 8px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: {{ $gold }}; }
.lh-docname  { font-size: 13px; font-weight: 700; color: {{ $navy }}; margin-top: 2px; }
.lh-contact  { font-size: 8px; color: {{ $light }}; margin-top: 6px; padding-top: 6px; border-top: 1px solid {{ $borderSm }}; }

/* ── Fixed footer ────────────────────────────────────────────── */
#brochure-footer { position: fixed; bottom: -84px; left: 0; right: 0; height: 84px; border-top: 2px solid {{ $teal }}; padding-top: 7px; }
.ft-line1 { font-size: 8.5px; color: {{ $muted }}; text-align: center; margin-bottom: 4px; }
.ft-line1 strong { color: {{ $navy }}; }
.ft-sep { color: {{ $border }}; margin: 0 5px; }
.ft-line2 { font-size: 7.5px; color: {{ $light }}; text-align: center; border-top: 1px solid {{ $borderSm }}; padding-top: 4px; font-style: italic; }

/* ── Content wrapper ─────────────────────────────────────────── */
#content { width: 100%; }

/* ── Confidential notice ─────────────────────────────────────── */
.conf-notice {
    border: 1px solid {{ $goldLight }}; background: #FEF9EE;
    padding: 6px 11px; margin-bottom: 14px; border-radius: 4px;
    font-size: 8.5px; color: #7A5A10;
}

/* ── HERO ────────────────────────────────────────────────────── */
.hero-wrap { display: table; width: 100%; border-radius: 8px; overflow: hidden;
             background: {{ $navy }}; margin-bottom: 20px; }
.hero-main { display: table-cell; vertical-align: top; padding: 22px 24px; width: 62%; }
.hero-side { display: table-cell; vertical-align: top; padding: 20px 18px; width: 38%;
             background: {{ $navyLight }}; border-radius: 0 8px 8px 0; }
.hero-badge { display: inline-block; font-size: 7.5px; font-weight: 700; letter-spacing: 1.2px;
              text-transform: uppercase; color: #5CD4E0; background: rgba(0,157,173,0.2);
              border: 1px solid rgba(0,157,173,0.35); padding: 3px 10px; border-radius: 12px; margin-bottom: 12px; }
.hero-h1    { font-size: 26px; font-weight: 700; color: #fff; line-height: 1.1; margin-bottom: 5px; }
.hero-h1 b  { color: {{ $goldLight }}; }
.hero-sub   { font-size: 10px; color: {{ $goldLight }}; font-weight: 700; margin-bottom: 10px; }
.hero-tag   { font-size: 10.5px; color: rgba(255,255,255,0.78); line-height: 1.6;
              border-left: 3px solid {{ $gold }}; padding-left: 10px; margin-bottom: 10px; }
.hero-desc  { font-size: 9.5px; color: rgba(255,255,255,0.65); line-height: 1.6; }
.stats-ttl  { font-size: 7.5px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
              color: rgba(255,255,255,0.4); margin-bottom: 11px; }
.stat-item  { padding: 9px 10px; background: rgba(255,255,255,0.06); border-radius: 7px; margin-bottom: 6px; }
.stat-item:last-child { margin-bottom: 0; }
.stat-val   { font-size: 16px; font-weight: 700; color: #fff; line-height: 1; }
.stat-lbl   { font-size: 8px; color: rgba(255,255,255,0.5); margin-top: 2px; }

/* ── Section layout helpers ──────────────────────────────────── */
.sec        { padding: 18px 0 14px; }
.sec-alt    { background: {{ $soft }}; padding: 18px 12px 14px; border-radius: 6px; margin: 0 -12px 16px; }
.sec-label  { font-size: 8px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
              color: {{ $teal }}; margin-bottom: 4px; }
.sec-title  { font-size: 15px; font-weight: 700; color: {{ $navy }}; margin-bottom: 8px; }
.sec-sub    { font-size: 10px; color: {{ $muted }}; line-height: 1.65; margin-bottom: 14px; }

/* ── OVERVIEW ─────────────────────────────────────────────────── */
.ov-table { display: table; width: 100%; border-spacing: 0; }
.ov-text  { display: table-cell; vertical-align: top; width: 57%; padding-right: 20px; }
.ov-box   { display: table-cell; vertical-align: top; width: 43%; }
.ov-para  { font-size: 9.5px; color: {{ $muted }}; line-height: 1.75; margin-bottom: 10px; }
.who-sub  { font-size: 10px; font-weight: 700; color: {{ $navy }}; margin: 12px 0 7px; }
.who-table { width: 100%; border-collapse: collapse; }
.who-table td { padding: 5px 0; font-size: 9.5px; color: {{ $muted }};
                border-bottom: 1px solid {{ $borderSm }}; vertical-align: top; }
.who-table tr:last-child td { border-bottom: none; }
.who-chk  { width: 18px; vertical-align: top; }
.who-chkmark { display: inline-block; width: 16px; height: 16px; background: rgba(0,122,135,0.1);
               border-radius: 8px; text-align: center; line-height: 16px; font-size: 9px;
               color: {{ $teal }}; font-weight: 700; }

/* Problems box — matches mockup exactly */
.prb-box   { background: {{ $soft }}; border: 1px solid {{ $border }}; border-radius: 8px; padding: 16px; }
.prb-h4    { font-size: 11px; font-weight: 700; color: {{ $navy }}; margin-bottom: 12px; }
.prb-item  { display: table; width: 100%; padding: 9px 0; border-bottom: 1px solid {{ $borderSm }}; }
.prb-item:last-child { border-bottom: none; }
.prb-num   { display: table-cell; width: 26px; vertical-align: top; }
.prb-circ  { display: inline-block; width: 20px; height: 20px; background: {{ $teal }};
             border-radius: 10px; color: #fff; font-size: 9px; font-weight: 700;
             text-align: center; line-height: 20px; }
.prb-body  { display: table-cell; vertical-align: top; }
.prb-title { font-size: 9.5px; font-weight: 700; color: {{ $navy }}; margin-bottom: 2px; }
.prb-text  { font-size: 9px; color: {{ $muted }}; line-height: 1.5; }

/* ── MODULES — white cards, plain 1px border, NO top stripe ─── */
.mod-table { width: 100%; border-collapse: separate; border-spacing: 6px; }
.mod-cell  { vertical-align: top; width: 33%; padding: 13px 14px;
             background: {{ $white }}; border: 1px solid {{ $border }}; border-radius: 8px; }
.mod-h4    { font-size: 11px; font-weight: 700; color: {{ $navy }}; margin-bottom: 4px; }
.mod-desc  { font-size: 9px; color: {{ $muted }}; line-height: 1.55; margin-bottom: 9px; }
.mod-feats { list-style: none; padding: 0; margin: 0; }
.mod-feats li { font-size: 9px; color: {{ $muted }}; padding: 2px 0;
                display: table; width: 100%; }
.mod-feats li::before { content: ''; display: table-cell; width: 8px; }
.mod-dot   { display: inline-block; width: 4px; height: 4px; background: {{ $teal }};
             border-radius: 2px; margin-right: 5px; vertical-align: middle; }

/* ── WORKFLOW — white background, circular step nodes ─────────── */
.wf-outer  { background: {{ $white }}; }
.wf-table  { width: 100%; border-collapse: collapse; }
.wf-cell   { text-align: center; vertical-align: top; padding: 6px 3px; }
.wf-line   { vertical-align: middle; width: 16px; }
.wf-line-inner { height: 2px; background: {{ $teal }}; }
.wf-node-wrap { text-align: center; margin-bottom: 7px; position: relative; }
.wf-node   { display: inline-block; width: 46px; height: 46px; border-radius: 23px;
             text-align: center; line-height: 46px; font-size: 16px; font-weight: 700; }
.wf-node-start { background: {{ $teal }}; color: #fff; }
.wf-node-mid   { background: {{ $white }}; border: 3px solid {{ $teal }}; color: {{ $teal }}; line-height: 40px; }
.wf-node-end   { background: {{ $navy }}; color: #fff; }
.wf-steplbl { font-size: 9px; font-weight: 700; color: {{ $navy }}; margin-bottom: 2px; }
.wf-stepdsc { font-size: 8px; color: {{ $muted }}; line-height: 1.35; max-width: 80px; margin: 0 auto; }
.wf-num-badge { font-size: 8px; font-weight: 700; color: {{ $navy }}; }

/* Workflow detail cards — matches mockup .wf-card */
.wfcard-table { width: 100%; border-collapse: separate; border-spacing: 7px; margin-top: 14px; }
.wfcard { vertical-align: top; width: 50%; padding: 14px 16px;
          background: {{ $white }}; border: 1px solid {{ $border }};
          border-left: 4px solid {{ $teal }}; border-radius: 7px; }
.wfcard-h5 { font-size: 10px; font-weight: 700; color: {{ $navy }}; margin-bottom: 6px; }
.wfcard-p  { font-size: 9.5px; color: {{ $muted }}; line-height: 1.6; }

/* ── BENEFITS — centered layout with colored icon areas ────────── */
.ben-table { width: 100%; border-collapse: separate; border-spacing: 8px; }
.ben-cell  { vertical-align: top; width: 25%; text-align: center; padding: 16px 10px; }
.ben-icon  { display: inline-block; width: 46px; height: 46px; border-radius: 11px;
             text-align: center; line-height: 46px; font-size: 20px; margin-bottom: 10px; }
.ben-h4    { font-size: 10.5px; font-weight: 700; color: {{ $navy }}; margin-bottom: 5px; }
.ben-p     { font-size: 9px; color: {{ $muted }}; line-height: 1.55; }

/* ── INTEGRATIONS ─────────────────────────────────────────────── */
.eco-hub-row { text-align: center; margin-bottom: 14px; }
.eco-hub-box { display: inline-block; width: 56px; height: 56px; border-radius: 14px;
               background: {{ $teal }}; text-align: center; line-height: 56px;
               font-size: 22px; color: #fff; font-weight: 700; margin-bottom: 4px; }
.eco-hub-lbl { font-size: 9px; font-weight: 700; color: {{ $navy }}; }
.spokes-wrap { text-align: center; margin-bottom: 14px; }
.spoke-badge { display: inline-block; background: {{ $white }}; border: 1.5px solid {{ $border }};
               border-radius: 20px; padding: 4px 11px; margin: 3px 2px;
               font-size: 8.5px; font-weight: 600; color: {{ $navy }}; }
.int-table { width: 100%; border-collapse: separate; border-spacing: 7px; }
.int-cell  { vertical-align: top; width: 33%; padding: 14px 16px;
             background: {{ $white }}; border: 1px solid {{ $border }}; border-radius: 8px; }
.int-h5    { font-size: 10px; font-weight: 700; color: {{ $navy }}; margin-bottom: 6px; }
.int-p     { font-size: 9.5px; color: {{ $muted }}; line-height: 1.55; }

/* ── SPECS — white groups, teal h4 underline (matches mockup) ─── */
.specs-table { width: 100%; border-collapse: separate; border-spacing: 10px; }
.specs-grp   { vertical-align: top; width: 50%; }
.specs-h4    { font-size: 11px; font-weight: 700; color: {{ $navy }};
               margin-bottom: 10px; padding-bottom: 8px;
               border-bottom: 2px solid {{ $teal }}; }
.spec-row    { display: table; width: 100%; padding: 7px 0;
               border-bottom: 1px solid {{ $borderSm }}; font-size: 9.5px; }
.spec-row:last-child { border-bottom: none; }
.spec-key   { display: table-cell; color: {{ $muted }}; width: 55%; }
.spec-val   { display: table-cell; color: {{ $navy }}; font-weight: 600; text-align: right; }

/* ── CTA — navy background ───────────────────────────────────── */
.cta-block { background: {{ $navy }}; border-radius: 8px; padding: 20px 24px; margin-top: 20px; }
.cta-goldbar { height: 3px; background: {{ $gold }}; border-radius: 8px 8px 0 0; margin: -20px -24px 16px; }
.cta-h2    { font-size: 16px; font-weight: 700; color: #fff; text-align: center; margin-bottom: 6px; }
.cta-h2 b  { color: {{ $goldLight }}; }
.cta-p     { font-size: 9.5px; color: rgba(255,255,255,0.65); text-align: center;
             line-height: 1.6; margin-bottom: 16px; }
.cta-row   { display: table; width: 100%; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 14px; }
.cta-cell  { display: table-cell; text-align: center; padding: 0 6px; width: 25%; }
.cta-lbl   { font-size: 7px; color: {{ $tealLight }}; text-transform: uppercase;
             letter-spacing: 0.8px; display: block; margin-bottom: 3px; }
.cta-val   { font-size: 9px; font-weight: 700; color: #fff; }
.cta-val a { color: #fff; text-decoration: none; }

/* ── Helpers ──────────────────────────────────────────────────── */
.pb-before   { page-break-before: always; }
.avoid-break { page-break-inside: avoid; }
.divider     { height: 1px; background: {{ $border }}; margin: 16px 0; }
</style>
</head>
<body>

{{-- ══ FIXED LETTERHEAD ══════════════════════════════════════════ --}}
<div id="brochure-header">
    <div class="lh-teal-bar"></div>
    <div class="lh-gold-bar"></div>
    <div class="lh-row">
        <div class="lh-left">
            <span class="lh-logobox"><span>O</span></span>
            <span class="lh-brand">
                <div class="lh-name"><b>OPES</b> Health Systems SARL</div>
                <div class="lh-sub">{{ $company['tagline'] }}</div>
            </span>
        </div>
        <div class="lh-right">
            <div class="lh-doclabel">Product Brochure</div>
            <div class="lh-docname">{{ $product['name'] }}</div>
        </div>
    </div>
    <div class="lh-contact">
        {{ $company['website'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['email'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['phone'] }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $company['address'] }}
    </div>
</div>

{{-- ══ FIXED FOOTER ══════════════════════════════════════════════ --}}
<div id="brochure-footer">
    <div class="ft-line1">
        <strong>OPES Health Systems SARL</strong>
        <span class="ft-sep">|</span> {{ $company['website'] }}
        <span class="ft-sep">|</span> {{ $company['email'] }}
        <span class="ft-sep">|</span> {{ $company['phone'] }}
    </div>
    <div class="ft-line2">
        PROPRIETARY &amp; CONFIDENTIAL — This document is the exclusive property of OPES Health Systems SARL.
        Unauthorized reproduction, distribution, or disclosure is strictly prohibited.
        &nbsp;|&nbsp; {{ $company['copyright'] }} &nbsp;|&nbsp; {{ $company['legal'] }}
    </div>
</div>

{{-- ══ MAIN CONTENT ═══════════════════════════════════════════════ --}}
<div id="content">

    {{-- Confidential notice --}}
    <div class="conf-notice avoid-break">
        ⚠&nbsp; PROPRIETARY DOCUMENT — OPES Health Systems SARL &nbsp;·&nbsp; {{ $product['name'] }} Product Brochure &nbsp;·&nbsp; For authorised distribution only
    </div>

    {{-- ── HERO ─────────────────────────────────────────────── --}}
    <div class="hero-wrap avoid-break">
        <div class="hero-main">
            <div class="hero-badge">{{ $product['category'] }}&nbsp;&nbsp;·&nbsp;&nbsp;Core Platform</div>
            <div class="hero-h1">{{ $product['name'] }}</div>
            @if(!empty($product['subtitle']))
            <div class="hero-sub">{{ $product['subtitle'] }}</div>
            @endif
            <div class="hero-tag">{{ $product['tagline'] }}</div>
            <div class="hero-desc">{{ $product['description'] }}</div>
        </div>
        <div class="hero-side">
            <div class="stats-ttl">At a glance</div>
            @foreach($product['stats'] ?? [] as $stat)
            <div class="stat-item">
                <div class="stat-val">{{ $stat['value'] }}</div>
                <div class="stat-lbl">{{ $stat['label'] }}</div>
            </div>
            @endforeach
            <div class="stat-item">
                <div class="stat-val">EN / FR</div>
                <div class="stat-lbl">Fully bilingual interface</div>
            </div>
            <div class="stat-item">
                <div class="stat-val">HL7 FHIR</div>
                <div class="stat-lbl">Interoperability standard</div>
            </div>
            <div class="stat-item">
                <div class="stat-val">22</div>
                <div class="stat-lbl">OPES systems it connects to</div>
            </div>
        </div>
    </div>

    {{-- ── OVERVIEW ─────────────────────────────────────────── --}}
    <div class="sec avoid-break">
        <div class="sec-label">Overview</div>
        <div class="sec-title">Built for African healthcare, not adapted for it</div>
        <div class="ov-table">
            <div class="ov-text">
                @if(!empty($product['description2']))
                <p class="ov-para">{{ $product['description2'] }}</p>
                @endif
                @if(!empty($product['target_users']))
                <div class="who-sub">Who is {{ $product['name'] }} for?</div>
                <table class="who-table">
                    @foreach($product['target_users'] as $user)
                    <tr>
                        <td class="who-chk"><span class="who-chkmark">✓</span></td>
                        <td>{{ $user }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </div>
            <div class="ov-box">
                @if(!empty($product['problems_solved']))
                <div class="prb-box">
                    <div class="prb-h4">Problems {{ $product['name'] }} solves</div>
                    @foreach($product['problems_solved'] as $i => $prob)
                    <div class="prb-item">
                        <div class="prb-num"><span class="prb-circ">{{ $i + 1 }}</span></div>
                        <div class="prb-body">
                            <div class="prb-title">{{ $prob['title'] }}</div>
                            <div class="prb-text">{{ $prob['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── MODULES — grey section, plain bordered cards, NO top stripe ── --}}
    @if(!empty($product['modules']))
    <div class="sec-alt avoid-break pb-before">
        <div class="sec-label">Modules</div>
        <div class="sec-title">Everything in one connected system</div>
        <div class="sec-sub">Each module activates independently — start with Registration and Consultation, add Lab, Pharmacy, and Billing as you grow.</div>
        @php $mods = $product['modules']; @endphp
        @for($i = 0; $i < count($mods); $i += 3)
        <table class="mod-table avoid-break">
            <tr>
                @for($j = $i; $j < min($i + 3, count($mods)); $j++)
                <td class="mod-cell">
                    <div class="mod-h4">{{ $mods[$j]['name'] }}</div>
                    <div class="mod-desc">{{ $mods[$j]['desc'] }}</div>
                    <ul class="mod-feats">
                        @foreach($mods[$j]['features'] ?? [] as $f)
                        <li><span class="mod-dot"></span>{{ $f }}</li>
                        @endforeach
                    </ul>
                </td>
                @endfor
                @for($j = count($mods); $j < $i + 3; $j++)
                <td style="width:33%"></td>
                @endfor
            </tr>
        </table>
        @endfor
    </div>
    @endif

    {{-- ── WORKFLOW — white background, circular step nodes ────── --}}
    @if(!empty($product['workflow']))
    <div class="sec avoid-break">
        <div class="sec-label">Patient Workflow</div>
        <div class="sec-title">How {{ $product['name'] }} moves a patient through your facility</div>
        <div class="sec-sub">From the moment a patient arrives to discharge, every step is captured — and every department sees the full picture in real time.</div>
        @php
            $wfSteps = $product['workflow'];
            $wfCount = count($wfSteps);
        @endphp
        <table class="wf-table">
            <tr>
                @foreach($wfSteps as $i => $step)
                @if($i > 0)
                <td class="wf-line"><div class="wf-line-inner"></div></td>
                @endif
                <td class="wf-cell">
                    @php
                        $nodeClass = 'wf-node-mid';
                        if($i === 0) $nodeClass = 'wf-node-start';
                        if($i === $wfCount - 1) $nodeClass = 'wf-node-end';
                    @endphp
                    <div class="wf-node-wrap">
                        <div class="wf-node {{ $nodeClass }}">{{ $i + 1 }}</div>
                    </div>
                    <div class="wf-steplbl">{{ $step['step'] }}</div>
                    <div class="wf-stepdsc">{{ $step['desc'] }}</div>
                </td>
                @endforeach
            </tr>
        </table>

        @if(!empty($product['workflow_details']))
        <table class="wfcard-table avoid-break">
            <tr>
                @foreach(array_slice($product['workflow_details'], 0, 2) as $wfd)
                <td class="wfcard">
                    <div class="wfcard-h5">{{ $wfd['title'] }}</div>
                    <div class="wfcard-p">{{ $wfd['desc'] }}</div>
                </td>
                @endforeach
            </tr>
        </table>
        @if(count($product['workflow_details']) > 2)
        <table class="wfcard-table avoid-break">
            <tr>
                @foreach(array_slice($product['workflow_details'], 2) as $wfd)
                <td class="wfcard">
                    <div class="wfcard-h5">{{ $wfd['title'] }}</div>
                    <div class="wfcard-p">{{ $wfd['desc'] }}</div>
                </td>
                @endforeach
                @if(count($product['workflow_details']) % 2 !== 0)
                <td style="width:50%"></td>
                @endif
            </tr>
        </table>
        @endif
        @endif
    </div>
    @endif

    {{-- ── BENEFITS — grey section, centered 4-col layout ─────── --}}
    @if(!empty($product['benefits']))
    <div class="sec-alt avoid-break">
        <div class="sec-label">Key Benefits</div>
        <div class="sec-title" style="text-align:center">Why facilities choose {{ $product['name'] }}</div>
        @php
            $bens = $product['benefits'];
            $benColors = ['rgba(0,122,135,0.08)', 'rgba(200,150,46,0.08)', 'rgba(15,43,76,0.06)', 'rgba(0,157,173,0.08)'];
            $benSymbols = ['⚡', '⟁', '✦', '◉'];
        @endphp
        <table class="ben-table avoid-break">
            <tr>
                @foreach($bens as $bi => $ben)
                <td class="ben-cell">
                    <div class="ben-icon" style="background:{{ $benColors[$bi % 4] }}">
                        <span style="color:{{ $teal }}">{{ $benSymbols[$bi % 4] }}</span>
                    </div>
                    <div class="ben-h4">{{ $ben['title'] }}</div>
                    <div class="ben-p">{{ $ben['desc'] }}</div>
                </td>
                @endforeach
                @for($bi = count($bens); $bi < 4; $bi++)
                <td style="width:25%"></td>
                @endfor
            </tr>
        </table>
    </div>
    @endif

    {{-- ── INTEGRATIONS ─────────────────────────────────────────── --}}
    @if(!empty($product['integrations']))
    <div class="sec avoid-break pb-before">
        <div class="sec-label">OPES Ecosystem</div>
        <div class="sec-title">{{ $product['name'] }} connects everything</div>
        <div class="sec-sub">{{ $product['name'] }} is the clinical core — every other OPES system feeds into it or reads from it.</div>

        <div class="eco-hub-row">
            <div class="eco-hub-box">O</div>
            <div class="eco-hub-lbl">{{ $product['name'] }} — Clinical Core</div>
        </div>

        <div class="spokes-wrap">
            @foreach($product['integrations'] as $intSlug)
            @php
                $allProducts = array_merge(config('products', []), config('products_specialist', []));
                $intName = $allProducts[$intSlug]['name'] ?? strtoupper($intSlug);
            @endphp
            <span class="spoke-badge">{{ $intName }}</span>
            @endforeach
        </div>

        @if(!empty($product['integration_details']))
        <table class="int-table avoid-break">
            <tr>
                @foreach(array_slice($product['integration_details'], 0, 3) as $intd)
                <td class="int-cell">
                    <div class="int-h5">{{ $intd['name'] }}</div>
                    <div class="int-p">{{ $intd['desc'] }}</div>
                </td>
                @endforeach
            </tr>
        </table>
        @endif
    </div>
    @endif

    {{-- ── TECHNICAL SPECS — grey section, white groups, teal h4 underline ── --}}
    @if(!empty($product['specs']))
    <div class="sec-alt pb-before">
        <div class="sec-label">Technical Specifications</div>
        <div class="sec-title">Built to enterprise standards</div>
        @php $specGroups = $product['specs']; @endphp
        @for($i = 0; $i < count($specGroups); $i += 2)
        <table class="specs-table avoid-break">
            <tr>
                <td class="specs-grp">
                    <div class="specs-h4">{{ $specGroups[$i]['group'] }}</div>
                    @foreach($specGroups[$i]['rows'] as $row)
                    <div class="spec-row">
                        <span class="spec-key">{{ $row['key'] }}</span>
                        <span class="spec-val">{{ $row['val'] }}</span>
                    </div>
                    @endforeach
                </td>
                @if(isset($specGroups[$i+1]))
                <td class="specs-grp">
                    <div class="specs-h4">{{ $specGroups[$i+1]['group'] }}</div>
                    @foreach($specGroups[$i+1]['rows'] as $row)
                    <div class="spec-row">
                        <span class="spec-key">{{ $row['key'] }}</span>
                        <span class="spec-val">{{ $row['val'] }}</span>
                    </div>
                    @endforeach
                </td>
                @else
                <td style="width:50%"></td>
                @endif
            </tr>
        </table>
        @endfor
    </div>
    @endif

    {{-- ── CTA — navy, gold top bar ─────────────────────────────── --}}
    <div class="cta-block avoid-break">
        <div class="cta-goldbar"></div>
        <div class="cta-h2">See <b>{{ $product['name'] }}</b> in your facility</div>
        <div class="cta-p">
            Book a free demonstration tailored to your facility type.
            Contact our team to schedule a live demo, request a pilot deployment, or get a custom quote.
        </div>
        <div class="cta-row">
            <div class="cta-cell">
                <span class="cta-lbl">Website</span>
                <span class="cta-val"><a href="{{ $company['website_full'] }}">{{ $company['website'] }}</a></span>
            </div>
            <div class="cta-cell">
                <span class="cta-lbl">Email</span>
                <span class="cta-val"><a href="mailto:{{ $company['email'] }}">{{ $company['email'] }}</a></span>
            </div>
            <div class="cta-cell">
                <span class="cta-lbl">Phone</span>
                <span class="cta-val">{{ $company['phone'] }}</span>
            </div>
            <div class="cta-cell">
                <span class="cta-lbl">Address</span>
                <span class="cta-val">{{ $company['address'] }}</span>
            </div>
        </div>
    </div>

</div>{{-- /#content --}}
</body>
</html>
