@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="{{ __('partnerships.meta_title') }}"
    description="{{ __('partnerships.meta_desc') }}">

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="handshake" style="width:12px;height:12px"></i>
        {{ __('partnerships.hero_eyebrow') }}
    </div>
    <h1 class="sol-title">{{ __('partnerships.hero_title') }}</h1>
    <p class="sol-sub">{{ __('partnerships.hero_sub') }}</p>
</div>

{{-- PARTNER TYPES --}}
<div class="section">
    <div class="sol-grid">
        @foreach([
            ['icon'=>'hospital','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('partnerships.partner_0_title'),'desc'=>__('partnerships.partner_0_desc'),'items'=>[__('partnerships.partner_0_item_0'),__('partnerships.partner_0_item_1'),__('partnerships.partner_0_item_2'),__('partnerships.partner_0_item_3')]],
            ['icon'=>'building-2','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('partnerships.partner_1_title'),'desc'=>__('partnerships.partner_1_desc'),'items'=>[__('partnerships.partner_1_item_0'),__('partnerships.partner_1_item_1'),__('partnerships.partner_1_item_2'),__('partnerships.partner_1_item_3')]],
            ['icon'=>'globe','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('partnerships.partner_2_title'),'desc'=>__('partnerships.partner_2_desc'),'items'=>[__('partnerships.partner_2_item_0'),__('partnerships.partner_2_item_1'),__('partnerships.partner_2_item_2'),__('partnerships.partner_2_item_3')]],
            ['icon'=>'cpu','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('partnerships.partner_3_title'),'desc'=>__('partnerships.partner_3_desc'),'items'=>[__('partnerships.partner_3_item_0'),__('partnerships.partner_3_item_1'),__('partnerships.partner_3_item_2'),__('partnerships.partner_3_item_3')]],
            ['icon'=>'shield-check','color'=>'#00C896','bg'=>'rgba(0,200,150,0.08)','title'=>__('partnerships.partner_4_title'),'desc'=>__('partnerships.partner_4_desc'),'items'=>[__('partnerships.partner_4_item_0'),__('partnerships.partner_4_item_1'),__('partnerships.partner_4_item_2'),__('partnerships.partner_4_item_3')]],
            ['icon'=>'graduation-cap','color'=>'#1A6FE8','bg'=>'rgba(26,111,232,0.08)','title'=>__('partnerships.partner_5_title'),'desc'=>__('partnerships.partner_5_desc'),'items'=>[__('partnerships.partner_5_item_0'),__('partnerships.partner_5_item_1'),__('partnerships.partner_5_item_2'),__('partnerships.partner_5_item_3')]],
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
    <h2>{{ __('partnerships.cta_title') }}</h2>
    <p>{{ __('partnerships.cta_sub') }}</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        {{ __('partnerships.cta_button') }}
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
