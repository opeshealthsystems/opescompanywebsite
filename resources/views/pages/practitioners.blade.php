@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="Join the OPES Practitioner Programme"
    description="Work with us to build a healthier future for Cameroon. Test our systems, share your expertise, and help shape health technology that works.">

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="user-check" style="width:12px;height:12px"></i>
        Practitioner Programme
    </div>
    <h1 class="sol-title">Join the OPES <span class="gradient-text">Practitioner Programme</span></h1>
    <p class="sol-sub">Work with us to build a healthier future for Cameroon. Test our systems, share your expertise, and help shape health technology that works.</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;margin-top:32px">
        <a href="{{ route('practitioner.register', ['locale' => $locale]) }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:8px">
            Apply as a Practitioner
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="#how-it-works" class="btn-secondary" style="display:inline-flex;align-items:center;gap:8px">
            Learn More
            <i data-lucide="chevron-down" style="width:15px;height:15px"></i>
        </a>
    </div>
</div>

<div class="divider"></div>

{{-- WHO CAN JOIN --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="users" style="width:12px;height:12px"></i>
        Eligibility
    </div>
    <h2 class="section-title">Who Can Join?</h2>
    <p class="section-sub" style="max-width:600px;margin:0 auto 48px">We welcome healthcare professionals across all disciplines who are passionate about improving health systems in Cameroon and beyond.</p>

    @php
    $icons = [
        'doctor'       => 'stethoscope',
        'nurse'        => 'heart-pulse',
        'radiologist'  => 'scan',
        'cardiologist' => 'activity',
        'pharmacist'   => 'pill',
        'lab_tech'     => 'microscope',
        'health_admin' => 'clipboard-list',
        'researcher'   => 'flask-conical',
        'other'        => 'user',
    ];
    $colors = ['#00C896','#1A6FE8','#00C896','#1A6FE8','#00C896','#1A6FE8','#00C896','#1A6FE8','#00C896'];
    $i = 0;
    @endphp

    <div class="sol-grid" style="max-width:960px;margin:0 auto">
        @foreach($professions as $key => $label)
        @php
            $icon = $icons[$key] ?? 'user';
            $color = $colors[$i % count($colors)];
            $bg = ($i % 2 === 0) ? 'rgba(0,200,150,0.08)' : 'rgba(26,111,232,0.08)';
            $i++;
        @endphp
        <div class="sol-card" style="text-align:center;align-items:center">
            <div class="sol-card-icon" style="background:{{ $bg }}">
                <i data-lucide="{{ $icon }}" style="width:24px;height:24px;color:{{ $color }}"></i>
            </div>
            <h3 class="sol-card-title" style="text-align:center">{{ $label }}</h3>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- HOW IT WORKS --}}
<div id="how-it-works" class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="git-branch" style="width:12px;height:12px"></i>
        Process
    </div>
    <h2 class="section-title">How It Works</h2>
    <p class="section-sub" style="max-width:580px;margin:0 auto 48px">Getting started is simple. Three steps stand between you and making a real impact on healthcare in Cameroon.</p>

    <div class="pi-grid" style="max-width:900px;margin:0 auto;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))">
        @foreach([
            ['step'=>'01','icon'=>'user-plus','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Register','desc'=>'Create your practitioner profile with your professional details and credentials.'],
            ['step'=>'02','icon'=>'clipboard-check','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Apply to a Programme','desc'=>'Browse open testing and demo programmes and submit your application.'],
            ['step'=>'03','icon'=>'file-text','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Test & Report','desc'=>'Test our systems and share your honest findings — with optional video reviews.'],
        ] as $step)
        <div class="pi-card" style="flex-direction:column;align-items:center;text-align:center;position:relative">
            <div style="font-size:11px;font-weight:700;color:#475569;letter-spacing:0.1em;margin-bottom:12px;text-transform:uppercase">Step {{ $step['step'] }}</div>
            <div style="width:48px;height:48px;border-radius:12px;background:{{ $step['bg'] }};display:flex;align-items:center;justify-content:center;margin-bottom:16px">
                <i data-lucide="{{ $step['icon'] }}" style="width:22px;height:22px;color:{{ $step['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:15px;margin-bottom:8px">{{ $step['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $step['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- BENEFITS --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="star" style="width:12px;height:12px"></i>
        Benefits
    </div>
    <h2 class="section-title">Why Join?</h2>
    <p class="section-sub" style="max-width:600px;margin:0 auto 48px">More than a testing programme — it's a chance to be part of something that matters.</p>

    <div class="pi-grid" style="max-width:900px;margin:0 auto">
        @foreach([
            ['icon'=>'cpu','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Shape Healthcare Technology','desc'=>'Your real-world feedback directly influences how OPES products are built and refined.'],
            ['icon'=>'coins','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Volunteer or Paid Options','desc'=>'Choose roles that match your availability — from volunteer contributions to compensated engagements.'],
            ['icon'=>'briefcase','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>'Build Your Portfolio','desc'=>'Document your contributions and grow your professional profile with verifiable health-tech experience.'],
            ['icon'=>'award','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>'Get Certified on OPES Systems','desc'=>'Earn official OPES certifications that demonstrate your expertise and commitment to digital health.'],
        ] as $b)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $b['bg'] }};display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $b['icon'] }}" style="width:18px;height:18px;color:{{ $b['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $b['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $b['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA FOOTER --}}
<div class="demo-section">
    <h2>Ready to make a difference?</h2>
    <p>Join hundreds of healthcare professionals helping build the future of health technology in Cameroon.</p>
    <a href="{{ route('practitioner.register', ['locale' => $locale]) }}" class="btn-primary" style="display:inline-flex;margin-top:8px;align-items:center;gap:8px">
        Register Now
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
