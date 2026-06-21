@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Programme partenaires — OPES Health Systems' : 'Partner Program — OPES Health Systems' }}"
    description="{{ $isFr ? 'Programme de partenariat OPES : revendeurs, implémenteurs, partenaires technologiques, académiques et gouvernementaux. Niveaux Silver, Gold, Platinum, Strategic.' : 'OPES partner program: resellers, implementers, technology, academic, and government partners. Silver, Gold, Platinum, and Strategic levels.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="handshake" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Programme Partenaires v1.0' : 'Partner Program v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Un écosystème de partenaires' : 'A structured partner' }}
        <span class="gradient-text">{{ $isFr ? 'structuré & certifié' : 'ecosystem' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Le programme partenaire OPES établit un écosystème structuré d\'implémenteurs, revendeurs, partenaires technologiques, institutions académiques et alliances stratégiques — pour accélérer l\'adoption, améliorer la capacité de service et étendre la portée régionale.'
            : 'The OPES Partner Program creates a structured ecosystem of implementation partners, resellers, technology partners, academic institutions, and strategic alliances — to accelerate adoption, improve service capacity, and expand regional reach.' }}
    </p>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['5','Catégories de partenaires'],['4','Niveaux de partenariat'],['Silver → Strategic','Niveaux certifiés'],['Board Approval','Partenaires stratégiques']]
            : [['5','Partner categories'],['4','Partnership levels'],['Silver → Strategic','Certified tiers'],['Board Approval','Strategic partners']]
        as $s)
        <div class="stat-item">
            <div class="stat-value" style="font-size:clamp(14px,2vw,22px)">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PARTNER CATEGORIES ───────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="grid" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Catégories de partenaires' : 'Partner categories' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq types de partenariats' : 'Five partnership types' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @php $cats = $isFr ? [
            ['tag','#00C896','Revendeur autorisé','Génération de leads et activités commerciales.',['Génération de leads','Activités de vente'],['Commission commerciale','Support marketing']],
            ['map','#1A6FE8','Partenaire implémenteur','Déploiement, configuration et formation des équipes client.',['Déploiement','Configuration','Formation'],['Implémenteurs certifiés requis']],
            ['code','#00C896','Partenaire technologique','Intégrations, APIs et infrastructure.',['Intégrations','APIs','Infrastructure'],['Fournisseurs cloud','Paiement','Identité']],
            ['graduation-cap','#1A6FE8','Partenaire académique','Développement des compétences et recherche.',['Universités','Écoles de soins infirmiers','Écoles de médecine'],['Développement des effectifs','Recherche']],
            ['building-2','#00C896','Partenaire gouvernemental stratégique','Ministères, programmes d\'assurance et institutions de santé publique.',['Ministères','Programmes d\'assurance nationale','Institutions de santé publique'],[]],
        ] : [
            ['tag','#00C896','Authorized Reseller','Lead generation and sales activities on behalf of OPES.',['Lead generation','Sales activities'],['Sales commission','Marketing support']],
            ['map','#1A6FE8','Implementation Partner','Deployment, configuration, and training for customer sites.',['Deployment','Configuration','Training'],['Certified implementers required']],
            ['code','#00C896','Technology Partner','Integrations, APIs, and infrastructure partnerships.',['Integrations','APIs','Infrastructure'],['Cloud providers','Payment providers','Identity providers']],
            ['graduation-cap','#1A6FE8','Academic Partner','Workforce development and research.',['Universities','Nursing schools','Medical schools'],['Workforce development','Research collaboration']],
            ['building-2','#00C896','Strategic Government Partner','Ministries, national insurance programs, and public health institutions.',['Ministries','National insurance programs','Public health institutions'],[]],
        ]; @endphp
        @foreach($cats as $cat)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px">
            <div style="width:38px;height:38px;border-radius:10px;background:{{ $cat[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $cat[0] }}" style="width:17px;height:17px;color:{{ $cat[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:4px">{{ $cat[2] }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:12px">{{ $cat[3] }}</div>
            @foreach($cat[4] as $fn)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);padding:3px 0">
                <i data-lucide="chevron-right" style="width:10px;height:10px;color:{{ $cat[1] }};flex-shrink:0"></i>{{ $fn }}
            </div>
            @endforeach
            @if(count($cat[5]))
            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #1e293b">
                @foreach($cat[5] as $ben)
                <div style="display:flex;align-items:center;gap:6px;font-size:10px;color:#00C896;padding:2px 0">
                    <i data-lucide="check" style="width:9px;height:9px;flex-shrink:0"></i>{{ $ben }}
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PARTNER LEVELS ───────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="award" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Niveaux de partenariat' : 'Partner levels' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Quatre niveaux de certification' : 'Four certification levels' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:28px">
        @foreach($isFr
            ? [['var(--text-muted)','Silver','Niveau d\'entrée','2 professionnels certifiés','entry'],['#F59E0B','Gold','Intermédiaire','5 professionnels certifiés','intermediate'],['#1A6FE8','Platinum','Avancé','10 professionnels certifiés','advanced'],['#00C896','Strategic','Partenaire national','Approbation du Conseil','top']]
            : [['var(--text-muted)','Silver','Entry level','2 certified professionals','entry'],['#F59E0B','Gold','Intermediate','5 certified professionals','intermediate'],['#1A6FE8','Platinum','Advanced','10 certified professionals','advanced'],['#00C896','Strategic','National-level','Board approval required','top']]
        as $level)
        <div style="background:#0F172A;border:2px solid {{ $level[0] }}40;border-radius:14px;padding:20px;text-align:center;{{ $level[4]==='top' ? 'border-color:'.$level[0].';background:#0d1f19;' : '' }}">
            <div style="width:48px;height:48px;border-radius:50%;background:{{ $level[0] }}20;border:2px solid {{ $level[0] }};display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i data-lucide="award" style="width:20px;height:20px;color:{{ $level[0] }}"></i>
            </div>
            <div style="font-weight:800;color:{{ $level[0] }};font-size:15px;margin-bottom:4px">{{ $level[1] }}</div>
            <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px">{{ $level[2] }}</div>
            <div style="background:{{ $level[0] }}15;border-radius:8px;padding:8px;font-size:11px;color:#e2e8f0;font-weight:600">{{ $level[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── BENEFITS + KPIs ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px">
        {{-- Benefits --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="gift" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Avantages partenaires' : 'Partner benefits' }}
            </div>
            <div style="display:flex;flex-direction:column;gap:8px">
                @foreach($isFr
                    ? [['badge-check','#00C896','Certification','Accès aux programmes de certification OPES.'],['headphones','#1A6FE8','Support technique','Support dédié aux partenaires certifiés.'],['trending-up','#00C896','Enablement commercial','Outils de vente, ressources et formation commerciale.'],['megaphone','#1A6FE8','Marketing conjoint','Cobranding et activités marketing communes.'],['cpu','#00C896','Accès produit anticipé','Accès prioritaire aux nouvelles versions et fonctionnalités.']]
                    : [['badge-check','#00C896','Certification','Access to OPES certification programmes.'],['headphones','#1A6FE8','Technical support','Dedicated support for certified partners.'],['trending-up','#00C896','Sales enablement','Sales tools, resources, and commercial training.'],['megaphone','#1A6FE8','Joint marketing','Co-branding and joint marketing activities.'],['cpu','#00C896','Early product access','Priority access to new releases and features.']]
                as $b)
                <div style="display:flex;align-items:center;gap:10px;padding:12px;background:#0F172A;border-radius:8px;border-left:2px solid {{ $b[1] }}">
                    <i data-lucide="{{ $b[0] }}" style="width:14px;height:14px;color:{{ $b[1] }};flex-shrink:0"></i>
                    <div>
                        <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $b[2] }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $b[3] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- KPIs + Governance --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'KPIs & gouvernance partenaires' : 'Partner KPIs & governance' }}
            </div>
            <div style="background:#0f1a2e;border:1px solid rgba(0,200,150,0.15);border-radius:12px;padding:18px;margin-bottom:16px">
                <div style="font-size:11px;font-weight:700;color:#00C896;margin-bottom:10px">{{ $isFr ? 'Indicateurs de performance' : 'Performance indicators' }}</div>
                @foreach($isFr
                    ? ['Revenus générés','Satisfaction client','Taux de succès des déploiements','Taux de complétion de la formation']
                    : ['Revenue generated','Customer satisfaction','Deployment success rate','Training completion rate']
                as $kpi)
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);padding:5px 0;border-bottom:1px solid #1e293b30">
                    <i data-lucide="chevron-right" style="width:10px;height:10px;color:#00C896;flex-shrink:0"></i>{{ $kpi }}
                </div>
                @endforeach
            </div>
            <div style="background:#0f152e;border:1px solid rgba(26,111,232,0.15);border-radius:12px;padding:18px">
                <div style="font-size:11px;font-weight:700;color:#1A6FE8;margin-bottom:10px">{{ $isFr ? 'Évaluation des partenaires' : 'Partner evaluation' }}</div>
                @foreach($isFr
                    ? ['Revues de performance','Audits qualité','Revues de certification','Revues de conformité']
                    : ['Performance reviews','Quality audits','Certification reviews','Compliance reviews']
                as $ev)
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);padding:5px 0;border-bottom:1px solid #1e293b30">
                    <i data-lucide="chevron-right" style="width:10px;height:10px;color:#1A6FE8;flex-shrink:0"></i>{{ $ev }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Devenir partenaire OPES ?' : 'Become an OPES partner?' }}</h2>
    <p>{{ $isFr
        ? 'Rejoignez l\'écosystème OPES et contribuez à la transformation numérique de la santé en Afrique centrale et au-delà.'
        : 'Join the OPES ecosystem and contribute to the digital transformation of healthcare across Central Africa and beyond.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Rejoindre le programme' : 'Join the programme' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/partnerships') }}" class="btn-secondary">
            {{ $isFr ? 'Voir nos partenaires' : 'View our partners' }}
            <i data-lucide="handshake" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>
