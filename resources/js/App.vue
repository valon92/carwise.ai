<template>
  <div id="app" :class="{ 'dark': isDarkMode }" class="min-h-screen bg-gradient-to-br from-secondary-50 to-primary-50 dark:from-secondary-900 dark:to-secondary-800 transition-colors duration-300 flex flex-col">
    <Navbar @toggle-dark-mode="toggleDarkMode" :is-dark-mode="isDarkMode" />
    <main class="relative flex-1">
      <router-view />
    </main>
    <Footer />
    
    <!-- Notification System -->
    <NotificationSystem />
    
    <!-- Modern loading overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
      <div class="flex flex-col items-center space-y-4">
        <div class="w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
        <p class="text-secondary-600 dark:text-secondary-400 font-medium">Loading...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, defineAsyncComponent } from 'vue'
import { initPerformanceOptimizations } from './utils/performance'

// Lazy load components for better performance
const Navbar = defineAsyncComponent({
  loader: () => import('./components/Navbar.vue'),
  loadingComponent: {
    template: '<div class="h-16 bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700 animate-pulse"></div>'
  },
  errorComponent: {
    template: '<div class="h-16 bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-400">Failed to load navigation</div>'
  },
  delay: 200,
  timeout: 3000
})

const Footer = defineAsyncComponent({
  loader: () => import('./components/Footer.vue'),
  loadingComponent: {
    template: '<div class="h-32 bg-secondary-100 dark:bg-secondary-800 animate-pulse"></div>'
  },
  errorComponent: {
    template: '<div class="h-32 bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-400">Failed to load footer</div>'
  },
  delay: 200,
  timeout: 3000
})

const NotificationSystem = defineAsyncComponent({
  loader: () => import('./components/NotificationSystem.vue'),
  loadingComponent: {
    template: '<div></div>' // Empty loading state for notifications
  },
  errorComponent: {
    template: '<div></div>' // Silent fail for notifications
  },
  delay: 100,
  timeout: 2000
})

export default {
  name: 'App',
  components: {
    Navbar,
    Footer,
    NotificationSystem
  },
  setup() {
    const isDarkMode = ref(false)
    const isLoading = ref(false)

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value
      localStorage.setItem('darkMode', isDarkMode.value.toString())
    }

    onMounted(() => {
      // Check for saved dark mode preference
      const savedDarkMode = localStorage.getItem('darkMode')
      if (savedDarkMode !== null) {
        isDarkMode.value = savedDarkMode === 'true'
      } else {
        // Check system preference
        isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
      }

      // Initialize performance optimizations
      initPerformanceOptimizations()
    })

    return {
      isDarkMode,
      isLoading,
      toggleDarkMode
    }
  }
}
</script>

<style>
/* Global styles for smooth transitions */
* {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

/* Custom focus styles */
.focus-visible:focus {
  outline: 2px solid theme('colors.primary.500');
  outline-offset: 2px;
}

/* Smooth page transitions */
.router-enter-active,
.router-leave-active {
  transition: all 0.3s ease;
}

.router-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.router-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
