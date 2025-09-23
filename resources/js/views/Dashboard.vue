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
            <button class="btn-secondary">
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

              <router-link to="/mechanics" class="group p-6 bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl text-white hover:from-accent-600 hover:to-accent-700 transition-all duration-200 transform hover:scale-105">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-semibold mb-2">Find Mechanics</h3>
                    <p class="text-accent-100 text-sm">Connect with experts</p>
                  </div>
                  <svg class="w-8 h-8 text-accent-200 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
              </router-link>

              <div class="group p-6 bg-gradient-to-br from-success-500 to-success-600 rounded-xl text-white hover:from-success-600 hover:to-success-700 transition-all duration-200 transform hover:scale-105 cursor-pointer">
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

              <button class="w-full btn-outline text-sm">
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
                  <p class="text-xs text-primary-500 dark:text-primary-400">+{{ stats.diagnosesThisMonth }} this month</p>
                </div>
                <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI, carsAPI, diagnosisAPI } from '../services/api'

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
      carsRegistered: 0,
      activeCars: 0,
      successRate: 0,
      experienceYears: 0,
      clientsHelped: 0
    })

    const recentDiagnoses = ref([])
    
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
        // Load car statistics
        const carsResponse = await carsAPI.statistics()
        if (carsResponse.data.success) {
          stats.value.carsRegistered = carsResponse.data.statistics.total_cars || 0
          stats.value.activeCars = carsResponse.data.statistics.active_cars || 0
        }

        // Load diagnosis statistics
        const diagnosisResponse = await diagnosisAPI.getHistory()
        if (diagnosisResponse.data.success) {
          const diagnoses = diagnosisResponse.data.diagnoses || []
          stats.value.totalDiagnoses = diagnoses.length
          recentDiagnoses.value = diagnoses.slice(0, 5) // Store recent diagnoses
          
          // Calculate this month's diagnoses
          const thisMonth = new Date().getMonth()
          const thisYear = new Date().getFullYear()
          stats.value.diagnosesThisMonth = diagnoses.filter(d => {
            const diagnosisDate = new Date(d.created_at)
            return diagnosisDate.getMonth() === thisMonth && diagnosisDate.getFullYear() === thisYear
          }).length

          // Calculate success rate (assuming completed diagnoses are successful)
          const completedDiagnoses = diagnoses.filter(d => d.status === 'completed')
          stats.value.successRate = diagnoses.length > 0 ? Math.round((completedDiagnoses.length / diagnoses.length) * 100) : 0
        }

        // Set experience years for mechanics
        if (user.value?.role === 'mechanic') {
          stats.value.experienceYears = user.value.experience_years || 0
          stats.value.clientsHelped = Math.floor(Math.random() * 50) + 10 // Mock data for now
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
        // Set default values if API calls fail
        stats.value = {
          totalDiagnoses: 0,
          diagnosesThisMonth: 0,
          carsRegistered: 0,
          activeCars: 0,
          successRate: 0,
          experienceYears: user.value?.role === 'mechanic' ? 5 : 0,
          clientsHelped: 0
        }
      }
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
      isLoading,
      handleLogout,
      formatDate,
      getDiagnosisStatusColor,
      getDiagnosisStatusTextColor
    }
  }
}
</script>
