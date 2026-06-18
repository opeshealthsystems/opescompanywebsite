@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="{{ __('about.meta_title') }}"
    description="{{ __('about.meta_desc') }}">

{{-- HERO --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="building-2" style="width:12px;height:12px"></i>
        {{ __('about.hero_eyebrow') }}
    </div>
    <h1 class="about-title">{{ __('about.hero_title') }} <span class="gradient-text">{{ __('about.hero_title_gradient') }}</span></h1>
    <p class="about-sub">{{ __('about.hero_sub') }}</p>
</div>

<div class="section about-layout">

    {{-- MISSION --}}
    <div class="about-card" style="border-color:rgba(0,200,150,0.2)">
        <div class="about-card-icon" style="background:rgba(0,200,150,0.08)">
            <i data-lucide="target" style="width:22px;height:22px;color:#00C896"></i>
        </div>
        <h3>{{ __('about.mission_title') }}</h3>
        <p>{{ __('about.mission_body') }}</p>
    </div>

    <div class="about-card" style="border-color:rgba(26,111,232,0.2)">
        <div class="about-card-icon" style="background:rgba(26,111,232,0.08)">
            <i data-lucide="eye" style="width:22px;height:22px;color:#1A6FE8"></i>
        </div>
        <h3>{{ __('about.vision_title') }}</h3>
        <p>{{ __('about.vision_body') }}</p>
    </div>

</div>

<div class="divider"></div>

{{-- STORY --}}
<div class="section" style="max-width:800px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="book-open" style="width:12px;height:12px"></i>
        {{ __('about.story_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('about.story_title') }}</h2>
    <div class="about-story">
        <p>{{ __('about.story_p1') }}</p>
        <p>{{ __('about.story_p2') }}</p>
        <p>{{ __('about.story_p3') }}</p>
        <p>{{ __('about.story_p4') }}</p>
    </div>
</div>

<div class="divider"></div>

{{-- PLATFORM --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ __('about.platform_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('about.platform_title') }}</h2>
    <p style="color:#64748b;max-width:680px;margin:12px auto 0;font-size:15px;line-height:1.75">{{ __('about.platform_desc') }}</p>
    <div class="platform-cat-grid">
        @foreach([
            ['icon'=>'file-text',    'color'=>'#00C896', 'label'=>__('about.platform_cat_0')],
            ['icon'=>'building-2',   'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_1')],
            ['icon'=>'flask-conical','color'=>'#00C896', 'label'=>__('about.platform_cat_2')],
            ['icon'=>'pill',         'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_3')],
            ['icon'=>'stethoscope',  'color'=>'#00C896', 'label'=>__('about.platform_cat_4')],
            ['icon'=>'share-2',      'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_5')],
            ['icon'=>'brain',        'color'=>'#00C896', 'label'=>__('about.platform_cat_6')],
            ['icon'=>'zap',          'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_7')],
            ['icon'=>'badge',        'color'=>'#00C896', 'label'=>__('about.platform_cat_8')],
            ['icon'=>'bar-chart-2',  'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_9')],
        ] as $cat)
        <div class="platform-cat-item">
            <div class="platform-cat-icon" style="background:{{ $cat['color'] }}1a">
                <i data-lucide="{{ $cat['icon'] }}" style="width:20px;height:20px;color:{{ $cat['color'] }}"></i>
            </div>
            <span>{{ $cat['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- OPESCARE SPOTLIGHT --}}
<div class="section">
    <div class="opescare-spotlight">
        <div class="section-label" style="margin-bottom:14px">
            <i data-lucide="share-2" style="width:12px;height:12px"></i>
            {{ __('about.opescare_eyebrow') }}
        </div>
        <h2 style="font-size:clamp(20px,3vw,26px);font-weight:700;color:#e2e8f0;margin-bottom:14px;line-height:1.3">{{ __('about.opescare_title') }}</h2>
        <p style="color:#94a3b8;max-width:640px;line-height:1.75;margin-bottom:28px;font-size:15px">{{ __('about.opescare_body') }}</p>
        <a href="{{ url($locale.'/products/opescare') }}" class="btn-primary" style="display:inline-flex">
            {{ __('about.opescare_cta') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
    </div>
</div>

<div class="divider"></div>

{{-- NUMBERS --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:32px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        {{ __('about.stats_eyebrow') }}
    </div>
    <div class="stats-bar" style="max-width:900px;margin:0 auto">
        @foreach([
            ['value'=>'22','label'=>__('about.stat_0_label')],
            ['value'=>'EN/FR','label'=>__('about.stat_1_label')],
            ['value'=>'CEMAC','label'=>__('about.stat_2_label')],
            ['value'=>'HL7 FHIR','label'=>__('about.stat_3_label')],
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
    <h2 class="section-title">{{ __('about.values_title') }}</h2>
    <div class="pi-grid" style="max-width:900px;margin:32px auto 0">
        @foreach([
            ['icon'=>'heart','color'=>'#00C896','title'=>__('about.value_0_title'),'desc'=>__('about.value_0_desc')],
            ['icon'=>'shield-check','color'=>'#1A6FE8','title'=>__('about.value_1_title'),'desc'=>__('about.value_1_desc')],
            ['icon'=>'globe-2','color'=>'#00C896','title'=>__('about.value_2_title'),'desc'=>__('about.value_2_desc')],
            ['icon'=>'handshake','color'=>'#1A6FE8','title'=>__('about.value_3_title'),'desc'=>__('about.value_3_desc')],
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

{{-- MANIFESTO --}}
<div class="about-manifesto">
    <p>{{ __('about.manifesto_line1') }}</p>
    <p class="manifesto-accent">{{ __('about.manifesto_line2') }}</p>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ __('about.cta_title') }}</h2>
    <p>{{ __('about.cta_body') }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ __('about.cta_contact') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/partnerships') }}" class="btn-secondary">
            {{ __('about.cta_partnership') }} <i data-lucide="handshake" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>
