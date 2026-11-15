@props(['kind', 'locale'])
@php
    $isFr = $locale === 'fr';
    $isData = $kind === 'data-license';
    $organizationTypes = [
        'hospital' => $isFr ? 'Hôpital ou hôpital universitaire' : 'Hospital or teaching hospital',
        'clinic' => $isFr ? 'Clinique ou centre de soins' : 'Clinic or care provider',
        'pharmacy' => $isFr ? 'Pharmacie ou réseau pharmaceutique' : 'Pharmacy or pharmacy network',
        'pharmaceutical' => $isFr ? 'Entreprise pharmaceutique ou fabricant' : 'Pharmaceutical company or manufacturer',
        'lab' => $isFr ? 'Laboratoire de diagnostic ou de recherche' : 'Diagnostic or research laboratory',
        'university' => $isFr ? 'Université ou institution d’enseignement' : 'University or teaching institution',
        'public-health' => $isFr ? 'Institution de santé publique' : 'Public health institution',
        'technology-company' => $isFr ? 'Entreprise de technologies de santé' : 'Health technology company',
        'government' => $isFr ? 'Institution publique' : 'Government institution',
        'ngo' => 'ONG / NGO', 'other' => $isFr ? 'Autre institution' : 'Other institution',
    ];
    $areas = [
        'medical-hardware' => $isFr ? 'Matériel médical et dispositifs connectés' : 'Medical hardware and connected devices',
        'health-software' => $isFr ? 'Logiciels de santé et flux de soins' : 'Health software and care workflows',
        'interoperability' => $isFr ? 'Interopérabilité et échange de données' : 'Interoperability and health data exchange',
        'data-quality' => $isFr ? 'Qualité des données et analyses' : 'Data quality and analytics',
        'clinical-support' => $isFr ? 'Aide à la décision clinique' : 'Clinical decision support',
        'telemedicine-triage' => $isFr ? 'Télémédecine et triage' : 'Telemedicine and triage',
        'pharmacy-medicines' => $isFr ? 'Pharmacie et disponibilité des médicaments' : 'Pharmacy and medicine availability',
        'laboratory-diagnostics' => $isFr ? 'Laboratoire et diagnostic' : 'Laboratory and diagnostics',
        'care-pathways' => $isFr ? 'Parcours de soins et utilisation des services' : 'Care pathways and service use',
        'public-health' => $isFr ? 'Santé publique' : 'Public health',
        'other' => $isFr ? 'Autre domaine' : 'Other area',
    ];
    $readiness = [
        'exploring' => $isFr ? 'Nous explorons encore la question' : 'We are exploring a research question',
        'protocol-planned' => $isFr ? 'Un protocole est en préparation' : 'A protocol is being planned',
        'pilot-sought' => $isFr ? 'Nous cherchons une évaluation ou un projet pilote' : 'We are seeking an evaluation or pilot',
        'active-study' => $isFr ? 'Une étude est déjà en cours' : 'A study is already active',
    ];
    $id = 'participation-'.str_replace('-', '', $kind);
@endphp

<style>
    .ohs-participation { flex-basis:100%; text-align:left; }
    .ohs-participation-summary { min-height:46px; display:inline-flex; align-items:center; justify-content:center; gap:9px; border:1px solid #7890AA; border-radius:9px; padding:11px 17px; background:#315F7D; color:#F1F5F9; font:inherit; font-weight:700; cursor:pointer; list-style:none; }
    .ohs-participation-summary::-webkit-details-marker { display:none; }
    .ohs-participation-summary:hover { background:#244A63; border-color:#9DB1C7; }
    .ohs-participation-summary:focus-visible,.ohs-participation input:focus-visible,.ohs-participation select:focus-visible,.ohs-participation textarea:focus-visible { outline:3px solid #A9D8FF; outline-offset:2px; }
    .ohs-participation-panel { margin-top:16px; padding:clamp(18px,4vw,30px); border:1px solid #385671; border-radius:14px; background:#102A43; }
    .ohs-participation-panel h2 { margin:0 0 8px; color:#F1F5F9; font-size:clamp(1.35rem,3vw,1.8rem); line-height:1.3; }
    .ohs-participation-intro,.ohs-participation-hint { color:#A7B8CC; line-height:1.7; }
    .ohs-participation-intro { margin:0 0 20px; }
    .ohs-participation-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
    .ohs-participation-field { min-width:0; }
    .ohs-participation-field--wide { grid-column:1/-1; }
    .ohs-participation-field label,.ohs-participation-legend { display:block; margin-bottom:6px; color:#F1F5F9; font-size:.9rem; font-weight:700; }
    .ohs-participation-field input,.ohs-participation-field select,.ohs-participation-field textarea { width:100%; min-height:44px; border:1px solid #7890AA; border-radius:8px; padding:10px 12px; background:#0B1F33; color:#F1F5F9; font:inherit; }
    .ohs-participation-field textarea { min-height:120px; resize:vertical; }
    .ohs-participation-field select option { background:#0B1F33; color:#F1F5F9; }
    .ohs-participation-field input::placeholder,.ohs-participation-field textarea::placeholder { color:#A7B8CC; opacity:1; }
    .ohs-participation-check { display:flex; align-items:flex-start; gap:10px; color:#CBD5E1; font-size:.9rem; line-height:1.6; }
    .ohs-participation-check input { flex:0 0 auto; width:18px; height:18px; margin-top:2px; accent-color:#9ACBFF; }
    .ohs-participation-hint { margin:6px 0 0; font-size:.83rem; }
    .ohs-participation-alert { margin:0 0 16px; border:1px solid #7890AA; border-radius:8px; padding:12px 14px; color:#F1F5F9; line-height:1.55; }
    .ohs-participation-alert--success { border-color:#69A78A; }
    .ohs-participation-submit { min-height:46px; border:1px solid #7890AA; border-radius:9px; padding:11px 18px; background:#315F7D; color:#F1F5F9; font:inherit; font-weight:700; cursor:pointer; }
    .ohs-participation-submit:hover { background:#244A63; }
    .ohs-participation-submit:focus-visible { outline:3px solid #A9D8FF; outline-offset:3px; }
    @media(max-width:560px) { .ohs-participation-grid { grid-template-columns:1fr; } .ohs-participation-field--wide { grid-column:auto; } .ohs-participation-summary,.ohs-participation-submit { width:100%; } }
</style>

<details class="ohs-participation" id="{{ $id }}" @if($errors->any() || session('success')) open @endif>
    <summary class="ohs-participation-summary"><i data-lucide="handshake" aria-hidden="true"></i>{{ $isFr ? 'Participer' : 'Participate' }}<i data-lucide="chevron-down" aria-hidden="true"></i></summary>
    <div class="ohs-participation-panel">
        <h2>{{ $isData ? ($isFr ? 'Proposer un partenariat de données' : 'Propose a data partnership') : ($isFr ? 'Proposer une collaboration de recherche' : 'Propose a research collaboration') }}</h2>
        <p class="ohs-participation-intro">{{ $isData ? ($isFr ? 'Présentez votre institution et la question de recherche envisagée. Cette première prise de contact ne transfère aucune donnée et ne vaut pas accord de licence.' : 'Tell us about your institution and the research question you have in mind. This initial enquiry transfers no data and does not constitute a licence agreement.') : ($isFr ? 'Présentez votre institution, la technologie ou le flux de soins à étudier et le type de collaboration recherché.' : 'Tell us about your institution, the technology or care workflow to study, and the kind of collaboration you are seeking.') }}</p>

        @if(session('success')) <p class="ohs-participation-alert ohs-participation-alert--success" role="status">{{ session('success') }}</p> @endif
        @if($errors->any())
            <div class="ohs-participation-alert" role="alert">
                <strong>{{ $isFr ? 'Vérifiez les champs du formulaire :' : 'Please review the form fields:' }}</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit', ['locale' => $locale]) }}">
            @csrf
            <input type="hidden" name="participation[program]" value="{{ $kind }}">
            <div class="ohs-participation-grid">
                <div class="ohs-participation-field"><label for="{{ $id }}-org">{{ $isFr ? 'Nom de l’institution' : 'Institution name' }} *</label><input id="{{ $id }}-org" name="participation[organization_name]" value="{{ old('participation.organization_name') }}" maxlength="150" autocomplete="organization" required></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-type">{{ $isFr ? 'Type d’institution' : 'Institution type' }} *</label><select id="{{ $id }}-type" name="participation[organization_type]" required><option value="">{{ $isFr ? 'Sélectionner' : 'Select one' }}</option>@foreach($organizationTypes as $value => $label)<option value="{{ $value }}" @selected(old('participation.organization_type') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-name">{{ $isFr ? 'Nom du contact' : 'Contact name' }} *</label><input id="{{ $id }}-name" name="name" value="{{ old('name') }}" maxlength="100" autocomplete="name" required></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-email">{{ $isFr ? 'E-mail professionnel' : 'Work email' }} *</label><input id="{{ $id }}-email" type="email" name="email" value="{{ old('email') }}" maxlength="150" autocomplete="email" required></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-phone">{{ $isFr ? 'Téléphone (facultatif)' : 'Phone (optional)' }}</label><input id="{{ $id }}-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel"></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-country">{{ $isFr ? 'Pays' : 'Country' }} *</label><input id="{{ $id }}-country" name="participation[country]" value="{{ old('participation.country') }}" maxlength="80" autocomplete="country-name" required></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-city">{{ $isFr ? 'Ville (facultatif)' : 'City (optional)' }}</label><input id="{{ $id }}-city" name="participation[city]" value="{{ old('participation.city') }}" maxlength="80" autocomplete="address-level2"></div>
                <div class="ohs-participation-field"><label for="{{ $id }}-area">{{ $isFr ? 'Domaine principal' : 'Primary area' }} *</label><select id="{{ $id }}-area" name="participation[area]" required><option value="">{{ $isFr ? 'Sélectionner un domaine' : 'Select an area' }}</option>@foreach($areas as $value => $label)<option value="{{ $value }}" @selected(old('participation.area') === $value)>{{ $label }}</option>@endforeach</select></div>

                @if($isData)
                    <div class="ohs-participation-field"><label for="{{ $id }}-authority">{{ $isFr ? 'Autorité de l’institution pour envisager cet usage' : 'Institution authority to consider this use' }} *</label><select id="{{ $id }}-authority" name="participation[data_authority]" required><option value="">{{ $isFr ? 'Sélectionner' : 'Select one' }}</option><option value="authorized" @selected(old('participation.data_authority') === 'authorized')>{{ $isFr ? 'Nous pensons pouvoir l’autoriser, sous réserve de vérification' : 'We believe we can authorise it, subject to review' }}</option><option value="confirming" @selected(old('participation.data_authority') === 'confirming')>{{ $isFr ? 'Nous vérifions actuellement' : 'We are confirming this' }}</option><option value="need-guidance" @selected(old('participation.data_authority') === 'need-guidance')>{{ $isFr ? 'Nous souhaitons en discuter' : 'We would like to discuss this' }}</option></select></div>
                    <div class="ohs-participation-field"><label for="{{ $id }}-status">{{ $isFr ? 'État actuel de préparation des données' : 'Current data preparation status' }}</label><select id="{{ $id }}-status" name="participation[data_status]"><option value="">{{ $isFr ? 'Sélectionner si connu' : 'Select if known' }}</option><option value="aggregated" @selected(old('participation.data_status') === 'aggregated')>{{ $isFr ? 'Données agrégées' : 'Aggregated data' }}</option><option value="deidentified" @selected(old('participation.data_status') === 'deidentified')>{{ $isFr ? 'Données désidentifiées' : 'De-identified data' }}</option><option value="not-assessed" @selected(old('participation.data_status') === 'not-assessed')>{{ $isFr ? 'Pas encore évalué' : 'Not yet assessed' }}</option><option value="unsure" @selected(old('participation.data_status') === 'unsure')>{{ $isFr ? 'Nous ne savons pas encore' : 'Not sure yet' }}</option></select></div>
                    <div class="ohs-participation-field ohs-participation-field--wide"><label for="{{ $id }}-period">{{ $isFr ? 'Période ou couverture potentielle (facultatif)' : 'Potential data period or coverage (optional)' }}</label><input id="{{ $id }}-period" name="participation[data_period]" value="{{ old('participation.data_period') }}" maxlength="80" placeholder="{{ $isFr ? 'Ex. années couvertes, régions ou services' : 'E.g. years covered, regions or services' }}"></div>
                @else
                    <div class="ohs-participation-field ohs-participation-field--wide"><label for="{{ $id }}-readiness">{{ $isFr ? 'Où en êtes-vous ? (facultatif)' : 'Where are you in the process? (optional)' }}</label><select id="{{ $id }}-readiness" name="participation[readiness]"><option value="">{{ $isFr ? 'Sélectionner si connu' : 'Select if known' }}</option>@foreach($readiness as $value => $label)<option value="{{ $value }}" @selected(old('participation.readiness') === $value)>{{ $label }}</option>@endforeach</select></div>
                @endif

                <div class="ohs-participation-field ohs-participation-field--wide"><label for="{{ $id }}-proposal">{{ $isData ? ($isFr ? 'Question de recherche et usage envisagé' : 'Research question and proposed use') : ($isFr ? 'Question de recherche ou collaboration souhaitée' : 'Research question or collaboration sought') }} *</label><textarea id="{{ $id }}-proposal" name="participation[proposal]" minlength="20" maxlength="700" required placeholder="{{ $isFr ? 'Décrivez le besoin, les personnes ou services concernés et le résultat que vous espérez étudier.' : 'Describe the need, the people or services involved, and the outcome you hope to study.' }}">{{ old('participation.proposal') }}</textarea><p class="ohs-participation-hint">{{ $isFr ? 'Ne transmettez aucun dossier patient, identifiant, extrait de données, échantillon ou information confidentielle.' : 'Do not send patient records, identifiers, data extracts, samples or confidential information.' }}</p></div>

                <div class="ohs-participation-field ohs-participation-field--wide"><label class="ohs-participation-check"><input type="checkbox" name="participation[contact_permission]" value="1" required @checked(old('participation.contact_permission'))><span>{{ $isFr ? 'J’autorise OPES Health Systems à utiliser ces coordonnées pour répondre à cette proposition.' : 'I authorise OPES Health Systems to use these contact details to respond to this proposal.' }}</span></label></div>
                <div class="ohs-participation-field ohs-participation-field--wide"><label class="ohs-participation-check"><input type="checkbox" name="participation[no_sensitive_data]" value="1" required @checked(old('participation.no_sensitive_data'))><span>{{ $isFr ? 'Je confirme ne pas avoir inclus de données patient, identifiants, dossiers, extraits de données ni informations de santé sensibles.' : 'I confirm that I have not included patient data, identifiers, records, data extracts or sensitive health information.' }}</span></label></div>
                <div class="ohs-participation-field ohs-participation-field--wide"><button class="ohs-participation-submit" type="submit">{{ $isFr ? 'Envoyer la proposition de participation' : 'Submit participation proposal' }}<i data-lucide="arrow-right" aria-hidden="true"></i></button></div>
            </div>
        </form>
    </div>
</details>
