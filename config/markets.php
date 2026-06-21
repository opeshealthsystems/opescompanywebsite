<?php

// CEMAC market landing pages — country-specific marketing data.
// Rendered by App\Http\Controllers\MarketController into resources/views/pages/markets/*.
// Cameroon is the home market (covered by the main site), so it is intentionally not listed here.
// All facts are grounded in the country blog articles (content/articles/66-70) and public knowledge;
// no invented statistics. Health figures (mortality etc.) deliberately live in the blog, not here.

return [

    'gabon' => [
        'slug' => 'gabon',
        'name' => 'Gabon',
        'flag' => '🇬🇦',
        'code' => 'ga',
        'capital' => 'Libreville',
        'cities' => 'Libreville · Port-Gentil',
        'language' => ['en' => 'Francophone', 'fr' => 'Francophone'],
        'currency' => 'XAF (FCFA)',
        'plan' => 'Plan Stratégique Gabon Émergent (PSGE)',
        'facilities' => 'CHU de Libreville (CHUL), Hôpital d\'Instruction des Armées, Port-Gentil',
        'related_blog' => '68-gabon-health-information-system-digital-transformation',
        'accent' => '#00C896',
        'tagline' => [
            'en' => 'Hospital software built for Gabon — CNAM-ready billing, bilingual, and fit for Libreville\'s clinics.',
            'fr' => 'Un logiciel hospitalier conçu pour le Gabon — facturation prête pour la CNAM, bilingue, adapté aux cliniques de Libreville.',
        ],
        'intro' => [
            'en' => 'Gabon has the highest income per capita in the CEMAC zone and, through the CNAM, one of the region\'s most developed universal-health-insurance schemes. That makes clean, electronic billing and accurate records not a luxury but a daily operational requirement for accredited facilities — exactly what OPES Health Systems is built to deliver.',
            'fr' => 'Le Gabon affiche le revenu par habitant le plus élevé de la zone CEMAC et, via la CNAM, l\'un des régimes d\'assurance maladie universelle les plus développés de la région. Une facturation électronique propre et des dossiers précis ne sont donc pas un luxe mais une exigence opérationnelle quotidienne pour les établissements agréés — précisément ce qu\'OPES Health Systems est conçu pour offrir.',
        ],
        'stats' => [
            ['~2.3M', 'Population', 'Population'],
            ['CNAM', 'Universal health insurance', 'Assurance maladie universelle'],
            ['CEMAC', 'Member state', 'État membre'],
            ['EN / FR', 'Bilingual platform', 'Plateforme bilingue'],
        ],
        'driver' => [
            'icon' => 'shield-check',
            'title_en' => 'CNAM-ready billing & claims',
            'title_fr' => 'Facturation & remboursements CNAM',
            'desc_en' => 'The Caisse Nationale d\'Assurance Maladie (CNAM) requires accredited facilities to submit claims with accurate patient identifiers, service codes, and authenticated records. OPES captures these fields at the point of care and generates clean, structured, electronically submittable claims — so Libreville clinics stop leaving revenue on the table to delayed or rejected paper submissions.',
            'desc_fr' => 'La Caisse Nationale d\'Assurance Maladie (CNAM) impose aux établissements agréés des demandes de remboursement avec identifiants patients exacts, codes d\'actes et dossiers authentifiés. OPES capture ces données au point de soin et génère des demandes propres, structurées et transmissibles électroniquement — pour que les cliniques de Libreville cessent de perdre des recettes à cause de soumissions papier retardées ou rejetées.',
        ],
        'context' => [
            'en' => [
                'CNAM accreditation makes electronic, audit-ready billing a practical necessity, not an option.',
                'A centralised health system concentrates specialist capacity in Libreville and Franceville — multi-site coordination matters.',
                'PSGE digital-economy investment (fibre backbone, e-government) creates a favourable environment for health digitisation.',
                'A growing private and parastatal clinic sector serves Gabon\'s professional class, oil sector, and expatriate community.',
            ],
            'fr' => [
                'L\'agrément CNAM rend la facturation électronique et auditable nécessaire, et non optionnelle.',
                'Un système de santé centralisé concentre la capacité spécialisée à Libreville et Franceville — la coordination multi-sites compte.',
                'Les investissements numériques du PSGE (dorsale fibre, e-gouvernement) créent un environnement favorable à la digitalisation de la santé.',
                'Un secteur privé et parapublic croissant sert les cadres gabonais, le secteur pétrolier et la communauté expatriée.',
            ],
        ],
        'fit' => [
            ['receipt', 'CNAM-compatible billing', 'Facturation compatible CNAM', 'Structured claims with the fields CNAM requires, generated as a byproduct of the clinical workflow.', 'Des demandes structurées avec les champs exigés par la CNAM, générées automatiquement par le flux clinique.'],
            ['languages', 'Bilingual EN/FR by design', 'Bilingue EN/FR par conception', 'Every screen, form, and report in French and English — switchable mid-session.', 'Chaque écran, formulaire et rapport en français et en anglais — commutable en cours de session.'],
            ['network', 'Multi-site & interoperable', 'Multi-sites & interopérable', 'One patient record across Libreville and Port-Gentil sites, built on HL7 FHIR with a universal Health ID.', 'Un dossier patient unique entre les sites de Libreville et Port-Gentil, bâti sur HL7 FHIR avec un identifiant de santé universel.'],
            ['bar-chart-2', 'HMIS & MoH reporting', 'Reporting HMIS & ministère', 'Facility data that feeds national reporting cleanly instead of arriving late on paper.', 'Des données d\'établissement qui alimentent proprement le reporting national au lieu d\'arriver tardivement sur papier.'],
        ],
    ],

    'congo-brazzaville' => [
        'slug' => 'congo-brazzaville',
        'name' => 'Republic of Congo',
        'flag' => '🇨🇬',
        'code' => 'cg',
        'capital' => 'Brazzaville',
        'cities' => 'Brazzaville · Pointe-Noire',
        'language' => ['en' => 'Francophone', 'fr' => 'Francophone'],
        'currency' => 'XAF (FCFA)',
        'plan' => 'Plan National de Développement Sanitaire (PNDS)',
        'facilities' => 'CHU de Brazzaville, Hôpital de Base de Talangaï, Pointe-Noire',
        'related_blog' => '67-healthcare-digitization-republic-of-congo-brazzaville',
        'accent' => '#1A6FE8',
        'tagline' => [
            'en' => 'Hospital software for the Republic of Congo — CNSS-ready billing, bilingual, built for Brazzaville and Pointe-Noire.',
            'fr' => 'Un logiciel hospitalier pour la République du Congo — facturation prête pour la CNSS, bilingue, conçu pour Brazzaville et Pointe-Noire.',
        ],
        'intro' => [
            'en' => 'Congo-Brazzaville\'s PNDS makes health-information quality a national priority, and the CNSS is one of the most important structured payers in the system. Facilities that generate clean, auditable billing records submit cleaner CNSS claims and get reimbursed faster — a direct return-on-investment case OPES Health Systems is designed to deliver.',
            'fr' => 'Le PNDS du Congo-Brazzaville fait de la qualité de l\'information sanitaire une priorité nationale, et la CNSS est l\'un des payeurs structurés les plus importants du système. Les établissements qui produisent des enregistrements de facturation propres et auditables soumettent des demandes CNSS plus propres et sont remboursés plus vite — un retour sur investissement direct qu\'OPES Health Systems est conçu pour offrir.',
        ],
        'stats' => [
            ['~5.8M', 'Population', 'Population'],
            ['CNSS', 'Social security payer', 'Payeur sécurité sociale'],
            ['CEMAC', 'Member state', 'État membre'],
            ['EN / FR', 'Bilingual platform', 'Plateforme bilingue'],
        ],
        'driver' => [
            'icon' => 'file-check',
            'title_en' => 'CNSS claims, cleaner and faster',
            'title_fr' => 'Demandes CNSS, plus propres et plus rapides',
            'desc_en' => 'The Caisse Nationale de Sécurité Sociale (CNSS) reimburses medical expenses for covered workers and dependents. Paper billing makes claim submission slow and dispute-prone; OPES generates structured, itemised records — service codes, patient identifiers, dates, prescribing clinician — so private clinics in Brazzaville submit clean claims and receive timely reimbursement.',
            'desc_fr' => 'La Caisse Nationale de Sécurité Sociale (CNSS) rembourse les frais médicaux des travailleurs couverts et de leurs ayants droit. La facturation papier rend la soumission lente et sujette aux litiges ; OPES génère des enregistrements structurés et détaillés — codes d\'actes, identifiants patients, dates, médecin prescripteur — pour que les cliniques de Brazzaville soumettent des demandes propres et soient remboursées à temps.',
        ],
        'context' => [
            'en' => [
                'The PNDS provides a policy mandate for digitisation — facilities aligned to it are well placed for government or donor co-financing.',
                'CNSS-insured patients are a significant share of the private-clinic base in Brazzaville; clean billing is a direct revenue lever.',
                'DHIS2 is deployed nationally — a working foundation that facility-level tools can build upon.',
                'World Bank and AFD health-system-strengthening projects create procurement pathways for HMS deployment.',
            ],
            'fr' => [
                'Le PNDS offre un mandat politique pour la digitalisation — les établissements alignés sont bien placés pour un cofinancement public ou des bailleurs.',
                'Les patients assurés CNSS représentent une part importante de la patientèle des cliniques privées de Brazzaville ; une facturation propre est un levier de recettes direct.',
                'DHIS2 est déployé au niveau national — une base fonctionnelle sur laquelle les outils au niveau établissement peuvent s\'appuyer.',
                'Les projets de renforcement du système de santé de la Banque mondiale et de l\'AFD créent des voies de marché pour le déploiement d\'un SIH.',
            ],
        ],
        'fit' => [
            ['receipt', 'CNSS-ready billing', 'Facturation prête pour la CNSS', 'Itemised, auditable claim records that cut rejections and speed CNSS reimbursement.', 'Des enregistrements de demandes détaillés et auditables qui réduisent les rejets et accélèrent le remboursement CNSS.'],
            ['languages', 'Bilingual EN/FR by design', 'Bilingue EN/FR par conception', 'Full French-first operation with English available throughout.', 'Une exploitation entièrement en français avec l\'anglais disponible partout.'],
            ['arrow-left-right', 'DHIS2 & HMIS ready', 'Prêt pour DHIS2 & HMIS', 'Facility data that aggregates cleanly into the national platform already in use.', 'Des données d\'établissement qui s\'agrègent proprement dans la plateforme nationale déjà en place.'],
            ['network', 'Brazzaville–Pointe-Noire continuity', 'Continuité Brazzaville–Pointe-Noire', 'One connected record across sites via a universal Health ID on HL7 FHIR.', 'Un dossier connecté unique entre les sites via un identifiant de santé universel sur HL7 FHIR.'],
        ],
    ],

    'chad' => [
        'slug' => 'chad',
        'name' => 'Chad',
        'flag' => '🇹🇩',
        'code' => 'td',
        'capital' => "N'Djamena",
        'cities' => "N'Djamena · Moundou · Abéché",
        'language' => ['en' => 'French & Arabic', 'fr' => 'Français & arabe'],
        'currency' => 'XAF (FCFA)',
        'plan' => 'Plan Quinquennal de Développement Sanitaire (PQDS)',
        'facilities' => "Hôpital Général de Référence Nationale (HGRN), Hôpital de la Mère et de l'Enfant",
        'related_blog' => '66-digital-health-chad-hospital-management-system',
        'accent' => '#1A6FE8',
        'tagline' => [
            'en' => 'Hospital software that works in Chad — offline-first, mobile-money-ready, and aligned to the PQDS.',
            'fr' => 'Un logiciel hospitalier qui fonctionne au Tchad — hors-ligne d\'abord, prêt pour le mobile money, aligné sur le PQDS.',
        ],
        'intro' => [
            'en' => 'Chad\'s facilities operate under some of the toughest infrastructure constraints anywhere — limited connectivity and electricity make offline-first software a prerequisite, not a preference. OPES Health Systems is designed to keep working when the link drops and synchronise when it returns, while feeding the HMIS data the Ministry of Public Health needs.',
            'fr' => 'Les établissements du Tchad fonctionnent sous certaines des contraintes d\'infrastructure les plus dures qui soient — une connectivité et une électricité limitées font du logiciel hors-ligne d\'abord un prérequis, non une préférence. OPES Health Systems est conçu pour continuer à fonctionner quand la connexion tombe et se synchroniser à son retour, tout en alimentant les données HMIS dont le ministère de la Santé publique a besoin.',
        ],
        'stats' => [
            ['~17M', 'Population', 'Population'],
            ['PQDS', 'National health plan', 'Plan national de santé'],
            ['CEMAC', 'Member state', 'État membre'],
            ['EN / FR', 'Bilingual platform', 'Plateforme bilingue'],
        ],
        'driver' => [
            'icon' => 'wifi-off',
            'title_en' => 'Offline-first by design',
            'title_fr' => 'Hors-ligne d\'abord par conception',
            'desc_en' => 'With national electrification near 8–10% and connectivity concentrated in N\'Djamena, any system deployed in Chad must function without a continuous internet connection. OPES works offline and synchronises when connectivity returns — and pairs digital billing with Airtel Money and Moov Money confirmations, replacing fragile manual cash-handling.',
            'desc_fr' => 'Avec une électrification nationale proche de 8–10 % et une connectivité concentrée à N\'Djamena, tout système déployé au Tchad doit fonctionner sans connexion internet continue. OPES fonctionne hors ligne et se synchronise au retour de la connectivité — et associe la facturation numérique aux confirmations Airtel Money et Moov Money, remplaçant la manipulation manuelle d\'espèces.',
        ],
        'context' => [
            'en' => [
                'Connectivity and electricity constraints make offline-first architecture essential outside N\'Djamena.',
                'Paper records generate incomplete, delayed HMIS data; digital tools that feed DHIS2/HMIS are positioned as prerequisites for planning.',
                'Mobile money (Airtel Money, Moov Money) is the practical rail for digital billing where banking infrastructure is thin.',
                'Donor priorities (Global Fund, USAID, World Bank, EU) reward data quality, UHC, and supply-chain visibility.',
            ],
            'fr' => [
                'Les contraintes de connectivité et d\'électricité rendent l\'architecture hors-ligne d\'abord essentielle hors de N\'Djamena.',
                'Les dossiers papier génèrent des données HMIS incomplètes et tardives ; les outils numériques qui alimentent DHIS2/HMIS sont des prérequis à la planification.',
                'Le mobile money (Airtel Money, Moov Money) est le canal pratique de la facturation numérique là où l\'infrastructure bancaire est limitée.',
                'Les priorités des bailleurs (Fonds mondial, USAID, Banque mondiale, UE) récompensent la qualité des données, la CSU et la visibilité de la chaîne d\'approvisionnement.',
            ],
        ],
        'fit' => [
            ['wifi-off', 'Offline-first operation', 'Fonctionnement hors-ligne d\'abord', 'Keeps running without internet and syncs when connectivity returns — built for low-power, low-connectivity sites.', 'Continue de fonctionner sans internet et se synchronise au retour de la connexion — conçu pour les sites à faible énergie et faible connectivité.'],
            ['smartphone', 'Mobile-money billing', 'Facturation mobile money', 'Invoices tied to Airtel Money and Moov Money confirmations instead of manual cash handling.', 'Des factures liées aux confirmations Airtel Money et Moov Money plutôt qu\'à la manipulation d\'espèces.'],
            ['bar-chart-2', 'HMIS / DHIS2 reporting', 'Reporting HMIS / DHIS2', 'Clean facility data that aggregates into national reporting the MoH and donors require.', 'Des données d\'établissement propres qui s\'agrègent dans le reporting national exigé par le ministère et les bailleurs.'],
            ['languages', 'Bilingual EN/FR', 'Bilingue EN/FR', 'French-first operation suited to Chad\'s administrative language, English available throughout.', 'Une exploitation en français adaptée à la langue administrative du Tchad, l\'anglais disponible partout.'],
        ],
    ],

    'central-african-republic' => [
        'slug' => 'central-african-republic',
        'name' => 'Central African Republic',
        'flag' => '🇨🇫',
        'code' => 'cf',
        'capital' => 'Bangui',
        'cities' => 'Bangui',
        'language' => ['en' => 'French & Sango', 'fr' => 'Français & sango'],
        'currency' => 'XAF (FCFA)',
        'plan' => 'National health strategy (MoH, WHO-supported)',
        'facilities' => "Hôpital de l'Amitié (Bangui), Hôpital de Pédiatrie de Bangui",
        'related_blog' => '69-central-african-republic-health-technology-challenges',
        'accent' => '#00C896',
        'tagline' => [
            'en' => 'Resilient hospital software for the Central African Republic — offline-first, interoperable, and realistic about fragile settings.',
            'fr' => 'Un logiciel hospitalier résilient pour la République centrafricaine — hors-ligne d\'abord, interopérable et réaliste face aux contextes fragiles.',
        ],
        'intro' => [
            'en' => 'The Central African Republic is one of the world\'s most challenging health environments, where much of the clinical workload is carried by NGO-supported facilities. The realistic entry point for digital health is software that runs offline, interoperates with the tools humanitarian actors already use, and respects tight, cyclical budgets — which is how OPES Health Systems approaches fragile settings.',
            'fr' => 'La République centrafricaine est l\'un des environnements de santé les plus difficiles au monde, où une grande partie de la charge clinique est portée par des établissements appuyés par des ONG. Le point d\'entrée réaliste pour la santé numérique est un logiciel qui fonctionne hors ligne, interagit avec les outils déjà utilisés par les acteurs humanitaires et respecte des budgets serrés et cycliques — c\'est l\'approche d\'OPES Health Systems face aux contextes fragiles.',
        ],
        'stats' => [
            ['~5M', 'Population', 'Population'],
            ['NGO-led', 'Care delivery', 'Prestation de soins'],
            ['CEMAC', 'Member state', 'État membre'],
            ['EN / FR', 'Bilingual platform', 'Plateforme bilingue'],
        ],
        'driver' => [
            'icon' => 'shield',
            'title_en' => 'Built for fragile, low-resource settings',
            'title_fr' => 'Conçu pour les contextes fragiles et à faibles ressources',
            'desc_en' => 'Across much of CAR, NGOs are the primary care providers and tools must run on low-cost hardware in offline or low-connectivity conditions. OPES is designed to deploy in those conditions and to interoperate — via HL7 FHIR and DHIS2 — with the platforms humanitarian actors and the Ministry already use, rather than forcing a rip-and-replace.',
            'desc_fr' => 'Dans une grande partie de la RCA, les ONG sont les principaux prestataires de soins et les outils doivent fonctionner sur du matériel à faible coût en conditions hors ligne ou de faible connectivité. OPES est conçu pour se déployer dans ces conditions et pour interagir — via HL7 FHIR et DHIS2 — avec les plateformes déjà utilisées par les acteurs humanitaires et le ministère, sans imposer un remplacement total.',
        ],
        'context' => [
            'en' => [
                'NGO-managed and NGO-supported facilities are the most practical entry points for digital health adoption.',
                'Offline / low-connectivity operation on low-cost hardware is a hard requirement, not a nice-to-have.',
                'Interoperability with OpenMRS, DHIS2, and FHIR-based tools matters more than displacing them.',
                'WHO, UNICEF, UNFPA, and USAID coordinate much of the health response — alignment opens doors.',
            ],
            'fr' => [
                'Les établissements gérés ou appuyés par des ONG sont les points d\'entrée les plus pratiques pour l\'adoption de la santé numérique.',
                'Le fonctionnement hors ligne / faible connectivité sur matériel à faible coût est une exigence stricte, pas un bonus.',
                'L\'interopérabilité avec OpenMRS, DHIS2 et les outils basés sur FHIR compte plus que de les remplacer.',
                'L\'OMS, l\'UNICEF, l\'UNFPA et l\'USAID coordonnent une grande part de la réponse sanitaire — l\'alignement ouvre des portes.',
            ],
        ],
        'fit' => [
            ['wifi-off', 'Offline & low-resource', 'Hors-ligne & faibles ressources', 'Runs on modest hardware without continuous connectivity and syncs when links return.', 'Fonctionne sur du matériel modeste sans connectivité continue et se synchronise au retour des liens.'],
            ['arrow-left-right', 'Interoperable, not disruptive', 'Interopérable, non disruptif', 'Exchanges data with DHIS2 and FHIR-based tools humanitarian actors already rely on.', 'Échange des données avec DHIS2 et les outils FHIR sur lesquels les acteurs humanitaires s\'appuient déjà.'],
            ['languages', 'Bilingual EN/FR', 'Bilingue EN/FR', 'French-first interface for CAR\'s administrative language, English for international partners.', 'Interface en français pour la langue administrative de la RCA, anglais pour les partenaires internationaux.'],
            ['heart-pulse', 'Programme-focused workflows', 'Flux axés sur les programmes', 'Strong fit for maternal health, HIV, TB, and nutrition programmes that dominate clinical activity.', 'Bien adapté aux programmes de santé maternelle, VIH, tuberculose et nutrition qui dominent l\'activité clinique.'],
        ],
    ],

    'equatorial-guinea' => [
        'slug' => 'equatorial-guinea',
        'name' => 'Equatorial Guinea',
        'flag' => '🇬🇶',
        'code' => 'gq',
        'capital' => 'Malabo',
        'cities' => 'Malabo · Bata',
        'language' => ['en' => 'Spanish (with French & Portuguese)', 'fr' => 'Espagnol (avec français & portugais)'],
        'currency' => 'XAF (FCFA)',
        'plan' => 'Ministry of Health & Social Welfare strategy',
        'facilities' => 'Hospital Regional de Malabo, Hospital Regional de Bata',
        'related_blog' => '70-equatorial-guinea-digital-health-hospital-management',
        'accent' => '#1A6FE8',
        'tagline' => [
            'en' => 'Hospital software for Equatorial Guinea — INSESO-ready records for Malabo and Bata\'s clinics, with CEMAC interoperability.',
            'fr' => 'Un logiciel hospitalier pour la Guinée équatoriale — des dossiers prêts pour l\'INSESO pour les cliniques de Malabo et Bata, avec l\'interopérabilité CEMAC.',
        ],
        'intro' => [
            'en' => 'Equatorial Guinea pairs high oil-driven income with a small, twin-centre health system across Malabo (Bioko Island) and Bata (mainland). Its most immediately addressable segment is the private and oil-sector clinics serving professionals and expatriates — facilities with the resources and the INSESO documentation needs that make operational software a clear win. (Note: Spanish is the primary language; OPES operates in English and French, well suited to the international and CEMAC-facing segment.)',
            'fr' => 'La Guinée équatoriale associe un revenu élevé tiré du pétrole à un petit système de santé bicéphale entre Malabo (île de Bioko) et Bata (continent). Son segment le plus immédiatement adressable est celui des cliniques privées et du secteur pétrolier servant cadres et expatriés — des établissements dotés des ressources et des besoins de documentation INSESO qui font d\'un logiciel opérationnel un gain évident. (Note : l\'espagnol est la langue principale ; OPES fonctionne en anglais et en français, bien adapté au segment international et tourné vers la CEMAC.)',
        ],
        'stats' => [
            ['~1.5M', 'Population', 'Population'],
            ['INSESO', 'Social security payer', 'Payeur sécurité sociale'],
            ['CEMAC', 'Member state', 'État membre'],
            ['EN / FR', 'Bilingual platform', 'Plateforme bilingue'],
        ],
        'driver' => [
            'icon' => 'building-2',
            'title_en' => 'Private & oil-sector clinics first',
            'title_fr' => 'Cliniques privées & du secteur pétrolier d\'abord',
            'desc_en' => 'The clearest market is Malabo and Bata\'s private clinics serving oil-company staff, consultants, and the diplomatic community — facilities that combine financial resources, management sophistication, and INSESO reimbursement needs. Accurate records of eligibility, services, and charges are exactly what INSESO accreditation requires, and what OPES generates as a byproduct of its workflow.',
            'desc_fr' => 'Le marché le plus clair est celui des cliniques privées de Malabo et Bata servant le personnel des compagnies pétrolières, les consultants et la communauté diplomatique — des établissements alliant ressources financières, sophistication de gestion et besoins de remboursement INSESO. Des dossiers précis d\'éligibilité, d\'actes et de charges sont exactement ce qu\'exige l\'agrément INSESO, et ce qu\'OPES génère automatiquement par son flux de travail.',
        ],
        'context' => [
            'en' => [
                'Private clinics serving the oil and expatriate sector are the most immediately accessible market segment.',
                'INSESO accreditation and reimbursement require accurate eligibility, service, and charge records.',
                'A twin-centre geography (Malabo island, Bata mainland) makes connected, multi-site records valuable.',
                'Verifiable quality data can help rebuild domestic confidence and reduce costly medical-tourism outflows.',
            ],
            'fr' => [
                'Les cliniques privées servant le secteur pétrolier et expatrié sont le segment de marché le plus immédiatement accessible.',
                'L\'agrément et le remboursement INSESO exigent des dossiers précis d\'éligibilité, d\'actes et de charges.',
                'Une géographie bicéphale (île de Malabo, continent de Bata) rend précieux des dossiers connectés et multi-sites.',
                'Des données de qualité vérifiables peuvent aider à restaurer la confiance domestique et réduire les coûteux flux de tourisme médical.',
            ],
        ],
        'fit' => [
            ['receipt', 'INSESO-ready records', 'Dossiers prêts pour l\'INSESO', 'Accurate eligibility, service, and charge records that streamline INSESO accreditation and claims.', 'Des dossiers précis d\'éligibilité, d\'actes et de charges qui simplifient l\'agrément et les demandes INSESO.'],
            ['network', 'Malabo–Bata continuity', 'Continuité Malabo–Bata', 'One connected record across the island and mainland centres via a universal Health ID.', 'Un dossier connecté unique entre les centres insulaire et continental via un identifiant de santé universel.'],
            ['languages', 'EN/FR for the international segment', 'EN/FR pour le segment international', 'English and French operation suited to oil-sector, expatriate, and CEMAC-facing facilities.', 'Une exploitation en anglais et français adaptée aux établissements du secteur pétrolier, expatriés et tournés vers la CEMAC.'],
            ['shield-check', 'Verifiable quality data', 'Données de qualité vérifiables', 'Systematic outcome and process data that helps build domestic confidence in local facilities.', 'Des données systématiques de résultats et de processus qui aident à bâtir la confiance dans les établissements locaux.'],
        ],
    ],

];
