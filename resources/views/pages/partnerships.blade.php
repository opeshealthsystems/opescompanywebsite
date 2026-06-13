@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="handshake" style="width:12px;height:12px"></i>
        Partnerships
    </div>
    <h1 class="sol-title">Partner with OPES Health Systems</h1>
    <p class="sol-sub">We work with healthcare facilities, government bodies, NGOs, hardware vendors, and insurance providers who share our vision for a connected, digital Cameroon health system.</p>
</div>

{{-- PARTNER TYPES --}}
<div class="section">
    <div class="sol-grid">
        @foreach([
            ['icon'=>'hospital','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Healthcare Facility Partners','desc'=>'Pilot facilities, reference sites, and early adopters who help shape OPES systems and benefit from preferential pricing and direct support from our Douala team.','items'=>['Preferential implementation pricing','Co-development of features for your context','Case study and testimonial visibility','Direct line to OPES engineering team']],
            ['icon'=>'building-2','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Government & Ministry Partners','desc'=>'National health programmes, district health teams, and Ministry of Health departments deploying population-scale digital health infrastructure.','items'=>['MoH 2026–2030 alignment documentation','HMIS integration and reporting','UHC tracking and population dashboards','Multi-site deployment support']],
            ['icon'=>'globe','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'NGO & International Partners','desc'=>'WHO, UNICEF, MSF, and international health organisations running programmes in Cameroon and the CEMAC region.','items'=>['Donor reporting and programme dashboards','Multi-language support beyond EN/FR on request','Offline-first deployment for remote areas','Open data export in FHIR format']],
            ['icon'=>'cpu','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Technology Partners','desc'=>'Hardware vendors, medical device manufacturers, and IT service companies who want to bundle OPES into their healthcare offerings.','items'=>['Reseller agreements and revenue sharing','API integration and OEM licensing','Joint marketing in the CEMAC market','Technical certification programme']],
            ['icon'=>'shield-check','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Insurance & CNPS Partners','desc'=>'Health insurance providers, mutuals, and CNPS looking to integrate digital claims verification and patient identity through OPESCare.','items'=>['Real-time eligibility verification','HL7 FHIR claims data','Fraud reduction through patient Health ID','CNPS billing module integration']],
            ['icon'=>'graduation-cap','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Academic & Research Partners','desc'=>'Universities, medical schools, and research institutions using OPES for clinical education, research data, and public health studies.','items'=>['Anonymised de-identified data access','Research module integration','Clinical training environment','Publication co-authorship opportunities']],
        ] as $p)
        <div class="sol-card">
            <div class="sol-card-icon" style="background:{{ $p['bg'] }}">
                <i data-lucide="{{ $p['icon'] }}" style="width:24px;height:24px;color:{{ $p['color'] }}"></i>
            </div>
            <h3 class="sol-card-title">{{ $p['title'] }}</h3>
            <p class="sol-card-desc">{{ $p['desc'] }}</p>
            <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px">
                @foreach($p['items'] as $item)
                <li style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#64748b">
                    <i data-lucide="check" style="width:11px;height:11px;color:{{ $p['color'] }};flex-shrink:0;margin-top:2px"></i>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>Start a partnership conversation</h2>
    <p>Tell us about your organisation and what you're trying to achieve. We'll respond within one business day from our Douala office.</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        Contact our Partnerships Team
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
