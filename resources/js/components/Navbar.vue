<template>
  <nav class="sticky top-0 z-40 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center group">
            <div class="w-10 h-10 gradient-primary rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
              <span class="text-white font-bold text-xl">C</span>
            </div>
            <span class="ml-3 text-xl font-bold text-secondary-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
              CarWise AI
            </span>
          </router-link>
        </div>
        
        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-1">
          <router-link 
            to="/" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Home
          </router-link>
          <router-link 
            to="/diagnose" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/diagnose' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Diagnose
          </router-link>
          <router-link 
            to="/my-cars" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/my-cars' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            My Cars
          </router-link>
          <router-link 
            to="/mechanics" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/mechanics' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Mechanics
          </router-link>
        </div>
        
        <!-- Right side actions -->
        <div class="flex items-center space-x-3">
          <!-- Dark mode toggle -->
          <button 
            @click="$emit('toggle-dark-mode')"
            class="p-2 rounded-lg text-secondary-600 dark:text-secondary-400 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
            :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <svg v-if="!isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </button>

          <!-- Mobile menu button -->
          <button 
            @click="toggleMobileMenu"
            class="md:hidden p-3 rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors duration-200 border-2 border-primary-500 shadow-lg"
            :class="{ 'bg-primary-700': mobileMenuOpen }"
            style="z-index: 9999; pointer-events: auto; cursor: pointer;"
            title="Toggle Mobile Menu"
          >
            <span class="sr-only">Toggle Mobile Menu</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <!-- Auth buttons -->
          <template v-if="!isAuthenticated">
            <router-link to="/login" class="btn-ghost hidden sm:inline-flex">
              Login
            </router-link>
            <router-link to="/register" class="btn-primary">
              Register
            </router-link>
          </template>
          <template v-else>
            <div class="flex items-center space-x-3">
              <div class="hidden sm:flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                  <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                    {{ userInitials }}
                  </span>
                </div>
                <span class="text-sm font-medium text-secondary-700 dark:text-secondary-300">{{ user.name }}</span>
              </div>
              <button @click="logout" class="btn-ghost">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="mobileMenuOpen" class="mobile-menu-container md:hidden border-t-2 border-primary-500 bg-primary-50 dark:bg-primary-900 py-6 space-y-3" style="display: block !important; position: relative; z-index: 50;">
        <router-link 
          to="/" 
          class="mobile-nav-link"
          :class="{ 'mobile-nav-link-active': $route.path === '/' }"
          @click="mobileMenuOpen = false"
        >
          Home
        </router-link>
        <router-link 
          to="/diagnose" 
          class="mobile-nav-link"
          :class="{ 'mobile-nav-link-active': $route.path === '/diagnose' }"
          @click="mobileMenuOpen = false"
        >
          Diagnose
        </router-link>
        <router-link 
          to="/my-cars" 
          class="mobile-nav-link"
          :class="{ 'mobile-nav-link-active': $route.path === '/my-cars' }"
          @click="mobileMenuOpen = false"
        >
          My Cars
        </router-link>
        <router-link 
          to="/mechanics" 
          class="mobile-nav-link"
          :class="{ 'mobile-nav-link-active': $route.path === '/mechanics' }"
          @click="mobileMenuOpen = false"
        >
          Mechanics
        </router-link>
        <div v-if="!isAuthenticated" class="pt-4 border-t border-secondary-200 dark:border-secondary-700 space-y-2">
          <router-link to="/login" class="mobile-nav-link" @click="mobileMenuOpen = false">
            Login
          </router-link>
          <router-link to="/register" class="btn-primary w-full justify-center" @click="mobileMenuOpen = false">
            Register
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../services/api'

export default {
  name: 'Navbar',
  emits: ['toggle-dark-mode'],
  props: {
    isDarkMode: {
      type: Boolean,
      default: false
    }
  },
  setup() {
    const router = useRouter()
    const mobileMenuOpen = ref(false)
    const isAuthenticated = ref(false)
    const user = ref(null)

    const userInitials = computed(() => {
      if (!user.value) return 'U'
      return user.value.name ? user.value.name.charAt(0).toUpperCase() : 'U'
    })

    const toggleMobileMenu = (event) => {
      if (event) {
        event.preventDefault()
        event.stopPropagation()
      }
      console.log('Button clicked! Current state:', mobileMenuOpen.value)
      mobileMenuOpen.value = !mobileMenuOpen.value
      console.log('Mobile menu toggled to:', mobileMenuOpen.value)
    }

    const logout = async () => {
      try {
        await authAPI.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        isAuthenticated.value = false
        user.value = null
        mobileMenuOpen.value = false
        router.push('/')
      }
    }

    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative') && !event.target.closest('.mobile-menu-container')) {
        mobileMenuOpen.value = false
      }
    }

    onMounted(() => {
      // Check if user is authenticated
      const token = localStorage.getItem('token')
      const userData = localStorage.getItem('user')
      
      if (token && userData) {
        isAuthenticated.value = true
        user.value = JSON.parse(userData)
      }

      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      mobileMenuOpen,
      isAuthenticated,
      user,
      userInitials,
      toggleMobileMenu,
      logout
    }
  }
}
</script>

<style scoped>
.nav-link {
  @apply flex items-center px-3 py-2 rounded-lg text-sm font-medium text-secondary-600 dark:text-secondary-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200;
}

.nav-link-active {
  @apply text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20;
}

.mobile-nav-link {
  @apply block px-3 py-2 rounded-lg text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200;
}

.mobile-nav-link-active {
  @apply text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20;
}
</style>
