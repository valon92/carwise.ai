<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Modern Header with Enhanced User Experience -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <!-- User Info Section -->
          <div class="flex-1">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                  My CarWise Dashboard
                </h1>
                <p class="text-gray-600 mt-1">Manage your AI car diagnostics and subscription</p>
              </div>
            </div>
            
            <!-- Status Badges -->
            <div class="flex flex-wrap items-center gap-3">
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                {{ userType }} Account
              </span>
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium" 
                    :class="subscriptionStatus === 'Active' ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200' : 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200'">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ subscriptionStatus }}
              </span>
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                AI Powered
              </span>
            </div>
          </div>
          
          <!-- Quick Stats -->
          <div class="lg:text-right">
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50 shadow-lg">
              <p class="text-sm font-medium text-gray-500 mb-1">This Month's Spending</p>
              <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                €{{ totalSpent.toLocaleString() }}
              </p>
              <div class="flex items-center justify-center lg:justify-end mt-2">
                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="text-sm text-green-600 font-medium">+{{ usageTrend }}% vs last month</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Enhanced Key Metrics Dashboard -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Current Plan Card -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200/50 overflow-hidden">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <button @click="upgradePlan" class="text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
              </button>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Plan</h3>
            <p class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
              {{ currentPlan?.name || 'Free Plan' }}
            </p>
            <p class="text-sm text-gray-600 mb-3">€{{ currentPlan?.price || '0' }}/month</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ currentPlan?.diagnoses_per_month || 0 }} diagnoses/month
            </div>
          </div>
        </div>

        <!-- API Usage Card -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200/50 overflow-hidden">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <span class="text-xs font-medium px-2 py-1 rounded-full" 
                    :class="(apiUsage.used / apiUsage.limit) > 0.8 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                {{ Math.round((apiUsage.used / apiUsage.limit) * 100) }}%
              </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">API Usage</h3>
            <p class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-1">
              {{ apiUsage.used }}/{{ apiUsage.limit }}
            </p>
            <p class="text-sm text-gray-600 mb-3">BMW CarData calls</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
              <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" 
                   :style="{ width: `${(apiUsage.used / apiUsage.limit) * 100}%` }"></div>
            </div>
            <p class="text-xs text-gray-500">{{ apiUsage.limit - apiUsage.used }} calls remaining</p>
          </div>
        </div>

        <!-- AI Diagnostics Card -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200/50 overflow-hidden">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-xs text-gray-500">This month</p>
                <p class="text-sm font-medium text-green-600">+{{ usageTrend }}%</p>
              </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">AI Diagnostics</h3>
            <p class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">
              {{ diagnosticsCount }}
            </p>
            <p class="text-sm text-gray-600 mb-3">Completed diagnoses</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              {{ reportsGenerated }} reports generated
            </div>
          </div>
        </div>

        <!-- Next Billing Card -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200/50 overflow-hidden">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <button @click="viewBillingHistory" class="text-yellow-600 hover:text-yellow-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </button>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Next Billing</h3>
            <p class="text-2xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-1">
              {{ nextBillingDate }}
            </p>
            <p class="text-sm text-gray-600 mb-3">Auto-renewal enabled</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
              </svg>
              €{{ currentPlan?.price || '0' }} will be charged
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced User Dashboard Tabs -->
      <div class="mb-8">
        <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-2 border border-gray-200/50 shadow-lg">
          <nav class="flex space-x-2">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="flex-1 py-3 px-4 rounded-xl font-medium text-sm transition-all duration-300 relative group"
              :class="activeTab === tab.id 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg transform scale-105' 
                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'"
            >
              <div class="flex items-center justify-center space-x-2">
                <svg v-if="tab.id === 'subscription'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg v-else-if="tab.id === 'payments'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <svg v-else-if="tab.id === 'usage'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>{{ tab.name }}</span>
              </div>
              <div v-if="activeTab === tab.id" class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 opacity-10 -z-10"></div>
            </button>
          </nav>
        </div>
      </div>

      <!-- Quick Actions Section -->
      <div class="mb-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50 shadow-lg">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Quick Actions
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button @click="upgradePlan" class="group p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 hover:scale-105">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="font-medium text-gray-900">Upgrade Plan</p>
                  <p class="text-sm text-gray-600">Get more features</p>
                </div>
              </div>
            </button>
            
            <button @click="viewBillingHistory" class="group p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 hover:from-green-100 hover:to-emerald-100 transition-all duration-300 hover:scale-105">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="font-medium text-gray-900">Billing History</p>
                  <p class="text-sm text-gray-600">View invoices</p>
                </div>
              </div>
            </button>
            
            <button @click="updatePaymentMethod" class="group p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200 hover:from-purple-100 hover:to-pink-100 transition-all duration-300 hover:scale-105">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="font-medium text-gray-900">Payment Method</p>
                  <p class="text-sm text-gray-600">Update card</p>
                </div>
              </div>
            </button>
            
            <button @click="contactSupport" class="group p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border border-yellow-200 hover:from-yellow-100 hover:to-orange-100 transition-all duration-300 hover:scale-105">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="font-medium text-gray-900">Support</p>
                  <p class="text-sm text-gray-600">Get help</p>
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Tab Content -->
      <div v-if="activeTab === 'subscription'" class="space-y-6">
        <!-- Enhanced Subscription Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Plan Management Card -->
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-gray-200/50">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Plan Management
              </h3>
              <span class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full text-sm font-medium">
                Active
              </span>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                  <h4 class="font-medium text-gray-900">{{ currentPlan?.name || 'Free Plan' }}</h4>
                  <p class="text-sm text-gray-600">{{ currentPlan?.description || 'Basic diagnostics without BMW API' }}</p>
                </div>
                <span class="text-2xl font-bold text-blue-600">€{{ currentPlan?.price || '0' }}</span>
              </div>
              
              <div class="grid grid-cols-2 gap-3">
                <button 
                  @click="upgradePlan"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
                >
                  Upgrade Plan
                </button>
                <button 
                  @click="viewBillingHistory"
                  class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors"
                >
                  Billing History
                </button>
      </div>
    </div>
  </div>

          <!-- Usage Analytics -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Usage Analytics</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-600">BMW API Calls</span>
                <span class="text-sm font-bold">{{ apiUsage.used }}/{{ apiUsage.limit }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" :style="{ width: `${(apiUsage.used / apiUsage.limit) * 100}%` }"></div>
              </div>
              
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-600">AI Diagnostics</span>
                <span class="text-sm font-bold">{{ diagnosticsCount }} this month</span>
              </div>
              
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-600">Cost per Diagnosis</span>
                <span class="text-sm font-bold">€{{ costPerDiagnosis }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment History Tab -->
      <div v-if="activeTab === 'payments'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Payment Methods -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Payment Methods</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">**** **** **** 4242</p>
                    <p class="text-sm text-gray-600">Expires 12/25</p>
                  </div>
                </div>
                <span class="text-sm font-medium text-green-600">Default</span>
              </div>
              <button 
                @click="updatePaymentMethod"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
              >
                Update Payment Method
              </button>
            </div>
          </div>

          <!-- Billing Summary -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Billing Summary</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">This Month</span>
                <span class="text-lg font-bold text-blue-600">€{{ currentPlan?.price || '0' }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Last Month</span>
                <span class="text-lg font-bold text-gray-600">€{{ currentPlan?.price || '0' }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Total This Year</span>
                <span class="text-lg font-bold text-green-600">€{{ yearlyTotal }}</span>
              </div>
              <button 
                @click="viewBillingHistory"
                class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors"
              >
                View Full History
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Usage Analytics Tab -->
      <div v-if="activeTab === 'usage'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Usage Breakdown -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Usage This Month</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">AI Diagnoses</span>
                <span class="text-sm font-bold">{{ diagnosticsCount }}/{{ currentPlan?.diagnoses_per_month || 'Unlimited' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">BMW API Calls</span>
                <span class="text-sm font-bold">{{ apiUsage.used }}/{{ apiUsage.limit }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Reports Generated</span>
                <span class="text-sm font-bold">{{ reportsGenerated }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Service Requests</span>
                <span class="text-sm font-bold">{{ serviceRequests }}</span>
              </div>
              <hr class="my-2">
              <div class="flex justify-between font-bold">
                <span>Total Usage</span>
                <span>{{ totalUsage }} actions</span>
              </div>
            </div>
          </div>

          <!-- Cost Breakdown -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Cost Breakdown</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Subscription</span>
                <span class="text-sm font-bold">€{{ currentPlan?.price || '0' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Extra Diagnoses</span>
                <span class="text-sm font-bold">€{{ extraDiagnosesCost }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Service Fees</span>
                <span class="text-sm font-bold">€{{ serviceFees }}</span>
              </div>
              <hr class="my-2">
              <div class="flex justify-between font-bold">
                <span>Total This Month</span>
                <span>€{{ totalSpent }}</span>
              </div>
            </div>
          </div>

          <!-- Usage Trend -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Usage Trend</h3>
            <div class="text-center">
              <div class="text-4xl font-bold text-blue-600 mb-2">{{ usageTrend }}%</div>
              <p class="text-sm text-gray-600">vs last month</p>
              <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div class="bg-blue-500 h-3 rounded-full" :style="{ width: `${Math.min(usageTrend, 100)}%` }"></div>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">{{ usageTrend > 0 ? 'Increased' : 'Decreased' }} usage</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
        <div class="space-y-4">
          <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                <p class="text-xs text-gray-500">{{ activity.date }}</p>
              </div>
            </div>
            <span class="text-sm font-medium" :class="activity.amount > 0 ? 'text-green-600' : 'text-gray-600'">
              {{ activity.amount > 0 ? '+' : '' }}${{ activity.amount }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'SubscriptionDashboard',
  setup() {
    const router = useRouter()
    
    // Tab management
    const activeTab = ref('subscription')
    const tabs = ref([
      { id: 'subscription', name: 'My Subscription' },
      { id: 'payments', name: 'Payments & Billing' },
      { id: 'usage', name: 'Usage Analytics' }
    ])

    // User type and status
    const userType = ref('Individual')
    const subscriptionStatus = ref('Active')
    
    // Core subscription data
    const currentPlan = ref(null)
    const apiUsage = ref({ used: 0, limit: 0 })
    const diagnosticsCount = ref(0)
    const nextBillingDate = ref('')
    const totalSpent = ref(0)

    // User-specific data
    const reportsGenerated = ref(0)
    const serviceRequests = ref(0)
    const extraDiagnosesCost = ref(0)
    const serviceFees = ref(0)
    const yearlyTotal = ref(0)
    const usageTrend = ref(0)

    const recentActivity = ref([])

    // Computed properties
    const costPerDiagnosis = computed(() => {
      if (diagnosticsCount.value === 0) return '0.00'
      return (6.99).toFixed(2) // Standard diagnosis cost
    })

    const totalUsage = computed(() => {
      return diagnosticsCount.value + reportsGenerated.value + serviceRequests.value
    })

    // Mock data - replace with actual API calls
    const loadSubscriptionData = async () => {
      // Simulate API call for individual user data
      currentPlan.value = {
        name: 'Pro Plan',
        price: 9.99,
        description: '3 diagnoses + AI reports + service offers',
        features: ['3 AI Diagnoses', 'BMW API Access', 'Service Recommendations', 'Priority Support'],
        diagnoses_per_month: 3
      }
      
      // User's API usage tracking
      apiUsage.value = {
        used: 2,
        limit: 3
      }
      
      // User's usage data
      diagnosticsCount.value = 2
      reportsGenerated.value = 1
      serviceRequests.value = 0
      nextBillingDate.value = 'January 15, 2024'
      totalSpent.value = 9.99
      yearlyTotal.value = 119.88
      usageTrend.value = 15 // 15% increase vs last month
      
      // User's cost breakdown
      extraDiagnosesCost.value = 0 // No extra diagnoses this month
      serviceFees.value = 0 // No service fees this month
      
      recentActivity.value = [
        {
          id: 1,
          description: 'AI diagnosis completed for BMW X3',
          date: 'Dec 20, 2023',
          amount: 0,
          type: 'diagnosis'
        },
        {
          id: 2,
          description: 'Technical report generated',
          date: 'Dec 19, 2023',
          amount: 0,
          type: 'report'
        },
        {
          id: 3,
          description: 'Pro Plan subscription renewed',
          date: 'Dec 15, 2023',
          amount: 9.99,
          type: 'subscription'
        },
        {
          id: 4,
          description: 'AI diagnosis completed for BMW 320i',
          date: 'Dec 12, 2023',
          amount: 0,
          type: 'diagnosis'
        },
        {
          id: 5,
          description: 'Payment method updated',
          date: 'Dec 10, 2023',
          amount: 0,
          type: 'payment'
        }
      ]
    }

    // Action methods
    const upgradePlan = () => {
      router.push('/subscription/plans')
    }

    const viewBillingHistory = () => {
      console.log('Navigate to billing history')
    }

    const updatePaymentMethod = () => {
      console.log('Update payment method')
      // TODO: Implement payment method update modal or redirect
      alert('Payment method update feature coming soon!')
    }

    const downloadInvoice = () => {
      console.log('Download latest invoice')
    }

    const contactSupport = () => {
      console.log('Contact support')
    }

    const cancelSubscription = () => {
      if (confirm('Are you sure you want to cancel your subscription?')) {
        console.log('Cancel subscription')
      }
    }

    onMounted(() => {
      loadSubscriptionData()
    })

    return {
      // Tab management
      activeTab,
      tabs,
      
      // User info
      userType,
      subscriptionStatus,
      
      // Core data
      currentPlan,
      apiUsage,
      diagnosticsCount,
      nextBillingDate,
      totalSpent,
      
      // User-specific data
      reportsGenerated,
      serviceRequests,
      extraDiagnosesCost,
      serviceFees,
      yearlyTotal,
      usageTrend,
      recentActivity,
      
      // Computed
      costPerDiagnosis,
      totalUsage,
      
      // Actions
      upgradePlan,
      viewBillingHistory,
      updatePaymentMethod,
      downloadInvoice,
      contactSupport,
      cancelSubscription
    }
  }
}
</script>