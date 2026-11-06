@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ __('solutions.meta_title') }}"
    description="{{ __('solutions.meta_desc') }}">

{{-- HERO --}}
<div class="sol-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ __('solutions.hero_eyebrow') }}
    </div>
    <h1 class="sol-title">{{ __('solutions.hero_title_1') }} <span style="color:#acd5ec">{{ __('solutions.hero_title_gradient') }}</span></h1>
    <p class="sol-sub">{{ __('solutions.hero_sub') }}</p>
</div>

{{-- FACILITY SOLUTIONS --}}
<div class="section" style="padding-top:8px">
    <p class="section-sub" style="margin:0 auto 22px;text-align:center">{{ __('solutions.audience_intro') }}</p>
    <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap">
        <a class="btn-secondary" href="#government">{{ __('solutions.audience_government') }}</a>
        <a class="btn-secondary" href="#health-organizations">{{ __('solutions.audience_health') }}</a>
        <a class="btn-secondary" href="#health-ai-partners">{{ __('solutions.audience_ai') }}</a>
        <a class="btn-secondary" href="#medical-informatics-partners">{{ __('solutions.audience_academic') }}</a>
    </div>
</div>

<section class="section" id="government" style="scroll-margin-top:96px">
    <h2 class="section-title">{{ __('solutions.audience_government') }}</h2>
    <p class="section-sub" style="max-width:850px;margin:0 auto 28px">{{ $isFr ? 'Nous collaborons avec les ministères, programmes nationaux, autorités sanitaires et partenaires de développement au Cameroun, en Afrique et à l’international. Chaque initiative est définie avec le pays et les organisations responsables.' : 'We work with ministries, national programmes, health authorities and development partners in Cameroon, across Africa and internationally. Each initiative is scoped with the country and organizations responsible.' }}</p>
    <div class="sol-grid">
        <article class="sol-card">
            <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="landmark" style="width:24px;height:24px;color:#8AC5ED"></i></div>
            <h3 class="sol-card-title">{{ __('solutions.facility_4_title') }}</h3>
            <p class="sol-card-desc">{{ __('solutions.facility_4_desc') }}</p>
            <div class="sol-systems"><span class="sol-badge">UHC IS</span><span class="sol-badge">OPES CDMS</span><span class="sol-badge">OPESCare</span><span class="sol-badge">RCMIS</span></div>
        </article>
        <article class="sol-card">
            <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="chart-no-axes-combined" style="width:24px;height:24px;color:#8AC5ED"></i></div>
            <h3 class="sol-card-title">{{ $isFr ? 'Services de données de santé' : 'Health data services' }}</h3>
            <p class="sol-card-desc">{{ $isFr ? 'Échange et qualité des données, rapports opérationnels, indicateurs de programme et analytique à définir avec les autorités concernées, sur des bases légales et des règles d’accès convenues.' : 'Health information exchange, data quality, operational reporting, programme indicators and analytics scoped with responsible authorities under agreed lawful bases and access rules.' }}</p>
            <div class="sol-systems"><span class="sol-badge">OPESCare</span><span class="sol-badge">APIs</span><span class="sol-badge">Reporting</span></div>
        </article>
        <article class="sol-card">
            <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="workflow" style="width:24px;height:24px;color:#8AC5ED"></i></div>
            <h3 class="sol-card-title">{{ $isFr ? 'Aide à la décision clinique et programmes' : 'Clinical decision support and programmes' }}</h3>
            <p class="sol-card-desc">{{ $isFr ? 'Intégrer des rappels, vérifications de sécurité et parcours d’aide à la décision aux logiciels de santé, et étudier leur adéquation aux politiques et protocoles du programme.' : 'Bring reminders, safety checks and clinical decision support workflows into health software, then assess fit with each programme’s policies and protocols.' }}</p>
            <div class="sol-systems"><span class="sol-badge">CDSS</span><span class="sol-badge">OPES EMR</span><span class="sol-badge">OPES Triage</span></div>
        </article>
    </div>
    <p class="brand-notice" style="max-width:900px;margin:20px auto 0">{{ $isFr ? 'Les systèmes, indicateurs, intégrations, hébergement et gouvernance sont évalués et convenus projet par projet. Une présence au catalogue ne constitue pas une confirmation de déploiement dans un pays.' : 'Systems, indicators, integrations, hosting and governance are assessed and agreed project by project. A catalogue listing does not confirm deployment in a particular country.' }}</p>
</section>

<section class="section" id="health-organizations" style="scroll-margin-top:96px">
    <h2 class="section-title">{{ __('solutions.audience_health') }}</h2>
    <div class="sol-grid">

        @foreach([
            ['id'=>'hospitals','icon'=>'hospital','color'=>'#69D5F5','bg'=>'rgba(0,200,150,0.08)','title'=>__('solutions.facility_0_title'),'desc'=>__('solutions.facility_0_desc'),'systems'=>['OPES Hospital HIS','OPES EMR','OPESCare','OPES Lab','PHARMIS','RADIS','RCMIS']],
            ['id'=>'clinics','icon'=>'stethoscope','color'=>'#8AC5ED','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_1_title'),'desc'=>__('solutions.facility_1_desc'),'systems'=>['OPES EMR','Opes Triage','OPESCare']],
            ['id'=>'laboratories','icon'=>'microscope','color'=>'#69D5F5','bg'=>'rgba(0,200,150,0.08)','title'=>__('solutions.facility_2_title'),'desc'=>__('solutions.facility_2_desc'),'systems'=>['OPES Lab','OPESCare']],
            ['id'=>'pharmacies','icon'=>'pill','color'=>'#8AC5ED','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_3_title'),'desc'=>__('solutions.facility_3_desc'),'systems'=>['PHARMIS','OPESCare']],
            ['id'=>'multi-site','icon'=>'users','color'=>'#8AC5ED','bg'=>'rgba(26,111,232,0.08)','title'=>__('solutions.facility_5_title'),'desc'=>__('solutions.facility_5_desc'),'systems'=>['OPESCare','OPES Hospital HIS','OPES EMR','All 22 systems']],
        ] as $sol)
        <div class="sol-card" id="{{ $sol['id'] }}" style="scroll-margin-top:90px">
            <div class="sol-card-icon" style="background:{{ $sol['bg'] }}">
                <i data-lucide="{{ $sol['icon'] }}" style="width:24px;height:24px;color:{{ $sol['color'] }}"></i>
            </div>
            <h3 class="sol-card-title">{{ $sol['title'] }}</h3>
            <p class="sol-card-desc">{{ $sol['desc'] }}</p>
            <div class="sol-systems">
                @foreach($sol['systems'] as $sys)
                <span class="sol-badge">{{ $sys }}</span>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>
</div>

<section class="section" id="opescare-health-network" style="scroll-margin-top:96px">
    <div class="sol-card" style="max-width:920px;margin:0 auto;display:grid;grid-template-columns:auto 1fr;gap:18px;align-items:start">
        <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="network" style="width:25px;height:25px;color:#8AC5ED"></i></div>
        <div>
            <div class="section-label">{{ $isFr ? 'Produit phare · Réseau de santé' : 'Flagship · Health Network' }}</div>
            <h2 class="sol-card-title">{{ __('solutions.opescare_title') }}</h2>
            <p class="sol-card-desc">{{ __('solutions.opescare_body') }}</p>
            <a class="btn-secondary" href="{{ url($locale.'/products/opescare') }}" style="display:inline-flex;margin-top:14px">{{ __('solutions.opescare_link') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
        </div>
    </div>
</section>

<section class="section" id="health-ai-partners" style="scroll-margin-top:96px">
    <div class="sol-card" style="max-width:920px;margin:0 auto">
        <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="brain-circuit" style="width:25px;height:25px;color:#8AC5ED"></i></div>
        <h2 class="sol-card-title">{{ __('solutions.ai_title') }}</h2>
        <p class="sol-card-desc">{{ __('solutions.ai_body') }}</p>
        <ul style="color:var(--text-muted);line-height:1.8;padding-left:20px;margin:14px 0">
            <li>{{ $isFr ? 'Intégration d’outils d’IA partenaires dans des parcours de santé convenus.' : 'Integrate partner AI tools into agreed health workflows.' }}</li>
            <li>{{ $isFr ? 'Évaluation avec des équipes cliniques et des établissements partenaires.' : 'Evaluate with clinical teams and participating health organizations.' }}</li>
            <li>{{ $isFr ? 'Étude séparée de tout accès aux données, des autorisations, de la sécurité et des critères de validation.' : 'Review data access, permissions, security and validation criteria as separate workstreams.' }}</li>
        </ul>
        <a class="btn-primary" href="{{ url($locale.'/become-a-partner?partner_type=health_ai') }}" style="display:inline-flex;margin-top:14px">{{ __('solutions.ai_link') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
        <a class="btn-secondary" href="{{ url($locale.'/partnerships') }}" style="display:inline-flex;margin:14px 0 0 8px">{{ __('solutions.more_partnerships') }}</a>
    </div>
</section>

<section class="section" id="medical-informatics-partners" style="scroll-margin-top:96px">
    <div class="sol-card" style="max-width:920px;margin:0 auto">
        <div class="sol-card-icon" style="background:rgba(49,95,125,0.18)"><i data-lucide="graduation-cap" style="width:25px;height:25px;color:#8AC5ED"></i></div>
        <h2 class="sol-card-title">{{ __('solutions.academic_title') }}</h2>
        <p class="sol-card-desc">{{ __('solutions.academic_body') }}</p>
        <a class="btn-secondary" href="{{ url($locale.'/medical-informatics') }}" style="display:inline-flex;margin-top:14px">{{ __('solutions.academic_link') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
    </div>
</section>

<div class="divider"></div>

{{-- WHY OPES --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="star" style="width:12px;height:12px"></i>
        {{ __('solutions.why_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('solutions.why_title') }}</h2>
    <p class="section-sub" style="max-width:640px;margin:0 auto 48px">{{ __('solutions.why_sub') }}</p>

    <div class="pi-grid" style="max-width:900px;margin:0 auto">
        @foreach([
            ['icon'=>'globe','color'=>'#69D5F5','title'=>__('solutions.why_0_title'),'desc'=>__('solutions.why_0_desc')],
            ['icon'=>'link','color'=>'#8AC5ED','title'=>__('solutions.why_1_title'),'desc'=>__('solutions.why_1_desc')],
            ['icon'=>'wifi-off','color'=>'#69D5F5','title'=>__('solutions.why_2_title'),'desc'=>__('solutions.why_2_desc')],
            ['icon'=>'shield','color'=>'#8AC5ED','title'=>__('solutions.why_3_title'),'desc'=>__('solutions.why_3_desc')],
            ['icon'=>'cpu','color'=>'#69D5F5','title'=>__('solutions.why_4_title'),'desc'=>__('solutions.why_4_desc')],
            ['icon'=>'life-buoy','color'=>'#8AC5ED','title'=>__('solutions.why_5_title'),'desc'=>__('solutions.why_5_desc')],
        ] as $w)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $w['icon'] }}" style="width:18px;height:18px;color:{{ $w['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $w['title'] }}</div>
            <div style="font-size:13px;color:var(--text-muted);line-height:1.6">{{ $w['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ __('solutions.cta_title') }}</h2>
    <p>{{ __('solutions.cta_sub') }}</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        {{ __('solutions.cta_button') }}
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>
