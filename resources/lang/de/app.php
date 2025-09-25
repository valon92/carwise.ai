<?php

return [
    // Navigation
    'nav' => [
        'home' => 'Startseite',
        'diagnose' => 'Diagnose',
        'my_cars' => 'Meine Autos',
        'mechanics' => 'Mechaniker',
        'dashboard' => 'Dashboard',
        'login' => 'Anmelden',
        'register' => 'Registrieren',
        'logout' => 'Abmelden',
        'profile' => 'Profil',
    ],

    // Common
    'common' => [
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'delete' => 'Löschen',
        'edit' => 'Bearbeiten',
        'add' => 'Hinzufügen',
        'search' => 'Suchen',
        'filter' => 'Filtern',
        'loading' => 'Lädt...',
        'error' => 'Fehler',
        'success' => 'Erfolg',
        'warning' => 'Warnung',
        'info' => 'Information',
        'yes' => 'Ja',
        'no' => 'Nein',
        'close' => 'Schließen',
        'back' => 'Zurück',
        'next' => 'Weiter',
        'previous' => 'Vorherige',
        'submit' => 'Absenden',
        'reset' => 'Zurücksetzen',
        'confirm' => 'Bestätigen',
    ],

    // Home Page
    'home' => [
        'title' => 'KI-gestützte Autodiagnose',
        'subtitle' => 'Erhalten Sie sofortige, genaue Diagnosen für Ihre Fahrzeugprobleme mit fortschrittlicher KI-Technologie',
        'get_started' => 'Loslegen',
        'learn_more' => 'Mehr erfahren',
        'features' => [
            'title' => 'Warum CarWise.ai wählen?',
            'ai_diagnosis' => [
                'title' => 'KI-gestützte Diagnose',
                'description' => 'Fortschrittliche künstliche Intelligenz analysiert Ihre Autoprobleme mit hoher Genauigkeit'
            ],
            'global_support' => [
                'title' => 'Globale Autounterstützung',
                'description' => 'Unterstützung für 63+ Automarken aus 20+ Ländern weltweit'
            ],
            'instant_results' => [
                'title' => 'Sofortige Ergebnisse',
                'description' => 'Erhalten Sie Diagnoseergebnisse in Sekunden, nicht Tagen'
            ],
            'expert_network' => [
                'title' => 'Expertennetzwerk',
                'description' => 'Verbinden Sie sich mit zertifizierten Mechanikern in Ihrer Nähe'
            ]
        ]
    ],

    // Diagnosis
    'diagnosis' => [
        'title' => 'KI-gestützte Autodiagnose',
        'subtitle' => 'Erhalten Sie sofortige, genaue Diagnosen für Ihre Fahrzeugprobleme mit fortschrittlicher KI-Technologie. Laden Sie Symptome, Fotos hoch oder beschreiben Sie das Problem für eine umfassende Analyse.',
        'steps' => [
            'vehicle_info' => 'Fahrzeuginfo',
            'symptoms' => 'Symptome',
            'ai_analysis' => 'KI-Analyse',
            'results' => 'Ergebnisse'
        ],
        'vehicle_info' => [
            'title' => 'Fahrzeuginformationen',
            'make' => 'Marke',
            'model' => 'Modell',
            'year' => 'Jahr',
            'mileage' => 'Kilometerstand',
            'engine_type' => 'Motortyp',
            'engine_size' => 'Motorgröße',
            'select_make' => 'Marke auswählen',
            'select_model' => 'Modell auswählen',
            'select_make_first' => 'Bitte wählen Sie zuerst eine Marke',
            'pre_filled' => 'Aus Ihrem Auto vorausgefüllt'
        ],
        'symptoms' => [
            'title' => 'Problembeschreibung',
            'description' => 'Beschreiben Sie das Problem, das Sie mit Ihrem Fahrzeug haben',
            'common_symptoms' => 'Häufige Symptome',
            'select_symptoms' => 'Wählen Sie Symptome aus, die auf Ihr Fahrzeug zutreffen',
            'upload_photos' => 'Fotos hochladen (Optional)',
            'drag_drop' => 'Fotos hier hineinziehen oder klicken zum Auswählen',
            'max_files' => 'Maximal 5 Dateien, je 10MB'
        ],
        'results' => [
            'title' => 'Diagnoseergebnisse',
            'problem_title' => 'Identifiziertes Problem',
            'confidence' => 'Vertrauenswert',
            'severity' => 'Schweregrad',
            'description' => 'Beschreibung',
            'likely_causes' => 'Wahrscheinliche Ursachen',
            'recommended_actions' => 'Empfohlene Maßnahmen',
            'estimated_costs' => 'Geschätzte Kosten',
            'ai_insights' => 'KI-Einblicke',
            'immediate_attention' => 'Sofortige Aufmerksamkeit erforderlich',
            'find_mechanics' => 'Zertifizierte Mechaniker finden',
            'cost_note' => '* Kostenschätzungen sind ungefähr und können je nach Standort und spezifischen Fahrzeuganforderungen variieren.'
        ]
    ],

    // My Cars
    'my_cars' => [
        'title' => 'Meine Autos',
        'subtitle' => 'Verwalten Sie Ihre Fahrzeuge, verfolgen Sie Wartungen und sehen Sie Diagnoseverlauf alles an einem Ort',
        'add_car' => 'Neues Auto hinzufügen',
        'add_first_car' => 'Ihr erstes Auto hinzufügen',
        'no_cars' => 'Noch keine Autos hinzugefügt',
        'no_cars_description' => 'Beginnen Sie mit dem Hinzufügen Ihres ersten Autos, um Wartungen zu verfolgen und KI-gestützte Diagnosen zu erhalten.',
        'statistics' => [
            'total_cars' => 'Gesamte Autos',
            'active_cars' => 'Aktive Autos',
            'total_diagnoses' => 'Gesamte Diagnosen',
            'average_age' => 'Durchschnittsalter'
        ],
        'form' => [
            'title' => 'Neues Auto hinzufügen',
            'edit_title' => 'Auto bearbeiten',
            'brand' => 'Marke',
            'model' => 'Modell',
            'year' => 'Jahr',
            'vin' => 'FIN',
            'license_plate' => 'Kennzeichen',
            'color' => 'Farbe',
            'fuel_type' => 'Kraftstofftyp',
            'transmission' => 'Getriebe',
            'mileage' => 'Kilometerstand',
            'purchase_date' => 'Kaufdatum',
            'notes' => 'Notizen',
            'status' => 'Status',
            'select_brand' => 'Marke auswählen',
            'select_model' => 'Modell auswählen',
            'select_brand_first' => 'Bitte wählen Sie zuerst eine Marke',
            'saving' => 'Speichern...',
            'update_car' => 'Auto aktualisieren',
            'add_car' => 'Auto hinzufügen'
        ],
        'actions' => [
            'diagnose' => 'Diagnose',
            'edit' => 'Bearbeiten',
            'delete' => 'Löschen',
            'view_history' => 'Verlauf anzeigen'
        ]
    ],

    // Mechanics
    'mechanics' => [
        'title' => 'Zertifizierte Mechaniker finden',
        'subtitle' => 'Verbinden Sie sich mit professionellen Mechanikern in Ihrer Nähe für Experten-Reparatur- und Wartungsdienstleistungen',
        'search_placeholder' => 'Mechaniker nach Name, Standort oder Spezialität suchen...',
        'filter_by' => 'Filtern nach',
        'specialty' => 'Spezialität',
        'location' => 'Standort',
        'rating' => 'Bewertung',
        'all_specialties' => 'Alle Spezialitäten',
        'all_locations' => 'Alle Standorte',
        'contact' => 'Kontakt',
        'view_profile' => 'Profil anzeigen',
        'no_mechanics' => 'Keine Mechaniker gefunden',
        'no_mechanics_description' => 'Versuchen Sie, Ihre Suchkriterien anzupassen oder schauen Sie später nach neuen Mechanikern in Ihrer Nähe.'
    ],

    // Authentication
    'auth' => [
        'login' => [
            'title' => 'Willkommen zurück',
            'subtitle' => 'Melden Sie sich in Ihrem Konto an, um fortzufahren',
            'email' => 'E-Mail-Adresse',
            'password' => 'Passwort',
            'remember_me' => 'Angemeldet bleiben',
            'forgot_password' => 'Passwort vergessen?',
            'sign_in' => 'Anmelden',
            'no_account' => 'Haben Sie kein Konto?',
            'sign_up' => 'Registrieren'
        ],
        'register' => [
            'title' => 'Konto erstellen',
            'subtitle' => 'Treten Sie CarWise.ai bei und beginnen Sie, Ihre Autoprobleme mit KI zu diagnostizieren',
            'personal_info' => 'Persönliche Informationen',
            'contact_info' => 'Kontaktinformationen',
            'professional_info' => 'Berufliche Informationen (Optional)',
            'preferences' => 'Einstellungen',
            'first_name' => 'Vorname',
            'last_name' => 'Nachname',
            'email' => 'E-Mail-Adresse',
            'phone' => 'Telefonnummer',
            'password' => 'Passwort',
            'confirm_password' => 'Passwort bestätigen',
            'location' => 'Standort',
            'bio' => 'Bio',
            'role' => 'Rolle',
            'customer' => 'Kunde',
            'mechanic' => 'Mechaniker',
            'experience_years' => 'Jahre Erfahrung',
            'expertise' => 'Expertise',
            'hourly_rate' => 'Stundensatz',
            'timezone' => 'Zeitzone',
            'language' => 'Sprache',
            'terms' => 'Ich stimme den Nutzungsbedingungen und der Datenschutzrichtlinie zu',
            'create_account' => 'Konto erstellen',
            'have_account' => 'Haben Sie bereits ein Konto?',
            'sign_in' => 'Anmelden'
        ]
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome' => 'Willkommen zurück',
        'overview' => 'Übersicht',
        'recent_activity' => 'Letzte Aktivität',
        'quick_actions' => 'Schnellaktionen',
        'statistics' => [
            'total_cars' => 'Gesamte Autos',
            'active_cars' => 'Aktive Autos',
            'total_diagnoses' => 'Gesamte Diagnosen',
            'average_age' => 'Durchschnittsalter'
        ],
        'recent_diagnoses' => [
            'title' => 'Letzte Diagnosen',
            'no_diagnoses' => 'Keine letzten Diagnosen',
            'view_all' => 'Alle anzeigen'
        ]
    ],

    // Notifications
    'notifications' => [
        'car_added' => 'Auto hinzugefügt',
        'car_updated' => 'Auto aktualisiert',
        'car_deleted' => 'Auto gelöscht',
        'diagnosis_complete' => 'Diagnose abgeschlossen',
        'registration_success' => 'Registrierung erfolgreich',
        'login_success' => 'Anmeldung erfolgreich',
        'logout_success' => 'Abmeldung erfolgreich'
    ],

    // Validation
    'validation' => [
        'required' => 'Dieses Feld ist erforderlich',
        'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein',
        'min' => 'Dieses Feld muss mindestens :min Zeichen haben',
        'max' => 'Dieses Feld darf nicht mehr als :max Zeichen haben',
        'numeric' => 'Dieses Feld muss eine Zahl sein',
        'date' => 'Bitte geben Sie ein gültiges Datum ein',
        'unique' => 'Dieser Wert ist bereits vergeben',
        'confirmed' => 'Passwort-Bestätigung stimmt nicht überein'
    ],
    'smart_car' => 'Intelligentes Auto',
    'powered_by_ai' => 'Angetrieben von KI',
    'start_diagnosis' => 'Diagnose starten',
];
