@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ __('about.meta_title') }}"
    description="{{ __('about.meta_desc') }}">

{{-- HERO --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="building-2" style="width:12px;height:12px"></i>
        {{ __('about.hero_eyebrow') }}
    </div>
    <h1 class="about-title">{{ __('about.hero_title') }} <span class="gradient-text">{{ __('about.hero_title_gradient') }}</span></h1>
    <p class="about-sub">{{ __('about.hero_sub') }}</p>
</div>

<div class="section about-layout">

    {{-- MISSION --}}
    <div class="about-card" style="border-color:rgba(0,200,150,0.2)">
        <div class="about-card-icon" style="background:rgba(0,200,150,0.08)">
            <i data-lucide="target" style="width:22px;height:22px;color:#00C896"></i>
        </div>
        <h3>{{ __('about.mission_title') }}</h3>
        <p>{{ __('about.mission_body') }}</p>
    </div>

    <div class="about-card" style="border-color:rgba(26,111,232,0.2)">
        <div class="about-card-icon" style="background:rgba(26,111,232,0.08)">
            <i data-lucide="eye" style="width:22px;height:22px;color:#1A6FE8"></i>
        </div>
        <h3>{{ __('about.vision_title') }}</h3>
        <p>{{ __('about.vision_body') }}</p>
    </div>

</div>

<div class="divider"></div>

{{-- STORY --}}
<div class="section" style="max-width:800px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="book-open" style="width:12px;height:12px"></i>
        {{ __('about.story_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('about.story_title') }}</h2>
    <div class="about-story">
        <p>{{ __('about.story_p1') }}</p>
        <p>{{ __('about.story_p2') }}</p>
        <p>{{ __('about.story_p3') }}</p>
        <p>{{ __('about.story_p4') }}</p>
    </div>
</div>

<div class="divider"></div>

{{-- PLATFORM --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ __('about.platform_eyebrow') }}
    </div>
    <h2 class="section-title">{{ __('about.platform_title') }}</h2>
    <p style="color:var(--text-muted);max-width:680px;margin:12px auto 0;font-size:15px;line-height:1.75">{{ __('about.platform_desc') }}</p>
    <div class="platform-cat-grid">
        @foreach([
            ['icon'=>'file-text',    'color'=>'#00C896', 'label'=>__('about.platform_cat_0')],
            ['icon'=>'building-2',   'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_1')],
            ['icon'=>'flask-conical','color'=>'#00C896', 'label'=>__('about.platform_cat_2')],
            ['icon'=>'pill',         'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_3')],
            ['icon'=>'stethoscope',  'color'=>'#00C896', 'label'=>__('about.platform_cat_4')],
            ['icon'=>'share-2',      'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_5')],
            ['icon'=>'brain',        'color'=>'#00C896', 'label'=>__('about.platform_cat_6')],
            ['icon'=>'zap',          'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_7')],
            ['icon'=>'badge',        'color'=>'#00C896', 'label'=>__('about.platform_cat_8')],
            ['icon'=>'bar-chart-2',  'color'=>'#1A6FE8', 'label'=>__('about.platform_cat_9')],
        ] as $cat)
        <div class="platform-cat-item">
            <div class="platform-cat-icon" style="background:{{ $cat['color'] }}1a">
                <i data-lucide="{{ $cat['icon'] }}" style="width:20px;height:20px;color:{{ $cat['color'] }}"></i>
            </div>
            <span>{{ $cat['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- OPESCARE SPOTLIGHT --}}
<div class="section">
    <div class="opescare-spotlight">
        <div class="section-label" style="margin-bottom:14px">
            <i data-lucide="share-2" style="width:12px;height:12px"></i>
            {{ __('about.opescare_eyebrow') }}
        </div>
        <h2 style="font-size:clamp(20px,3vw,26px);font-weight:700;color:#e2e8f0;margin-bottom:14px;line-height:1.3">{{ __('about.opescare_title') }}</h2>
        <p style="color:var(--text-muted);max-width:640px;line-height:1.75;margin-bottom:28px;font-size:15px">{{ __('about.opescare_body') }}</p>
        <a href="{{ url($locale.'/products/opescare') }}" class="btn-primary" style="display:inline-flex">
            {{ __('about.opescare_cta') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
    </div>
</div>

<div class="divider"></div>

{{-- NUMBERS --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:32px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        {{ __('about.stats_eyebrow') }}
    </div>
    <div class="stats-bar" style="max-width:900px;margin:0 auto">
        @foreach([
            ['value'=>'22','label'=>__('about.stat_0_label')],
            ['value'=>'EN/FR','label'=>__('about.stat_1_label')],
            ['value'=>'CEMAC','label'=>__('about.stat_2_label')],
            ['value'=>'HL7 FHIR','label'=>__('about.stat_3_label')],
        ] as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s['value'] }}</div>
            <div class="stat-label">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- OPES HEALTH OS UMBRELLA --}}
<div class="section" style="max-width:1000px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        OPES Health OS
    </div>
    <h2 class="section-title">{{ $isFr ? 'Une plateforme, six familles de produits' : 'One platform, six product families' }}</h2>
    <p style="color:var(--text-muted);max-width:700px;margin:12px auto 36px;font-size:15px;line-height:1.75">
        {{ $isFr
            ? 'OPES Health OS est notre système d\'exploitation de santé unifié. Il regroupe six familles de produits — du dossier patient à l\'intelligence clinique — toutes interopérables, bilingues, et conçues pour fonctionner ensemble ou indépendamment.'
            : 'OPES Health OS is our unified health operating system. It groups six product families — from patient records to clinical intelligence — all interoperable, bilingual, and designed to work together or independently.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px">
        @foreach(config('product_families.families') as $family)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $family['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <i data-lucide="{{ $family['icon'] }}" style="width:20px;height:20px;color:{{ $family['color'] }}"></i>
            </div>
            <div style="font-weight:800;color:#e2e8f0;font-size:15px;margin-bottom:3px">{{ $family['name'] }}</div>
            <div style="font-size:var(--fs-xs);color:{{ $family['color'] }};text-transform:uppercase;letter-spacing:0.07em;font-weight:700;margin-bottom:8px">{{ $family['tagline'] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">{{ $family['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- GOVERNANCE --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="shield-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gouvernance' : 'Governance' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Gouvernance d\'entreprise et modèle opérationnel' : 'Corporate governance and operating model' }}</h2>
    <p style="color:var(--text-muted);max-width:720px;font-size:15px;line-height:1.75;margin-bottom:36px">
        {{ $isFr
            ? 'OPES Health Systems opère selon un modèle de gouvernance rigoureux qui aligne nos décisions produit sur les besoins cliniques réels, la conformité réglementaire, et la durabilité de nos clients. Notre architecture de gouvernance comprend des comités techniques, cliniques et éthiques qui supervisent chaque évolution de la plateforme.'
            : 'OPES Health Systems operates under a rigorous governance model that aligns our product decisions with real clinical needs, regulatory compliance, and the sustainability of our clients. Our governance architecture includes technical, clinical, and ethics committees that oversee every evolution of the platform.' }}
    </p>
    <div class="pi-grid" style="max-width:960px">
        @php
        $govItems = $isFr ? [
            ['icon'=>'building-2',   'color'=>'#00C896','title'=>'Conseil de direction',        'desc'=>'Supervision stratégique de l\'entreprise, définition des orientations à long terme et des priorités d\'investissement.'],
            ['icon'=>'users',        'color'=>'#1A6FE8','title'=>'Comité technique & produit',   'desc'=>'Gouvernance de la roadmap technologique, validation des architectures, gestion des dépendances et des risques techniques.'],
            ['icon'=>'heart',        'color'=>'#00C896','title'=>'Comité de gouvernance clinique','desc'=>'Révision des modules cliniques par des praticiens de santé africains pour garantir la pertinence médicale de chaque fonctionnalité.'],
            ['icon'=>'scale',        'color'=>'#1A6FE8','title'=>'Comité conformité & éthique',  'desc'=>'Supervision de la conformité OHADA, droit camerounais des données de santé, GDPR-alignement, et éthique de l\'IA clinique.'],
            ['icon'=>'bar-chart-2',  'color'=>'#00C896','title'=>'Gestion des risques',           'desc'=>'Identification, évaluation et atténuation des risques opérationnels, financiers, et de continuité de service pour nos clients.'],
            ['icon'=>'refresh-cw',   'color'=>'#1A6FE8','title'=>'Cycle de révision produit',    'desc'=>'Révisions trimestrielles des performances produit intégrant les retours clients, les incidents et les évolutions réglementaires.'],
        ] : [
            ['icon'=>'building-2',   'color'=>'#00C896','title'=>'Board of Directors',           'desc'=>'Strategic oversight of the company, setting long-term direction and investment priorities.'],
            ['icon'=>'users',        'color'=>'#1A6FE8','title'=>'Technical & Product Committee', 'desc'=>'Governance of the technology roadmap, architecture validation, dependency management, and technical risk.'],
            ['icon'=>'heart',        'color'=>'#00C896','title'=>'Clinical Governance Committee', 'desc'=>'Review of clinical modules by African health practitioners to ensure the medical relevance of every feature.'],
            ['icon'=>'scale',        'color'=>'#1A6FE8','title'=>'Compliance & Ethics Committee', 'desc'=>'Supervision of OHADA compliance, Cameroonian health data law, GDPR-alignment, and clinical AI ethics.'],
            ['icon'=>'bar-chart-2',  'color'=>'#00C896','title'=>'Risk Management',               'desc'=>'Identification, assessment, and mitigation of operational, financial, and service continuity risks for our clients.'],
            ['icon'=>'refresh-cw',   'color'=>'#1A6FE8','title'=>'Product Review Cycle',         'desc'=>'Quarterly product performance reviews integrating customer feedback, incidents, and regulatory developments.'],
        ];
        @endphp
        @foreach($govItems as $g)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $g['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $g['icon'] }}" style="width:18px;height:18px;color:{{ $g['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $g['title'] }}</div>
            <div style="font-size:13px;color:var(--text-muted);line-height:1.6">{{ $g['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- VALUES --}}
<div class="section" style="text-align:center">
    <h2 class="section-title">{{ __('about.values_title') }}</h2>
    <div class="pi-grid" style="max-width:900px;margin:32px auto 0">
        @foreach([
            ['icon'=>'heart','color'=>'#00C896','title'=>__('about.value_0_title'),'desc'=>__('about.value_0_desc')],
            ['icon'=>'shield-check','color'=>'#1A6FE8','title'=>__('about.value_1_title'),'desc'=>__('about.value_1_desc')],
            ['icon'=>'globe-2','color'=>'#00C896','title'=>__('about.value_2_title'),'desc'=>__('about.value_2_desc')],
            ['icon'=>'handshake','color'=>'#1A6FE8','title'=>__('about.value_3_title'),'desc'=>__('about.value_3_desc')],
        ] as $v)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $v['icon'] }}" style="width:18px;height:18px;color:{{ $v['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $v['title'] }}</div>
            <div style="font-size:13px;color:var(--text-muted);line-height:1.6">{{ $v['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- MANIFESTO --}}
<div class="about-manifesto">
    <p>{{ __('about.manifesto_line1') }}</p>
    <p class="manifesto-accent">{{ __('about.manifesto_line2') }}</p>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ __('about.cta_title') }}</h2>
    <p>{{ __('about.cta_body') }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ __('about.cta_contact') }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/partnerships') }}" class="btn-secondary">
            {{ __('about.cta_partnership') }} <i data-lucide="handshake" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>
