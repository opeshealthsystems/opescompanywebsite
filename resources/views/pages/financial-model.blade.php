@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Modèle financier & stratégie de revenus — OPES Health Systems' : 'Financial Model & Revenue Strategy — OPES Health Systems' }}"
    description="{{ $isFr ? 'Architecture de revenus OPES : six piliers (licences, maintenance, implémentation, formation, interopérabilité, programmes gouvernementaux) avec cibles 2026–2031.' : 'OPES revenue architecture: six pillars (licensing, maintenance, implementation, training, interoperability, government) with 2026–2031 targets.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="dollar-sign" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Stratégie financière v1.0' : 'Revenue Strategy v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Revenus récurrents' : 'Predictable recurring' }}
        <span class="gradient-text">{{ $isFr ? 'prévisibles & diversifiés' : 'revenue at scale' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Construire une entreprise d\'infrastructure de santé numérique financièrement durable avec des flux de revenus diversifiés et des revenus récurrents solides — tout en soutenant la transformation sanitaire à travers l\'Afrique.'
            : 'Build a financially sustainable digital health infrastructure company with diversified revenue streams and strong recurring revenue — while supporting healthcare transformation across Africa.' }}
    </p>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['6','Piliers de revenus'],['5','Phases d\'évolution'],['20–30%','Cible programmes gouvernementaux'],['7','KPIs financiers']]
            : [['6','Revenue pillars'],['5','Evolution phases'],['20–30%','Government programme target'],['7','Financial KPIs']]
        as $s)
        <div class="stat-item">
            <div class="stat-value" style="font-size:clamp(14px,2vw,22px)">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 6 REVENUE PILLARS ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Piliers de revenus' : 'Revenue pillars' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six sources de revenus diversifiées' : 'Six diversified revenue streams' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @php $pillars = $isFr ? [
            ['code','#00C896','Licences logicielles','Licences perpétuelles et par abonnement pour toute la gamme OPES.',['OPES Clinic','OPES Hospital','OPES Specialty Suite','OPES Care','OPES Clinical Intelligence'],null],
            ['wrench','#1A6FE8','Maintenance annuelle','Mises à jour, support et maintenance de sécurité.',['Mises à jour','Support','Maintenance sécurité'],'20–25% des revenus annuels'],
            ['map','#00C896','Services d\'implémentation','Déploiement, configuration et support go-live.',['Déploiement','Configuration','Support go-live'],'15–20% des revenus annuels'],
            ['graduation-cap','#1A6FE8','Formation & certification','Formations et certifications dispensées via OPES Academy.',['OPES Academy'],'5–10% des revenus annuels'],
            ['share-2','#00C896','Services d\'interopérabilité','Accès API, services HIE et Health ID.',['Accès API','Services HIE','Services Health ID'],'15–20% des revenus annuels'],
            ['building-2','#1A6FE8','Programmes gouvernementaux','Plateformes nationales, registres et infrastructure UHC.',['Plateformes nationales','Registres','Infrastructure UHC'],'20–30% des revenus annuels'],
        ] : [
            ['code','#00C896','Software licensing','Perpetual and subscription licences across the full OPES product range.',['OPES Clinic','OPES Hospital','OPES Specialty Suite','OPES Care','OPES Clinical Intelligence'],null],
            ['wrench','#1A6FE8','Annual maintenance','Updates, support, and security maintenance.',['Updates','Support','Security maintenance'],'20–25% of annual revenue'],
            ['map','#00C896','Implementation services','Deployment, configuration, and go-live support.',['Deployment','Configuration','Go-live support'],'15–20% of annual revenue'],
            ['graduation-cap','#1A6FE8','Training & certification','Training and certifications delivered via OPES Academy.',['OPES Academy'],'5–10% of annual revenue'],
            ['share-2','#00C896','Interoperability services','API access, HIE services, and Health ID services.',['API access','HIE services','Health ID services'],'15–20% of annual revenue'],
            ['building-2','#1A6FE8','Government programmes','National platforms, registries, and UHC infrastructure.',['National platforms','Registries','UHC infrastructure'],'20–30% of annual revenue'],
        ]; @endphp
        @foreach($pillars as $p)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $p[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $p[0] }}" style="width:16px;height:16px;color:{{ $p[1] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $p[2] }}</div>
            </div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);margin-bottom:10px;line-height:1.55">{{ $p[3] }}</div>
            @foreach($p[4] as $item)
            <div style="display:flex;align-items:center;gap:6px;font-size:var(--fs-xs);color:var(--text-muted);padding:3px 0">
                <i data-lucide="chevron-right" style="width:9px;height:9px;color:{{ $p[1] }};flex-shrink:0"></i>{{ $item }}
            </div>
            @endforeach
            @if($p[5])
            <div style="margin-top:12px;background:{{ $p[1] }}10;border:1px solid {{ $p[1] }}20;border-radius:8px;padding:6px 10px;font-size:var(--fs-2xs);font-weight:700;color:{{ $p[1] }};text-align:center">
                {{ $isFr ? 'Cible : ' : 'Target: ' }}{{ $p[5] }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── REVENUE EVOLUTION + KPIs ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Revenue evolution --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="trending-up" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Évolution du modèle de revenus' : 'Revenue model evolution' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:20px">{{ $isFr ? 'Vers une infrastructure récurrente' : 'Toward recurring infrastructure' }}</h3>
            @foreach($isFr
                ? [['1','var(--text-faint)','Ventes de logiciels'],['2','#1A6FE8','Maintenance récurrente'],['3','#00A87B','Revenus d\'interopérabilité'],['4','#00C896','Revenus d\'infrastructure nationale'],['5','#FFB020','Revenus d\'écosystème régional']]
                : [['1','var(--text-faint)','Software sales'],['2','#1A6FE8','Recurring maintenance'],['3','#00A87B','Interoperability revenue'],['4','#00C896','National infrastructure revenue'],['5','#FFB020','Regional ecosystem revenue']]
            as $idx => $ph)
            <div style="display:flex;gap:12px">
                <div style="display:flex;flex-direction:column;align-items:center">
                    <div style="width:30px;height:30px;border-radius:50%;background:{{ $ph[1] }}20;border:1px solid {{ $ph[1] }}60;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:var(--fs-xs);font-weight:800;color:{{ $ph[1] }}">{{ $ph[0] }}</div>
                    @if($idx < 4)<div style="width:1px;height:12px;background:#1e293b;margin:2px 0"></div>@endif
                </div>
                <div style="padding-top:6px;margin-bottom:{{ $idx < 4 ? '6px' : '0' }}">
                    <div style="font-size:13px;font-weight:600;color:#e2e8f0">{{ $ph[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- KPIs --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'KPIs financiers' : 'Financial KPIs' }}
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px">
                @foreach($isFr
                    ? [['#00C896','Revenus récurrents annuels (ARR)'],['#1A6FE8','Valeur vie client (CLV)'],['#00C896','Marge brute'],['#1A6FE8','Taux de renouvellement'],['#00C896','Revenu par établissement'],['#1A6FE8','Revenu par utilisateur'],['#00C896','Coût d\'acquisition client (CAC)']]
                    : [['#00C896','Annual recurring revenue (ARR)'],['#1A6FE8','Customer lifetime value (CLV)'],['#00C896','Gross margin'],['#1A6FE8','Renewal rate'],['#00C896','Revenue per facility'],['#1A6FE8','Revenue per user'],['#00C896','Cost of customer acquisition (CAC)']]
                as $kpi)
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#0F172A;border-radius:8px;border-left:2px solid {{ $kpi[0] }}">
                    <i data-lucide="chevron-right" style="width:11px;height:11px;color:{{ $kpi[0] }};flex-shrink:0"></i>
                    <span style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:600">{{ $kpi[1] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── 5-YEAR FACILITY TARGETS ──────────────────────────────────── --}}
<div class="section" style="max-width:760px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="target" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Objectifs de croissance sur 5 ans' : '5-year growth targets' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Jalons annuels' : 'Annual milestones' }}</h2>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:24px">
        @foreach($isFr
            ? [['2026','10','établissements','var(--text-faint)'],['2027','50','établissements','#1A6FE8'],['2028','150','établissements','#00A87B'],['2029','Programmes','gouvernementaux','#00C896'],['2031','Expansion','régionale','#FFB020']]
            : [['2026','10','facilities','var(--text-faint)'],['2027','50','facilities','#1A6FE8'],['2028','150','facilities','#00A87B'],['2029','Government','programmes','#00C896'],['2031','Regional','expansion','#FFB020']]
        as $t)
        <div style="background:#0F172A;border:2px solid {{ $t[3] }}40;border-radius:12px;padding:16px 20px;text-align:center;min-width:110px">
            <div style="font-size:var(--fs-2xs);font-weight:800;color:{{ $t[3] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px">{{ $t[0] }}</div>
            <div style="font-size:22px;font-weight:800;color:#e2e8f0;line-height:1.1">{{ $t[1] }}</div>
            <div style="font-size:var(--fs-2xs);color:var(--text-muted);margin-top:2px">{{ $t[2] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Discuter de notre modèle commercial ?' : 'Discuss our commercial model?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe commerciale peut vous présenter les options de licence, les structures de prix et les modalités de partenariat adaptées à votre organisation.'
        : 'Our commercial team can walk you through licensing options, pricing structures, and partnership arrangements suited to your organisation.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/pricing') }}" class="btn-primary">
            {{ $isFr ? 'Voir les tarifs' : 'View pricing' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter notre équipe commerciale' : 'Contact our commercial team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>
