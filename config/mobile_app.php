<?php

return [
    'version' => '0.3.0',
    'android_build' => 8,
    'runtime_version' => '0.3.0',
    'channel' => 'preview',
    'released_at' => '2026-11-06',
    'download_path' => 'mobile-app/opes-health-0.3.0.apk',
    'download_url' => 'https://expo.dev/artifacts/eas/wpw_TUZEa4YDtj6-bI-384qhoFSs14_GExlvYhoktI0.apk',
    'sha256' => 'EE1FD8C03954CD4947ABDF544225D49120300790890EC823078E8D34527E9231',
    'eas_build_url' => 'https://expo.dev/accounts/opesware/projects/opes-health/builds/8e8b12ec-ea72-406c-acbf-1d723650e652',
    'ota_updates' => [
        [
            'group' => '911604e1-d930-4468-b706-b322c870fda6',
            'date' => '2026-11-06',
            'runtime' => '0.3.0',
            'title' => [
                'en' => 'OPES Health 0.3.0: session safety, role workflows and bilingual UI',
                'fr' => 'OPES Health 0.3.0 : sessions sécurisées, workflows par rôle et interface bilingue',
            ],
            'summary' => [
                'en' => 'Published Android OTA for runtime 0.3.0. Improves session restoration and expiry handling, confirms sensitive actions, refreshes live records after changes, and includes English/French localization. Install Android build 8 (app/runtime 0.3.0) to receive this update.',
                'fr' => 'Mise à jour OTA Android publiée pour le runtime 0.3.0. Améliore la restauration et l’expiration des sessions, confirme les actions sensibles, actualise les données après modification et ajoute les interfaces anglaise et française. Installer le build Android 8 (application/runtime 0.3.0) pour recevoir cette mise à jour.',
            ],
        ],
        [
            'group' => '5180b9a2-3b9c-4037-966b-9f42a697d334',
            'date' => '2026-11-02',
            'runtime' => '0.1.0',
            'title' => [
                'en' => 'Keep Mark as sent visible on every quote',
                'fr' => 'Garder l’action Marquer comme envoyé visible sur chaque devis',
            ],
            'summary' => [
                'en' => 'Every quote now shows View quote and Mark as sent side by side. Mark as sent is enabled only for drafts when the account has finance write access. Compatible with existing 0.1.0 preview installs.',
                'fr' => 'Chaque devis affiche Voir le devis et Marquer comme envoyé côte à côte. L’action n’est active que pour les brouillons si le compte possède les droits d’écriture financière. Compatible avec les installations d’aperçu 0.1.0.',
            ],
        ],
        [
            'group' => '24512695-225f-4428-870d-a303545098df',
            'date' => '2026-11-02',
            'runtime' => '0.2.0',
            'title' => [
                'en' => 'Keep Mark as sent visible on every quote',
                'fr' => 'Garder l’action Marquer comme envoyé visible sur chaque devis',
            ],
            'summary' => [
                'en' => 'Every quote now shows View quote and Mark as sent side by side. Mark as sent is enabled only for drafts when the account has finance write access.',
                'fr' => 'Chaque devis affiche Voir le devis et Marquer comme envoyé côte à côte. L’action n’est active que pour les brouillons si le compte possède les droits d’écriture financière.',
            ],
        ],
        [
            'group' => '10b96eb6-77da-4c5f-aa61-cccb10691dba',
            'date' => '2026-11-02',
            'runtime' => '0.1.0',
            'title' => [
                'en' => 'Quote details, customer records and safe mobile navigation',
                'fr' => 'Détails des devis, fiches clients et navigation mobile sûre',
            ],
            'summary' => [
                'en' => 'Added quote and customer detail actions, expanded quote line-item and tax details, fixed sticky app headers and phone-safe bottom tabs, and added role-aware messages access. Existing 0.1.0 installs can receive this compatible update.',
                'fr' => 'Ajout des actions et détails des devis et clients, des lignes et taxes des devis, correction des en-têtes fixes et onglets adaptés aux zones sûres du téléphone, et accès aux messages selon le rôle. Les installations 0.1.0 peuvent recevoir cette mise à jour compatible.',
            ],
        ],
        [
            'group' => '8a1a680d-4beb-4255-8b63-5e3c3b7f4913',
            'date' => '2026-11-02',
            'runtime' => '0.1.0',
            'title' => [
                'en' => 'Health workspace demos restored; hotel profiles removed',
                'fr' => 'Démos des espaces santé rétablies ; profils hôteliers supprimés',
            ],
            'summary' => [
                'en' => 'Restored the five OPES Health sample profiles: staff, customer, health practitioner, tester, and partner. These fictional previews remain local and do not call the live API.',
                'fr' => 'Rétablissement des cinq profils de démonstration OPES Health : personnel, client, professionnel de santé, testeur et partenaire. Ces aperçus fictifs restent locaux et n’appellent pas l’API en production.',
            ],
        ],
        [
            'group' => '42ada363-a3f3-4620-a640-50a045b9a775',
            'date' => '2026-11-02',
            'runtime' => '0.1.0',
            'title' => [
                'en' => 'Role-based workflows and mobile navigation',
                'fr' => 'Workflows par rôle et navigation mobile',
            ],
        ],
        [
            'group' => 'c0ec69f7-ce49-4c0a-bc2d-d8f683fb2d04',
            'date' => '2026-11-02',
            'runtime' => '0.1.0',
            'title' => [
                'en' => 'Admin sign-in uses the secure account flow; admin demo removed',
                'fr' => 'Connexion administrateur sécurisée ; démonstration admin supprimée',
            ],
        ],
    ],
    'releases' => [
        [
            'version' => '0.3.0', 'build' => 8, 'date' => '2026-11-06', 'current' => true,
            'title' => [
                'en' => 'Safer sessions, smoother workflows and bilingual UI',
                'fr' => 'Sessions plus sûres, workflows améliorés et interface bilingue',
            ],
            'summary' => [
                'en' => 'Current private Android preview. Uses OAuth 2.0 with S256 PKCE and server RBAC; adds stronger session recovery, confirmed high-impact actions, live-record refresh and English/French support. Includes OTA group 911604e1-d930-4468-b706-b322c870fda6 for runtime 0.3.0.',
                'fr' => 'Aperçu Android privé actuel. Utilise OAuth 2.0 avec PKCE S256 et le RBAC serveur ; améliore la récupération de session, confirme les actions importantes, actualise les données et prend en charge l’anglais et le français. Inclut le groupe OTA 911604e1-d930-4468-b706-b322c870fda6 pour le runtime 0.3.0.',
            ],
            'changes' => [
                'en' => [
                    'Protected signed-in routes, handled expired sessions clearly, and refreshed once after an authentication failure while reusing the same idempotency key.',
                    'Added confirmations for sign-out and sensitive partner, assignment and lead actions; refreshed live records before full updates.',
                    'Kept demo workspace previews local and removed all sample records and administrator demo access.',
                    'Added English/French language selection, readable form labels, a clearer profile and an updated home screen with mobile-safe navigation.',
                    'App icon remains the rounded shield; the full OPES seal remains the splash-screen mark.',
                    'App version 0.3.0, Android build 8, OTA runtime 0.3.0.',
                ],
                'fr' => [
                    'Protection des écrans connectés, gestion claire des sessions expirées et nouvel essai après erreur d’authentification en conservant la même clé d’idempotence.',
                    'Ajout de confirmations pour la déconnexion et les actions sensibles des partenaires, affectations et prospects ; actualisation des données avant remplacement complet.',
                    'Aperçus des espaces de démonstration conservés localement ; suppression des données fictives et de l’accès administrateur de démonstration.',
                    'Ajout du choix anglais/français, de libellés de formulaire lisibles, d’un profil amélioré et d’un nouvel accueil avec navigation adaptée au mobile.',
                    'L’icône reste le bouclier arrondi ; le sceau OPES complet reste réservé à l’écran de lancement.',
                    'Version 0.3.0, build Android 8, runtime OTA 0.3.0.',
                ],
            ],
        ],
        [
            'version' => '0.2.0', 'build' => 7, 'date' => '2026-11-02', 'current' => false,
            'title' => [
                'en' => 'Detailed quotes, customer profiles and safer navigation',
                'fr' => 'Devis détaillés, profils clients et navigation plus sûre',
            ],
            'summary' => [
                'en' => 'Current private Android preview. Quote cards include view and send-status actions with detailed line items, customer and tax data; customer cards open full profiles. The app header stays fixed, bottom tabs respect phone safe areas, and the new rounded shield icon is used on device while the full seal remains on the splash screen.',
                'fr' => 'Aperçu Android privé actuel. Les devis affichent des actions de consultation et d’envoi avec les lignes, données client et taxes ; les fiches clients ouvrent les profils détaillés. L’en-tête reste fixe, les onglets respectent les zones sûres du téléphone, et le nouvel icône bouclier arrondi apparaît sur l’appareil tandis que le sceau complet reste sur l’écran de lancement.',
            ],
            'changes' => [
                'en' => [
                    'Added side-by-side View quote and Mark as sent actions; details now include customer enquiry, quote dates, line items, taxes, totals and notes.',
                    'Added explicit customer-detail access with all returned profile fields.',
                    'Pinned the app header and made the bottom navigation account for Android and iOS safe-area insets.',
                    'Added a role-aware messages shortcut beside notifications for staff and customer accounts.',
                    'Changed in-app branding to OPES Health Systems text; the seal is reserved for the splash screen.',
                    'Applied the supplied rounded shield app icon. App version 0.2.0, Android build 7.',
                ],
                'fr' => [
                    'Ajout des actions Voir le devis et Marquer comme envoyé côte à côte ; les détails incluent le client, les dates, les lignes, les taxes, les totaux et les notes.',
                    'Ajout d’un accès explicite aux fiches clients avec tous les champs renvoyés par le profil.',
                    'En-tête fixe et navigation basse adaptée aux zones sûres Android et iOS.',
                    'Ajout d’un raccourci messages adapté au rôle, à côté des notifications pour le personnel et les clients.',
                    'Identité textuelle OPES Health Systems dans l’app ; le sceau est réservé à l’écran de lancement.',
                    'Application de l’icône bouclier arrondie fournie. Version 0.2.0, build Android 7.',
                ],
            ],
        ],
        [
            'version' => '0.1.0', 'build' => 6, 'date' => '2026-11-02', 'current' => false,
            'title' => [
                'en' => 'Secure account sign-in and role workspaces',
                'fr' => 'Connexion sécurisée et espaces par rôle',
            ],
            'summary' => [
                'en' => 'Current private Android preview. Admins sign in with their real OPES account; server roles route each account to its workspace.',
                'fr' => 'Aperçu privé Android actuel. Les administrateurs se connectent avec leur compte OPES ; les rôles serveur ouvrent l’espace correspondant.',
            ],
            'changes' => [
                'en' => [
                    'Removed the administrator demo account; administrator sign-in uses OAuth 2.0 with S256 PKCE.',
                    'Added role-based home screens and four adaptive navigation tabs for supported account types.',
                    'Added API-backed staff, customer, practitioner, tester and partner workflows where the API supports them.',
                    'Added course lessons, progress tracking, certificates, account recovery and notifications.',
                    'Published compatible OTA updates for runtime 0.1.0; no role chooser is shown.',
                ],
                'fr' => [
                    'Suppression du compte administrateur de démonstration ; connexion par OAuth 2.0 avec PKCE S256.',
                    'Ajout d’écrans d’accueil par rôle et de quatre onglets adaptatifs pour les types de comptes pris en charge.',
                    'Ajout de workflows API pour le personnel, les clients, les praticiens, les testeurs et les partenaires lorsque l’API le permet.',
                    'Ajout des leçons, du suivi de progression, des certificats, de la récupération de compte et des notifications.',
                    'Publication de mises à jour OTA compatibles avec le runtime 0.1.0 ; aucun choix de rôle à la connexion.',
                ],
            ],
        ],
        [
            'version' => '0.1.0', 'build' => 5, 'date' => '2026-11-02', 'current' => false,
            'title' => [
                'en' => 'Preview build packaging update',
                'fr' => 'Mise à jour du paquet d’aperçu',
            ],
            'summary' => [
                'en' => 'Build pipeline update to keep responsive web-export files out of the native Android package; no user-facing feature change.',
                'fr' => 'Mise à jour du processus de compilation pour exclure les fichiers web adaptatifs du paquet Android ; aucun changement fonctionnel visible.',
            ],
            'changes' => ['en' => [], 'fr' => []],
        ],
        [
            'version' => '0.1.0', 'build' => 3, 'date' => '2026-11-01', 'current' => false,
            'title' => [
                'en' => 'Functional role workspaces',
                'fr' => 'Espaces fonctionnels par rôle',
            ],
            'summary' => [
                'en' => 'Introduced the interactive role workspaces and learning flows that later preview builds expanded.',
                'fr' => 'Introduction des espaces interactifs par rôle et des parcours d’apprentissage développés dans les aperçus suivants.',
            ],
            'changes' => [
                'en' => [
                    'Added role-specific mobile workspaces and API-backed workflow screens.',
                    'Added study lessons and course progress in addition to certificates.',
                    'Added tester assignment progress and issue reporting.',
                ],
                'fr' => [
                    'Ajout d’espaces mobiles par rôle et d’écrans de workflow connectés à l’API.',
                    'Ajout des leçons et de la progression des cours, en complément des certificats.',
                    'Ajout du suivi des affectations et du signalement de problèmes pour les testeurs.',
                ],
            ],
        ],
        [
            'version' => '0.1.0', 'build' => 2, 'date' => '2026-11-01', 'current' => false,
            'title' => [
                'en' => 'Quote requests retain product pricing',
                'fr' => 'Les demandes de devis conservent le prix du produit',
            ],
            'summary' => [
                'en' => 'Carried the selected product and indicative price into the quote request flow.',
                'fr' => 'Transmission du produit sélectionné et du prix indicatif dans le parcours de demande de devis.',
            ],
            'changes' => ['en' => [], 'fr' => []],
        ],
        [
            'version' => '0.1.0', 'build' => 1, 'date' => '2026-11-01', 'current' => false,
            'title' => [
                'en' => 'Private Android preview foundation',
                'fr' => 'Base de l’aperçu privé Android',
            ],
            'summary' => [
                'en' => 'Initial preview build with OAuth/PKCE sign-in, RBAC workspace routing and OTA update support.',
                'fr' => 'Premier aperçu avec connexion OAuth/PKCE, routage RBAC des espaces et prise en charge des mises à jour OTA.',
            ],
            'changes' => ['en' => [], 'fr' => []],
        ],
    ],
];
