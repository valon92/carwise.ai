<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 transform -translate-y-full"
    enter-to-class="opacity-100 transform translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 transform translate-y-0"
    leave-to-class="opacity-0 transform -translate-y-full"
  >
    <div
      v-if="showUpdateNotification"
      class="fixed top-4 left-4 right-4 z-50 max-w-md mx-auto"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-white font-semibold text-lg">Update Available</h3>
                <p class="text-green-100 text-sm">New version ready to install</p>
              </div>
            </div>
            <button
              @click="dismissUpdate"
              class="text-white hover:text-green-200 transition-colors"
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
            <!-- Update Info -->
            <div class="space-y-3">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Bug fixes and performance improvements</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">New features and enhanced security</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <span class="text-gray-700 dark:text-gray-300 text-sm">Improved offline functionality</span>
              </div>
            </div>
            
            <!-- Version Info -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Current Version</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ currentVersion }}</p>
                </div>
                <div class="text-center">
                  <svg class="w-6 h-6 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                  </svg>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">New Version</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ newVersion }}</p>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-3">
              <button
                @click="handleUpdate"
                :disabled="isUpdating"
                class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2"
              >
                <svg v-if="!isUpdating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isUpdating ? 'Updating...' : 'Update Now' }}</span>
              </button>
              
              <button
                @click="dismissUpdate"
                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                Later
              </button>
            </div>
            
            <!-- Auto-update info -->
            <div class="text-center">
              <p class="text-xs text-gray-500 dark:text-gray-400">
                The app will automatically update in the background
              </p>
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
    default: 1000
  },
  autoUpdate: {
    type: Boolean,
    default: false
  },
  autoUpdateDelay: {
    type: Number,
    default: 10000
  }
})

const emit = defineEmits(['update', 'dismiss', 'update-complete'])

// PWA composable
const {
  hasUpdate,
  isUpdateAvailable,
  updatePWA,
  swRegistration
} = usePWA()

// Component state
const showUpdateNotification = ref(false)
const isUpdating = ref(false)
const currentVersion = ref('1.0.0')
const newVersion = ref('1.0.1')
const autoUpdateTimer = ref(null)

// Computed properties
const shouldShowUpdate = computed(() => {
  return props.autoShow && hasUpdate.value && isUpdateAvailable.value
})

// Methods
const handleUpdate = async () => {
  isUpdating.value = true
  
  try {
    const success = await updatePWA()
    if (success) {
      showUpdateNotification.value = false
      emit('update')
      
      // Show success message
      showUpdateSuccess()
    }
  } catch (error) {
    console.error('Update failed:', error)
    showUpdateError()
  } finally {
    isUpdating.value = false
  }
}

const dismissUpdate = () => {
  showUpdateNotification.value = false
  emit('dismiss')
  
  // Clear auto-update timer
  if (autoUpdateTimer.value) {
    clearTimeout(autoUpdateTimer.value)
    autoUpdateTimer.value = null
  }
}

const showUpdateSuccess = () => {
  // Show success notification
  if ('Notification' in window && Notification.permission === 'granted') {
    new Notification('CarWise.ai Updated!', {
      body: 'The app has been updated successfully. Enjoy the new features!',
      icon: '/icons/icon-192x192.png',
      tag: 'pwa-update-success'
    })
  }
  
  emit('update-complete')
}

const showUpdateError = () => {
  // Show error message
  console.error('PWA update failed')
}

const startAutoUpdate = () => {
  if (props.autoUpdate && shouldShowUpdate.value) {
    autoUpdateTimer.value = setTimeout(() => {
      if (shouldShowUpdate.value) {
        handleUpdate()
      }
    }, props.autoUpdateDelay)
  }
}

const getVersionInfo = () => {
  // Get version from service worker or manifest
  if (swRegistration.value) {
    // Try to get version from service worker
    const sw = swRegistration.value.active || swRegistration.value.waiting
    if (sw) {
      // This would need to be implemented in the service worker
      // For now, we'll use placeholder values
      currentVersion.value = '1.0.0'
      newVersion.value = '1.0.1'
    }
  }
}

const showUpdateWithDelay = () => {
  if (shouldShowUpdate.value) {
    setTimeout(() => {
      if (shouldShowUpdate.value) {
        showUpdateNotification.value = true
        getVersionInfo()
        
        // Start auto-update if enabled
        if (props.autoUpdate) {
          startAutoUpdate()
        }
      }
    }, props.delay)
  }
}

// Watchers
watch(hasUpdate, (newValue) => {
  if (newValue && shouldShowUpdate.value) {
    showUpdateWithDelay()
  }
})

watch(isUpdateAvailable, (newValue) => {
  if (newValue && shouldShowUpdate.value) {
    showUpdateWithDelay()
  }
})

// Lifecycle
onMounted(() => {
  if (shouldShowUpdate.value) {
    showUpdateWithDelay()
  }
})
</script>

<style scoped>
/* Custom animations */
@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(0);
    opacity: 1;
  }
  to {
    transform: translateY(-100%);
    opacity: 0;
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .fixed {
    top: 1rem;
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
  .bg-green-600 {
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

