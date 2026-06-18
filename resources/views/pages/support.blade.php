@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Support & SLA — OPES Health Systems' : 'Support & SLA — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Niveaux de support Bronze, Silver, Gold et Platinum — engagements de service pour votre établissement de santé.'
        : 'Bronze, Silver, Gold, and Platinum support tiers — service commitments for your health facility.' }}">

{{-- HERO --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="headphones" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Support & SLA' : 'Support & SLA' }}
    </div>
    <h1>
        {{ $isFr ? 'Votre système ne dort pas.' : 'Your system never sleeps.' }}
        <span class="gradient-text">{{ $isFr ? 'Notre équipe non plus.' : 'Neither do we.' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'Quatre niveaux de support adaptés à chaque taille d\'établissement — des cliniques privées aux hôpitaux généraux en passant par les réseaux ministériels. Choisissez votre niveau de garantie.'
            : 'Four support tiers adapted to every facility size — from private clinics to general hospitals and ministry networks. Choose your level of assurance.' }}
    </p>
</div>

{{-- SLA TIERS --}}
<div style="max-width:1100px;margin:0 auto;padding:0 24px">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px">
        @php
        $tiers = $isFr ? [
            [
                'name'       => 'Bronze',
                'tagline'    => 'Essentiel',
                'color'      => '#cd7f32',
                'icon'       => 'medal',
                'uptime'     => '99,0 %',
                'response'   => '72 h',
                'channels'   => 'E-mail',
                'hours'      => 'Jours ouvrés',
                'onsite'     => 'Non inclus',
                'updates'    => 'Mises à jour majeures',
                'manager'    => 'Support pool',
                'target'     => 'Cliniques, cabinets',
                'features'   => ['Tickets illimités','Base de connaissances en ligne','Mises à jour de sécurité incluses','Rapport d\'incidents mensuel'],
            ],
            [
                'name'       => 'Silver',
                'tagline'    => 'Standard',
                'color'      => '#94a3b8',
                'icon'       => 'shield',
                'uptime'     => '99,5 %',
                'response'   => '24 h',
                'channels'   => 'E-mail + Téléphone',
                'hours'      => 'Jours ouvrés 8h–18h',
                'onsite'     => '2 visites/an',
                'updates'    => 'Mises à jour majeures + mineures',
                'manager'    => 'Support pool prioritaire',
                'target'     => 'Hôpitaux de district, labs',
                'features'   => ['Tout Bronze','Téléphone prioritaire','Revue semestrielle','Mises à jour fonctionnelles'],
            ],
            [
                'name'       => 'Gold',
                'tagline'    => 'Prioritaire',
                'color'      => '#f59e0b',
                'icon'       => 'star',
                'uptime'     => '99,9 %',
                'response'   => '4 h',
                'channels'   => 'E-mail + Téléphone + WhatsApp',
                'hours'      => 'Jours ouvrés 7h–21h',
                'onsite'     => '4 visites/an',
                'updates'    => 'Toutes mises à jour incluses',
                'manager'    => 'Responsable compte dédié',
                'target'     => 'Hôpitaux généraux, chaînes',
                'features'   => ['Tout Silver','Responsable dédié','Tableau de bord de santé du système','Hotline critique 24h weekends'],
                'featured'   => true,
            ],
            [
                'name'       => 'Platinum',
                'tagline'    => 'Entreprise',
                'color'      => '#00C896',
                'icon'       => 'award',
                'uptime'     => '99,99 %',
                'response'   => '1 h',
                'channels'   => 'Tous canaux + Astreinte directe',
                'hours'      => '24/7 · 365 jours',
                'onsite'     => 'Illimité',
                'updates'    => 'Toutes mises à jour + roadmap prioritaire',
                'manager'    => 'Équipe dédiée',
                'target'     => 'Ministères, HMO, réseaux multi-sites',
                'features'   => ['Tout Gold','SLA contractualisé','Astreinte téléphonique directe 24/7','Ingénieur attitré sur site','Accès roadmap prioritaire'],
            ],
        ] : [
            [
                'name'       => 'Bronze',
                'tagline'    => 'Essential',
                'color'      => '#cd7f32',
                'icon'       => 'medal',
                'uptime'     => '99.0%',
                'response'   => '72 h',
                'channels'   => 'Email',
                'hours'      => 'Business days',
                'onsite'     => 'Not included',
                'updates'    => 'Major releases',
                'manager'    => 'Support pool',
                'target'     => 'Clinics, private practices',
                'features'   => ['Unlimited tickets','Online knowledge base','Security updates included','Monthly incident report'],
            ],
            [
                'name'       => 'Silver',
                'tagline'    => 'Standard',
                'color'      => '#94a3b8',
                'icon'       => 'shield',
                'uptime'     => '99.5%',
                'response'   => '24 h',
                'channels'   => 'Email + Phone',
                'hours'      => 'Business days 8am–6pm',
                'onsite'     => '2 visits/year',
                'updates'    => 'Major + minor releases',
                'manager'    => 'Priority support pool',
                'target'     => 'District hospitals, labs',
                'features'   => ['Everything in Bronze','Priority phone line','Semi-annual review','Functional updates'],
            ],
            [
                'name'       => 'Gold',
                'tagline'    => 'Priority',
                'color'      => '#f59e0b',
                'icon'       => 'star',
                'uptime'     => '99.9%',
                'response'   => '4 h',
                'channels'   => 'Email + Phone + WhatsApp',
                'hours'      => 'Business days 7am–9pm',
                'onsite'     => '4 visits/year',
                'updates'    => 'All updates included',
                'manager'    => 'Named account manager',
                'target'     => 'General hospitals, chains',
                'features'   => ['Everything in Silver','Dedicated account manager','System health dashboard','24h critical hotline weekends'],
                'featured'   => true,
            ],
            [
                'name'       => 'Platinum',
                'tagline'    => 'Enterprise',
                'color'      => '#00C896',
                'icon'       => 'award',
                'uptime'     => '99.99%',
                'response'   => '1 h',
                'channels'   => 'All channels + Direct on-call',
                'hours'      => '24/7 · 365 days',
                'onsite'     => 'Unlimited',
                'updates'    => 'All updates + roadmap priority',
                'manager'    => 'Dedicated team',
                'target'     => 'Ministries, HMOs, multi-site networks',
                'features'   => ['Everything in Gold','Contractual SLA','Direct 24/7 phone escalation','On-site dedicated engineer','Roadmap priority access'],
            ],
        ];
        @endphp

        @foreach($tiers as $tier)
        <div class="pricing-card {{ isset($tier['featured']) ? 'pricing-card-featured' : '' }}" style="{{ isset($tier['featured']) ? '' : 'border-color:'.$tier['color'].'30' }}">
            @if(isset($tier['featured']))
            <div class="pricing-recommended">{{ $isFr ? 'LE PLUS POPULAIRE' : 'MOST POPULAR' }}</div>
            @endif
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $tier['color'] }}20;display:flex;align-items:center;justify-content:center">
                    <i data-lucide="{{ $tier['icon'] }}" style="width:18px;height:18px;color:{{ $tier['color'] }}"></i>
                </div>
                <div>
                    <div style="font-weight:800;color:#e2e8f0;font-size:17px">{{ $tier['name'] }}</div>
                    <div style="font-size:11px;color:{{ $tier['color'] }};text-transform:uppercase;letter-spacing:0.07em;font-weight:700">{{ $tier['tagline'] }}</div>
                </div>
            </div>

            <div class="pricing-divider"></div>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px">
                @php
                $attrs = $isFr ? [
                    ['Disponibilité SLA', $tier['uptime']],
                    ['Temps de réponse',  $tier['response']],
                    ['Canaux de support', $tier['channels']],
                    ['Heures de couverture', $tier['hours']],
                    ['Visites sur site',  $tier['onsite']],
                    ['Responsable',       $tier['manager']],
                ] : [
                    ['Uptime SLA',        $tier['uptime']],
                    ['Response time',     $tier['response']],
                    ['Support channels',  $tier['channels']],
                    ['Coverage hours',    $tier['hours']],
                    ['On-site visits',    $tier['onsite']],
                    ['Account manager',   $tier['manager']],
                ];
                @endphp
                @foreach($attrs as $attr)
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <span style="color:#475569;font-size:12px">{{ $attr[0] }}</span>
                    <span style="color:#94a3b8;font-size:12px;font-weight:600;text-align:right">{{ $attr[1] }}</span>
                </div>
                @endforeach
            </div>

            <div class="pricing-divider"></div>

            <ul class="pricing-features" style="margin-top:16px">
                @foreach($tier['features'] as $f)
                <li><i data-lucide="check" class="fi pricing-check"></i> {{ $f }}</li>
                @endforeach
            </ul>

            <a href="{{ url($locale.'/contact') }}" class="{{ isset($tier['featured']) ? 'pricing-cta pricing-cta-primary' : 'pricing-cta pricing-cta-secondary' }}" style="margin-top:20px">
                <i data-lucide="send" style="width:13px;height:13px"></i>
                {{ $isFr ? 'Nous contacter' : 'Contact us' }}
            </a>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- SLA TABLE --}}
<div class="pricing-compare" style="max-width:960px;margin:0 auto;padding:0 24px">
    <h2>{{ $isFr ? 'Comparaison des engagements de service' : 'Service commitment comparison' }}</h2>
    <table class="compare-table">
        <thead>
            <tr>
                <th style="width:35%">{{ $isFr ? 'Engagement' : 'Commitment' }}</th>
                <th>Bronze</th>
                <th>Silver</th>
                <th class="col-featured">Gold</th>
                <th>Platinum</th>
            </tr>
        </thead>
        <tbody>
            @php
            $rows = $isFr ? [
                ['Temps de réponse initial',           '72 h',  '24 h', '4 h',    '1 h'],
                ['Disponibilité SLA',                  '99,0 %','99,5 %','99,9 %','99,99 %'],
                ['Support hors heures ouvrées',        '✕',     '✕',    '✓ week.',  '✓ 24/7'],
                ['Responsable de compte dédié',        '✕',     '✕',    '✓',       '✓'],
                ['Visites sur site par an',            '0',     '2',    '4',       'Illimité'],
                ['Mises à jour fonctionnelles',        '✕',     '✓',    '✓',       '✓'],
                ['Tableau de bord santé système',      '✕',     '✕',    '✓',       '✓'],
                ['Accès roadmap prioritaire',          '✕',     '✕',    '✕',       '✓'],
                ['Formation continue incluse',         '✕',     '✕',    '✓',       '✓'],
                ['SLA contractualisé',                 '✕',     '✕',    '✕',       '✓'],
            ] : [
                ['Initial response time',              '72 h',  '24 h', '4 h',    '1 h'],
                ['Uptime SLA',                         '99.0%', '99.5%','99.9%',  '99.99%'],
                ['Out-of-hours support',               '✕',     '✕',   '✓ weekends','✓ 24/7'],
                ['Named account manager',              '✕',     '✕',    '✓',       '✓'],
                ['On-site visits per year',            '0',     '2',    '4',       'Unlimited'],
                ['Functional updates',                 '✕',     '✓',    '✓',       '✓'],
                ['System health dashboard',            '✕',     '✕',    '✓',       '✓'],
                ['Roadmap priority access',            '✕',     '✕',    '✕',       '✓'],
                ['Ongoing training included',          '✕',     '✕',    '✓',       '✓'],
                ['Contractual SLA',                    '✕',     '✕',    '✕',       '✓'],
            ];
            @endphp
            @foreach($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                @foreach(array_slice($row,1) as $idx => $val)
                <td @if($idx===2) class="col-featured" @endif>
                    @if($val === '✓')
                        <span class="compare-check">✓</span>
                    @elseif($val === '✕')
                        <span class="compare-x">✕</span>
                    @else
                        {{ $val }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="divider"></div>

{{-- INCIDENT RESPONSE --}}
<div class="section" style="max-width:800px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="alert-triangle" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gestion des incidents' : 'Incident management' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Priorité P1 à P4 — nous savons ce qui compte' : 'P1 to P4 priority — we know what matters' }}</h2>
    <p style="color:#64748b;font-size:14px;line-height:1.75;margin:12px 0 28px">
        {{ $isFr
            ? 'Tous les tickets sont classifiés par impact sur la continuité des soins. Un système de santé indisponible est P1 — notre équipe est mobilisée immédiatement, quelle que soit l\'heure.'
            : 'All tickets are classified by impact on care continuity. A down health system is P1 — our team mobilises immediately, regardless of the hour.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px">
        @foreach($isFr
            ? [['P1','Critique','Système indisponible ou risque patient','#ef4444'],['P2','Élevée','Fonctionnalité majeure bloquée','#f59e0b'],['P3','Moyenne','Dégradation partielle','#1A6FE8'],['P4','Faible','Questions / améliorations','#64748b']]
            : [['P1','Critical','System down or patient safety risk','#ef4444'],['P2','High','Major feature blocked','#f59e0b'],['P3','Medium','Partial degradation','#1A6FE8'],['P4','Low','Questions / enhancements','#64748b']]
        as $p)
        <div style="background:#0F172A;border:1px solid {{ $p[3] }}30;border-radius:10px;padding:14px;text-align:left">
            <div style="font-weight:800;color:{{ $p[3] }};font-size:16px;margin-bottom:2px">{{ $p[0] }}</div>
            <div style="font-weight:700;color:#e2e8f0;font-size:12px;margin-bottom:4px">{{ $p[1] }}</div>
            <div style="font-size:11px;color:#64748b;line-height:1.5">{{ $p[2] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Quel niveau vous convient ?' : 'Which tier suits you?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe commerciale vous aide à choisir le niveau de support adapté à votre établissement et votre budget.'
        : 'Our sales team will help you choose the right support tier for your facility and budget.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Nous contacter' : 'Contact us' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/pricing') }}" class="btn-secondary">
            {{ $isFr ? 'Voir les tarifs' : 'View pricing' }} <i data-lucide="tag" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>
