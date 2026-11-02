<?php

return [
    'version' => '0.1.0',
    'android_build' => 6,
    'runtime_version' => '0.1.0',
    'channel' => 'preview',
    'released_at' => '2026-11-02',
    'download_path' => 'mobile-app/opes-health-0.1.0.apk',
    'sha256' => '453c7e0e0f79958037438ce63c91ffaf15d27562e3883cd57d857cb7921f95ac',
    'eas_build_url' => 'https://expo.dev/accounts/opesware/projects/opes-health/builds/772f3b9b-0a13-4509-8d39-ba70520ce413',
    'ota_updates' => [
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
            'version' => '0.1.0', 'build' => 6, 'date' => '2026-11-02', 'current' => true,
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
