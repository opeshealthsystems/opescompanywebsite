@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="{{ __('solutions.meta_title') }}"
    description="{{ __('solutions.meta_desc') }}">

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ __('solutions.hero_eyebrow') }}
    </div>
    <h1 class="sol-title">{{ __('solutions.hero_title_1') }} <span class="gradient-text">{{ __('solutions.hero_title_gradient') }}</span></h1>
    <p class="sol-sub">{{ __('solutions.hero_sub') }}</p>
</div>

{{-- FACILITY SOLUTIONS --}}
<div class="section">
    <div class="sol-grid">

        @foreach([
            ['icon'=>'hospital','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('solutions.facility_0_title'),'desc'=>__('solutions.facility_0_desc'),'systems'=>['OPES Hospital HIS','OPES EMR','OPESCare','OPES Lab','PHARMIS','RADIS','RCMIS']],
            ['icon'=>'stethoscope','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_1_title'),'desc'=>__('solutions.facility_1_desc'),'systems'=>['OPES EMR','Opes Triage','OPESCare']],
            ['icon'=>'microscope','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('solutions.facility_2_title'),'desc'=>__('solutions.facility_2_desc'),'systems'=>['OPES Lab','OPESCare']],
            ['icon'=>'pill','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_3_title'),'desc'=>__('solutions.facility_3_desc'),'systems'=>['PHARMIS','OPESCare']],
            ['icon'=>'shield-check','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('solutions.facility_4_title'),'desc'=>__('solutions.facility_4_desc'),'systems'=>['UHC IS','OPES CDMS','OPESCare','RCMIS']],
            ['icon'=>'users','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_5_title'),'desc'=>__('solutions.facility_5_desc'),'systems'=>['OPESCare','OPES Hospital HIS','OPES EMR','All 22 systems']],
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
        {{ __('solutions.why_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('solutions.why_title') }}</h2>
    <p class="section-sub" style="max-width:640px;margin:0 auto 48px">{{ __('solutions.why_sub') }}</p>

    <div class="pi-grid" style="max-width:900px;margin:0 auto">
        @foreach([
            ['icon'=>'globe','color'=>'#00C896','title'=>__('solutions.why_0_title'),'desc'=>__('solutions.why_0_desc')],
            ['icon'=>'link','color'=>'#1A6FE8','title'=>__('solutions.why_1_title'),'desc'=>__('solutions.why_1_desc')],
            ['icon'=>'wifi-off','color'=>'#00C896','title'=>__('solutions.why_2_title'),'desc'=>__('solutions.why_2_desc')],
            ['icon'=>'shield','color'=>'#1A6FE8','title'=>__('solutions.why_3_title'),'desc'=>__('solutions.why_3_desc')],
            ['icon'=>'cpu','color'=>'#00C896','title'=>__('solutions.why_4_title'),'desc'=>__('solutions.why_4_desc')],
            ['icon'=>'life-buoy','color'=>'#1A6FE8','title'=>__('solutions.why_5_title'),'desc'=>__('solutions.why_5_desc')],
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
    <h2>{{ __('solutions.cta_title') }}</h2>
    <p>{{ __('solutions.cta_sub') }}</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        {{ __('solutions.cta_button') }}
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
