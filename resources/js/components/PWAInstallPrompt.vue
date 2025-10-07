<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 transform translate-y-full"
    enter-to-class="opacity-100 transform translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 transform translate-y-0"
    leave-to-class="opacity-0 transform translate-y-full"
  >
    <div
      v-if="showInstallPrompt"
      class="fixed bottom-4 left-4 right-4 z-50 max-w-md mx-auto"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-white font-semibold text-lg">Install CarWise.ai</h3>
                <p class="text-primary-100 text-sm">Get the full app experience</p>
              </div>
            </div>
            <button
              @click="dismissInstallPrompt"
              class="text-white hover:text-primary-200 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          <div class="space-y-4">
            <!-- Benefits -->
            <div class="space-y-3">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Works offline with cached data</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 19.132l13.132-13.132a2.5 2.5 0 00-3.536-3.536L1.332 15.596 4.868 19.132z"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Faster loading and better performance</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 19.132l13.132-13.132a2.5 2.5 0 00-3.536-3.536L1.332 15.596 4.868 19.132z"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Push notifications for updates</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Native app-like experience</span>
              </div>
            </div>
            
            <!-- Install Button -->
            <button
              @click="handleInstall"
              :disabled="isInstalling"
              class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2"
            >
              <svg v-if="!isInstalling" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isInstalling ? 'Installing...' : 'Install App' }}</span>
            </button>
            
            <!-- Dismiss Options -->
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
              <button
                @click="dismissInstallPrompt"
                class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
              >
                Not now
              </button>
              <button
                @click="dismissPermanently"
                class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
              >
                Don't ask again
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePWA } from '../composables/usePWA'

const props = defineProps({
  autoShow: {
    type: Boolean,
    default: true
  },
  delay: {
    type: Number,
    default: 3000
  },
  maxDismissals: {
    type: Number,
    default: 3
  }
})

const emit = defineEmits(['install', 'dismiss', 'dismiss-permanently'])

// PWA composable
const {
  isInstallable,
  isInstalled,
  installPWA,
  pwaStatus
} = usePWA()

// Component state
const showInstallPrompt = ref(false)
const isInstalling = ref(false)
const dismissCount = ref(0)
const isDismissedPermanently = ref(false)

// Computed properties
const shouldShowPrompt = computed(() => {
  return (
    props.autoShow &&
    !isDismissedPermanently.value &&
    isInstallable.value &&
    !isInstalled.value &&
    dismissCount.value < props.maxDismissals &&
    !localStorage.getItem('pwa-install-dismissed-permanently')
  )
})

// Methods
const handleInstall = async () => {
  isInstalling.value = true
  
  try {
    const success = await installPWA()
    if (success) {
      showInstallPrompt.value = false
      emit('install')
      
      // Show success message
      showInstallSuccess()
    }
  } catch (error) {
    console.error('Installation failed:', error)
    showInstallError()
  } finally {
    isInstalling.value = false
  }
}

const dismissInstallPrompt = () => {
  showInstallPrompt.value = false
  dismissCount.value++
  emit('dismiss')
  
  // Store dismissal count
  localStorage.setItem('pwa-install-dismissals', dismissCount.value.toString())
}

const dismissPermanently = () => {
  showInstallPrompt.value = false
  isDismissedPermanently.value = true
  localStorage.setItem('pwa-install-dismissed-permanently', 'true')
  emit('dismiss-permanently')
}

const showInstallSuccess = () => {
  // Show success notification
  if ('Notification' in window && Notification.permission === 'granted') {
    new Notification('CarWise.ai Installed!', {
      body: 'The app has been installed successfully. You can now access it from your home screen.',
      icon: '/icons/icon-192x192.png',
      tag: 'pwa-install-success'
    })
  }
}

const showInstallError = () => {
  // Show error message
  console.error('PWA installation failed')
}

const checkDismissalStatus = () => {
  const dismissals = localStorage.getItem('pwa-install-dismissals')
  const dismissedPermanently = localStorage.getItem('pwa-install-dismissed-permanently')
  
  if (dismissals) {
    dismissCount.value = parseInt(dismissals, 10)
  }
  
  if (dismissedPermanently) {
    isDismissedPermanently.value = true
  }
}

const showPromptWithDelay = () => {
  if (shouldShowPrompt.value) {
    setTimeout(() => {
      if (shouldShowPrompt.value) {
        showInstallPrompt.value = true
      }
    }, props.delay)
  }
}

// Watchers
watch(isInstallable, (newValue) => {
  if (newValue && shouldShowPrompt.value) {
    showPromptWithDelay()
  }
})

watch(isInstalled, (newValue) => {
  if (newValue) {
    showInstallPrompt.value = false
  }
})

// Lifecycle
onMounted(() => {
  checkDismissalStatus()
  
  if (shouldShowPrompt.value) {
    showPromptWithDelay()
  }
})
</script>

<style scoped>
/* Custom animations */
@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slideDown {
  from {
    transform: translateY(0);
    opacity: 1;
  }
  to {
    transform: translateY(100%);
    opacity: 0;
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .fixed {
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
  }
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .bg-white {
    background-color: #1f2937;
  }
  
  .border-gray-200 {
    border-color: #374151;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .bg-primary-600 {
    background-color: #000;
  }
  
  .text-white {
    color: #fff;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .transition-all {
    transition: none;
  }
  
  .animate-spin {
    animation: none;
  }
}
</style>

