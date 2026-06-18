@php
$locale = app()->getLocale();
$isFr   = $locale === 'fr';
@endphp

<x-layouts.app
    :title="$isFr ? 'Opération Cliniques Mobiles — OPES Health Systems' : 'Operation Mobile Clinics — OPES Health Systems'"
    :description="$isFr ? 'Notre initiative de construction de cliniques mobiles et centres de santé pour les communautés rurales éloignées du Cameroun et d\'Afrique.' : 'Our initiative to build mobile clinics and health centres for remote rural communities across Cameroon and Africa.'">

<style>
/* ── Mobile Clinic page ─────────────────────────────────────── */
.mc-hero {
    background: linear-gradient(135deg, #041e12 0%, #062a1a 60%, #080F1E 100%);
    padding: 80px 48px 64px;
    text-align: center;
    border-bottom: 1px solid #0e2d1c;
    position: relative;
    overflow: hidden;
}
.mc-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 60% 40% at 50% 0%, rgba(0,200,150,.12) 0%, transparent 70%);
    pointer-events: none;
}
.mc-hero .section-label { justify-content: center; margin-bottom: 16px; color: #00C896; }
.mc-hero h1 { font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; color: #f1f5f9; margin-bottom: 18px; line-height: 1.15; }
.mc-hero h1 span { color: #00C896; }
.mc-hero p { color: #94a3b8; font-size: 1.05rem; max-width: 620px; margin: 0 auto 36px; line-height: 1.75; }
.mc-hero-badges { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.mc-badge { background: rgba(0,200,150,0.08); border: 1px solid rgba(0,200,150,0.2); border-radius: 20px; padding: 7px 16px; font-size: 0.78rem; color: #00C896; display: flex; align-items: center; gap: 6px; }

/* Impact numbers */
.mc-impact { max-width: 1000px; margin: 0 auto; padding: 56px 24px 0; }
.mc-impact-title { text-align: center; margin-bottom: 36px; }
.mc-impact-title h2 { font-size: 1.6rem; font-weight: 800; color: #f1f5f9; margin-bottom: 8px; }
.mc-impact-title p { color: #64748b; font-size: 0.9rem; }
.mc-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 56px; }
.mc-stat { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 24px; text-align: center; }
.mc-stat-num { font-size: 2rem; font-weight: 800; color: #00C896; line-height: 1; margin-bottom: 6px; }
.mc-stat-label { font-size: 0.78rem; color: #64748b; line-height: 1.4; }

/* What we're building */
.mc-what { background: #060d18; border-top: 1px solid #1e293b; border-bottom: 1px solid #1e293b; padding: 60px 24px; }
.mc-what-inner { max-width: 1000px; margin: 0 auto; }
.mc-what h2 { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin-bottom: 8px; }
.mc-what > .mc-what-inner > p { color: #64748b; margin-bottom: 36px; }
.mc-build-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.mc-build-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 14px; padding: 24px; }
.mc-build-card svg { color: #00C896; margin-bottom: 14px; }
.mc-build-card h3 { font-size: 0.92rem; font-weight: 700; color: #e2e8f0; margin-bottom: 10px; }
.mc-build-card p  { font-size: 0.8rem; color: #64748b; line-height: 1.6; }

/* How it works */
.mc-how { max-width: 1000px; margin: 0 auto; padding: 60px 24px; }
.mc-how h2 { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin-bottom: 36px; text-align: center; }
.mc-phases { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; position: relative; }
.mc-phases::before { content: ''; position: absolute; top: 28px; left: 14%; right: 14%; height: 2px; background: linear-gradient(90deg, #00C896, #1A6FE8); z-index: 0; }
.mc-phase { text-align: center; padding: 0 12px; position: relative; z-index: 1; }
.mc-phase-num { width: 56px; height: 56px; border-radius: 50%; background: #0f172a; border: 2px solid #00C896; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.2rem; font-weight: 800; color: #00C896; }
.mc-phase h3 { font-size: 0.82rem; font-weight: 700; color: #e2e8f0; margin-bottom: 8px; }
.mc-phase p  { font-size: 0.75rem; color: #64748b; line-height: 1.5; }

/* Support / Donate section */
.mc-support { background: linear-gradient(135deg, #041e12, #062a1a); border-top: 1px solid #0e2d1c; border-bottom: 1px solid #0e2d1c; padding: 72px 24px; }
.mc-support-inner { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 380px; gap: 52px; align-items: start; }
.mc-support h2 { font-size: 1.6rem; font-weight: 800; color: #f1f5f9; margin-bottom: 14px; }
.mc-support > .mc-support-inner > div > p { color: #94a3b8; line-height: 1.75; margin-bottom: 28px; }
.mc-support-options { display: flex; flex-direction: column; gap: 14px; }
.mc-support-option { display: flex; align-items: flex-start; gap: 12px; }
.mc-support-option svg { color: #00C896; margin-top: 2px; flex-shrink: 0; }
.mc-support-option h4 { font-size: 0.85rem; font-weight: 700; color: #e2e8f0; margin-bottom: 3px; }
.mc-support-option p  { font-size: 0.78rem; color: #64748b; line-height: 1.5; }

/* Funding form */
.mc-fund-form { background: #080e1a; border: 1px solid #1e293b; border-radius: 16px; padding: 28px; }
.mc-fund-form h3 { font-size: 1rem; font-weight: 700; color: #f1f5f9; margin-bottom: 8px; }
.mc-fund-form p  { font-size: 0.8rem; color: #64748b; margin-bottom: 20px; line-height: 1.5; }
.mc-fund-amounts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 16px; }
.mc-amount-btn { background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; font-size: 0.82rem; color: #94a3b8; transition: all .2s; font-weight: 600; }
.mc-amount-btn.selected, .mc-amount-btn:hover { border-color: #00C896; color: #00C896; background: #00C89610; }
.mff-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.mff-group label { font-size: 0.78rem; font-weight: 600; color: #94a3b8; }
.mff-group input, .mff-group select, .mff-group textarea {
    background: #0f172a; border: 1px solid #1e293b; border-radius: 8px;
    padding: 10px 14px; color: #e2e8f0; font-size: 0.88rem; font-family: inherit; width: 100%;
    transition: border-color .2s;
}
.mff-group input:focus, .mff-group select:focus { outline: none; border-color: #00C896; }
.mff-submit { background: #00C896; color: #0f172a; border: none; border-radius: 9px; padding: 13px 24px; font-size: 0.9rem; font-weight: 700; cursor: pointer; width: 100%; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 4px; }
.mff-submit:hover { background: #00b386; }
.mc-fund-note { font-size: 0.72rem; color: #475569; text-align: center; margin-top: 12px; line-height: 1.5; }

.mff-success { text-align: center; padding: 20px 0; }
.mff-success h4 { color: #00C896; margin-bottom: 8px; }
.mff-success p  { color: #64748b; font-size: .82rem; line-height: 1.6; }

/* Transparency */
.mc-transparency { max-width: 1000px; margin: 0 auto; padding: 60px 24px; }
.mc-transparency h2 { font-size: 1.4rem; font-weight: 800; color: #f1f5f9; margin-bottom: 10px; text-align: center; }
.mc-transparency > p { color: #64748b; text-align: center; margin-bottom: 36px; font-size: 0.9rem; }
.mc-transp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
.mc-transp-card { background: #080e1a; border: 1px solid #1e293b; border-radius: 12px; padding: 22px; text-align: center; }
.mc-transp-card svg { color: #1A6FE8; margin: 0 auto 12px; display: block; }
.mc-transp-card h4 { font-size: 0.85rem; font-weight: 700; color: #e2e8f0; margin-bottom: 8px; }
.mc-transp-card p  { font-size: 0.78rem; color: #64748b; line-height: 1.5; }

/* CTA strip */
.mc-cta { background: linear-gradient(90deg, #00C89614, #1A6FE808); border-top: 1px solid #1e293b; border-bottom: 1px solid #1e293b; padding: 52px 24px; text-align: center; }
.mc-cta h2 { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin-bottom: 12px; }
.mc-cta p  { color: #94a3b8; margin-bottom: 28px; max-width: 540px; margin-left: auto; margin-right: auto; }
.mc-cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

@media (max-width: 768px) {
    .mc-hero { padding: 52px 20px 44px; }
    .mc-stats { grid-template-columns: 1fr 1fr; }
    .mc-build-grid { grid-template-columns: 1fr; }
    .mc-phases { grid-template-columns: 1fr 1fr; gap: 24px; }
    .mc-phases::before { display: none; }
    .mc-support-inner { grid-template-columns: 1fr; }
    .mc-transp-grid { grid-template-columns: 1fr; }
    .mc-fund-amounts { grid-template-columns: repeat(3, 1fr); }
    .mc-impact, .mc-how, .mc-transparency { padding-left: 16px; padding-right: 16px; }
}
</style>

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="mc-hero">
    <div class="section-label">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        {{ $isFr ? 'Initiative communautaire · OPES Health Systems' : 'Community Initiative · OPES Health Systems' }}
    </div>
    <h1>{{ $isFr ? 'Opération' : 'Operation' }}<br><span>{{ $isFr ? 'Cliniques Mobiles' : 'Build Mobile Clinics' }}</span></h1>
    <p>{{ $isFr ? 'Nous construisons des cliniques mobiles, des centres de santé et des dispensaires pour les communautés rurales éloignées du Cameroun et d\'Afrique. Chaque contribution vous aide à apporter les soins là où ils sont le plus nécessaires.' : 'We are building mobile clinics, health centres, and dispensaries for remote rural communities across Cameroon and Africa. Every contribution helps bring care where it is needed most.' }}</p>
    <div class="mc-hero-badges">
        <div class="mc-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            {{ $isFr ? 'Initiative à but non lucratif' : 'Non-profit initiative' }}
        </div>
        <div class="mc-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $isFr ? 'Rapports trimestriels' : 'Quarterly reports' }}
        </div>
        <div class="mc-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $isFr ? 'Fonds 100% tracés' : '100% traceable funds' }}
        </div>
        <div class="mc-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $isFr ? 'Régions CEMAC ciblées' : 'CEMAC regions targeted' }}
        </div>
    </div>
</div>

{{-- ── IMPACT STATS ──────────────────────────────────────────────── --}}
<div class="mc-impact">
    <div class="mc-impact-title">
        <h2>{{ $isFr ? 'L\'impact que nous visons' : 'The impact we aim for' }}</h2>
        <p>{{ $isFr ? 'D\'ici 2028, voici ce que nous construisons ensemble' : 'By 2028, here is what we are building together' }}</p>
    </div>
    <div class="mc-stats">
        <div class="mc-stat">
            <div class="mc-stat-num">50+</div>
            <div class="mc-stat-label">{{ $isFr ? 'Cliniques mobiles déployées' : 'Mobile clinics deployed' }}</div>
        </div>
        <div class="mc-stat">
            <div class="mc-stat-num">200K+</div>
            <div class="mc-stat-label">{{ $isFr ? 'Patients par an touchés' : 'Patients reached per year' }}</div>
        </div>
        <div class="mc-stat">
            <div class="mc-stat-num">15</div>
            <div class="mc-stat-label">{{ $isFr ? 'Régions rurales ciblées' : 'Rural regions targeted' }}</div>
        </div>
        <div class="mc-stat">
            <div class="mc-stat-num">5</div>
            <div class="mc-stat-label">{{ $isFr ? 'Pays de la zone CEMAC' : 'CEMAC countries covered' }}</div>
        </div>
    </div>
</div>

{{-- ── WHAT WE'RE BUILDING ───────────────────────────────────────── --}}
<div class="mc-what">
    <div class="mc-what-inner">
        <div class="section-label" style="margin-bottom:12px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg> {{ $isFr ? 'Ce que nous construisons' : 'What we\'re building' }}</div>
        <h2>{{ $isFr ? 'Des soins pour les zones oubliées' : 'Healthcare for forgotten zones' }}</h2>
        <p>{{ $isFr ? 'Trop de communautés rurales vivent à plus de 50 km du centre de santé le plus proche. Nous changeons cela.' : 'Too many rural communities live more than 50 km from the nearest health facility. We are changing that.' }}</p>
        <div class="mc-build-grid">
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <h3>{{ $isFr ? 'Cliniques mobiles' : 'Mobile Clinics' }}</h3>
                <p>{{ $isFr ? 'Véhicules médicaux entièrement équipés avec diagnostic de base, médicaments essentiels et connectivité OPES pour les consultations et le suivi des patients.' : 'Fully equipped medical vehicles with basic diagnostics, essential medicines, and OPES connectivity for consultations and patient follow-up.' }}</p>
            </div>
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <h3>{{ $isFr ? 'Centres de santé communautaires' : 'Community Health Centres' }}</h3>
                <p>{{ $isFr ? 'Structures semi-permanentes construites dans des villages isolés, dotées d\'un infirmier résident, d\'un équipement de base et d\'un accès à la télémédecine.' : 'Semi-permanent structures built in isolated villages, staffed by a resident nurse, basic equipment, and telemedicine access.' }}</p>
            </div>
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h3>{{ $isFr ? 'Postes de santé ruraux' : 'Rural Dispensaries' }}</h3>
                <p>{{ $isFr ? 'Petits postes de soins approvisionnés en médicaments essentiels, dirigés par des agents de santé communautaires formés par OPES.' : 'Small care posts stocked with essential medicines, managed by community health workers trained through OPES Academy.' }}</p>
            </div>
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                <h3>{{ $isFr ? 'Connectivité & OPES numérique' : 'Digital Connectivity & OPES' }}</h3>
                <p>{{ $isFr ? 'Chaque site est équipé d\'une connexion satellite ou GSM et du logiciel OPES pour assurer le suivi des patients, les rappels de vaccination et les alertes épidémiques.' : 'Each site is equipped with satellite or GSM connectivity and OPES software for patient tracking, vaccination reminders, and epidemic alerts.' }}</p>
            </div>
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <h3>{{ $isFr ? 'Formation des agents de santé' : 'Health Worker Training' }}</h3>
                <p>{{ $isFr ? 'Formation des agents de santé communautaires locaux grâce à l\'Académie OPES — diagnostics de base, premiers soins, santé maternelle et infantile.' : 'Training local community health workers through OPES Academy — basic diagnostics, first aid, maternal and child health.' }}</p>
            </div>
            <div class="mc-build-card">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <h3>{{ $isFr ? 'Santé maternelle & infantile' : 'Maternal & Child Health' }}</h3>
                <p>{{ $isFr ? 'Priorité aux consultations prénatales, aux accouchements assistés et à la vaccination des enfants dans les zones à forte mortalité maternelle.' : 'Priority focus on antenatal consultations, assisted deliveries, and child vaccination in high maternal mortality zones.' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── HOW IT WORKS ──────────────────────────────────────────────── --}}
<div class="mc-how">
    <h2>{{ $isFr ? 'Comment ça fonctionne' : 'How it works' }}</h2>
    <div class="mc-phases">
        <div class="mc-phase">
            <div class="mc-phase-num">1</div>
            <h3>{{ $isFr ? 'Identification' : 'Identification' }}</h3>
            <p>{{ $isFr ? 'Nous cartographions les zones sans accès aux soins de santé à moins de 50 km.' : 'We map zones with no healthcare access within 50 km.' }}</p>
        </div>
        <div class="mc-phase">
            <div class="mc-phase-num">2</div>
            <h3>{{ $isFr ? 'Financement' : 'Funding' }}</h3>
            <p>{{ $isFr ? 'Nous collectons les fonds auprès de donateurs bienveillants, d\'ONG et d\'organisations internationales.' : 'We raise funds from wellwishers, NGOs, and international organisations.' }}</p>
        </div>
        <div class="mc-phase">
            <div class="mc-phase-num">3</div>
            <h3>{{ $isFr ? 'Déploiement' : 'Deployment' }}</h3>
            <p>{{ $isFr ? 'Construction ou acquisition de la clinique, installation du logiciel OPES, formation du personnel.' : 'Build or acquire the clinic, install OPES software, train staff.' }}</p>
        </div>
        <div class="mc-phase">
            <div class="mc-phase-num">4</div>
            <h3>{{ $isFr ? 'Suivi & rapport' : 'Monitoring' }}</h3>
            <p>{{ $isFr ? 'Rapports trimestriels avec photos, données et impact partagés avec tous les donateurs.' : 'Quarterly reports with photos, data, and impact shared with all donors.' }}</p>
        </div>
    </div>
</div>

{{-- ── SUPPORT / DONATE ──────────────────────────────────────────── --}}
<div class="mc-support">
    <div class="mc-support-inner">
        <div>
            <div class="section-label" style="margin-bottom:14px;color:#00C896">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                {{ $isFr ? 'Comment soutenir' : 'How to support' }}
            </div>
            <h2>{{ $isFr ? 'Chaque franc compte' : 'Every contribution counts' }}</h2>
            <p>{{ $isFr ? 'Que vous soyez un particulier bienveillant, une entreprise, une ONG ou une organisation internationale — il y a une façon de contribuer à cette mission.' : 'Whether you are an individual wellwisher, a company, an NGO, or an international organisation — there is a way to contribute to this mission.' }}</p>
            <div class="mc-support-options">
                <div class="mc-support-option">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <div>
                        <h4>{{ $isFr ? 'Don individuel' : 'Individual donation' }}</h4>
                        <p>{{ $isFr ? 'Tout montant est bienvenu. Un don de 50 000 FCFA couvre les fournitures médicales d\'une clinique mobile pour une semaine.' : 'Any amount is welcome. A donation of XAF 50,000 covers medical supplies for a mobile clinic for one week.' }}</p>
                    </div>
                </div>
                <div class="mc-support-option">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <div>
                        <h4>{{ $isFr ? 'Parrainage d\'entreprise' : 'Corporate sponsorship' }}</h4>
                        <p>{{ $isFr ? 'Parrainez entièrement une clinique mobile (~8M FCFA) et votre logo apparaîtra sur la clinique et dans tous nos rapports.' : 'Fully sponsor a mobile clinic (~XAF 8M) and your logo appears on the clinic and in all our reports.' }}</p>
                    </div>
                </div>
                <div class="mc-support-option">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <div>
                        <h4>{{ $isFr ? 'Partenariat ONG / institutionnel' : 'NGO / institutional partnership' }}</h4>
                        <p>{{ $isFr ? 'Joignez-vous à nous en tant que partenaire opérationnel pour co-concevoir et co-financer des sites dans votre zone d\'intervention.' : 'Join us as an operational partner to co-design and co-fund sites in your area of operation.' }}</p>
                    </div>
                </div>
                <div class="mc-support-option">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
                    <div>
                        <h4>{{ $isFr ? 'Dons en nature' : 'In-kind donations' }}</h4>
                        <p>{{ $isFr ? 'Médicaments, équipements médicaux, véhicules, générateurs — contactez-nous pour discuter des dons en nature.' : 'Medicines, medical equipment, vehicles, generators — contact us to discuss in-kind donations.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mc-fund-form">
            <h3>{{ $isFr ? 'Exprimer votre intérêt' : 'Express your interest' }}</h3>
            <p>{{ $isFr ? 'Remplissez ce formulaire et notre équipe vous contactera pour discuter de votre contribution.' : 'Fill this form and our team will contact you to discuss your contribution.' }}</p>

            @if(session('mc_success'))
            <div class="mff-success">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2" style="margin:0 auto 12px;display:block"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h4>{{ $isFr ? 'Merci !' : 'Thank you!' }}</h4>
                <p>{{ $isFr ? 'Nous avons bien reçu votre intérêt. Notre équipe vous contactera sous 48h.' : 'We received your interest. Our team will contact you within 48 hours.' }}</p>
            </div>
            @else
            <form action="{{ url($locale.'/contact') }}" method="POST">
                @csrf
                <input type="hidden" name="products" value="Operation Build Mobile Clinics">
                <input type="hidden" name="facility_type" value="wellwisher_donor">

                <div class="mc-fund-amounts" id="amountBtns">
                    @foreach(['50,000 FCFA', '200,000 FCFA', '500,000 FCFA', '1,000,000 FCFA', '5,000,000 FCFA', $isFr ? 'Autre' : 'Other'] as $amt)
                    <div class="mc-amount-btn" onclick="selectAmount(this, '{{ $amt }}')">{{ $amt }}</div>
                    @endforeach
                </div>

                <div class="mff-group">
                    <label>{{ $isFr ? 'Nom complet' : 'Full name' }} *</label>
                    <input type="text" name="name" required placeholder="{{ $isFr ? 'Votre nom' : 'Your name' }}">
                </div>
                <div class="mff-group">
                    <label>Email *</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="mff-group">
                    <label>{{ $isFr ? 'Type de contribution' : 'Contribution type' }}</label>
                    <select name="message" id="contribType">
                        <option value="{{ $isFr ? 'Don individuel' : 'Individual donation' }}">{{ $isFr ? 'Don individuel' : 'Individual donation' }}</option>
                        <option value="{{ $isFr ? 'Parrainage d\'entreprise' : 'Corporate sponsorship' }}">{{ $isFr ? 'Parrainage d\'entreprise' : 'Corporate sponsorship' }}</option>
                        <option value="{{ $isFr ? 'Partenariat ONG' : 'NGO partnership' }}">{{ $isFr ? 'Partenariat ONG' : 'NGO partnership' }}</option>
                        <option value="{{ $isFr ? 'Don en nature' : 'In-kind donation' }}">{{ $isFr ? 'Don en nature' : 'In-kind donation' }}</option>
                    </select>
                </div>
                <button type="submit" class="mff-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    {{ $isFr ? 'M\'inscrire comme donateur' : 'Register as donor' }}
                </button>
                <p class="mc-fund-note">{{ $isFr ? 'Pas de paiement en ligne — notre équipe vous contactera pour discuter des modalités de contribution.' : 'No online payment — our team will contact you to discuss contribution arrangements.' }}</p>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- ── TRANSPARENCY ──────────────────────────────────────────────── --}}
<div class="mc-transparency">
    <h2>{{ $isFr ? 'Notre engagement de transparence' : 'Our transparency commitment' }}</h2>
    <p>{{ $isFr ? 'Chaque franc donné est tracé et rapporté publiquement.' : 'Every franc donated is tracked and reported publicly.' }}</p>
    <div class="mc-transp-grid">
        <div class="mc-transp-card">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <h4>{{ $isFr ? 'Rapports trimestriels' : 'Quarterly reports' }}</h4>
            <p>{{ $isFr ? 'Envoyés à tous les donateurs avec photos, données patients anonymisées et état financier.' : 'Sent to all donors with photos, anonymised patient data, and financial statement.' }}</p>
        </div>
        <div class="mc-transp-card">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <h4>{{ $isFr ? 'Publication publique' : 'Public dashboard' }}</h4>
            <p>{{ $isFr ? 'Données d\'impact publiées sur notre site web — nombre de consultations, vaccinations, naissances assistées.' : 'Impact data published on our website — number of consultations, vaccinations, assisted births.' }}</p>
        </div>
        <div class="mc-transp-card">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <h4>{{ $isFr ? 'Audit indépendant' : 'Independent audit' }}</h4>
            <p>{{ $isFr ? 'Comptes audités chaque année par un cabinet indépendant. Les rapports sont disponibles sur demande.' : 'Accounts audited annually by an independent firm. Reports available on request.' }}</p>
        </div>
    </div>
</div>

{{-- ── CTA ───────────────────────────────────────────────────────── --}}
<div class="mc-cta">
    <h2>{{ $isFr ? 'Ensemble, nous sauvons des vies' : 'Together, we save lives' }}</h2>
    <p>{{ $isFr ? 'Rejoignez OPES Health Systems et des centaines de bienveillants pour apporter des soins de santé aux communautés qui en ont le plus besoin.' : 'Join OPES Health Systems and hundreds of wellwishers in bringing healthcare to the communities that need it most.' }}</p>
    <div class="mc-cta-btns">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            {{ $isFr ? 'Nous contacter' : 'Contact us' }}
        </a>
        <a href="{{ url($locale.'/become-a-partner') }}" class="btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            {{ $isFr ? 'Devenir partenaire' : 'Become a partner' }}
        </a>
    </div>
</div>

<script>
function selectAmount(el, amt) {
    document.querySelectorAll('.mc-amount-btn').forEach(function(b){ b.classList.remove('selected'); });
    el.classList.add('selected');
}
</script>

</x-layouts.app>
