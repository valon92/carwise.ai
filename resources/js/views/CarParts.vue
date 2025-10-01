<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-900 dark:via-secondary-800 dark:to-primary-900">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full"></div>
          <div class="absolute top-32 right-20 w-16 h-16 bg-white/5 rounded-full"></div>
          <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white/10 rounded-full"></div>
        </div>
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center text-white">
          <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full mb-8 shadow-2xl">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
          </div>
          <h1 class="text-5xl md:text-6xl font-bold mb-6">
            AI-Powered Parts Discovery
          </h1>
          <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto mb-8">
            Get personalized car parts recommendations based on your AI diagnosis. Find the exact parts you need to fix your vehicle issues.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link to="/diagnose" class="btn-primary bg-white text-primary-600 hover:bg-white/90 px-8 py-3 text-lg font-semibold inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              Start AI Diagnosis
            </router-link>
            <button @click="scrollToSearch" class="btn-secondary border-white text-white hover:bg-white hover:text-primary-600 px-8 py-3 text-lg font-semibold">
              Find Parts
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="relative -mt-16 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">{{ totalParts }}+</div>
            <div class="text-secondary-600 dark:text-secondary-400">Available Parts</div>
          </div>
          <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">12</div>
            <div class="text-secondary-600 dark:text-secondary-400">Categories</div>
          </div>
          <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">50+</div>
            <div class="text-secondary-600 dark:text-secondary-400">Brands</div>
          </div>
          <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">24/7</div>
            <div class="text-secondary-600 dark:text-secondary-400">Support</div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Diagnosis Integration Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-8 border border-blue-200 dark:border-blue-800">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
          </div>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Get Personalized Parts Recommendations
          </h2>
          <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Upload photos of your car issues or describe symptoms to get AI-powered diagnosis and exact parts recommendations.
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Step 1 -->
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Upload Photos</h3>
            <p class="text-gray-600 dark:text-gray-400">Take photos of your car issues or symptoms</p>
          </div>
          
          <!-- Step 2 -->
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">AI Analysis</h3>
            <p class="text-gray-600 dark:text-gray-400">Get instant diagnosis and problem identification</p>
          </div>
          
          <!-- Step 3 -->
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Find Parts</h3>
            <p class="text-gray-600 dark:text-gray-400">Get exact parts recommendations with purchase links</p>
          </div>
        </div>
        
        <div class="text-center mt-8">
          <router-link to="/diagnose" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Start AI Diagnosis Now
          </router-link>
        </div>
      </div>
    </div>

    <!-- Recent Diagnoses Section -->
    <div v-if="showRecentDiagnoses" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-sm border border-gray-200 dark:border-secondary-700 p-8">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Recent Diagnoses</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">Find parts for your diagnosed issues</p>
            </div>
          </div>
          <router-link to="/diagnose" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">
            View All Diagnoses →
          </router-link>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="diagnosis in recentDiagnoses" :key="diagnosis.id" 
               class="border border-gray-200 dark:border-secondary-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ diagnosis.vehicle }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ diagnosis.issue }}</p>
              </div>
              <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                {{ diagnosis.status }}
              </span>
            </div>
            
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Suggested Parts:</h4>
              <div class="flex flex-wrap gap-2">
                <span v-for="part in diagnosis.suggested_parts" :key="part" 
                      class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-full">
                  {{ part }}
                </span>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ diagnosis.diagnosis_date }}</span>
              <button @click="searchForDiagnosisParts(diagnosis)" 
                      class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                Find Parts
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

      <!-- Search and Filters - Alibaba Style -->
      <div id="search-section" class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm border border-gray-200 dark:border-secondary-700 mb-6">
        <!-- Main Search Bar -->
        <div class="p-4 border-b border-gray-200 dark:border-secondary-700">
          <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
            <div class="relative">
              <input 
                v-model="searchQuery"
                @input="debouncedSearch"
                type="text" 
                  class="w-full h-12 pl-12 pr-4 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                  placeholder="Search for car parts, brands, or part numbers..."
              />
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

            <!-- Search Button -->
            <button 
              @click="filterParts"
              class="h-12 px-8 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search
            </button>
          </div>
        </div>
        
        <!-- Filters Row -->
        <div class="p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Category Filter -->
          <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
              <select v-model="selectedCategory" @change="filterParts" class="w-full h-10 px-3 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">All Categories</option>
              <option v-for="category in categories" :key="category" :value="category">
                  {{ category.charAt(0).toUpperCase() + category.slice(1) }}
              </option>
            </select>
          </div>

            <!-- Brand Filter -->
          <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
              <select v-model="selectedManufacturer" @change="filterParts" class="w-full h-10 px-3 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">All Brands</option>
              <option v-for="manufacturer in manufacturers" :key="manufacturer" :value="manufacturer">
                {{ manufacturer }}
              </option>
            </select>
          </div>

            <!-- Price Range -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
              <select class="w-full h-10 px-3 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Any Price</option>
                <option value="0-50">$0 - $50</option>
                <option value="50-100">$50 - $100</option>
                <option value="100-200">$100 - $200</option>
                <option value="200-500">$200 - $500</option>
                <option value="500+">$500+</option>
              </select>
            </div>

            <!-- Sort By -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
              <select v-model="sortBy" @change="filterParts" class="w-full h-10 px-3 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="name">Name</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="rating">Rating</option>
                <option value="newest">Newest</option>
              </select>
            </div>
          </div>
          
          <!-- VIN Lookup -->
          <div class="mt-4 p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
            <div class="flex items-center gap-3 mb-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Vehicle Lookup</h3>
            </div>
            <div class="flex gap-3">
              <input 
                v-model="vinInput"
                type="text" 
                placeholder="Enter 17-character VIN (e.g., 1HGBH41JXMN109186)"
                class="flex-1 h-10 px-3 border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                maxlength="17"
              />
              <button 
                @click="lookupVehicleByVIN"
                class="h-10 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Lookup
              </button>
            </div>
            <div v-if="vehicleData" class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <div class="text-sm text-green-800 dark:text-green-200">
                <strong>Vehicle Found:</strong> {{ vehicleData.displayName }}
                <span v-if="vehicleData.engine" class="ml-2 text-green-600 dark:text-green-300">({{ vehicleData.engine }})</span>
              </div>
            </div>
          </div>

          <!-- Advanced Actions -->
          <div class="mt-4 flex flex-wrap gap-3">
            <button 
              @click="searchPublicAPIs"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search Public APIs
            </button>
            <button 
              @click="comparePricesAcrossAPIs"
              class="px-4 py-2 border border-green-600 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Compare Prices
            </button>
            <button 
              @click="searchPartnerParts(searchQuery)"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search Partners
            </button>
            <button 
              @click="clearFilters"
              class="px-4 py-2 border border-gray-300 dark:border-secondary-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-secondary-700 rounded-lg text-sm font-medium transition-colors duration-200"
            >
              Clear Filters
            </button>
            <button 
              @click="showMockProducts = !showMockProducts"
              class="px-4 py-2 border border-purple-600 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              {{ showMockProducts ? 'Hide' : 'Show' }} Demo Products
            </button>
          </div>
        </div>
      </div>


      <!-- Public API Results Section -->
      <div v-if="publicAPIParts.length > 0 && showMockProducts" class="mb-12">
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm border border-gray-200 dark:border-secondary-700 overflow-hidden">
          <!-- Section Header -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Public API Results</h2>
                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium rounded-full">
                      DEMO
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ publicAPIParts.length }} parts found from eBay & Amazon</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Live data</span>
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              </div>
            </div>
          </div>
          
          <!-- Results Grid -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <div 
                v-for="part in publicAPIParts" 
                :key="`${part.source}-${part.id}`"
                class="group cursor-pointer bg-white dark:bg-secondary-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-secondary-700 overflow-hidden"
                @click="handleAffiliateClick(part)"
              >
                <!-- Source Badge -->
                <div class="relative">
                  <div class="absolute top-2 left-2 z-10">
                    <span class="px-2 py-1 text-xs font-medium rounded-full shadow-sm"
                          :class="part.source === 'ebay' ? 'bg-blue-500 text-white' : 'bg-orange-500 text-white'">
                      {{ part.source.toUpperCase() }}
                    </span>
                  </div>
                  
                  <!-- Product Image -->
                  <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-secondary-700">
                    <img 
                      :src="part.image_url || '/images/parts/placeholder.jpg'" 
                      :alt="part.name"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                      loading="lazy"
                    />
                    
                    <!-- Condition Badge -->
                    <div class="absolute top-2 right-2">
                      <span class="px-2 py-1 text-xs font-medium rounded-full shadow-sm bg-white/90 dark:bg-secondary-800/90 text-gray-700 dark:text-gray-300">
                        {{ part.condition || 'New' }}
                      </span>
                    </div>
                    
                    <!-- AI Recommended Badge -->
                    <div v-if="part.ai_recommended" class="absolute bottom-2 right-2">
                      <span class="px-2 py-1 text-xs font-medium rounded-full shadow-sm bg-gradient-to-r from-blue-500 to-purple-500 text-white">
                        AI Recommended
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Product Info -->
                <div class="p-4 space-y-3">
                  <!-- Brand -->
                  <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    {{ part.brand || 'Unknown Brand' }}
                  </div>
                  
                  <!-- Product Name -->
                  <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                    {{ part.name }}
                  </h3>
                  
                  <!-- Rating and Reviews -->
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                      <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                      <span class="text-xs text-gray-600 dark:text-gray-400">
                        {{ part.rating.toFixed(1) }}
                      </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ part.review_count }} reviews
                    </div>
                  </div>
                  
                  <!-- Price -->
                  <div class="flex items-center justify-between">
                    <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                      {{ part.formatted_price }}
                    </div>
                    <div class="text-right">
                      <div v-if="part.prime_eligible" class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                        Prime ✓
                      </div>
                      <div v-else-if="part.shipping_cost" class="text-xs text-gray-500 dark:text-gray-400">
                        +${{ part.shipping_cost }} shipping
                      </div>
                      <div v-else class="text-xs text-green-600 dark:text-green-400">
                        Free shipping
                      </div>
                    </div>
                  </div>
                  
                  <!-- Action Button -->
                  <button 
                    @click.stop="handleAffiliateClick(part)"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    View on {{ part.source === 'ebay' ? 'eBay' : 'Amazon' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories Section -->
      <div id="categories-section" class="mb-12">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-secondary-900 dark:text-white mb-4">Browse by Category</h2>
          <p class="text-lg text-secondary-600 dark:text-secondary-400">Find the right parts for your vehicle</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div 
            v-for="category in categories" 
            :key="category"
            @click="selectCategory(category)"
            class="group cursor-pointer bg-white dark:bg-secondary-800 rounded-xl p-6 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
            :class="selectedCategory === category ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20' : ''"
          >
            <div class="w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <svg v-if="category === 'engine'" class="w-full h-full text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <svg v-else-if="category === 'brakes'" class="w-full h-full text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              <svg v-else-if="category === 'electrical'" class="w-full h-full text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              <svg v-else-if="category === 'suspension'" class="w-full h-full text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <svg v-else-if="category === 'transmission'" class="w-full h-full text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <svg v-else class="w-full h-full text-secondary-600 dark:text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
              </svg>
            </div>
            <h3 class="font-semibold text-secondary-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
              {{ category.charAt(0).toUpperCase() + category.slice(1) }}
            </h3>
          </div>
        </div>
      </div>

      <!-- Featured Parts -->
      <div v-if="featuredParts.length > 0 && !searchQuery" class="mb-12">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
        </div>
          <h2 class="text-3xl font-bold text-secondary-900 dark:text-white mb-4">AI Recommended Parts</h2>
          <p class="text-lg text-secondary-600 dark:text-secondary-400 max-w-2xl mx-auto">
            Smart recommendations based on your vehicle and common issues. Get personalized suggestions with AI diagnosis.
          </p>
        </div>
        
        <!-- Compact Grid Layout -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
          <div 
            v-for="part in featuredParts.slice(0, 12)" 
            :key="part.id"
            class="group cursor-pointer bg-white dark:bg-secondary-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden border border-gray-100 dark:border-secondary-700"
            @click="viewPart(part)"
          >
            <!-- Compact Image Container -->
            <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-secondary-700 dark:to-secondary-800">
              <img 
                :src="part.image_url || getBrandImage(part.brand)" 
                :alt="part.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              
              <!-- Featured Badge -->
              <div class="absolute top-2 right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-md">
                ⭐
            </div>
              
              <!-- Stock Indicator -->
              <div class="absolute bottom-2 right-2">
                <div class="w-3 h-3 rounded-full shadow-md"
                     :class="part.stock_quantity > 0 ? 'bg-green-500' : 'bg-red-500'">
                </div>
              </div>
              
              <!-- Brand Logo Overlay -->
              <div class="absolute bottom-2 left-2 bg-white/90 dark:bg-secondary-800/90 backdrop-blur-sm rounded-lg p-1 shadow-sm">
                <img 
                  :src="getBrandLogo(part.brand)" 
                  :alt="part.brand"
                  class="w-4 h-4 object-contain"
                  @error="handleImageError"
                />
              </div>
            </div>
            
            <!-- Compact Content -->
            <div class="p-3 space-y-2">
              <!-- Category Badge -->
              <div class="flex justify-center">
                <span class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs font-medium rounded-full">
                  {{ part.category.charAt(0).toUpperCase() + part.category.slice(1) }}
                </span>
              </div>
              
              <!-- Title -->
              <h3 class="text-sm font-bold text-secondary-900 dark:text-white text-center line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                {{ part.name }}
              </h3>
              
              <!-- Price and Rating -->
              <div class="flex items-center justify-between">
                <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                  {{ part.formatted_price }}
                </div>
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <span class="text-xs font-medium text-secondary-600 dark:text-secondary-400">
                    {{ parseFloat(part.rating).toFixed(1) }}
                </span>
              </div>
            </div>
              
              <!-- Quick Action Button -->
              <button 
                @click.stop="viewPart(part)"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-300 hover:shadow-lg text-sm"
              >
                View Details
              </button>
          </div>
        </div>
      </div>

        <!-- View All Button -->
        <div class="text-center mt-8">
          <router-link to="/diagnose" class="btn-primary bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Get AI Diagnosis
          </router-link>
        </div>
          </div>
          
      <!-- Partner Results -->
      <div v-if="showPartnerResults && Object.keys(partnerParts).length > 0" class="mb-12">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-secondary-900 dark:text-white mb-4">Partner Results</h2>
          <p class="text-lg text-secondary-600 dark:text-secondary-400">Parts from authorized global partners</p>
        </div>
        
        <div v-for="(partnerData, partnerId) in partnerParts" :key="partnerId" class="mb-8">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white dark:bg-secondary-800 rounded-xl flex items-center justify-center shadow-lg">
                  <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ partnerData.partner.name.charAt(0) }}
                  </span>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-secondary-900 dark:text-white">{{ partnerData.partner.name }}</h3>
                  <p class="text-sm text-secondary-600 dark:text-secondary-400">
                    {{ partnerData.count }} parts found • {{ partnerData.partner.commission_rate }}% commission
                  </p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm text-green-600 dark:text-green-400 font-medium">Authorized Partner</div>
                <div class="text-xs text-secondary-500 dark:text-secondary-500">Global Shipping</div>
              </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
              v-for="part in partnerData.parts.slice(0, 8)" 
              :key="`${partnerId}-${part.partner_part_id}`"
              class="group cursor-pointer bg-white dark:bg-secondary-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden border border-gray-100 dark:border-secondary-700"
            >
              <!-- Image Container -->
              <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-secondary-700 dark:to-secondary-800">
                <div class="aspect-w-16 aspect-h-12 relative">
                  <img 
                    :src="part.image_url || getBrandImage(part.brand)" 
                :alt="part.name"
                    class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700"
                    loading="lazy"
                  />
                  <!-- Gradient Overlay -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                  
                  <!-- Partner Badge -->
                  <div class="absolute top-3 left-3 bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-md">
                    {{ partnerData.partner.name }}
            </div>
                  
                  <!-- Stock Status -->
                  <div class="absolute bottom-3 right-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold shadow-md"
                          :class="part.stock_quantity > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                      {{ part.stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Content Section -->
              <div class="p-4 space-y-3">
                <!-- Title and Category -->
                <div>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full">
                      {{ part.category.charAt(0).toUpperCase() + part.category.slice(1) }}
                    </span>
                  </div>
                  <h3 class="text-lg font-bold text-secondary-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                {{ part.name }}
              </h3>
                  <p class="text-secondary-600 dark:text-secondary-400 text-sm line-clamp-2">
                {{ part.description }}
              </p>
                </div>
                
                <!-- Rating and Price -->
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-1">
                    <div class="flex text-yellow-400">
                      <svg v-for="i in 5" :key="i" class="w-3 h-3" :class="i <= Math.floor(parseFloat(part.rating)) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                </div>
                    <span class="text-xs font-medium text-secondary-600 dark:text-secondary-400">
                      {{ parseFloat(part.rating).toFixed(1) }}
                  </span>
                </div>
                  <div class="text-right">
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                      ${{ parseFloat(part.price).toFixed(2) }}
                    </div>
                    <div class="text-xs text-green-600 dark:text-green-400 font-medium">
                      Free Shipping
                    </div>
                  </div>
              </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                  <button 
                    @click="buyFromPartner(partnerId, part.partner_part_id)"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-300 hover:shadow-lg text-sm"
                  >
                    Buy Now
                  </button>
                  <button 
                    @click="viewPart(part)"
                    class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-300 hover:shadow-lg"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Show More Button -->
          <div v-if="partnerData.parts.length > 8" class="text-center mt-6">
            <button class="btn-secondary border-blue-600 text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-medium">
              View All {{ partnerData.count }} Parts from {{ partnerData.partner.name }}
            </button>
          </div>
        </div>
      </div>

      <!-- Price Comparison -->
      <div v-if="priceComparison.length > 0" class="mb-12">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-secondary-900 dark:text-white mb-4">Price Comparison</h2>
          <p class="text-lg text-secondary-600 dark:text-secondary-400">Compare prices across all partners</p>
        </div>
        
        <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-secondary-700">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Partner</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Part Name</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Brand</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-secondary-700">
                <tr v-for="(part, index) in priceComparison" :key="index" class="hover:bg-gray-50 dark:hover:bg-secondary-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                          {{ part.partner_name.charAt(0) }}
                </span>
                      </div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ part.partner_name }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 dark:text-white">{{ part.part_name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ part.part_number }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ part.brand }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-lg font-bold text-green-600 dark:text-green-400">
                      ${{ parseFloat(part.price).toFixed(2) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ part.currency }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                          :class="part.stock > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'">
                      {{ part.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex text-yellow-400 mr-1">
                        <svg v-for="i in 5" :key="i" class="w-3 h-3" :class="i <= Math.floor(part.rating) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                      </div>
                      <span class="text-sm text-gray-600 dark:text-gray-400">{{ part.rating.toFixed(1) }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button 
                      @click="buyFromPartner(part.partner_id, part.part_id)"
                      :disabled="part.stock === 0"
                      class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:cursor-not-allowed"
                    >
                      Buy Now
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
              </div>

      <!-- Results Header - Alibaba Style -->
      <div v-if="parts.length > 0" class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              Car Parts
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ totalParts }} products found
                </span>
          </div>

          <!-- View Options -->
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">View:</span>
              <button class="p-2 border border-gray-300 dark:border-secondary-600 rounded-lg hover:bg-gray-50 dark:hover:bg-secondary-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
              </button>
              <button class="p-2 border border-gray-300 dark:border-secondary-600 rounded-lg hover:bg-gray-50 dark:hover:bg-secondary-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
              </button>
            </div>
            
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">Sort by:</span>
              <select v-model="sortBy" @change="sortParts" class="h-8 px-3 text-sm border border-gray-300 dark:border-secondary-600 rounded-lg bg-white dark:bg-secondary-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="name">Name</option>
                <option value="price">Price: Low to High</option>
                <option value="rating">Rating</option>
                <option value="created_at">Newest</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Grid - Alibaba Style -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        <div 
          v-for="part in parts" 
          :key="part.id"
          class="group cursor-pointer bg-white dark:bg-secondary-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-secondary-700 overflow-hidden"
          @click="viewPart(part)"
        >
          <!-- Product Image -->
          <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-secondary-700">
            <img 
              :src="part.image_url || getBrandImage(part.brand)" 
              :alt="part.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
              loading="lazy"
            />
            
            <!-- Stock Badge -->
            <div class="absolute top-2 right-2">
              <span class="px-2 py-1 text-xs font-medium rounded-full shadow-sm"
                    :class="part.stock_quantity > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                {{ part.stock_quantity > 0 ? 'In Stock' : 'Out' }}
              </span>
            </div>
            
            <!-- Brand Logo -->
            <div class="absolute bottom-2 left-2 bg-white/90 dark:bg-secondary-800/90 rounded p-1 shadow-sm">
              <img 
                :src="getBrandLogo(part.brand)" 
                :alt="part.brand"
                class="w-4 h-4 object-contain"
                @error="handleImageError"
              />
            </div>
          </div>
          
          <!-- Product Info -->
          <div class="p-3 space-y-2">
            <!-- Category -->
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
              {{ part.category }}
            </div>
            
            <!-- Product Name -->
            <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
              {{ part.name }}
            </h3>
            
            <!-- Price -->
            <div class="flex items-center justify-between">
              <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                {{ part.formatted_price }}
              </div>
              <div class="flex items-center gap-1">
                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                <span class="text-xs text-gray-600 dark:text-gray-400">
                  {{ parseFloat(part.rating).toFixed(1) }}
                </span>
                </div>
              </div>
            
            <!-- Action Button -->
            <button 
              @click.stop="viewPart(part)"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200"
            >
              View Details
            </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center space-x-2">
            <button 
              @click="changePage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm font-medium text-secondary-500 bg-white border border-secondary-300 rounded-md hover:bg-secondary-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            
            <span 
              v-for="page in visiblePages" 
              :key="page"
              @click="changePage(page)"
              class="px-3 py-2 text-sm font-medium rounded-md cursor-pointer"
              :class="page === currentPage 
                ? 'text-white bg-primary-600 border border-primary-600' 
                : 'text-secondary-500 bg-white border border-secondary-300 hover:bg-secondary-50'"
            >
              {{ page }}
            </span>
            
            <button 
              @click="changePage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm font-medium text-secondary-500 bg-white border border-secondary-300 rounded-md hover:bg-secondary-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="parts.length === 0" class="card glass text-center py-20">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 mx-auto mb-8 bg-gradient-to-br from-primary-100 to-secondary-100 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">
            No Parts Found
          </h3>
          <p class="text-secondary-600 dark:text-secondary-400 mb-8">
            Try adjusting your search criteria or filters to find the parts you need.
          </p>
          <button 
            @click="clearFilters"
            class="btn-primary"
          >
            Clear Filters
          </button>
        </div>
      </div>

    </div>

    <!-- Part Detail Modal -->
    <div v-if="selectedPart" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-secondary-800 border-b border-secondary-200 dark:border-secondary-700 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold text-secondary-900 dark:text-white">
              {{ selectedPart.name }}
            </h3>
            <button 
              @click="selectedPart = null"
              class="p-2 rounded-lg text-secondary-400 hover:text-secondary-600 hover:bg-secondary-100 dark:hover:bg-secondary-700 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Part Image -->
            <div>
              <img 
                :src="selectedPart.image_url || '/images/parts/placeholder.jpg'" 
                :alt="selectedPart.name"
                class="w-full h-64 object-cover rounded-lg mb-4"
              />
            </div>

            <!-- Part Details -->
            <div class="space-y-6">
              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Description
                </h4>
                <p class="text-secondary-600 dark:text-secondary-400">
                  {{ selectedPart.description }}
                </p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Part Number:</span>
                  <p class="font-medium">{{ selectedPart.part_number }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Manufacturer:</span>
                  <p class="font-medium">{{ selectedPart.manufacturer }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Category:</span>
                  <p class="font-medium">{{ selectedPart.category.charAt(0).toUpperCase() + selectedPart.category.slice(1) }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Quality:</span>
                  <p class="font-medium">{{ selectedPart.quality_display }}</p>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Installation Information
                </h4>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Difficulty:</span>
                    <p class="font-medium">{{ selectedPart.difficulty_level.charAt(0).toUpperCase() + selectedPart.difficulty_level.slice(1) }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Installation Time:</span>
                    <p class="font-medium">{{ selectedPart.installation_time_display }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Warranty:</span>
                    <p class="font-medium">{{ selectedPart.warranty_display }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Weight:</span>
                    <p class="font-medium">{{ selectedPart.weight || 'Not specified' }}</p>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Compatibility
                </h4>
                <div class="space-y-2">
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Compatible Brands:</span>
                    <p class="font-medium">{{ selectedPart.compatible_brands?.join(', ') || 'Not specified' }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Compatible Models:</span>
                    <p class="font-medium">{{ selectedPart.compatible_models?.join(', ') || 'Not specified' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Price and Actions -->
          <div class="mt-8 pt-6 border-t border-secondary-200 dark:border-secondary-700">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                  {{ selectedPart.formatted_price }}
                </span>
                <p class="text-sm text-secondary-500 dark:text-secondary-400">
                  {{ selectedPart.in_stock ? 'In Stock' : 'Out of Stock' }}
                </p>
              </div>
              <div class="flex space-x-4">
                <button 
                  class="btn-ghost"
                  @click="selectedPart = null"
                >
                  Close
                </button>
                <button 
                  class="btn-primary"
                  :disabled="!selectedPart.in_stock"
                >
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Recommendations Section -->
    <div class="mt-16 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 rounded-3xl p-8 text-white">
      <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full mb-6">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
          </svg>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Need Help Finding the Right Parts?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
          Get AI-powered recommendations based on your vehicle's diagnosis. Our smart system analyzes your car's issues and suggests the perfect parts for your specific needs.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link to="/diagnose" class="bg-white text-primary-600 hover:bg-white/90 px-8 py-3 text-lg font-semibold rounded-xl transition-colors duration-300">
            Get AI Diagnosis
          </router-link>
          <button class="border-2 border-white text-white hover:bg-white hover:text-primary-600 px-8 py-3 text-lg font-semibold rounded-xl transition-colors duration-300">
            Contact Support
          </button>
        </div>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
            <div class="text-2xl font-bold mb-2">95%</div>
            <div class="text-white/80">Accuracy Rate</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
            <div class="text-2xl font-bold mb-2">24/7</div>
            <div class="text-white/80">AI Support</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
            <div class="text-2xl font-bold mb-2">Free</div>
            <div class="text-white/80">Diagnosis</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import publicAPI from '../services/publicAPI'

// Reactive data
const parts = ref([])
const featuredParts = ref([])
const partnerParts = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedManufacturer = ref('')
const sortBy = ref('name')

// Ranking system

// Public API integration
const publicAPIParts = ref([])
const vehicleData = ref(null)
const vinInput = ref('')
const showVINLookup = ref(false)
const apiStatus = ref({})
const publicAPIPriceComparison = ref(null)
const showPriceComparison = ref(false)
const showMockProducts = ref(true)
const recentDiagnoses = ref([])
const showRecentDiagnoses = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const totalParts = ref(0)
const selectedPart = ref(null)
const showPartnerResults = ref(false)
const partnerStats = ref({})
const priceComparison = ref([])

// Filters
const categories = ref([])
const manufacturers = ref([])

// Computed
const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
// Ranking system methods

// Public API methods
const lookupVehicleByVIN = async () => {
  if (!publicAPI.validateVIN(vinInput.value)) {
    alert('Please enter a valid 17-character VIN')
    return
  }

  try {
    const result = await publicAPI.getVehicleByVIN(vinInput.value)
    
    if (result.success) {
      vehicleData.value = publicAPI.formatVehicleData(result.data)
      searchQuery.value = vehicleData.value.searchQuery
      await searchPublicAPIs()
    } else {
      alert('Vehicle not found. Please check your VIN.')
    }
  } catch (error) {
    console.error('VIN lookup error:', error)
    alert('Error looking up vehicle. Please try again.')
  }
}

const searchPublicAPIs = async () => {
  if (!searchQuery.value.trim()) return

  try {
    const result = await publicAPI.searchAllAPIs(searchQuery.value, vehicleData.value)
    
    if (result.success) {
      const allParts = []
      
      // Combine eBay and Amazon results
      if (result.data.ebay && result.data.ebay.success) {
        const ebayParts = publicAPI.formatPartsData(result.data.ebay.data, 'ebay')
        allParts.push(...ebayParts)
      }
      
      if (result.data.amazon && result.data.amazon.success) {
        const amazonParts = publicAPI.formatPartsData(result.data.amazon.data, 'amazon')
        allParts.push(...amazonParts)
      }
      
      publicAPIParts.value = allParts
      showPartnerResults.value = true
    }
  } catch (error) {
    console.error('Public API search error:', error)
  }
}

const comparePricesAcrossAPIs = async () => {
  if (!searchQuery.value.trim()) return

  try {
    const result = await publicAPI.comparePricesAcrossAPIs(searchQuery.value, vehicleData.value)
    
    if (result) {
      publicAPIPriceComparison.value = result
      showPriceComparison.value = true
    }
  } catch (error) {
    console.error('Price comparison error:', error)
  }
}

const getAPIStatus = async () => {
  try {
    const result = await publicAPI.getAPIStatus()
    if (result.success) {
      apiStatus.value = result.data
    }
  } catch (error) {
    console.error('API status error:', error)
  }
}

const handleAffiliateClick = async (part) => {
  await publicAPI.trackAffiliateClick(part.id, part.source, part)
  window.open(part.affiliate_url, '_blank')
}

// Load mock public API parts for demonstration
const loadMockPublicAPIParts = async () => {
  const mockParts = [
    // eBay Parts
    {
      id: 'ebay-123456789',
      name: 'Bosch Premium Air Filter for BMW 3 Series 2012-2018',
      description: 'High-quality air filter for improved engine performance and fuel efficiency',
      price: 24.99,
      formatted_price: '$24.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Bosch',
      part_number: 'F026400123',
      rating: 4.5,
      review_count: 127,
      stock_quantity: 15,
      source: 'ebay',
      affiliate_url: 'https://ebay.com/itm/bosch-air-filter-bmw',
      category: 'engine',
      ai_recommended: true,
      shipping_cost: 5.99,
      estimated_delivery: '2-3 days',
      seller: 'AutoPartsPro',
      prime_eligible: false,
      availability: 'In Stock'
    },
    {
      id: 'ebay-987654321',
      name: 'Brembo Brake Pads Front Set for Audi A4 2016-2020',
      description: 'Premium ceramic brake pads for superior stopping power and reduced noise',
      price: 89.99,
      formatted_price: '$89.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Brembo',
      part_number: 'P85070',
      rating: 4.8,
      review_count: 89,
      stock_quantity: 8,
      source: 'ebay',
      affiliate_url: 'https://ebay.com/itm/brembo-brake-pads-audi',
      category: 'brakes',
      ai_recommended: true,
      shipping_cost: 0,
      estimated_delivery: '1-2 days',
      seller: 'BrakeSpecialist',
      prime_eligible: false,
      availability: 'In Stock'
    },
    {
      id: 'ebay-456789123',
      name: 'NGK Laser Iridium Spark Plugs Set of 4 for Honda Civic 2016-2021',
      description: 'Advanced iridium spark plugs for better fuel economy and smoother engine performance',
      price: 45.50,
      formatted_price: '$45.50',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'NGK',
      part_number: 'ILZKR7B-11S',
      rating: 4.6,
      review_count: 203,
      stock_quantity: 25,
      source: 'ebay',
      affiliate_url: 'https://ebay.com/itm/ngk-spark-plugs-honda',
      category: 'engine',
      ai_recommended: false,
      shipping_cost: 3.99,
      estimated_delivery: '3-5 days',
      seller: 'SparkPlugKing',
      prime_eligible: false,
      availability: 'In Stock'
    },
    // Amazon Parts
    {
      id: 'amazon-B08XYZ123',
      name: 'ACDelco Professional Oil Filter for Chevrolet Silverado 2014-2018',
      description: 'Professional grade oil filter with superior filtration and extended service life',
      price: 12.99,
      formatted_price: '$12.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'ACDelco',
      part_number: 'PF63',
      rating: 4.4,
      review_count: 156,
      stock_quantity: 50,
      source: 'amazon',
      affiliate_url: 'https://amazon.com/dp/B08XYZ123',
      category: 'engine',
      ai_recommended: true,
      shipping_cost: 0,
      estimated_delivery: '1 day',
      seller: 'Amazon',
      prime_eligible: true,
      availability: 'In Stock'
    },
    {
      id: 'amazon-B09ABC456',
      name: 'Michelin Defender T+H All-Season Tire 225/60R16 98H',
      description: 'All-season tire with excellent traction and long-lasting tread life',
      price: 145.99,
      formatted_price: '$145.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Michelin',
      part_number: '225/60R16',
      rating: 4.7,
      review_count: 342,
      stock_quantity: 12,
      source: 'amazon',
      affiliate_url: 'https://amazon.com/dp/B09ABC456',
      category: 'tires',
      ai_recommended: true,
      shipping_cost: 0,
      estimated_delivery: '2 days',
      seller: 'Amazon',
      prime_eligible: true,
      availability: 'In Stock'
    },
    {
      id: 'amazon-B10DEF789',
      name: 'Denso Alternator for Toyota Camry 2012-2017',
      description: 'High-output alternator for reliable electrical system performance',
      price: 189.99,
      formatted_price: '$189.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Denso',
      part_number: '210-0100',
      rating: 4.5,
      review_count: 78,
      stock_quantity: 6,
      source: 'amazon',
      affiliate_url: 'https://amazon.com/dp/B10DEF789',
      category: 'electrical',
      ai_recommended: false,
      shipping_cost: 0,
      estimated_delivery: '1-2 days',
      seller: 'Amazon',
      prime_eligible: true,
      availability: 'In Stock'
    },
    {
      id: 'ebay-789123456',
      name: 'Monroe Sensa-Trac Strut Assembly for Ford Focus 2012-2018',
      description: 'Complete strut assembly with coil spring for improved ride comfort',
      price: 125.99,
      formatted_price: '$125.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Monroe',
      part_number: '171825',
      rating: 4.3,
      review_count: 94,
      stock_quantity: 4,
      source: 'ebay',
      affiliate_url: 'https://ebay.com/itm/monroe-strut-ford',
      category: 'suspension',
      ai_recommended: true,
      shipping_cost: 8.99,
      estimated_delivery: '4-6 days',
      seller: 'SuspensionPro',
      prime_eligible: false,
      availability: 'In Stock'
    },
    {
      id: 'amazon-B11GHI012',
      name: 'Gates Timing Belt Kit for Honda Accord 2013-2017',
      description: 'Complete timing belt kit with water pump and tensioner for peace of mind',
      price: 89.99,
      formatted_price: '$89.99',
      currency: 'USD',
      image_url: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
      condition: 'New',
      brand: 'Gates',
      part_number: 'TCKWP328',
      rating: 4.6,
      review_count: 127,
      stock_quantity: 18,
      source: 'amazon',
      affiliate_url: 'https://amazon.com/dp/B11GHI012',
      category: 'engine',
      ai_recommended: true,
      shipping_cost: 0,
      estimated_delivery: '1 day',
      seller: 'Amazon',
      prime_eligible: true,
      availability: 'In Stock'
    }
  ]

  // Simulate API delay
  await new Promise(resolve => setTimeout(resolve, 500))
  
  publicAPIParts.value = mockParts
}

// Load recent diagnoses for personalized recommendations
const loadRecentDiagnoses = async () => {
  try {
    // Mock recent diagnoses data
    const mockDiagnoses = [
      {
        id: 1,
        vehicle: '2018 BMW 3 Series',
        issue: 'Engine knocking sound',
        diagnosis_date: '2024-01-15',
        suggested_parts: ['Engine Oil Filter', 'Spark Plugs', 'Air Filter'],
        status: 'completed'
      },
      {
        id: 2,
        vehicle: '2018 BMW 3 Series',
        issue: 'Brake squeaking',
        diagnosis_date: '2024-01-10',
        suggested_parts: ['Brake Pads', 'Brake Rotors'],
        status: 'completed'
      }
    ]
    
    recentDiagnoses.value = mockDiagnoses
    showRecentDiagnoses.value = mockDiagnoses.length > 0
  } catch (error) {
    console.error('Error loading recent diagnoses:', error)
  }
}

// Search for parts based on diagnosis
const searchForDiagnosisParts = (diagnosis) => {
  // Set search query based on diagnosis
  searchQuery.value = diagnosis.issue
  selectedCategory.value = ''
  selectedBrand.value = ''
  
  // Scroll to search section
  scrollToSearch()
  
  // Trigger search
  filterParts()
}

const loadParts = async () => {
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: 12,
      sort_by: sortBy.value,
      sort_order: 'asc'
    })

    if (selectedCategory.value) {
      params.append('category', selectedCategory.value)
    }

    if (selectedManufacturer.value) {
      params.append('manufacturer', selectedManufacturer.value)
    }

    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }

    const response = await fetch(`/api/car-parts?${params}`)
    const data = await response.json()

    if (data.success) {
      parts.value = data.data
      totalPages.value = data.pagination.last_page
      totalParts.value = data.pagination.total
      
      if (data.filters) {
        categories.value = data.filters.categories || []
        manufacturers.value = data.filters.manufacturers || []
      }
    }
  } catch (error) {
    console.error('Error loading parts:', error)
  }
}

const loadFeaturedParts = async () => {
  try {
    const response = await fetch('/api/car-parts/featured')
    const data = await response.json()

    if (data.success) {
      featuredParts.value = data.data
    }
  } catch (error) {
    console.error('Error loading featured parts:', error)
  }
}

const debouncedSearch = debounce(() => {
  currentPage.value = 1
  loadParts()
}, 500)

const filterParts = () => {
  currentPage.value = 1
  loadParts()
}

const sortParts = () => {
  currentPage.value = 1
  loadParts()
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    loadParts()
  }
}

const viewPart = (part) => {
  selectedPart.value = part
}

const selectCategory = (category) => {
  selectedCategory.value = category
  filterParts()
  scrollToSearch()
}

const scrollToSearch = () => {
  const element = document.getElementById('search-section')
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToCategories = () => {
  const element = document.getElementById('categories-section')
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

// Brand integration methods
const getBrandImage = (brand) => {
  const brandImages = {
    'BMW': '/images/brands/bmw-parts.jpg',
    'Mercedes-Benz': '/images/brands/mercedes-parts.jpg',
    'Audi': '/images/brands/audi-parts.jpg',
    'Volkswagen': '/images/brands/vw-parts.jpg',
    'Toyota': '/images/brands/toyota-parts.jpg',
    'Honda': '/images/brands/honda-parts.jpg',
    'Ford': '/images/brands/ford-parts.jpg',
    'Chevrolet': '/images/brands/chevrolet-parts.jpg',
    'Nissan': '/images/brands/nissan-parts.jpg',
    'Hyundai': '/images/brands/hyundai-parts.jpg',
    'Kia': '/images/brands/kia-parts.jpg',
    'Mazda': '/images/brands/mazda-parts.jpg',
    'Subaru': '/images/brands/subaru-parts.jpg',
    'Lexus': '/images/brands/lexus-parts.jpg',
    'Infiniti': '/images/brands/infiniti-parts.jpg',
    'Acura': '/images/brands/acura-parts.jpg',
    'Genesis': '/images/brands/genesis-parts.jpg',
    'Volvo': '/images/brands/volvo-parts.jpg',
    'Saab': '/images/brands/saab-parts.jpg',
    'Jaguar': '/images/brands/jaguar-parts.jpg',
    'Land Rover': '/images/brands/landrover-parts.jpg',
    'Porsche': '/images/brands/porsche-parts.jpg',
    'Ferrari': '/images/brands/ferrari-parts.jpg',
    'Lamborghini': '/images/brands/lamborghini-parts.jpg',
    'Maserati': '/images/brands/maserati-parts.jpg',
    'Bentley': '/images/brands/bentley-parts.jpg',
    'Rolls-Royce': '/images/brands/rollsroyce-parts.jpg',
    'Aston Martin': '/images/brands/astonmartin-parts.jpg',
    'McLaren': '/images/brands/mclaren-parts.jpg',
    'Bugatti': '/images/brands/bugatti-parts.jpg'
  }
  return brandImages[brand] || '/images/parts/default-part.jpg'
}

const getBrandLogo = (brand) => {
  const brandLogos = {
    'BMW': '/images/brands/bmw-logo.png',
    'Mercedes-Benz': '/images/brands/mercedes-logo.png',
    'Audi': '/images/brands/audi-logo.png',
    'Volkswagen': '/images/brands/vw-logo.png',
    'Toyota': '/images/brands/toyota-logo.png',
    'Honda': '/images/brands/honda-logo.png',
    'Ford': '/images/brands/ford-logo.png',
    'Chevrolet': '/images/brands/chevrolet-logo.png',
    'Nissan': '/images/brands/nissan-logo.png',
    'Hyundai': '/images/brands/hyundai-logo.png',
    'Kia': '/images/brands/kia-logo.png',
    'Mazda': '/images/brands/mazda-logo.png',
    'Subaru': '/images/brands/subaru-logo.png',
    'Lexus': '/images/brands/lexus-logo.png',
    'Infiniti': '/images/brands/infiniti-logo.png',
    'Acura': '/images/brands/acura-logo.png',
    'Genesis': '/images/brands/genesis-logo.png',
    'Volvo': '/images/brands/volvo-logo.png',
    'Saab': '/images/brands/saab-logo.png',
    'Jaguar': '/images/brands/jaguar-logo.png',
    'Land Rover': '/images/brands/landrover-logo.png',
    'Porsche': '/images/brands/porsche-logo.png',
    'Ferrari': '/images/brands/ferrari-logo.png',
    'Lamborghini': '/images/brands/lamborghini-logo.png',
    'Maserati': '/images/brands/maserati-logo.png',
    'Bentley': '/images/brands/bentley-logo.png',
    'Rolls-Royce': '/images/brands/rollsroyce-logo.png',
    'Aston Martin': '/images/brands/astonmartin-logo.png',
    'McLaren': '/images/brands/mclaren-logo.png',
    'Bugatti': '/images/brands/bugatti-logo.png'
  }
  return brandLogos[brand] || '/images/brands/default-logo.png'
}

const handleImageError = (event) => {
  event.target.src = '/images/parts/default-part.jpg'
}

// Affiliate system methods
const addToCart = async (part) => {
  try {
    // Track affiliate click
    await trackAffiliateClick(part)
    
    // Add to cart logic
    const response = await fetch('/api/cart/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({
        part_id: part.id,
        quantity: 1,
        affiliate_source: 'carwise_ai'
      })
    })
    
    if (response.ok) {
      // Show success message
      console.log('Part added to cart successfully')
    }
  } catch (error) {
    console.error('Error adding to cart:', error)
  }
}

const trackAffiliateClick = async (part) => {
  try {
    await fetch('/api/affiliate/track-click', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        part_id: part.id,
        brand: part.brand,
        category: part.category,
        user_agent: navigator.userAgent,
        referrer: document.referrer,
        timestamp: new Date().toISOString()
      })
    })
  } catch (error) {
    console.error('Error tracking affiliate click:', error)
  }
}

// Partner integration methods
const searchPartnerParts = async (query) => {
  try {
    const response = await fetch('/api/partners/search', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        query: query,
        category: selectedCategory.value,
        brand: selectedManufacturer.value,
        in_stock: true
      })
    })
    
    const data = await response.json()
    if (data.success) {
      partnerParts.value = data.data
      showPartnerResults.value = true
    }
  } catch (error) {
    console.error('Error searching partner parts:', error)
  }
}

const comparePrices = async (partName, brand = null, partNumber = null) => {
  try {
    const response = await fetch('/api/partners/compare-prices', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        part_name: partName,
        brand: brand,
        part_number: partNumber
      })
    })
    
    const data = await response.json()
    if (data.success) {
      priceComparison.value = data.data.price_comparison
      return data.data
    }
  } catch (error) {
    console.error('Error comparing prices:', error)
  }
}

const getPartnerStats = async () => {
  try {
    const response = await fetch('/api/partners/stats')
    const data = await response.json()
    if (data.success) {
      partnerStats.value = data.data
    }
  } catch (error) {
    console.error('Error getting partner stats:', error)
  }
}

const syncPartnerParts = async () => {
  try {
    const response = await fetch('/api/partners/sync', {
      method: 'POST'
    })
    const data = await response.json()
    if (data.success) {
      console.log('Partner parts synced successfully:', data.data)
      // Refresh parts after sync
      await loadParts()
    }
  } catch (error) {
    console.error('Error syncing partner parts:', error)
  }
}

const getAffiliateLink = async (partnerId, partId) => {
  try {
    const response = await fetch(`/api/partners/${partnerId}/parts/${partId}/affiliate-link`)
    const data = await response.json()
    if (data.success) {
      return data.data.affiliate_link
    }
  } catch (error) {
    console.error('Error getting affiliate link:', error)
  }
  return null
}

const buyFromPartner = async (partnerId, partId) => {
  try {
    const affiliateLink = await getAffiliateLink(partnerId, partId)
    if (affiliateLink) {
      window.open(affiliateLink, '_blank')
    }
  } catch (error) {
    console.error('Error opening partner link:', error)
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedManufacturer.value = ''
  currentPage.value = 1
  loadParts()
}

const getQualityColor = (quality) => {
  const colors = {
    'oem': 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20',
    'premium': 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20',
    'standard': 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20',
    'economy': 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-900/20'
  }
  return colors[quality] || colors.standard
}

const getDifficultyColor = (difficulty) => {
  const colors = {
    'easy': 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20',
    'medium': 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20',
    'hard': 'text-orange-600 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/20',
    'professional': 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/20'
  }
  return colors[difficulty] || colors.medium
}

// Debounce utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Lifecycle
onMounted(async () => {
  await loadFeaturedParts()
  await loadParts()
  await getAPIStatus()
  await loadMockPublicAPIParts()
  await loadRecentDiagnoses()
})

// Watch for page changes
watch(currentPage, () => {
  loadParts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
