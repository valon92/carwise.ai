import { ref, computed, onMounted, onUnmounted } from 'vue'

export function usePWA() {
  // PWA State
  const isInstalled = ref(false)
  const isInstallable = ref(false)
  const isOnline = ref(navigator.onLine)
  const isStandalone = ref(false)
  const hasUpdate = ref(false)
  const isUpdateAvailable = ref(false)
  const swRegistration = ref(null)
  const installPrompt = ref(null)
  const updatePrompt = ref(null)
  
  // PWA Features
  const canInstall = ref(false)
  const canShare = ref(false)
  const canSync = ref(false)
  const canPush = ref(false)
  const canBackgroundSync = ref(false)
  const canPeriodicSync = ref(false)
  
  // Computed properties
  const pwaStatus = computed(() => {
    if (isInstalled.value) return 'installed'
    if (isInstallable.value) return 'installable'
    return 'browser'
  })
  
  const isPWAReady = computed(() => {
    return isInstalled.value || isStandalone.value
  })
  
  const offlineCapabilities = computed(() => {
    return {
      canCache: true,
      canSync: canSync.value,
      canBackgroundSync: canBackgroundSync.value,
      canPeriodicSync: canPeriodicSync.value
    }
  })
  
  // Service Worker Registration
  const registerServiceWorker = async () => {
    if ('serviceWorker' in navigator) {
      try {
        const registration = await navigator.serviceWorker.register('/sw.js', {
          scope: '/'
        })
        
        swRegistration.value = registration
        console.log('[PWA] Service Worker registered:', registration)
        
        // Check for updates
        registration.addEventListener('updatefound', () => {
          const newWorker = registration.installing
          if (newWorker) {
            newWorker.addEventListener('statechange', () => {
              if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                hasUpdate.value = true
                isUpdateAvailable.value = true
                showUpdateNotification()
              }
            })
          }
        })
        
        return registration
      } catch (error) {
        console.error('[PWA] Service Worker registration failed:', error)
        return null
      }
    }
    return null
  }
  
  // Install PWA
  const installPWA = async () => {
    if (installPrompt.value) {
      const result = await installPrompt.value.prompt()
      console.log('[PWA] Install prompt result:', result)
      
      if (result.outcome === 'accepted') {
        isInstalled.value = true
        isInstallable.value = false
        installPrompt.value = null
        
        // Track installation
        trackPWAInstallation()
        
        return true
      }
    }
    return false
  }
  
  // Update PWA
  const updatePWA = async () => {
    if (swRegistration.value && swRegistration.value.waiting) {
      // Tell the waiting service worker to skip waiting
      swRegistration.value.waiting.postMessage({ type: 'SKIP_WAITING' })
      
      // Reload the page to use the new service worker
      window.location.reload()
      return true
    }
    return false
  }
  
  // Check PWA Installation Status
  const checkInstallationStatus = () => {
    // Check if running as PWA
    isStandalone.value = window.matchMedia('(display-mode: standalone)').matches ||
                        window.navigator.standalone ||
                        document.referrer.includes('android-app://')
    
    // Check if already installed
    isInstalled.value = isStandalone.value || localStorage.getItem('pwa-installed') === 'true'
    
    console.log('[PWA] Installation status:', {
      isStandalone: isStandalone.value,
      isInstalled: isInstalled.value
    })
  }
  
  // Check PWA Capabilities
  const checkPWACapabilities = () => {
    canInstall.value = 'serviceWorker' in navigator && 'PushManager' in window
    canShare.value = 'share' in navigator
    canSync.value = 'serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype
    canPush.value = 'PushManager' in window && 'Notification' in window
    canBackgroundSync.value = 'serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype
    canPeriodicSync.value = 'serviceWorker' in navigator && 'periodicSync' in window.ServiceWorkerRegistration.prototype
    
    console.log('[PWA] Capabilities:', {
      canInstall: canInstall.value,
      canShare: canShare.value,
      canSync: canSync.value,
      canPush: canPush.value,
      canBackgroundSync: canBackgroundSync.value,
      canPeriodicSync: canPeriodicSync.value
    })
  }
  
  // Request Notification Permission
  const requestNotificationPermission = async () => {
    if ('Notification' in window) {
      const permission = await Notification.requestPermission()
      console.log('[PWA] Notification permission:', permission)
      return permission === 'granted'
    }
    return false
  }
  
  // Subscribe to Push Notifications
  const subscribeToPush = async () => {
    if (!swRegistration.value || !canPush.value) {
      return null
    }
    
    try {
      const subscription = await swRegistration.value.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: process.env.VITE_VAPID_PUBLIC_KEY
      })
      
      console.log('[PWA] Push subscription:', subscription)
      
      // Send subscription to server
      await fetch('/api/push/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(subscription)
      })
      
      return subscription
    } catch (error) {
      console.error('[PWA] Push subscription failed:', error)
      return null
    }
  }
  
  // Background Sync
  const registerBackgroundSync = async (tag, data = {}) => {
    if (!canBackgroundSync.value || !swRegistration.value) {
      return false
    }
    
    try {
      await swRegistration.value.sync.register(tag)
      console.log('[PWA] Background sync registered:', tag)
      
      // Store data for sync
      if (Object.keys(data).length > 0) {
        localStorage.setItem(`sync-${tag}`, JSON.stringify(data))
      }
      
      return true
    } catch (error) {
      console.error('[PWA] Background sync registration failed:', error)
      return false
    }
  }
  
  // Periodic Background Sync
  const registerPeriodicSync = async (tag, minInterval = 86400000) => {
    if (!canPeriodicSync.value || !swRegistration.value) {
      return false
    }
    
    try {
      await swRegistration.value.periodicSync.register(tag, {
        minInterval: minInterval
      })
      console.log('[PWA] Periodic sync registered:', tag)
      return true
    } catch (error) {
      console.error('[PWA] Periodic sync registration failed:', error)
      return false
    }
  }
  
  // Share Content
  const shareContent = async (data) => {
    if (!canShare.value) {
      return false
    }
    
    try {
      await navigator.share(data)
      console.log('[PWA] Content shared:', data)
      return true
    } catch (error) {
      console.error('[PWA] Share failed:', error)
      return false
    }
  }
  
  // Cache Management
  const clearCache = async () => {
    if (swRegistration.value) {
      try {
        const cacheNames = await caches.keys()
        await Promise.all(
          cacheNames.map(cacheName => caches.delete(cacheName))
        )
        console.log('[PWA] Cache cleared')
        return true
      } catch (error) {
        console.error('[PWA] Cache clear failed:', error)
        return false
      }
    }
    return false
  }
  
  const getCacheSize = async () => {
    if (swRegistration.value) {
      try {
        const cacheNames = await caches.keys()
        let totalSize = 0
        
        for (const cacheName of cacheNames) {
          const cache = await caches.open(cacheName)
          const keys = await cache.keys()
          totalSize += keys.length
        }
        
        return totalSize
      } catch (error) {
        console.error('[PWA] Cache size calculation failed:', error)
        return 0
      }
    }
    return 0
  }
  
  // Offline Data Management
  const storeOfflineData = (key, data) => {
    try {
      localStorage.setItem(`offline-${key}`, JSON.stringify({
        data,
        timestamp: Date.now()
      }))
      console.log('[PWA] Offline data stored:', key)
      return true
    } catch (error) {
      console.error('[PWA] Offline data storage failed:', error)
      return false
    }
  }
  
  const getOfflineData = (key) => {
    try {
      const stored = localStorage.getItem(`offline-${key}`)
      if (stored) {
        const parsed = JSON.parse(stored)
        return parsed.data
      }
      return null
    } catch (error) {
      console.error('[PWA] Offline data retrieval failed:', error)
      return null
    }
  }
  
  const clearOfflineData = (key) => {
    try {
      localStorage.removeItem(`offline-${key}`)
      console.log('[PWA] Offline data cleared:', key)
      return true
    } catch (error) {
      console.error('[PWA] Offline data clear failed:', error)
      return false
    }
  }
  
  // Event Handlers
  const handleBeforeInstallPrompt = (e) => {
    e.preventDefault()
    installPrompt.value = e
    isInstallable.value = true
    console.log('[PWA] Install prompt available')
  }
  
  const handleAppInstalled = () => {
    isInstalled.value = true
    isInstallable.value = false
    installPrompt.value = null
    localStorage.setItem('pwa-installed', 'true')
    console.log('[PWA] App installed')
    
    // Track installation
    trackPWAInstallation()
  }
  
  const handleOnline = () => {
    isOnline.value = true
    console.log('[PWA] Back online')
    
    // Trigger background sync
    if (canBackgroundSync.value && swRegistration.value) {
      swRegistration.value.sync.register('online-sync')
    }
  }
  
  const handleOffline = () => {
    isOnline.value = false
    console.log('[PWA] Gone offline')
  }
  
  // Notifications
  const showUpdateNotification = () => {
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('CarWise.ai Update Available', {
        body: 'A new version is available. Click to update.',
        icon: '/icons/icon-192x192.png',
        tag: 'pwa-update',
        requireInteraction: true,
        actions: [
          { action: 'update', title: 'Update Now' },
          { action: 'dismiss', title: 'Later' }
        ]
      })
    }
  }
  
  // Analytics
  const trackPWAInstallation = () => {
    // Track PWA installation event
    if (typeof gtag !== 'undefined') {
      gtag('event', 'pwa_install', {
        event_category: 'PWA',
        event_label: 'Installation'
      })
    }
  }
  
  // Lifecycle
  onMounted(async () => {
    // Check installation status
    checkInstallationStatus()
    
    // Check PWA capabilities
    checkPWACapabilities()
    
    // Register service worker
    await registerServiceWorker()
    
    // Set up event listeners
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
    window.addEventListener('appinstalled', handleAppInstalled)
    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)
    
    // Request notification permission
    if (canPush.value) {
      await requestNotificationPermission()
    }
  })
  
  onUnmounted(() => {
    // Clean up event listeners
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
    window.removeEventListener('appinstalled', handleAppInstalled)
    window.removeEventListener('online', handleOnline)
    window.removeEventListener('offline', handleOffline)
  })
  
  return {
    // State
    isInstalled,
    isInstallable,
    isOnline,
    isStandalone,
    hasUpdate,
    isUpdateAvailable,
    swRegistration,
    installPrompt,
    updatePrompt,
    
    // Features
    canInstall,
    canShare,
    canSync,
    canPush,
    canBackgroundSync,
    canPeriodicSync,
    
    // Computed
    pwaStatus,
    isPWAReady,
    offlineCapabilities,
    
    // Methods
    installPWA,
    updatePWA,
    requestNotificationPermission,
    subscribeToPush,
    registerBackgroundSync,
    registerPeriodicSync,
    shareContent,
    clearCache,
    getCacheSize,
    storeOfflineData,
    getOfflineData,
    clearOfflineData
  }
}

