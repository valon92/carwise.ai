// Service Worker for CarWise.ai Push Notifications
const CACHE_NAME = 'carwise-sw-v1';
const urlsToCache = [
    '/',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
    
    // Skip waiting to activate immediately
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    // Take control of all clients immediately
    self.clients.claim();
});

// Fetch event
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            })
    );
});

// Push event - Handle incoming push notifications
self.addEventListener('push', (event) => {
    console.log('Push event received:', event);
    
    let notificationData = {
        title: 'CarWise.ai',
        body: 'You have a new notification',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        tag: 'carwise-notification',
        requireInteraction: false,
        data: {
            url: '/'
        }
    };
    
    // Parse push data if available
    if (event.data) {
        try {
            const pushData = event.data.json();
            notificationData = {
                ...notificationData,
                ...pushData
            };
        } catch (error) {
            console.error('Error parsing push data:', error);
            notificationData.body = event.data.text() || notificationData.body;
        }
    }
    
    // Show notification
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            data: notificationData.data,
            actions: [
                {
                    action: 'view',
                    title: 'View',
                    icon: '/icons/icon-72x72.png'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                    icon: '/icons/icon-72x72.png'
                }
            ]
        })
    );
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    const action = event.action;
    const notificationData = event.notification.data || {};
    
    if (action === 'dismiss') {
        // Just close the notification
        return;
    }
    
    // Default action or 'view' action
    const urlToOpen = notificationData.url || '/';
    
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((clientList) => {
            // Check if there's already a window/tab open with the target URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(urlToOpen) && 'focus' in client) {
                    return client.focus();
                }
            }
            
            // If no existing window, open a new one
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Background sync event (for offline functionality)
self.addEventListener('sync', (event) => {
    console.log('Background sync event:', event);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Handle background sync tasks
            handleBackgroundSync()
        );
    }
});

// Handle background sync
async function handleBackgroundSync() {
    try {
        // Get pending requests from IndexedDB
        const pendingRequests = await getPendingRequests();
        
        for (const request of pendingRequests) {
            try {
                await fetch(request.url, request.options);
                // Remove from pending requests if successful
                await removePendingRequest(request.id);
            } catch (error) {
                console.error('Background sync failed for request:', request, error);
            }
        }
    } catch (error) {
        console.error('Background sync error:', error);
    }
}

// Get pending requests from IndexedDB
async function getPendingRequests() {
    // This would typically interact with IndexedDB
    // For now, return empty array
    return [];
}

// Remove pending request from IndexedDB
async function removePendingRequest(requestId) {
    // This would typically interact with IndexedDB
    // For now, just log
    console.log('Removing pending request:', requestId);
}

// Message event - Handle messages from main thread
self.addEventListener('message', (event) => {
    console.log('Service Worker received message:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

// Error event
self.addEventListener('error', (event) => {
    console.error('Service Worker error:', event);
});

// Unhandled rejection event
self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker unhandled rejection:', event);
});

console.log('CarWise.ai Service Worker loaded successfully');