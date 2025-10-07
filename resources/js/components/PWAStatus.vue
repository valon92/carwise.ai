<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">PWA Status</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Progressive Web App Information</p>
        </div>
      </div>
      
      <div class="flex items-center space-x-2">
        <div 
          class="w-3 h-3 rounded-full"
          :class="{
            'bg-green-500': isPWAReady,
            'bg-yellow-500': isInstallable && !isInstalled,
            'bg-gray-400': !isPWAReady && !isInstallable
          }"
        ></div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ pwaStatusText }}
        </span>
      </div>
    </div>
    
    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      <!-- Installation Status -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Installation</h4>
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': isInstalled,
              'bg-yellow-500': isInstallable,
              'bg-gray-400': !isInstalled && !isInstallable
            }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ isInstalled ? 'App is installed' : isInstallable ? 'Ready to install' : 'Not installable' }}
        </p>
      </div>
      
      <!-- Connection Status -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Connection</h4>
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': isOnline,
              'bg-red-500': !isOnline
            }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ isOnline ? 'Online' : 'Offline' }}
        </p>
      </div>
      
      <!-- Service Worker Status -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Service Worker</h4>
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': swRegistration,
              'bg-red-500': !swRegistration
            }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ swRegistration ? 'Active' : 'Not registered' }}
        </p>
      </div>
    </div>
    
    <!-- Features Status -->
    <div class="mb-6">
      <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">PWA Features</h4>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canInstall,
              'bg-gray-400': !canInstall
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Install</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canShare,
              'bg-gray-400': !canShare
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Share</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canPush,
              'bg-gray-400': !canPush
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Push</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canSync,
              'bg-gray-400': !canSync
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Sync</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canBackgroundSync,
              'bg-gray-400': !canBackgroundSync
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Background Sync</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': canPeriodicSync,
              'bg-gray-400': !canPeriodicSync
            }"
          ></div>
          <span class="text-xs text-gray-600 dark:text-gray-400">Periodic Sync</span>
        </div>
      </div>
    </div>
    
    <!-- Cache Information -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Cache Information</h4>
        <button
          @click="refreshCacheInfo"
          class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
        >
          Refresh
        </button>
      </div>
      
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">Cache Size</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ cacheSize }} items
          </span>
        </div>
        
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ lastCacheUpdate }}
          </span>
        </div>
        
        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-600 dark:text-gray-400">Storage Used</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ storageUsed }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Actions -->
    <div class="flex flex-wrap gap-3">
      <button
        v-if="isInstallable && !isInstalled"
        @click="handleInstall"
        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
      >
        Install App
      </button>
      
      <button
        v-if="hasUpdate"
        @click="handleUpdate"
        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
      >
        Update App
      </button>
      
      <button
        @click="clearCache"
        class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
      >
        Clear Cache
      </button>
      
      <button
        @click="requestNotificationPermission"
        v-if="canPush"
        class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors"
      >
        Enable Notifications
      </button>
    </div>
    
    <!-- Debug Information (Development Only) -->
    <div v-if="isDevelopment" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
      <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Debug Information</h4>
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <pre class="text-xs text-gray-600 dark:text-gray-400 overflow-auto">{{ debugInfo }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePWA } from '../composables/usePWA'

const props = defineProps({
  showDebug: {
    type: Boolean,
    default: false
  }
})

// PWA composable
const {
  isInstalled,
  isInstallable,
  isOnline,
  isStandalone,
  hasUpdate,
  swRegistration,
  canInstall,
  canShare,
  canSync,
  canPush,
  canBackgroundSync,
  canPeriodicSync,
  pwaStatus,
  isPWAReady,
  installPWA,
  updatePWA,
  clearCache,
  getCacheSize,
  requestNotificationPermission
} = usePWA()

// Component state
const cacheSize = ref(0)
const lastCacheUpdate = ref('Never')
const storageUsed = ref('0 MB')
const isDevelopment = ref(process.env.NODE_ENV === 'development')

// Computed properties
const pwaStatusText = computed(() => {
  if (isPWAReady.value) return 'Ready'
  if (isInstallable.value) return 'Installable'
  return 'Browser Mode'
})

const debugInfo = computed(() => {
  return JSON.stringify({
    isInstalled: isInstalled.value,
    isInstallable: isInstallable.value,
    isOnline: isOnline.value,
    isStandalone: isStandalone.value,
    hasUpdate: hasUpdate.value,
    pwaStatus: pwaStatus.value,
    userAgent: navigator.userAgent,
    displayMode: window.matchMedia('(display-mode: standalone)').matches ? 'standalone' : 'browser',
    serviceWorker: !!swRegistration.value
  }, null, 2)
})

// Methods
const handleInstall = async () => {
  try {
    await installPWA()
  } catch (error) {
    console.error('Installation failed:', error)
  }
}

const handleUpdate = async () => {
  try {
    await updatePWA()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const refreshCacheInfo = async () => {
  try {
    cacheSize.value = await getCacheSize()
    lastCacheUpdate.value = new Date().toLocaleString()
    
    // Get storage usage
    if ('storage' in navigator && 'estimate' in navigator.storage) {
      const estimate = await navigator.storage.estimate()
      storageUsed.value = formatBytes(estimate.usage || 0)
    }
  } catch (error) {
    console.error('Failed to refresh cache info:', error)
  }
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Lifecycle
onMounted(() => {
  refreshCacheInfo()
})
</script>

<style scoped>
/* Custom styles for better visual hierarchy */
.bg-gray-50 {
  background-color: #f9fafb;
}

.dark .bg-gray-50 {
  background-color: #374151;
}

.bg-gray-700 {
  background-color: #374151;
}

.dark .bg-gray-700 {
  background-color: #1f2937;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .grid-cols-3 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* Animation for status indicators */
.w-2.h-2.rounded-full {
  transition: background-color 0.3s ease;
}

/* Hover effects */
button {
  transition: all 0.2s ease;
}

button:hover {
  transform: translateY(-1px);
}

/* Debug info styling */
pre {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  line-height: 1.4;
  max-height: 200px;
}
</style>

