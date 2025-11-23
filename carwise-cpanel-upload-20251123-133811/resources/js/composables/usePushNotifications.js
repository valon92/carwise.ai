import { ref, onMounted, onUnmounted } from 'vue'

// Global push notification state
const pushNotificationsEnabled = ref(false)
const pushNotificationsSupported = ref(false)
const pushToken = ref(null)
const permissionStatus = ref('default')

export function usePushNotifications() {
    // Check if push notifications are supported
    const checkSupport = () => {
        if (typeof window !== 'undefined' && 'serviceWorker' in navigator && 'PushManager' in window) {
            pushNotificationsSupported.value = true
            return true
        }
        return false
    }

    // Request notification permission
    const requestPermission = async () => {
        if (!checkSupport()) {
            console.warn('Push notifications not supported')
            return false
        }

        try {
            const permission = await Notification.requestPermission()
            permissionStatus.value = permission
            
            if (permission === 'granted') {
                pushNotificationsEnabled.value = true
                console.log('✅ Push notification permission granted')
                return true
            } else {
                console.warn('❌ Push notification permission denied')
                return false
            }
        } catch (error) {
            console.error('Error requesting notification permission:', error)
            return false
        }
    }

    // Register service worker for push notifications
    const registerServiceWorker = async () => {
        if (!checkSupport()) return false

        try {
            const registration = await navigator.serviceWorker.register('/sw.js')
            console.log('Service Worker registered:', registration)
            return registration
        } catch (error) {
            console.error('Service Worker registration failed:', error)
            return false
        }
    }

    // Subscribe to push notifications
    const subscribeToPush = async () => {
        if (!checkSupport() || !pushNotificationsEnabled.value) return false

        try {
            const registration = await navigator.serviceWorker.ready
            
            // Get Firebase messaging token
            if (window.firebase && window.firebase.messaging) {
                const messaging = window.firebase.messaging()
                const token = await messaging.getToken({
                    vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY
                })
                
                if (token) {
                    pushToken.value = token
                    console.log('📱 Firebase push token:', token)
                    
                    // Send token to server
                    await sendTokenToServer(token)
                    return token
                }
            }

            // Fallback to OneSignal
            if (window.OneSignal) {
                await window.OneSignal.init({
                    appId: import.meta.env.VITE_ONESIGNAL_APP_ID,
                    allowLocalhostAsSecureOrigin: true,
                    notifyButton: {
                        enable: false
                    }
                })

                const playerId = await window.OneSignal.getUserId()
                if (playerId) {
                    pushToken.value = playerId
                    console.log('📱 OneSignal player ID:', playerId)
                    
                    // Send token to server
                    await sendTokenToServer(playerId)
                    return playerId
                }
            }

            return false
        } catch (error) {
            console.error('Error subscribing to push notifications:', error)
            return false
        }
    }

    // Send token to server
    const sendTokenToServer = async (token) => {
        try {
            const response = await fetch('/api/push-notifications/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    token: token,
                    platform: getPlatform(),
                    user_agent: navigator.userAgent
                })
            })

            if (response.ok) {
                console.log('✅ Push token registered with server')
                return true
            } else {
                console.error('❌ Failed to register push token with server')
                return false
            }
        } catch (error) {
            console.error('Error sending token to server:', error)
            return false
        }
    }

    // Get platform information
    const getPlatform = () => {
        const userAgent = navigator.userAgent.toLowerCase()
        
        if (userAgent.includes('android')) return 'android'
        if (userAgent.includes('iphone') || userAgent.includes('ipad')) return 'ios'
        if (userAgent.includes('windows')) return 'windows'
        if (userAgent.includes('mac')) return 'mac'
        if (userAgent.includes('linux')) return 'linux'
        
        return 'web'
    }

    // Initialize push notifications
    const initializePushNotifications = async () => {
        if (!checkSupport()) {
            console.warn('Push notifications not supported in this browser')
            return false
        }

        try {
            // Request permission
            const permissionGranted = await requestPermission()
            if (!permissionGranted) return false

            // Register service worker
            await registerServiceWorker()

            // Subscribe to push notifications
            const token = await subscribeToPush()
            if (token) {
                console.log('🎉 Push notifications initialized successfully')
                return true
            }

            return false
        } catch (error) {
            console.error('Error initializing push notifications:', error)
            return false
        }
    }

    // Show local notification
    const showLocalNotification = (title, options = {}) => {
        if (!pushNotificationsEnabled.value) return false

        try {
            const notification = new Notification(title, {
                icon: '/icons/icon-192x192.png',
                badge: '/icons/icon-72x72.png',
                tag: 'carwise-notification',
                requireInteraction: false,
                ...options
            })

            // Auto-close after 5 seconds
            setTimeout(() => {
                notification.close()
            }, 5000)

            // Handle click
            notification.onclick = () => {
                window.focus()
                if (options.url) {
                    window.location.href = options.url
                }
                notification.close()
            }

            return notification
        } catch (error) {
            console.error('Error showing local notification:', error)
            return false
        }
    }

    // Handle incoming push notifications
    const handlePushMessage = (payload) => {
        console.log('📨 Received push message:', payload)

        const { title, body, data } = payload

        // Show local notification
        showLocalNotification(title, {
            body: body,
            data: data,
            url: data?.url || '/'
        })

        // Handle notification data
        if (data) {
            handleNotificationData(data)
        }
    }

    // Handle notification data
    const handleNotificationData = (data) => {
        const { type, url } = data

        switch (type) {
            case 'diagnosis_complete':
                console.log('🔍 Diagnosis complete notification received')
                break
            case 'maintenance_reminder':
                console.log('🔧 Maintenance reminder notification received')
                break
            case 'part_available':
                console.log('🔧 Part available notification received')
                break
            case 'price_drop':
                console.log('💰 Price drop notification received')
                break
            case 'appointment_reminder':
                console.log('👨‍🔧 Appointment reminder notification received')
                break
            default:
                console.log('📨 Custom notification received:', type)
        }

        // Navigate to URL if provided
        if (url && url !== window.location.pathname) {
            setTimeout(() => {
                window.location.href = url
            }, 1000)
        }
    }

    // Unsubscribe from push notifications
    const unsubscribeFromPush = async () => {
        try {
            if (window.firebase && window.firebase.messaging) {
                const messaging = window.firebase.messaging()
                await messaging.deleteToken()
            }

            if (window.OneSignal) {
                await window.OneSignal.setSubscription(false)
            }

            // Remove token from server
            await fetch('/api/push-notifications/unregister', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    token: pushToken.value
                })
            })

            pushToken.value = null
            pushNotificationsEnabled.value = false
            console.log('🔕 Unsubscribed from push notifications')
            return true
        } catch (error) {
            console.error('Error unsubscribing from push notifications:', error)
            return false
        }
    }

    // Check notification permission status
    const checkPermissionStatus = () => {
        if (typeof window !== 'undefined' && 'Notification' in window) {
            permissionStatus.value = Notification.permission
            return Notification.permission
        }
        return 'denied'
    }

    // Test push notification
    const testPushNotification = () => {
        if (!pushNotificationsEnabled.value) {
            console.warn('Push notifications not enabled')
            return false
        }

        showLocalNotification('Test Notification - CarWise.ai', {
            body: 'This is a test notification to verify push notifications are working correctly.',
            data: {
                type: 'test',
                url: '/'
            }
        })

        return true
    }

    // Get push notification status
    const getPushNotificationStatus = () => {
        return {
            supported: pushNotificationsSupported.value,
            enabled: pushNotificationsEnabled.value,
            permission: permissionStatus.value,
            token: pushToken.value ? `${pushToken.value.substring(0, 20)}...` : null,
            platform: getPlatform()
        }
    }

    // Initialize on mount
    onMounted(async () => {
        // Check support
        checkSupport()
        
        // Check permission status
        checkPermissionStatus()

        // Auto-initialize if permission is already granted
        if (permissionStatus.value === 'granted') {
            await initializePushNotifications()
        }
    })

    return {
        pushNotificationsEnabled,
        pushNotificationsSupported,
        pushToken,
        permissionStatus,
        checkSupport,
        requestPermission,
        initializePushNotifications,
        subscribeToPush,
        unsubscribeFromPush,
        showLocalNotification,
        handlePushMessage,
        testPushNotification,
        getPushNotificationStatus,
        checkPermissionStatus
    }
}

// Global push notification state















