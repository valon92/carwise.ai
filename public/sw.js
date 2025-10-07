// CarWise.ai Service Worker - PWA Implementation
const CACHE_NAME = 'carwise-ai-v1.0.0'
const STATIC_CACHE = 'carwise-static-v1.0.0'
const DYNAMIC_CACHE = 'carwise-dynamic-v1.0.0'
const API_CACHE = 'carwise-api-v1.0.0'
const IMAGES_CACHE = 'carwise-images-v1.0.0'

// Cache strategies
const CACHE_STRATEGIES = {
  STATIC: 'cache-first',
  DYNAMIC: 'network-first',
  API: 'network-first',
  IMAGES: 'cache-first'
}

// Files to cache on install
const STATIC_FILES = [
  '/',
  '/manifest.json',
  '/favicon.ico',
  '/build/assets/app.css',
  '/build/assets/app.js',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/offline.html'
]

// API endpoints to cache
const API_ENDPOINTS = [
  '/api/parts',
  '/api/categories',
  '/api/brands',
  '/api/vehicles',
  '/api/diagnosis'
]

// Install event - cache static files
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker...')
  
  event.waitUntil(
    Promise.all([
      // Cache static files
      caches.open(STATIC_CACHE).then((cache) => {
        console.log('[SW] Caching static files...')
        return cache.addAll(STATIC_FILES)
      }),
      
      // Cache API endpoints
      caches.open(API_CACHE).then((cache) => {
        console.log('[SW] Caching API endpoints...')
        return cache.addAll(API_ENDPOINTS.map(endpoint => new Request(endpoint, {
          method: 'GET',
          headers: { 'Accept': 'application/json' }
        })))
      })
    ]).then(() => {
      console.log('[SW] Installation complete')
      return self.skipWaiting()
    })
  )
})

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating service worker...')
  
  event.waitUntil(
    Promise.all([
      // Clean up old caches
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== STATIC_CACHE && 
                cacheName !== DYNAMIC_CACHE && 
                cacheName !== API_CACHE && 
                cacheName !== IMAGES_CACHE) {
              console.log('[SW] Deleting old cache:', cacheName)
              return caches.delete(cacheName)
            }
          })
        )
      }),
      
      // Take control of all clients
      self.clients.claim()
    ]).then(() => {
      console.log('[SW] Activation complete')
    })
  )
})

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
  const { request } = event
  const url = new URL(request.url)
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return
  }
  
  // Skip chrome-extension and other non-http requests
  if (!url.protocol.startsWith('http')) {
    return
  }
  
  // Determine cache strategy based on request type
  if (isStaticFile(request)) {
    event.respondWith(cacheFirst(request, STATIC_CACHE))
  } else if (isAPIRequest(request)) {
    event.respondWith(networkFirst(request, API_CACHE))
  } else if (isImageRequest(request)) {
    event.respondWith(cacheFirst(request, IMAGES_CACHE))
  } else {
    event.respondWith(networkFirst(request, DYNAMIC_CACHE))
  }
})

// Cache First Strategy - for static files and images
async function cacheFirst(request, cacheName) {
  try {
    const cachedResponse = await caches.match(request)
    if (cachedResponse) {
      console.log('[SW] Cache hit:', request.url)
      return cachedResponse
    }
    
    console.log('[SW] Cache miss, fetching:', request.url)
    const networkResponse = await fetch(request)
    
    if (networkResponse.ok) {
      const cache = await caches.open(cacheName)
      cache.put(request, networkResponse.clone())
    }
    
    return networkResponse
  } catch (error) {
    console.error('[SW] Cache first error:', error)
    
    // Return offline page for navigation requests
    if (request.mode === 'navigate') {
      return caches.match('/offline.html')
    }
    
    // Return cached version if available
    const cachedResponse = await caches.match(request)
    if (cachedResponse) {
      return cachedResponse
    }
    
    throw error
  }
}

// Network First Strategy - for API requests and dynamic content
async function networkFirst(request, cacheName) {
  try {
    console.log('[SW] Network first, fetching:', request.url)
    const networkResponse = await fetch(request)
    
    if (networkResponse.ok) {
      const cache = await caches.open(cacheName)
      cache.put(request, networkResponse.clone())
    }
    
    return networkResponse
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url)
    
    const cachedResponse = await caches.match(request)
    if (cachedResponse) {
      return cachedResponse
    }
    
    // Return offline response for API requests
    if (isAPIRequest(request)) {
      return new Response(
        JSON.stringify({
          error: 'Offline',
          message: 'You are currently offline. Please check your connection.',
          offline: true
        }),
        {
          status: 503,
          statusText: 'Service Unavailable',
          headers: { 'Content-Type': 'application/json' }
        }
      )
    }
    
    throw error
  }
}

// Helper functions
function isStaticFile(request) {
  const url = new URL(request.url)
  return url.pathname.startsWith('/build/') ||
         url.pathname.startsWith('/icons/') ||
         url.pathname.endsWith('.css') ||
         url.pathname.endsWith('.js') ||
         url.pathname.endsWith('.woff2') ||
         url.pathname.endsWith('.woff') ||
         url.pathname.endsWith('.ttf')
}

function isAPIRequest(request) {
  const url = new URL(request.url)
  return url.pathname.startsWith('/api/') ||
         url.hostname === 'api.carwise.ai'
}

function isImageRequest(request) {
  const url = new URL(request.url)
  return url.pathname.match(/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i) ||
         url.pathname.startsWith('/images/')
}

// Background Sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('[SW] Background sync:', event.tag)
  
  if (event.tag === 'cart-sync') {
    event.waitUntil(syncCartData())
  } else if (event.tag === 'diagnosis-sync') {
    event.waitUntil(syncDiagnosisData())
  } else if (event.tag === 'notification-sync') {
    event.waitUntil(syncNotifications())
  }
})

// Push notifications
self.addEventListener('push', (event) => {
  console.log('[SW] Push notification received:', event)
  
  const options = {
    body: 'You have a new notification from CarWise.ai',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-96x96.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'View Details',
        icon: '/icons/explore-icon.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/icons/close-icon.png'
      }
    ],
    requireInteraction: true,
    silent: false
  }
  
  if (event.data) {
    const data = event.data.json()
    options.body = data.body || options.body
    options.title = data.title || 'CarWise.ai'
    options.data = { ...options.data, ...data }
  }
  
  event.waitUntil(
    self.registration.showNotification('CarWise.ai', options)
  )
})

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification clicked:', event)
  
  event.notification.close()
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/notifications')
    )
  } else if (event.action === 'close') {
    // Just close the notification
    return
  } else {
    // Default action - open the app
    event.waitUntil(
      clients.matchAll({ type: 'window' }).then((clientList) => {
        // If app is already open, focus it
        for (const client of clientList) {
          if (client.url === '/' && 'focus' in client) {
            return client.focus()
          }
        }
        
        // Otherwise open new window
        if (clients.openWindow) {
          return clients.openWindow('/')
        }
      })
    )
  }
})

// Background sync functions
async function syncCartData() {
  try {
    console.log('[SW] Syncing cart data...')
    
    // Get offline cart data from IndexedDB
    const offlineCart = await getOfflineCart()
    
    if (offlineCart && offlineCart.length > 0) {
      // Sync with server
      const response = await fetch('/api/cart/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${await getAuthToken()}`
        },
        body: JSON.stringify({ items: offlineCart })
      })
      
      if (response.ok) {
        console.log('[SW] Cart synced successfully')
        await clearOfflineCart()
      }
    }
  } catch (error) {
    console.error('[SW] Cart sync failed:', error)
  }
}

async function syncDiagnosisData() {
  try {
    console.log('[SW] Syncing diagnosis data...')
    
    const offlineDiagnoses = await getOfflineDiagnoses()
    
    for (const diagnosis of offlineDiagnoses) {
      const response = await fetch('/api/diagnosis', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${await getAuthToken()}`
        },
        body: JSON.stringify(diagnosis)
      })
      
      if (response.ok) {
        await removeOfflineDiagnosis(diagnosis.id)
      }
    }
  } catch (error) {
    console.error('[SW] Diagnosis sync failed:', error)
  }
}

async function syncNotifications() {
  try {
    console.log('[SW] Syncing notifications...')
    
    const response = await fetch('/api/notifications/sync', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${await getAuthToken()}`
      }
    })
    
    if (response.ok) {
      const notifications = await response.json()
      
      // Show notifications to user
      for (const notification of notifications) {
        await self.registration.showNotification(notification.title, {
          body: notification.message,
          icon: '/icons/icon-192x192.png',
          data: notification
        })
      }
    }
  } catch (error) {
    console.error('[SW] Notification sync failed:', error)
  }
}

// IndexedDB helpers
async function getOfflineCart() {
  // Implementation would use IndexedDB
  return []
}

async function clearOfflineCart() {
  // Implementation would clear IndexedDB
}

async function getOfflineDiagnoses() {
  // Implementation would use IndexedDB
  return []
}

async function removeOfflineDiagnosis(id) {
  // Implementation would remove from IndexedDB
}

async function getAuthToken() {
  // Implementation would get token from IndexedDB or localStorage
  return null
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
  if (event.tag === 'content-sync') {
    event.waitUntil(updateContent())
  }
})

async function updateContent() {
  try {
    console.log('[SW] Periodic content update...')
    
    // Update cached content
    const response = await fetch('/api/content/update')
    if (response.ok) {
      const content = await response.json()
      // Update caches with new content
    }
  } catch (error) {
    console.error('[SW] Periodic sync failed:', error)
  }
}

// Message handling from main thread
self.addEventListener('message', (event) => {
  console.log('[SW] Message received:', event.data)
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting()
  } else if (event.data && event.data.type === 'GET_VERSION') {
    event.ports[0].postMessage({ version: CACHE_NAME })
  } else if (event.data && event.data.type === 'CACHE_URLS') {
    event.waitUntil(
      caches.open(DYNAMIC_CACHE).then((cache) => {
        return cache.addAll(event.data.urls)
      })
    )
  }
})

console.log('[SW] Service worker loaded successfully')

