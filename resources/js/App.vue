<template>
  <div
    id="app"
    class="flex min-h-screen flex-col transition-colors duration-300"
    :class="isAuthShell ? 'bg-white' : 'bg-gradient-to-br from-secondary-50 to-primary-50'"
  >
    <Navbar v-if="!isAuthShell" />
    <main class="relative flex-1">
      <router-view />
    </main>
    <Footer v-if="!isAuthShell" />
    <BackToTop v-if="!isAuthShell" />

    <NotificationSystem />

    <div
      v-if="isLoading"
      class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm"
    >
      <div class="flex flex-col items-center space-y-4">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600" />
        <p class="font-medium text-secondary-600">Loading...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, defineAsyncComponent } from 'vue'
import { useRoute } from 'vue-router'
import { initPerformanceOptimizations } from './utils/performance'
import BackToTop from './components/BackToTop.vue'

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
    NotificationSystem,
    BackToTop
  },
  setup() {
    const route = useRoute()
    const isLoading = ref(false)
    const isAuthShell = computed(() => ['Login', 'Register'].includes(route.name))

    onMounted(() => {
      localStorage.removeItem('darkMode')
      document.documentElement.classList.remove('dark')
    })

    return {
      isLoading,
      isAuthShell,
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
