<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <!-- Header Section -->
    <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-2xl">{{ userInitials }}</span>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">
                Welcome back, {{ user?.first_name || user?.name }}!
              </h1>
              <p class="text-secondary-600 dark:text-secondary-400">
                {{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }} • {{ user?.location || 'No location set' }}
              </p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
              <button @click="handleNotifications" class="p-2 text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-white transition-colors duration-200 relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4 7h8l-2 2H6l-2-2z"></path>
                </svg>
                <span v-if="notifications.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {{ notifications.length }}
                </span>
              </button>
            </div>
            
            <button @click="handleSettings" class="btn-secondary">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Settings
            </button>
            <button @click="handleLogout" class="btn-outline">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Quick Actions -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Quick Actions -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <router-link to="/diagnose" class="group p-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl text-white hover:from-primary-600 hover:to-primary-700 transition-all duration-200 transform hover:scale-105">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-semibold mb-2">AI Diagnosis</h3>
                    <p class="text-primary-100 text-sm">Get instant car diagnosis</p>
                  </div>
                  <svg class="w-8 h-8 text-primary-200 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </router-link>

              <router-link to="/my-cars" class="group p-6 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl text-white hover:from-secondary-600 hover:to-secondary-700 transition-all duration-200 transform hover:scale-105">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-semibold mb-2">My Cars</h3>
                    <p class="text-secondary-100 text-sm">Manage your vehicles</p>
                  </div>
                  <svg class="w-8 h-8 text-secondary-200 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
              </router-link>


              <div @click="handleHistory" class="group p-6 bg-gradient-to-br from-success-500 to-success-600 rounded-xl text-white hover:from-success-600 hover:to-success-700 transition-all duration-200 transform hover:scale-105 cursor-pointer">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-semibold mb-2">History</h3>
                    <p class="text-success-100 text-sm">View past diagnoses</p>
                  </div>
                  <svg class="w-8 h-8 text-success-200 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
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

          <!-- Recent Activity -->
          <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20">
            <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Recent Activity</h2>
            <div class="space-y-4">
              <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center space-x-4 p-4 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <h4 class="text-sm font-medium text-secondary-900 dark:text-white">{{ activity.title }}</h4>
                  <p class="text-xs text-secondary-600 dark:text-secondary-400">{{ activity.description }}</p>
                </div>
                <div class="text-xs text-secondary-500 dark:text-secondary-500">
                  {{ activity.time }}
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
                <div class="w-12 h-12 gradient-primary rounded-xl flex items-center justify-center">
                  <span class="text-white font-bold text-lg">{{ userInitials }}</span>
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
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI, carsAPI, diagnosisAPI, dashboardAPI } from '../services/api'

export default {
  name: 'Dashboard',
  setup() {
    const router = useRouter()
    const user = ref(null)
    const isLoading = ref(true)

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
        const response = await authAPI.getUser()
        if (response.data.success) {
          user.value = response.data.user
          await loadStatistics()
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
      // Navigate to settings page or open settings modal
      alert('Settings feature coming soon!')
    }

    const handleHistory = () => {
      // Navigate to diagnosis history page
      router.push('/diagnosis/history')
    }

    const handleLogout = async () => {
      try {
        await authAPI.logout()
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
        // Force logout even if API call fails
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      }
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
      handleEditProfile,
      handleNotifications,
      closeNotificationsModal,
      handleSettings,
      handleHistory,
      handleLogout,
      formatDate,
      getDiagnosisStatusColor,
      getDiagnosisStatusTextColor
    }
  }
}
</script>
