@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Gestion des risques entreprise — OPES Health Systems' : 'Enterprise Risk Management — OPES Health Systems' }}"
    description="{{ $isFr ? 'Cadre ERM d\'OPES : identification, évaluation, traitement et surveillance des risques stratégiques, cliniques, cybersécurité et opérationnels.' : 'OPES ERM framework: systematic identification, assessment, treatment, and monitoring of strategic, clinical, cybersecurity, and operational risks.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="shield-alert" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Cadre ERM v1.0' : 'ERM Framework v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Gestion proactive' : 'Proactive enterprise' }}
        <span class="gradient-text">{{ $isFr ? 'des risques entreprise' : 'risk management' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Approche systématique pour identifier, évaluer, gérer, surveiller et atténuer les risques pouvant affecter les objectifs organisationnels, les opérations cliniques, la conformité, la sécurité et la durabilité de l\'activité.'
            : 'A systematic approach to identifying, assessing, managing, monitoring, and mitigating risks that could affect organizational objectives, healthcare operations, compliance, security, and business sustainability.' }}
    </p>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['7','Catégories de risques'],['4','Principes ERM'],['4','Options de traitement'],['3','Niveaux d\'escalade']]
            : [['7','Risk categories'],['4','ERM principles'],['4','Treatment options'],['3','Escalation levels']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PRINCIPLES + GOVERNANCE ──────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px">
        {{-- Principles --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="compass" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Principes ERM' : 'ERM principles' }}
            </div>
            @foreach($isFr
                ? [['user-check','#00C896','Responsabilité','Chaque risque doit avoir un propriétaire clairement désigné.'],['radar','#1A6FE8','Gestion proactive','Les risques doivent être identifiés avant que les incidents ne surviennent.'],['eye','#00C896','Transparence','Les risques doivent être visibles et reportables à tous les niveaux.'],['refresh-cw','#1A6FE8','Surveillance continue','Les risques nécessitent une revue permanente et un suivi actif.']]
                : [['user-check','#00C896','Accountability','Every risk must have a clearly designated owner.'],['radar','#1A6FE8','Proactive management','Risks should be identified before incidents occur.'],['eye','#00C896','Transparency','Risks must be visible and reportable at all levels.'],['refresh-cw','#1A6FE8','Continuous monitoring','Risks require ongoing review and active tracking.']]
            as $p)
            <div style="display:flex;gap:12px;align-items:flex-start;padding:14px;background:#0F172A;border-radius:10px;margin-bottom:8px;border-left:3px solid {{ $p[1] }}">
                <i data-lucide="{{ $p[0] }}" style="width:15px;height:15px;color:{{ $p[1] }};flex-shrink:0;margin-top:1px"></i>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:12px;margin-bottom:3px">{{ $p[2] }}</div>
                    <div style="font-size:11px;color:#64748b;line-height:1.55">{{ $p[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Governance --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="network" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Structure de gouvernance des risques' : 'Risk governance structure' }}
            </div>
            @foreach($isFr
                ? [['#00C896','Comité des risques du Conseil','Supervision stratégique des risques à long terme.'],['#1A6FE8','Comité exécutif des risques','Supervision opérationnelle et revue trimestrielle.'],['#00C896','Bureau de gestion des risques','Activités quotidiennes de gestion des risques et tenue du registre.']]
                : [['#00C896','Board Risk Committee','Strategic oversight of long-term risks.'],['#1A6FE8','Executive Risk Committee','Operational oversight and quarterly review.'],['#00C896','Risk Management Office','Daily risk management activities and register maintenance.']]
            as $gov)
            <div style="padding:16px;background:#0F172A;border:1px solid #1e293b;border-radius:10px;margin-bottom:8px;border-left:3px solid {{ $gov[0] }}">
                <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:4px">{{ $gov[1] }}</div>
                <div style="font-size:11px;color:#64748b">{{ $gov[2] }}</div>
            </div>
            @endforeach
            {{-- Escalation --}}
            <div style="margin-top:16px;background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:10px">
                    {{ $isFr ? 'Modèle d\'escalade' : 'Escalation model' }}
                </div>
                @foreach($isFr
                    ? ['Équipes opérationnelles','Management','Comité exécutif','Comité du Conseil']
                    : ['Operational teams','Management','Executive Committee','Board Committee']
                as $idx => $level)
                <div style="display:flex;align-items:center;gap:8px;padding:6px 0">
                    <div style="width:18px;height:18px;border-radius:4px;background:{{ ['#334155','#475569','#1A6FE8','#00C896'][$idx] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:9px;font-weight:800;color:{{ ['#334155','#475569','#1A6FE8','#00C896'][$idx] }}">{{ $idx+1 }}</div>
                    <span style="font-size:11px;color:#94a3b8">{{ $level }}</span>
                    @if($idx < 3)<i data-lucide="chevron-right" style="width:10px;height:10px;color:#334155;margin-left:auto"></i>@endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── 7 RISK CATEGORIES ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="alert-triangle" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Catégories de risques' : 'Risk categories' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Sept catégories de risques surveillées' : 'Seven risk categories monitored' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:28px">
        @php $riskCats = $isFr ? [
            ['trending-up','#1A6FE8','Risques stratégiques',['Concurrence','Changement réglementaire','Disruption technologique']],
            ['dollar-sign','#F59E0B','Risques financiers',['Manque à gagner','Contraintes de trésorerie','Fluctuations de devises']],
            ['heart-pulse','#EF4444','Risques cliniques',['Erreurs CDSS','Défaillances d\'alertes','Incidents patient']],
            ['settings','#00C896','Risques opérationnels',['Déploiements échoués','Contraintes ressources','Défaillances processus']],
            ['shield-x','#A855F7','Risques cybersécurité',['Ransomware','Fuites de données','Menaces internes']],
            ['share-2','#F97316','Risques d\'interopérabilité',['Échecs d\'échange','Erreurs d\'identité','Problèmes de mapping']],
            ['message-circle-warning','#94a3b8','Risques de réputation',['Interruptions de service','Insatisfaction client','Incidents publics']],
        ] : [
            ['trending-up','#1A6FE8','Strategic risks',['Market competition','Regulatory change','Technology disruption']],
            ['dollar-sign','#F59E0B','Financial risks',['Revenue shortfalls','Cash flow constraints','Currency fluctuations']],
            ['heart-pulse','#EF4444','Clinical risks',['Clinical decision errors','Alert failures','Patient safety incidents']],
            ['settings','#00C896','Operational risks',['Failed deployments','Resource constraints','Process failures']],
            ['shield-x','#A855F7','Cybersecurity risks',['Ransomware','Data breaches','Insider threats']],
            ['share-2','#F97316','Interoperability risks',['Exchange failures','Identity errors','Data mapping issues']],
            ['message-square','#94a3b8','Reputational risks',['Service outages','Customer dissatisfaction','Public incidents']],
        ]; @endphp
        @foreach($riskCats as $cat)
        <div style="background:#0F172A;border:1px solid #1e293b;border-top:3px solid {{ $cat[1] }};border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                <i data-lucide="{{ $cat[0] }}" style="width:14px;height:14px;color:{{ $cat[1] }}"></i>
                <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $cat[2] }}</div>
            </div>
            @foreach($cat[3] as $ex)
            <div style="font-size:11px;color:#64748b;padding:3px 0;border-bottom:1px solid #1e293b20">{{ $ex }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── ASSESSMENT + TREATMENT + KRIs ──────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px">
        {{-- Assessment --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">{{ $isFr ? 'Évaluation des risques' : 'Risk assessment' }}</div>
            @foreach($isFr
                ? [['#00C896','Impact','Gravité si le risque se matérialise.'],['#1A6FE8','Probabilité','Chance que le risque se produise.'],['#F59E0B','Détectabilité','Capacité à détecter le risque avant l\'impact.'],['#94a3b8','Risque résiduel','Risque restant après traitement.']]
                : [['#00C896','Impact','Severity if the risk materialises.'],['#1A6FE8','Likelihood','Probability the risk will occur.'],['#F59E0B','Detectability','Ability to detect the risk before impact.'],['#94a3b8','Residual risk','Risk remaining after treatment.']]
            as $dim)
            <div style="padding:10px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;border-left:2px solid {{ $dim[0] }}">
                <div style="font-weight:700;color:#e2e8f0;font-size:11px">{{ $dim[1] }}</div>
                <div style="font-size:10px;color:#64748b;margin-top:2px">{{ $dim[2] }}</div>
            </div>
            @endforeach
        </div>
        {{-- Treatment --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#00C896;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">{{ $isFr ? 'Options de traitement' : 'Treatment options' }}</div>
            @foreach($isFr
                ? [['ban','#EF4444','Éviter','Éliminer l\'activité génératrice de risque.'],['minimize-2','#F59E0B','Réduire','Atténuer l\'impact ou la probabilité.'],['arrow-left-right','#1A6FE8','Transférer','Transférer via assurance ou contrats.'],['check-circle','#94a3b8','Accepter','Tolérer le risque résiduel documenté.']]
                : [['ban','#EF4444','Avoid','Eliminate the risk-generating activity.'],['minimize-2','#F59E0B','Reduce','Mitigate impact or likelihood.'],['arrow-left-right','#1A6FE8','Transfer','Transfer via insurance or contracts.'],['check-circle','#94a3b8','Accept','Tolerate the documented residual risk.']]
            as $tr)
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px">
                <i data-lucide="{{ $tr[0] }}" style="width:13px;height:13px;color:{{ $tr[1] }};flex-shrink:0;margin-top:1px"></i>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:11px">{{ $tr[2] }}</div>
                    <div style="font-size:10px;color:#64748b;margin-top:2px">{{ $tr[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- KRIs --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#A855F7;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">{{ $isFr ? 'Indicateurs clés de risque (KRI)' : 'Key risk indicators (KRIs)' }}</div>
            @foreach($isFr
                ? ['Disponibilité des systèmes','Volume d\'incidents de sécurité','Attrition client','Taux d\'incidents cliniques','Taux d\'échec des projets']
                : ['System availability','Security incident volume','Customer churn','Clinical incident rate','Project failure rate']
            as $kri)
            <div style="display:flex;align-items:center;gap:6px;padding:8px 10px;background:#0F172A;border-radius:8px;margin-bottom:6px;font-size:11px;color:#94a3b8">
                <i data-lucide="activity" style="width:11px;height:11px;color:#A855F7;flex-shrink:0"></i>{{ $kri }}
            </div>
            @endforeach
            <div style="margin-top:12px;background:#0f152e;border:1px solid rgba(168,85,247,0.15);border-radius:10px;padding:12px 14px">
                <div style="font-size:10px;font-weight:700;color:#A855F7;margin-bottom:6px">{{ $isFr ? 'Intégrations ERM' : 'ERM integrations' }}</div>
                @foreach($isFr
                    ? ['Cadre cybersécurité','Reprise après sinistre','Gouvernance clinique','Management de la qualité (QMS)']
                    : ['Cybersecurity framework','Disaster recovery','Clinical governance','Quality management (QMS)']
                as $int)
                <div style="font-size:10px;color:#64748b;padding:3px 0">{{ $int }}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Questions sur notre gestion des risques ?' : 'Questions about our risk management?' }}</h2>
    <p>{{ $isFr
        ? 'Contactez notre équipe pour en savoir plus sur nos contrôles de risques, notre posture de sécurité et notre cadre de résilience.'
        : 'Contact our team to learn more about our risk controls, security posture, and resilience framework.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Nous contacter' : 'Contact us' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/compliance') }}" class="btn-secondary">
            {{ $isFr ? 'Conformité & sécurité' : 'Compliance & trust' }}
            <i data-lucide="shield-check" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>
