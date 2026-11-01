@php $isFr = app()->getLocale() === 'fr'; @endphp
<x-layouts.app>
<section class="section" style="max-width:900px;margin:auto">
    <h1 class="section-title">{{ $isFr ? 'Support et contrats de service' : 'Support and service agreements' }}</h1>
    <p class="section-sub">{{ $isFr ? 'Définissez le support adapté à votre établissement avant le déploiement.' : 'Agree the support your facility needs before deployment.' }}</p>
    <p class="brand-notice">{{ $isFr ? 'Les délais de réponse, horaires, objectifs de disponibilité et exclusions sont définis dans votre contrat signé. Aucun pourcentage de disponibilité ni support permanent n’est garanti par cette page.' : 'Response times, service hours, availability targets and exclusions are defined in your signed agreement. This page does not guarantee a particular uptime percentage or round-the-clock support.' }}</p>
    <h2 style="font-size:22px;margin:28px 0 12px">{{ $isFr ? 'Points à convenir' : 'What to agree' }}</h2>
    <ul style="color:var(--text-2);padding-left:24px;list-style:disc">
        @foreach($isFr ? ['Produits, versions et établissements couverts', 'Canaux, horaires, fuseau horaire et langues', 'Incidents, priorités et procédure d’escalade', 'Objectifs de réponse, de reprise et de disponibilité', 'Maintenance, sauvegardes et exclusions', 'Formation, mises à jour et responsabilités'] : ['Covered products, versions and facilities', 'Contact channels, service hours, time zone and languages', 'Incident definitions, priorities and escalation', 'Response, recovery and availability objectives', 'Maintenance, backups and exclusions', 'Training, updates and responsibilities'] as $item)
        <li style="margin:10px 0">{{ $item }}</li>
        @endforeach
    </ul>
    <h2 style="font-size:22px;margin:28px 0 12px">{{ $isFr ? 'Vous avez déjà un compte ?' : 'Already have an account?' }}</h2>
    <p style="color:var(--text-2)">{{ $isFr ? 'Connectez-vous pour utiliser les fonctions de support accessibles à votre compte. Pour toute question avant-vente, contactez notre équipe.' : 'Sign in to use the support functions available to your account. For pre-sales questions, contact our team.' }}</p>
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:24px"><a class="btn-primary" href="{{ route('contact') }}">{{ $isFr ? 'Contacter OPES' : 'Contact OPES' }}</a><a class="btn-secondary" href="{{ route('login') }}">{{ $isFr ? 'Se connecter' : 'Sign in' }}</a></div>
</section>
</x-layouts.app>
