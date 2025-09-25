<?php

return [
    // Navigation
    'nav' => [
        'home' => 'Accueil',
        'diagnose' => 'Diagnostic',
        'my_cars' => 'Mes Voitures',
        'mechanics' => 'Mécaniciens',
        'dashboard' => 'Tableau de Bord',
        'login' => 'Connexion',
        'register' => 'S\'inscrire',
        'logout' => 'Déconnexion',
        'profile' => 'Profil',
    ],

    // Common
    'common' => [
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'delete' => 'Supprimer',
        'edit' => 'Modifier',
        'add' => 'Ajouter',
        'search' => 'Rechercher',
        'filter' => 'Filtrer',
        'loading' => 'Chargement...',
        'error' => 'Erreur',
        'success' => 'Succès',
        'warning' => 'Avertissement',
        'info' => 'Information',
        'yes' => 'Oui',
        'no' => 'Non',
        'close' => 'Fermer',
        'back' => 'Retour',
        'next' => 'Suivant',
        'previous' => 'Précédent',
        'submit' => 'Soumettre',
        'reset' => 'Réinitialiser',
        'confirm' => 'Confirmer',
    ],

    // Home Page
    'home' => [
        'title' => 'Diagnostic Automobile par IA',
        'subtitle' => 'Obtenez un diagnostic instantané et précis pour les problèmes de votre véhicule en utilisant une technologie IA avancée',
        'get_started' => 'Commencer',
        'learn_more' => 'En savoir plus',
        'features' => [
            'title' => 'Pourquoi choisir CarWise.ai ?',
            'ai_diagnosis' => [
                'title' => 'Diagnostic par IA',
                'description' => 'L\'intelligence artificielle avancée analyse les problèmes de votre voiture avec une grande précision'
            ],
            'global_support' => [
                'title' => 'Support Automobile Mondial',
                'description' => 'Support pour 63+ marques automobiles de 20+ pays dans le monde'
            ],
            'instant_results' => [
                'title' => 'Résultats Instantanés',
                'description' => 'Obtenez les résultats de diagnostic en secondes, pas en jours'
            ],
            'expert_network' => [
                'title' => 'Réseau d\'Experts',
                'description' => 'Connectez-vous avec des mécaniciens certifiés dans votre région'
            ]
        ]
    ],

    // Diagnosis
    'diagnosis' => [
        'title' => 'Diagnostic Automobile par IA',
        'subtitle' => 'Obtenez un diagnostic instantané et précis pour les problèmes de votre véhicule en utilisant une technologie IA avancée. Téléchargez des symptômes, des photos ou décrivez le problème pour une analyse complète.',
        'steps' => [
            'vehicle_info' => 'Info Véhicule',
            'symptoms' => 'Symptômes',
            'ai_analysis' => 'Analyse IA',
            'results' => 'Résultats'
        ],
        'vehicle_info' => [
            'title' => 'Informations du Véhicule',
            'make' => 'Marque',
            'model' => 'Modèle',
            'year' => 'Année',
            'mileage' => 'Kilométrage',
            'engine_type' => 'Type de Moteur',
            'engine_size' => 'Taille du Moteur',
            'select_make' => 'Sélectionner une Marque',
            'select_model' => 'Sélectionner un Modèle',
            'select_make_first' => 'Veuillez d\'abord sélectionner une marque',
            'pre_filled' => 'Pré-rempli depuis votre voiture'
        ],
        'symptoms' => [
            'title' => 'Description du Problème',
            'description' => 'Décrivez le problème que vous rencontrez avec votre véhicule',
            'common_symptoms' => 'Symptômes Courants',
            'select_symptoms' => 'Sélectionnez les symptômes qui s\'appliquent à votre véhicule',
            'upload_photos' => 'Télécharger des Photos (Optionnel)',
            'drag_drop' => 'Glissez-déposez des photos ici, ou cliquez pour sélectionner',
            'max_files' => 'Maximum 5 fichiers, 10MB chacun'
        ],
        'results' => [
            'title' => 'Résultats du Diagnostic',
            'problem_title' => 'Problème Identifié',
            'confidence' => 'Score de Confiance',
            'severity' => 'Gravité',
            'description' => 'Description',
            'likely_causes' => 'Causes Probables',
            'recommended_actions' => 'Actions Recommandées',
            'estimated_costs' => 'Coûts Estimés',
            'ai_insights' => 'Insights IA',
            'immediate_attention' => 'Attention Immédiate Requise',
            'find_mechanics' => 'Trouver des Mécaniciens Certifiés',
            'cost_note' => '* Les estimations de coûts sont approximatives et peuvent varier selon l\'emplacement et les exigences spécifiques du véhicule.'
        ]
    ],

    // My Cars
    'my_cars' => [
        'title' => 'Mes Voitures',
        'subtitle' => 'Gérez vos véhicules, suivez la maintenance et consultez l\'historique des diagnostics tout en un seul endroit',
        'add_car' => 'Ajouter une Nouvelle Voiture',
        'add_first_car' => 'Ajouter Votre Première Voiture',
        'no_cars' => 'Aucune voiture ajoutée pour le moment',
        'no_cars_description' => 'Commencez par ajouter votre première voiture pour commencer à suivre la maintenance et obtenir des diagnostics par IA.',
        'statistics' => [
            'total_cars' => 'Total des Voitures',
            'active_cars' => 'Voitures Actives',
            'total_diagnoses' => 'Total des Diagnostics',
            'average_age' => 'Âge Moyen'
        ],
        'form' => [
            'title' => 'Ajouter une Nouvelle Voiture',
            'edit_title' => 'Modifier la Voiture',
            'brand' => 'Marque',
            'model' => 'Modèle',
            'year' => 'Année',
            'vin' => 'VIN',
            'license_plate' => 'Plaque d\'Immatriculation',
            'color' => 'Couleur',
            'fuel_type' => 'Type de Carburant',
            'transmission' => 'Transmission',
            'mileage' => 'Kilométrage',
            'purchase_date' => 'Date d\'Achat',
            'notes' => 'Notes',
            'status' => 'Statut',
            'select_brand' => 'Sélectionner une marque',
            'select_model' => 'Sélectionner un modèle',
            'select_brand_first' => 'Veuillez d\'abord sélectionner une marque',
            'saving' => 'Enregistrement...',
            'update_car' => 'Mettre à Jour la Voiture',
            'add_car' => 'Ajouter la Voiture'
        ],
        'actions' => [
            'diagnose' => 'Diagnostic',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'view_history' => 'Voir l\'Historique'
        ]
    ],

    // Mechanics
    'mechanics' => [
        'title' => 'Trouver des Mécaniciens Certifiés',
        'subtitle' => 'Connectez-vous avec des mécaniciens professionnels dans votre région pour des services experts de réparation et de maintenance automobile',
        'search_placeholder' => 'Rechercher des mécaniciens par nom, emplacement ou spécialité...',
        'filter_by' => 'Filtrer par',
        'specialty' => 'Spécialité',
        'location' => 'Emplacement',
        'rating' => 'Note',
        'all_specialties' => 'Toutes les Spécialités',
        'all_locations' => 'Tous les Emplacements',
        'contact' => 'Contacter',
        'view_profile' => 'Voir le Profil',
        'no_mechanics' => 'Aucun mécanicien trouvé',
        'no_mechanics_description' => 'Essayez d\'ajuster vos critères de recherche ou revenez plus tard pour de nouveaux mécaniciens dans votre région.'
    ],

    // Authentication
    'auth' => [
        'login' => [
            'title' => 'Bon Retour',
            'subtitle' => 'Connectez-vous à votre compte pour continuer',
            'email' => 'Adresse E-mail',
            'password' => 'Mot de Passe',
            'remember_me' => 'Se Souvenir de Moi',
            'forgot_password' => 'Mot de Passe Oublié ?',
            'sign_in' => 'Se Connecter',
            'no_account' => 'Vous n\'avez pas de compte ?',
            'sign_up' => 'S\'inscrire'
        ],
        'register' => [
            'title' => 'Créer un Compte',
            'subtitle' => 'Rejoignez CarWise.ai et commencez à diagnostiquer les problèmes de votre voiture avec l\'IA',
            'personal_info' => 'Informations Personnelles',
            'contact_info' => 'Informations de Contact',
            'professional_info' => 'Informations Professionnelles (Optionnel)',
            'preferences' => 'Préférences',
            'first_name' => 'Prénom',
            'last_name' => 'Nom de Famille',
            'email' => 'Adresse E-mail',
            'phone' => 'Numéro de Téléphone',
            'password' => 'Mot de Passe',
            'confirm_password' => 'Confirmer le Mot de Passe',
            'location' => 'Emplacement',
            'bio' => 'Bio',
            'role' => 'Rôle',
            'customer' => 'Client',
            'mechanic' => 'Mécanicien',
            'experience_years' => 'Années d\'Expérience',
            'expertise' => 'Expertise',
            'hourly_rate' => 'Taux Horaire',
            'timezone' => 'Fuseau Horaire',
            'language' => 'Langue',
            'terms' => 'J\'accepte les Conditions d\'Utilisation et la Politique de Confidentialité',
            'create_account' => 'Créer un Compte',
            'have_account' => 'Vous avez déjà un compte ?',
            'sign_in' => 'Se Connecter'
        ]
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Tableau de Bord',
        'welcome' => 'Bon retour',
        'overview' => 'Aperçu',
        'recent_activity' => 'Activité Récente',
        'quick_actions' => 'Actions Rapides',
        'statistics' => [
            'total_cars' => 'Total des Voitures',
            'active_cars' => 'Voitures Actives',
            'total_diagnoses' => 'Total des Diagnostics',
            'average_age' => 'Âge Moyen'
        ],
        'recent_diagnoses' => [
            'title' => 'Diagnostics Récents',
            'no_diagnoses' => 'Aucun diagnostic récent',
            'view_all' => 'Voir Tout'
        ]
    ],

    // Notifications
    'notifications' => [
        'car_added' => 'Voiture Ajoutée',
        'car_updated' => 'Voiture Mise à Jour',
        'car_deleted' => 'Voiture Supprimée',
        'diagnosis_complete' => 'Diagnostic Terminé',
        'registration_success' => 'Inscription Réussie',
        'login_success' => 'Connexion Réussie',
        'logout_success' => 'Déconnexion Réussie'
    ],

    // Validation
    'validation' => [
        'required' => 'Ce champ est requis',
        'email' => 'Veuillez entrer une adresse e-mail valide',
        'min' => 'Ce champ doit contenir au moins :min caractères',
        'max' => 'Ce champ ne doit pas dépasser :max caractères',
        'numeric' => 'Ce champ doit être un nombre',
        'date' => 'Veuillez entrer une date valide',
        'unique' => 'Cette valeur est déjà prise',
        'confirmed' => 'La confirmation du mot de passe ne correspond pas'
    ],
    'smart_car' => 'Voiture Intelligente',
    'powered_by_ai' => 'Alimenté par l\'IA',
    'start_diagnosis' => 'Commencer le diagnostic',
];
