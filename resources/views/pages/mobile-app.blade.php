@php
    $fr = app()->getLocale() === 'fr';
    $appSchema = [
        '@context' => 'https://schema.org', '@type' => 'SoftwareApplication',
        'name' => 'OPES Health', 'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Android', 'softwareVersion' => '0.1.0-preview',
        'description' => $fr
            ? 'Application OPES Health en aperçu privé pour les équipes, clients, praticiens et testeurs.'
            : 'OPES Health app private preview for staff, customers, practitioners and testers.',
        'publisher' => ['@type' => 'Organization', 'name' => 'OPES Health Systems', 'url' => 'https://opeshealthsystems.com'],
    ];
@endphp
<x-layouts.app :title="$fr ? 'Application OPES Health et mises à jour' : 'OPES Health app and updates'" :description="$fr ? 'Aperçu de l’application mobile OPES Health : accès sécurisé, services par rôle et historique des versions.' : 'Preview the OPES Health mobile app: secure role-based access, account services and version updates.'">
<main class="ohs-app-page">
    <header class="ohs-app-hero">
        <div>
            <p class="ohs-app-eyebrow">OPES HEALTH SYSTEMS · {{ $fr ? 'APPLICATION MOBILE' : 'MOBILE APP' }}</p>
            <h1>{{ $fr ? 'Les services OPES, dans votre espace mobile.' : 'OPES services, in your mobile workspace.' }}</h1>
            <p class="ohs-app-lede">{{ $fr ? 'Un accès sécurisé aux comptes, services et outils OPES, avec des écrans adaptés à chaque rôle.' : 'Secure access to OPES accounts, services and work tools, with screens tailored to each account role.' }}</p>
            <div class="ohs-app-actions">
                <a class="ohs-app-primary" href="#download">{{ $fr ? 'Télécharger l’aperçu Android' : 'Download Android preview' }}</a>
                <a class="ohs-app-secondary" href="{{ url('/'.app()->getLocale().'/developers') }}">{{ $fr ? 'Documentation API' : 'API documentation' }}</a>
            </div>
            <p class="ohs-app-status"><span></span>{{ $fr ? 'Aperçu privé Android · Connexion sécurisée par rôle' : 'Private Android preview · Secure role-based sign-in' }}</p>
        </div>
        <div class="ohs-app-mark" aria-hidden="true"><img src="{{ asset('mobile-app/icon.png') }}" alt=""></div>
    </header>

    <section class="ohs-app-section">
        <p class="ohs-app-eyebrow">{{ $fr ? 'UN SEUL ACCÈS' : 'ONE SECURE SIGN-IN' }}</p>
        <h2>{{ $fr ? 'Chaque rôle retrouve son propre espace.' : 'Every account gets its own workspace.' }}</h2>
        <div class="ohs-app-grid">
            <article><h3>{{ $fr ? 'Équipe et administration' : 'Staff and administrators' }}</h3><p>{{ $fr ? 'Les API publiées prennent en charge les devis, profils clients, demandes de partenariat et dossiers CRM selon les droits du compte.' : 'Published APIs support quotes, customer profiles, partner applications and CRM records according to account permissions.' }}</p></article>
            <article><h3>{{ $fr ? 'Clients' : 'Customers' }}</h3><p>{{ $fr ? 'Consultez le profil, les demandes de service, factures et tickets d’assistance.' : 'Access your profile, service requests, invoices and support tickets.' }}</p></article>
            <article><h3>{{ $fr ? 'Praticiens de santé' : 'Health practitioners' }}</h3><p>{{ $fr ? 'Gérez votre profil et accédez aux cours et certificats disponibles pour votre compte.' : 'Manage your profile and access courses and certificates available to your account.' }}</p></article>
            <article><h3>{{ $fr ? 'Testeurs' : 'Testers' }}</h3><p>{{ $fr ? 'Consultez vos affectations, mettez à jour leur progression et signalez les problèmes.' : 'Review assignments, update progress and report issues.' }}</p></article>
        </div>
        <p class="ohs-app-note">{{ $fr ? 'L’accès est autorisé par OAuth 2.0 avec PKCE. Les API appliquent les rôles, autorisations et accès aux dossiers à chaque requête. Les fonctions de gestion des contacts et des comptes partenaires restent à ajouter au contrat API.' : 'Access uses OAuth 2.0 with PKCE. The API checks roles, permissions and record access on every request. Contact-directory management and partner self-service accounts still need API operations.' }}</p>
    </section>

    <section class="ohs-app-section" id="updates">
        <p class="ohs-app-eyebrow">{{ $fr ? 'HISTORIQUE DES VERSIONS' : 'VERSION HISTORY' }}</p>
        <h2>{{ $fr ? 'Versions et mises à jour' : 'Versions and updates' }}</h2>
        <div class="ohs-app-release">
            <div class="ohs-app-version"><strong>0.1.0</strong><span>{{ $fr ? 'APERÇU PRIVÉ' : 'PRIVATE PREVIEW' }}</span></div>
            <div><h3>{{ $fr ? 'Fondations de l’application' : 'App foundation' }}</h3><p>{{ $fr ? '1 novembre 2026 · Connexion OAuth PKCE, routage automatique selon les rôles RBAC, identité visuelle et API publiées.' : '1 November 2026 · OAuth PKCE sign-in, automatic RBAC workspace routing, app branding and published API connections.' }}</p><ul><li>{{ $fr ? 'Espaces équipe, client, praticien, testeur et partenaire' : 'Staff, customer, practitioner, tester and partner workspaces' }}</li><li>{{ $fr ? 'Jetons stockés dans le coffre sécurisé natif' : 'Tokens stored in native secure storage' }}</li><li>{{ $fr ? 'Mises à jour OTA pour les versions compatibles' : 'OTA updates for compatible installed builds' }}</li><li>{{ $fr ? 'Aucun compte ni donnée de démonstration' : 'No demo accounts or sample business data' }}</li></ul></div>
        </div>
        <div class="ohs-app-release ohs-app-future"><div class="ohs-app-version"><strong>1.0.0</strong><span>{{ $fr ? 'À VENIR' : 'PLANNED' }}</span></div><div><h3>{{ $fr ? 'Première version publique' : 'First public release' }}</h3><p>{{ $fr ? 'La publication suivra les validations des boutiques et la confirmation des API de gestion des comptes partenaires et des contacts.' : 'Public release follows store readiness and completion of contact-directory and partner-account APIs.' }}</p></div></div>
    </section>

    <section class="ohs-app-bottom" id="download">
        @if(file_exists(public_path('mobile-app/opes-health-0.1.0.apk')))
            <h2>{{ $fr ? 'Télécharger l’aperçu Android' : 'Download the Android preview' }}</h2>
            <p>{{ $fr ? 'Version 0.1.0 · Aperçu privé Android. Utilisez votre compte OPES ; vos rôles déterminent votre espace.' : 'Version 0.1.0 · Private Android preview. Sign in with your OPES account; your roles determine your workspace.' }}</p>
            <a href="{{ asset('mobile-app/opes-health-0.1.0.apk') }}" download>{{ $fr ? 'Télécharger le fichier APK' : 'Download Android APK' }}</a>
        @else
            <h2>{{ $fr ? 'Aperçu Android en préparation' : 'Android preview in progress' }}</h2>
            <p>{{ $fr ? 'Le fichier d’installation sera disponible ici après la compilation et la vérification.' : 'The install file will appear here after the Android build is complete and verified.' }}</p>
        @endif
        <p>{{ $fr ? 'Besoin d’aide ?' : 'Need help?' }} <a href="mailto:contact@opeshealthsystems.com">contact@opeshealthsystems.com</a></p>
    </section>
</main>
<script type="application/ld+json">{!! json_encode($appSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
<style>
.ohs-app-page{width:100%;max-width:none;box-sizing:border-box;margin:0;padding:68px max(24px,calc((100vw - 1120px)/2)) 80px;background:#F5F8FC;color:#183853;line-height:1.65}.ohs-app-hero{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(220px,.6fr);gap:36px;align-items:center;padding:44px 0 60px}.ohs-app-eyebrow{font-size:12px;font-weight:700;letter-spacing:1.4px;color:#2369a6;margin:0 0 12px}.ohs-app-hero h1{max-width:720px;font-size:clamp(36px,5vw,58px);line-height:1.08;color:#082d55;margin:0 0 18px}.ohs-app-lede{max-width:650px;font-size:18px;color:#52677d;margin:0}.ohs-app-mark{justify-self:center;width:min(100%,280px);aspect-ratio:1}.ohs-app-mark img{display:block;width:100%;height:100%;object-fit:contain;border-radius:18px}.ohs-app-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:26px}.ohs-app-actions a{min-height:46px;display:inline-flex;align-items:center;padding:10px 18px;border:1px solid #082d55;border-radius:5px;text-decoration:none;font-weight:650}.ohs-app-primary{background:#082d55;color:#fff}.ohs-app-actions .ohs-app-secondary{color:#082d55;background:#fff}.ohs-app-status{display:flex;align-items:center;gap:8px;color:#52677d;font-size:13px;margin-top:18px}.ohs-app-status span{width:8px;height:8px;border-radius:50%;background:#52677d}.ohs-app-section{padding:34px 0 40px;border-top:1px solid #D9E2EC}.ohs-app-section h2,.ohs-app-bottom h2{font-size:clamp(26px,3vw,34px);line-height:1.2;color:#082d55;margin:0 0 24px}.ohs-app-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.ohs-app-grid article{padding:21px 22px;border:1px solid #D9E2EC;border-radius:7px;background:#FFFFFF}.ohs-app-grid h3,.ohs-app-release h3{font-size:17px;color:#153957;margin:0 0 8px}.ohs-app-grid p,.ohs-app-release p,.ohs-app-bottom p{color:#52677d;margin:0}.ohs-app-note{border-left:3px solid #7f9eb8;padding:12px 16px;color:#40586e;background:#EDF3F8;margin:18px 0 0;font-size:14px}.ohs-app-release{display:grid;grid-template-columns:170px minmax(0,1fr);gap:22px;padding:22px 0;border-top:1px solid #D9E2EC}.ohs-app-release:last-child{border-bottom:1px solid #D9E2EC}.ohs-app-version{display:flex;flex-direction:column;gap:4px;color:#082d55}.ohs-app-version strong{font-size:21px}.ohs-app-version span{font-size:10px;letter-spacing:1px;font-weight:700;color:#52677d}.ohs-app-release ul{margin:12px 0 0;padding-left:20px;color:#52677d}.ohs-app-future{opacity:1}.ohs-app-bottom{margin-top:38px;padding:28px;border:1px solid #D9E2EC;border-radius:7px;background:#FFFFFF}.ohs-app-bottom h2{margin-bottom:10px}.ohs-app-bottom a{display:inline-block;margin-top:10px;color:#145c95;font-weight:650}@media(max-width:720px){.ohs-app-page{padding:38px 17px 58px}.ohs-app-hero{grid-template-columns:1fr;gap:22px;padding:24px 0 42px}.ohs-app-mark{width:140px;grid-row:1;justify-self:start}.ohs-app-grid{grid-template-columns:1fr}.ohs-app-release{grid-template-columns:1fr;gap:8px}.ohs-app-status{align-items:flex-start}}
</style>
</x-layouts.app>
