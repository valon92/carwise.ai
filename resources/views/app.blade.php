<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AI-powered car diagnosis and maintenance platform. Get instant, accurate diagnosis for your vehicle problems using advanced AI technology.">

    <title>{{ config('app.name', 'CarWise.ai') }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3b82f6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="CarWise.ai">
    <meta name="application-name" content="CarWise.ai">
    <meta name="msapplication-TileColor" content="#3b82f6">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- PWA Meta Tags -->
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-orientations" content="portrait">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="CarWise.ai - AI Car Diagnostics">
    <meta property="og:description" content="AI-powered car diagnosis and maintenance platform. Get instant, accurate diagnosis for your vehicle problems using advanced AI technology.">
    <meta property="og:image" content="{{ url('/icons/icon-512x512.png') }}">
    <meta property="og:site_name" content="CarWise.ai">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="CarWise.ai - AI Car Diagnostics">
    <meta property="twitter:description" content="AI-powered car diagnosis and maintenance platform. Get instant, accurate diagnosis for your vehicle problems using advanced AI technology.">
    <meta property="twitter:image" content="{{ url('/icons/icon-512x512.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon1.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon1.png">
    <link rel="apple-touch-icon" href="/icons/icon1.png">

    <!-- Fonts with preload -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet"></noscript>
    
    <!-- Resource hints for better performance -->
    <link rel="dns-prefetch" href="//127.0.0.1:8000">
    <link rel="preconnect" href="//127.0.0.1:8000" crossorigin>

    <!-- Google Analytics 4 -->
    @if(config('services.google_analytics.enabled') && config('services.google_analytics.measurement_id'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.measurement_id') }}', {
            page_title: document.title,
            page_location: window.location.href,
            custom_map: {
                'custom_parameter_1': 'user_type',
                'custom_parameter_2': 'car_brand',
                'custom_parameter_3': 'diagnosis_type'
            }
        });
        
        // Enhanced ecommerce tracking
        gtag('config', '{{ config('services.google_analytics.measurement_id') }}', {
            send_page_view: true,
            enhanced_ecommerce: true
        });
    </script>
    @endif

    <!-- Sentry JavaScript SDK -->
    @if(config('sentry.enabled') && config('sentry.dsn'))
    <script
        src="https://browser.sentry-cdn.com/8.0.0/bundle.min.js"
        integrity="sha384-4x3UC0fCveFI0qxK2qw1Fdum8mAYz0FhaUbHc4fE2H6PzmnMXq5X2X/UXfVpK5cF"
        crossorigin="anonymous"
    ></script>
    <script>
        Sentry.init({
            dsn: '{{ config('sentry.dsn') }}',
            environment: '{{ config('sentry.environment') }}',
            release: '{{ config('sentry.release') }}',
            sampleRate: {{ config('sentry.sample_rate') }},
            tracesSampleRate: {{ config('sentry.traces_sample_rate') }},
            integrations: [
                new Sentry.BrowserTracing({
                    // Set sampling rate for performance monitoring
                    tracePropagationTargets: [
                        'localhost',
                        '127.0.0.1',
                        /^https:\/\/yourdomain\.com\//,
                    ],
                }),
                new Sentry.Replay({
                    // Capture 10% of all sessions
                    sessionSampleRate: 0.1,
                    // Capture 100% of sessions with an error
                    errorSampleRate: 1.0,
                }),
            ],
            beforeSend(event, hint) {
                // Filter out common non-critical errors
                if (event.exception) {
                    const error = hint.originalException;
                    if (error && error.message) {
                        const message = error.message.toLowerCase();
                        if (message.includes('resizeobserver loop limit exceeded') ||
                            message.includes('non-error promise rejection captured') ||
                            message.includes('script error') ||
                            message.includes('network request failed')) {
                            return null;
                        }
                    }
                }
                
                // Add CarWise.ai specific context
                event.tags = event.tags || {};
                event.tags.platform = 'carwise-ai';
                event.tags.version = '1.0.0';
                
                return event;
            },
        });
        
        // Set user context if available
        const user = localStorage.getItem('user');
        if (user) {
            try {
                const userData = JSON.parse(user);
                Sentry.setUser({
                    id: userData.id,
                    email: userData.email,
                    username: userData.name || userData.first_name,
                    role: userData.role || 'customer'
                });
            } catch (e) {
                console.warn('Failed to parse user data for Sentry:', e);
            }
        }
        
        console.log('🔍 Sentry initialized successfully');
    </script>
    @endif

    <!-- New Relic Browser Agent -->
    @if(config('services.newrelic.enabled') && config('services.newrelic.license_key'))
    <script type="text/javascript">
        window.NREUM||(NREUM={});NREUM.info = {
            "beacon":"bam.nr-data.net",
            "errorBeacon":"bam.nr-data.net",
            "licenseKey":"{{ config('services.newrelic.license_key') }}",
            "applicationID":"{{ config('services.newrelic.account_id') }}",
            "transactionName":"{{ config('services.newrelic.app_name', 'CarWise.ai') }}",
            "queueTime":0,
            "applicationTime":0,
            "agent":"",
            "atts":""
        };
    </script>
    <script type="text/javascript" src="https://js-agent.newrelic.com/nr-1218.min.js"></script>
    <script>
        // Initialize New Relic
        if (window.newrelic) {
            // Set application name
            window.newrelic.setApplicationID('{{ config('services.newrelic.app_name', 'CarWise.ai') }}');
            
            // Set user context if available
            const user = localStorage.getItem('user');
            if (user) {
                try {
                    const userData = JSON.parse(user);
                    window.newrelic.setCustomAttribute('user_id', userData.id);
                    window.newrelic.setCustomAttribute('user_type', userData.role || 'customer');
                    window.newrelic.setCustomAttribute('user_email', userData.email);
                } catch (e) {
                    console.warn('Failed to parse user data for New Relic:', e);
                }
            }
            
            // Set environment attributes
            window.newrelic.setCustomAttribute('environment', '{{ config('app.env') }}');
            window.newrelic.setCustomAttribute('version', '1.0.0');
            window.newrelic.setCustomAttribute('platform', 'carwise-ai');
            
            console.log('📊 New Relic initialized successfully');
        }
    </script>
    @endif

    <!-- Firebase SDK -->
    @if(config('services.firebase.enabled') && config('services.firebase.messaging_sender_id'))
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
        import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js';

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.project_id') }}.firebaseapp.com",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.project_id') }}.appspot.com",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            vapidKey: "{{ config('services.firebase.vapid_key') }}"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Make messaging available globally
        window.firebase = { messaging };

        // Handle foreground messages
        onMessage(messaging, (payload) => {
            console.log('Message received in foreground:', payload);
            
            // Show notification manually for foreground messages
            if (payload.notification) {
                const notificationTitle = payload.notification.title;
                const notificationOptions = {
                    body: payload.notification.body,
                    icon: payload.notification.icon || '/icons/icon-192x192.png',
                    badge: '/icons/icon-72x72.png',
                    tag: 'carwise-notification',
                    data: payload.data
                };

                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(notificationTitle, notificationOptions);
                }
            }
        });

        console.log('🔥 Firebase initialized successfully');
    </script>
    @endif

    <!-- OneSignal SDK -->
    @if(config('services.onesignal.enabled') && config('services.onesignal.app_id'))
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ config('services.onesignal.app_id') }}",
                allowLocalhostAsSecureOrigin: true,
                notifyButton: {
                    enable: false
                },
                promptOptions: {
                    slidedown: {
                        enabled: true,
                        autoPrompt: true,
                        timeDelay: 20,
                        pageViews: 1,
                        actionMessage: "We'd like to show you notifications for the latest updates.",
                        acceptButtonText: "Allow",
                        cancelButtonText: "No Thanks"
                    }
                }
            });
        });
        console.log('📱 OneSignal initialized successfully');
    </script>
    @endif

    <!-- Pusher SDK -->
    @if(config('services.pusher.enabled') && config('services.pusher.key'))
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher globally
        window.Pusher = Pusher;
        console.log('🔌 Pusher SDK loaded successfully');
    </script>
    @endif

    <!-- Suppress HTTP errors for optional API endpoints (before app.js loads) -->
    <script>
      // Suppress console errors for optional API endpoints
      (function() {
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
          const originalFetch = window.fetch;
          window.fetch = function(...args) {
            const url = args[0];
            const urlStr = typeof url === 'string' ? url : (url instanceof Request ? url.url : url.toString());
            
            // Check if this is an optional API endpoint
            const isOptionalEndpoint = urlStr && (
              urlStr.includes('/api/carapi/real-parts') ||
              urlStr.includes('/api/carapi/models') ||
              urlStr.includes('/api/carapi/makes')
            );
            
            if (isOptionalEndpoint) {
              return originalFetch.apply(this, args).catch(() => {
                // Return a mock response to prevent console errors
                return new Response(JSON.stringify({ success: false, message: 'API not configured' }), {
                  status: 503,
                  statusText: 'Service Unavailable',
                  headers: { 'Content-Type': 'application/json' }
                });
              });
            }
            
            return originalFetch.apply(this, args);
          };
          
          // Suppress console.error for optional endpoints
          const originalError = console.error;
          console.error = function(...args) {
            const message = args.join(' ');
            if (message.includes('/api/carapi/real-parts') || 
                message.includes('/api/carapi/models') ||
                message.includes('/api/carapi/makes') ||
                (message.includes('404') && message.includes('carapi')) ||
                (message.includes('503') && message.includes('carapi'))) {
              return; // Suppress
            }
            originalError.apply(console, args);
          };
        }
      })();
    </script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- PWA Service Worker Registration (disabled on localhost/dev) -->
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                const isLocalhost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
                const isDevEnv = {{ app()->environment('local') ? 'true' : 'false' }};

                // In dev/localhost: always unregister and do NOT register.
                if (isLocalhost || isDevEnv) {
                    navigator.serviceWorker.getRegistrations()
                        .then((regs) => Promise.all(regs.map((r) => r.unregister())))
                        .then(() => {
                            console.log('[PWA] Service Worker disabled on localhost/dev (unregistered).');
                            // Also clear any cached service workers
                            if ('caches' in window) {
                                caches.keys().then(names => {
                                    names.forEach(name => caches.delete(name));
                                });
                            }
                        })
                        .catch(() => {});
                    return;
                }

                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('[PWA] Service Worker registered successfully:', registration.scope);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            if (newWorker) {
                                newWorker.addEventListener('statechange', function() {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New content is available, show update notification
                                        console.log('[PWA] New content available, please refresh.');
                                        if (confirm('New version available! Refresh to update?')) {
                                            window.location.reload();
                                        }
                                    }
                                });
                            }
                        });
                    })
                    .catch(function(error) {
                        console.log('[PWA] Service Worker registration failed:', error);
                    });
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', function(e) {
            console.log('[PWA] Install prompt triggered');
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button or notification
            if (window.showInstallPrompt) {
                window.showInstallPrompt();
            }
        });
        
        // PWA Installed
        window.addEventListener('appinstalled', function() {
            console.log('[PWA] App was installed');
            deferredPrompt = null;
        });
        
        // Connection status
        window.addEventListener('online', function() {
            console.log('[PWA] Back online');
            document.body.classList.remove('offline');
        });
        
        window.addEventListener('offline', function() {
            console.log('[PWA] Gone offline');
            document.body.classList.add('offline');
        });
        
        // Initial connection status
        if (!navigator.onLine) {
            document.body.classList.add('offline');
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
