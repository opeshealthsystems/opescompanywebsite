@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="{{ __('contact.meta_title') }}"
    description="{{ __('contact.meta_desc') }}">

<div class="contact-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="mail" style="width:12px;height:12px"></i>
        {{ __('contact.hero_eyebrow') }}
    </div>
    <h1 class="contact-title">{{ __('contact.hero_title') }}</h1>
    <p class="contact-sub">{{ __('contact.hero_sub') }}</p>
</div>

<div class="contact-layout section">

    {{-- ── LEFT: FORM ─────────────────────────────────────── --}}
    <div class="contact-form-col">
        <h2 class="contact-form-title">{{ __('contact.form_title') }}</h2>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:24px">{{ __('contact.form_subtitle') }}</p>

        @if(session('success'))
        <div class="contact-success">
            <i data-lucide="check-circle" style="width:16px;height:16px;color:#00C896"></i>
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ url($locale.'/contact') }}" method="POST" class="contact-form">
            @csrf

            <div class="cf-row">
                <div class="cf-field">
                    <label class="cf-label">{{ __('contact.label_name') }}</label>
                    <input class="cf-input @error('name') cf-error @enderror" type="text" name="name"
                           value="{{ old('name') }}" placeholder="{{ __('contact.placeholder_name') }}">
                    @error('name')<span class="cf-err-msg">{{ $message }}</span>@enderror
                </div>
                <div class="cf-field">
                    <label class="cf-label">{{ __('contact.label_email') }}</label>
                    <input class="cf-input @error('email') cf-error @enderror" type="email" name="email"
                           value="{{ old('email') }}" placeholder="{{ __('contact.placeholder_email') }}">
                    @error('email')<span class="cf-err-msg">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-row">
                <div class="cf-field">
                    <label class="cf-label">{{ __('contact.label_phone') }}</label>
                    <input class="cf-input" type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ __('contact.placeholder_phone') }}">
                </div>
                <div class="cf-field">
                    <label class="cf-label">{{ __('contact.label_facility') }}</label>
                    <select class="cf-input" name="facility_type">
                        <option value="">{{ __('contact.select_default') }}</option>
                        @foreach(config('facility_types') as $value => $labels)
                        <option value="{{ $value }}" @selected(old('facility_type') === $value)>
                            {{ $labels[app()->getLocale()] ?? $labels['en'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="cf-field">
                <label class="cf-label">{{ __('contact.label_products') }}</label>
                <input class="cf-input" type="text" name="products"
                       value="{{ old('products') }}" placeholder="{{ __('contact.placeholder_products') }}">
            </div>

            <div class="cf-field">
                <label class="cf-label">{{ __('contact.label_message') }}</label>
                <textarea class="cf-input cf-textarea" name="message" rows="4"
                          placeholder="{{ __('contact.placeholder_message') }}">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="cf-submit">
                <i data-lucide="send" style="width:15px;height:15px"></i>
                {{ __('contact.btn_submit') }}
            </button>
        </form>
    </div>

    {{-- ── RIGHT: INFO ─────────────────────────────────────── --}}
    <div class="contact-info-col">
        <div class="contact-info-card">
            <h3 style="font-size:16px;margin-bottom:20px">{{ __('contact.office_title') }}</h3>
            <div class="ci-row">
                <i data-lucide="map-pin" style="width:15px;height:15px;color:#00C896;flex-shrink:0;margin-top:2px"></i>
                <div>
                    <div style="font-weight:600;color:#e2e8f0">{{ __('contact.office_city') }}</div>
                    <div style="color:var(--text-muted);font-size:13px">{{ __('contact.office_region') }}</div>
                </div>
            </div>
            <div class="ci-row">
                <i data-lucide="mail" style="width:15px;height:15px;color:#1A6FE8;flex-shrink:0"></i>
                <div>
                    <div style="color:#e2e8f0">contact@opeshealthsystems.com</div>
                    <div style="color:var(--text-muted);font-size:13px">{{ __('contact.office_email_note') }}</div>
                </div>
            </div>
            <div class="ci-row">
                <i data-lucide="phone" style="width:15px;height:15px;color:#00C896;flex-shrink:0"></i>
                <div>
                    <div style="color:#e2e8f0">{{ config('company.phone') }}</div>
                    <div style="color:var(--text-muted);font-size:13px">{{ __('contact.office_phone_hours') }}</div>
                </div>
            </div>
        </div>

        <div class="contact-info-card" style="margin-top:20px">
            <h3 style="font-size:15px;margin-bottom:16px">{{ __('contact.steps_title') }}</h3>
            @foreach([
                ['icon'=>'user-check','color'=>'#00C896','step'=>__('contact.step_0_title'),'desc'=>__('contact.step_0_desc')],
                ['icon'=>'calendar','color'=>'#1A6FE8','step'=>__('contact.step_1_title'),'desc'=>__('contact.step_1_desc')],
                ['icon'=>'file-text','color'=>'#00C896','step'=>__('contact.step_2_title'),'desc'=>__('contact.step_2_desc')],
            ] as $s)
            <div class="ci-step">
                <div class="ci-step-icon" style="background:rgba(255,255,255,0.05)">
                    <i data-lucide="{{ $s['icon'] }}" style="width:14px;height:14px;color:{{ $s['color'] }}"></i>
                </div>
                <div>
                    <div style="font-weight:600;color:#e2e8f0;font-size:13px">{{ $s['step'] }}</div>
                    <div style="color:var(--text-muted);font-size:12px">{{ $s['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

</x-layouts.app>
