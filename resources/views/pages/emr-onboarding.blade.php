@php
    $locale        = app()->getLocale();
    $isFr          = $locale === 'fr';
    $jobRoles      = \App\Models\OnboardingStaff::$jobRoles;
    $accessLevels  = \App\Models\OnboardingStaff::$accessLevels;
    $facilityTypes = \App\Models\HospitalOnboarding::facilityTypes();
    $sizes         = \App\Models\HospitalOnboarding::sizes();
    $servicesOpts  = \App\Models\HospitalOnboarding::$services;
    $languages     = \App\Models\HospitalOnboarding::$workingLanguages;
    $domainChoices = \App\Models\HospitalOnboarding::$emailDomainChoices;
    $patientFmts   = \App\Models\HospitalOnboarding::$patientDataFormats;

    $oldStaff  = old('staff');
    $staffRows = (is_array($oldStaff) && count($oldStaff)) ? array_values($oldStaff) : [[]];
    $oldSites  = old('sites');
    $siteRows  = (is_array($oldSites) && count($oldSites)) ? array_values($oldSites) : [[]];
@endphp

<x-layouts.app :title="$isFr ? 'Intégration EMR — OPES Health Systems' : 'EMR Onboarding — OPES Health Systems'"
               :description="$isFr ? 'Fournissez les informations de votre clinique et de votre personnel pour configurer votre essai EMR en 24 heures.' : 'Provide your clinic and staff information to set up your EMR trial within 24 hours.'">

<style>
.demo-page-hero { background: var(--surface); padding: 72px 48px 56px; text-align: center; border-bottom: 1px solid #1e293b; }
.demo-page-hero h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.demo-page-hero p  { color: var(--text-muted); font-size: 1.05rem; max-width: 600px; margin: 0 auto 28px; line-height: 1.7; }
.demo-steps { display: flex; justify-content: center; gap: 28px; flex-wrap: wrap; margin-top: 8px; }
.demo-step  { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.82rem; }
.demo-step-num { width: 22px; height: 22px; border-radius: 50%; background: #69D5F5; color: #0f172a; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

.demo-layout  { max-width: 960px; margin: 0 auto; padding: 52px 24px 80px; display: grid; grid-template-columns: 1fr 320px; gap: 40px; align-items: start; }
.demo-form-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; }
.demo-form-card h2 { font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 10px; display: flex; align-items: center; gap: 9px; }
.privacy-note { font-size: 0.78rem; color: var(--text-muted); line-height: 1.6; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 12px 14px; margin-bottom: 26px; }
.df-section { margin-bottom: 30px; }
.df-section-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 6px; border-bottom: 1px solid #1e293b; padding-bottom: 8px; }
.df-section-hint { font-size: 0.74rem; color: var(--text-faint); margin: 8px 0 14px; line-height: 1.55; }
.df-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.df-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.df-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.df-group label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); }
.df-group input, .df-group select, .df-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.df-group input[type=file] { padding: 8px 10px; font-size: 0.8rem; color: var(--text-muted); }
.df-group input:focus, .df-group select:focus, .df-group textarea:focus { outline: none; border-color: #69D5F5; }
.df-group select option { background: #0f172a; }
.df-group textarea { min-height: 72px; resize: vertical; }

.check-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.cb-label { display: flex; align-items: center; gap: 8px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 9px 12px; cursor: pointer; font-size: 0.82rem; color: var(--text-muted); transition: all .2s; }
.cb-label:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F510; }
.cb-label input[type=checkbox] { accent-color: #69D5F5; }
.consent-toggle { display: flex; align-items: flex-start; gap: 10px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 12px 14px; cursor: pointer; font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5; }
.consent-toggle:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F510; }
.consent-toggle input { accent-color: #69D5F5; width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }

.staff-row { background: #0b1322; border: 1px solid #1e293b; border-radius: 12px; padding: 18px 18px 8px; margin-bottom: 14px; }
.staff-row-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.staff-row-title { font-size: 0.74rem; font-weight: 700; color: var(--text-muted); letter-spacing: .06em; text-transform: uppercase; }
.staff-remove { display: inline-flex; align-items: center; gap: 5px; background: transparent; border: 1px solid #1e293b; color: var(--text-muted); border-radius: 7px; padding: 5px 10px; font-size: 0.72rem; cursor: pointer; transition: all .2s; }
.staff-remove:hover { border-color: #ef4444; color: #ef4444; }
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
    .df-row, .df-row-3, .check-grid { grid-template-columns: 1fr; }
}
</style>

<div class="demo-page-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
        {{ $isFr ? 'Intégration EMR' : 'EMR Onboarding' }}
    </div>
    <h1>{{ $isFr ? 'Votre essai EMR, prêt en 24 heures' : 'Your EMR trial, ready in 24 hours' }}</h1>
    <p>{{ $isFr ? 'Dès que nous recevons ce formulaire complété et l\'accord d\'essai signé, votre essai gratuit est prêt sous 24 heures, avec les identifiants pour vous et votre personnel.' : 'As soon as we receive this completed form and the signed trial agreement, your free trial is ready within 24 hours, with logins for you and your staff.' }}</p>
    <div class="demo-steps">
        <div class="demo-step"><div class="demo-step-num">1</div>{{ $isFr ? 'Remplissez ce formulaire' : 'Complete this form' }}</div>
        <div class="demo-step" style="color:var(--text-faint)">›</div>
        <div class="demo-step"><div class="demo-step-num">2</div>{{ $isFr ? 'Nous créons les comptes EMR' : 'We create the EMR accounts' }}</div>
        <div class="demo-step" style="color:var(--text-faint)">›</div>
        <div class="demo-step"><div class="demo-step-num">3</div>{{ $isFr ? 'Vous démarrez sous 24h' : 'You start within 24h' }}</div>
    </div>
</div>

<div class="demo-layout">

    <div class="demo-form-card">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ $isFr ? 'Formulaire de configuration de la clinique' : 'Clinic setup form' }}
        </h2>

        @if(session('success'))
        <div class="df-success">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2" style="margin:0 auto 14px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Informations reçues !' : 'Information received!' }}</h3>
            <p>{{ $isFr ? 'Merci ! Nous créons les comptes EMR pour votre personnel. Si quelque chose manque, nous vous le dirons immédiatement afin que rien ne retarde votre démarrage.' : 'Thank you! We will create the EMR accounts for your staff. If anything is missing, we will tell you straight away so nothing delays your start.' }}</p>
        </div>
        @else

        @if($errors->any())
        <div class="df-error">{{ $isFr ? 'Veuillez corriger les champs en surbrillance ci-dessous.' : 'Please correct the highlighted fields below.' }}</div>
        @endif

        <div class="privacy-note">
            {{ $isFr ? 'Nous utilisons ces informations uniquement pour configurer les comptes et l\'essai de votre clinique. Nous ne les partageons avec aucun tiers.' : 'We use this information only to set up your clinic\'s accounts and trial. We do not share it with any third party.' }}
        </div>

        <form action="{{ route('emr-onboarding.submit', $locale) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- A. Authorisation & consent --}}
            <div class="df-section">
                <div class="df-section-label">A · {{ $isFr ? 'Autorisation & consentement' : 'Authorisation & consent' }}</div>
                <div class="df-section-hint">{{ $isFr ? 'Propriétaire ou directeur de la clinique.' : 'Owner or director of the clinic.' }}</div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Propriétaire / Directeur — nom' : 'Owner / Director — name' }}</label>
                        <input type="text" name="owner_name" maxlength="120" value="{{ old('owner_name') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Titre' : 'Title' }}</label>
                        <input type="text" name="owner_title" maxlength="80" value="{{ old('owner_title') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label>
                        <input type="tel" name="owner_phone" maxlength="30" value="{{ old('owner_phone') }}">
                    </div>
                    <div class="df-group">
                        <label>Email</label>
                        <input type="email" name="owner_email" maxlength="150" value="{{ old('owner_email') }}">
                    </div>
                </div>
                <div class="df-section-hint" style="margin-top:4px">{{ $isFr ? 'Personne qui remplit ce formulaire.' : 'Person completing this form.' }}</div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Votre nom' : 'Your name' }} *</label>
                        <input type="text" name="contact_name" required maxlength="120" value="{{ old('contact_name') }}">
                        @error('contact_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Votre fonction' : 'Your role' }}</label>
                        <input type="text" name="completed_by_role" maxlength="80" value="{{ old('completed_by_role') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Votre téléphone' : 'Your phone' }}</label>
                        <input type="tel" name="contact_phone" maxlength="30" value="{{ old('contact_phone') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Votre email' : 'Your email' }} *</label>
                        <input type="email" name="contact_email" required maxlength="150" value="{{ old('contact_email') }}">
                        @error('contact_email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <label class="consent-toggle">
                    <input type="checkbox" name="completer_authorised" value="1" {{ old('completer_authorised') ? 'checked' : '' }}>
                    <span>{{ $isFr ? 'Je suis autorisé(e) à approuver les comptes au nom de la clinique.' : 'I am authorised to approve accounts on behalf of the clinic.' }}</span>
                </label>
                <label class="consent-toggle">
                    <input type="checkbox" name="data_sharing_consent" value="1" required {{ old('data_sharing_consent') ? 'checked' : '' }}>
                    <span>{{ $isFr ? 'Je confirme que la clinique est autorisée à partager les données du personnel et des patients avec OPES pour cet essai.' : 'I confirm the clinic is entitled to share staff and patient data with OPES for this trial.' }} *</span>
                </label>
                @error('data_sharing_consent')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
            </div>

            {{-- B. Clinic identity --}}
            <div class="df-section">
                <div class="df-section-label">B · {{ $isFr ? 'Identité de la clinique' : 'Clinic identity' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Nom courant de la clinique' : 'Clinic common name' }} *</label>
                    <input type="text" name="facility_name" required maxlength="150" value="{{ old('facility_name') }}" placeholder="{{ $isFr ? 'Clinique Joko' : 'Joko Clinic' }}">
                    @error('facility_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Raison sociale' : 'Legal name' }}</label>
                        <input type="text" name="legal_name" maxlength="160" value="{{ old('legal_name') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Nom commercial' : 'Trading name' }}</label>
                        <input type="text" name="trading_name" maxlength="160" value="{{ old('trading_name') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Type' : 'Type' }}</label>
                        <select name="facility_type">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($facilityTypes as $val => $labels)
                            <option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Langue de travail' : 'Working language' }}</label>
                        <select name="working_language">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($languages as $val => $labels)
                            <option value="{{ $val }}" {{ old('working_language') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="df-row-3">
                    <div class="df-group">
                        <label>{{ $isFr ? 'N° d\'enregistrement' : 'Registration no.' }}</label>
                        <input type="text" name="registration_number" maxlength="80" value="{{ old('registration_number') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Date d\'enregistrement' : 'Registration date' }}</label>
                        <input type="date" name="registration_date" value="{{ old('registration_date') }}" aria-label="{{ $isFr ? 'Date d\'enregistrement' : 'Registration date' }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'N° fiscal (NIU)' : 'Tax no. (NIU)' }}</label>
                        <input type="text" name="tax_number" maxlength="60" value="{{ old('tax_number') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'N° de licence d\'exploitation' : 'Operating licence no.' }}</label>
                        <input type="text" name="licence_number" maxlength="80" value="{{ old('licence_number') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Autorité de délivrance' : 'Issuing authority' }}</label>
                        <input type="text" name="licence_authority" maxlength="120" value="{{ old('licence_authority') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Pays' : 'Country' }}</label>
                        <input type="text" name="country" maxlength="60" value="{{ old('country', 'Cameroun') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Ville' : 'Town' }}</label>
                        <input type="text" name="city" maxlength="80" value="{{ old('city') }}">
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Adresse' : 'Address' }}</label>
                    <input type="text" name="address" maxlength="255" value="{{ old('address') }}">
                </div>
                <div class="df-row-3">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Localisation GPS' : 'GPS location' }}</label>
                        <input type="text" name="gps_location" maxlength="120" value="{{ old('gps_location') }}" placeholder="4.0511, 9.7679">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label>
                        <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Email officiel' : 'Official email' }}</label>
                        <input type="email" name="email" maxlength="150" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Site web' : 'Website' }}</label>
                        <input type="text" name="website" maxlength="150" value="{{ old('website') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Heures d\'ouverture' : 'Opening hours' }}</label>
                        <input type="text" name="opening_hours" maxlength="160" value="{{ old('opening_hours') }}" placeholder="{{ $isFr ? 'Lun–Sam 8h–18h' : 'Mon–Sat 8am–6pm' }}">
                    </div>
                </div>
                <div class="df-group" style="margin-top:4px">
                    <label>{{ $isFr ? 'Sites (si plusieurs emplacements)' : 'Sites (if more than one location)' }}</label>
                </div>
                <div id="site-rows">
                    @foreach($siteRows as $i => $row)
                        @include('pages.partials.emr-site-row', ['i' => $i, 'row' => $row, 'locale' => $locale, 'isFr' => $isFr])
                    @endforeach
                </div>
                <button type="button" class="staff-add" id="site-add">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    {{ $isFr ? 'Ajouter un site' : 'Add a site' }}
                </button>
            </div>

            {{-- C. Services --}}
            <div class="df-section">
                <div class="df-section-label">C · {{ $isFr ? 'Services' : 'Services' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Services offerts' : 'Services offered' }}</label>
                    <div class="check-grid">
                        @foreach($servicesOpts as $key => $labels)
                        <label class="cb-label">
                            <input type="checkbox" name="services[]" value="{{ $key }}" {{ in_array($key, old('services', [])) ? 'checked' : '' }}>
                            {{ $labels[$locale] ?? $labels['en'] }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Départements / unités' : 'Departments / units' }}</label>
                    <input type="text" name="departments" maxlength="500" value="{{ old('departments') }}">
                </div>
                <div class="df-row-3">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Lits' : 'Beds' }}</label>
                        <input type="number" min="0" name="beds" value="{{ old('beds') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Salles de consultation' : 'Consulting rooms' }}</label>
                        <input type="number" min="0" name="consulting_rooms" value="{{ old('consulting_rooms') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Patients / jour' : 'Patients / day' }}</label>
                        <input type="number" min="0" name="patients_per_day" value="{{ old('patients_per_day') }}">
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Assureurs acceptés (noms uniquement)' : 'Insurers accepted (names only)' }}</label>
                    <input type="text" name="insurers" maxlength="1000" value="{{ old('insurers') }}">
                </div>
                <div class="df-row-3">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Mobile money (noms)' : 'Mobile money (names)' }}</label>
                        <input type="text" name="mobile_money" maxlength="255" value="{{ old('mobile_money') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'TVA / taxe' : 'VAT / tax rate' }}</label>
                        <input type="text" name="vat_rate" maxlength="20" value="{{ old('vat_rate') }}" placeholder="19.25%">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Devise' : 'Currency' }}</label>
                        <input type="text" name="currency" maxlength="10" value="{{ old('currency', 'XAF') }}">
                    </div>
                </div>
            </div>

            {{-- D. Setup --}}
            <div class="df-section">
                <div class="df-section-label">D · {{ $isFr ? 'Configuration' : 'Setup' }}</div>
                @if($products->isNotEmpty())
                <div class="df-group">
                    <label>{{ $isFr ? 'Produits / modules souhaités' : 'Products / modules wanted' }}</label>
                    <div class="check-grid">
                        @foreach($products as $listedProduct)
                        @php $prod = $listedProduct->name; @endphp
                        <label class="cb-label">
                            <input type="checkbox" name="products[]" value="{{ $prod }}" {{ in_array($prod, old('products', [])) ? 'checked' : '' }}>
                            {{ $prod }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Appareils du personnel (et combien)' : 'Staff devices (and how many)' }}</label>
                        <input type="text" name="devices" maxlength="1000" value="{{ old('devices') }}" placeholder="{{ $isFr ? '3 ordinateurs, 2 tablettes...' : '3 computers, 2 tablets...' }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Fiabilité internet / électricité' : 'Internet / electricity reliability' }}</label>
                        <input type="text" name="connectivity_notes" maxlength="1000" value="{{ old('connectivity_notes') }}">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Domaine email' : 'Email domain' }}</label>
                        <select name="email_domain_choice">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($domainChoices as $val => $labels)
                            <option value="{{ $val }}" {{ old('email_domain_choice') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Votre domaine (le cas échéant)' : 'Your domain (if any)' }}</label>
                        <input type="text" name="email_domain" maxlength="120" value="{{ old('email_domain') }}" placeholder="@jokoclinics.com">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Logiciel / dossiers actuels' : 'Current software / records' }}</label>
                        <input type="text" name="current_software" maxlength="1000" value="{{ old('current_software') }}" placeholder="{{ $isFr ? 'Papier, Excel, autre' : 'Paper, Excel, other' }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Date de démarrage souhaitée' : 'Preferred start date' }}</label>
                        <input type="date" name="preferred_start_date" value="{{ old('preferred_start_date') }}" aria-label="{{ $isFr ? 'Date de démarrage' : 'Start date' }}">
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Qui participera à la formation ?' : 'Who will attend the training?' }}</label>
                    <input type="text" name="training_attendees" maxlength="1000" value="{{ old('training_attendees') }}">
                </div>
            </div>

            {{-- E. Staff --}}
            <div class="df-section">
                <div class="df-section-label">E · {{ $isFr ? 'Personnel' : 'Staff' }}</div>
                <div class="df-section-hint">{{ $isFr ? 'Chaque personne reçoit un compte nommé et un email professionnel ; l\'accès est limité par rôle pour protéger la confidentialité des patients. Les numéros de licence confirment que seul le personnel qualifié prescrit ou signe les résultats.' : 'Each person gets a named account and professional email; access is limited by role to protect patient confidentiality. Licence numbers confirm that only qualified staff prescribe or sign results.' }}</div>
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

            {{-- F. Roles --}}
            <div class="df-section">
                <div class="df-section-label">F · {{ $isFr ? 'Rôles' : 'Roles' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Administrateur principal du système' : 'Main system administrator' }}</label>
                    <input type="text" name="system_admin" maxlength="120" value="{{ old('system_admin') }}">
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Qui peut voir dossiers, factures, stock et rapports ?' : 'Who can see records, bills, stock and reports?' }}</label>
                    <input type="text" name="records_viewers" maxlength="1000" value="{{ old('records_viewers') }}">
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Qui approuve ordonnances et résultats de laboratoire ?' : 'Who approves prescriptions and lab results?' }}</label>
                    <input type="text" name="approval_authority" maxlength="1000" value="{{ old('approval_authority') }}">
                </div>
            </div>

            {{-- G. Documents & data --}}
            <div class="df-section">
                <div class="df-section-label">G · {{ $isFr ? 'Documents & données' : 'Documents & data' }}</div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Logo (PNG ou SVG)' : 'Logo (PNG or SVG)' }}</label>
                        <input type="file" name="logo" accept="image/*">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Cachet' : 'Stamp' }}</label>
                        <input type="file" name="stamp" accept="image/*">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'En-tête' : 'Letterhead' }}</label>
                        <input type="file" name="letterhead" accept="image/*">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Accord d\'essai signé' : 'Signed trial agreement' }}</label>
                        <input type="file" name="signed_agreement" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Liste des services & prix' : 'Service & price list' }}</label>
                        <input type="file" name="service_price_list" accept=".pdf,.xls,.xlsx,.csv,.doc,.docx">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Stock de médicaments' : 'Drug stock' }}</label>
                        <input type="file" name="drug_stock" accept=".pdf,.xls,.xlsx,.csv">
                    </div>
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Liste des tests de laboratoire' : 'Lab test list' }}</label>
                        <input type="file" name="lab_test_list" accept=".pdf,.xls,.xlsx,.csv">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Licence pharmacie (si séparée)' : 'Pharmacy licence (if separate)' }}</label>
                        <input type="file" name="pharmacy_licence" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Licence laboratoire (si séparée)' : 'Lab licence (if separate)' }}</label>
                    <input type="file" name="lab_licence" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="df-section-hint" style="margin-top:14px">
                    <strong style="color:var(--text-muted)">{{ $isFr ? 'Dossiers patients' : 'Patient records' }}:</strong>
                    {{ $isFr ? 'ne les envoyez pas ici. Pour la sécurité, nous vous fournirons un lien de téléversement protégé et importerons d\'abord un échantillon (~20 patients) pour vérification.' : 'do not send them here. For security we will give you a protected upload link and first import a small sample (~20 patients) for you to check.' }}
                </div>
                <div class="df-row-3">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Format des dossiers' : 'Records format' }}</label>
                        <select name="patient_data_format">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach($patientFmts as $val => $labels)
                            <option value="{{ $val }}" {{ old('patient_data_format') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Nombre approx. de patients' : 'Approx. patient count' }}</label>
                        <input type="number" min="0" name="patient_record_count" value="{{ old('patient_record_count') }}">
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Notes' : 'Notes' }}</label>
                        <input type="text" name="patient_data_notes" maxlength="1000" value="{{ old('patient_data_notes') }}">
                    </div>
                </div>
            </div>

            <div class="df-group">
                <label>{{ $isFr ? 'Autre chose que nous devrions savoir ?' : 'Anything else we should know?' }}</label>
                <textarea name="notes" maxlength="2000">{{ old('notes') }}</textarea>
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
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Nous recevons vos informations immédiatement' : 'We receive your information right away' }}</div>
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Un compte EMR nommé par membre du personnel' : 'A named EMR account per staff member' }}</div>
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Essai prêt sous 24h après le formulaire & l\'accord signé' : 'Trial ready within 24h after the form & signed agreement' }}</div>
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Courte session de formation incluse' : 'A short training session included' }}</div>
        </div>

        <div class="demo-info-card">
            <h3>{{ $isFr ? 'Besoin d\'aide ?' : 'Need help?' }}</h3>
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/></svg>{{ config('company.phone') }}</div>
            <div class="demo-info-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>{{ config('company.email') }}</div>
        </div>
    </div>

</div>

<template id="staff-row-template">
    @include('pages.partials.emr-staff-row', ['i' => '__IDX__', 'row' => [], 'jobRoles' => $jobRoles, 'accessLevels' => $accessLevels, 'locale' => $locale, 'isFr' => $isFr])
</template>
<template id="site-row-template">
    @include('pages.partials.emr-site-row', ['i' => '__IDX__', 'row' => [], 'locale' => $locale, 'isFr' => $isFr])
</template>

<script>
(function () {
    function initRepeater(listId, addId, tplId, rowAttr, removeAttr, startCount) {
        var list = document.getElementById(listId);
        var addBtn = document.getElementById(addId);
        var tpl = document.getElementById(tplId);
        if (!list || !addBtn || !tpl) return;
        var next = startCount;

        addBtn.addEventListener('click', function () {
            var html = tpl.innerHTML.replace(/__IDX__/g, String(next++));
            var wrap = document.createElement('div');
            wrap.innerHTML = html.trim();
            var row = wrap.firstElementChild;
            if (row) list.appendChild(row);
        });

        list.addEventListener('click', function (e) {
            var btn = e.target.closest('[' + removeAttr + ']');
            if (!btn) return;
            var rows = list.querySelectorAll('[' + rowAttr + ']');
            if (rows.length <= 1) {
                var row = btn.closest('[' + rowAttr + ']');
                row.querySelectorAll('input').forEach(function (el) { if (el.type === 'checkbox') el.checked = false; else el.value = ''; });
                row.querySelectorAll('select').forEach(function (el) { el.selectedIndex = 0; });
                return;
            }
            btn.closest('[' + rowAttr + ']').remove();
        });
    }

    initRepeater('staff-rows', 'staff-add', 'staff-row-template', 'data-staff-row', 'data-staff-remove', {{ count($staffRows) }});
    initRepeater('site-rows', 'site-add', 'site-row-template', 'data-site-row', 'data-site-remove', {{ count($siteRows) }});
})();
</script>

</x-layouts.app>
