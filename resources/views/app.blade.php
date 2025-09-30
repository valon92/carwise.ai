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
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="CarWise.ai">

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
    
    <!-- Service Worker - Cleanup without reload -->
    <script>
        console.log('SW Cleanup: Starting cleanup...');
        
        if ('serviceWorker' in navigator) {
            // Unregister service workers without reload
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                console.log('SW Cleanup: Found', registrations.length, 'registrations');
                
                const unregisterPromises = registrations.map(function(registration) {
                    console.log('SW Cleanup: Unregistering', registration.scope);
                    return registration.unregister();
                });
                
                return Promise.all(unregisterPromises);
            }).then(function() {
                console.log('SW Cleanup: All service workers unregistered');
                
                // Clear caches without reload
                if ('caches' in window) {
                    return caches.keys().then(function(cacheNames) {
                        console.log('SW Cleanup: Found caches:', cacheNames);
                        
                        const deletePromises = cacheNames.map(function(cacheName) {
                            console.log('SW Cleanup: Deleting cache:', cacheName);
                            return caches.delete(cacheName);
                        });
                        
                        return Promise.all(deletePromises);
                    });
                }
            }).then(function() {
                console.log('SW Cleanup: All caches cleared - cleanup complete');
            }).catch(function(error) {
                console.error('SW Cleanup: Error during cleanup:', error);
            });
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
