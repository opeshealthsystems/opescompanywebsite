@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        Solutions
    </div>
    <h1 class="sol-title">Digital health for every <span class="gradient-text">facility type</span></h1>
    <p class="sol-sub">Whether you're a district clinic, referral hospital, standalone pharmacy, or a Ministry of Health programme — OPES has a configured solution ready to deploy.</p>
</div>

{{-- FACILITY SOLUTIONS --}}
<div class="section">
    <div class="sol-grid">

        @foreach([
            ['icon'=>'hospital','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Referral & Teaching Hospitals','desc'=>'Full Hospital HIS, EMR, all specialist modules, lab, radiology, pharmacy, revenue cycle — one unified ecosystem connected by OPESCare Health ID.','systems'=>['OPES Hospital HIS','OPES EMR','OPESCare','OPES Lab','PHARMIS','RADIS','RCMIS']],
            ['icon'=>'stethoscope','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Clinics & Health Centres','desc'=>'OPES EMR for electronic medical records, Opes Triage for patient flow management, and OPESCare for patient identity — affordable and deployable in days.','systems'=>['OPES EMR','Opes Triage','OPESCare']],
            ['icon'=>'microscope','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Independent Laboratories','desc'=>'OPES Lab for test order management, results, reporting, and HL7 FHIR-compatible integration with any referring facility.','systems'=>['OPES Lab','OPESCare']],
            ['icon'=>'pill','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Pharmacies & Drug Stores','desc'=>'PHARMIS covers inventory, prescriptions, CNPS billing, expiry tracking, and inter-branch transfers. Integrates natively with OPES EMR.','systems'=>['PHARMIS','OPESCare']],
            ['icon'=>'shield-check','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Government & Ministry Programmes','desc'=>'OPES supports UHC-IS for universal health coverage tracking, CDMS for clinical documents, and population-level dashboards for the Ministry of Health 2026–2030 plan.','systems'=>['UHC IS','OPES CDMS','OPESCare','RCMIS']],
            ['icon'=>'users','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Hospital Networks & Groups','desc'=>'Multi-site deployments with centralised OPESCare Health ID mean a patient registered in Douala can be seen in Yaoundé with full history — no re-registration.','systems'=>['OPESCare','OPES Hospital HIS','OPES EMR','All 22 systems']],
        ] as $sol)
        <div class="sol-card">
            <div class="sol-card-icon" style="background:{{ $sol['bg'] }}">
                <i data-lucide="{{ $sol['icon'] }}" style="width:24px;height:24px;color:{{ $sol['color'] }}"></i>
            </div>
            <h3 class="sol-card-title">{{ $sol['title'] }}</h3>
            <p class="sol-card-desc">{{ $sol['desc'] }}</p>
            <div class="sol-systems">
                @foreach($sol['systems'] as $sys)
                <span class="sol-badge">{{ $sys }}</span>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>
</div>

<div class="divider"></div>

{{-- WHY OPES --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="star" style="width:12px;height:12px"></i>
        Why OPES
    </div>
    <h2 class="section-title">Built for the realities of African healthcare</h2>
    <p class="section-sub" style="max-width:640px;margin:0 auto 48px">Unlike generic health software adapted for Africa, OPES was designed from scratch for Cameroonian clinical workflows, bilingual interfaces, and CEMAC regulatory requirements.</p>

    <div class="pi-grid" style="max-width:900px;margin:0 auto">
        @foreach([
            ['icon'=>'globe','color'=>'#00C896','title'=>'Fully bilingual (EN/FR)','desc'=>'Every screen, report, and document in both French and English. No translation patches — built-in from day one.'],
            ['icon'=>'link','color'=>'#1A6FE8','title'=>'HL7 FHIR R4 native','desc'=>'Industry-standard interoperability built in. Any OPES system can talk to any other — or to your existing equipment.'],
            ['icon'=>'wifi-off','color'=>'#00C896','title'=>'Works offline','desc'=>'Low-connectivity mode keeps your facility running during outages. Data syncs automatically when the connection returns.'],
            ['icon'=>'shield','color'=>'#1A6FE8','title'=>'MoH 2026–2030 aligned','desc'=>'Designed alongside the Cameroon Ministry of Health national digitalization roadmap for UHC and HMIS compliance.'],
            ['icon'=>'cpu','color'=>'#00C896','title'=>'Cloud & on-premise','desc'=>'Deploy on our secure cloud or install on your own servers. Both options get full support and updates.'],
            ['icon'=>'life-buoy','color'=>'#1A6FE8','title'=>'Local support in Douala','desc'=>'Our team is based in Bonamousadi, Douala. We speak your language, know your context, and respond within hours.'],
        ] as $w)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $w['icon'] }}" style="width:18px;height:18px;color:{{ $w['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $w['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $w['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>Ready to see a demo?</h2>
    <p>Book a free 45-minute session with our team. We'll show you the right OPES systems for your facility.</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        Book a Free Demo
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
