@php
$locale = app()->getLocale();
$isFr   = $locale === 'fr';
@endphp

<x-layouts.app :title="$isFr ? 'Rejoindre les testeurs — OPES Health Systems' : 'Join Beta Testers — OPES Health Systems'"
               :description="$isFr ? 'Rejoignez le programme de test OPES et aidez à façonner l\'avenir des logiciels de santé africains.' : 'Join the OPES beta testing programme and help shape the future of African health software.'">

<style>
.testers-hero { background: linear-gradient(135deg, #080F1E 0%, #0c1220 100%); padding: 72px 48px 56px; text-align: center; border-bottom: 1px solid #1e293b; }
.testers-hero h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.testers-hero p  { color: var(--text-muted); font-size: 1.05rem; max-width: 560px; margin: 0 auto; line-height: 1.7; }

.tester-perks { display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; margin: 36px auto 0; max-width: 700px; padding: 0 24px; }
.tester-perk { display: flex; align-items: center; gap: 8px; background: #080e1a; border: 1px solid #1e293b; border-radius: 8px; padding: 10px 16px; font-size: 0.8rem; color: var(--text-muted); }
.tester-perk svg { color: #F59E0B; flex-shrink: 0; }

.testers-layout { max-width: 900px; margin: 0 auto; padding: 52px 24px 80px; display: grid; grid-template-columns: 1fr 300px; gap: 40px; align-items: start; }
.testers-form-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; }
.testers-form-card h2 { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; margin-bottom: 28px; display: flex; align-items: center; gap: 9px; }
.tf-section { margin-bottom: 24px; }
.tf-section-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid #1e293b; padding-bottom: 8px; }
.tf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.tf-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.tf-group label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); }
.tf-group input, .tf-group select, .tf-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.tf-group input:focus, .tf-group select:focus, .tf-group textarea:focus { outline: none; border-color: #F59E0B; }
.tf-group textarea { min-height: 100px; resize: vertical; }

.tf-checkbox-group { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.tf-cb-label { display: flex; align-items: center; gap: 7px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 8px 10px; cursor: pointer; font-size: 0.78rem; color: var(--text-muted); transition: all .2s; }
.tf-cb-label:has(input:checked) { border-color: #F59E0B; color: #e2e8f0; background: #F59E0B10; }
.tf-cb-label input[type=checkbox] { accent-color: #F59E0B; flex-shrink: 0; }

.tf-submit { background: #F59E0B; color: #0f172a; border: none; border-radius: 9px; padding: 13px 32px; font-size: 0.92rem; font-weight: 700; cursor: pointer; width: 100%; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
.tf-submit:hover { background: #d97706; }
.tf-success { background: #F59E0B14; border: 1px solid #F59E0B40; border-radius: 10px; padding: 28px; text-align: center; }
.tf-success h3 { color: #F59E0B; font-size: 1.2rem; margin-bottom: 10px; }
.tf-success p  { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }

.testers-sidebar { display: flex; flex-direction: column; gap: 18px; }
.testers-info-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 22px; }
.testers-info-card h3 { font-size: 0.88rem; font-weight: 700; color: #f1f5f9; margin-bottom: 14px; }
.tf-benefit { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; }
.tf-benefit svg { flex-shrink: 0; margin-top: 1px; color: #F59E0B; }

@media (max-width: 768px) {
    .testers-hero { padding: 48px 20px 40px; }
    .testers-layout { grid-template-columns: 1fr; padding: 32px 16px 60px; gap: 28px; }
    .tf-row { grid-template-columns: 1fr; }
    .tf-checkbox-group { grid-template-columns: 1fr 1fr; }
    .tester-perks { gap: 12px; }
}
</style>

<div class="testers-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px;color:#F59E0B">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"/><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"/><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"/><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"/><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"/></svg>
        {{ $isFr ? 'Programme Beta Testeurs' : 'Beta Tester Programme' }}
    </div>
    <h1>{{ $isFr ? 'Testez les logiciels de santé de demain' : 'Test tomorrow\'s health software today' }}</h1>
    <p>{{ $isFr ? 'En tant que professionnel de santé, votre retour façonne directement la qualité de nos produits. Rejoignez notre programme de testeurs et soyez parmi les premiers à utiliser OPES.' : 'As a health professional, your feedback directly shapes the quality of our products. Join our beta programme and be among the first to use OPES.' }}</p>

    <div class="tester-perks">
        @foreach([
            ['en' => 'Early product access', 'fr' => 'Accès anticipé aux produits'],
            ['en' => 'Impact product roadmap', 'fr' => 'Influencez la feuille de route'],
            ['en' => 'Certificate of participation', 'fr' => 'Certificat de participation'],
            ['en' => 'Stipend for active testers', 'fr' => 'Indemnité pour testeurs actifs'],
        ] as $p)
        <div class="tester-perk">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            {{ $isFr ? $p['fr'] : $p['en'] }}
        </div>
        @endforeach
    </div>
</div>

<div class="testers-layout">

    <div class="testers-form-card">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ $isFr ? 'Formulaire de candidature' : 'Application Form' }}
        </h2>

        @if(session('success'))
        <div class="tf-success">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" style="margin:0 auto 14px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Candidature envoyée !' : 'Application submitted!' }}</h3>
            <p>{{ $isFr ? 'Merci ! Notre équipe examinera votre candidature et vous contactera si votre profil correspond à nos besoins actuels de test.' : 'Thank you! Our team will review your application and contact you if your profile matches our current testing needs.' }}</p>
        </div>
        @else

        <form action="{{ route('join-testers.submit', $locale) }}" method="POST">
            @csrf

            <div class="tf-section">
                <div class="tf-section-label">{{ $isFr ? 'Informations personnelles' : 'Personal information' }}</div>
                <div class="tf-row">
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Nom complet' : 'Full name' }} *</label>
                        <input type="text" name="name" required maxlength="100" value="{{ old('name') }}">
                        @error('name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="tf-group">
                        <label>Email *</label>
                        <input type="email" name="email" required maxlength="150" value="{{ old('email') }}">
                        @error('email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="tf-row">
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Téléphone / WhatsApp' : 'Phone / WhatsApp' }}</label>
                        <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}">
                    </div>
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Années d\'expérience' : 'Years of experience' }} *</label>
                        <input type="number" name="years_experience" required min="0" max="50" value="{{ old('years_experience', 0) }}">
                        @error('years_experience')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="tf-section">
                <div class="tf-section-label">{{ $isFr ? 'Profil professionnel' : 'Professional profile' }}</div>
                <div class="tf-row">
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Profession' : 'Profession' }} *</label>
                        <select name="profession" required>
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach(\App\Models\TesterApplication::$professions as $val => $labels)
                            <option value="{{ $val }}" {{ old('profession') === $val ? 'selected' : '' }}>
                                {{ $labels[$locale] ?? $labels['en'] }}
                            </option>
                            @endforeach
                        </select>
                        @error('profession')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Spécialité' : 'Specialty' }}</label>
                        <input type="text" name="specialty" maxlength="100" value="{{ old('specialty') }}" placeholder="{{ $isFr ? 'Ex: Cardiologie, Pédiatrie' : 'e.g. Cardiology, Paediatrics' }}">
                    </div>
                </div>
                <div class="tf-group">
                    <label>{{ $isFr ? 'Nom de l\'établissement' : 'Institution name' }}</label>
                    <input type="text" name="institution_name" maxlength="150" value="{{ old('institution_name') }}" placeholder="{{ $isFr ? 'Hôpital, clinique, pharmacie...' : 'Hospital, clinic, pharmacy...' }}">
                </div>
                <div class="tf-row">
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Pays' : 'Country' }} *</label>
                        <input type="text" name="country" required maxlength="60" value="{{ old('country') }}">
                        @error('country')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="tf-group">
                        <label>{{ $isFr ? 'Ville' : 'City' }}</label>
                        <input type="text" name="city" maxlength="60" value="{{ old('city') }}">
                    </div>
                </div>
            </div>

            <div class="tf-section">
                <div class="tf-section-label">{{ $isFr ? 'Profil technique' : 'Technical profile' }}</div>
                <div class="tf-group">
                    <label>{{ $isFr ? 'Appareils utilisés' : 'Devices you use' }}</label>
                    <div class="tf-checkbox-group">
                        @foreach(['smartphone' => ['en'=>'Smartphone','fr'=>'Smartphone'], 'tablet' => ['en'=>'Tablet','fr'=>'Tablette'], 'laptop' => ['en'=>'Laptop','fr'=>'Ordinateur portable'], 'desktop' => ['en'=>'Desktop','fr'=>'Ordinateur fixe']] as $val => $labels)
                        <label class="tf-cb-label">
                            <input type="checkbox" name="devices[]" value="{{ $val }}" {{ in_array($val, old('devices', [])) ? 'checked' : '' }}>
                            {{ $isFr ? $labels['fr'] : $labels['en'] }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="tf-group" style="margin-top:12px">
                    <label>{{ $isFr ? 'Systèmes d\'exploitation' : 'Operating systems' }}</label>
                    <div class="tf-checkbox-group">
                        @foreach(['android' => 'Android', 'ios' => 'iOS', 'windows' => 'Windows', 'macos' => 'macOS', 'web' => 'Web Browser'] as $val => $label)
                        <label class="tf-cb-label">
                            <input type="checkbox" name="platforms[]" value="{{ $val }}" {{ in_array($val, old('platforms', [])) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="tf-group" style="margin-top:12px">
                    <label>{{ $isFr ? 'Expérience avec les logiciels de santé' : 'Experience with health software' }}</label>
                    <textarea name="tech_experience" maxlength="1000"
                              placeholder="{{ $isFr ? 'Avez-vous déjà utilisé un EMR, HIS, ou autre logiciel de santé ? Décrivez votre expérience...' : 'Have you used an EMR, HIS, or other health software before? Describe your experience...' }}">{{ old('tech_experience') }}</textarea>
                </div>
            </div>

            <div class="tf-section">
                <div class="tf-section-label">{{ $isFr ? 'Motivation' : 'Motivation' }}</div>
                <div class="tf-group">
                    <label>{{ $isFr ? 'Pourquoi souhaitez-vous rejoindre le programme ?' : 'Why do you want to join the programme?' }} *</label>
                    <textarea name="motivation" required maxlength="2000"
                              placeholder="{{ $isFr ? 'Partagez vos motivations, ce que vous espérez apporter et ce que vous souhaitez apprendre...' : 'Share your motivations, what you hope to contribute, and what you\'d like to learn...' }}">{{ old('motivation') }}</textarea>
                    @error('motivation')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
            </div>

            <button type="submit" class="tf-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $isFr ? 'Envoyer ma candidature' : 'Submit application' }}
            </button>
        </form>
        @endif
    </div>

    <div class="testers-sidebar">
        <div class="testers-info-card">
            <h3>{{ $isFr ? 'Ce que font les testeurs' : 'What testers do' }}</h3>
            @foreach([
                ['en' => 'Test new features before release', 'fr' => 'Tester les nouvelles fonctionnalités avant leur sortie'],
                ['en' => 'Report bugs and usability issues', 'fr' => 'Signaler les bugs et problèmes d\'ergonomie'],
                ['en' => 'Complete structured test scenarios', 'fr' => 'Effectuer des scénarios de test structurés'],
                ['en' => 'Submit feedback via tester portal', 'fr' => 'Soumettre des retours via le portail testeur'],
                ['en' => '2–4 hours/month commitment', 'fr' => 'Engagement de 2–4 heures/mois'],
            ] as $b)
            <div class="tf-benefit">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? $b['fr'] : $b['en'] }}
            </div>
            @endforeach
        </div>

        <div class="testers-info-card" style="background:linear-gradient(135deg,#F59E0B08,#080e1a);border-color:#F59E0B20">
            <h3>{{ $isFr ? 'Déjà testeur ?' : 'Already a tester?' }}</h3>
            <p style="font-size:.8rem;color:var(--text-muted);line-height:1.6;margin-bottom:14px">{{ $isFr ? 'Connectez-vous à votre tableau de bord testeur pour voir vos missions et soumettre vos rapports.' : 'Log in to your tester dashboard to view assignments and submit reports.' }}</p>
            <a href="{{ url($locale.'/login') }}" style="display:block;text-align:center;background:#F59E0B20;border:1px solid #F59E0B40;color:#F59E0B;border-radius:8px;padding:10px;font-size:.82rem;font-weight:600;text-decoration:none">
                {{ $isFr ? 'Connexion testeur →' : 'Tester login →' }}
            </a>
        </div>

        <div class="testers-info-card">
            <h3>{{ $isFr ? 'Profils recherchés' : 'Profiles we seek' }}</h3>
            <p style="font-size:.8rem;color:var(--text-muted);line-height:1.6">
                {{ $isFr ? 'Médecins, infirmiers, pharmaciens, administrateurs de santé, techniciens de laboratoire, chercheurs en santé, et professionnels IT du secteur santé.' : 'Doctors, nurses, pharmacists, health administrators, lab technicians, health researchers, and health IT professionals.' }}
            </p>
        </div>
    </div>

</div>

</x-layouts.app>
