@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    bodyClass="health-id-concept-page"
    :title="$isFr ? 'OPESCare Health ID | Identité et interopérabilité en santé' : 'OPESCare Health ID | Health identity and interoperability'"
    :description="$isFr ? 'Relier l’identité des patients entre organisations participantes grâce à un index maître, des échanges interopérables et des accès gouvernés.' : 'Connect patient identity across participating health organizations through a master patient index, interoperable exchange and governed access.'">
    <div class="health-page health-id-page">
        <header class="hid-nav">
            <a class="hid-brand" href="{{ url($locale) }}" aria-label="OPES Health Systems home">
                <span class="hid-wordmark"><strong>OPES</strong> Health Systems</span>
                <span class="hid-brand-rule" aria-hidden="true"></span>
            </a>
            <nav class="hid-desktop-nav" aria-label="{{ $isFr ? 'Navigation principale' : 'Primary navigation' }}">
                <a href="{{ url($locale.'/health-os') }}">{{ $isFr ? 'Plateforme' : 'Platform' }} <i data-lucide="chevron-down" aria-hidden="true"></i></a>
                <a href="{{ url($locale.'/solutions') }}">{{ $isFr ? 'Solutions' : 'Solutions' }} <i data-lucide="chevron-down" aria-hidden="true"></i></a>
                <a href="{{ url($locale.'/partnerships') }}">{{ $isFr ? 'Partenariats' : 'Partnerships' }}</a>
                <a href="{{ url($locale.'/blog') }}">{{ $isFr ? 'Ressources' : 'Resources' }} <i data-lucide="chevron-down" aria-hidden="true"></i></a>
            </nav>
            <a class="hid-nav-cta" href="{{ url($locale.'/contact?service=opescare') }}">{{ $isFr ? 'Parler à notre équipe' : 'Talk to our team' }}</a>
            <details class="hid-mobile-menu">
                <summary aria-label="{{ $isFr ? 'Ouvrir le menu' : 'Open navigation menu' }}"><span></span><span></span><span></span></summary>
                <nav aria-label="{{ $isFr ? 'Navigation principale' : 'Primary navigation' }}">
                    <a href="{{ url($locale.'/health-os') }}">{{ $isFr ? 'Plateforme' : 'Platform' }}</a>
                    <a href="{{ url($locale.'/solutions') }}">{{ $isFr ? 'Solutions' : 'Solutions' }}</a>
                    <a href="{{ url($locale.'/partnerships') }}">{{ $isFr ? 'Partenariats' : 'Partnerships' }}</a>
                    <a href="{{ url($locale.'/blog') }}">{{ $isFr ? 'Ressources' : 'Resources' }}</a>
                    <a href="{{ url($locale.'/contact?service=opescare') }}">{{ $isFr ? 'Parler à notre équipe' : 'Talk to our team' }}</a>
                </nav>
            </details>
        </header>

        <section class="health-hero" aria-labelledby="health-id-title">
            <div class="health-hero-copy">
                <div class="health-eyebrow">{{ $isFr ? 'SOINS CONNECTÉS · OPESCARE' : 'CONNECTED CARE · OPESCARE' }}</div>
                <h1 id="health-id-title">{{ $isFr ? 'Une identité santé qui relie les soins.' : 'A health identity that' }}@unless($isFr)<br class="health-desktop-break"> connects care.@endunless</h1>
                <p>{{ $isFr ? 'Reliez l’identité des patients entre organisations de santé participantes grâce à un échange d’informations gouverné et fondé sur des standards.' : '' }}@unless($isFr)Link patient identity across participating health<br class="health-desktop-break"> organizations with governed, standards-based<br class="health-desktop-break"> information exchange.@endunless</p>
                <div class="health-actions">
                    <a class="health-button health-button-primary" href="{{ url($locale.'/products/opescare') }}">{{ $isFr ? 'Découvrir OPESCare' : 'Explore OPESCare' }}</a>
                    <a class="health-button health-button-secondary" href="{{ url($locale.'/contact?service=opescare') }}">{{ $isFr ? 'Discuter d’une intégration' : 'Discuss an integration' }}</a>
                </div>
            </div>

            <div class="health-network-card" role="img" aria-label="{{ $isFr ? 'Un Health ID relié aux établissements participants par un contrôle du consentement' : 'A Health ID connected to participating facilities through a consent gate' }}">
                <svg class="health-connectors" viewBox="0 0 747 350" preserveAspectRatio="none" aria-hidden="true"><path d="M218 175H274M372 175H420Q437 175 437 158V65Q437 48 458 48M420 175H458M420 175Q437 175 437 192V285Q437 302 458 302"/></svg>
                <div class="health-id-node">
                    <span class="health-id-corner corner-tl" aria-hidden="true"></span><span class="health-id-corner corner-tr" aria-hidden="true"></span><span class="health-id-corner corner-bl" aria-hidden="true"></span><span class="health-id-corner corner-br" aria-hidden="true"></span>
                    <svg class="health-person" viewBox="0 0 84 84" aria-hidden="true"><circle cx="42" cy="24" r="13"/><path d="M16 73c1-16 12-25 26-25s25 9 26 25H16z"/></svg>
                    <strong>OPES Health ID</strong>
                </div>
                <div class="health-consent"><i data-lucide="lock-keyhole" aria-hidden="true"></i><span>{{ $isFr ? 'Consentement' : 'Consent' }}</span></div>
                <div class="health-facilities">
                    <div class="health-facility"><svg viewBox="0 0 56 56" fill="none" aria-hidden="true"><path d="M9 28 32 9l23 19v27H9V28Z"/><path d="M24 55V39h16v16"/><path class="health-facility-accent" d="M32 18v13M25.5 24.5h13"/></svg><span>{{ $isFr ? 'Clinique' : 'Clinic' }}</span></div>
                    <div class="health-facility"><svg viewBox="0 0 56 56" fill="none" aria-hidden="true"><path d="M23 8h18M28 8v18L15 48a5 5 0 0 0 4.3 7h25.4a5 5 0 0 0 4.3-7L36 26V8"/><path class="health-lab-liquid" d="M21 42h22l6 9a3 3 0 0 1-2.6 4H17.6a3 3 0 0 1-2.6-4l6-9Z"/></svg><span>{{ $isFr ? 'Laboratoire' : 'Laboratory' }}</span></div>
                    <div class="health-facility"><svg viewBox="0 0 56 56" fill="none" aria-hidden="true"><path d="M23 54V10h18v44M8 54V28h15M41 28h15v26M8 54h48"/><path class="health-facility-accent" d="M32 18v12M26 24h12"/><path d="M14 36h3M14 44h3M47 36h3M47 44h3M29 38h6M29 46h6"/></svg><span>{{ $isFr ? 'Hôpital' : 'Hospital' }}</span></div>
                </div>
            </div>
        </section>

        <section class="health-feature-grid" aria-label="{{ $isFr ? 'Composants Health ID' : 'Health ID capabilities' }}">
            @foreach($isFr
                ? [['layers-3','Index maître des patients','Comparer et faire réviser les identités entre les systèmes participants.'],['arrow-left-right','Interopérabilité','Relier les systèmes approuvés au moyen d’API et de standards définis pour le projet.'],['lock-keyhole','Accès fondé sur le consentement','Définir la finalité, les autorisations et les exigences d’audit pour chaque déploiement.']]
                : [['layers-3','Master Patient Index','Match and review identities across participating systems.'],['arrow-left-right','Interoperability','Connect approved systems through scoped APIs and standards.'],['lock-keyhole','Consent-based access','Define purpose, permissions and audit requirements per deployment.']]
            as $feature)
            <article class="health-feature"><span class="health-feature-icon"><i data-lucide="{{ $feature[0] }}" aria-hidden="true"></i></span><div><h2>{{ $feature[1] }}</h2><p>{{ $feature[2] }}</p></div></article>
            @endforeach
        </section>

        <section class="health-workflow" aria-label="{{ $isFr ? 'Parcours de l’identité santé' : 'Health identity workflow' }}">
            @foreach($isFr ? [['id-card','Enregistrer l’identité'],['database','Comparer les dossiers'],['shield-check','Autoriser l’échange'],['file-search','Tracer l’accès']] : [['id-card','Register identity'],['database','Match records'],['shield-check','Authorize exchange'],['file-search','Audit access']] as $index => $step)
                @if($index > 0)<i class="health-step-arrow" data-lucide="arrow-right" aria-hidden="true"></i>@endif
                <div class="health-step"><i data-lucide="{{ $step[0] }}" aria-hidden="true"></i><strong>{{ $step[1] }}</strong></div>
            @endforeach
        </section>

        <p class="health-boundary">{{ $isFr ? 'La couverture, les intégrations, les règles de rapprochement des identités et les accès autorisés sont définis avec les organisations participantes pour chaque déploiement.' : 'Coverage, integrations, identity matching and permitted data access are agreed with participating organizations for each deployment.' }}</p>
    </div>
</x-layouts.app>

<style>
body.health-id-concept-page{--hp-ink:#0b1c36;--hp-blue:#28577f;--hp-cyan:#00a8d2;--hp-copy:#273449;--hp-line:#cfd8e2;background:#fff!important;color:var(--hp-ink)!important;font-family:Inter,Arial,sans-serif!important;margin:0!important}
body.health-id-concept-page .site-nav,body.health-id-concept-page .site-footer{display:none!important}
body.health-id-concept-page #main-content{width:100%;max-width:none;margin:0;padding:0;background:#fff;color:var(--hp-ink)}
.health-id-page,.health-id-page *{box-sizing:border-box}
.health-page{width:100%;max-width:1536px;margin:0 auto;padding:0 33px 30px;color:var(--hp-ink);background:#fff}
.hid-nav{position:relative;left:50%;transform:translateX(-50%);height:82px;width:100vw;margin:0;display:grid;grid-template-columns:360px minmax(0,1fr) 210px;align-items:center;gap:24px;padding:0 45px 0 60px;border-bottom:1px solid #d7e0e8;background:#fff}
.hid-brand{width:max-content;color:var(--hp-ink);text-decoration:none;position:relative;display:flex;align-items:flex-start;height:100%;padding-top:4px}
.hid-wordmark{font-family:'Plus Jakarta Sans',Inter,sans-serif;font-size:34px;letter-spacing:-.045em;white-space:nowrap}.hid-wordmark strong{font-weight:800}.hid-brand-rule{position:absolute;left:0;bottom:22px;width:50px;height:6px;background:var(--hp-cyan)}
.hid-desktop-nav{display:flex;align-items:center;justify-content:flex-start;gap:70px;padding-left:86px;transform:translateY(-5px)}.hid-desktop-nav a{display:inline-flex;align-items:center;gap:5px;color:#111d32;font-size:19px;font-weight:500;text-decoration:none;white-space:nowrap}.hid-desktop-nav a:hover{color:var(--hp-blue)}.hid-desktop-nav i{width:15px;height:15px;stroke-width:1.8}
.hid-nav-cta{justify-self:end;min-width:210px;height:49px;padding:0 20px;border-radius:8px;background:var(--hp-blue);color:white;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:18px;font-weight:700;transform:translateY(-5px)}.hid-nav-cta:hover,.health-button-primary:hover{background:#20476a}
.hid-mobile-menu{display:none}.hid-mobile-menu summary{list-style:none}.hid-mobile-menu summary::-webkit-details-marker{display:none}
.health-hero{height:504px;display:grid;grid-template-columns:626px minmax(0,1fr);column-gap:45px;align-items:start;padding:52px 27px 0}
.health-hero-copy{max-width:626px;padding-top:1px}.health-eyebrow{display:flex;align-items:center;gap:9px;color:#087fa0;font-size:16px;font-weight:750;letter-spacing:.11em;margin:0 0 28px}
.health-hero h1{font-family:Inter,Arial,sans-serif;font-size:56px;font-weight:750;line-height:1.08;letter-spacing:-.042em;margin:0 0 18px;color:var(--hp-ink);max-width:620px;transform:scaleX(1.12);transform-origin:left center}
body.health-id-concept-page .health-hero-copy>p{font-size:26px!important;font-weight:400;line-height:1.28!important;color:var(--hp-copy);max-width:600px;margin:0;transform:scaleX(1.04);transform-origin:left top}
.health-actions{display:flex;flex-wrap:wrap;gap:20px;margin-top:28px}.health-button{height:56px;padding:0 26px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:18px;font-weight:700;white-space:nowrap;transition:background-color .15s ease,border-color .15s ease}.health-button-primary{min-width:272px;background:var(--hp-blue);color:#fff;border:1px solid var(--hp-blue)}.health-button-secondary{min-width:300px;background:#fff;color:var(--hp-ink);border:1.5px solid var(--hp-ink)}.health-button-secondary:hover{background:#f5f7f9}
.health-network-card{height:350px;width:100%;position:relative;overflow:visible;background:transparent;border:0;border-radius:0;margin-top:17px}
.health-connectors{position:absolute;inset:0;width:100%;height:100%;z-index:0;pointer-events:none}.health-connectors path{fill:none;stroke:#173454;stroke-width:2;vector-effect:non-scaling-stroke}
.health-id-node{position:absolute;z-index:1;left:0;top:81px;width:218px;height:200px;border:2px solid #173454;border-radius:12px;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding-top:27px;overflow:hidden}
.health-id-node strong{position:absolute;bottom:0;left:0;right:0;height:47px;display:grid;place-items:center;background:#173454;color:#fff;font-size:21px;letter-spacing:-.02em;font-weight:700}
.health-person{width:104px;height:104px;fill:#173454;margin-top:0}.health-id-corner{position:absolute;top:24px;width:23px;height:23px;border-color:var(--hp-cyan);border-style:solid}.corner-tl{left:44px;border-width:3px 0 0 3px;border-radius:7px 0 0 0}.corner-tr{right:44px;border-width:3px 3px 0 0;border-radius:0 7px 0 0}.corner-bl{left:44px;top:auto;bottom:67px;border-width:0 0 3px 3px;border-radius:0 0 0 7px}.corner-br{right:44px;top:auto;bottom:67px;border-width:0 3px 3px 0;border-radius:0 0 7px 0}
.health-consent{position:absolute;z-index:2;left:274px;top:126px;width:98px;height:98px;border:2px solid var(--hp-cyan);border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;color:var(--hp-ink);font-size:16px;font-weight:500}.health-consent svg{width:31px;height:31px;stroke-width:2.2}
.health-facilities{position:absolute;z-index:1;left:458px;right:0;top:0;display:grid;gap:29px}.health-facility{height:96px;display:grid;grid-template-columns:82px 1px minmax(0,1fr);align-items:center;gap:20px;padding:0 18px;background:#fff;border:1px solid #bdc9d5;border-radius:12px;color:var(--hp-ink)}.health-facility:before{content:"";grid-column:2;grid-row:1;width:1px;height:62px;background:#c8d2dc}.health-facility svg{grid-column:1;grid-row:1;width:56px;height:56px;color:#173454;stroke:#173454;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}.health-facility svg .health-facility-accent{stroke:var(--hp-cyan);stroke-width:4}.health-facility svg .health-lab-liquid{fill:var(--hp-cyan);stroke:none}.health-facility span{grid-column:3;grid-row:1;font-size:21px;font-weight:700;white-space:nowrap}
.health-feature-grid{display:grid;grid-template-columns:1.065fr 1fr 1.08fr;gap:22px;margin:4px 0 0}
.health-feature{height:172px;display:flex;align-items:center;gap:24px;padding:22px 29px;background:#fff;border:1px solid #c8d2dc;border-radius:12px;color:var(--hp-ink)}
.health-feature-icon{width:96px;min-width:96px;height:96px;border-right:1.5px solid var(--hp-cyan);padding-right:18px;display:grid;place-items:center;color:#173454}.health-feature-icon svg{width:62px;height:62px;stroke-width:1.9}.health-feature:nth-child(2) .health-feature-icon svg{transform:scaleY(-1)}
.health-feature h2{font-size:28px;line-height:1.2;letter-spacing:-.035em;font-weight:750;margin:0 0 11px;color:var(--hp-ink);white-space:nowrap}.health-feature p{font-size:19px;line-height:1.43;color:#283449;margin:0}body.health-id-concept-page .health-feature p{font-size:19px!important;line-height:1.43!important}
@media(min-width:1519px){.health-feature:first-child>div{transform:translateY(-10px)}.health-feature:nth-child(2)>div{transform:translateY(4px)}.health-feature:nth-child(2) h2{transform:translateY(-3px)}.health-feature:nth-child(3)>div{transform:translateY(-12px)}}
.health-workflow{height:119px;display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:46px;padding:20px 58px;background:#f0f4f7;border-radius:12px;color:var(--hp-ink)}
.health-step{display:flex;align-items:center;gap:22px;color:var(--hp-ink);white-space:nowrap}.health-step>svg{width:54px;height:54px;color:#173454;stroke-width:1.8;flex:none}.health-step strong{font-size:19px;font-weight:750;line-height:1.25}.health-step-arrow{width:36px;height:30px;color:var(--hp-cyan);stroke-width:2;flex:none}
.health-boundary{display:flex;align-items:center;gap:10px;margin:35px 9px 0;color:#3e4a5b;font-size:17px;line-height:1.45}body.health-id-concept-page .health-boundary{font-size:17px!important;line-height:1.45!important}.health-boundary svg{width:17px;height:17px;color:#5d7083;flex:none}
@media(max-width:1300px){.hid-nav{grid-template-columns:1fr auto 190px;gap:16px;padding:0 22px}.hid-desktop-nav{gap:24px}.hid-desktop-nav a{font-size:15px}.hid-nav-cta{min-width:185px}.health-hero{grid-template-columns:minmax(0,.88fr) minmax(0,1fr);gap:28px;padding-left:20px;padding-right:20px}.health-hero h1{font-size:clamp(44px,4vw,56px)}body.health-id-concept-page .health-hero-copy>p{font-size:19px}.health-network-card{transform:scale(.94);transform-origin:top left;width:calc(100% / .94);margin-bottom:-21px}.health-feature{gap:16px;padding:18px}.health-feature-icon{width:72px;min-width:72px;padding-right:12px}.health-feature-icon svg{width:50px;height:50px}.health-feature h2{font-size:22px;white-space:normal}.health-feature p{font-size:16px}.health-workflow{padding-left:28px;padding-right:28px}.health-step{gap:12px}.health-step>svg{width:42px;height:42px}.health-step strong{font-size:16px}}
@media(max-width:1518px){.hid-nav{position:static;left:auto;transform:none;height:74px;width:calc(100% + 48px);margin-left:-24px;margin-right:-24px;grid-template-columns:1fr auto;padding:0 44px}.hid-brand-rule{bottom:15px}.hid-desktop-nav,.hid-nav-cta{display:none}.hid-mobile-menu{display:block;position:relative;justify-self:end}.hid-mobile-menu summary{width:44px;height:44px;border:1px solid #cad4dd;border-radius:8px;display:flex;flex-direction:column;justify-content:center;gap:5px;padding:0 11px;cursor:pointer}.hid-mobile-menu summary span{height:2px;width:20px;background:#173454;border-radius:2px}.hid-mobile-menu nav{position:absolute;z-index:5;right:0;top:52px;width:min(280px,calc(100vw - 40px));padding:8px;background:white;border:1px solid #d3dce4;border-radius:10px;box-shadow:0 8px 24px rgba(11,43,82,.12);display:grid}.hid-mobile-menu nav a{padding:13px 12px;color:#0b1c36;text-decoration:none;border-radius:6px;font-size:15px}.hid-mobile-menu nav a:hover{background:#f0f4f7}.health-page{padding:0 24px 26px}.health-hero{height:auto;min-height:0;grid-template-columns:1fr;gap:12px;padding:35px 6px 0}.health-hero-copy{max-width:700px}.health-hero h1{font-size:clamp(42px,7vw,58px);transform:none}body.health-id-concept-page .health-hero-copy>p{font-size:19px!important;line-height:1.5!important;max-width:650px;transform:none}.health-network-card{transform:none;width:min(747px,100%);justify-self:center;margin:12px auto 28px}.health-feature-grid{margin-top:0}.health-feature{height:150px}.health-workflow{margin-top:28px}.health-boundary{margin-top:22px}}
@media(max-width:1518px){.health-feature-grid{grid-template-columns:1fr}.health-feature{height:auto;min-height:140px}.health-workflow{height:auto;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px;padding:24px 28px}.health-step-arrow{display:none}.health-feature p{font-size:16px!important;line-height:1.45!important}body.health-id-concept-page .health-boundary{font-size:15px!important;line-height:1.5!important}}
@media(max-width:620px){.hid-nav{height:70px;width:calc(100% + 32px);margin-left:-16px;margin-right:-16px;padding:0 17px 0 33px}.hid-brand{padding-top:20px}.hid-wordmark{font-size:22px}.hid-brand-rule{height:4px;width:42px;bottom:13px}.health-page{padding:0 16px 24px}.health-hero{padding:30px 3px 0;gap:22px}.health-desktop-break{display:none}.health-eyebrow{font-size:12px;letter-spacing:.1em;margin-bottom:18px}.health-hero h1{font-size:clamp(37px,10.5vw,48px);line-height:1.08;letter-spacing:-.04em;margin-bottom:15px}body.health-id-concept-page .health-hero-copy>p{font-size:17px!important;line-height:1.55!important}.health-actions{display:grid;grid-template-columns:1fr;gap:10px;margin-top:22px}.health-button{width:100%;height:52px;min-width:0;font-size:16px}.health-network-card{height:390px;width:100%;margin:4px auto 23px;transform:none}.health-id-node{left:50%;top:0;transform:translateX(-50%);width:190px;height:172px;padding-top:18px}.health-person{width:68px;height:68px}.health-id-node strong{height:42px;font-size:18px}.health-id-corner{top:16px;width:19px;height:19px}.corner-tl{left:35px}.corner-tr{right:35px}.health-consent{left:50%;top:185px;transform:translateX(-50%);width:84px;height:84px;font-size:14px}.health-consent svg{width:26px;height:26px}.health-facilities{left:0;right:0;top:292px;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px}.health-facility{height:88px;grid-template-columns:1fr;grid-template-rows:45px auto;justify-items:center;gap:2px;padding:7px 3px}.health-facility:before{display:none}.health-facility svg{grid-column:1;grid-row:1;width:38px;height:38px}.health-facility span{grid-column:1;grid-row:2;font-size:12px;white-space:normal;text-align:center}.health-connectors{display:none}.health-feature-grid{grid-template-columns:1fr;gap:12px}.health-feature{height:auto;min-height:122px;padding:15px 14px;gap:15px}.health-feature-icon{width:66px;min-width:66px;height:76px;padding-right:11px}.health-feature-icon svg{width:46px;height:46px}.health-feature h2{font-size:19px;line-height:1.2;margin-bottom:6px;white-space:normal}body.health-id-concept-page .health-feature p{font-size:14px!important;line-height:1.45!important}body.health-id-concept-page .health-boundary{font-size:13px!important;line-height:1.5!important}.health-workflow{height:auto;display:grid;grid-template-columns:1fr;margin-top:20px;padding:13px 17px;gap:8px}.health-step{min-height:47px;gap:15px}.health-step>svg{width:34px;height:34px}.health-step strong{font-size:15px}.health-step-arrow{display:block;width:24px;height:19px;transform:rotate(90deg);margin-left:5px}.health-boundary{align-items:flex-start;margin:17px 2px 0;font-size:13px;line-height:1.5}}
@media(prefers-reduced-motion:reduce){.health-button{transition:none}}
</style>
