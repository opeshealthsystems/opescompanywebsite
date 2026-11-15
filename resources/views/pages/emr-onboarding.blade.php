@php
    $locale        = app()->getLocale();
    $isFr          = $locale === 'fr';
    $jobRoles      = \App\Models\OnboardingStaff::$jobRoles;
    $accessLevels  = \App\Models\OnboardingStaff::$accessLevels;
    $facilityTypes = \App\Models\HospitalOnboarding::facilityTypes();
    $sizes         = \App\Models\HospitalOnboarding::sizes();
    $oldStaff      = old('staff');
    $staffRows     = (is_array($oldStaff) && count($oldStaff)) ? array_values($oldStaff) : [[]];
@endphp

<x-layouts.app :title="$isFr ? 'Intégration EMR — OPES Health Systems' : 'EMR Onboarding — OPES Health Systems'"
               :description="$isFr ? 'Fournissez les informations de votre établissement et de votre personnel pour configurer votre démonstration EMR.' : 'Provide your facility and staff information to set up your EMR demo.'">

<style>
.demo-page-hero { background: var(--surface); padding: 72px 48px 56px; text-align: center; border-bottom: 1px solid #1e293b; }
.demo-page-hero h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.demo-page-hero p  { color: var(--text-muted); font-size: 1.05rem; max-width: 580px; margin: 0 auto 28px; line-height: 1.7; }
.demo-steps { display: flex; justify-content: center; gap: 32px; flex-wrap: wrap; margin-top: 8px; }
.demo-step  { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.82rem; }
.demo-step-num { width: 22px; height: 22px; border-radius: 50%; background: #69D5F5; color: #0f172a; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

.demo-layout  { max-width: 940px; margin: 0 auto; padding: 52px 24px 80px; display: grid; grid-template-columns: 1fr 320px; gap: 40px; align-items: start; }
.demo-form-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; }
.demo-form-card h2 { font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 28px; display: flex; align-items: center; gap: 9px; }
.df-section { margin-bottom: 28px; }
.df-section-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid #1e293b; padding-bottom: 8px; }
.df-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.df-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.df-group label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); }
.df-group input, .df-group select, .df-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.df-group input:focus, .df-group select:focus, .df-group textarea:focus { outline: none; border-color: #69D5F5; }
.df-group select option { background: #0f172a; }
.df-group textarea { min-height: 80px; resize: vertical; }

.prod-checkbox-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.prod-cb-label { display: flex; align-items: center; gap: 8px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 9px 12px; cursor: pointer; font-size: 0.82rem; color: var(--text-muted); transition: all .2s; }
.prod-cb-label:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F510; }
.prod-cb-label input[type=checkbox] { accent-color: #69D5F5; }

/* staff repeater */
.staff-row { background: #0b1322; border: 1px solid #1e293b; border-radius: 12px; padding: 18px 18px 8px; margin-bottom: 14px; }
.staff-row-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.staff-row-title { font-size: 0.74rem; font-weight: 700; color: var(--text-muted); letter-spacing: .06em; text-transform: uppercase; }
.staff-remove { display: inline-flex; align-items: center; gap: 5px; background: transparent; border: 1px solid #1e293b; color: var(--text-muted); border-radius: 7px; padding: 5px 10px; font-size: 0.72rem; cursor: pointer; transition: all .2s; }
.staff-remove:hover { border-color: #ef4444; color: #ef4444; }
.staff-task-toggle { display: flex; align-items: center; gap: 9px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 10px 12px; cursor: pointer; font-size: 0.82rem; color: var(--text-muted); margin-bottom: 14px; }
.staff-task-toggle:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F510; }
.staff-task-toggle input { accent-color: #69D5F5; width: 16px; height: 16px; flex-shrink: 0; }
.staff-add { display: inline-flex; align-items: center; gap: 7px; background: #0f172a; border: 1px dashed #69D5F560; color: #69D5F5; border-radius: 9px; padding: 11px 18px; font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all .2s; }
.staff-add:hover { background: #69D5F510; border-color: #69D5F5; }

.df-submit { background: #69D5F5; color: #0f172a; border: none; border-radius: 9px; padding: 13px 32px; font-size: 0.92rem; font-weight: 700; cursor: pointer; width: 100%; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 16px; }
.df-submit:hover { background: #00b386; }

.demo-sidebar { display: flex; flex-direction: column; gap: 20px; }
.demo-info-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 24px; }
.demo-info-card h3 { font-size: 0.9rem; font-weight: 700; color: #f1f5f9; margin-bottom: 16px; }
.demo-info-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; }
.demo-info-row svg { flex-shrink: 0; margin-top: 1px; color: var(--brand-accent); }

.df-success { background: #69D5F514; border: 1px solid #69D5F540; border-radius: 10px; padding: 28px; text-align: center; }
.df-success h3 { color: var(--brand-accent); font-size: 1.2rem; margin-bottom: 10px; }
.df-success p  { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
.df-error { background: #ef444414; border: 1px solid #ef444440; border-radius: 10px; padding: 14px 18px; margin-bottom: 22px; color: #fca5a5; font-size: 0.82rem; }

@media (max-width: 768px) {
    .demo-page-hero { padding: 48px 20px 40px; }
    .demo-layout { grid-template-columns: 1fr; padding: 32px 16px 60px; gap: 28px; }
    .demo-steps { gap: 16px; }
    .df-row, .prod-checkbox-grid { grid-template-columns: 1fr; }
}
</style>

<div class="demo-page-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
        {{ $isFr ? 'Intégration EMR' : 'EMR Onboarding' }}
    </div>
    <h1>{{ $isFr ? 'Configurons votre démo EMR' : 'Let\'s set up your EMR demo' }}</h1>
    <p>{{ $isFr ? 'Indiquez les détails de votre établissement et la liste du personnel qui utilisera le système. Notre équipe créera ensuite chaque compte dans l\'EMR pour vous.' : 'Tell us about your facility and the staff who will use the system. Our team will then create each account in the EMR for you.' }}</p>
    <div class="demo-steps">
        <div class="demo-step"><div class="demo-step-num">1</div>{{ $isFr ? 'Remplissez ce formulaire' : 'Fill in this form' }}</div>
        <div class="demo-step" style="color:var(--text-faint)">›</div>
        <div class="demo-step"><div class="demo-step-num">2</div>{{ $isFr ? 'Nous créons les comptes EMR' : 'We create the EMR accounts' }}</div>
        <div class="demo-step" style="color:var(--text-faint)">›</div>
        <div class="demo-step"><div class="demo-step-num">3</div>{{ $isFr ? 'Votre équipe se connecte' : 'Your team logs in' }}</div>
    </div>
</div>

<div class="demo-layout">

    <div class="demo-form-card">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ $isFr ? 'Formulaire d\'intégration' : 'Onboarding form' }}
        </h2>

        @if(session('success'))
        <div class="df-success">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2" style="margin:0 auto 14px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Informations reçues !' : 'Information received!' }}</h3>
            <p>{{ $isFr ? 'Merci ! Notre équipe va créer les comptes EMR pour votre personnel et vous contactera avec les détails de connexion.' : 'Thank you! Our team will create the EMR accounts for your staff and contact you with the login details.' }}</p>
        </div>
        @else

        @if($errors->any())
        <div class="df-error">
            {{ $isFr ? 'Veuillez corriger les champs en surbrillance ci-dessous.' : 'Please correct the highlighted fields below.' }}
        </div>
        @endif

        <form action="{{ route('emr-onboarding.submit', $locale) }}" method="POST">
            @csrf

            {{-- Facility --}}
            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Votre établissement' : 'Your facility' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Nom de l\'établissement' : 'Facility name' }} *</label>
                    <input type="text" name="facility_name" required maxlength="150" value="{{ old('facility_name') }}" placeholder="{{ $isFr ? 'Hôpital Central de Yaoundé' : 'Central Hospital Yaoundé' }}">
                    @error('facility_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Type d\'établissement' : 'Facility type' }}</label>
                        <select name="facility_type" aria-label="{{ $isFr ? 'Type d\'établissement' : 'Facility type' }}">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($facilityTypes as $val => $labels)
                            <option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Taille (personnel)' : 'Size (staff)' }}</label>
                        <select name="facility_size" aria-label="{{ $isFr ? 'Taille du personnel' : 'Staff size' }}">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($sizes as $s)
                            <option value="{{ $s }}" {{ old('facility_size') === $s ? 'selected' : '' }}>{{ $s }} {{ $isFr ? 'personnes' : 'people' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Pays' : 'Country' }}</label>
                        <input type="text" name="country" maxlength="60" value="{{ old('country', 'Cameroun') }}" placeholder="{{ $isFr ? 'Cameroun' : 'Cameroon' }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Ville / Région' : 'City / Region' }}</label>
                        <input type="text" name="city" maxlength="80" value="{{ old('city') }}" placeholder="{{ $isFr ? 'Yaoundé' : 'Yaoundé' }}">
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Adresse' : 'Address' }}</label>
                    <input type="text" name="address" maxlength="255" value="{{ old('address') }}" placeholder="{{ $isFr ? 'Quartier, rue...' : 'District, street...' }}">
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Téléphone de l\'établissement' : 'Facility phone' }}</label>
                        <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Email de l\'établissement' : 'Facility email' }}</label>
                        <input type="email" name="email" maxlength="150" value="{{ old('email') }}" placeholder="info@hospital.cm">
                        @error('email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Site web' : 'Website' }}</label>
                        <input type="text" name="website" maxlength="150" value="{{ old('website') }}" placeholder="https://...">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'N° d\'enregistrement / licence' : 'Registration / licence no.' }}</label>
                        <input type="text" name="registration_number" maxlength="80" value="{{ old('registration_number') }}">
                    </div>
                </div>
            </div>

            {{-- Primary contact --}}
            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Contact principal' : 'Primary contact' }}</div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Nom complet' : 'Full name' }} *</label>
                        <input type="text" name="contact_name" required maxlength="120" value="{{ old('contact_name') }}" placeholder="{{ $isFr ? 'Dr. Mbarga Jean' : 'Dr. John Smith' }}">
                        @error('contact_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Fonction' : 'Title / Role' }}</label>
                        <input type="text" name="contact_title" maxlength="80" value="{{ old('contact_title') }}" placeholder="{{ $isFr ? 'Directeur, DSI...' : 'Director, IT lead...' }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Téléphone / WhatsApp' : 'Phone / WhatsApp' }}</label>
                        <input type="tel" name="contact_phone" maxlength="30" value="{{ old('contact_phone') }}" placeholder="+237 6XX XXX XXX">
                    </div>
                    <div class="df-group">
                        <label>Email *</label>
                        <input type="email" name="contact_email" required maxlength="150" value="{{ old('contact_email') }}" placeholder="you@hospital.cm">
                        @error('contact_email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Scope --}}
            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Portée de la démo' : 'Demo scope' }}</div>
                @if($products->isNotEmpty())
                <div class="df-group">
                    <label>{{ $isFr ? 'Produits / modules souhaités' : 'Products / modules wanted' }}</label>
                    <div class="prod-checkbox-grid">
                        @foreach($products as $listedProduct)
                        @php $prod = $listedProduct->name; @endphp
                        <label class="prod-cb-label">
                            <input type="checkbox" name="products[]" value="{{ $prod }}" {{ in_array($prod, old('products', [])) ? 'checked' : '' }}>
                            {{ $prod }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="df-group">
                    <label>{{ $isFr ? 'Départements / unités concernés' : 'Departments / units in scope' }}</label>
                    <input type="text" name="departments" maxlength="500" value="{{ old('departments') }}" placeholder="{{ $isFr ? 'Consultation, Laboratoire, Pharmacie, Caisse...' : 'Outpatient, Lab, Pharmacy, Billing...' }}">
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Date de démarrage souhaitée (facultatif)' : 'Preferred start date (optional)' }}</label>
                    <input type="date" name="preferred_start_date" aria-label="{{ $isFr ? 'Date de démarrage souhaitée' : 'Preferred start date' }}" value="{{ old('preferred_start_date') }}">
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Notes ou questions' : 'Notes or questions' }}</label>
                    <textarea name="notes" maxlength="2000" placeholder="{{ $isFr ? 'Tout ce que nous devrions savoir...' : 'Anything we should know...' }}">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Staff --}}
            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Personnel qui accédera au système' : 'Staff who will access the system' }}</div>
                <div id="staff-rows">
                    @foreach($staffRows as $i => $row)
                        @include('pages.partials.emr-staff-row', ['i' => $i, 'row' => $row, 'jobRoles' => $jobRoles, 'accessLevels' => $accessLevels, 'locale' => $locale, 'isFr' => $isFr])
                    @endforeach
                </div>
                <button type="button" class="staff-add" id="staff-add">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    {{ $isFr ? 'Ajouter un membre du personnel' : 'Add staff member' }}
                </button>
            </div>

            <button type="submit" class="df-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $isFr ? 'Envoyer les informations' : 'Submit information' }}
            </button>
        </form>
        @endif
    </div>

    <div class="demo-sidebar">
        <div class="demo-info-card">
            <h3>{{ $isFr ? 'Et ensuite ?' : 'What happens next' }}</h3>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Nous recevons vos informations immédiatement' : 'We receive your information right away' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Nous créons un compte EMR pour chaque membre du personnel' : 'We create an EMR account for each staff member' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Vous recevez les identifiants de connexion' : 'You receive the login credentials' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Disponible en anglais et en français' : 'Available in English and French' }}
            </div>
        </div>

        <div class="demo-info-card">
            <h3>{{ $isFr ? 'Besoin d\'aide ?' : 'Need help?' }}</h3>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/></svg>
                {{ config('company.phone') }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                {{ config('company.email') }}
            </div>
        </div>
    </div>

</div>

{{-- Template for a new staff row (cloned by JS; __IDX__ is replaced with the next index) --}}
<template id="staff-row-template">
    @include('pages.partials.emr-staff-row', ['i' => '__IDX__', 'row' => [], 'jobRoles' => $jobRoles, 'accessLevels' => $accessLevels, 'locale' => $locale, 'isFr' => $isFr])
</template>

<script>
(function () {
    var list = document.getElementById('staff-rows');
    var tpl  = document.getElementById('staff-row-template');
    var addBtn = document.getElementById('staff-add');
    if (!list || !tpl || !addBtn) return;

    var nextIndex = {{ count($staffRows) }};

    addBtn.addEventListener('click', function () {
        var html = tpl.innerHTML.replace(/__IDX__/g, String(nextIndex++));
        var wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        var row = wrap.firstElementChild;
        if (row) list.appendChild(row);
    });

    list.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-staff-remove]');
        if (!btn) return;
        var rows = list.querySelectorAll('[data-staff-row]');
        if (rows.length <= 1) {
            // keep at least one row; just clear its inputs
            var row = btn.closest('[data-staff-row]');
            row.querySelectorAll('input').forEach(function (el) {
                if (el.type === 'checkbox') { el.checked = false; } else { el.value = ''; }
            });
            row.querySelectorAll('select').forEach(function (el) { el.selectedIndex = 0; });
            return;
        }
        btn.closest('[data-staff-row]').remove();
    });
})();
</script>

</x-layouts.app>
