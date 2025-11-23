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
      v-if="isOffline"
      class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg"
    >
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-sm">You're Offline</h3>
              <p class="text-red-100 text-xs">
                {{ hasOfflineActions ? `${offlineActions.length} actions queued` : 'Working offline' }}
              </p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <!-- Sync Progress -->
            <div v-if="isSyncing" class="flex items-center space-x-2">
              <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span class="text-xs">{{ syncProgress }}%</span>
            </div>
            
            <!-- Offline Actions Count -->
            <div v-if="hasOfflineActions" class="bg-white bg-opacity-20 rounded-full px-2 py-1">
              <span class="text-xs font-medium">{{ offlineActions.length }}</span>
            </div>
            
            <!-- Retry Button -->
            <button
              @click="retryConnection"
              class="text-white hover:text-red-200 transition-colors"
              title="Retry Connection"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
  
  <!-- Offline Actions Panel -->
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 transform translate-y-full"
    enter-to-class="opacity-100 transform translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 transform translate-y-0"
    leave-to-class="opacity-0 transform translate-y-full"
  >
    <div
      v-if="isOffline && hasOfflineActions && showOfflinePanel"
      class="fixed bottom-4 left-4 right-4 z-40 max-w-md mx-auto"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-white font-semibold text-sm">Offline Actions</h3>
                <p class="text-orange-100 text-xs">{{ offlineActions.length }} actions queued</p>
              </div>
            </div>
            <button
              @click="showOfflinePanel = false"
              class="text-white hover:text-orange-200 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Actions List -->
        <div class="max-h-48 overflow-y-auto">
          <div
            v-for="action in offlineActions.slice(0, 5)"
            :key="action.id"
            class="flex items-center justify-between p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActionIcon(action.type)"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ getActionTitle(action.type) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatTime(action.timestamp) }}</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <div v-if="action.retryCount > 0" class="text-xs text-orange-600 dark:text-orange-400">
                Retry {{ action.retryCount }}/{{ action.maxRetries }}
              </div>
              <button
                @click="removeOfflineAction(action.id)"
                class="text-gray-400 hover:text-red-500 transition-colors"
                title="Remove Action"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Show More Button -->
          <div v-if="offlineActions.length > 5" class="p-3 text-center">
            <button
              @click="showAllActions = !showAllActions"
              class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
            >
              {{ showAllActions ? 'Show Less' : `Show ${offlineActions.length - 5} More` }}
            </button>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="p-3 bg-gray-50 dark:bg-gray-700">
          <div class="flex space-x-2">
            <button
              @click="clearOfflineActions"
              class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
            >
              Clear All
            </button>
            <button
              @click="retryConnection"
              class="flex-1 px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
            >
              Retry Sync
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useOffline } from '../composables/useOffline'

const props = defineProps({
  showPanel: {
    type: Boolean,
    default: true
  }
})

// Offline composable
const {
  isOffline,
  offlineActions,
  hasOfflineActions,
  isSyncing,
  syncProgress,
  removeOfflineAction,
  clearOfflineActions,
  syncOfflineActions
} = useOffline()

// Component state
const showOfflinePanel = ref(false)
const showAllActions = ref(false)

// Computed properties
const shouldShowPanel = computed(() => {
  return props.showPanel && isOffline.value && hasOfflineActions.value
})

// Methods
const retryConnection = () => {
  if (navigator.onLine) {
    // Force sync
    syncOfflineActions()
  } else {
    // Test connection
    fetch('/', { method: 'HEAD', cache: 'no-cache' })
      .then(() => {
        // Connection restored
        window.dispatchEvent(new Event('online'))
      })
      .catch(() => {
        // Still offline
        console.log('Still offline')
      })
  }
}

const getActionIcon = (type) => {
  const icons = {
    'add-to-cart': 'M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01',
    'remove-from-cart': 'M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01',
    'add-to-wishlist': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'submit-diagnosis': 'M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0-8h10a2 2 0 012 2v6a2 2 0 01-2 2H9m0-8v8',
    'update-preferences': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  }
  return icons[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const getActionTitle = (type) => {
  const titles = {
    'add-to-cart': 'Add to Cart',
    'remove-from-cart': 'Remove from Cart',
    'add-to-wishlist': 'Add to Wishlist',
    'submit-diagnosis': 'Submit Diagnosis',
    'update-preferences': 'Update Preferences'
  }
  return titles[type] || 'Unknown Action'
}

const formatTime = (timestamp) => {
  const now = Date.now()
  const diff = now - timestamp
  
  if (diff < 60000) { // Less than 1 minute
    return 'Just now'
  } else if (diff < 3600000) { // Less than 1 hour
    const minutes = Math.floor(diff / 60000)
    return `${minutes}m ago`
  } else if (diff < 86400000) { // Less than 1 day
    const hours = Math.floor(diff / 3600000)
    return `${hours}h ago`
  } else {
    const days = Math.floor(diff / 86400000)
    return `${days}d ago`
  }
}

// Lifecycle
onMounted(() => {
  // Show panel after a delay when offline
  if (isOffline.value && hasOfflineActions.value) {
    setTimeout(() => {
      showOfflinePanel.value = true
    }, 3000)
  }
})

// Watch for offline actions
watch(hasOfflineActions, (newValue) => {
  if (newValue && isOffline.value) {
    // Show panel when actions are added
    setTimeout(() => {
      showOfflinePanel.value = true
    }, 1000)
  }
})
</script>

<style scoped>
/* Custom animations */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .fixed.bottom-4 {
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
  .bg-red-500 {
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
  
  .animate-pulse {
    animation: none;
  }
}
</style>
