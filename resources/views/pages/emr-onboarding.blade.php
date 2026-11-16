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

    $steps = $isFr
        ? ['Autorisation', 'Clinique', 'Services', 'Config.', 'Personnel', 'Rôles', 'Documents']
        : ['Authorisation', 'Clinic', 'Services', 'Setup', 'Staff', 'Roles', 'Documents'];
@endphp

<x-layouts.app :title="$isFr ? 'Intégration EMR — OPES Health Systems' : 'EMR Onboarding — OPES Health Systems'"
               :description="$isFr ? 'Configurez votre essai EMR : informations de la clinique et du personnel, prêt en 24 heures.' : 'Set up your EMR trial: clinic and staff information, ready in 24 hours.'">

<style>
.ob-hero { background: var(--surface); padding: 56px 24px 40px; text-align: center; border-bottom: 1px solid #1e293b; }
.ob-hero .section-label { justify-content: center; margin-bottom: 14px; }
.ob-hero h1 { font-size: clamp(1.7rem, 3vw, 2.4rem); font-weight: 800; color: #f1f5f9; margin-bottom: 12px; }
.ob-hero p { color: var(--text-muted); font-size: 1rem; max-width: 560px; margin: 0 auto; line-height: 1.65; }

.ob-wrap { max-width: 760px; margin: 0 auto; padding: 40px 20px 72px; }

/* progress */
.ob-progress { display: flex; align-items: flex-start; justify-content: space-between; gap: 4px; margin-bottom: 26px; }
.ob-pill { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 7px; cursor: default; position: relative; }
.ob-pill::before { content: ''; position: absolute; top: 13px; left: -50%; width: 100%; height: 2px; background: #1e293b; z-index: 0; }
.ob-pill:first-child::before { display: none; }
.ob-pill.done::before, .ob-pill.current::before { background: #69D5F5; }
.ob-dot { width: 28px; height: 28px; border-radius: 50%; background: #0f172a; border: 2px solid #1e293b; color: var(--text-faint); font-size: 12px; font-weight: 800; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; transition: all .2s; }
.ob-pill.done .ob-dot { background: #69D5F5; border-color: #69D5F5; color: #0f172a; }
.ob-pill.current .ob-dot { border-color: #69D5F5; color: #69D5F5; box-shadow: 0 0 0 4px #69D5F522; }
.ob-pill.done, .ob-pill.current { cursor: pointer; }
.ob-pill-label { font-size: 0.68rem; font-weight: 600; color: var(--text-faint); text-align: center; line-height: 1.2; }
.ob-pill.current .ob-pill-label { color: #e2e8f0; }

/* card */
.ob-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 18px; padding: 34px 36px; }
.ob-privacy { font-size: 0.78rem; color: var(--text-muted); line-height: 1.6; background: #0f172a; border: 1px solid #1e293b; border-left: 3px solid #69D5F5; border-radius: 8px; padding: 12px 16px; margin-bottom: 28px; }

.ob-step { display: none; animation: obfade .25s ease; }
.ob-step.active { display: block; }
@keyframes obfade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

.ob-head { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.ob-badge { width: 40px; height: 40px; border-radius: 11px; background: #69D5F515; border: 1px solid #69D5F540; color: #69D5F5; font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ob-head h2 { font-size: 1.28rem; font-weight: 800; color: #f1f5f9; margin: 0; }
.ob-sub { font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0 0 24px 54px; }
@media (max-width: 560px){ .ob-sub { margin-left: 0; } }

/* fields */
.df-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.df-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.df-group { display: flex; flex-direction: column; gap: 7px; margin-bottom: 18px; }
.df-group label { font-size: 0.8rem; font-weight: 600; color: #cbd5e1; }
.df-group input, .df-group select, .df-group textarea {
    background: #0f172a; border: 1px solid #243244; border-radius: 9px;
    padding: 11px 14px; color: #e2e8f0; font-size: 0.9rem; font-family: inherit; width: 100%;
    transition: border-color .15s, box-shadow .15s;
}
.df-group input[type=file] { padding: 9px 11px; font-size: 0.82rem; color: var(--text-muted); }
.df-group input:focus, .df-group select:focus, .df-group textarea:focus { outline: none; border-color: #69D5F5; box-shadow: 0 0 0 3px #69D5F520; }
.df-group select option { background: #0f172a; }
.df-group textarea { min-height: 84px; resize: vertical; }
.df-err { color: #fca5a5; font-size: 0.74rem; }
.ob-group-head { font-size: 0.72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-faint); margin: 6px 0 14px; }

/* choice chips */
.check-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
.prod-scroll { max-height: 230px; overflow-y: auto; padding: 4px; border: 1px solid #1e293b; border-radius: 10px; background: #0b1322; }
.cb-label { display: flex; align-items: center; gap: 9px; background: #0f172a; border: 1px solid #243244; border-radius: 9px; padding: 10px 13px; cursor: pointer; font-size: 0.84rem; color: var(--text-muted); transition: all .15s; }
.cb-label:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F512; }
.cb-label input[type=checkbox] { accent-color: #69D5F5; width: 15px; height: 15px; }
.consent-toggle { display: flex; align-items: flex-start; gap: 11px; background: #0f172a; border: 1px solid #243244; border-radius: 10px; padding: 14px 16px; cursor: pointer; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 14px; line-height: 1.55; }
.consent-toggle:has(input:checked) { border-color: #69D5F5; color: #e2e8f0; background: #69D5F512; }
.consent-toggle input { accent-color: #69D5F5; width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; }

/* repeater rows */
.staff-row { background: #0b1322; border: 1px solid #1e293b; border-radius: 13px; padding: 20px 20px 6px; margin-bottom: 16px; }
.staff-row-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.staff-row-title { font-size: 0.74rem; font-weight: 700; color: #94a3b8; letter-spacing: .06em; text-transform: uppercase; }
.staff-remove { display: inline-flex; align-items: center; gap: 5px; background: transparent; border: 1px solid #243244; color: var(--text-muted); border-radius: 8px; padding: 6px 11px; font-size: 0.74rem; cursor: pointer; transition: all .15s; }
.staff-remove:hover { border-color: #ef4444; color: #ef4444; }
.staff-add { display: inline-flex; align-items: center; gap: 8px; background: #0f172a; border: 1px dashed #69D5F560; color: #69D5F5; border-radius: 10px; padding: 12px 20px; font-size: 0.86rem; font-weight: 600; cursor: pointer; transition: all .15s; }
.staff-add:hover { background: #69D5F512; border-color: #69D5F5; }

/* nav */
.ob-nav { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 30px; padding-top: 22px; border-top: 1px solid #1e293b; }
.ob-counter { font-size: 0.8rem; color: var(--text-faint); font-weight: 600; }
.ob-btn { border: none; border-radius: 10px; padding: 12px 26px; font-size: 0.9rem; font-weight: 700; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 8px; transition: all .15s; }
.ob-btn-ghost { background: transparent; border: 1px solid #243244; color: var(--text-muted); }
.ob-btn-ghost:hover { border-color: #475569; color: #e2e8f0; }
.ob-btn-primary { background: #69D5F5; color: #0f172a; }
.ob-btn-primary:hover { background: #00b386; }

/* footer info */
.ob-foot { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 24px; }
.ob-foot-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 12px; padding: 18px 20px; }
.ob-foot-card h3 { font-size: 0.82rem; font-weight: 700; color: #f1f5f9; margin: 0 0 12px; }
.ob-foot-row { display: flex; align-items: flex-start; gap: 9px; margin-bottom: 10px; font-size: 0.8rem; color: var(--text-muted); line-height: 1.45; }
.ob-foot-row svg { flex-shrink: 0; margin-top: 1px; color: var(--brand-accent); }

.ob-success { background: #69D5F514; border: 1px solid #69D5F540; border-radius: 16px; padding: 40px 32px; text-align: center; }
.ob-success h3 { color: var(--brand-accent); font-size: 1.3rem; margin-bottom: 12px; }
.ob-success p { color: var(--text-muted); font-size: 0.9rem; line-height: 1.65; max-width: 460px; margin: 0 auto; }
.ob-errbox { background: #ef444414; border: 1px solid #ef444440; border-radius: 10px; padding: 13px 18px; margin-bottom: 22px; color: #fca5a5; font-size: 0.82rem; }

@media (max-width: 620px){
    .ob-card { padding: 26px 20px; }
    .df-row, .df-row-3, .check-grid, .ob-foot { grid-template-columns: 1fr; }
    .ob-pill-label { display: none; }
}
</style>

<div class="ob-hero">
    <div class="section-label">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
        {{ $isFr ? 'Intégration EMR' : 'EMR Onboarding' }}
    </div>
    <h1>{{ $isFr ? 'Votre essai EMR, prêt en 24 heures' : 'Your EMR trial, ready in 24 hours' }}</h1>
    <p>{{ $isFr ? 'Renseignez votre clinique et votre personnel. Dès réception du formulaire et de l\'accord signé, nous créons tous les comptes EMR.' : 'Tell us about your clinic and staff. Once we receive the form and the signed agreement, we create all the EMR accounts.' }}</p>
</div>

<div class="ob-wrap">
    @if(session('success'))
    <div class="ob-card">
        <div class="ob-success">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#69D5F5" stroke-width="2" style="margin:0 auto 16px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Informations reçues !' : 'Information received!' }}</h3>
            <p>{{ $isFr ? 'Merci ! Nous créons les comptes EMR pour votre personnel. Si quelque chose manque, nous vous le dirons immédiatement afin que rien ne retarde votre démarrage.' : 'Thank you! We will create the EMR accounts for your staff. If anything is missing, we will tell you straight away so nothing delays your start.' }}</p>
        </div>
    </div>
    @else

    <div class="ob-progress">
        @foreach($steps as $si => $label)
        <div class="ob-pill" data-pill="{{ $si }}">
            <div class="ob-dot">{{ $si + 1 }}</div>
            <div class="ob-pill-label">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <div class="ob-card">
        @if($errors->any())
        <div class="ob-errbox">{{ $isFr ? 'Veuillez corriger les champs en surbrillance.' : 'Please correct the highlighted fields.' }}</div>
        @endif

        <div class="ob-privacy">
            {{ $isFr ? 'Nous utilisons ces informations uniquement pour configurer les comptes et l\'essai de votre clinique. Nous ne les partageons avec aucun tiers.' : 'We use this information only to set up your clinic\'s accounts and trial. We do not share it with any third party.' }}
        </div>

        <form action="{{ route('emr-onboarding.submit', $locale) }}" method="POST" enctype="multipart/form-data" id="ob-form">
            @csrf

            {{-- STEP A --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">A</div><h2>{{ $isFr ? 'Autorisation & consentement' : 'Authorisation & consent' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Le propriétaire de la clinique et la personne qui remplit ce formulaire.' : 'The clinic owner and the person completing this form.' }}</p>

                <div class="ob-group-head">{{ $isFr ? 'Propriétaire / Directeur' : 'Owner / Director' }}</div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Nom' : 'Name' }}</label><input type="text" name="owner_name" maxlength="120" value="{{ old('owner_name') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Titre' : 'Title' }}</label><input type="text" name="owner_title" maxlength="80" value="{{ old('owner_title') }}"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label><input type="tel" name="owner_phone" maxlength="30" value="{{ old('owner_phone') }}"></div>
                    <div class="df-group"><label>Email</label><input type="email" name="owner_email" maxlength="150" value="{{ old('owner_email') }}"></div>
                </div>

                <div class="ob-group-head" style="margin-top:18px">{{ $isFr ? 'Personne remplissant le formulaire' : 'Person completing the form' }}</div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Votre nom' : 'Your name' }} *</label><input type="text" name="contact_name" required maxlength="120" value="{{ old('contact_name') }}">@error('contact_name')<span class="df-err">{{ $message }}</span>@enderror</div>
                    <div class="df-group"><label>{{ $isFr ? 'Votre fonction' : 'Your role' }}</label><input type="text" name="completed_by_role" maxlength="80" value="{{ old('completed_by_role') }}"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Votre téléphone' : 'Your phone' }}</label><input type="tel" name="contact_phone" maxlength="30" value="{{ old('contact_phone') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Votre email' : 'Your email' }} *</label><input type="email" name="contact_email" required maxlength="150" value="{{ old('contact_email') }}">@error('contact_email')<span class="df-err">{{ $message }}</span>@enderror</div>
                </div>

                <label class="consent-toggle" style="margin-top:8px">
                    <input type="checkbox" name="completer_authorised" value="1" {{ old('completer_authorised') ? 'checked' : '' }}>
                    <span>{{ $isFr ? 'Je suis autorisé(e) à approuver les comptes au nom de la clinique.' : 'I am authorised to approve accounts on behalf of the clinic.' }}</span>
                </label>
                <label class="consent-toggle">
                    <input type="checkbox" name="data_sharing_consent" value="1" required {{ old('data_sharing_consent') ? 'checked' : '' }}>
                    <span>{{ $isFr ? 'Je confirme que la clinique est autorisée à partager les données du personnel et des patients avec OPES pour cet essai.' : 'I confirm the clinic is entitled to share staff and patient data with OPES for this trial.' }} *</span>
                </label>
                @error('data_sharing_consent')<span class="df-err">{{ $message }}</span>@enderror
            </div>

            {{-- STEP B --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">B</div><h2>{{ $isFr ? 'Identité de la clinique' : 'Clinic identity' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Coordonnées légales et administratives de votre établissement.' : 'Your facility\'s legal and administrative details.' }}</p>

                <div class="df-group"><label>{{ $isFr ? 'Nom courant de la clinique' : 'Clinic common name' }} *</label><input type="text" name="facility_name" required maxlength="150" value="{{ old('facility_name') }}" placeholder="{{ $isFr ? 'Clinique Joko' : 'Joko Clinic' }}">@error('facility_name')<span class="df-err">{{ $message }}</span>@enderror</div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Raison sociale' : 'Legal name' }}</label><input type="text" name="legal_name" maxlength="160" value="{{ old('legal_name') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Nom commercial' : 'Trading name' }}</label><input type="text" name="trading_name" maxlength="160" value="{{ old('trading_name') }}"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Type' : 'Type' }}</label>
                        <select name="facility_type"><option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>@foreach($facilityTypes as $val => $labels)<option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>@endforeach</select>
                    </div>
                    <div class="df-group"><label>{{ $isFr ? 'Langue de travail' : 'Working language' }}</label>
                        <select name="working_language"><option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>@foreach($languages as $val => $labels)<option value="{{ $val }}" {{ old('working_language') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>@endforeach</select>
                    </div>
                </div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'N° d\'enregistrement' : 'Registration no.' }}</label><input type="text" name="registration_number" maxlength="80" value="{{ old('registration_number') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Date' : 'Date' }}</label><input type="date" name="registration_date" value="{{ old('registration_date') }}" aria-label="{{ $isFr ? 'Date d\'enregistrement' : 'Registration date' }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'N° fiscal (NIU)' : 'Tax no. (NIU)' }}</label><input type="text" name="tax_number" maxlength="60" value="{{ old('tax_number') }}"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'N° de licence d\'exploitation' : 'Operating licence no.' }}</label><input type="text" name="licence_number" maxlength="80" value="{{ old('licence_number') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Autorité de délivrance' : 'Issuing authority' }}</label><input type="text" name="licence_authority" maxlength="120" value="{{ old('licence_authority') }}"></div>
                </div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'Pays' : 'Country' }}</label><input type="text" name="country" maxlength="60" value="{{ old('country', 'Cameroun') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Ville' : 'Town' }}</label><input type="text" name="city" maxlength="80" value="{{ old('city') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'GPS' : 'GPS' }}</label><input type="text" name="gps_location" maxlength="120" value="{{ old('gps_location') }}" placeholder="4.05, 9.76"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Adresse' : 'Address' }}</label><input type="text" name="address" maxlength="255" value="{{ old('address') }}"></div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label><input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Email officiel' : 'Official email' }}</label><input type="email" name="email" maxlength="150" value="{{ old('email') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Site web' : 'Website' }}</label><input type="text" name="website" maxlength="150" value="{{ old('website') }}"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Heures d\'ouverture' : 'Opening hours' }}</label><input type="text" name="opening_hours" maxlength="160" value="{{ old('opening_hours') }}" placeholder="{{ $isFr ? 'Lun–Sam 8h–18h' : 'Mon–Sat 8am–6pm' }}"></div>

                <div class="ob-group-head" style="margin-top:18px">{{ $isFr ? 'Sites (si plusieurs emplacements)' : 'Sites (if more than one location)' }}</div>
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

            {{-- STEP C --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">C</div><h2>{{ $isFr ? 'Services' : 'Services' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Ce que propose votre clinique et vos paramètres de facturation.' : 'What your clinic offers and your billing settings.' }}</p>

                <div class="df-group">
                    <label>{{ $isFr ? 'Services offerts' : 'Services offered' }}</label>
                    <div class="check-grid">
                        @foreach($servicesOpts as $key => $labels)
                        <label class="cb-label"><input type="checkbox" name="services[]" value="{{ $key }}" {{ in_array($key, old('services', [])) ? 'checked' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</label>
                        @endforeach
                    </div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Départements / unités' : 'Departments / units' }}</label><input type="text" name="departments" maxlength="500" value="{{ old('departments') }}"></div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'Lits' : 'Beds' }}</label><input type="number" min="0" name="beds" value="{{ old('beds') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Salles de consult.' : 'Consult. rooms' }}</label><input type="number" min="0" name="consulting_rooms" value="{{ old('consulting_rooms') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Patients / jour' : 'Patients / day' }}</label><input type="number" min="0" name="patients_per_day" value="{{ old('patients_per_day') }}"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Assureurs acceptés (noms uniquement)' : 'Insurers accepted (names only)' }}</label><input type="text" name="insurers" maxlength="1000" value="{{ old('insurers') }}"></div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'Mobile money (noms)' : 'Mobile money (names)' }}</label><input type="text" name="mobile_money" maxlength="255" value="{{ old('mobile_money') }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'TVA / taxe' : 'VAT / tax' }}</label><input type="text" name="vat_rate" maxlength="20" value="{{ old('vat_rate') }}" placeholder="19.25%"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Devise' : 'Currency' }}</label><input type="text" name="currency" maxlength="10" value="{{ old('currency', 'XAF') }}"></div>
                </div>
            </div>

            {{-- STEP D --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">D</div><h2>{{ $isFr ? 'Configuration' : 'Setup' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Matériel, connectivité et modules souhaités pour votre essai.' : 'Devices, connectivity and the modules you want for your trial.' }}</p>

                @if($products->isNotEmpty())
                <div class="df-group">
                    <label>{{ $isFr ? 'Produits / modules souhaités' : 'Products / modules wanted' }}</label>
                    <div class="prod-scroll"><div class="check-grid">
                        @foreach($products as $listedProduct)
                        @php $prod = $listedProduct->name; @endphp
                        <label class="cb-label"><input type="checkbox" name="products[]" value="{{ $prod }}" {{ in_array($prod, old('products', [])) ? 'checked' : '' }}>{{ $prod }}</label>
                        @endforeach
                    </div></div>
                </div>
                @endif
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Appareils du personnel (et combien)' : 'Staff devices (and how many)' }}</label><input type="text" name="devices" maxlength="1000" value="{{ old('devices') }}" placeholder="{{ $isFr ? '3 ordinateurs, 2 tablettes...' : '3 computers, 2 tablets...' }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Fiabilité internet / électricité' : 'Internet / electricity reliability' }}</label><input type="text" name="connectivity_notes" maxlength="1000" value="{{ old('connectivity_notes') }}"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Domaine email' : 'Email domain' }}</label>
                        <select name="email_domain_choice"><option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>@foreach($domainChoices as $val => $labels)<option value="{{ $val }}" {{ old('email_domain_choice') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>@endforeach</select>
                    </div>
                    <div class="df-group"><label>{{ $isFr ? 'Votre domaine (le cas échéant)' : 'Your domain (if any)' }}</label><input type="text" name="email_domain" maxlength="120" value="{{ old('email_domain') }}" placeholder="@jokoclinics.com"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Logiciel / dossiers actuels' : 'Current software / records' }}</label><input type="text" name="current_software" maxlength="1000" value="{{ old('current_software') }}" placeholder="{{ $isFr ? 'Papier, Excel...' : 'Paper, Excel...' }}"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Date de démarrage souhaitée' : 'Preferred start date' }}</label><input type="date" name="preferred_start_date" value="{{ old('preferred_start_date') }}" aria-label="{{ $isFr ? 'Date de démarrage' : 'Start date' }}"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Qui participera à la formation ?' : 'Who will attend the training?' }}</label><input type="text" name="training_attendees" maxlength="1000" value="{{ old('training_attendees') }}"></div>
            </div>

            {{-- STEP E --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">E</div><h2>{{ $isFr ? 'Personnel' : 'Staff' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Chaque personne reçoit un compte nommé ; l\'accès est limité par rôle. Les numéros de licence confirment que seul le personnel qualifié prescrit ou signe les résultats.' : 'Each person gets a named account; access is limited by role. Licence numbers confirm that only qualified staff prescribe or sign results.' }}</p>
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

            {{-- STEP F --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">F</div><h2>{{ $isFr ? 'Rôles' : 'Roles' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Qui administre, consulte et approuve dans le système.' : 'Who administers, views and approves in the system.' }}</p>
                <div class="df-group"><label>{{ $isFr ? 'Administrateur principal du système' : 'Main system administrator' }}</label><input type="text" name="system_admin" maxlength="120" value="{{ old('system_admin') }}"></div>
                <div class="df-group"><label>{{ $isFr ? 'Qui peut voir dossiers, factures, stock et rapports ?' : 'Who can see records, bills, stock and reports?' }}</label><input type="text" name="records_viewers" maxlength="1000" value="{{ old('records_viewers') }}"></div>
                <div class="df-group"><label>{{ $isFr ? 'Qui approuve ordonnances et résultats de laboratoire ?' : 'Who approves prescriptions and lab results?' }}</label><input type="text" name="approval_authority" maxlength="1000" value="{{ old('approval_authority') }}"></div>
            </div>

            {{-- STEP G --}}
            <div class="ob-step" data-step>
                <div class="ob-head"><div class="ob-badge">G</div><h2>{{ $isFr ? 'Documents & données' : 'Documents & data' }}</h2></div>
                <p class="ob-sub">{{ $isFr ? 'Téléversez ce que vous avez maintenant — le reste peut suivre.' : 'Upload what you have now — the rest can follow.' }}</p>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Logo (PNG ou SVG)' : 'Logo (PNG or SVG)' }}</label><input type="file" name="logo" accept="image/*"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Cachet' : 'Stamp' }}</label><input type="file" name="stamp" accept="image/*"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'En-tête' : 'Letterhead' }}</label><input type="file" name="letterhead" accept="image/*"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Accord d\'essai signé' : 'Signed trial agreement' }}</label><input type="file" name="signed_agreement" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Liste des services & prix' : 'Service & price list' }}</label><input type="file" name="service_price_list" accept=".pdf,.xls,.xlsx,.csv,.doc,.docx"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Stock de médicaments' : 'Drug stock' }}</label><input type="file" name="drug_stock" accept=".pdf,.xls,.xlsx,.csv"></div>
                </div>
                <div class="df-row">
                    <div class="df-group"><label>{{ $isFr ? 'Liste des tests de laboratoire' : 'Lab test list' }}</label><input type="file" name="lab_test_list" accept=".pdf,.xls,.xlsx,.csv"></div>
                    <div class="df-group"><label>{{ $isFr ? 'Licence pharmacie (si séparée)' : 'Pharmacy licence (if separate)' }}</label><input type="file" name="pharmacy_licence" accept=".pdf,.jpg,.jpeg,.png"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Licence laboratoire (si séparée)' : 'Lab licence (if separate)' }}</label><input type="file" name="lab_licence" accept=".pdf,.jpg,.jpeg,.png"></div>

                <div class="ob-privacy" style="border-left-color:#f59e0b;margin:18px 0">
                    <strong style="color:#e2e8f0">{{ $isFr ? 'Dossiers patients' : 'Patient records' }}:</strong>
                    {{ $isFr ? ' ne les envoyez pas ici. Pour la sécurité, nous vous fournirons un lien de téléversement protégé et importerons d\'abord un échantillon (~20 patients) pour vérification.' : ' do not send them here. For security we will give you a protected upload link and first import a small sample (~20 patients) for you to check.' }}
                </div>
                <div class="df-row-3">
                    <div class="df-group"><label>{{ $isFr ? 'Format des dossiers' : 'Records format' }}</label>
                        <select name="patient_data_format"><option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>@foreach($patientFmts as $val => $labels)<option value="{{ $val }}" {{ old('patient_data_format') === $val ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>@endforeach</select>
                    </div>
                    <div class="df-group"><label>{{ $isFr ? 'Nb. de patients' : 'Patient count' }}</label><input type="number" min="0" name="patient_record_count" value="{{ old('patient_record_count') }}"></div>
                    <div class="df-group"><label>Notes</label><input type="text" name="patient_data_notes" maxlength="1000" value="{{ old('patient_data_notes') }}"></div>
                </div>
                <div class="df-group"><label>{{ $isFr ? 'Autre chose que nous devrions savoir ?' : 'Anything else we should know?' }}</label><textarea name="notes" maxlength="2000">{{ old('notes') }}</textarea></div>
            </div>

            <div class="ob-nav">
                <button type="button" class="ob-btn ob-btn-ghost" id="ob-back">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    {{ $isFr ? 'Retour' : 'Back' }}
                </button>
                <span class="ob-counter" id="ob-counter"></span>
                <button type="button" class="ob-btn ob-btn-primary" id="ob-next">
                    {{ $isFr ? 'Suivant' : 'Next' }}
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <button type="submit" class="ob-btn ob-btn-primary" id="ob-submit" style="display:none">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $isFr ? 'Envoyer' : 'Submit' }}
                </button>
            </div>
        </form>
    </div>

    <div class="ob-foot">
        <div class="ob-foot-card">
            <h3>{{ $isFr ? 'Et ensuite ?' : 'What happens next' }}</h3>
            <div class="ob-foot-row"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Un compte EMR nommé par membre du personnel' : 'A named EMR account per staff member' }}</div>
            <div class="ob-foot-row"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Essai prêt sous 24h après le formulaire & l\'accord signé' : 'Trial ready within 24h after the form & signed agreement' }}</div>
            <div class="ob-foot-row"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ $isFr ? 'Courte session de formation incluse' : 'A short training session included' }}</div>
        </div>
        <div class="ob-foot-card">
            <h3>{{ $isFr ? 'Besoin d\'aide ?' : 'Need help?' }}</h3>
            <div class="ob-foot-row"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/></svg>{{ config('company.phone') }}</div>
            <div class="ob-foot-row"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>{{ config('company.email') }}</div>
        </div>
    </div>
    @endif
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
        var list = document.getElementById(listId), addBtn = document.getElementById(addId), tpl = document.getElementById(tplId);
        if (!list || !addBtn || !tpl) return;
        var next = startCount;
        addBtn.addEventListener('click', function () {
            var html = tpl.innerHTML.replace(/__IDX__/g, String(next++));
            var wrap = document.createElement('div'); wrap.innerHTML = html.trim();
            var row = wrap.firstElementChild; if (row) list.appendChild(row);
        });
        list.addEventListener('click', function (e) {
            var btn = e.target.closest('[' + removeAttr + ']'); if (!btn) return;
            var rows = list.querySelectorAll('[' + rowAttr + ']');
            if (rows.length <= 1) {
                var row = btn.closest('[' + rowAttr + ']');
                row.querySelectorAll('input').forEach(function (el){ if (el.type==='checkbox'||el.type==='file') el.value=''; else el.value=''; if(el.type==='checkbox') el.checked=false; });
                row.querySelectorAll('select').forEach(function (el){ el.selectedIndex = 0; });
                return;
            }
            btn.closest('[' + rowAttr + ']').remove();
        });
    }

    var form = document.getElementById('ob-form');
    if (form) {
        var steps = Array.prototype.slice.call(form.querySelectorAll('.ob-step'));
        var pills = Array.prototype.slice.call(document.querySelectorAll('[data-pill]'));
        var back = document.getElementById('ob-back'), next = document.getElementById('ob-next'),
            submit = document.getElementById('ob-submit'), counter = document.getElementById('ob-counter');
        var cur = 0;
        function render() {
            steps.forEach(function (s, i){ s.classList.toggle('active', i === cur); });
            pills.forEach(function (p, i){ p.classList.toggle('done', i < cur); p.classList.toggle('current', i === cur); });
            back.style.visibility = cur === 0 ? 'hidden' : 'visible';
            var last = cur === steps.length - 1;
            next.style.display = last ? 'none' : '';
            submit.style.display = last ? '' : 'none';
            counter.textContent = '{{ $isFr ? "Étape" : "Step" }} ' + (cur + 1) + ' / ' + steps.length;
        }
        function go(i, scroll) {
            cur = Math.max(0, Math.min(steps.length - 1, i)); render();
            if (scroll !== false) { var y = form.getBoundingClientRect().top + window.pageYOffset - 90; window.scrollTo({ top: y, behavior: 'smooth' }); }
        }
        function stepValid(i) {
            var f = steps[i].querySelectorAll('input, select, textarea');
            for (var j = 0; j < f.length; j++) { if (!f[j].checkValidity()) { f[j].reportValidity(); return false; } }
            return true;
        }
        next.addEventListener('click', function () { if (stepValid(cur)) go(cur + 1); });
        back.addEventListener('click', function () { go(cur - 1); });
        pills.forEach(function (p, i){ p.addEventListener('click', function (){ if (i <= cur) go(i); }); });
        form.addEventListener('submit', function (e) {
            for (var i = 0; i < steps.length; i++) {
                var f = steps[i].querySelectorAll('input, select, textarea');
                for (var j = 0; j < f.length; j++) { if (!f[j].checkValidity()) { e.preventDefault(); go(i); f[j].reportValidity(); return; } }
            }
        });
        // Jump to the first step that has a server-side validation message
        var firstErr = form.querySelector('.df-err');
        var startAt = 0;
        if (firstErr) { for (var k = 0; k < steps.length; k++) { if (steps[k].contains(firstErr)) { startAt = k; break; } } }
        go(startAt, false);
    }

    initRepeater('staff-rows', 'staff-add', 'staff-row-template', 'data-staff-row', 'data-staff-remove', {{ count($staffRows) }});
    initRepeater('site-rows', 'site-add', 'site-row-template', 'data-site-row', 'data-site-remove', {{ count($siteRows) }});
})();
</script>

</x-layouts.app>
