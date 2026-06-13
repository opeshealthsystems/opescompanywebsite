@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

{{-- HERO --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="building-2" style="width:12px;height:12px"></i>
        About OPES Health Systems
    </div>
    <h1 class="about-title">Building Africa's digital health <span class="gradient-text">infrastructure</span></h1>
    <p class="about-sub">OPES Health Systems SARL is a Cameroon-based healthtech company headquartered in Bonamousadi, Douala — designing integrated healthcare software for the Cameroon and CEMAC region.</p>
</div>

<div class="section about-layout">

    {{-- MISSION --}}
    <div class="about-card" style="border-color:rgba(0,200,150,0.2)">
        <div class="about-card-icon" style="background:rgba(0,200,150,0.08)">
            <i data-lucide="target" style="width:22px;height:22px;color:#00C896"></i>
        </div>
        <h3>Our Mission</h3>
        <p>To eliminate fragmented, paper-based healthcare records across Cameroon and the CEMAC region by providing every health facility — from a rural clinic to a national referral hospital — with affordable, bilingual, interoperable digital health software.</p>
    </div>

    <div class="about-card" style="border-color:rgba(26,111,232,0.2)">
        <div class="about-card-icon" style="background:rgba(26,111,232,0.08)">
            <i data-lucide="eye" style="width:22px;height:22px;color:#1A6FE8"></i>
        </div>
        <h3>Our Vision</h3>
        <p>A CEMAC region where every patient has a universal Health ID and their complete medical record follows them seamlessly — from first contact at a village health post through specialist care at a teaching hospital — all connected through the OPESCare interoperability layer.</p>
    </div>

</div>

<div class="divider"></div>

{{-- STORY --}}
<div class="section" style="max-width:800px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="book-open" style="width:12px;height:12px"></i>
        Our Story
    </div>
    <h2 class="section-title">Born from Cameroon's healthcare realities</h2>
    <div class="about-story">
        <p>OPES Health Systems grew out of direct experience with the challenges of healthcare delivery in Cameroon: duplicate patient records across facilities, lost prescription histories, manual laboratory logbooks, and revenue leakage from disconnected billing systems.</p>
        <p>We set out to build what we couldn't find — a comprehensive, bilingual, Africa-first healthcare ecosystem. Not a port of a Western EMR with French toggled on, but software designed from the ground up for the Cameroonian clinical workflow, CNPS billing requirements, and HL7 FHIR interoperability standards.</p>
        <p>Based in Bonamousadi, Douala, our team combines clinical knowledge, software engineering, and deep understanding of the CEMAC health sector. OPES is aligned with the Cameroon Ministry of Health 2026–2030 national digitalization plan and supports the Universal Health Coverage mandate.</p>
    </div>
</div>

<div class="divider"></div>

{{-- NUMBERS --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:32px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        The OPES Ecosystem
    </div>
    <div class="stats-bar" style="max-width:700px;margin:0 auto">
        @foreach([
            ['value'=>'22','label'=>'Integrated Software Systems'],
            ['value'=>'EN/FR','label'=>'Fully Bilingual'],
            ['value'=>'CEMAC','label'=>'Regional Coverage'],
            ['value'=>'HL7 FHIR','label'=>'Interoperability Standard'],
        ] as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s['value'] }}</div>
            <div class="stat-label">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- VALUES --}}
<div class="section" style="text-align:center">
    <h2 class="section-title">Our values</h2>
    <div class="pi-grid" style="max-width:900px;margin:32px auto 0">
        @foreach([
            ['icon'=>'heart','color'=>'#00C896','title'=>'Patient First','desc'=>'Every feature, every workflow, every design decision starts with: does this make care better for the patient?'],
            ['icon'=>'shield-check','color'=>'#1A6FE8','title'=>'Trust Through Data Security','desc'=>'Patient data is sacred. We apply industry-standard encryption, role-based access, and audit trails on every system.'],
            ['icon'=>'globe-2','color'=>'#00C896','title'=>'Built for Africa','desc'=>'Not adapted — designed. Offline-capable, bilingual, CNPS-aware, and priced for the African health market.'],
            ['icon'=>'handshake','color'=>'#1A6FE8','title'=>'Long-term Partnership','desc'=>'We measure success by how long facilities stay with OPES — and how much their operations improve.'],
        ] as $v)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $v['icon'] }}" style="width:18px;height:18px;color:{{ $v['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $v['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $v['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>Work with us</h2>
    <p>We're looking for healthcare facilities, government partners, and NGOs who share our vision for a digitally connected Cameroon health system.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            Get in Touch <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/partnerships') }}" class="btn-secondary">
            Partnership Opportunities <i data-lucide="handshake" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>
