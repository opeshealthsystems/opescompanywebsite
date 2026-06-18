@php
$locale = app()->getLocale();
$isFr   = $locale === 'fr';
@endphp

<x-layouts.app :title="$isFr ? 'Devenir Partenaire — OPES Health Systems' : 'Become a Partner — OPES Health Systems'"
               :description="$isFr ? 'Rejoignez l\'écosystème OPES et contribuez à la transformation de la santé numérique en Afrique.' : 'Join the OPES ecosystem and help transform digital health across Africa.'">

<style>
.partner-page-hero { background: linear-gradient(135deg, #080F1E 0%, #0a1628 100%); padding: 72px 48px 56px; text-align: center; border-bottom: 1px solid #1e293b; }
.partner-page-hero h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.partner-page-hero p  { color: #94a3b8; font-size: 1.05rem; max-width: 580px; margin: 0 auto; line-height: 1.7; }

.partner-type-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; max-width: 900px; margin: 40px auto 0; padding: 0 24px; }
.ptype-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 12px; padding: 18px; text-align: center; transition: border-color .2s; }
.ptype-card:hover { border-color: #00C896; }
.ptype-card svg { margin: 0 auto 10px; color: #00C896; display: block; }
.ptype-card h4 { font-size: 0.82rem; font-weight: 700; color: #e2e8f0; margin-bottom: 6px; }
.ptype-card p  { font-size: 0.75rem; color: #64748b; line-height: 1.5; }

.partner-layout { max-width: 900px; margin: 0 auto; padding: 52px 24px 80px; display: grid; grid-template-columns: 1fr 300px; gap: 40px; align-items: start; }
.partner-form-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; }
.partner-form-card h2 { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; margin-bottom: 28px; display: flex; align-items: center; gap: 9px; }
.pf-section { margin-bottom: 24px; }
.pf-section-label { font-size: 0.72rem; font-weight: 700; color: #64748b; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid #1e293b; padding-bottom: 8px; }
.pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.pf-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.pf-group label { font-size: 0.78rem; font-weight: 600; color: #94a3b8; }
.pf-group input, .pf-group select, .pf-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.pf-group input:focus, .pf-group select:focus, .pf-group textarea:focus { outline: none; border-color: #1A6FE8; }
.pf-group textarea { min-height: 110px; resize: vertical; }
.pf-submit { background: #1A6FE8; color: #fff; border: none; border-radius: 9px; padding: 13px 32px; font-size: 0.92rem; font-weight: 700; cursor: pointer; width: 100%; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
.pf-submit:hover { background: #1560d0; }
.pf-success { background: #1A6FE814; border: 1px solid #1A6FE840; border-radius: 10px; padding: 28px; text-align: center; }
.pf-success h3 { color: #1A6FE8; font-size: 1.2rem; margin-bottom: 10px; }
.pf-success p  { color: #94a3b8; font-size: 0.88rem; line-height: 1.6; }

.partner-sidebar { display: flex; flex-direction: column; gap: 18px; }
.partner-info-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 22px; }
.partner-info-card h3 { font-size: 0.88rem; font-weight: 700; color: #f1f5f9; margin-bottom: 14px; }
.partner-benefit { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; font-size: 0.8rem; color: #94a3b8; line-height: 1.5; }
.partner-benefit svg { flex-shrink: 0; margin-top: 1px; color: #1A6FE8; }

@media (max-width: 768px) {
    .partner-page-hero { padding: 48px 20px 40px; }
    .partner-type-grid { grid-template-columns: 1fr 1fr; }
    .partner-layout { grid-template-columns: 1fr; padding: 32px 16px 60px; gap: 28px; }
    .pf-row { grid-template-columns: 1fr; }
}
</style>

<div class="partner-page-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px;color:#1A6FE8">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1A6FE8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        {{ $isFr ? 'Programme Partenaires' : 'Partner Programme' }}
    </div>
    <h1>{{ $isFr ? 'Construisons l\'avenir de la santé africaine ensemble' : 'Let\'s build Africa\'s health future together' }}</h1>
    <p>{{ $isFr ? 'Rejoignez le réseau de partenaires OPES et co-construisez l\'infrastructure de santé numérique de demain pour plus de 500 millions de personnes.' : 'Join the OPES partner network and co-build tomorrow\'s digital health infrastructure for 500M+ people across Africa.' }}</p>
</div>

<div class="partner-type-grid">
    @foreach([
        ['icon' => 'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z M2 9h4v12H2z M4 4a2 2 0 1 0 0 4 2 2 0 0 0 0-4z', 'title_en' => 'Reseller', 'title_fr' => 'Revendeur', 'desc_en' => 'Sell OPES products to health facilities', 'desc_fr' => 'Revendez nos produits aux établissements'],
        ['icon' => 'M12 2L2 7l10 5 10-5-10-5z M2 17l10 5 10-5 M2 12l10 5 10-5', 'title_en' => 'Technology', 'title_fr' => 'Technologie', 'desc_en' => 'Integrate your tech with OPES APIs', 'desc_fr' => 'Intégrez votre tech aux APIs OPES'],
        ['icon' => 'M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z', 'title_en' => 'Hospital', 'title_fr' => 'Hôpital', 'desc_en' => 'Clinical validation & pilot partnership', 'desc_fr' => 'Validation clinique & pilote'],
        ['icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', 'title_en' => 'NGO / Dev', 'title_fr' => 'ONG / Dev', 'desc_en' => 'Community health & funding partnership', 'desc_fr' => 'Santé communautaire & financement'],
    ] as $pt)
    <div class="ptype-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="{{ $pt['icon'] }}"/>
        </svg>
        <h4>{{ $isFr ? $pt['title_fr'] : $pt['title_en'] }}</h4>
        <p>{{ $isFr ? $pt['desc_fr'] : $pt['desc_en'] }}</p>
    </div>
    @endforeach
</div>

<div class="partner-layout">

    <div class="partner-form-card">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1A6FE8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ $isFr ? 'Formulaire de candidature' : 'Partnership Application' }}
        </h2>

        @if(session('success'))
        <div class="pf-success">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1A6FE8" stroke-width="2" style="margin:0 auto 14px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Candidature reçue !' : 'Application received!' }}</h3>
            <p>{{ $isFr ? 'Merci pour votre intérêt. Notre équipe partenariats examinera votre candidature et vous contactera dans les 3–5 jours ouvrés.' : 'Thank you for your interest. Our partnerships team will review your application and contact you within 3–5 business days.' }}</p>
        </div>
        @else

        <form action="{{ route('become-a-partner.submit', $locale) }}" method="POST">
            @csrf

            <div class="pf-section">
                <div class="pf-section-label">{{ $isFr ? 'Organisation' : 'Organisation' }}</div>
                <div class="pf-group">
                    <label>{{ $isFr ? 'Nom de l\'organisation' : 'Organisation name' }} *</label>
                    <input type="text" name="organization_name" required maxlength="150" value="{{ old('organization_name') }}">
                    @error('organization_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Type d\'organisation' : 'Organisation type' }}</label>
                        <select name="organization_type">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            <option value="private">{{ $isFr ? 'Secteur privé' : 'Private sector' }}</option>
                            <option value="public">{{ $isFr ? 'Secteur public' : 'Public sector' }}</option>
                            <option value="ngo">NGO / ONG</option>
                            <option value="startup">Startup</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Site web' : 'Website' }}</label>
                        <input type="url" name="website" maxlength="200" value="{{ old('website') }}" placeholder="https://...">
                        @error('website')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Pays' : 'Country' }} *</label>
                        <input type="text" name="country" required maxlength="60" value="{{ old('country') }}">
                        @error('country')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Ville' : 'City' }}</label>
                        <input type="text" name="city" maxlength="60" value="{{ old('city') }}">
                    </div>
                </div>
            </div>

            <div class="pf-section">
                <div class="pf-section-label">{{ $isFr ? 'Contact' : 'Contact' }}</div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Nom du contact' : 'Contact name' }} *</label>
                        <input type="text" name="contact_name" required maxlength="100" value="{{ old('contact_name') }}">
                        @error('contact_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="pf-group">
                        <label>Email *</label>
                        <input type="email" name="email" required maxlength="150" value="{{ old('email') }}">
                        @error('email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="pf-group">
                    <label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label>
                    <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="pf-section">
                <div class="pf-section-label">{{ $isFr ? 'Type de partenariat' : 'Partnership type' }}</div>
                <div class="pf-group">
                    <label>{{ $isFr ? 'Comment souhaitez-vous collaborer ?' : 'How would you like to collaborate?' }} *</label>
                    <select name="partner_type" required>
                        <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                        @foreach(\App\Models\PartnerApplication::$partnerTypes as $val => $labels)
                        <option value="{{ $val }}" {{ old('partner_type') === $val ? 'selected' : '' }}>
                            {{ $labels[$locale] ?? $labels['en'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('partner_type')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Chiffre d\'affaires annuel' : 'Annual revenue range' }}</label>
                        <select name="annual_revenue_range">
                            <option value="">{{ $isFr ? 'Optionnel' : 'Optional' }}</option>
                            <option value="<100k">{{ $isFr ? 'Moins de 100K USD' : 'Under $100K' }}</option>
                            <option value="100k-500k">$100K – $500K</option>
                            <option value="500k-2m">$500K – $2M</option>
                            <option value="2m-10m">$2M – $10M</option>
                            <option value=">10m">{{ $isFr ? 'Plus de 10M USD' : 'Over $10M' }}</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label>{{ $isFr ? 'Marché cible' : 'Target market' }}</label>
                        <input type="text" name="target_market" maxlength="100" value="{{ old('target_market') }}" placeholder="{{ $isFr ? 'Ex: Yaoundé, Douala, CEMAC' : 'e.g. Cameroon, CEMAC region' }}">
                    </div>
                </div>
            </div>

            <div class="pf-section">
                <div class="pf-section-label">{{ $isFr ? 'Votre proposition' : 'Your proposal' }}</div>
                <div class="pf-group">
                    <label>{{ $isFr ? 'Décrivez comment vous souhaitez collaborer' : 'Describe how you\'d like to collaborate' }} *</label>
                    <textarea name="description" required maxlength="3000"
                              placeholder="{{ $isFr ? 'Décrivez votre organisation, vos atouts, et comment vous voyez ce partenariat bénéficier aux deux parties...' : 'Describe your organisation, your strengths, and how you see this partnership benefiting both sides...' }}">{{ old('description') }}</textarea>
                    @error('description')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
            </div>

            <button type="submit" class="pf-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $isFr ? 'Soumettre la candidature' : 'Submit application' }}
            </button>
        </form>
        @endif
    </div>

    <div class="partner-sidebar">
        <div class="partner-info-card">
            <h3>{{ $isFr ? 'Avantages partenaires' : 'Partner benefits' }}</h3>
            @foreach([
                ['en' => 'Revenue sharing & commissions', 'fr' => 'Partage de revenus & commissions'],
                ['en' => 'Technical training & certification', 'fr' => 'Formation technique & certification'],
                ['en' => 'Co-marketing & brand visibility', 'fr' => 'Co-marketing & visibilité de marque'],
                ['en' => 'API access & sandbox environment', 'fr' => 'Accès API & environnement sandbox'],
                ['en' => 'Dedicated partner success manager', 'fr' => 'Gestionnaire partenaire dédié'],
                ['en' => 'Priority product roadmap input', 'fr' => 'Influence prioritaire sur la feuille de route'],
            ] as $b)
            <div class="partner-benefit">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? $b['fr'] : $b['en'] }}
            </div>
            @endforeach
        </div>

        <div class="partner-info-card" style="border-color:#1A6FE820;background:linear-gradient(135deg,#1A6FE808,#080e1a)">
            <h3>{{ $isFr ? 'Déjà partenaire ?' : 'Already a partner?' }}</h3>
            <p style="font-size:.8rem;color:#64748b;line-height:1.6;margin-bottom:14px">{{ $isFr ? 'Connectez-vous à votre espace partenaire pour accéder aux ressources, statistiques et commissions.' : 'Log in to your partner portal to access resources, analytics and commission reports.' }}</p>
            <a href="{{ url($locale.'/login') }}" style="display:block;text-align:center;background:#1A6FE820;border:1px solid #1A6FE840;color:#1A6FE8;border-radius:8px;padding:10px;font-size:.82rem;font-weight:600;text-decoration:none">
                {{ $isFr ? 'Connexion partenaire →' : 'Partner login →' }}
            </a>
        </div>
    </div>

</div>

</x-layouts.app>
