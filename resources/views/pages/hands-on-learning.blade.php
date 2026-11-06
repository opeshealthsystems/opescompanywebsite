@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    :title="$isFr ? 'Projet Hands-On Learning — Apprentissage sur des systèmes professionnels | OPES' : 'Project Hands-On Learning — Learn on Professional Systems | OPES'"
    :description="$isFr ? 'OPES Hands-On Learning invite les universités, écoles professionnelles et établissements de santé à former les apprenants sur des parcours numériques professionnels, avec accès institutionnel gratuit proposé et certifications professionnelles facultatives.' : 'OPES Hands-On Learning invites universities, professional schools and health institutions to prepare learners on professional digital workflows, with proposed free institutional access and optional professional certifications.'">

<style>
.hol-hero{background:var(--surface);border-bottom:1px solid #1e293b;padding:clamp(48px,8vw,92px) 24px 60px;text-align:center}
.hol-hero h1{max-width:900px;margin:0 auto 18px;color:#f1f5f9;font-size:clamp(2rem,5vw,3.35rem);line-height:1.12;font-weight:800;letter-spacing:-.03em}
.hol-hero p{max-width:790px;margin:0 auto;color:var(--text-muted);font-size:clamp(1rem,2vw,1.12rem);line-height:1.75}
.hol-actions{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:26px}
.hol-status{max-width:900px;margin:20px auto 0;border:1px solid #607d94;background:#0b1d31;border-radius:12px;padding:14px 18px;color:#d9e5ee;font-size:.9rem;line-height:1.65}
.hol-section{max-width:1120px;margin:0 auto;padding:clamp(36px,6vw,68px) 24px;scroll-margin-top:90px}
.hol-section h2{color:#f1f5f9;font-size:clamp(1.45rem,3vw,2rem);line-height:1.25;margin:0 0 12px}
.hol-lead{max-width:820px;margin:0 0 26px;color:var(--text-muted);line-height:1.75}
.hol-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,235px),1fr));gap:15px}
.hol-card{height:100%;padding:21px;border-radius:12px;border:1px solid #29445e;background:#0b1d31}
.hol-card h3{margin:10px 0 7px;color:#eaf2f8;font-size:1rem;line-height:1.45}
.hol-card p,.hol-card li{color:var(--text-muted);font-size:.9rem;line-height:1.68}
.hol-icon{width:40px;height:40px;display:grid;place-items:center;border-radius:10px;background:#173651;color:#acd5ec}
.hol-steps{counter-reset:step;display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,180px),1fr));gap:12px}
.hol-step{padding:19px;border:1px solid #29445e;background:#0b1d31;border-radius:12px}
.hol-step:before{counter-increment:step;content:counter(step,decimal-leading-zero);display:block;margin-bottom:10px;color:#acd5ec;font-size:.78rem;font-weight:800;letter-spacing:.12em}
.hol-step h3{margin:0 0 7px;color:#eaf2f8;font-size:.94rem}
.hol-step p{margin:0;color:var(--text-muted);font-size:.88rem;line-height:1.6}
.hol-table-wrap{overflow-x:auto;border:1px solid #29445e;border-radius:12px}
.hol-table{width:100%;min-width:660px;border-collapse:collapse}
.hol-table th,.hol-table td{padding:13px 15px;text-align:left;vertical-align:top;border-bottom:1px solid #29445e;color:#cbd5e1;font-size:.88rem;line-height:1.6}
.hol-table th{background:#102943;color:#eaf2f8;font-size:.8rem;letter-spacing:.03em}
.hol-table tr:last-child td{border-bottom:0}
.hol-note{margin-top:16px;border:1px solid #29445e;border-radius:10px;padding:15px 17px;color:#cbd5e1;font-size:.9rem;line-height:1.7}
.hol-faq{border-top:1px solid #29445e}.hol-faq details{padding:16px 0;border-bottom:1px solid #29445e}.hol-faq summary{color:#eaf2f8;font-weight:700;cursor:pointer;line-height:1.5}.hol-faq p{max-width:850px;color:var(--text-muted);line-height:1.7}
@media(max-width:720px){.hol-hero{padding-left:18px;padding-right:18px}.hol-section{padding-left:18px;padding-right:18px}}
</style>

<header class="hol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px"><i data-lucide="book-open-check" style="width:14px;height:14px"></i>{{ $isFr ? 'Formation professionnelle · Partenariats institutionnels' : 'Professional learning · Institutional partnerships' }}</div>
    <h1>{{ $isFr ? 'Apprendre les systèmes professionnels en pratiquant leurs vrais parcours de travail.' : 'Learn professional systems by practising the workflows people use at work.' }}</h1>
    <p>{{ $isFr ? 'Depuis le Cameroun, OPES Health Systems invite les établissements de santé, universités, écoles professionnelles et partenaires de formation en Afrique et dans le monde à préparer les apprenants au travail numérique réel.' : 'From Cameroon, OPES Health Systems invites health institutions, universities, professional schools and training partners across Africa and worldwide to prepare learners for real digital work.' }}</p>
    <div class="hol-actions">
        <a class="btn-primary" href="{{ url($locale.'/become-a-partner?partner_type=academic') }}">{{ $isFr ? 'Proposer un partenariat' : 'Propose an institutional partnership' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
        <a class="btn-secondary" href="{{ url($locale.'/medical-informatics') }}">{{ $isFr ? 'Découvrir le parcours d’informatique médicale' : 'Explore the Medical Informatics pathway' }} <i data-lucide="graduation-cap" style="width:15px;height:15px"></i></a>
    </div>
    <p class="hol-status">{{ $isFr ? 'Statut : cadre de programme proposé, en préparation avec les partenaires. Les systèmes, cohortes, calendrier, évaluations, frais éventuels et co-émissions sont confirmés par accord avant le lancement de chaque parcours.' : 'Status: a proposed programme framework being prepared with partners. Systems, cohorts, timing, assessments, any fees and certificate co-issuance are agreed before each pathway launches.' }}</p>
</header>

<section class="hol-section" id="programme">
    <div class="section-label">{{ $isFr ? 'Le programme' : 'The programme' }}</div>
    <h2>{{ $isFr ? 'Relier les cours aux tâches que les équipes réalisent réellement' : 'Connect coursework to the tasks teams actually perform' }}</h2>
    <p class="hol-lead">{{ $isFr ? 'Le projet Hands-On Learning propose aux établissements partenaires un accès pédagogique gratuit aux plateformes OPES, des exercices guidés autour des parcours professionnels et une préparation facultative à la certification. Les activités s’intègrent aux cours existants et ne remplacent pas les diplômes de l’établissement.' : 'Project Hands-On Learning proposes free educational access to OPES platforms for partner institutions, guided exercises based on professional workflows and optional certification. Activities fit into existing courses and do not replace an institution’s academic qualifications.' }}</p>
    <div class="hol-grid">
        @foreach($isFr ? [
            ['building-2','Accès institutionnel','Un accès pédagogique aux plateformes OPES est proposé gratuitement aux établissements et cohortes partenaires, selon un périmètre convenu.'],
            ['workflow','Apprentissage pratique','Les apprenants pratiquent les tâches professionnelles et les échanges entre rôles sur des parcours guidés.'],
            ['badge-check','Certification facultative','Les candidats qui souhaitent attester leurs compétences peuvent suivre une évaluation professionnelle après validation du parcours.'],
            ['presentation','Formation des formateurs','Les enseignants et tuteurs peuvent être accompagnés pour intégrer les systèmes aux cours et guider les exercices.'],
        ] : [
            ['building-2','Institutional access','Free educational access to OPES platforms is proposed for partner institutions and cohorts within an agreed scope.'],
            ['workflow','Practical learning','Learners practise professional tasks and handoffs between roles through guided workflows.'],
            ['badge-check','Optional certification','Candidates who want to document their skills may take a professional assessment after the pathway is approved.'],
            ['presentation','Train-the-trainer','Faculty and tutors can be prepared to integrate the systems into courses and guide practical exercises.'],
        ] as $item)
        <article class="hol-card"><div class="hol-icon"><i data-lucide="{{ $item[0] }}" style="width:20px;height:20px"></i></div><h3>{{ $item[1] }}</h3><p>{{ $item[2] }}</p></article>
        @endforeach
    </div>
</section>

<section class="hol-section" id="learning-areas">
    <div class="section-label">{{ $isFr ? 'Domaines proposés' : 'Proposed learning areas' }}</div>
    <h2>{{ $isFr ? 'Un projet multi-systèmes, avec le parcours santé comme priorité' : 'A multi-system programme, with health as the priority pathway' }}</h2>
    <p class="hol-lead">{{ $isFr ? 'Le projet général décrit plusieurs domaines d’apprentissage. Le parcours santé et informatique médicale est détaillé en priorité ; les autres parcours sont distincts et seront définis avec les établissements concernés.' : 'The wider project outlines several learning domains. Health and Medical Informatics is the priority pathway; other pathways are separate and will be scoped with participating institutions.' }}</p>
    <div class="hol-table-wrap"><table class="hol-table">
        <thead><tr><th>{{ $isFr ? 'Domaine' : 'Domain' }}</th><th>{{ $isFr ? 'Exemples de parcours' : 'Example workflows' }}</th><th>{{ $isFr ? 'Publics concernés' : 'Potential learners' }}</th></tr></thead>
        <tbody>
            @foreach($isFr ? [
                ['Santé et informatique médicale','Dossier patient, triage, laboratoire, pharmacie, identité santé, interopérabilité et données.','Étudiants et professionnels de santé, personnel informatique, enseignants et responsables.'],
                ['Finance et comptabilité','Opérations bancaires, tenue de comptes et processus financiers.','Étudiants en finance, comptabilité et administration.'],
                ['Commerce et systèmes d’entreprise','Vente, gestion de stock, opérations d’entreprise et chaîne logistique.','Étudiants en commerce, gestion, approvisionnement et logistique.'],
                ['Éducation','Administration et gestion de systèmes scolaires.','Étudiants et professionnels de l’administration éducative.'],
            ] : [
                ['Health and Medical Informatics','Patient records, triage, laboratory, pharmacy, health identity, interoperability and data.','Health students and professionals, IT teams, educators and managers.'],
                ['Finance and accounting','Banking operations, bookkeeping and finance workflows.','Finance, accounting and administration learners.'],
                ['Commerce and enterprise systems','Sales, stock management, business operations and supply chains.','Commerce, management, procurement and logistics learners.'],
                ['Education','School administration and education-system workflows.','Education administration learners and professionals.'],
            ] as $row)
            <tr><td><strong>{{ $row[0] }}</strong></td><td>{{ $row[1] }}</td><td>{{ $row[2] }}</td></tr>
            @endforeach
        </tbody>
    </table></div>
    <p class="hol-note">{{ $isFr ? 'Les domaines et noms de systèmes ci-dessus proviennent du document de programme proposé. Les plateformes incluses, leur disponibilité, les comptes et les données de formation sont confirmés par cohorte. Pour le programme santé détaillé, consultez la page Informatique médicale.' : 'The domains and system areas above come from the proposed programme document. Included platforms, availability, accounts and training data are confirmed for each cohort. See the Medical Informatics page for the detailed health pathway.' }} <a href="{{ url($locale.'/medical-informatics') }}" style="color:#acd5ec">{{ $isFr ? 'Voir le parcours santé' : 'View the health pathway' }} →</a></p>
</section>

<section class="hol-section" id="partnership-process">
    <div class="section-label">{{ $isFr ? 'Fonctionnement du partenariat' : 'How the partnership works' }}</div>
    <h2>{{ $isFr ? 'Un cadre convenu avec chaque établissement' : 'A programme agreed with each institution' }}</h2>
    <div class="hol-steps">
        @foreach($isFr ? [
            ['Définir le parcours','Choisir les programmes, cohortes, objectifs, systèmes et règles de gouvernance.'],
            ['Organiser l’accès','Confirmer les plateformes, les comptes, les rôles, les appareils et la connectivité.'],
            ['Préparer les enseignants','Désigner les coordinateurs et préparer les enseignants aux activités pratiques.'],
            ['Intégrer la pratique','Ajouter les exercices et cas aux cours existants, avec des données pédagogiques adaptées.'],
            ['Évaluer et améliorer','Revoir les résultats, commentaires et besoins avec les partenaires.'],
        ] : [
            ['Agree the pathway','Select programmes, cohorts, learning outcomes, systems and governance.'],
            ['Set up access','Confirm platforms, accounts, roles, devices and connectivity.'],
            ['Prepare faculty','Nominate coordinators and prepare educators for the practical activities.'],
            ['Embed practice','Add exercises and cases to existing courses using suitable learning data.'],
            ['Review and improve','Review outcomes, feedback and needs with programme partners.'],
        ] as $step)
        <article class="hol-step"><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p></article>
        @endforeach
    </div>
    <div class="hol-grid" style="margin-top:18px">
        <article class="hol-card"><h3>{{ $isFr ? 'Contribution proposée d’OPES' : 'Proposed OPES contribution' }}</h3><p>{{ $isFr ? 'Plateformes, configuration et déploiement convenus, parcours pratiques, examens professionnels approuvés et appui à la formation des formateurs.' : 'Agreed platforms, setup and deployment, practical workflows, approved professional assessments and train-the-trainer support.' }}</p></article>
        <article class="hol-card"><h3>{{ $isFr ? 'Contribution de l’établissement' : 'Institution contribution' }}</h3><p>{{ $isFr ? 'Coordinateur, enseignants, cohorte, équipements ou appareils, connectivité et intégration pédagogique. Le périmètre exact dépend de l’accord.' : 'A coordinator, educators, learner cohort, equipment or devices, connectivity and course integration. The exact scope is set by agreement.' }}</p></article>
        <article class="hol-card"><h3>{{ $isFr ? 'Rôle des partenaires de santé' : 'Health partner role' }}</h3><p>{{ $isFr ? 'Les établissements cliniques peuvent contribuer à la revue, à l’audit et à l’encadrement du parcours santé. Toute co-émission ou part de revenus exige un accord écrit.' : 'Clinical institutions may contribute review, audit and mentoring to the health pathway. Any co-issuance or revenue share requires a written agreement.' }}</p></article>
    </div>
    <p class="hol-note">{{ $isFr ? 'Les besoins indicatifs du document source — notamment coordinateur, enseignants, cohorte, équipements et connexion — ne constituent pas encore des critères contractuels. Les conditions finales sont établies avec chaque établissement.' : 'Readiness items in the source brief—including a coordinator, faculty, cohort, equipment and connectivity—are indicative, not final contractual requirements. Final terms are agreed with each institution.' }}</p>
</section>

<section class="hol-section" id="credentials">
    <div class="section-label">{{ $isFr ? 'Compétences et reconnaissance' : 'Skills and recognition' }}</div>
    <h2>{{ $isFr ? 'Des compétences professionnelles complémentaires aux diplômes' : 'Professional skills that complement academic qualifications' }}</h2>
    <div class="hol-grid">
        <article class="hol-card"><h3>{{ $isFr ? 'Parcours d’apprentissage gratuit' : 'Free learning pathway' }}</h3><p>{{ $isFr ? 'L’accès pédagogique proposé aux apprenants des institutions partenaires est gratuit dans le cadre convenu. Une évaluation certifiante reste facultative.' : 'Proposed educational access for learners at partner institutions is free within the agreed programme. A certification assessment remains optional.' }}</p></article>
        <article class="hol-card"><h3>{{ $isFr ? 'Certifications professionnelles proposées' : 'Proposed professional certifications' }}</h3><p>{{ $isFr ? 'Les certifications sont des attestations professionnelles envisagées par le programme. Elles ne sont ni des diplômes universitaires, ni une accréditation académique, ni une autorisation d’exercer.' : 'The programme proposes professional credentials. They are not university degrees, academic accreditation or a licence to practise.' }}</p></article>
        <article class="hol-card"><h3>{{ $isFr ? 'Évaluation et vérification' : 'Assessment and verification' }}</h3><p>{{ $isFr ? 'Les tâches pratiques, examens, critères de réussite, organismes émetteurs et mécanismes de vérification sont arrêtés après revue et accord. Un QR code est envisagé sous réserve de validation et de disponibilité.' : 'Practical tasks, exams, pass criteria, issuing bodies and verification are finalized after review and agreement. QR verification is proposed subject to approval and availability.' }}</p></article>
    </div>
    <p class="hol-note">{{ $isFr ? 'Le document de programme propose des frais facultatifs de 25 000 à 75 000 FCFA selon le niveau, mais ces montants ne sont pas des tarifs définitifs. Ils doivent être confirmés avec les partenaires avant toute inscription payante.' : 'The programme document proposes optional fees of FCFA 25,000–75,000 by level, but these are not final prices. Partners must confirm them before any paid certification enrolment.' }}</p>
</section>

<section class="hol-section" id="faq">
    <div class="section-label">{{ $isFr ? 'Questions fréquentes' : 'Frequently asked' }}</div>
    <h2>{{ $isFr ? 'À propos du programme' : 'About the programme' }}</h2>
    <div class="hol-faq">
        <details><summary>{{ $isFr ? 'Les apprenants utilisent-ils des données réelles de patients ?' : 'Will learners use real patient data?' }}</summary><p>{{ $isFr ? 'Les exercices s’appuient sur des données de formation ou des données dûment autorisées et gouvernées. Les données identifiables ne sont pas utilisées sans base légale, autorisations et protections appropriées.' : 'Exercises use training data or data covered by appropriate authorization and governance. Identifiable records are not used without a lawful basis, approvals and safeguards.' }}</p></details>
        <details><summary>{{ $isFr ? 'Le certificat remplace-t-il un diplôme ou une autorisation professionnelle ?' : 'Does a certificate replace a degree or professional licence?' }}</summary><p>{{ $isFr ? 'Non. Toute certification du programme est une attestation professionnelle complémentaire. Elle ne remplace pas un diplôme délivré par l’établissement, une accréditation ni une autorisation d’exercer.' : 'No. Any programme certificate is an additional professional credential. It does not replace an institution-issued degree, accreditation or licence to practise.' }}</p></details>
        <details><summary>{{ $isFr ? 'Quand les cohortes commenceront-elles ?' : 'When do cohorts begin?' }}</summary><p>{{ $isFr ? 'Le calendrier dépend de la revue des partenaires, de l’approbation du parcours et des accords d’accès. Le début des cohortes sera annoncé après confirmation.' : 'Timing depends on partner review, pathway approval and access agreements. Cohort dates will be shared after they are confirmed.' }}</p></details>
    </div>
</section>

<div class="demo-section">
    <h2>{{ $isFr ? 'Préparons les apprenants au travail numérique réel' : 'Prepare learners for real digital work' }}</h2>
    <p>{{ $isFr ? 'Universités, établissements de santé, écoles professionnelles et partenaires de formation : échangeons sur votre programme et les systèmes adaptés à vos apprenants.' : 'Universities, health institutions, professional schools and training partners: let’s discuss your programme and the systems that fit your learners.' }}</p>
    <a href="{{ url($locale.'/become-a-partner?partner_type=academic') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">{{ $isFr ? 'Contacter OPES pour un partenariat' : 'Contact OPES about a partnership' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
</div>

</x-layouts.app>
