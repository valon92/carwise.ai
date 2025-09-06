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
                </div>
                <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>

              <div class="flex items-center justify-between p-4 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg">
                <div>
                  <p class="text-sm text-secondary-600 dark:text-secondary-400">Cars Registered</p>
                  <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ stats.carsRegistered }}</p>
                </div>
                <svg class="w-8 h-8 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>

              <div v-if="user?.role === 'mechanic'" class="flex items-center justify-between p-4 bg-accent-50 dark:bg-accent-900/20 rounded-lg">
                <div>
                  <p class="text-sm text-accent-600 dark:text-accent-400">Experience</p>
                  <p class="text-2xl font-bold text-accent-900 dark:text-accent-100">{{ stats.experienceYears }} years</p>
                </div>
                <svg class="w-8 h-8 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
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
import { authAPI } from '../services/api'

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
      carsRegistered: 0,
      experienceYears: 0
    })

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
          // Load additional stats here
          stats.value = {
            totalDiagnoses: 0,
            carsRegistered: 0,
            experienceYears: user.value.role === 'mechanic' ? 5 : 0 // This would come from API
          }
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        // Redirect to login if not authenticated
        router.push('/login')
      } finally {
        isLoading.value = false
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
      isLoading,
      handleLogout,
      formatDate
    }
  }
}
</script>
