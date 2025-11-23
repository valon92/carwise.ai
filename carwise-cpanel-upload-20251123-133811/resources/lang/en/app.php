<?php

return [
    // Navigation
    'nav' => [
        'home' => 'Home',
        'diagnose' => 'Diagnose',
        'my_cars' => 'My Cars',
        'car_parts' => 'Car Parts',
        'dashboard' => 'Dashboard',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'profile' => 'Profile',
    ],

    // Common
    'common' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'add' => 'Add',
        'search' => 'Search',
        'filter' => 'Filter',
        'loading' => 'Loading...',
        'error' => 'Error',
        'success' => 'Success',
        'warning' => 'Warning',
        'info' => 'Info',
        'yes' => 'Yes',
        'no' => 'No',
        'close' => 'Close',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'submit' => 'Submit',
        'reset' => 'Reset',
        'confirm' => 'Confirm',
    ],

    // Home Page
    'home' => [
        'title' => 'AI-Powered Car Diagnosis',
        'subtitle' => 'Get instant, accurate diagnosis for your vehicle problems using advanced AI technology',
        'get_started' => 'Get Started',
        'learn_more' => 'Learn More',
        'features' => [
            'title' => 'Why Choose CarWise.ai?',
            'ai_diagnosis' => [
                'title' => 'AI-Powered Diagnosis',
                'description' => 'Advanced artificial intelligence analyzes your car problems with high accuracy'
            ],
            'global_support' => [
                'title' => 'Global Car Support',
                'description' => 'Support for 63+ car brands from 20+ countries worldwide'
            ],
            'instant_results' => [
                'title' => 'Instant Results',
                'description' => 'Get diagnosis results in seconds, not days'
            ],
            'expert_network' => [
                'title' => 'Expert Network',
                'description' => 'Connect with certified mechanics in your area'
            ]
        ]
    ],

    // Diagnosis
    'diagnosis' => [
        'title' => 'AI-Powered Car Diagnosis',
        'subtitle' => 'Get instant, accurate diagnosis for your vehicle problems using advanced AI technology. Upload symptoms, photos, or describe the issue for comprehensive analysis.',
        'steps' => [
            'vehicle_info' => 'Vehicle Info',
            'symptoms' => 'Symptoms',
            'ai_analysis' => 'AI Analysis',
            'results' => 'Results'
        ],
        'vehicle_info' => [
            'title' => 'Vehicle Information',
            'make' => 'Make',
            'model' => 'Model',
            'year' => 'Year',
            'mileage' => 'Mileage',
            'engine_type' => 'Engine Type',
            'engine_size' => 'Engine Size',
            'select_make' => 'Select Make',
            'select_model' => 'Select a model',
            'select_make_first' => 'Please select a make first',
            'pre_filled' => 'Pre-filled from your car'
        ],
        'symptoms' => [
            'title' => 'Problem Description',
            'description' => 'Describe the problem you\'re experiencing with your vehicle',
            'common_symptoms' => 'Common Symptoms',
            'select_symptoms' => 'Select symptoms that apply to your vehicle',
            'upload_photos' => 'Upload Photos (Optional)',
            'drag_drop' => 'Drag and drop photos here, or click to select',
            'max_files' => 'Maximum 5 files, 10MB each'
        ],
        'results' => [
            'title' => 'Diagnosis Results',
            'problem_title' => 'Problem Identified',
            'confidence' => 'Confidence Score',
            'severity' => 'Severity',
            'description' => 'Description',
            'likely_causes' => 'Likely Causes',
            'recommended_actions' => 'Recommended Actions',
            'estimated_costs' => 'Estimated Costs',
            'ai_insights' => 'AI Insights',
            'immediate_attention' => 'Immediate Attention Required',
            'cost_note' => '* Cost estimates are approximate and may vary based on location and specific vehicle requirements.'
        ]
    ],

    // My Cars
    'my_cars' => [
        'title' => 'My Cars',
        'subtitle' => 'Manage your vehicles, track maintenance, and view diagnosis history all in one place',
        'add_car' => 'Add New Car',
        'add_first_car' => 'Add Your First Car',
        'no_cars' => 'No cars added yet',
        'no_cars_description' => 'Start by adding your first car to begin tracking maintenance and getting AI-powered diagnoses.',
        'statistics' => [
            'total_cars' => 'Total Cars',
            'active_cars' => 'Active Cars',
            'total_diagnoses' => 'Total Diagnoses',
            'average_age' => 'Average Age'
        ],
        'form' => [
            'title' => 'Add New Car',
            'edit_title' => 'Edit Car',
            'brand' => 'Brand',
            'model' => 'Model',
            'year' => 'Year',
            'vin' => 'VIN',
            'license_plate' => 'License Plate',
            'color' => 'Color',
            'fuel_type' => 'Fuel Type',
            'transmission' => 'Transmission',
            'mileage' => 'Mileage',
            'purchase_date' => 'Purchase Date',
            'notes' => 'Notes',
            'status' => 'Status',
            'select_brand' => 'Select a brand',
            'select_model' => 'Select a model',
            'select_brand_first' => 'Please select a brand first',
            'saving' => 'Saving...',
            'update_car' => 'Update Car',
            'add_car' => 'Add Car'
        ],
        'actions' => [
            'diagnose' => 'Diagnose',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'view_history' => 'View History'
        ]
    ],


    // Authentication
    'auth' => [
        'login' => [
            'title' => 'Welcome Back',
            'subtitle' => 'Sign in to your account to continue',
            'email' => 'Email Address',
            'password' => 'Password',
            'remember_me' => 'Remember Me',
            'forgot_password' => 'Forgot Password?',
            'sign_in' => 'Sign In',
            'no_account' => 'Don\'t have an account?',
            'sign_up' => 'Sign Up'
        ],
        'register' => [
            'title' => 'Create Account',
            'subtitle' => 'Join CarWise.ai and start diagnosing your car problems with AI',
            'personal_info' => 'Personal Information',
            'contact_info' => 'Contact Information',
            'professional_info' => 'Professional Information (Optional)',
            'preferences' => 'Preferences',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'location' => 'Location',
            'bio' => 'Bio',
            'role' => 'Role',
            'customer' => 'Customer',
            'mechanic' => 'Mechanic',
            'experience_years' => 'Years of Experience',
            'expertise' => 'Expertise',
            'hourly_rate' => 'Hourly Rate',
            'timezone' => 'Timezone',
            'language' => 'Language',
            'terms' => 'I agree to the Terms of Service and Privacy Policy',
            'create_account' => 'Create Account',
            'have_account' => 'Already have an account?',
            'sign_in' => 'Sign In'
        ]
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome' => 'Welcome back',
        'overview' => 'Overview',
        'recent_activity' => 'Recent Activity',
        'quick_actions' => 'Quick Actions',
        'statistics' => [
            'total_cars' => 'Total Cars',
            'active_cars' => 'Active Cars',
            'total_diagnoses' => 'Total Diagnoses',
            'average_age' => 'Average Age'
        ],
        'recent_diagnoses' => [
            'title' => 'Recent Diagnoses',
            'no_diagnoses' => 'No recent diagnoses',
            'view_all' => 'View All'
        ]
    ],

    // Notifications
    'notifications' => [
        'car_added' => 'Car Added',
        'car_updated' => 'Car Updated',
        'car_deleted' => 'Car Deleted',
        'diagnosis_complete' => 'Diagnosis Complete',
        'registration_success' => 'Registration Successful',
        'login_success' => 'Login Successful',
        'logout_success' => 'Logout Successful'
    ],

    // Validation
    'validation' => [
        'required' => 'This field is required',
        'email' => 'Please enter a valid email address',
        'min' => 'This field must be at least :min characters',
        'max' => 'This field must not exceed :max characters',
        'numeric' => 'This field must be a number',
        'date' => 'Please enter a valid date',
        'unique' => 'This value is already taken',
        'confirmed' => 'Password confirmation does not match'
    ],
    'smart_car' => 'Smart Car',
    'powered_by_ai' => 'Powered by AI',
    'start_diagnosis' => 'Start Diagnosis',
    'smart_car_diagnosis_platform' => 'Smart car diagnosis platform powered by AI. Get instant, accurate vehicle diagnostics and find quality car parts.',

    // Car Parts
    'car_parts' => [
        'title' => 'Authorized Car Parts',
        'subtitle' => 'Find high-quality, certified car parts for your vehicle. All parts are manufacturer-authorized and come with warranty.',
        'search_placeholder' => 'Search for parts...',
        'all_categories' => 'All Categories',
        'all_manufacturers' => 'All Manufacturers',
        'featured_parts' => 'Featured Parts',
        'all_parts' => 'All Parts',
        'sort_by_name' => 'Sort by Name',
        'sort_by_price' => 'Sort by Price',
        'sort_by_rating' => 'Sort by Rating',
        'sort_by_newest' => 'Sort by Newest',
        'previous' => 'Previous',
        'next' => 'Next',
        'no_parts_found' => 'No Parts Found',
        'no_parts_description' => 'Try adjusting your search criteria or filters to find the parts you need.',
        'clear_filters' => 'Clear Filters',
        'category' => 'Category',
        'manufacturer' => 'Manufacturer',
        'difficulty' => 'Difficulty',
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of Stock',
        'description' => 'Description',
        'part_number' => 'Part Number',
        'quality' => 'Quality',
        'installation_info' => 'Installation Information',
        'installation_time' => 'Installation Time',
        'warranty' => 'Warranty',
        'weight' => 'Weight',
        'compatibility' => 'Compatibility',
        'compatible_brands' => 'Compatible Brands',
        'compatible_models' => 'Compatible Models',
        'not_specified' => 'Not specified',
        'close' => 'Close',
        'add_to_cart' => 'Add to Cart',
        'categories' => [
            'engine' => 'Engine',
            'brakes' => 'Brakes',
            'electrical' => 'Electrical',
            'suspension' => 'Suspension',
            'transmission' => 'Transmission',
            'exhaust' => 'Exhaust',
            'cooling' => 'Cooling',
            'fuel' => 'Fuel System',
            'body' => 'Body Parts',
            'interior' => 'Interior',
            'exterior' => 'Exterior',
            'lighting' => 'Lighting',
        ],
        'difficulty_levels' => [
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard',
            'professional' => 'Professional Only',
        ],
    ],
];
