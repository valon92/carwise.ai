<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
    <!-- Modern Hero Section -->
    <section class="relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 opacity-90">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
          <div class="absolute top-20 right-20 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
          <div class="absolute bottom-10 left-1/3 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
        </div>
      </div>
      
      <!-- Hero Content -->
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
          <div class="animate-fade-in">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-8">
              <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse mr-3"></div>
              <span class="text-white/90 text-sm font-medium">AI-Powered Car Diagnostics</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold text-white mb-8 leading-tight">
              <span class="block">CarWise</span>
              <span class="block bg-gradient-to-r from-blue-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">
                .ai
              </span>
            </h1>
            
            <p class="text-2xl md:text-3xl text-white/90 mb-4 max-w-4xl mx-auto leading-relaxed font-light">
              The Future of Car Diagnostics
            </p>
            <p class="text-lg md:text-xl text-white/80 mb-12 max-w-3xl mx-auto leading-relaxed">
              Get instant, accurate vehicle diagnostics powered by advanced AI technology. 
              Diagnose, repair, and maintain your car with confidence.
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
              <router-link 
                to="/diagnose" 
                class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl font-semibold transition-all duration-300 flex items-center gap-3 shadow-2xl hover:shadow-3xl transform hover:scale-105"
              >
                <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                Start AI Diagnosis
              </router-link>
              <router-link 
                to="/register" 
                class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-2xl border border-white/20 text-white font-semibold transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Get Started Free
              </router-link>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-3xl font-bold text-white mb-2">10K+</div>
                <div class="text-white/80 text-sm">Cars Diagnosed</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-3xl font-bold text-white mb-2">95%</div>
                <div class="text-white/80 text-sm">Accuracy Rate</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-3xl font-bold text-white mb-2">24/7</div>
                <div class="text-white/80 text-sm">AI Support</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 bg-white/90 backdrop-blur-xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full border border-blue-200 dark:border-blue-700 mb-6">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span class="text-blue-700 dark:text-blue-300 text-sm font-medium">How It Works</span>
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">
            Get Instant Diagnosis in
            <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">3 Simple Steps</span>
          </h2>
          <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
            Our AI-powered platform makes car diagnostics simple, fast, and accurate
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-12">
          <!-- Step 1 -->
          <div class="relative group">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
              <div class="absolute -top-4 -left-4 w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                1
              </div>
              <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Upload & Describe</h3>
              <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                Upload photos, videos, or describe your vehicle issues. Our AI accepts multiple input formats for comprehensive analysis.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="relative group">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
              <div class="absolute -top-4 -left-4 w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                2
              </div>
              <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">AI Analysis</h3>
              <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                Our advanced AI analyzes your data using machine learning algorithms trained on millions of vehicle diagnostics.
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="relative group">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
              <div class="absolute -top-4 -left-4 w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                3
              </div>
              <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Get Results</h3>
              <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                Receive detailed diagnosis reports with solutions, part recommendations, and cost estimates in seconds.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Showcase Section -->
    <section class="py-24 bg-white/90 backdrop-blur-xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-full border border-emerald-200 dark:border-emerald-700 mb-6">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-emerald-700 dark:text-emerald-300 text-sm font-medium">Powerful Features</span>
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">
            Everything You Need for
            <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Smart Car Care</span>
          </h2>
          <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
            Comprehensive AI-powered tools for diagnosis, maintenance, and car parts shopping
          </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <!-- Left Side - Features List -->
          <div class="space-y-8">
            <div class="flex items-start space-x-4 group">
              <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">AI-Powered Diagnosis</h3>
                <p class="text-slate-600 dark:text-slate-400">Advanced machine learning algorithms analyze your vehicle data for accurate problem identification.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4 group">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Smart Parts Marketplace</h3>
                <p class="text-slate-600 dark:text-slate-400">Find and purchase the exact parts you need with AI recommendations and real-time pricing.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4 group">
              <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Vehicle Management</h3>
                <p class="text-slate-600 dark:text-slate-400">Track maintenance schedules, service history, and get smart reminders for your vehicles.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4 group">
              <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">24/7 Live Support</h3>
                <p class="text-slate-600 dark:text-slate-400">Get instant help from our AI assistant and connect with certified mechanics when needed.</p>
              </div>
            </div>
          </div>

          <!-- Right Side - Visual -->
          <div class="relative">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl">
              <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                  <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">AI Diagnosis Dashboard</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">Real-time analysis with detailed insights and recommendations</p>
                <div class="space-y-3">
                  <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Engine Health</span>
                    <span class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold">Excellent</span>
                  </div>
                  <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Brake System</span>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-semibold">Needs Check</span>
                  </div>
                  <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Battery Status</span>
                    <span class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold">Good</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Car Parts Marketplace Section -->
    <section class="py-24 bg-white/90 backdrop-blur-xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-full border border-orange-200 dark:border-orange-700 mb-6">
            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="text-orange-700 dark:text-orange-300 text-sm font-medium">Smart Marketplace</span>
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">
            Find the Perfect
            <span class="bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">Car Parts</span>
          </h2>
          <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
            AI-powered recommendations for genuine parts with real-time pricing and availability
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Smart Search</h3>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
              Find parts by vehicle make, model, year, or VIN with AI-powered compatibility matching.
            </p>
          </div>

          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Best Prices</h3>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
              Compare prices from multiple suppliers and get the best deals with real-time price alerts.
            </p>
          </div>

          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Quality Guaranteed</h3>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
              All parts come with manufacturer warranties and quality guarantees from trusted suppliers.
            </p>
          </div>
        </div>

        <div class="text-center">
          <router-link 
            to="/car-parts" 
            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-2xl font-semibold transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105"
          >
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Browse Car Parts Store
          </router-link>
        </div>
      </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-24 relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 opacity-90">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
          <div class="absolute top-20 right-20 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
          <div class="absolute bottom-10 left-1/3 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
        </div>
      </div>
      
      <div class="relative max-w-5xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
          <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-6">
            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse mr-3"></div>
            <span class="text-white/90 text-sm font-medium">Join the Future of Car Care</span>
          </div>
        </div>
        
        <h2 class="text-4xl md:text-6xl font-bold text-white mb-8 leading-tight">
          Ready to Transform Your
          <span class="block bg-gradient-to-r from-blue-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">
            Car Experience?
          </span>
        </h2>
        
        <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto leading-relaxed">
          Join thousands of car owners who trust CarWise.ai for accurate diagnostics, 
          smart maintenance, and seamless car parts shopping.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
          <router-link 
            to="/register" 
            class="group px-8 py-4 bg-white hover:bg-white/90 text-indigo-600 rounded-2xl font-semibold transition-all duration-300 flex items-center gap-3 shadow-2xl hover:shadow-3xl transform hover:scale-105"
          >
            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Start Free Trial
          </router-link>
          <router-link 
            to="/diagnose" 
            class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-2xl border border-white/20 text-white font-semibold transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Try AI Diagnosis
          </router-link>
        </div>
        
        <!-- Trust Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
            <div class="text-3xl font-bold text-white mb-2">10K+</div>
            <div class="text-white/80 text-sm">Happy Customers</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
            <div class="text-3xl font-bold text-white mb-2">95%</div>
            <div class="text-white/80 text-sm">Diagnosis Accuracy</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
            <div class="text-3xl font-bold text-white mb-2">24/7</div>
            <div class="text-white/80 text-sm">AI Support</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Home',
  setup() {
    const isVisible = ref(false)

    onMounted(() => {
      isVisible.value = true
    })

    return {
      isVisible
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Glass morphism effect */
.glass {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Hover effects */
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}
</style>
