@php $locale = app()->getLocale(); @endphp

<x-layouts.app>

<div class="contact-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="mail" style="width:12px;height:12px"></i>
        Contact OPES Health Systems
    </div>
    <h1 class="contact-title">Let's talk about your facility</h1>
    <p class="contact-sub">Book a free demo, ask a question, or request a custom quote. Our team in Douala responds within one business day.</p>
</div>

<div class="contact-layout section">

    {{-- ── LEFT: FORM ─────────────────────────────────────── --}}
    <div class="contact-form-col">
        <h2 class="contact-form-title">Book a Free Demo</h2>
        <p style="color:#64748b;font-size:13px;margin-bottom:24px">45 minutes · tailored to your facility type · no commitment</p>

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
                    <label class="cf-label">Full name *</label>
                    <input class="cf-input @error('name') cf-error @enderror" type="text" name="name"
                           value="{{ old('name') }}" placeholder="Dr. Ngo Marie">
                    @error('name')<span class="cf-err-msg">{{ $message }}</span>@enderror
                </div>
                <div class="cf-field">
                    <label class="cf-label">Email address *</label>
                    <input class="cf-input @error('email') cf-error @enderror" type="email" name="email"
                           value="{{ old('email') }}" placeholder="marie@clinique.cm">
                    @error('email')<span class="cf-err-msg">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-row">
                <div class="cf-field">
                    <label class="cf-label">Phone (optional)</label>
                    <input class="cf-input" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                </div>
                <div class="cf-field">
                    <label class="cf-label">Facility type</label>
                    <select class="cf-input" name="facility_type">
                        <option value="">Select…</option>
                        <option @selected(old('facility_type')=='clinic')>Clinic</option>
                        <option @selected(old('facility_type')=='hospital')>Hospital</option>
                        <option @selected(old('facility_type')=='laboratory')>Laboratory</option>
                        <option @selected(old('facility_type')=='pharmacy')>Pharmacy</option>
                        <option @selected(old('facility_type')=='government')>Government / Ministry</option>
                        <option @selected(old('facility_type')=='ngo')>NGO / International</option>
                        <option @selected(old('facility_type')=='other')>Other</option>
                    </select>
                </div>
            </div>

            <div class="cf-field">
                <label class="cf-label">Products of interest (optional)</label>
                <input class="cf-input" type="text" name="products"
                       value="{{ old('products') }}" placeholder="e.g. OPES EMR, OPES Lab, PHARMIS">
            </div>

            <div class="cf-field">
                <label class="cf-label">Message (optional)</label>
                <textarea class="cf-input cf-textarea" name="message" rows="4"
                          placeholder="Tell us about your facility, current challenges, or specific questions…">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="cf-submit">
                <i data-lucide="send" style="width:15px;height:15px"></i>
                Send message
            </button>
        </form>
    </div>

    {{-- ── RIGHT: INFO ─────────────────────────────────────── --}}
    <div class="contact-info-col">
        <div class="contact-info-card">
            <h3 style="font-size:16px;margin-bottom:20px">Our office</h3>
            <div class="ci-row">
                <i data-lucide="map-pin" style="width:15px;height:15px;color:#00C896;flex-shrink:0;margin-top:2px"></i>
                <div>
                    <div style="font-weight:600;color:#e2e8f0">Bonamousadi, Douala</div>
                    <div style="color:#64748b;font-size:13px">Cameroon · CEMAC Region</div>
                </div>
            </div>
            <div class="ci-row">
                <i data-lucide="mail" style="width:15px;height:15px;color:#1A6FE8;flex-shrink:0"></i>
                <div>
                    <div style="color:#e2e8f0">contact@opeshealthsystems.com</div>
                    <div style="color:#64748b;font-size:13px">Response within 1 business day</div>
                </div>
            </div>
            <div class="ci-row">
                <i data-lucide="phone" style="width:15px;height:15px;color:#00C896;flex-shrink:0"></i>
                <div>
                    <div style="color:#e2e8f0">+237 6XX XXX XXX</div>
                    <div style="color:#64748b;font-size:13px">Mon–Fri · 8h–18h WAT</div>
                </div>
            </div>
        </div>

        <div class="contact-info-card" style="margin-top:20px">
            <h3 style="font-size:15px;margin-bottom:16px">What happens next?</h3>
            @foreach([
                ['icon'=>'user-check','color'=>'#00C896','step'=>'We confirm your booking','desc'=>'Within one business day via email.'],
                ['icon'=>'calendar','color'=>'#1A6FE8','step'=>'45-minute tailored demo','desc'=>'Focused on your facility type and systems of interest.'],
                ['icon'=>'file-text','color'=>'#00C896','step'=>'Custom quote & proposal','desc'=>'No obligation. Pricing tailored to your facility size.'],
            ] as $s)
            <div class="ci-step">
                <div class="ci-step-icon" style="background:rgba(255,255,255,0.05)">
                    <i data-lucide="{{ $s['icon'] }}" style="width:14px;height:14px;color:{{ $s['color'] }}"></i>
                </div>
                <div>
                    <div style="font-weight:600;color:#e2e8f0;font-size:13px">{{ $s['step'] }}</div>
                    <div style="color:#64748b;font-size:12px">{{ $s['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

</x-layouts.app>
