@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Conditions d\'utilisation — OPES Health Systems' : 'Terms of Use — OPES Health Systems' }}"
    description="{{ $isFr ? 'Conditions d\'utilisation de la plateforme et du site web OPES Health Systems, régis par le droit OHADA et la réglementation commerciale camerounaise.' : 'Terms of use for OPES Health Systems platform and website, governed by OHADA law and Cameroonian commercial regulations.' }}">

<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">{{ $isFr ? 'Accueil' : 'Home' }}</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">{{ $isFr ? 'Conditions d\'utilisation' : 'Terms of Use' }}</span>
</div>

<article class="section" style="max-width:860px;margin:0 auto">

    <div style="margin-bottom:40px">
        <div class="section-label" style="margin-bottom:12px">
            <i data-lucide="file-text" style="width:12px;height:12px"></i>
            {{ $isFr ? 'Juridique' : 'Legal' }}
        </div>
        <h1 class="section-title">{{ $isFr ? 'Conditions d\'utilisation' : 'Terms of Use' }}</h1>
        <p class="section-sub">{{ $isFr ? 'Date d\'effet : 1er janvier 2025 &nbsp;·&nbsp; OPES Health Systems SARL' : 'Effective date: 1 January 2025 &nbsp;·&nbsp; OPES Health Systems SARL' }}</p>
    </div>

    <div class="blog-art-body prose">

        @if($isFr)
        <h2>1. Acceptation</h2>
        <p>En accédant à ce site web ou en utilisant la Plateforme OPES (« Plateforme »), vous acceptez d'être lié par les présentes Conditions d'utilisation. Si vous n'y consentez pas, veuillez ne pas utiliser la Plateforme.</p>

        <h2>2. Octroi de licence</h2>
        <p>Sous réserve d'un contrat de licence valide, OPES accorde à votre établissement une licence non exclusive et non transférable pour utiliser la Plateforme uniquement dans le cadre de vos opérations internes de santé. Les licences sont délivrées par établissement et par module produit, conformément à votre bon de commande.</p>

        <h2>3. Usage autorisé</h2>
        <p>Vous pouvez utiliser la Plateforme pour gérer les dossiers patients, la facturation, la pharmacie, le laboratoire et les opérations connexes de votre établissement de santé. Il vous est interdit de : (a) procéder à l'ingénierie inverse ou à la décompilation de la Plateforme ; (b) sous-licencier ou revendre l'accès ; (c) utiliser la Plateforme à toute fin illicite au regard du droit camerounais ou de la législation applicable.</p>

        <h2>4. Données patients</h2>
        <p>Votre établissement est le responsable du traitement de toutes les informations de santé des patients traitées via la Plateforme. Vous êtes tenu d'obtenir tous les consentements nécessaires des patients et de vous conformer à la réglementation camerounaise en matière de données de santé. OPES traite ces données uniquement en tant que sous-traitant, sur vos instructions.</p>

        <h2>5. Disponibilité et assistance</h2>
        <p>Les déploiements hébergés dans le cloud visent une disponibilité mensuelle de 99,5 %. Le support est assuré pendant les heures ouvrables camerounaises (lundi–vendredi, 08h00–18h00 WAT). Les déploiements sur site sont maintenus dans le cadre d'un contrat de support distinct.</p>

        <h2>6. Propriété intellectuelle</h2>
        <p>Tous les droits de propriété intellectuelle relatifs à la Plateforme (logiciels, designs, documentation) demeurent la propriété exclusive d'OPES Health Systems SARL. Votre licence ne vous transfère aucun droit de propriété.</p>

        <h2>7. Limitation de responsabilité</h2>
        <p>Dans la limite permise par le droit OHADA, la responsabilité totale d'OPES pour toute réclamation découlant de l'utilisation de la Plateforme est limitée aux frais de licence payés par votre établissement au cours des 12 mois précédant la réclamation. OPES n'est pas responsable des dommages indirects, consécutifs ou cliniques.</p>

        <h2>8. Droit applicable</h2>
        <p>Les présentes Conditions sont régies par les Actes uniformes OHADA et les lois du Cameroun. Tout litige sera soumis aux tribunaux compétents de Douala, Cameroun.</p>

        <h2>9. Contact</h2>
        <p>OPES Health Systems SARL<br>
        Bonamousadi, Douala, Cameroun<br>
        E-mail : <a href="mailto:legal@opeshealthsystems.com" style="color:#00C896">legal@opeshealthsystems.com</a></p>

        @else
        <h2>1. Acceptance</h2>
        <p>By accessing this website or using the OPES Platform ("Platform"), you agree to be bound by these Terms of Use. If you do not agree, do not use the Platform.</p>

        <h2>2. Licence Grant</h2>
        <p>Subject to a valid licence agreement, OPES grants your facility a non-exclusive, non-transferable licence to use the Platform solely for your internal health facility operations. Licences are issued per facility and per product module as agreed in your order form.</p>

        <h2>3. Permitted Use</h2>
        <p>You may use the Platform to manage patient records, billing, pharmacy, laboratory and related health facility operations. You must not: (a) reverse-engineer or decompile the Platform; (b) sublicense or resell access; (c) use the Platform for any purpose unlawful under Cameroonian or applicable law.</p>

        <h2>4. Patient Data</h2>
        <p>Your facility is the data controller for all patient health information processed through the Platform. You are responsible for obtaining all necessary consents from patients and for complying with Cameroonian health data regulations. OPES processes such data solely as a processor on your instructions.</p>

        <h2>5. Uptime and Support</h2>
        <p>Cloud-hosted deployments are targeted at 99.5% monthly uptime. Support is provided during Cameroon business hours (Monday–Friday 08:00–18:00 WAT). On-premise deployments are maintained under a separate support agreement.</p>

        <h2>6. Intellectual Property</h2>
        <p>All intellectual property in the Platform (software, designs, documentation) remains the exclusive property of OPES Health Systems SARL. Your licence does not transfer any ownership rights.</p>

        <h2>7. Limitation of Liability</h2>
        <p>To the maximum extent permitted by OHADA law, OPES's aggregate liability for any claim arising out of use of the Platform is limited to the licence fees paid by your facility in the 12 months preceding the claim. OPES is not liable for any indirect, consequential or clinical harm.</p>

        <h2>8. Governing Law</h2>
        <p>These Terms are governed by OHADA Uniform Acts and the laws of Cameroon. Any dispute shall be submitted to the competent courts of Douala, Cameroon.</p>

        <h2>9. Contact</h2>
        <p>OPES Health Systems SARL<br>
        Bonamousadi, Douala, Cameroon<br>
        Email: <a href="mailto:legal@opeshealthsystems.com" style="color:#00C896">legal@opeshealthsystems.com</a></p>
        @endif

    </div>

</article>

</x-layouts.app>
