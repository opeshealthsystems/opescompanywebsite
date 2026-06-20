# HL7 et FHIR en Afrique : pourquoi les normes d'interopérabilité des TI de santé comptent pour les hôpitaux camerounais

**Meta Description:** Découvrez comment les normes d'interopérabilité HL7 et FHIR s'appliquent aux hôpitaux africains, pourquoi elles comptent pour le système de santé du Cameroun et ce qu'une mise en œuvre pratique de SGH devrait prendre en charge aujourd'hui.

**Target Keywords:** HL7 FHIR Afrique, normes d'interopérabilité santé Cameroun, API FHIR hôpital Afrique, OpenHIE Cameroun, échange de données de santé Afrique, interopérabilité SGH CEMAC

---

## Qu'est-ce que HL7 et qu'est-ce que FHIR ?

HL7 — Health Level 7 — est une organisation internationale de normalisation fondée en 1987 qui élabore des cadres et des normes pour l'échange, l'intégration, le partage et la récupération de l'information de santé électronique. Le « Level 7 » fait référence à la septième couche du modèle de communication OSI (la couche application), indiquant que les normes HL7 fonctionnent au niveau de l'application de santé, et non de l'infrastructure réseau sous-jacente.

FHIR — Fast Healthcare Interoperability Resources, prononcé « fire » — est la norme la plus récente et la plus largement adoptée de HL7, publiée dans sa forme stable actuelle en tant que FHIR R4 en 2019. FHIR définit un ensemble de blocs de construction modulaires appelés « ressources » — Patient, Observation, Encounter, MedicationRequest, DiagnosticReport et environ 150 autres — qui représentent des concepts cliniques distincts. Chaque ressource a une structure définie, un ensemble standard de champs de données, et peut être échangée via des protocoles web standard (REST, JSON, XML). FHIR est conçu pour être implémentable par des développeurs web modernes sans connaissances spécialisées en TI de santé, ce qui a considérablement accéléré son adoption à l'échelle mondiale.

## Pourquoi l'interopérabilité compte pour la prestation de soins

L'interopérabilité — la capacité de différents systèmes d'information sanitaire à échanger et utiliser des données — n'est pas un raffinement technique. Elle a des conséquences directes sur la sécurité des patients et la qualité des soins.

Lorsqu'un patient se déplace entre un centre de santé communautaire, un hôpital de district et un centre de référence, son historique clinique devrait se déplacer avec lui. Sans interopérabilité, l'établissement d'accueil repart de zéro : il répète les examens, reprend l'anamnèse, court le risque d'interactions médicamenteuses dues à des prescriptions antérieures inconnues. Au Cameroun, où les chaînes de référencement relient les centres périphériques, les hôpitaux de district, les hôpitaux régionaux et les deux centres hospitaliers universitaires centraux (CHU de Yaoundé et CHU de Douala), l'absence d'échange électronique de données signifie que les lettres de référencement papier — souvent incomplètes, parfois perdues — sont le seul mécanisme de transfert d'information.

Au niveau national, l'interopérabilité permet aux dossiers individuels des établissements de contribuer aux bases de données de santé des populations comme DHIS2, aux registres nationaux de patients, aux systèmes de surveillance des maladies et aux plateformes de gestion de la chaîne d'approvisionnement — sans double saisie de données à chaque niveau.

## HL7 v2 vs HL7 v3 vs FHIR R4 : différences pratiques

Comprendre quelle version de HL7 est pertinente nécessite une brève orientation :

| Norme | Époque | Format | Adoption en Afrique | Usage clé |
|---|---|---|---|---|
| HL7 v2 | 1989–présent | Messages texte délimités par des barres verticales | Largement utilisée dans les systèmes de laboratoire, les HIS hérités | Transmission des résultats de laboratoire, messagerie ADT (Admissions, Sortie, Transfert) |
| HL7 v3 | 2005–années 2010 | Basé sur XML, très complexe | Très limitée | Documents CDA (comptes rendus de sortie, lettres de référencement) |
| FHIR R4 | 2019–présent | API REST, ressources JSON/XML | En forte croissance, notamment dans les programmes financés par les bailleurs | Échange moderne de données de santé, plateformes nationales d'échange d'informations sanitaires (HIE) |

À des fins pratiques en Afrique : HL7 v2 est encore largement utilisée par les analyseurs de laboratoire et les systèmes hérités pour transmettre les résultats ; HL7 v3 est largement contournée au profit de FHIR ; et FHIR R4 est la norme cible actuelle pour les nouvelles mises en œuvre et les programmes de santé numérique financés par les bailleurs.

## Cas d'usage de HL7 et FHIR au Cameroun et dans la région CEMAC

Plusieurs cas d'usage concrets illustrent pourquoi ces normes comptent pour les hôpitaux camerounais aujourd'hui et dans un avenir proche :

### Partage des résultats de laboratoire entre établissements
L'échantillon de sang d'un patient est traité dans un laboratoire de référence à Yaoundé. À l'aide des messages de résultats de laboratoire HL7 v2 ou des ressources FHIR DiagnosticReport, le résultat est renvoyé électroniquement à l'établissement prescripteur de Bafoussam — immédiatement, avec exactitude et sans risque d'erreur de transcription liée à un appel téléphonique ou à un fax papier.

### Référencement électronique et résumé du patient
Lorsqu'un hôpital de district réfère un patient à un hôpital régional, le référencement pourrait inclure un document FHIR structuré — données démographiques du patient, diagnostics actifs, médicaments en cours, résultats d'examens récents — que le SGH de l'établissement d'accueil peut importer directement dans le nouveau dossier patient.

### Soumission de données structurées à DHIS2
DHIS2 lui-même utilise une API REST avec des charges utiles JSON pour l'importation de données. Bien qu'il ne soit pas strictement conforme à FHIR, l'architecture de DHIS2 est conceptuellement alignée sur l'approche REST-et-JSON qu'utilise FHIR R4, ce qui rend le pont technique simple pour les systèmes qui mettent déjà en œuvre FHIR.

### Appariement national de l'identité des patients
Un identifiant patient unique — relié entre les établissements via un registre national des clients (Client Registry) utilisant la ressource HL7 FHIR Patient — permettrait à tout établissement participant de récupérer les dossiers antérieurs d'un patient en interrogeant le registre central. C'est la vision à long terme ; l'infrastructure est construite progressivement dans plusieurs pays africains.

### Rapport des programmes financés par les bailleurs
Les programmes financés par le PEPFAR, le Fonds mondial et GAVI spécifient de plus en plus la soumission de données conformes à FHIR comme exigence pour les outils de santé numérique acquis dans le cadre de leurs programmes. Les fournisseurs de SGH cherchant à participer à ces marchés doivent démontrer une capacité FHIR.

## OpenHIE : le cadre architectural pour l'échange d'informations sanitaires en Afrique

OpenHIE — l'Open Health Information Exchange — est une communauté collaborative qui définit une architecture de référence pour l'échange d'informations sanitaires à l'échelle nationale, largement adoptée à travers l'Afrique subsaharienne. L'architecture OpenHIE définit un ensemble de composants en interaction :

- **Couche d'interopérabilité (IL)** — un médiateur central d'information sanitaire (généralement OpenHIM) qui achemine, transforme et valide les messages entre les systèmes
- **Dossier de santé partagé (SHR)** — un référentiel de dossiers de santé longitudinaux des patients
- **Registre des clients (CR)** — un index maître des patients qui attribue et résout les identifiants uniques des patients
- **Registre des établissements (FR)** — une liste maîtresse de tous les établissements de santé avec leurs identifiants
- **Service de terminologie** — un référentiel de codes cliniques normalisés (ICD-10, SNOMED, LOINC)
- **Système d'information de gestion sanitaire (HMIS)** — généralement DHIS2

L'architecture nationale de santé numérique du Cameroun, en cours d'élaboration avec le soutien de partenaires internationaux, s'appuie sur les principes d'OpenHIE. Les établissements de santé qui mettent en œuvre des plateformes SGH conformes à FHIR seront positionnés pour se connecter à cette architecture à mesure qu'elle arrivera à maturité — sans nécessiter un remplacement de système.

## Obstacles à l'adoption de HL7/FHIR dans la région CEMAC

Malgré la valeur évidente des normes d'interopérabilité, leur adoption au Cameroun et dans la région CEMAC plus large se heurte à de réels obstacles :

**Capacité technique** — la mise en œuvre des API FHIR nécessite des développeurs de logiciels dotés de compétences spécifiques. De nombreux fournisseurs de SGH locaux manquent d'expertise FHIR en interne, et le vivier de professionnels de l'informatique de santé au Cameroun est restreint. Le développement de cette capacité demande du temps et des investissements.

**Coût** — la mise en œuvre et la maintenance d'interfaces conformes aux normes alourdissent les coûts de développement et d'hébergement. Pour les petits établissements ou les petits fournisseurs de SGH, le coût supplémentaire peut être difficile à justifier à court terme.

**Absence d'un HIE national auquel se connecter** — les normes d'interopérabilité apportent une valeur maximale lorsqu'il existe un échange d'informations sanitaires fonctionnel auquel se connecter. Là où cette infrastructure n'existe pas encore (ou n'existe que partiellement), l'incitation à investir dans la conformité FHIR est plus faible.

**Lacunes de gouvernance des données** — le partage des données patient entre établissements nécessite des cadres juridiques clairs pour le consentement du patient, la propriété des données et la protection de la vie privée. Le paysage de la protection des données au Cameroun évolue, et les incertitudes peuvent ralentir le partage de données entre établissements même là où la capacité technique existe.

**Connectivité** — l'échange de données fondé sur des normes via des API REST nécessite une connexion internet fiable, qui reste inconstante à travers le Cameroun, en particulier en dehors des grands centres urbains.

## Ce qu'un SGH camerounais doit prendre en charge aujourd'hui : minimum pratique

Compte tenu des obstacles ci-dessus, quel est le minimum réaliste qu'un SGH camerounais devrait prendre en charge dès maintenant, avec une trajectoire crédible vers une interopérabilité plus complète ?

Le minimum pratique comprend :

- **Saisie de données structurées avec des codes standard** — ICD-10 pour les diagnostics, codes standard des tests de laboratoire, noms standard des médicaments. Sans données codées structurées à la source, aucune capacité d'API ne produit de sortie interopérable utile.
- **Intégration de l'API DHIS2** — l'interopérabilité la plus immédiatement utile au Cameroun aujourd'hui, avec un système national en activité auquel se connecter.
- **Export de données dans des formats standard** — la capacité d'exporter les données patient dans des formats JSON ou XML compatibles avec les ressources FHIR, même si une API FHIR en activité n'est pas encore maintenue.
- **Identification unique du patient** — attribuer et préserver un identifiant patient cohérent au sein de l'établissement, en tant que bloc de construction pour le futur appariement national de l'identité des patients.
- **API documentée** — un SGH qui fournit une API documentée permet une intégration future avec les composants du HIE national à mesure qu'ils sont déployés, sans nécessiter le remplacement du SGH.

## Exigences en matière d'API FHIR pour les programmes financés par les bailleurs

Les hôpitaux participant aux programmes VIH financés par le PEPFAR, aux subventions TB ou paludisme du Fonds mondial, ou aux programmes de vaccination soutenus par GAVI font de plus en plus face à des exigences de leurs partenaires de mise en œuvre des programmes en matière de soumission électronique de données. Le cadre MER (Monitoring, Evaluation and Reporting) du PEPFAR et les orientations sur les systèmes d'information du Fonds mondial font tous deux référence à FHIR comme norme cible pour l'échange de données.

Pour les établissements camerounais inscrits dans des programmes financés par les bailleurs, cela crée une incitation pratique : un SGH capable de produire des soumissions de données au format FHIR est de plus en plus un prérequis à la participation aux programmes, ou à tout le moins simplifie considérablement la charge de rapport de conformité.

## Comment le SGH OPES aborde l'interopérabilité

Le système de gestion hospitalière OPES est conçu avec l'interopérabilité comme principe architectural central, et non comme une considération secondaire. Le modèle de données OPES utilise des diagnostics codés en ICD-10, une dénomination standardisée des médicaments et des champs démographiques structurés — le prérequis de tout échange de données significatif. Le module d'intégration DHIS2 fournit l'interopérabilité la plus immédiatement pertinente pour les exigences nationales de rapport du Cameroun.

OPES maintient une API interne documentée qui permet l'intégration avec des systèmes externes, y compris les instruments de laboratoire transmettant des résultats via des messages HL7 v2, ainsi que des plateformes tierces d'analytique ou de gestion de programmes. La feuille de route d'OPES inclut des points de terminaison de ressources FHIR R4 pour les données de patient, de consultation et de compte rendu diagnostique — permettant la connexion à l'infrastructure nationale de HIE à mesure que l'architecture de santé numérique du Cameroun arrive à maturité.

Pour les directeurs d'hôpitaux évaluant les options de SGH, la bonne question à propos de l'interopérabilité n'est pas de savoir si un système est « conforme à FHIR » dans l'abstrait, mais s'il utilise des données structurées et codées au point de soins, s'il peut se connecter à DHIS2 dès aujourd'hui, et si son architecture permet une intégration future sans remplacement de système. OPES est conçu pour répondre à ces trois critères — offrant aux établissements camerounais une trajectoire crédible des exigences de rapport d'aujourd'hui vers le système de santé interopérable de demain.
