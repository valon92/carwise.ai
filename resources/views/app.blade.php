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

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- PWA Service Worker Registration -->
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
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
