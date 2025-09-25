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
    
    <!-- Performance optimizations -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    <!-- Critical CSS preload -->
    <link rel="preload" href="{{ asset('build/assets/app.css') }}" as="style">
    
    <!-- Resource hints for better performance -->
    <link rel="dns-prefetch" href="//127.0.0.1:8000">
    <link rel="preconnect" href="//127.0.0.1:8000" crossorigin>
    
    <!-- Preload critical resources -->
    <link rel="modulepreload" href="{{ asset('build/assets/app.js') }}">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
