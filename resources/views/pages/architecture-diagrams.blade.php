@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Pack de diagrammes d\'architecture enterprise — OPES Health Systems' : 'Enterprise Architecture Diagram Pack — OPES Health Systems' }}"
    description="{{ $isFr ? '12 diagrammes d\'architecture visuels OPES Health OS : écosystème, Health ID, HIE, hôpital, intelligence clinique, santé nationale, sécurité et gouvernance.' : '12 visual OPES Health OS architecture diagrams: ecosystem, Health ID, HIE, hospital, clinical intelligence, national health, security, and governance.' }}">

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="git-branch" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Standards d\'architecture officielle v1.0' : 'Official architecture standards v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Pack de diagrammes' : 'Enterprise Architecture' }}
        <span class="gradient-text">{{ $isFr ? 'd\'architecture enterprise' : 'Diagram Pack' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Les diagrammes d\'architecture communiquent plus en 30 secondes que 20 pages de texte — la référence visuelle officielle pour OPES Health OS, conçue pour les ministères, conseils d\'administration, investisseurs, comités techniques et équipes d\'appels d\'offres.'
            : 'Architecture diagrams communicate more in 30 seconds than 20 pages of text — the definitive visual reference for OPES Health OS, designed for ministries, hospital boards, investors, technical committees, and procurement teams.' }}
    </p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:20px">
        @foreach($isFr
            ? ['Ministères de la Santé','Conseils d\'administration','Investisseurs','Bailleurs de fonds','Comités techniques','Équipes d\'appels d\'offres']
            : ['Ministries of Health','Hospital Boards','Investors','Donors','Technical Committees','Procurement Teams']
        as $chip)
        <span style="background:#0F172A;border:1px solid #1e293b;border-radius:20px;padding:5px 12px;font-size:11px;color:var(--text-muted)">{{ $chip }}</span>
        @endforeach
    </div>
</div>

{{-- ── STATS ─────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['12','Diagrammes'],['6','Couches d\'architecture'],['5','Domaines systèmes'],['1','Écosystème unifié']]
            : [['12','Diagrams'],['6','Architecture layers'],['5','System domains'],['1','Unified ecosystem']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

@php
/* ── Reusable inline styles ──────────────────────────────────────── */
$node     = 'background:#0F172A;border:1px solid #1e293b;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#e2e8f0;';
$nodeTeal = 'background:#00C89610;border:1px solid #00C89640;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#00C896;';
$nodeBlue = 'background:#1A6FE810;border:1px solid #1A6FE840;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#1A6FE8;';
$nodeGov  = 'background:#F59E0B10;border:1px solid #F59E0B40;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#F59E0B;';
$nodeRed  = 'background:#EF444410;border:1px solid #EF444440;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#EF4444;';
$arrow    = 'display:flex;justify-content:center;padding:5px 0;';
$line     = 'width:1px;height:20px;background:linear-gradient(to bottom,#00C89650,#1A6FE850);margin:0 auto;';
$hline    = 'height:1px;background:linear-gradient(to right,transparent,#1e293b,transparent);margin:0 16%;';
$card     = 'background:#080E1A;border:1px solid #1e293b;border-radius:16px;padding:28px 24px;';
@endphp

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAM 1: OPES Health OS Ecosystem Architecture               --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
        <div style="width:32px;height:32px;border-radius:8px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#00C896;flex-shrink:0">1</div>
        <div>
            <div class="section-label" style="margin-bottom:4px"><i data-lucide="layers" style="width:12px;height:12px"></i> {{ $isFr ? 'Diagramme 1' : 'Diagram 1' }}</div>
            <h2 style="font-size:18px;font-weight:700;color:#e2e8f0;margin:0">{{ $isFr ? 'Architecture écosystème OPES Health OS' : 'OPES Health OS Ecosystem Architecture' }}</h2>
        </div>
    </div>
    <div style="{{ $card }}">
        {{-- Root --}}
        <div style="max-width:360px;margin:0 auto">
            <div style="{{ $nodeBlue }}font-size:13px;letter-spacing:0.05em;padding:12px 20px">OPES HEALTH SYSTEMS</div>
        </div>
        <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
        <div style="max-width:360px;margin:0 auto">
            <div style="{{ $nodeTeal }}font-size:13px;padding:12px 20px">OPES HEALTH OS</div>
        </div>
        {{-- Branch to 4 products --}}
        <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
        <div style="{{ $hline }}"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-top:0">
            @foreach($isFr
                ? [['#00C896','OPES Clinic'],['#1A6FE8','OPES Hospital'],['#00C896','OPES Care'],['#1A6FE8','OPES Clinical Intelligence']]
                : [['#00C896','OPES Clinic'],['#1A6FE8','OPES Hospital'],['#00C896','OPES Care'],['#1A6FE8','OPES Clinical Intelligence']]
            as $p)
            <div style="display:flex;flex-direction:column;align-items:center">
                <div style="width:1px;height:16px;background:#1e293b"></div>
                <div style="background:{{ $p[0] }}10;border:1px solid {{ $p[0] }}40;border-radius:8px;padding:8px 10px;text-align:center;font-size:11px;font-weight:700;color:{{ $p[0] }};width:100%">{{ $p[1] }}</div>
            </div>
            @endforeach
        </div>
        {{-- Sub-layer --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-top:6px">
            @foreach($isFr
                ? [['var(--text-muted)','Systèmes spécialisés'],['var(--text-muted)','Opérations hôpital'],['var(--text-muted)','Health ID · MPI · HIE'],['var(--text-muted)','CDSS · Triage · Analytics']]
                : [['var(--text-muted)','Specialty systems'],['var(--text-muted)','Hospital operations'],['var(--text-muted)','Health ID · MPI · HIE'],['var(--text-muted)','CDSS · Triage · Analytics']]
            as $sub)
            <div style="display:flex;flex-direction:column;align-items:center">
                <div style="width:1px;height:14px;background:#1e293b"></div>
                <div style="background:#0F172A;border:1px dashed #1e293b;border-radius:8px;padding:7px 8px;text-align:center;font-size:10px;color:var(--text-faint);width:100%">{{ $sub[1] }}</div>
            </div>
            @endforeach
        </div>
        {{-- Convergence to Public Health --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-top:6px">
            @for($i=0;$i<4;$i++)<div style="display:flex;justify-content:center"><div style="width:1px;height:14px;background:#1e293b"></div></div>@endfor
        </div>
        <div style="{{ $hline }}"></div>
        <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
        <div style="max-width:400px;margin:0 auto">
            <div style="{{ $nodeGov }}padding:11px 20px">{{ $isFr ? 'OPES SANTÉ PUBLIQUE' : 'OPES PUBLIC HEALTH' }}</div>
        </div>
        <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
        <div style="max-width:400px;margin:0 auto">
            <div style="{{ $nodeGov }}padding:11px 20px">{{ $isFr ? 'GOUVERNEMENTS & PROGRAMMES NATIONAUX' : 'GOVERNMENTS & NATIONAL PROGRAMMES' }}</div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAMS 2 + 3                                                  --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px">

        {{-- DIAGRAM 2: Health ID Architecture --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#00C896;flex-shrink:0">2</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 2' : 'Diagram 2' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">OPES Health ID</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="{{ $node }}">{{ $isFr ? 'Patient' : 'Patient' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeTeal }}">OPES Health ID</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeBlue }}">{{ $isFr ? 'Index Patient Maître (MPI)' : 'Master Patient Index (MPI)' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $hline }}"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    @foreach($isFr
                        ? [['#00C896','Clinique'],['#1A6FE8','Hôpital'],['#00C896','Laboratoire'],['#1A6FE8','Pharmacie']]
                        : [['#00C896','Clinic'],['#1A6FE8','Hospital'],['#00C896','Laboratory'],['#1A6FE8','Pharmacy']]
                    as $f)
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:1px;height:14px;background:#1e293b"></div>
                        <div style="background:{{ $f[0] }}10;border:1px solid {{ $f[0] }}30;border-radius:7px;padding:7px 10px;text-align:center;font-size:11px;font-weight:700;color:{{ $f[0] }};width:100%">{{ $f[1] }}</div>
                    </div>
                    @endforeach
                </div>
                <div style="{{ $hline }}margin-top:14px"></div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="background:#00C89610;border:1px solid #00C89630;border-radius:8px;padding:12px;text-align:center">
                    <div style="font-size:11px;font-weight:700;color:#00C896">{{ $isFr ? 'Dossier longitudinal' : 'Longitudinal Record' }}</div>
                    <div style="font-size:10px;color:var(--text-faint);margin-top:4px">{{ $isFr ? 'Un patient · Une identité · Un dossier' : 'One Patient · One Identity · One Record' }}</div>
                </div>
            </div>
        </div>

        {{-- DIAGRAM 3: HIE --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#1A6FE8;flex-shrink:0">3</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 3' : 'Diagram 3' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Échange d\'informations de santé (HIE)' : 'Health Information Exchange (HIE)' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center">
                    {{-- Senders --}}
                    <div style="display:flex;flex-direction:column;gap:6px">
                        @foreach($isFr
                            ? ['Hôpital A','Hôpital B','Laboratoire','Pharmacie','Assurance','Santé publique']
                            : ['Hospital A','Hospital B','Laboratory','Pharmacy','Insurance','Public Health']
                        as $sender)
                        <div style="{{ $node }}font-size:10px;padding:7px 10px">{{ $sender }}</div>
                        @endforeach
                    </div>
                    {{-- Arrows + HUB --}}
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:0 8px">
                        <div style="font-size:18px;color:#1A6FE8">⇄</div>
                        <div style="background:#1A6FE810;border:2px solid #1A6FE840;border-radius:10px;padding:16px 12px;text-align:center">
                            <i data-lucide="share-2" style="width:20px;height:20px;color:#1A6FE8;display:block;margin:0 auto 6px"></i>
                            <div style="font-size:10px;font-weight:800;color:#1A6FE8;letter-spacing:0.06em">OPES HIE</div>
                            <div style="font-size:9px;color:var(--text-faint);margin-top:2px">{{ $isFr ? 'PLATEFORME' : 'PLATFORM' }}</div>
                        </div>
                        <div style="font-size:18px;color:#1A6FE8">⇄</div>
                    </div>
                    {{-- Exchange types --}}
                    <div style="display:flex;flex-direction:column;gap:6px">
                        @foreach($isFr
                            ? [['#00C896','Référencements'],['#1A6FE8','Résultats'],['#00C896','Dossiers cliniques'],['#1A6FE8','Sinistres'],['#00C896','Notifications']]
                            : [['#00C896','Referrals'],['#1A6FE8','Results'],['#00C896','Clinical records'],['#1A6FE8','Claims'],['#00C896','Notifications']]
                        as $ex)
                        <div style="background:{{ $ex[0] }}10;border:1px solid {{ $ex[0] }}30;border-radius:7px;padding:7px 10px;text-align:center;font-size:10px;font-weight:600;color:{{ $ex[0] }}">{{ $ex[1] }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAM 4: OPES Hospital Architecture                           --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="width:28px;height:28px;border-radius:7px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#00C896;flex-shrink:0">4</div>
        <div>
            <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 4' : 'Diagram 4' }}</div>
            <div style="font-size:18px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Architecture OPES Hospital' : 'OPES Hospital Architecture' }}</div>
        </div>
    </div>
    <div style="{{ $card }}">
        <div style="display:grid;grid-template-columns:200px 1fr;gap:24px;align-items:start">
            {{-- Left: main flow --}}
            <div>
                <div style="{{ $node }}">{{ $isFr ? 'Patient' : 'Patient' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeTeal }}">{{ $isFr ? 'Enregistrement' : 'Registration' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeBlue }}">{{ $isFr ? 'Dossier médical électronique (DME)' : 'Electronic Medical Record (EMR)' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeTeal }}font-size:10px">{{ $isFr ? 'Documentation clinique' : 'Clinical Documentation' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="background:#F59E0B10;border:1px solid #F59E0B40;border-radius:8px;padding:9px 14px;text-align:center;font-size:11px;font-weight:700;color:#F59E0B">{{ $isFr ? 'Tableau de bord exécutif' : 'Executive Dashboard' }}</div>
            </div>
            {{-- Right: branch departments --}}
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:14px;padding-left:16px">{{ $isFr ? 'Départements connectés via EMR' : 'Departments connected via EMR' }}</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($isFr
                        ? [['microscope','#00C896','Laboratoire'],['scan','#1A6FE8','Radiologie'],['pill','#00C896','Pharmacie'],['dollar-sign','#1A6FE8','Facturation'],['bed','#00C896','Admissions & Lits'],['heart-pulse','#1A6FE8','Chirurgie & Soins infirmiers']]
                        : [['microscope','#00C896','Laboratory'],['scan','#1A6FE8','Radiology'],['pill','#00C896','Pharmacy'],['dollar-sign','#1A6FE8','Billing'],['bed','#00C896','Admissions & Beds'],['heart-pulse','#1A6FE8','Theatre & Nursing']]
                    as $dept)
                    <div style="display:flex;align-items:center;gap:10px;padding:9px 14px;background:#0F172A;border:1px solid #1e293b;border-radius:8px;border-left:2px solid {{ $dept[1] }}">
                        <i data-lucide="{{ $dept[0] }}" style="width:13px;height:13px;color:{{ $dept[1] }};flex-shrink:0"></i>
                        <span style="font-size:11px;font-weight:700;color:#e2e8f0">{{ $dept[2] }}</span>
                        <div style="margin-left:auto">
                            <i data-lucide="arrow-left" style="width:10px;height:10px;color:#1e293b"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAMS 5 + 6                                                  --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px">

        {{-- DIAGRAM 5: Clinical Intelligence --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#00C896;flex-shrink:0">5</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 5' : 'Diagram 5' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'OPES Intelligence Clinique' : 'OPES Clinical Intelligence' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="{{ $node }}">{{ $isFr ? 'Données cliniques' : 'Clinical data' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeBlue }}font-size:13px;padding:11px 14px">OPES Clinical Intelligence</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $hline }}"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
                    @foreach($isFr
                        ? [['#00C896','cpu','CDSS'],['#1A6FE8','zap','Triage'],['#00C896','bar-chart-2','Analytique'],['#1A6FE8','activity','Surveillance']]
                        : [['#00C896','cpu','CDSS'],['#1A6FE8','zap','Triage'],['#00C896','bar-chart-2','Analytics'],['#1A6FE8','activity','Surveillance']]
                    as $ci)
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:1px;height:14px;background:#1e293b"></div>
                        <div style="background:{{ $ci[0] }}10;border:1px solid {{ $ci[0] }}30;border-radius:8px;padding:10px;text-align:center;width:100%">
                            <i data-lucide="{{ $ci[1] }}" style="width:16px;height:16px;color:{{ $ci[0] }};margin:0 auto 5px;display:block"></i>
                            <div style="font-size:11px;font-weight:700;color:{{ $ci[0] }}">{{ $ci[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="{{ $hline }}margin-top:14px"></div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $node }}">{{ $isFr ? 'Professionnels de santé' : 'Healthcare professionals' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="background:#00C89610;border:1px solid #00C89630;border-radius:8px;padding:10px;text-align:center">
                    <div style="font-size:12px;font-weight:700;color:#00C896">{{ $isFr ? 'Meilleures décisions' : 'Better decisions' }}</div>
                </div>
            </div>
        </div>

        {{-- DIAGRAM 6: National Digital Health --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#1A6FE8;flex-shrink:0">6</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 6' : 'Diagram 6' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Santé numérique nationale' : 'National Digital Health' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="{{ $nodeGov }}font-size:12px;padding:11px">{{ $isFr ? 'Ministère de la Santé' : 'Ministry of Health' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeBlue }}font-size:12px;padding:11px">{{ $isFr ? 'Plateforme nationale de santé numérique' : 'National Digital Health Platform' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $hline }}"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
                    @foreach($isFr
                        ? [['#00C896','fingerprint','Health ID'],['#1A6FE8','share-2','HIE'],['#00C896','database','Registres'],['#1A6FE8','bar-chart-2','Analytique']]
                        : [['#00C896','fingerprint','Health ID'],['#1A6FE8','share-2','HIE'],['#00C896','database','Registries'],['#1A6FE8','bar-chart-2','Analytics']]
                    as $nc)
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:1px;height:14px;background:#1e293b"></div>
                        <div style="background:{{ $nc[0] }}10;border:1px solid {{ $nc[0] }}30;border-radius:8px;padding:9px;text-align:center;width:100%">
                            <i data-lucide="{{ $nc[1] }}" style="width:14px;height:14px;color:{{ $nc[0] }};margin:0 auto 4px;display:block"></i>
                            <div style="font-size:11px;font-weight:700;color:{{ $nc[0] }}">{{ $nc[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="{{ $hline }}margin-top:14px"></div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $node }}">{{ $isFr ? 'Établissements de santé' : 'Healthcare facilities' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeTeal }}">{{ $isFr ? 'Patients' : 'Patients' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAMS 7 + 8                                                  --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px">

        {{-- DIAGRAM 7: Public Health --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#00C896;flex-shrink:0">7</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 7' : 'Diagram 7' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Architecture santé publique' : 'OPES Public Health Architecture' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                @foreach($isFr
                    ? [['var(--text-muted)','Établissements de santé'],['#1A6FE8','Rapports de santé publique'],['#00C896','Moteur de surveillance des maladies'],['#1A6FE8','Plateforme d\'analytique nationale'],['#F59E0B','Tableaux de bord gouvernementaux']]
                    : [['var(--text-muted)','Healthcare facilities'],['#1A6FE8','Public health reporting'],['#00C896','Disease surveillance engine'],['#1A6FE8','National analytics platform'],['#F59E0B','Government dashboards']]
                as $idx => $ph)
                @if($idx > 0)<div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>@endif
                <div style="background:{{ $ph[0] }}10;border:1px solid {{ $ph[0] }}30;border-radius:8px;padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:{{ $ph[0] }}">{{ $ph[1] }}</div>
                @endforeach
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid #1e293b">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">{{ $isFr ? 'Programmes soutenus' : 'Supported programmes' }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:5px">
                        @foreach(['HIV','TB','Malaria','Maternal Health','Immunisation','NCDs'] as $prog)
                        <span style="background:#00C89610;border:1px solid #00C89625;border-radius:12px;padding:2px 9px;font-size:10px;font-weight:600;color:#00C896">{{ $prog }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- DIAGRAM 8: Security Architecture --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#8B5CF615;border:1px solid #8B5CF630;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#8B5CF6;flex-shrink:0">8</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 8' : 'Diagram 8' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Architecture de sécurité' : 'OPES Security Architecture' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                @foreach($isFr
                    ? [['user','var(--text-muted)','Utilisateurs'],['key','#8B5CF6','Authentification & MFA'],['shield','#8B5CF6','Contrôle d\'accès basé sur les rôles'],['layout-dashboard','#1A6FE8','Applications'],['lock','#00C896','Couche de chiffrement'],['database','#1A6FE8','Bases de données'],['file-text','#F59E0B','Journaux d\'audit'],['activity','#EF4444','Surveillance de sécurité']]
                    : [['user','var(--text-muted)','Users'],['key','#8B5CF6','Authentication & MFA'],['shield','#8B5CF6','Role-based access control'],['layout-dashboard','#1A6FE8','Applications'],['lock','#00C896','Encryption layer'],['database','#1A6FE8','Databases'],['file-text','#F59E0B','Audit logging'],['activity','#EF4444','Security monitoring']]
                as $idx => $layer)
                @if($idx > 0)<div style="{{ $arrow }}"><div style="width:1px;height:14px;background:{{ $layer[1] }}40;margin:0 auto;"></div></div>@endif
                <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:{{ $layer[1] }}08;border:1px solid {{ $layer[1] }}25;border-radius:8px">
                    <i data-lucide="{{ $layer[0] }}" style="width:13px;height:13px;color:{{ $layer[1] }};flex-shrink:0"></i>
                    <span style="font-size:11px;font-weight:700;color:{{ $layer[1] }}">{{ $layer[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAMS 9 + 10                                                 --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px">

        {{-- DIAGRAM 9: Disaster Recovery --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#1A6FE8;flex-shrink:0">9</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 9' : 'Diagram 9' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Architecture de reprise après sinistre' : 'Disaster Recovery Architecture' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center">
                    {{-- Primary --}}
                    <div>
                        <div style="text-align:center;font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Site principal' : 'Primary site' }}</div>
                        @foreach($isFr
                            ? [['#00C896','server','Serveurs applicatifs'],['#1A6FE8','database','Base de données principale']]
                            : [['#00C896','server','Application servers'],['#1A6FE8','database','Primary database']]
                        as $idx => $pr)
                        @if($idx > 0)<div style="{{ $arrow }}"><div style="width:1px;height:12px;background:#1e293b;margin:0 auto"></div></div>@endif
                        <div style="background:{{ $pr[0] }}10;border:1px solid {{ $pr[0] }}30;border-radius:7px;padding:9px;text-align:center">
                            <i data-lucide="{{ $pr[1] }}" style="width:14px;height:14px;color:{{ $pr[0] }};display:block;margin:0 auto 4px"></i>
                            <div style="font-size:10px;font-weight:700;color:{{ $pr[0] }}">{{ $pr[2] }}</div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Replication --}}
                    <div style="text-align:center;padding:0 4px">
                        <div style="font-size:10px;color:var(--text-faint);margin-bottom:6px">{{ $isFr ? 'Réplication' : 'Replication' }}</div>
                        <div style="font-size:20px;color:#00C896">⇄</div>
                        <div style="font-size:9px;color:var(--text-faint);margin-top:4px">RPO · RTO</div>
                    </div>
                    {{-- Secondary --}}
                    <div>
                        <div style="text-align:center;font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Site secondaire' : 'Secondary site' }}</div>
                        @foreach($isFr
                            ? [['#1A6FE8','cloud','Site de reprise'],['#F59E0B','hard-drive','Systèmes de sauvegarde'],['#00C896','refresh-cw','Services de récupération']]
                            : [['#1A6FE8','cloud','Recovery site'],['#F59E0B','hard-drive','Backup systems'],['#00C896','refresh-cw','Recovery services']]
                        as $idx => $sec)
                        @if($idx > 0)<div style="{{ $arrow }}"><div style="width:1px;height:12px;background:#1e293b;margin:0 auto"></div></div>@endif
                        <div style="background:{{ $sec[0] }}10;border:1px solid {{ $sec[0] }}30;border-radius:7px;padding:9px;text-align:center">
                            <i data-lucide="{{ $sec[1] }}" style="width:14px;height:14px;color:{{ $sec[0] }};display:block;margin:0 auto 4px"></i>
                            <div style="font-size:10px;font-weight:700;color:{{ $sec[0] }}">{{ $sec[2] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid #1e293b;display:flex;gap:10px;justify-content:center">
                    @foreach(['RPO','RTO','High Availability'] as $target)
                    <span style="background:#00C89610;border:1px solid #00C89625;border-radius:12px;padding:3px 10px;font-size:10px;font-weight:700;color:#00C896">{{ $target }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DIAGRAM 10: Governance Architecture --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:28px;height:28px;border-radius:7px;background:#F59E0B15;border:1px solid #F59E0B30;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#F59E0B;flex-shrink:0">10</div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 10' : 'Diagram 10' }}</div>
                    <div style="font-size:14px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Architecture de gouvernance' : 'Governance Architecture' }}</div>
                </div>
            </div>
            <div style="{{ $card }}">
                <div style="{{ $nodeGov }}font-size:12px;padding:11px">{{ $isFr ? 'Conseil d\'administration' : 'Board of Directors' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeBlue }}font-size:12px;padding:11px">{{ $isFr ? 'Directeur Général (CEO)' : 'Chief Executive Officer' }}</div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $hline }}"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
                    @foreach($isFr
                        ? [['#00C896','Gouvernance clinique'],['#8B5CF6','Gouvernance sécurité'],['#1A6FE8','Conformité'],['#F59E0B','Qualité'],['#EF4444','Risque']]
                        : [['#00C896','Clinical governance'],['#8B5CF6','Security governance'],['#1A6FE8','Compliance'],['#F59E0B','Quality'],['#EF4444','Risk']]
                    as $gov)
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:1px;height:14px;background:#1e293b"></div>
                        <div style="background:{{ $gov[0] }}10;border:1px solid {{ $gov[0] }}30;border-radius:7px;padding:8px;text-align:center;width:100%;font-size:10px;font-weight:700;color:{{ $gov[0] }}">{{ $gov[1] }}</div>
                    </div>
                    @endforeach
                </div>
                <div style="{{ $hline }}margin-top:14px"></div>
                <div style="{{ $arrow }}"><div style="{{ $line }}"></div></div>
                <div style="{{ $nodeTeal }}font-size:11px">{{ $isFr ? 'Produits & Services' : 'Products & Services' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAM 11: OPES Academy Ecosystem                              --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:760px;margin:0 auto">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="width:28px;height:28px;border-radius:7px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#1A6FE8;flex-shrink:0">11</div>
        <div>
            <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 11' : 'Diagram 11' }}</div>
            <div style="font-size:18px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Écosystème OPES Academy' : 'OPES Academy Ecosystem' }}</div>
        </div>
    </div>
    <div style="{{ $card }}">
        <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:20px;align-items:center">
            {{-- Sources --}}
            <div>
                <div style="text-align:center;font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Institutions partenaires' : 'Partner institutions' }}</div>
                @foreach($isFr
                    ? ['Universités','Écoles de soins infirmiers','Facultés de médecine']
                    : ['Universities','Nursing schools','Medical schools']
                as $src)
                <div style="{{ $node }}font-size:10px;padding:7px 10px;margin-bottom:6px">{{ $src }}</div>
                @endforeach
            </div>
            {{-- Academy HUB --}}
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                <div style="font-size:18px;color:#1A6FE8">→</div>
                <div style="background:#1A6FE810;border:2px solid #1A6FE840;border-radius:12px;padding:18px 14px;text-align:center">
                    <i data-lucide="graduation-cap" style="width:22px;height:22px;color:#1A6FE8;display:block;margin:0 auto 8px"></i>
                    <div style="font-size:11px;font-weight:800;color:#1A6FE8">OPES</div>
                    <div style="font-size:11px;font-weight:800;color:#1A6FE8">ACADEMY</div>
                </div>
                <div style="font-size:18px;color:#1A6FE8">→</div>
            </div>
            {{-- Outputs --}}
            <div>
                <div style="text-align:center;font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Rôles certifiés' : 'Certified roles' }}</div>
                @foreach($isFr
                    ? [['#00C896','Utilisateurs certifiés'],['#1A6FE8','Administrateurs certifiés'],['#00C896','Implémenteurs certifiés'],['#1A6FE8','Formateurs certifiés']]
                    : [['#00C896','Certified users'],['#1A6FE8','Certified administrators'],['#00C896','Certified implementers'],['#1A6FE8','Certified trainers']]
                as $role)
                <div style="background:{{ $role[0] }}10;border:1px solid {{ $role[0] }}30;border-radius:7px;padding:7px 10px;text-align:center;font-size:10px;font-weight:700;color:{{ $role[0] }};margin-bottom:6px">{{ $role[1] }}</div>
                @endforeach
            </div>
        </div>
        <div style="{{ $arrow }}margin-top:12px"><div style="{{ $line }}"></div></div>
        <div style="background:#00C89610;border:1px solid #00C89630;border-radius:10px;padding:14px;text-align:center">
            <div style="font-size:13px;font-weight:700;color:#00C896">{{ $isFr ? 'Effectifs de santé numériques' : 'Digital health workforce' }}</div>
            <div style="font-size:10px;color:var(--text-faint);margin-top:4px">{{ $isFr ? 'Adoption · Capacité · Durabilité' : 'Adoption · Capacity · Sustainability' }}</div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- DIAGRAM 12: Complete OPES Health OS Enterprise View             --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#00C896,#1A6FE8);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0">12</div>
        <div>
            <div style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Diagramme 12' : 'Diagram 12' }}</div>
            <div style="font-size:18px;font-weight:700;color:#e2e8f0">{{ $isFr ? 'Vue enterprise complète OPES Health OS' : 'Complete OPES Health OS Enterprise View' }}</div>
        </div>
    </div>
    <div style="{{ $card }}padding:0;overflow:hidden">
        <div style="background:linear-gradient(135deg,#00C89615,#1A6FE815);padding:16px 24px;border-bottom:1px solid #1e293b;text-align:center">
            <div style="font-size:16px;font-weight:800;color:#e2e8f0;letter-spacing:0.05em">OPES HEALTH OS</div>
        </div>
        @foreach($isFr
            ? [['#00C896','Couche clinique',['Clinic','Hospital','Specialty Suite'],'activity'],
               ['#1A6FE8','Couche interopérabilité',['Health ID','Index Patient Maître (MPI)','HIE','Registres'],'share-2'],
               ['#8B5CF6','Couche intelligence',['CDSS','Triage','Analytique','Surveillance'],'cpu'],
               ['#F59E0B','Couche santé publique',['Rapports nationaux','Registres nationaux','Tableaux de bord'],'building-2'],
               ['var(--text-muted)','Couche support',['Academy','Sécurité','Conformité','Qualité','Gouvernance'],'shield']]
            : [['#00C896','Clinical layer',['Clinic','Hospital','Specialty Suite'],'activity'],
               ['#1A6FE8','Interoperability layer',['Health ID','Master Patient Index (MPI)','HIE','Registries'],'share-2'],
               ['#8B5CF6','Intelligence layer',['CDSS','Triage','Analytics','Surveillance'],'cpu'],
               ['#F59E0B','Public health layer',['National reporting','National registries','Dashboards'],'building-2'],
               ['var(--text-muted)','Support layer',['Academy','Security','Compliance','Quality','Governance'],'shield']]
        as $layer)
        <div style="padding:16px 24px;border-bottom:1px solid #0f172a;display:flex;align-items:center;gap:20px">
            <div style="display:flex;align-items:center;gap:8px;min-width:200px">
                <i data-lucide="{{ $layer[3] }}" style="width:14px;height:14px;color:{{ $layer[0] }};flex-shrink:0"></i>
                <span style="font-size:12px;font-weight:700;color:{{ $layer[0] }}">{{ $layer[1] }}</span>
            </div>
            <div style="flex:1;display:flex;flex-wrap:wrap;gap:6px">
                @foreach($layer[2] as $comp)
                <span style="background:{{ $layer[0] }}10;border:1px solid {{ $layer[0] }}25;border-radius:12px;padding:3px 10px;font-size:11px;font-weight:600;color:{{ $layer[0] }}">{{ $comp }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
        <div style="padding:16px 24px;background:linear-gradient(135deg,#00C89610,#1A6FE810);text-align:center">
            <div style="font-size:13px;font-weight:700;background:linear-gradient(135deg,#00C896,#1A6FE8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">
                {{ $isFr ? '→ Écosystème national de santé' : '→ National Health Ecosystem' }}
            </div>
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Prêt à explorer l\'architecture en profondeur ?' : 'Ready to explore the architecture in depth?' }}</h2>
    <p>{{ $isFr
        ? 'Ces diagrammes représentent l\'architecture réelle de la plateforme OPES Health OS. Notre équipe technique peut vous présenter des détails d\'implémentation adaptés à votre contexte.'
        : 'These diagrams represent the actual architecture of the OPES Health OS platform. Our technical team can walk you through implementation details tailored to your context.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/architecture') }}" class="btn-primary">
            {{ $isFr ? 'Documentation architecture' : 'Architecture documentation' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Parler à notre équipe technique' : 'Talk to our technical team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>
