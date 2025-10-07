<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Offline Data Manager</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Manage your offline data and sync status</p>
        </div>
      </div>
      
      <div class="flex items-center space-x-2">
        <div 
          class="w-3 h-3 rounded-full"
          :class="{
            'bg-green-500': isOnline,
            'bg-red-500': isOffline
          }"
        ></div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ isOnline ? 'Online' : 'Offline' }}
        </span>
      </div>
    </div>
    
    <!-- Storage Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <!-- Offline Data Size -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Offline Data</h4>
          <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ formatBytes(offlineDataSize) }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ Object.keys(offlineData).length }} items
        </p>
      </div>
      
      <!-- Offline Actions -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Queued Actions</h4>
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': !hasOfflineActions,
              'bg-orange-500': hasOfflineActions
            }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ offlineActions.length }} actions
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ hasOfflineActions ? 'Pending sync' : 'All synced' }}
        </p>
      </div>
      
      <!-- Sync Queue -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white">Sync Queue</h4>
          <div 
            class="w-2 h-2 rounded-full"
            :class="{
              'bg-green-500': !hasSyncQueue,
              'bg-purple-500': hasSyncQueue
            }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ syncQueue.length }} items
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ hasSyncQueue ? 'Pending sync' : 'All synced' }}
        </p>
      </div>
    </div>
    
    <!-- Sync Status -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Sync Status</h4>
        <div class="flex items-center space-x-2">
          <div v-if="isSyncing" class="flex items-center space-x-2">
            <div class="w-4 h-4 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-primary-600 dark:text-primary-400">Syncing...</span>
          </div>
          <span v-else class="text-sm text-gray-500 dark:text-gray-400">
            {{ lastSyncTime ? `Last sync: ${formatTime(lastSyncTime)}` : 'Never synced' }}
          </span>
        </div>
      </div>
      
      <!-- Sync Progress -->
      <div v-if="isSyncing" class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
        <div 
          class="bg-primary-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: `${syncProgress}%` }"
        ></div>
      </div>
      
      <!-- Sync Actions -->
      <div class="flex space-x-3">
        <button
          @click="syncOfflineActions"
          :disabled="isSyncing || isOffline"
          class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 disabled:bg-gray-400 transition-colors"
        >
          {{ isSyncing ? 'Syncing...' : 'Sync Now' }}
        </button>
        
        <button
          @click="clearOfflineData"
          class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
        >
          Clear Data
        </button>
        
        <button
          @click="exportOfflineData"
          class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
        >
          Export Data
        </button>
      </div>
    </div>
    
    <!-- Offline Data Details -->
    <div class="mb-6">
      <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Offline Data Details</h4>
      <div class="space-y-2">
        <div
          v-for="(data, key) in offlineData"
          :key="key"
          class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
        >
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ key }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatTime(data.timestamp) }} • {{ formatBytes(JSON.stringify(data.data).length) }}
              </p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <button
              @click="viewOfflineData(key)"
              class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm"
            >
              View
            </button>
            <button
              @click="removeOfflineData(key)"
              class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm"
            >
              Remove
            </button>
          </div>
        </div>
        
        <div v-if="Object.keys(offlineData).length === 0" class="text-center py-8">
          <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
            </svg>
          </div>
          <p class="text-gray-500 dark:text-gray-400 text-sm">No offline data stored</p>
        </div>
      </div>
    </div>
    
    <!-- Offline Actions Details -->
    <div v-if="hasOfflineActions">
      <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Queued Actions</h4>
      <div class="space-y-2">
        <div
          v-for="action in offlineActions"
          :key="action.id"
          class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800"
        >
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ getActionTitle(action.type) }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatTime(action.timestamp) }}
                <span v-if="action.retryCount > 0" class="text-orange-600 dark:text-orange-400">
                  • Retry {{ action.retryCount }}/{{ action.maxRetries }}
                </span>
              </p>
            </div>
          </div>
          
          <button
            @click="removeOfflineAction(action.id)"
            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm"
          >
            Remove
          </button>
        </div>
      </div>
    </div>
    
    <!-- Data Viewer Modal -->
    <div
      v-if="showDataViewer"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click="showDataViewer = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-hidden"
        @click.stop
      >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Offline Data: {{ selectedDataKey }}
            </h3>
            <button
              @click="showDataViewer = false"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-4 overflow-auto max-h-80">
          <pre class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ selectedDataContent }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useOffline } from '../composables/useOffline'

// Offline composable
const {
  isOnline,
  isOffline,
  offlineData,
  offlineActions,
  hasOfflineActions,
  syncQueue,
  hasSyncQueue,
  lastSyncTime,
  isSyncing,
  syncProgress,
  storeOfflineData,
  getOfflineData,
  removeOfflineData,
  clearOfflineData,
  removeOfflineAction,
  clearOfflineActions,
  syncOfflineActions
} = useOffline()

// Component state
const showDataViewer = ref(false)
const selectedDataKey = ref('')
const selectedDataContent = ref('')

// Computed properties
const offlineDataSize = computed(() => {
  try {
    return JSON.stringify(offlineData.value).length
  } catch {
    return 0
  }
})

// Methods
const formatBytes = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatTime = (timestamp) => {
  if (!timestamp) return 'Never'
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

const viewOfflineData = (key) => {
  const data = getOfflineData(key)
  if (data) {
    selectedDataKey.value = key
    selectedDataContent.value = JSON.stringify(data, null, 2)
    showDataViewer.value = true
  }
}

const exportOfflineData = () => {
  try {
    const dataToExport = {
      offlineData: offlineData.value,
      offlineActions: offlineActions.value,
      syncQueue: syncQueue.value,
      exportTime: Date.now(),
      version: '1.0.0'
    }
    
    const blob = new Blob([JSON.stringify(dataToExport, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `carwise-offline-data-${new Date().toISOString().split('T')[0]}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
    
    console.log('[Offline] Data exported successfully')
  } catch (error) {
    console.error('[Offline] Failed to export data:', error)
  }
}
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
    grid-template-columns: repeat(1, minmax(0, 1fr));
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

/* Modal animations */
.fixed.inset-0 {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Pre-formatted text styling */
pre {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  line-height: 1.4;
  max-height: 200px;
  overflow: auto;
}
</style>

