<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
    <!-- Modern Header Section -->
    <div class="relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 opacity-90">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
          <div class="absolute top-20 right-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
          <div class="absolute bottom-10 left-1/3 w-80 h-80 bg-purple-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
        </div>
      </div>
      
      <!-- Header Content -->
      <div class="relative bg-white/10 dark:bg-slate-900/10 backdrop-blur-xl border-b border-white/20 dark:border-slate-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4 sm:space-x-6">
              <div class="relative">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-r from-white/20 to-white/10 rounded-3xl flex items-center justify-center shadow-2xl border border-white/30 backdrop-blur-sm overflow-hidden">
                  <span v-if="!user?.avatar" class="text-white font-bold text-2xl sm:text-3xl drop-shadow-lg">{{ userInitials }}</span>
                  <img v-else :src="`/storage/${user.avatar}`" :alt="user?.first_name || user?.name" class="w-full h-full object-cover">
                </div>
                <div class="absolute -inset-2 bg-gradient-to-r from-blue-400 to-purple-400 rounded-3xl blur opacity-30 animate-pulse"></div>
              </div>
              <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white drop-shadow-lg mb-2 truncate">
                  Welcome back, {{ user?.first_name || user?.name }}!
                </h1>
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <p class="text-white/90 text-sm sm:text-lg">
                      {{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}
                    </p>
                  </div>
                  <div class="hidden sm:block w-px h-6 bg-white/30"></div>
                  <p class="text-white/80 text-sm sm:text-lg">
                    {{ user?.location || 'No location set' }}
                  </p>
                </div>
              </div>
            </div>
            
            <div class="flex items-center justify-between lg:justify-end space-x-2 sm:space-x-4">
              <!-- Weather Widget -->
              <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl px-2 sm:px-4 py-2 sm:py-3 border border-white/20">
                <div class="flex items-center space-x-2 sm:space-x-3">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-400/20 rounded-lg flex items-center justify-center">
                    <svg class="w-3 h-3 sm:w-5 sm:h-5 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.38l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-white/90 text-xs sm:text-sm font-medium">{{ weather.temperature }}°C</p>
                    <p class="text-white/70 text-xs hidden sm:block">{{ weather.condition }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Notifications -->
              <div class="relative">
                <button @click="handleNotifications" class="p-2 sm:p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl sm:rounded-2xl border border-white/20 transition-all duration-200 relative group">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4 7h8l-2 2H6l-2-2z"></path>
                  </svg>
                  <span v-if="notifications.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-6 sm:w-6 flex items-center justify-center font-bold animate-pulse">
                    {{ notifications.length }}
                  </span>
                </button>
              </div>
              
              <!-- Settings -->
              <button @click="handleSettings" class="px-3 sm:px-6 py-2 sm:py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl sm:rounded-2xl border border-white/20 text-white font-medium transition-all duration-200 flex items-center space-x-1 sm:space-x-2 group">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="hidden sm:inline text-sm sm:text-base">Settings</span>
              </button>
              
              <!-- Logout -->
              <button @click="handleLogout" class="px-3 sm:px-6 py-2 sm:py-3 bg-red-500/20 hover:bg-red-500/30 backdrop-blur-sm rounded-xl sm:rounded-2xl border border-red-400/30 text-white font-medium transition-all duration-200 flex items-center space-x-1 sm:space-x-2 group">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="hidden sm:inline text-sm sm:text-base">Logout</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Quick Actions -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Modern Quick Actions -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center mb-8">
              <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Quick Actions</h2>
                <p class="text-slate-600 dark:text-slate-400">Access your most used features</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- AI Diagnosis -->
              <router-link to="/diagnose" class="group p-6 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-2xl text-white hover:from-blue-600 hover:via-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                <div class="flex flex-col items-center text-center">
                  <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">AI Diagnosis</h3>
                  <p class="text-blue-100 text-sm">Instant car analysis</p>
                </div>
              </router-link>

              <!-- My Cars -->
              <router-link to="/my-cars" class="group p-6 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 rounded-2xl text-white hover:from-emerald-600 hover:via-teal-600 hover:to-cyan-700 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                <div class="flex flex-col items-center text-center">
                  <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">My Cars</h3>
                  <p class="text-emerald-100 text-sm">Manage vehicles</p>
                </div>
              </router-link>

              <!-- Car Parts Store -->
              <router-link to="/car-parts" class="group p-6 bg-gradient-to-br from-orange-500 via-red-500 to-pink-600 rounded-2xl text-white hover:from-orange-600 hover:via-red-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                <div class="flex flex-col items-center text-center">
                  <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">Parts Store</h3>
                  <p class="text-orange-100 text-sm">Buy car parts</p>
                </div>
              </router-link>

              <!-- Live Chat -->
              <div @click="toggleLiveChat" class="group p-6 bg-gradient-to-br from-purple-500 via-indigo-500 to-blue-600 rounded-2xl text-white hover:from-purple-600 hover:via-indigo-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 hover:shadow-xl cursor-pointer">
                <div class="flex flex-col items-center text-center">
                  <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">Live Chat</h3>
                  <p class="text-purple-100 text-sm">Get support</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Diagnoses -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-semibold text-secondary-900 dark:text-white">Recent Diagnoses</h2>
              <router-link to="/diagnose" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">
                View All
              </router-link>
            </div>
            <div class="space-y-4">
              <div v-if="recentDiagnoses.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-secondary-900 dark:text-white">No diagnoses yet</h3>
                <p class="mt-1 text-sm text-secondary-500 dark:text-secondary-400">Start by diagnosing your first car issue.</p>
                <div class="mt-4">
                  <router-link to="/diagnose" class="btn-primary">
                    Start Diagnosis
                  </router-link>
                </div>
              </div>
              <div v-else>
                <div v-for="diagnosis in recentDiagnoses.slice(0, 3)" :key="diagnosis.id" class="flex items-center space-x-4 p-4 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="getDiagnosisStatusColor(diagnosis.status)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-sm font-medium text-secondary-900 dark:text-white">{{ diagnosis.vehicle_info?.make }} {{ diagnosis.vehicle_info?.model }}</h4>
                    <p class="text-xs text-secondary-600 dark:text-secondary-400">{{ diagnosis.description || 'No description' }}</p>
                  </div>
                  <div class="text-right">
                    <div class="text-xs text-secondary-500 dark:text-secondary-500">
                      {{ formatDate(diagnosis.created_at) }}
                    </div>
                    <div class="text-xs font-medium" :class="getDiagnosisStatusTextColor(diagnosis.status)">
                      {{ diagnosis.status }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Maintenance Reminders -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center mb-8">
              <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Maintenance Reminders</h2>
                <p class="text-slate-600 dark:text-slate-400">Keep your vehicles in top condition</p>
              </div>
            </div>
            
            <div class="space-y-4">
              <div v-for="reminder in maintenanceReminders" :key="reminder.id" class="p-6 bg-gradient-to-r from-amber-50 via-orange-50 to-red-50 dark:from-amber-900/20 dark:via-orange-900/20 dark:to-red-900/20 rounded-2xl border border-amber-200 dark:border-amber-700/50 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="getReminderUrgencyClass(reminder.urgency)">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div>
                      <h4 class="font-semibold text-slate-900 dark:text-white">{{ reminder.title }}</h4>
                      <p class="text-sm text-slate-600 dark:text-slate-400">{{ reminder.carName }} • {{ reminder.description }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-medium" :class="getReminderUrgencyTextClass(reminder.urgency)">
                      {{ reminder.dueDate }}
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                      {{ reminder.mileage }} km
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-if="maintenanceReminders.length === 0" class="text-center py-8">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">All caught up!</h3>
                <p class="text-slate-600 dark:text-slate-400">No maintenance reminders at this time.</p>
              </div>
            </div>
          </div>

          <!-- AI Insights & Recommendations -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center mb-8">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">AI Insights</h2>
                <p class="text-slate-600 dark:text-slate-400">Smart recommendations for your vehicles</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div v-for="insight in aiInsights" :key="insight.id" class="p-6 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 dark:from-purple-900/20 dark:via-pink-900/20 dark:to-indigo-900/20 rounded-2xl border border-purple-200 dark:border-purple-700/50 hover:shadow-lg transition-all duration-200">
                <div class="flex items-start space-x-4">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="getInsightTypeClass(insight.type)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getInsightIcon(insight.type)"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-2">{{ insight.title }}</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">{{ insight.description }}</p>
                    <div class="flex items-center justify-between">
                      <span class="text-xs px-3 py-1 rounded-full" :class="getInsightPriorityClass(insight.priority)">
                        {{ insight.priority }}
                      </span>
                      <button v-if="insight.action" @click="handleInsightAction(insight)" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium">
                        {{ insight.action }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Profile & Stats -->
        <div class="space-y-8">
          <!-- Profile Card -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Profile</h2>
            <div class="space-y-4">
              <div class="flex items-center space-x-3">
                <div class="w-12 h-12 gradient-primary rounded-xl flex items-center justify-center overflow-hidden">
                  <span v-if="!user?.avatar" class="text-white font-bold text-lg">{{ userInitials }}</span>
                  <img v-else :src="`/storage/${user.avatar}`" :alt="user?.first_name || user?.name" class="w-full h-full object-cover">
                </div>
                <div>
                  <h3 class="font-semibold text-secondary-900 dark:text-white">{{ user?.full_name || user?.name }}</h3>
                  <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ user?.email }}</p>
                </div>
              </div>
              
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-secondary-600 dark:text-secondary-400">Role:</span>
                  <span class="text-secondary-900 dark:text-white font-medium">{{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-secondary-600 dark:text-secondary-400">Location:</span>
                  <span class="text-secondary-900 dark:text-white font-medium">{{ user?.location || 'Not set' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-secondary-600 dark:text-secondary-400">Member since:</span>
                  <span class="text-secondary-900 dark:text-white font-medium">{{ formatDate(user?.created_at) }}</span>
                </div>
              </div>

              <button @click="handleEditProfile" class="w-full btn-outline text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Profile
              </button>
            </div>
          </div>

          <!-- Statistics -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Statistics</h2>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                <div>
                  <p class="text-sm text-primary-600 dark:text-primary-400">Total Diagnoses</p>
                  <p class="text-2xl font-bold text-primary-900 dark:text-primary-100">{{ stats.totalDiagnoses }}</p>
                  <p class="text-xs text-primary-500 dark:text-primary-400">+{{ stats.diagnosesThisMonth }} this month, +{{ stats.diagnosesThisWeek }} this week</p>
                </div>
                <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>

              <div class="flex items-center justify-between p-4 bg-info-50 dark:bg-info-900/20 rounded-lg">
                <div>
                  <p class="text-sm text-info-600 dark:text-info-400">Avg Response Time</p>
                  <p class="text-2xl font-bold text-info-900 dark:text-info-100">{{ stats.averageResponseTime }}s</p>
                  <p class="text-xs text-info-500 dark:text-info-400">AI processing time</p>
                </div>
                <svg class="w-8 h-8 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>

              <div class="flex items-center justify-between p-4 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg">
                <div>
                  <p class="text-sm text-secondary-600 dark:text-secondary-400">Cars Registered</p>
                  <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ stats.carsRegistered }}</p>
                  <p class="text-xs text-secondary-500 dark:text-secondary-400">{{ stats.activeCars }} active</p>
                </div>
                <svg class="w-8 h-8 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>

              <div class="flex items-center justify-between p-4 bg-success-50 dark:bg-success-900/20 rounded-lg">
                <div>
                  <p class="text-sm text-success-600 dark:text-success-400">Success Rate</p>
                  <p class="text-2xl font-bold text-success-900 dark:text-success-100">{{ stats.successRate }}%</p>
                  <p class="text-xs text-success-500 dark:text-success-400">Accurate diagnoses</p>
                </div>
                <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>

              <div v-if="user?.role === 'mechanic'" class="flex items-center justify-between p-4 bg-accent-50 dark:bg-accent-900/20 rounded-lg">
                <div>
                  <p class="text-sm text-accent-600 dark:text-accent-400">Experience</p>
                  <p class="text-2xl font-bold text-accent-900 dark:text-accent-100">{{ stats.experienceYears }} years</p>
                  <p class="text-xs text-accent-500 dark:text-accent-400">{{ stats.clientsHelped }} clients helped</p>
                </div>
                <svg class="w-8 h-8 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Analytics Charts -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Diagnosis Analytics</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Monthly Trend Chart -->
              <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-4">
                <h3 class="font-semibold text-primary-900 dark:text-primary-100 mb-4">Monthly Diagnoses</h3>
                <div class="h-32 flex items-end justify-between space-x-2">
                  <div v-for="(month, index) in stats.monthlyDiagnoses" :key="index" 
                       class="flex-1 bg-primary-500 rounded-t-lg transition-all duration-300 hover:bg-primary-600"
                       :style="{ height: `${(month.value / Math.max(...stats.monthlyDiagnoses.map(m => m.value), 1)) * 100}%` }"
                       :title="`${month.month}: ${month.value} diagnoses`">
                  </div>
                </div>
                <div class="flex justify-between text-xs text-primary-700 dark:text-primary-300 mt-2">
                  <span v-for="(month, index) in stats.monthlyDiagnoses" :key="index">{{ month.month }}</span>
                </div>
              </div>

              <!-- Issue Categories -->
              <div class="bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/20 dark:to-secondary-800/20 rounded-xl p-4">
                <h3 class="font-semibold text-secondary-900 dark:text-secondary-100 mb-4">Common Issues</h3>
                <div class="space-y-3">
                  <div v-for="(issue, index) in stats.mostCommonIssues.slice(0, 5)" :key="index" 
                       class="flex items-center justify-between">
                    <span class="text-sm text-secondary-700 dark:text-secondary-300">{{ issue.name }}</span>
                    <div class="flex items-center space-x-2">
                      <div class="w-16 bg-secondary-200 dark:bg-secondary-700 rounded-full h-2">
                        <div class="bg-secondary-500 h-2 rounded-full" 
                             :style="{ width: `${(issue.count / Math.max(...stats.mostCommonIssues.map(i => i.count), 1)) * 100}%` }">
                        </div>
                      </div>
                      <span class="text-xs text-secondary-600 dark:text-secondary-400 w-8">{{ issue.count }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Insights -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Quick Insights</h2>
            <div class="space-y-4">
              <div class="p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg border-l-4 border-warning-400">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-warning-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-warning-800 dark:text-warning-200">Maintenance Reminder</p>
                    <p class="text-xs text-warning-600 dark:text-warning-400">2 cars need service soon</p>
                  </div>
                </div>
              </div>

              <div class="p-4 bg-info-50 dark:bg-info-900/20 rounded-lg border-l-4 border-info-400">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-info-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-info-800 dark:text-info-200">AI Tips</p>
                    <p class="text-xs text-info-600 dark:text-info-400">Check tire pressure monthly</p>
                  </div>
                </div>
              </div>

              <div class="p-4 bg-success-50 dark:bg-success-900/20 rounded-lg border-l-4 border-success-400">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-success-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-success-800 dark:text-success-200">All Systems Good</p>
                    <p class="text-xs text-success-600 dark:text-success-400">No critical issues detected</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications Modal -->
    <div v-if="showNotificationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl max-w-md w-full max-h-96 overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-secondary-200 dark:border-secondary-700">
          <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">Notifications</h3>
          <button @click="closeNotificationsModal" class="p-2 text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-80">
          <div v-if="notifications.length === 0" class="text-center py-8">
            <svg class="w-12 h-12 text-secondary-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4 7h8l-2 2H6l-2-2z"></path>
            </svg>
            <p class="text-secondary-500 dark:text-secondary-400">No notifications yet</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="notification in notifications" :key="notification.id" 
                 class="p-4 rounded-lg border border-secondary-200 dark:border-secondary-700"
                 :class="{
                   'bg-info-50 dark:bg-info-900/20 border-info-200 dark:border-info-700': notification.type === 'info',
                   'bg-success-50 dark:bg-success-900/20 border-success-200 dark:border-success-700': notification.type === 'success',
                   'bg-warning-50 dark:bg-warning-900/20 border-warning-200 dark:border-warning-700': notification.type === 'warning',
                   'bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-700': notification.type === 'error'
                 }">
              <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <svg v-if="notification.type === 'info'" class="w-5 h-5 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else-if="notification.type === 'success'" class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else-if="notification.type === 'warning'" class="w-5 h-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-sm font-medium text-secondary-900 dark:text-white">{{ notification.title }}</h4>
                  <p class="text-sm text-secondary-600 dark:text-secondary-400 mt-1">{{ notification.message }}</p>
                  <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-2">{{ notification.time }}</p>
                </div>
                <div v-if="!notification.read" class="flex-shrink-0">
                  <div class="w-2 h-2 bg-primary-500 rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-secondary-200 dark:border-secondary-700">
          <button @click="closeNotificationsModal" class="btn-outline text-sm">
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Live Chat Widget -->
    <LiveChatWidget :is-cart-open="false" />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI, carsAPI, diagnosisAPI, dashboardAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'
import LiveChatWidget from '../components/LiveChatWidget.vue'

export default {
  name: 'Dashboard',
  components: {
    LiveChatWidget
  },
  setup() {
    const router = useRouter()
    const isLoading = ref(true)
    
    // Use the global auth state
    const { user, isAuthenticated, checkAuth, logout } = useAuth()
    
    // Weather data
    const weather = ref({
      temperature: 22,
      condition: 'Sunny',
      humidity: 65,
      windSpeed: 12
    })
    
    // Maintenance reminders
    const maintenanceReminders = ref([
      {
        id: 1,
        title: 'Oil Change Due',
        carName: 'Volkswagen Golf 2014',
        description: 'Regular oil change required',
        dueDate: 'In 3 days',
        mileage: '295,000 km',
        urgency: 'high'
      },
      {
        id: 2,
        title: 'Brake Inspection',
        carName: 'BMW X3 2018',
        description: 'Brake pads need checking',
        dueDate: 'In 1 week',
        mileage: '85,000 km',
        urgency: 'medium'
      }
    ])
    
    // AI Insights
    const aiInsights = ref([
      {
        id: 1,
        type: 'maintenance',
        title: 'Winter Preparation',
        description: 'Based on weather data, consider winter tire installation and antifreeze check.',
        priority: 'High',
        action: 'View Details'
      },
      {
        id: 2,
        type: 'fuel',
        title: 'Fuel Efficiency Tip',
        description: 'Your driving patterns suggest 15% fuel savings with eco-driving techniques.',
        priority: 'Medium',
        action: 'Learn More'
      },
      {
        id: 3,
        type: 'safety',
        title: 'Safety Check',
        description: 'All safety systems are functioning properly. Next check due in 2 months.',
        priority: 'Low',
        action: 'Schedule'
      },
      {
        id: 4,
        type: 'cost',
        title: 'Cost Optimization',
        description: 'You could save €200/year by switching to synthetic oil for your Golf.',
        priority: 'Medium',
        action: 'Compare Prices'
      }
    ])

    const userInitials = computed(() => {
      if (!user.value) return 'U'
      const first = user.value.first_name ? user.value.first_name[0] : (user.value.name ? user.value.name[0] : 'U')
      const last = user.value.last_name ? user.value.last_name[0] : ''
      return (first + last).toUpperCase()
    })

    const stats = ref({
      totalDiagnoses: 0,
      diagnosesThisMonth: 0,
      diagnosesThisWeek: 0,
      carsRegistered: 0,
      activeCars: 0,
      successRate: 0,
      experienceYears: 0,
      clientsHelped: 0,
      averageResponseTime: 0,
      totalCostSaved: 0,
      mostCommonIssues: [],
      diagnosisTrends: [],
      monthlyDiagnoses: [],
      weeklyDiagnoses: []
    })

    const recentDiagnoses = ref([])
    const notifications = ref([
      {
        id: 1,
        type: 'info',
        title: 'Welcome to CarWise AI!',
        message: 'Your account has been created successfully.',
        time: 'Just now',
        read: false
      },
      {
        id: 2,
        type: 'success',
        title: 'Diagnosis Completed',
        message: 'Your car diagnosis has been completed successfully.',
        time: '2 hours ago',
        read: false
      }
    ])
    
    const recentActivity = ref([
      {
        id: 1,
        title: 'Account Created',
        description: 'Welcome to CarWise AI!',
        time: 'Just now'
      }
    ])

    const loadUserData = async () => {
      try {
        // Check auth state first
        await checkAuth()
        if (isAuthenticated.value && user.value) {
          await loadStatistics()
        } else {
          // Redirect to login if not authenticated
          router.push('/login')
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        // Redirect to login if not authenticated
        router.push('/login')
      } finally {
        isLoading.value = false
      }
    }

    const loadStatistics = async () => {
      try {
        // Load comprehensive dashboard statistics
        const dashboardResponse = await dashboardAPI.getStatistics()
        if (dashboardResponse.data.success) {
          const data = dashboardResponse.data.data
          
          // Basic stats
          stats.value.totalDiagnoses = data.basic_stats.total_diagnoses
          stats.value.diagnosesThisMonth = data.time_stats.diagnoses_this_month
          stats.value.diagnosesThisWeek = data.time_stats.diagnoses_this_week
          stats.value.carsRegistered = data.basic_stats.total_cars
          stats.value.activeCars = data.basic_stats.active_cars
          stats.value.successRate = data.basic_stats.success_rate
          stats.value.averageResponseTime = data.time_stats.average_response_time
          stats.value.totalCostSaved = data.analytics.total_cost_saved
          
          // Analytics data
          stats.value.monthlyDiagnoses = data.analytics.monthly_diagnoses
          stats.value.mostCommonIssues = data.analytics.common_issues
          
          // Recent diagnoses
          recentDiagnoses.value = data.recent_diagnoses
          
          // User-specific stats
          if (user.value?.role === 'mechanic') {
            stats.value.experienceYears = data.user_info.experience_years
            stats.value.clientsHelped = data.user_info.clients_helped
          }
        }

        // Load notifications
        const notificationsResponse = await dashboardAPI.getNotifications()
        if (notificationsResponse.data.success) {
          notifications.value = notificationsResponse.data.data
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
        // Set default values if API calls fail
        stats.value = {
          totalDiagnoses: 0,
          diagnosesThisMonth: 0,
          diagnosesThisWeek: 0,
          carsRegistered: 0,
          activeCars: 0,
          successRate: 0,
          experienceYears: user.value?.role === 'mechanic' ? 5 : 0,
          clientsHelped: 0,
          averageResponseTime: 0,
          totalCostSaved: 0,
          mostCommonIssues: [],
          diagnosisTrends: [],
          monthlyDiagnoses: [],
          weeklyDiagnoses: []
        }
      }
    }

    // Helper functions for data generation
    const getWeekNumber = (date) => {
      const firstDayOfYear = new Date(date.getFullYear(), 0, 1)
      const pastDaysOfYear = (date - firstDayOfYear) / 86400000
      return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7)
    }

    const generateMonthlyData = (diagnoses) => {
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      const currentYear = new Date().getFullYear()
      const monthlyData = months.map(month => ({ month, value: 0 }))
      
      diagnoses.forEach(diagnosis => {
        const date = new Date(diagnosis.created_at)
        if (date.getFullYear() === currentYear) {
          monthlyData[date.getMonth()].value++
        }
      })
      
      return monthlyData
    }

    const generateCommonIssues = (diagnoses) => {
      const issues = {}
      diagnoses.forEach(diagnosis => {
        if (diagnosis.result?.likely_causes) {
          const causes = Array.isArray(diagnosis.result.likely_causes) 
            ? diagnosis.result.likely_causes 
            : JSON.parse(diagnosis.result.likely_causes || '[]')
          
          causes.forEach(cause => {
            const issueName = cause.title || 'Unknown Issue'
            issues[issueName] = (issues[issueName] || 0) + 1
          })
        }
      })
      
      return Object.entries(issues)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 10)
    }

    const handleEditProfile = () => {
      // Navigate to profile edit page or open modal
      router.push('/profile/edit')
    }

    const showNotificationsModal = ref(false)

    const handleNotifications = () => {
      // Show notifications modal
      showNotificationsModal.value = true
    }

    const closeNotificationsModal = () => {
      showNotificationsModal.value = false
    }

    const handleSettings = () => {
      // Navigate to profile edit page for settings
      router.push('/profile/edit')
    }

    const handleHistory = () => {
      // Navigate to diagnosis history page
      router.push('/diagnosis/history')
    }

    const handleLogout = async () => {
      await logout()
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      })
    }

    const getDiagnosisStatusColor = (status) => {
      switch (status?.toLowerCase()) {
        case 'completed': return 'bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-400'
        case 'processing': return 'bg-warning-100 dark:bg-warning-900 text-warning-600 dark:text-warning-400'
        case 'pending': return 'bg-info-100 dark:bg-info-900 text-info-600 dark:text-info-400'
        case 'failed': return 'bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-400'
        default: return 'bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'
      }
    }

    const getDiagnosisStatusTextColor = (status) => {
      switch (status?.toLowerCase()) {
        case 'completed': return 'text-success-600 dark:text-success-400'
        case 'processing': return 'text-warning-600 dark:text-warning-400'
        case 'pending': return 'text-info-600 dark:text-info-400'
        case 'failed': return 'text-danger-600 dark:text-danger-400'
        default: return 'text-secondary-600 dark:text-secondary-400'
      }
    }

    // Maintenance reminder functions
    const getReminderUrgencyClass = (urgency) => {
      switch (urgency?.toLowerCase()) {
        case 'high': return 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
        case 'medium': return 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'
        case 'low': return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
        default: return 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
      }
    }

    const getReminderUrgencyTextClass = (urgency) => {
      switch (urgency?.toLowerCase()) {
        case 'high': return 'text-red-600 dark:text-red-400'
        case 'medium': return 'text-amber-600 dark:text-amber-400'
        case 'low': return 'text-blue-600 dark:text-blue-400'
        default: return 'text-gray-600 dark:text-gray-400'
      }
    }

    // AI Insights functions
    const getInsightTypeClass = (type) => {
      switch (type?.toLowerCase()) {
        case 'maintenance': return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
        case 'fuel': return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
        case 'safety': return 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
        case 'cost': return 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
        default: return 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
      }
    }

    const getInsightIcon = (type) => {
      switch (type?.toLowerCase()) {
        case 'maintenance': return 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
        case 'fuel': return 'M13 10V3L4 14h7v7l9-11h-7z'
        case 'safety': return 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
        case 'cost': return 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'
        default: return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
      }
    }

    const getInsightPriorityClass = (priority) => {
      switch (priority?.toLowerCase()) {
        case 'high': return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
        case 'medium': return 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'
        case 'low': return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
        default: return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
      }
    }

    const handleInsightAction = (insight) => {
      // Handle insight action based on type
      switch (insight.type) {
        case 'maintenance':
          router.push('/my-cars')
          break
        case 'fuel':
          alert('Fuel efficiency tips coming soon!')
          break
        case 'safety':
          router.push('/diagnose')
          break
        case 'cost':
          router.push('/car-parts')
          break
        default:
          console.log('Insight action:', insight.action)
      }
    }

    const toggleLiveChat = () => {
      // This will be handled by the LiveChatWidget component
      console.log('Live chat toggled')
    }

    onMounted(() => {
      // Check if user is logged in
      const token = localStorage.getItem('token')
      const storedUser = localStorage.getItem('user')
      
      console.log('Dashboard mounted, checking auth...', { token: !!token, user: !!storedUser })
      
      if (token && storedUser) {
        user.value = JSON.parse(storedUser)
        console.log('User found, loading data...', user.value)
        loadUserData()
      } else {
        console.log('No auth found, redirecting to login...')
        router.push('/login')
      }
    })

    return {
      user,
      userInitials,
      stats,
      recentActivity,
      recentDiagnoses,
      notifications,
      isLoading,
      showNotificationsModal,
      weather,
      maintenanceReminders,
      aiInsights,
      handleEditProfile,
      handleNotifications,
      closeNotificationsModal,
      handleSettings,
      handleHistory,
      handleLogout,
      formatDate,
      getDiagnosisStatusColor,
      getDiagnosisStatusTextColor,
      getReminderUrgencyClass,
      getReminderUrgencyTextClass,
      getInsightTypeClass,
      getInsightIcon,
      getInsightPriorityClass,
      handleInsightAction,
      toggleLiveChat
    }
  }
}
</script>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.glass {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}
</style>
