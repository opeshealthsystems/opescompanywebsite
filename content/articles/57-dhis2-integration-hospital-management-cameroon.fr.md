# Intégration DHIS2 : comment les systèmes de gestion hospitalière alimentent la base de données nationale de santé du Cameroun

**Meta Description:** Comprenez comment fonctionne l'intégration SGH-vers-DHIS2 au Cameroun, quels indicateurs de santé sont rapportés, comment le MINSANTE utilise les données et comment le rapport automatisé remplace les retours mensuels manuels.

**Target Keywords:** intégration DHIS2 Cameroun, système de gestion hospitalière DHIS2, rapport de données de santé MINSANTE, API DHIS2 SGH, système d'information sanitaire Cameroun, base de données nationale de santé Afrique

---

## Qu'est-ce que DHIS2 ?

DHIS2 — le District Health Information Software 2 — est une plateforme d'information sanitaire open source développée par le réseau HISP (Health Information Systems Programme), basé à l'Université d'Oslo. C'est le plus grand système d'information de gestion sanitaire au monde, déployé dans plus de 100 pays pour agréger, analyser et visualiser les données de routine des établissements de santé aux niveaux du district, de la région et du pays.

DHIS2 n'est pas un système clinique : il ne stocke pas les dossiers individuels des patients. Il agrège plutôt les données en indicateurs récapitulatifs — décomptes mensuels des consultations externes, des consultations prénatales, des accouchements, des vaccinations administrées, des diagnostics de paludisme et d'autres métriques similaires — qui permettent aux autorités sanitaires de surveiller les tendances de santé des populations, de suivre la performance des programmes et d'allouer les ressources. Son interface web, sa bibliothèque d'indicateurs personnalisable et son API ouverte en ont fait la plateforme standard pour la gestion de l'information sanitaire de routine à travers l'Afrique subsaharienne.

## Le déploiement national de DHIS2 au Cameroun sous l'égide du MINSANTE

Le Ministère de la Santé Publique du Cameroun (MINSANTE) a adopté DHIS2 comme plateforme nationale de Système d'Information de Gestion Sanitaire (SIGS/HMIS) avec le soutien de l'OMS, du PEPFAR et du Fonds mondial au début des années 2010. Le système est accessible via le portail national d'information sanitaire et est utilisé par les bureaux de santé de district (DSP — Délégations de Santé de Province) et les délégations régionales de santé (DRS) pour compiler les rapports mensuels des établissements soumis par les structures de santé à travers le pays.

Dans le cadre du dispositif de rapport obligatoire du Cameroun, chaque établissement de santé accrédité — hôpital de district public, clinique privée, centre de santé confessionnel ou hôpital de mission — est tenu de soumettre un retour agrégé mensuel couvrant un ensemble défini d'indicateurs de santé à son bureau de santé de district de tutelle. Ces retours constituent les données d'entrée de DHIS2. Les données agrégées au niveau du district sont ensuite utilisées par le MINSANTE pour produire les statistiques sanitaires nationales, éclairer le plan national de santé et rendre compte aux bailleurs et partenaires internationaux.

## Pourquoi le rapport manuel vers DHIS2 échoue

Dans le modèle opérationnel standard actuel, un point focal des données dans chaque établissement de santé compte manuellement les marques de pointage des registres papier — le registre des consultations externes, le registre de CPN, le registre des accouchements, la carte de vaccination — à la fin de chaque mois, transcrit les décomptes sur le formulaire de rapport mensuel (Rapport Mensuel d'Activités — RMA), et soit soumet le formulaire papier au bureau de santé de district, soit saisit directement les chiffres dans DHIS2 via le formulaire de saisie de données de l'établissement.

Ce processus manuel engendre des problèmes bien documentés. Des erreurs de transcription surviennent à chaque étape — du pointage au formulaire, et du formulaire à la saisie dans DHIS2. Les établissements à forts volumes de patients trouvent pratiquement impossible de compter avec exactitude à partir des registres de pointage, surtout si la gestion des registres a été incohérente au cours du mois. La soumission des données est fréquemment tardive : le MINSANTE et ses partenaires rapportent régulièrement des taux d'exhaustivité des rapports dans les délais de 60 à 80 pour cent à l'échelle nationale, les établissements ruraux étant souvent en dessous de 50 pour cent. La correction rétrospective des données soumises est fastidieuse, de sorte que les erreurs persistent dans la base de données nationale.

Peut-être plus important encore, le processus manuel rompt le lien entre le dossier clinique individuel et l'indicateur agrégé. Si le diagnostic d'un patient n'est pas enregistré dans le registre des consultations externes, ou si le registre utilise une catégorie diagnostique non standard, le cas peut ne pas être comptabilisé dans le bon indicateur DHIS2 — voire ne pas être comptabilisé du tout. La qualité des données se dégrade systématiquement dans les établissements à forts volumes où la gestion des registres est difficile.

## Ce que signifie l'intégration automatisée SGH-vers-DHIS2

L'intégration automatisée signifie que le système de gestion hospitalière maintient une base de données vivante des consultations individuelles des patients — chacune codée par diagnostic, type de prestation, données démographiques du patient et date — et, à la fin de chaque période de rapport, agrège automatiquement ces dossiers en décomptes d'indicateurs exacts requis par DHIS2, puis les soumet via l'API DHIS2 sans transcription manuelle.

Le résultat est une chaîne de rapport où : un patient se présente au service de consultations externes → le clinicien enregistre la consultation dans le SGH avec un code de diagnostic → le SGH comptabilise cette consultation dans la catégorie d'indicateur DHIS2 pertinente → à la fin du mois, les décomptes agrégés sont transmis automatiquement à DHIS2. Aucun pointage. Aucune transcription. Aucun formulaire RMA saisi manuellement. La soumission DHIS2 de l'établissement est générée directement à partir des mêmes dossiers cliniques qui pilotent la facturation, la pharmacie et les soins aux patients — une source unique de vérité.

## API DHIS2 : comment un SGH se connecte

DHIS2 fournit une API REST bien documentée qui permet aux systèmes externes de lire et d'écrire des données de manière programmatique. L'API utilise des requêtes HTTPS standard avec des charges utiles JSON. Un SGH se connectant à DHIS2 doit :

1. **S'authentifier** à l'aide des identifiants propres à l'établissement fournis par le bureau de santé de district ou le MINSANTE
2. **Mapper les éléments de données locaux** — les codes internes de prestations et de diagnostics du SGH — vers les UID des éléments de données DHIS2 correspondants pour l'instance nationale
3. **Agréger les dossiers** sur la période de rapport en décomptes requis pour chaque élément de données et combinaison d'options de catégorie
4. **POSTER les valeurs de données** vers le point de terminaison `/api/dataValueSets` de DHIS2 dans la bonne unité organisationnelle (l'UID DHIS2 assigné à l'établissement) et le bon format de période (par exemple `202501` pour janvier 2025)
5. **Traiter la réponse** — DHIS2 renvoie un résumé d'importation indiquant le succès, les conflits ou les erreurs de validation, que le SGH devrait journaliser à des fins d'audit

Les exigences techniques sont tout à fait à la portée de n'importe quelle plateforme SGH moderne. Le principal défi de mise en œuvre est l'étape de mappage : s'assurer que les codes cliniques locaux correspondent correctement aux éléments de données DHIS2 nationaux, qui peuvent être mis à jour lorsque le MINSANTE révise la liste nationale des indicateurs.

## Quels indicateurs sont exportés vers DHIS2

L'instance nationale DHIS2 du Cameroun collecte des indicateurs dans plusieurs domaines de programmes de santé. L'ensemble minimal de données standard pour un établissement de santé de premier niveau comprend :

| Domaine de programme | Indicateurs DHIS2 clés |
|---|---|
| Services de consultations externes | Total des consultations externes (nouvelles + suivi), par groupe d'âge et sexe |
| Santé maternelle | Première CPN, 4e visite CPN, accouchements assistés, consultations postnatales |
| Santé de l'enfant | Consultations externes des moins de 5 ans, suivi de la croissance |
| Vaccination | Doses administrées par antigène (BCG, Penta, Rougeole, Fièvre jaune, etc.) |
| Paludisme | Cas de paludisme confirmés (TDR positif + microscopie positive), cas traités |
| VIH/SIDA | Tests VIH réalisés, résultats positifs, patients mis sous ARV |
| Tuberculose | Cas de TB notifiés (nouveaux pulmonaires, retraitement, extra-pulmonaires) |
| Planification familiale | Clients PF (nouveaux + continus), par méthode |
| Hospitalisation | Admissions, journées-lits, décès, par service |

Le rapport des programmes verticaux — pour les programmes VIH financés par le PEPFAR, les programmes paludisme et TB du Fonds mondial — peut nécessiter des éléments de données supplémentaires au-delà de l'ensemble de données standard du MINSANTE, soumis à des instances DHIS2 distinctes ou à des plateformes complémentaires.

## Agrégation des données à partir des dossiers individuels

La puissance de l'intégration SGH-vers-DHIS2 réside dans l'agrégation de dossiers individuels structurés. Un SGH qui saisit l'âge, le sexe, le diagnostic (codé ICD-10), le type de prestation et la date de visite de chaque patient peut automatiquement compter, par exemple, le nombre de patientes âgées de 15 à 49 ans ayant effectué une première visite CPN au cours d'un mois donné — un indicateur DHIS2 qui nécessiterait autrement un pointage manuel à partir du registre de CPN.

Cette agrégation doit être configurée avec soin : les codes de diagnostic ICD-10 utilisés dans le SGH doivent être mappés aux définitions des indicateurs DHIS2. Le paludisme confirmé par TDR (ICD-10 B54 ou codes plus spécifiques) doit être distingué du paludisme diagnostiqué cliniquement si l'indicateur DHIS2 établit cette distinction. Les décès maternels doivent être signalés au moment de l'enregistrement pour apparaître dans le décompte de la mortalité maternelle. Ces mappages sont établis lors de la mise en œuvre du SGH et validés par rapport aux retours manuels historiques.

## Soumission planifiée ou en temps réel

Deux approches architecturales existent pour la soumission des données SGH-vers-DHIS2 :

**La soumission par lots planifiée** agrège et soumet les données selon un calendrier défini — généralement mensuel, aligné sur l'échéance nationale de rapport. C'est l'approche la plus courante et elle s'aligne sur le cycle de rapport mensuel du RMA. Certaines mises en œuvre soumettent des données récapitulatives hebdomadaires pour permettre une alerte précoce des épidémies.

**La soumission en quasi temps réel** transmet les mises à jour agrégées au niveau individuel à DHIS2 quotidiennement ou plus fréquemment. Elle est moins courante au Cameroun mais devient de plus en plus pertinente pour les programmes de surveillance des maladies où la détection précoce des épidémies est une priorité. Elle nécessite une connexion internet stable au sein de l'établissement et une instance DHIS2 configurée pour accepter des mises à jour fréquentes.

## Intégration LMIS : rapport des produits

La fonction de Système d'Information de Gestion Logistique (LMIS) au sein de DHIS2 — ou intégrée à celui-ci — gère le rapport de consommation des produits : combien de doses de chaque vaccin ont été utilisées, combien de TDR du paludisme ont été consommés, combien de moustiquaires ont été distribuées. Au Cameroun, les données LMIS alimentent la chaîne d'approvisionnement gérée par la Centrale Nationale d'Approvisionnement en Médicaments Essentiels (CENAME) et éclairent la planification des approvisionnements.

Un SGH doté de modules de pharmacie et de gestion des stocks peut générer automatiquement les rapports LMIS à partir des dossiers de dispensation — en captant la consommation réelle des produits par rapport aux soldes de stock et en générant le rapport de stock au niveau de l'établissement qui alimente la chaîne d'approvisionnement nationale. Cela boucle la boucle entre les données de prestations cliniques et les données de la chaîne d'approvisionnement au sein d'un seul système intégré.

## Bénéfices pour la qualité des données de santé nationales

Le passage du rapport manuel au rapport automatisé SGH-vers-DHIS2 présente des bénéfices bien documentés pour la qualité des données de santé nationales dans les pays qui ont opéré cette transition. L'exhaustivité des rapports augmente parce que la soumission mensuelle est générée automatiquement plutôt que de dépendre d'un membre du personnel trouvant le temps de remplir un formulaire papier. La ponctualité s'améliore pour la même raison. L'exactitude des données s'améliore parce que les données sources sont les mêmes dossiers cliniques structurés utilisés pour la facturation — et non une marque de pointage recomptée à la fin du mois.

Pour le MINSANTE, des données d'établissement de meilleure qualité améliorent la fiabilité des statistiques sanitaires nationales, renforcent la base de données probantes pour les décisions politiques et améliorent la qualité des rapports soumis aux organismes internationaux, notamment l'OMS, l'Union africaine, ainsi que les bailleurs bilatéraux et multilatéraux.

## Comment le SGH OPES permet l'intégration DHIS2

Le système de gestion hospitalière OPES est conçu avec les exigences nationales de rapport du Cameroun comme considération centrale de conception. Le module clinique OPES impose le codage des diagnostics en ICD-10 au point de soins et capte les données démographiques du patient — âge, sexe, statut de grossesse — nécessaires pour désagréger correctement les indicateurs DHIS2. Le module de rapport OPES inclut une fonction d'export DHIS2 qui mappe les dossiers locaux vers les éléments de données nationaux et génère l'ensemble de données agrégées mensuelles prêt à être soumis.

Lorsque l'établissement dispose d'une connexion internet, OPES peut soumettre les données directement à DHIS2 via l'API, en générant un journal de résumé d'importation qui sert d'enregistrement officiel de la soumission. Lorsque la connectivité est peu fiable — fréquent dans les zones rurales et périurbaines du Cameroun — OPES génère le fichier de données au format DHIS2 pour une soumission hors ligne par le point focal des données lorsque la connectivité est disponible. Le résultat est un établissement qui remplit ses obligations de rapport envers le MINSANTE avec exactitude et dans les délais, sans alourdir la charge de travail du personnel clinique ou administratif — transformant la conformité d'une contrainte en un sous-produit des opérations normales.
