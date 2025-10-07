import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

export function useOffline() {
  // Offline State
  const isOnline = ref(navigator.onLine)
  const isOffline = computed(() => !isOnline.value)
  const offlineActions = ref([])
  const offlineData = ref({})
  const syncQueue = ref([])
  const lastSyncTime = ref(null)
  const isSyncing = ref(false)
  const syncProgress = ref(0)
  
  // Offline Storage
  const OFFLINE_STORAGE_KEY = 'carwise-offline-data'
  const OFFLINE_ACTIONS_KEY = 'carwise-offline-actions'
  const SYNC_QUEUE_KEY = 'carwise-sync-queue'
  
  // Computed Properties
  const hasOfflineActions = computed(() => offlineActions.value.length > 0)
  const hasSyncQueue = computed(() => syncQueue.value.length > 0)
  const offlineDataSize = computed(() => {
    try {
      return JSON.stringify(offlineData.value).length
    } catch {
      return 0
    }
  })
  
  // Connection Status Monitoring
  const updateConnectionStatus = () => {
    isOnline.value = navigator.onLine
    console.log('[Offline] Connection status:', isOnline.value ? 'Online' : 'Offline')
    
    if (isOnline.value) {
      // Back online - trigger sync
      handleBackOnline()
    } else {
      // Gone offline - show offline indicator
      handleGoneOffline()
    }
  }
  
  // Offline Data Management
  const storeOfflineData = (key, data) => {
    try {
      offlineData.value[key] = {
        data,
        timestamp: Date.now(),
        version: 1
      }
      
      // Save to localStorage
      localStorage.setItem(OFFLINE_STORAGE_KEY, JSON.stringify(offlineData.value))
      console.log('[Offline] Data stored:', key, data)
      return true
    } catch (error) {
      console.error('[Offline] Failed to store data:', error)
      return false
    }
  }
  
  const getOfflineData = (key) => {
    try {
      const data = offlineData.value[key]
      if (data) {
        return data.data
      }
      return null
    } catch (error) {
      console.error('[Offline] Failed to get data:', error)
      return null
    }
  }
  
  const removeOfflineData = (key) => {
    try {
      delete offlineData.value[key]
      localStorage.setItem(OFFLINE_STORAGE_KEY, JSON.stringify(offlineData.value))
      console.log('[Offline] Data removed:', key)
      return true
    } catch (error) {
      console.error('[Offline] Failed to remove data:', error)
      return false
    }
  }
  
  const clearOfflineData = () => {
    try {
      offlineData.value = {}
      localStorage.removeItem(OFFLINE_STORAGE_KEY)
      console.log('[Offline] All offline data cleared')
      return true
    } catch (error) {
      console.error('[Offline] Failed to clear data:', error)
      return false
    }
  }
  
  // Offline Actions Management
  const addOfflineAction = (action) => {
    try {
      const offlineAction = {
        id: Date.now() + Math.random(),
        type: action.type,
        data: action.data,
        endpoint: action.endpoint,
        method: action.method || 'POST',
        headers: action.headers || {},
        timestamp: Date.now(),
        retryCount: 0,
        maxRetries: 3
      }
      
      offlineActions.value.push(offlineAction)
      localStorage.setItem(OFFLINE_ACTIONS_KEY, JSON.stringify(offlineActions.value))
      console.log('[Offline] Action queued:', offlineAction.type)
      return offlineAction.id
    } catch (error) {
      console.error('[Offline] Failed to add action:', error)
      return null
    }
  }
  
  const removeOfflineAction = (actionId) => {
    try {
      const index = offlineActions.value.findIndex(action => action.id === actionId)
      if (index !== -1) {
        offlineActions.value.splice(index, 1)
        localStorage.setItem(OFFLINE_ACTIONS_KEY, JSON.stringify(offlineActions.value))
        console.log('[Offline] Action removed:', actionId)
        return true
      }
      return false
    } catch (error) {
      console.error('[Offline] Failed to remove action:', error)
      return false
    }
  }
  
  const clearOfflineActions = () => {
    try {
      offlineActions.value = []
      localStorage.removeItem(OFFLINE_ACTIONS_KEY)
      console.log('[Offline] All offline actions cleared')
      return true
    } catch (error) {
      console.error('[Offline] Failed to clear actions:', error)
      return false
    }
  }
  
  // Sync Queue Management
  const addToSyncQueue = (item) => {
    try {
      const syncItem = {
        id: Date.now() + Math.random(),
        type: item.type,
        data: item.data,
        priority: item.priority || 'normal',
        timestamp: Date.now(),
        retryCount: 0,
        maxRetries: 3
      }
      
      syncQueue.value.push(syncItem)
      localStorage.setItem(SYNC_QUEUE_KEY, JSON.stringify(syncQueue.value))
      console.log('[Offline] Item added to sync queue:', syncItem.type)
      return syncItem.id
    } catch (error) {
      console.error('[Offline] Failed to add to sync queue:', error)
      return null
    }
  }
  
  const removeFromSyncQueue = (itemId) => {
    try {
      const index = syncQueue.value.findIndex(item => item.id === itemId)
      if (index !== -1) {
        syncQueue.value.splice(index, 1)
        localStorage.setItem(SYNC_QUEUE_KEY, JSON.stringify(syncQueue.value))
        console.log('[Offline] Item removed from sync queue:', itemId)
        return true
      }
      return false
    } catch (error) {
      console.error('[Offline] Failed to remove from sync queue:', error)
      return false
    }
  }
  
  // Background Sync
  const syncOfflineActions = async () => {
    if (!isOnline.value || isSyncing.value) {
      return
    }
    
    isSyncing.value = true
    syncProgress.value = 0
    
    try {
      const actions = [...offlineActions.value]
      const totalActions = actions.length
      
      for (let i = 0; i < actions.length; i++) {
        const action = actions[i]
        syncProgress.value = Math.round((i / totalActions) * 100)
        
        try {
          const response = await fetch(action.endpoint, {
            method: action.method,
            headers: {
              'Content-Type': 'application/json',
              ...action.headers
            },
            body: JSON.stringify(action.data)
          })
          
          if (response.ok) {
            // Success - remove from offline actions
            removeOfflineAction(action.id)
            console.log('[Offline] Action synced successfully:', action.type)
          } else {
            // Failed - increment retry count
            action.retryCount++
            if (action.retryCount >= action.maxRetries) {
              // Max retries reached - remove action
              removeOfflineAction(action.id)
              console.warn('[Offline] Action failed after max retries:', action.type)
            }
          }
        } catch (error) {
          console.error('[Offline] Failed to sync action:', action.type, error)
          action.retryCount++
          if (action.retryCount >= action.maxRetries) {
            removeOfflineAction(action.id)
          }
        }
      }
      
      syncProgress.value = 100
      lastSyncTime.value = Date.now()
      console.log('[Offline] Sync completed')
      
    } catch (error) {
      console.error('[Offline] Sync failed:', error)
    } finally {
      isSyncing.value = false
      syncProgress.value = 0
    }
  }
  
  const syncQueueItems = async () => {
    if (!isOnline.value || isSyncing.value) {
      return
    }
    
    isSyncing.value = true
    
    try {
      const items = [...syncQueue.value]
      
      for (const item of items) {
        try {
          // Process sync item based on type
          await processSyncItem(item)
          removeFromSyncQueue(item.id)
        } catch (error) {
          console.error('[Offline] Failed to sync item:', item.type, error)
          item.retryCount++
          if (item.retryCount >= item.maxRetries) {
            removeFromSyncQueue(item.id)
          }
        }
      }
      
      lastSyncTime.value = Date.now()
      console.log('[Offline] Queue sync completed')
      
    } catch (error) {
      console.error('[Offline] Queue sync failed:', error)
    } finally {
      isSyncing.value = false
    }
  }
  
  const processSyncItem = async (item) => {
    switch (item.type) {
      case 'cart':
        await syncCartData(item.data)
        break
      case 'diagnosis':
        await syncDiagnosisData(item.data)
        break
      case 'wishlist':
        await syncWishlistData(item.data)
        break
      case 'preferences':
        await syncPreferencesData(item.data)
        break
      default:
        console.warn('[Offline] Unknown sync item type:', item.type)
    }
  }
  
  // Specific Sync Functions
  const syncCartData = async (cartData) => {
    try {
      const response = await fetch('/api/cart/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(cartData)
      })
      
      if (!response.ok) {
        throw new Error('Cart sync failed')
      }
      
      console.log('[Offline] Cart synced successfully')
    } catch (error) {
      console.error('[Offline] Cart sync failed:', error)
      throw error
    }
  }
  
  const syncDiagnosisData = async (diagnosisData) => {
    try {
      const response = await fetch('/api/diagnosis', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(diagnosisData)
      })
      
      if (!response.ok) {
        throw new Error('Diagnosis sync failed')
      }
      
      console.log('[Offline] Diagnosis synced successfully')
    } catch (error) {
      console.error('[Offline] Diagnosis sync failed:', error)
      throw error
    }
  }
  
  const syncWishlistData = async (wishlistData) => {
    try {
      const response = await fetch('/api/wishlist/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(wishlistData)
      })
      
      if (!response.ok) {
        throw new Error('Wishlist sync failed')
      }
      
      console.log('[Offline] Wishlist synced successfully')
    } catch (error) {
      console.error('[Offline] Wishlist sync failed:', error)
      throw error
    }
  }
  
  const syncPreferencesData = async (preferencesData) => {
    try {
      const response = await fetch('/api/preferences/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(preferencesData)
      })
      
      if (!response.ok) {
        throw new Error('Preferences sync failed')
      }
      
      console.log('[Offline] Preferences synced successfully')
    } catch (error) {
      console.error('[Offline] Preferences sync failed:', error)
      throw error
    }
  }
  
  // Event Handlers
  const handleBackOnline = () => {
    console.log('[Offline] Back online - starting sync')
    
    // Show online notification
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('CarWise.ai', {
        body: 'You are back online. Syncing your data...',
        icon: '/icons/icon-192x192.png',
        tag: 'offline-sync'
      })
    }
    
    // Start syncing
    setTimeout(() => {
      syncOfflineActions()
      syncQueueItems()
    }, 1000)
  }
  
  const handleGoneOffline = () => {
    console.log('[Offline] Gone offline')
    
    // Show offline notification
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('CarWise.ai', {
        body: 'You are offline. Your actions will be synced when you reconnect.',
        icon: '/icons/icon-192x192.png',
        tag: 'offline-mode'
      })
    }
  }
  
  // Offline Actions for Common Operations
  const addToCartOffline = (item) => {
    if (isOffline.value) {
      addOfflineAction({
        type: 'add-to-cart',
        endpoint: '/api/cart/add',
        data: item
      })
      return true
    }
    return false
  }
  
  const removeFromCartOffline = (itemId) => {
    if (isOffline.value) {
      addOfflineAction({
        type: 'remove-from-cart',
        endpoint: '/api/cart/remove',
        data: { itemId }
      })
      return true
    }
    return false
  }
  
  const addToWishlistOffline = (item) => {
    if (isOffline.value) {
      addOfflineAction({
        type: 'add-to-wishlist',
        endpoint: '/api/wishlist/add',
        data: item
      })
      return true
    }
    return false
  }
  
  const submitDiagnosisOffline = (diagnosisData) => {
    if (isOffline.value) {
      addOfflineAction({
        type: 'submit-diagnosis',
        endpoint: '/api/diagnosis',
        data: diagnosisData
      })
      return true
    }
    return false
  }
  
  const updatePreferencesOffline = (preferences) => {
    if (isOffline.value) {
      addOfflineAction({
        type: 'update-preferences',
        endpoint: '/api/preferences',
        data: preferences
      })
      return true
    }
    return false
  }
  
  // Load Data from Storage
  const loadOfflineData = () => {
    try {
      // Load offline data
      const storedData = localStorage.getItem(OFFLINE_STORAGE_KEY)
      if (storedData) {
        offlineData.value = JSON.parse(storedData)
      }
      
      // Load offline actions
      const storedActions = localStorage.getItem(OFFLINE_ACTIONS_KEY)
      if (storedActions) {
        offlineActions.value = JSON.parse(storedActions)
      }
      
      // Load sync queue
      const storedQueue = localStorage.getItem(SYNC_QUEUE_KEY)
      if (storedQueue) {
        syncQueue.value = JSON.parse(storedQueue)
      }
      
      console.log('[Offline] Data loaded from storage')
    } catch (error) {
      console.error('[Offline] Failed to load data:', error)
    }
  }
  
  // Periodic Sync
  const startPeriodicSync = () => {
    // Sync every 5 minutes when online
    setInterval(() => {
      if (isOnline.value && !isSyncing.value) {
        syncOfflineActions()
        syncQueueItems()
      }
    }, 5 * 60 * 1000) // 5 minutes
  }
  
  // Lifecycle
  onMounted(() => {
    // Load offline data
    loadOfflineData()
    
    // Set up event listeners
    window.addEventListener('online', updateConnectionStatus)
    window.addEventListener('offline', updateConnectionStatus)
    
    // Start periodic sync
    startPeriodicSync()
    
    // Initial sync if online
    if (isOnline.value) {
      setTimeout(() => {
        syncOfflineActions()
        syncQueueItems()
      }, 2000)
    }
  })
  
  onUnmounted(() => {
    // Clean up event listeners
    window.removeEventListener('online', updateConnectionStatus)
    window.removeEventListener('offline', updateConnectionStatus)
  })
  
  // Watch for connection changes
  watch(isOnline, (newValue) => {
    if (newValue) {
      // Back online
      handleBackOnline()
    } else {
      // Gone offline
      handleGoneOffline()
    }
  })
  
  return {
    // State
    isOnline,
    isOffline,
    offlineActions,
    offlineData,
    syncQueue,
    lastSyncTime,
    isSyncing,
    syncProgress,
    
    // Computed
    hasOfflineActions,
    hasSyncQueue,
    offlineDataSize,
    
    // Data Management
    storeOfflineData,
    getOfflineData,
    removeOfflineData,
    clearOfflineData,
    
    // Actions Management
    addOfflineAction,
    removeOfflineAction,
    clearOfflineActions,
    
    // Sync Management
    addToSyncQueue,
    removeFromSyncQueue,
    syncOfflineActions,
    syncQueueItems,
    
    // Offline Operations
    addToCartOffline,
    removeFromCartOffline,
    addToWishlistOffline,
    submitDiagnosisOffline,
    updatePreferencesOffline,
    
    // Utilities
    loadOfflineData
  }
}

