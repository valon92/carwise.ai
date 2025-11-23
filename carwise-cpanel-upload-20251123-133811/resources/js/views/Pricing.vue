<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <div v-if="route.query.action === 'upgrade'" class="mb-6">
            <button @click="goBackToDashboard" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Dashboard
            </button>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-4">
            {{ route.query.action === 'upgrade' ? 'Upgrade Your Plan' : 'CarWise.ai Pricing & Subscription Plans' }}
          </h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            {{ route.query.action === 'upgrade' ? 'Choose a plan that better fits your needs and unlock more features.' : 'Choose the perfect plan for your car diagnostic needs. From basic manual checks to advanced AI-powered fleet management.' }}
          </p>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Trust Indicators -->
      <div class="text-center mb-12">
        <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-600">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>14-day free trial</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>Cancel anytime</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>No setup fees</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>24/7 support</span>
          </div>
        </div>
      </div>

      <!-- Pricing Toggle -->
      <div class="flex justify-center mb-12">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-2 border border-gray-200/50 shadow-lg">
          <div class="flex">
            <button 
              @click="billingCycle = 'monthly'"
              class="px-6 py-3 rounded-xl font-medium transition-all duration-300"
              :class="billingCycle === 'monthly' 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg' 
                : 'text-gray-600 hover:text-gray-900'"
            >
              Monthly
            </button>
            <button 
              @click="billingCycle = 'yearly'"
              class="px-6 py-3 rounded-xl font-medium transition-all duration-300"
              :class="billingCycle === 'yearly' 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg' 
                : 'text-gray-600 hover:text-gray-900'"
            >
              Yearly
              <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Save 15%</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Pricing Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
        <!-- Basic Plan -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden hover:shadow-xl transition-all duration-300"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'basic' }">
          <div class="p-8">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic</h3>
              <p class="text-gray-600 mb-4">Perfect for new users</p>
              <div v-if="route.query.current_plan === 'basic'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-gray-600 to-gray-800 bg-clip-text text-transparent">€0</span>
                <span class="text-gray-600">/month</span>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                1 manual diagnosis per month
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Access to car articles library
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Email notifications
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Basic support
              </li>
            </ul>
            
            <button 
              @click="selectPlan('basic')"
              class="w-full bg-gradient-to-r from-gray-500 to-gray-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-gray-600 hover:to-gray-800 transition-all duration-300 transform hover:scale-105"
            >
              Get Started Free
            </button>
          </div>
        </div>

        <!-- Smart AI Plan -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden hover:shadow-xl transition-all duration-300"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'smart-ai' }">
          <div class="p-8">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Smart AI</h3>
              <p class="text-gray-600 mb-4">Pay-per-use detailed diagnosis</p>
              <div v-if="route.query.current_plan === 'smart-ai'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">€7.99</span>
                <span class="text-gray-600">/diagnosis</span>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Detailed AI diagnosis + repair recommendations
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Temporary API access for DTC retrieval
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                PDF report for diagnosis
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Multi-brand API connection support
              </li>
            </ul>
            
            <button 
              @click="selectPlan('smart-ai')"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
            >
              Choose Smart AI
            </button>
          </div>
        </div>

        <!-- Care+ Plan (Popular) -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border-2 border-blue-500 overflow-hidden hover:shadow-xl transition-all duration-300 relative"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'care-plus' }">
          <!-- Popular Badge -->
          <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-lg">
              Most Popular
            </span>
          </div>
          
          <div class="p-8 pt-12">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Care+</h3>
              <p class="text-gray-600 mb-4">Monthly coverage with preventive suggestions</p>
              <div v-if="route.query.current_plan === 'care-plus'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">€9.99</span>
                <span class="text-gray-600">/month</span>
                <div v-if="billingCycle === 'yearly'" class="text-sm text-green-600 mt-1">
                  €8.49/month (billed yearly)
                </div>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                3 diagnoses per month (with API)
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Repair history tracking
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Priority support
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                10% discount coupon for recommended parts
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">14-day free trial</span>
              </li>
            </ul>
            
            <button 
              @click="selectPlan('care-plus')"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
            >
              Start Free Trial
            </button>
          </div>
        </div>

        <!-- Pro Garage Plan -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden hover:shadow-xl transition-all duration-300"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'pro-garage' }">
          <div class="p-8">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro Garage</h3>
              <p class="text-gray-600 mb-4">For services & mechanics (B2B)</p>
              <div v-if="route.query.current_plan === 'pro-garage'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">€99</span>
                <span class="text-gray-600">/month</span>
                <div v-if="billingCycle === 'yearly'" class="text-sm text-green-600 mt-1">
                  €84.15/month (billed yearly)
                </div>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Multi-user access
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Detailed technical reports
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Integration with work system
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Reduced commission for parts
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                User licenses + dedicated API keys
              </li>
            </ul>
            
            <button 
              @click="selectPlan('pro-garage')"
              class="w-full bg-gradient-to-r from-purple-500 to-pink-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105"
            >
              Contact Sales
            </button>
          </div>
        </div>
      </div>

      <!-- Feature Comparison Table -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Compare All Features</h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-4 px-6 font-semibold text-gray-900">Features</th>
                <th class="text-center py-4 px-6 font-semibold text-gray-900">Basic</th>
                <th class="text-center py-4 px-6 font-semibold text-gray-900">Smart AI</th>
                <th class="text-center py-4 px-6 font-semibold text-gray-900">Care+</th>
                <th class="text-center py-4 px-6 font-semibold text-gray-900">Pro Garage</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr>
                <td class="py-4 px-6 text-gray-700">AI Diagnoses per month</td>
                <td class="text-center py-4 px-6">1</td>
                <td class="text-center py-4 px-6">Pay-per-use</td>
                <td class="text-center py-4 px-6">3</td>
                <td class="text-center py-4 px-6">Unlimited</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="py-4 px-6 text-gray-700">Multi-Brand API Access</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">✅</td>
                <td class="text-center py-4 px-6">✅</td>
                <td class="text-center py-4 px-6">✅</td>
              </tr>
              <tr>
                <td class="py-4 px-6 text-gray-700">PDF Reports</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">✅</td>
                <td class="text-center py-4 px-6">✅</td>
                <td class="text-center py-4 px-6">✅</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="py-4 px-6 text-gray-700">Priority Support</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">✅</td>
                <td class="text-center py-4 px-6">✅</td>
              </tr>
              <tr>
                <td class="py-4 px-6 text-gray-700">Multi-user Access</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">✅</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="py-4 px-6 text-gray-700">API Integration</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">❌</td>
                <td class="text-center py-4 px-6">✅</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- B2B Pricing Section -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">B2B Solutions & Enterprise Pricing</h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-4 px-6 font-semibold text-gray-900">Client Type</th>
                <th class="text-left py-4 px-6 font-semibold text-gray-900">Purpose</th>
                <th class="text-left py-4 px-6 font-semibold text-gray-900">Pricing</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr>
                <td class="py-4 px-6 text-gray-700 font-medium">Service Centers (Garages)</td>
                <td class="py-4 px-6 text-gray-600">Diagnostics + client reports + parts offers</td>
                <td class="py-4 px-6 text-blue-600 font-semibold">€49–€199 / month</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="py-4 px-6 text-gray-700 font-medium">Fleet Management</td>
                <td class="py-4 px-6 text-gray-600">Monitoring + defect prediction</td>
                <td class="py-4 px-6 text-blue-600 font-semibold">€5 / vehicle / month</td>
              </tr>
              <tr>
                <td class="py-4 px-6 text-gray-700 font-medium">Insurance Companies</td>
                <td class="py-4 px-6 text-gray-600">Risk scoring + AI analysis</td>
                <td class="py-4 px-6 text-blue-600 font-semibold">€0.50–€1 / vehicle / month</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="py-4 px-6 text-gray-700 font-medium">Parts Companies</td>
                <td class="py-4 px-6 text-gray-600">Integration for direct sales</td>
                <td class="py-4 px-6 text-blue-600 font-semibold">10–15% commission</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-6 text-center">
          <p class="text-gray-600 mb-4">Need a custom solution for your business?</p>
          <button class="bg-gradient-to-r from-purple-500 to-pink-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
            Contact Sales Team
          </button>
        </div>
      </div>

      <!-- Parts Integration Section -->
      <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl shadow-lg p-8 border border-orange-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Smart Parts Integration</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
              </div>
              <h4 class="text-xl font-semibold text-gray-900">AI-Powered Parts Recommendations</h4>
            </div>
            <p class="text-gray-600 mb-6">
              When our AI diagnoses a problem, it automatically suggests the exact parts needed for repair. 
              Users can browse and purchase parts directly through our integrated marketplace.
            </p>
            <div class="space-y-3">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-700">Exact part recommendations based on diagnosis</span>
              </div>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-700">Direct integration with parts marketplace</span>
              </div>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-700">Competitive pricing from multiple suppliers</span>
              </div>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-700">Fast delivery and installation guides</span>
              </div>
            </div>
          </div>
          
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
            <h5 class="font-semibold text-gray-900 mb-4 text-center">How It Works</h5>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3 mt-1">1</div>
                <div>
                  <h6 class="font-medium text-gray-900">AI Diagnosis</h6>
                  <p class="text-sm text-gray-600">Our AI analyzes your car and identifies the problem</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3 mt-1">2</div>
                <div>
                  <h6 class="font-medium text-gray-900">Parts Suggestions</h6>
                  <p class="text-sm text-gray-600">AI recommends exact parts needed for repair</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3 mt-1">3</div>
                <div>
                  <h6 class="font-medium text-gray-900">Browse & Purchase</h6>
                  <p class="text-sm text-gray-600">Compare prices and buy parts directly</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3 mt-1">4</div>
                <div>
                  <h6 class="font-medium text-gray-900">Installation Support</h6>
                  <p class="text-sm text-gray-600">Get guides and support for installation</p>
                </div>
              </div>
            </div>
            <div class="mt-6 text-center">
              <router-link 
                to="/car-parts"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Browse Car Parts
              </router-link>
            </div>
          </div>
        </div>
        
        <!-- Revenue Model Info -->
        <div class="mt-8 bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-gray-200/50">
          <div class="text-center">
            <h5 class="font-semibold text-gray-900 mb-2">Win-Win Revenue Model</h5>
            <p class="text-gray-600 text-sm">
              When users purchase parts through our platform, CarWise.ai earns a small commission (10-15%) 
              while users get competitive prices and expert recommendations. This helps us keep our 
              diagnostic services affordable while providing additional value.
            </p>
          </div>
        </div>
      </div>

      <!-- Add-ons Section -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Add-ons (Optional)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="text-center p-6 bg-gray-50 rounded-xl">
            <h4 class="font-semibold text-gray-900 mb-2">Professional Technical Report</h4>
            <p class="text-2xl font-bold text-blue-600 mb-2">€14.99</p>
            <p class="text-sm text-gray-600">PDF + service recommendation</p>
          </div>
          <div class="text-center p-6 bg-gray-50 rounded-xl">
            <h4 class="font-semibold text-gray-900 mb-2">Predictive Problem Analysis</h4>
            <p class="text-2xl font-bold text-blue-600 mb-2">€19.99</p>
            <p class="text-sm text-gray-600">Monthly predictive analysis</p>
          </div>
          <div class="text-center p-6 bg-gray-50 rounded-xl">
            <h4 class="font-semibold text-gray-900 mb-2">On-demand Expert Chat</h4>
            <p class="text-2xl font-bold text-blue-600 mb-2">€2.99</p>
            <p class="text-sm text-gray-600">Live expert consultation</p>
          </div>
        </div>
      </div>

      <!-- Competitive Advantages Section -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-8 border border-blue-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Why Choose CarWise.ai?</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Universal AI for All Brands</h4>
            <p class="text-sm text-gray-600">Single AI system for all car brands → saves development costs and increases accuracy</p>
          </div>
          
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Official API Connections</h4>
            <p class="text-sm text-gray-600">Direct integration with manufacturer APIs → security and credibility for users</p>
          </div>
          
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Neutral Platform</h4>
            <p class="text-sm text-gray-600">Not a garage, but an intelligent assistant for every car owner</p>
          </div>
          
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Multiple Revenue Streams</h4>
            <p class="text-sm text-gray-600">AI diagnostics, subscriptions, commissions, B2B solutions</p>
          </div>
        </div>
      </div>

      <!-- Testimonials Section -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">What Our Customers Say</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-4">
              <div class="flex justify-center mb-4">
                <div class="flex text-yellow-400">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                </div>
              </div>
              <p class="text-gray-700 italic">"CarWise.ai saved me hundreds of euros by catching issues early. The AI diagnosis is incredibly accurate!"</p>
            </div>
            <div class="flex items-center justify-center">
              <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">M</div>
              <div>
                <p class="font-semibold text-gray-900">Mark Thompson</p>
                <p class="text-sm text-gray-600">Car Owner</p>
              </div>
            </div>
          </div>
          
          <div class="text-center">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 mb-4">
              <div class="flex justify-center mb-4">
                <div class="flex text-yellow-400">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                </div>
              </div>
              <p class="text-gray-700 italic">"As a mechanic, CarWise.ai helps me diagnose problems faster and more accurately than traditional methods."</p>
            </div>
            <div class="flex items-center justify-center">
              <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">S</div>
              <div>
                <p class="font-semibold text-gray-900">Sarah Johnson</p>
                <p class="text-sm text-gray-600">Auto Mechanic</p>
              </div>
            </div>
          </div>
          
          <div class="text-center">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 mb-4">
              <div class="flex justify-center mb-4">
                <div class="flex text-yellow-400">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                </div>
              </div>
              <p class="text-gray-700 italic">"The Pro Garage plan is perfect for our fleet management. We've reduced maintenance costs by 30%!"</p>
            </div>
            <div class="flex items-center justify-center">
              <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">D</div>
              <div>
                <p class="font-semibold text-gray-900">David Chen</p>
                <p class="text-sm text-gray-600">Fleet Manager</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Frequently Asked Questions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Can I change my plan after purchase?</h4>
            <p class="text-gray-600 text-sm">Yes — upgrade immediately, downgrade at the beginning of the next period.</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Can I try it for free?</h4>
            <p class="text-gray-600 text-sm">Yes — 14-day trial on Care+ plan.</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">How do refunds work?</h4>
            <p class="text-gray-600 text-sm">Refund within 7 days for subscriptions (except parts purchases).</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Is there a long-term contract?</h4>
            <p class="text-gray-600 text-sm">No — monthly or yearly subscription with discount.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Modal -->
    <div v-if="showCheckoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Complete Your Purchase</h3>
            <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Selected Plan -->
          <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <h4 class="font-semibold text-gray-900">{{ selectedPlan?.name }}</h4>
            <p class="text-2xl font-bold text-blue-600">€{{ selectedPlan?.price }}/{{ selectedPlan?.billing_cycle === 'per_use' ? 'diagnosis' : 'month' }}</p>
            <p class="text-sm text-gray-600">{{ selectedPlan?.description }}</p>
          </div>

          <!-- Coupon Code -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code (Optional)</label>
            <div class="flex">
              <input 
                v-model="couponCode"
                type="text" 
                placeholder="Enter coupon code"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <button 
                @click="applyCoupon"
                class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors"
              >
                Apply
              </button>
            </div>
            <p v-if="couponMessage" class="text-sm mt-2" :class="couponValid ? 'text-green-600' : 'text-red-600'">
              {{ couponMessage }}
            </p>
          </div>

          <!-- Payment Method -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="stripe" class="mr-3">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.274 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.573-2.354 1.573-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                  </svg>
                  Credit Card (Stripe)
                </div>
              </label>
              <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="paypal" class="mr-3">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.105-.633c-.365-1.882-1.46-3.2-3.338-3.2H9.95c-.524 0-.968.382-1.05.9L7.78 19.337h4.696l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c3.298 0 6.664-1.747 8.647-6.797.03-.149.054-.294.077-.437z"/>
                  </svg>
                  PayPal
                </div>
              </label>
            </div>
          </div>

          <!-- Total -->
          <div class="border-t pt-4 mb-6">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-900">Total</span>
              <span class="text-2xl font-bold text-blue-600">€{{ finalPrice }}</span>
            </div>
          </div>

          <!-- Checkout Button -->
          <button 
            @click="processCheckout"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Complete Purchase</span>
          </button>

          <!-- Trial Notice -->
          <p v-if="selectedPlan?.trial_days > 0" class="text-sm text-gray-600 text-center mt-4">
            Start your {{ selectedPlan.trial_days }}-day free trial. Cancel anytime.
          </p>
        </div>
      </div>

      <!-- Money Back Guarantee -->
      <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-200 mb-12">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">30-Day Money Back Guarantee</h3>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Not satisfied with CarWise.ai? Get a full refund within 30 days, no questions asked. 
            We're confident you'll love our AI-powered car diagnostics.
          </p>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="text-center bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-12 text-white">
        <h3 class="text-3xl font-bold mb-4">Ready to Get Started?</h3>
        <p class="text-xl mb-8 opacity-90">
          Join thousands of car owners and mechanics who trust CarWise.ai for accurate diagnostics
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button 
            @click="selectPlan('care-plus')"
            class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-colors duration-300 transform hover:scale-105"
          >
            Start Free Trial
          </button>
          <button 
            @click="selectPlan('smart-ai')"
            class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-blue-600 transition-colors duration-300"
          >
            Try Smart AI
          </button>
        </div>
        <p class="text-sm mt-4 opacity-75">
          No credit card required for free trial • Cancel anytime
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { useSubscription } from '../composables/useSubscription'

const router = useRouter()
const route = useRoute()
const { user, isAuthenticated } = useAuth()
const { createSubscription, getPlans } = useSubscription()

// Reactive data
const billingCycle = ref('monthly')
const showCheckoutModal = ref(false)
const selectedPlan = ref(null)
const couponCode = ref('')
const couponValid = ref(false)
const couponMessage = ref('')
const paymentMethod = ref('stripe')
const loading = ref(false)

// Plans data
const plans = ref([
  {
    id: 'basic',
    name: 'Basic',
    price: 0,
    billing_cycle: 'monthly',
    description: 'Perfect for new users who want to try the service',
    trial_days: 0
  },
  {
    id: 'smart-ai',
    name: 'Smart AI',
    price: 7.99,
    billing_cycle: 'per_use',
    description: 'Pay-per-use detailed AI diagnosis with repair recommendations',
    trial_days: 0
  },
  {
    id: 'care-plus',
    name: 'Care+',
    price: 9.99,
    billing_cycle: 'monthly',
    description: 'Monthly coverage with preventive suggestions',
    trial_days: 14
  },
  {
    id: 'pro-garage',
    name: 'Pro Garage',
    price: 99,
    billing_cycle: 'monthly',
    description: 'For services & mechanics (B2B)',
    trial_days: 0
  }
])

// Computed
const finalPrice = computed(() => {
  if (!selectedPlan.value) return 0
  
  let price = selectedPlan.value.price
  
  // Apply yearly discount
  if (billingCycle.value === 'yearly' && selectedPlan.value.billing_cycle === 'monthly') {
    price = price * 0.85 // 15% discount
  }
  
  // Apply coupon discount
  if (couponValid.value && couponCode.value) {
    if (couponCode.value.includes('%')) {
      const discount = parseInt(couponCode.value.replace('%', ''))
      price = price * (1 - discount / 100)
    } else if (couponCode.value.startsWith('FIRST')) {
      price = Math.max(0, price - 10)
    }
  }
  
  return price.toFixed(2)
})

// Methods
const selectPlan = (planId) => {
  if (!isAuthenticated.value) {
    router.push('/login')
    return
  }
  
  selectedPlan.value = plans.value.find(p => p.id === planId)
  showCheckoutModal.value = true
  couponCode.value = ''
  couponValid.value = false
  couponMessage.value = ''
}

const applyCoupon = () => {
  if (!couponCode.value) {
    couponMessage.value = 'Please enter a coupon code'
    couponValid.value = false
    return
  }
  
  // Simulate coupon validation
  const validCoupons = ['20%OFF', 'FIRST10', 'SAVE15']
  
  if (validCoupons.includes(couponCode.value.toUpperCase())) {
    couponValid.value = true
    couponMessage.value = 'Coupon applied successfully!'
  } else {
    couponValid.value = false
    couponMessage.value = 'Invalid coupon code'
  }
}

const processCheckout = async () => {
  if (!selectedPlan.value) return
  
  loading.value = true
  
  try {
    // Simulate checkout process
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Create subscription
    const result = await createSubscription(selectedPlan.value.id)
    
    if (result.success) {
      // Show success message
      alert('Subscription created successfully!')
      showCheckoutModal.value = false
      
      // Redirect to dashboard
      router.push('/subscription/dashboard')
    } else {
      throw new Error(result.error || 'Failed to create subscription')
    }
  } catch (error) {
    console.error('Checkout failed:', error)
    alert('Checkout failed: ' + error.message)
  } finally {
    loading.value = false
  }
}

const goBackToDashboard = () => {
  router.push('/subscription/dashboard')
}

// Load plans from API
const loadPlansFromAPI = async () => {
  try {
    const apiPlans = await getPlans()
    
    // Update plans with API data
    plans.value = Object.values(apiPlans).map(plan => ({
      id: plan.id,
      name: plan.name,
      price: plan.price,
      billing_cycle: plan.billing_cycle,
      description: plan.description,
      trial_days: plan.trial_days || 0
    }))
    
    console.log('Plans loaded from API:', plans.value)
  } catch (error) {
    console.error('Failed to load plans from API:', error)
    // Keep default plans if API fails
  }
}

onMounted(() => {
  // Set page title
  document.title = 'Pricing & Subscription Plans - CarWise.ai'
  
  // Load plans from API
  loadPlansFromAPI()
  
  // Handle query parameters for upgrade flow
  const action = route.query.action
  const currentPlan = route.query.current_plan
  
  if (action === 'upgrade' && currentPlan) {
    // Highlight current plan and show upgrade options
    console.log('Upgrade flow from plan:', currentPlan)
    // You can add visual indicators or auto-scroll to relevant plans
  }
})
</script>

<style scoped>
/* Custom styles for the pricing page */
</style>
