@php
$locale = app()->getLocale();
$isFr   = $locale === 'fr';
@endphp

<x-layouts.app :title="$isFr ? 'Réserver une démo — OPES Health Systems' : 'Book a Demo — OPES Health Systems'"
               :description="$isFr ? 'Planifiez une démonstration personnalisée de nos logiciels de santé numérique.' : 'Schedule a personalised demo of our digital health software.'">

<style>
.demo-page-hero { background: linear-gradient(135deg, #080F1E 0%, #0d1a2e 100%); padding: 72px 48px 56px; text-align: center; border-bottom: 1px solid #1e293b; }
.demo-page-hero h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.demo-page-hero p  { color: #94a3b8; font-size: 1.05rem; max-width: 560px; margin: 0 auto 28px; line-height: 1.7; }
.demo-steps { display: flex; justify-content: center; gap: 32px; flex-wrap: wrap; margin-top: 8px; }
.demo-step  { display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.82rem; }
.demo-step-num { width: 22px; height: 22px; border-radius: 50%; background: #00C896; color: #0f172a; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

.demo-layout  { max-width: 900px; margin: 0 auto; padding: 52px 24px 80px; display: grid; grid-template-columns: 1fr 340px; gap: 40px; align-items: start; }
.demo-form-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; }
.demo-form-card h2 { font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 28px; display: flex; align-items: center; gap: 9px; }
.df-section { margin-bottom: 28px; }
.df-section-label { font-size: 0.72rem; font-weight: 700; color: #64748b; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid #1e293b; padding-bottom: 8px; }
.df-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.df-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.df-group label { font-size: 0.78rem; font-weight: 600; color: #94a3b8; }
.df-group input, .df-group select, .df-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.df-group input:focus, .df-group select:focus, .df-group textarea:focus { outline: none; border-color: #00C896; }
.df-group select option { background: #0f172a; }
.df-group textarea { min-height: 90px; resize: vertical; }

/* product checkboxes */
.prod-checkbox-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.prod-cb-label { display: flex; align-items: center; gap: 8px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 9px 12px; cursor: pointer; font-size: 0.82rem; color: #94a3b8; transition: all .2s; }
.prod-cb-label:has(input:checked) { border-color: #00C896; color: #e2e8f0; background: #00C89610; }
.prod-cb-label input[type=checkbox] { accent-color: #00C896; }

.df-submit { background: #00C896; color: #0f172a; border: none; border-radius: 9px; padding: 13px 32px; font-size: 0.92rem; font-weight: 700; cursor: pointer; width: 100%; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
.df-submit:hover { background: #00b386; }

/* sidebar */
.demo-sidebar { display: flex; flex-direction: column; gap: 20px; }
.demo-info-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 24px; }
.demo-info-card h3 { font-size: 0.9rem; font-weight: 700; color: #f1f5f9; margin-bottom: 16px; }
.demo-info-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; font-size: 0.82rem; color: #94a3b8; line-height: 1.5; }
.demo-info-row svg { flex-shrink: 0; margin-top: 1px; color: #00C896; }
.demo-testimonial { background: linear-gradient(135deg, #00C89612, #1A6FE808); border: 1px solid #00C89630; border-radius: 14px; padding: 20px; }
.demo-testimonial blockquote { font-size: 0.82rem; color: #94a3b8; font-style: italic; line-height: 1.65; margin-bottom: 12px; }
.demo-testimonial cite { font-size: 0.75rem; color: #64748b; font-style: normal; }

.df-success { background: #00C89614; border: 1px solid #00C89640; border-radius: 10px; padding: 28px; text-align: center; }
.df-success h3 { color: #00C896; font-size: 1.2rem; margin-bottom: 10px; }
.df-success p  { color: #94a3b8; font-size: 0.88rem; line-height: 1.6; }

@media (max-width: 768px) {
    .demo-page-hero { padding: 48px 20px 40px; }
    .demo-layout { grid-template-columns: 1fr; padding: 32px 16px 60px; gap: 28px; }
    .demo-steps { gap: 16px; }
    .df-row, .prod-checkbox-grid { grid-template-columns: 1fr; }
}
</style>

<div class="demo-page-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ $isFr ? 'Planifier une démo' : 'Schedule a Demo' }}
    </div>
    <h1>{{ $isFr ? 'Voyez OPES en action' : 'See OPES in Action' }}</h1>
    <p>{{ $isFr ? 'Obtenez une démonstration personnalisée adaptée à votre établissement. Nos experts vous guideront à travers les systèmes les plus pertinents pour vous.' : 'Get a personalised walkthrough tailored to your facility. Our experts will guide you through the systems most relevant to you.' }}</p>
    <div class="demo-steps">
        <div class="demo-step"><div class="demo-step-num">1</div>{{ $isFr ? 'Remplissez le formulaire' : 'Fill the form' }}</div>
        <div class="demo-step" style="color:#475569">›</div>
        <div class="demo-step"><div class="demo-step-num">2</div>{{ $isFr ? 'Notre équipe vous contacte (24h)' : 'Our team contacts you (24h)' }}</div>
        <div class="demo-step" style="color:#475569">›</div>
        <div class="demo-step"><div class="demo-step-num">3</div>{{ $isFr ? 'Démo en ligne ou sur site' : 'Online or on-site demo' }}</div>
    </div>
</div>

<div class="demo-layout">

    <div class="demo-form-card">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ $isFr ? 'Demande de démonstration' : 'Demo Request' }}
        </h2>

        @if(session('success'))
        <div class="df-success">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2" style="margin:0 auto 14px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>{{ $isFr ? 'Demande reçue !' : 'Request received!' }}</h3>
            <p>{{ $isFr ? 'Merci ! Notre équipe vous contactera dans les 24 heures ouvrées pour planifier votre démonstration personnalisée.' : 'Thank you! Our team will contact you within 24 business hours to schedule your personalised demo.' }}</p>
        </div>
        @else

        <form action="{{ route('book-demo.submit', $locale) }}" method="POST">
            @csrf

            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Vos coordonnées' : 'Your contact details' }}</div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Nom complet' : 'Full name' }} *</label>
                        <input type="text" name="name" required maxlength="100" value="{{ old('name') }}" placeholder="{{ $isFr ? 'Dr. Mbarga Jean' : 'Dr. John Smith' }}">
                        @error('name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                    <div class="df-group">
                        <label>Email *</label>
                        <input type="email" name="email" required maxlength="150" value="{{ old('email') }}" placeholder="you@hospital.cm">
                        @error('email')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Téléphone / WhatsApp' : 'Phone / WhatsApp' }}</label>
                    <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>

            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Votre établissement' : 'Your institution' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Nom de l\'établissement' : 'Organisation name' }} *</label>
                    <input type="text" name="organization_name" required maxlength="150" value="{{ old('organization_name') }}" placeholder="{{ $isFr ? 'Hôpital Central de Yaoundé' : 'Central Hospital Yaoundé' }}">
                    @error('organization_name')<span style="color:#ef4444;font-size:.75rem">{{ $message }}</span>@enderror
                </div>
                <div class="df-row">
                    <div class="df-group">
                        <label>{{ $isFr ? 'Type d\'établissement' : 'Institution type' }}</label>
                        <select name="institution_type">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach(\App\Models\DemoRequest::$institutionTypes as $val => $labels)
                            <option value="{{ $val }}" {{ old('institution_type') === $val ? 'selected' : '' }}>
                                {{ $labels[$locale] ?? $labels['en'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="df-group">
                        <label>{{ $isFr ? 'Taille (personnel)' : 'Size (staff)' }}</label>
                        <select name="institution_size">
                            <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                            @foreach(\App\Models\DemoRequest::$sizes as $s)
                            <option value="{{ $s }}" {{ old('institution_size') === $s ? 'selected' : '' }}>{{ $s }} {{ $isFr ? 'personnes' : 'people' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Pays' : 'Country' }}</label>
                    <input type="text" name="country" maxlength="60" value="{{ old('country', 'Cameroun') }}" placeholder="{{ $isFr ? 'Cameroun' : 'Cameroon' }}">
                </div>
            </div>

            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Produits d\'intérêt' : 'Products of interest' }}</div>
                <div class="prod-checkbox-grid">
                    @foreach(['OPESCare (Health ID)', 'OPES EMR', 'OPES Hospital HIS', 'OPES Triage', 'OPES Lab (LABIS)', 'PHARMIS', 'OPES Intelligence', 'Autre / Other'] as $prod)
                    <label class="prod-cb-label">
                        <input type="checkbox" name="products[]" value="{{ $prod }}" {{ in_array($prod, old('products', [])) ? 'checked' : '' }}>
                        {{ $prod }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="df-section">
                <div class="df-section-label">{{ $isFr ? 'Planification' : 'Scheduling' }}</div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Date souhaitée (facultatif)' : 'Preferred date (optional)' }}</label>
                    <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="df-group">
                    <label>{{ $isFr ? 'Message ou questions' : 'Message or questions' }}</label>
                    <textarea name="message" maxlength="2000" placeholder="{{ $isFr ? 'Décrivez vos besoins spécifiques, défis actuels, ou posez vos questions...' : 'Describe your specific needs, current challenges, or ask questions...' }}">{{ old('message') }}</textarea>
                </div>
            </div>

            <button type="submit" class="df-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $isFr ? 'Envoyer la demande' : 'Send demo request' }}
            </button>
        </form>
        @endif
    </div>

    <div class="demo-sidebar">
        <div class="demo-info-card">
            <h3>{{ $isFr ? 'Ce que comprend la démo' : 'What\'s included' }}</h3>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Session de 45–60 min avec un expert produit' : '45–60 min session with a product expert' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Présentation adaptée à votre type d\'établissement' : 'Demo tailored to your facility type' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Discussion sur l\'intégration et le déploiement' : 'Integration and deployment discussion' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Estimation de prix personnalisée' : 'Custom pricing estimate' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $isFr ? 'Disponible en anglais et en français' : 'Available in English and French' }}
            </div>
        </div>

        <div class="demo-info-card">
            <h3>{{ $isFr ? 'Disponibilité' : 'Availability' }}</h3>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $isFr ? 'Lun–Sam : 8h–18h (WAT)' : 'Mon–Sat: 8am–6pm (WAT)' }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/></svg>
                {{ config('company.phone') }}
            </div>
            <div class="demo-info-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                {{ config('company.email') }}
            </div>
        </div>

        <div class="demo-testimonial">
            <blockquote>"{{ $isFr ? 'La démo OPES nous a convaincus en 45 minutes. Leur expertise du contexte africain est impressionnante.' : 'The OPES demo convinced us in 45 minutes. Their understanding of the African healthcare context is impressive.' }}"</blockquote>
            <cite>— {{ $isFr ? 'Directeur médical, Hôpital privé, Douala' : 'Medical Director, Private Hospital, Douala' }}</cite>
        </div>
    </div>

</div>

</x-layouts.app>
