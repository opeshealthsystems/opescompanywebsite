@php
    $fr = app()->getLocale() === 'fr';
    $appRelease = config('mobile_app');
    $appSchema = [
        '@context' => 'https://schema.org', '@type' => 'SoftwareApplication',
        'name' => 'OPES Health', 'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Android', 'softwareVersion' => $appRelease['version'],
        'downloadUrl' => $appRelease['download_url'] ?? asset($appRelease['download_path']),
        'description' => $fr
            ? 'Application OPES Health en aperçu privé pour les équipes, clients, praticiens et testeurs, avec des cours à étudier, le suivi de progression et les certificats.'
            : 'OPES Health app private preview for staff, customers, practitioners and testers, with study lessons, learning progress and certificates.',
        'publisher' => ['@type' => 'Organization', 'name' => 'OPES Health Systems', 'url' => 'https://opeshealthsystems.com'],
    ];
@endphp
<x-layouts.app :title="$fr ? 'Application OPES Health et mises à jour' : 'OPES Health app and updates'" :description="$fr ? 'Aperçu de l’application mobile OPES Health : accès sécurisé, services par rôle, leçons d’étude, progression et certificats.' : 'Preview the OPES Health mobile app: secure role-based workspaces, study lessons, learning progress and certificates.'">
<main class="ohs-app-page">
    <header class="ohs-app-hero">
        <div>
            <p class="ohs-app-eyebrow">OPES HEALTH SYSTEMS · {{ $fr ? 'APPLICATION MOBILE' : 'MOBILE APP' }}</p>
            <h1>{{ $fr ? 'Les services OPES, dans votre espace mobile.' : 'OPES services, in your mobile workspace.' }}</h1>
            <p class="ohs-app-lede">{{ $fr ? 'Un accès sécurisé aux comptes, services et outils OPES, avec des écrans adaptés à chaque rôle.' : 'Secure access to OPES accounts, services and work tools, with screens tailored to each account role.' }}</p>
            <div class="ohs-app-actions">
                <a class="ohs-app-primary" href="opeshealth://">{{ $fr ? 'Se connecter dans l’application' : 'Sign in in the app' }}</a>
                <a class="ohs-app-secondary" href="#download">{{ $fr ? 'Télécharger l’aperçu Android' : 'Download Android preview' }}</a>
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
            <article><h3>{{ $fr ? 'Équipe et administration' : 'Staff and administrators' }}</h3><p>{{ $fr ? 'Gérez les devis, les profils clients, les contacts et demandes, les dossiers CRM et les demandes de partenariat selon les autorisations du compte.' : 'Manage quotes, customer profiles, contacts and enquiries, CRM deals and partner applications according to account permissions.' }}</p></article>
            <article><h3>{{ $fr ? 'Clients' : 'Customers' }}</h3><p>{{ $fr ? 'Consultez le profil, les demandes de service, factures et tickets d’assistance.' : 'Access your profile, service requests, invoices and support tickets.' }}</p></article>
            <article><h3>{{ $fr ? 'Praticiens de santé' : 'Health practitioners' }}</h3><p>{{ $fr ? 'Étudiez des leçons à votre rythme, reprenez là où vous vous êtes arrêté, suivez votre progression et consultez vos certificats.' : 'Study lessons at your pace, continue where you left off, track course progress and review your certificates.' }}</p></article>
            <article><h3>{{ $fr ? 'Testeurs' : 'Testers' }}</h3><p>{{ $fr ? 'Consultez vos affectations, mettez à jour leur progression et signalez les problèmes.' : 'Review assignments, update progress and report issues.' }}</p></article>
        </div>
        <p class="ohs-app-note">{{ $fr ? 'L’accès utilise OAuth 2.0 avec PKCE. Les API vérifient les rôles et autorisations à chaque requête. La gestion des contacts est réservée aux rôles administrateur ; la gestion privée des comptes partenaires reste à venir.' : 'Access uses OAuth 2.0 with PKCE. The API checks roles and permissions on every request. Contact management is limited to administrator roles; private partner account management is still planned.' }}</p>
    </section>

    <section class="ohs-app-section" id="updates">
        <p class="ohs-app-eyebrow">{{ $fr ? 'HISTORIQUE DES VERSIONS' : 'VERSION HISTORY' }}</p>
        <h2>{{ $fr ? 'Versions et mises à jour' : 'Versions and updates' }}</h2>
        @foreach($appRelease['releases'] as $release)
            <article class="ohs-app-release">
                <div class="ohs-app-version">
                    <strong>{{ $release['version'] }}</strong>
                    <span>{{ $fr ? 'BUILD ANDROID' : 'ANDROID BUILD' }} {{ $release['build'] }}</span>
                    <span class="{{ $release['current'] ? 'ohs-app-current' : '' }}">{{ $release['current'] ? ($fr ? 'APERÇU ACTUEL' : 'CURRENT PREVIEW') : ($fr ? 'REMPLACÉ' : 'SUPERSEDED') }}</span>
                </div>
                <div>
                    <h3>{{ $release['title'][$fr ? 'fr' : 'en'] }}</h3>
                    <p class="ohs-app-release-date">{{ \Illuminate\Support\Carbon::parse($release['date'])->locale(app()->getLocale())->translatedFormat('j F Y') }} · {{ $fr ? 'Version de l’application' : 'App version' }} {{ $release['version'] }} · {{ $fr ? 'Build Android' : 'Android build' }} {{ $release['build'] }}</p>
                    <p>{{ $release['summary'][$fr ? 'fr' : 'en'] }}</p>
                    @if(count($release['changes'][$fr ? 'fr' : 'en']))
                        <ul>@foreach($release['changes'][$fr ? 'fr' : 'en'] as $change)<li>{{ $change }}</li>@endforeach</ul>
                    @endif
                    @if($release['current'])
                        <p class="ohs-app-ota-label">{{ $fr ? 'Mises à jour OTA publiées pour le runtime' : 'OTA updates published for runtime' }} {{ $appRelease['runtime_version'] }}</p>
                        <ul class="ohs-app-ota-list">
                            @foreach($appRelease['ota_updates'] as $ota)
                                <li>
                                    <strong>{{ $ota['date'] }} · {{ $fr ? 'Runtime' : 'Runtime' }} {{ $ota['runtime'] }} · {{ $ota['title'][$fr ? 'fr' : 'en'] }}</strong>
                                    @if(!empty($ota['summary'][$fr ? 'fr' : 'en']))<p>{{ $ota['summary'][$fr ? 'fr' : 'en'] }}</p>@endif
                                    <code>{{ $ota['group'] }}</code>
                                </li>
                            @endforeach
                        </ul>
                        <a class="ohs-app-build-link" href="{{ $appRelease['eas_build_url'] }}" target="_blank" rel="noopener noreferrer">{{ $fr ? 'Détails de compilation EAS' : 'EAS build details' }}</a>
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    <section class="ohs-app-bottom" id="download">
        <h2>{{ $fr ? 'Télécharger la dernière version Android' : 'Download the latest Android app' }}</h2>
        <p>{{ $fr ? 'Version' : 'Version' }} {{ $appRelease['version'] }} · {{ $fr ? 'Build Android' : 'Android build' }} {{ $appRelease['android_build'] }} · {{ $fr ? 'Aperçu privé' : 'Private preview' }} · {{ $fr ? 'Runtime OTA' : 'OTA runtime' }} {{ $appRelease['runtime_version'] }}</p>
        <p>{{ $fr ? 'Cette version remplace les anciens builds d’aperçu. Connectez-vous avec votre compte OPES ; votre rôle détermine votre espace.' : 'This build supersedes earlier preview builds. Sign in with your OPES account; your role determines your workspace.' }}</p>
        <a href="{{ $appRelease['download_url'] ?? asset($appRelease['download_path']) }}" @if(empty($appRelease['download_url'])) download @endif>{{ $fr ? 'Télécharger l’application Android' : 'Download Android app' }}</a>
        <p class="ohs-app-checksum"><strong>SHA-256:</strong> <code>{{ $appRelease['sha256'] }}</code></p>
        <p><a href="{{ $appRelease['eas_build_url'] }}" target="_blank" rel="noopener noreferrer">{{ $fr ? 'Voir les détails de cette compilation' : 'View this build on EAS' }}</a></p>
        <p>{{ $fr ? 'Besoin d’aide ?' : 'Need help?' }} <a href="mailto:contact@opeshealthsystems.com">contact@opeshealthsystems.com</a></p>
    </section>
</main>
<script type="application/ld+json">{!! json_encode($appSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
<style>
.ohs-app-page{width:100%;max-width:none;box-sizing:border-box;margin:0;padding:68px max(24px,calc((100vw - 1120px)/2)) 80px;background:#F5F8FC;color:#183853;line-height:1.65}.ohs-app-hero{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(220px,.6fr);gap:36px;align-items:center;padding:44px 0 60px}.ohs-app-eyebrow{font-size:13px;font-weight:700;letter-spacing:1.4px;color:#315F7D;margin:0 0 12px}.ohs-app-hero h1{max-width:720px;font-size:clamp(36px,5vw,58px);line-height:1.08;color:#082d55;margin:0 0 18px}.ohs-app-lede{max-width:650px;font-size:18px;color:#52677d;margin:0}.ohs-app-mark{justify-self:center;width:min(100%,280px);aspect-ratio:1}.ohs-app-mark img{display:block;width:100%;height:100%;object-fit:contain;border-radius:18px}.ohs-app-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:26px}.ohs-app-actions a{min-height:46px;display:inline-flex;align-items:center;padding:10px 18px;border:1px solid #082d55;border-radius:5px;text-decoration:none;font-weight:650}.ohs-app-primary{background:#082d55;color:#fff}.ohs-app-actions .ohs-app-secondary{color:#082d55;background:#fff}.ohs-app-status{display:flex;align-items:center;gap:8px;color:#52677d;font-size:13px;margin-top:18px}.ohs-app-status span{width:8px;height:8px;border-radius:50%;background:#52677d}.ohs-app-section{padding:34px 0 40px;border-top:1px solid #D9E2EC}.ohs-app-section h2,.ohs-app-bottom h2{font-size:clamp(26px,3vw,34px);line-height:1.2;color:#082d55;margin:0 0 24px}.ohs-app-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.ohs-app-grid article{padding:21px 22px;border:1px solid #D9E2EC;border-radius:7px;background:#FFFFFF}.ohs-app-grid h3,.ohs-app-release h3{font-size:17px;color:#153957;margin:0 0 8px}.ohs-app-grid p,.ohs-app-release p,.ohs-app-bottom p{color:#52677d;margin:0}.ohs-app-note{border-left:3px solid #7f9eb8;padding:12px 16px;color:#40586e;background:#EDF3F8;margin:18px 0 0;font-size:14px}.ohs-app-release{display:grid;grid-template-columns:170px minmax(0,1fr);gap:22px;padding:22px 0;border-top:1px solid #D9E2EC}.ohs-app-release:last-child{border-bottom:1px solid #D9E2EC}.ohs-app-version{display:flex;flex-direction:column;gap:4px;color:#082d55}.ohs-app-version strong{font-size:21px}.ohs-app-version span{font-size:13px;letter-spacing:1px;font-weight:700;color:#52677d}.ohs-app-release ul{margin:12px 0 0;padding-left:20px;color:#52677d}.ohs-app-future{opacity:1}.ohs-app-bottom{margin-top:38px;padding:28px;border:1px solid #D9E2EC;border-radius:7px;background:#FFFFFF}.ohs-app-bottom h2{margin-bottom:10px}.ohs-app-bottom a{display:inline-block;margin-top:10px;color:#315F7D;font-weight:650}@media(max-width:720px){.ohs-app-page{padding:38px 17px 58px}.ohs-app-hero{grid-template-columns:1fr;gap:22px;padding:24px 0 42px}.ohs-app-mark{width:140px;grid-row:1;justify-self:start}.ohs-app-grid{grid-template-columns:1fr}.ohs-app-release{grid-template-columns:1fr;gap:8px}.ohs-app-status{align-items:flex-start}}
</style>
<style>
.ohs-app-release>div:last-child{min-width:0}.ohs-app-release-date,.ohs-app-ota-label{font-size:13px}.ohs-app-version .ohs-app-current{color:#315F7D}.ohs-app-ota-label{margin-top:14px!important;font-weight:700}.ohs-app-ota-list{overflow-wrap:anywhere}.ohs-app-ota-list code{overflow-wrap:anywhere;font-size:13px;color:#52677d}.ohs-app-build-link{display:inline-block;margin-top:12px;color:#315F7D;font-weight:650}.ohs-app-checksum code{display:block;overflow-wrap:anywhere;color:#40586e;font-size:13px}
</style>
</x-layouts.app>
