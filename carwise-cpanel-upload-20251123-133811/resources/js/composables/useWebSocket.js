import { ref, onMounted, onUnmounted, computed } from 'vue'

// Global WebSocket state
const websocketConnected = ref(false)
const websocketConnecting = ref(false)
const websocketError = ref(null)
const websocketReconnectAttempts = ref(0)
const maxReconnectAttempts = 5

// WebSocket instance
let websocket = null
let reconnectTimeout = null

export function useWebSocket() {
    // WebSocket connection status
    const isConnected = computed(() => websocketConnected.value)
    const isConnecting = computed(() => websocketConnecting.value)
    const hasError = computed(() => websocketError.value !== null)

    // Initialize WebSocket connection
    const connectWebSocket = () => {
        if (websocketConnecting.value || websocketConnected.value) {
            return
        }

        websocketConnecting.value = true
        websocketError.value = null

        try {
            // Use Pusher if available, otherwise fallback to native WebSocket
            if (window.Pusher) {
                initializePusher()
            } else {
                initializeNativeWebSocket()
            }
        } catch (error) {
            console.error('WebSocket connection error:', error)
            websocketError.value = error.message
            websocketConnecting.value = false
            handleReconnect()
        }
    }

    // Initialize Pusher
    const initializePusher = () => {
        try {
            const pusher = new window.Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'us2',
                useTLS: true,
                authEndpoint: '/api/websocket/auth',
                auth: {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                }
            })

            // Connection events
            pusher.connection.bind('connected', () => {
                console.log('🔌 Pusher connected successfully')
                websocketConnected.value = true
                websocketConnecting.value = false
                websocketReconnectAttempts.value = 0
                websocketError.value = null
            })

            pusher.connection.bind('disconnected', () => {
                console.log('🔌 Pusher disconnected')
                websocketConnected.value = false
                websocketConnecting.value = false
                handleReconnect()
            })

            pusher.connection.bind('error', (error) => {
                console.error('🔌 Pusher connection error:', error)
                websocketError.value = error.message || 'Connection error'
                websocketConnecting.value = false
                handleReconnect()
            })

            // Store Pusher instance globally
            window.pusher = pusher
            websocket = pusher

        } catch (error) {
            console.error('Pusher initialization error:', error)
            websocketError.value = error.message
            websocketConnecting.value = false
            handleReconnect()
        }
    }

    // Initialize native WebSocket (fallback)
    const initializeNativeWebSocket = () => {
        const wsUrl = `ws://${window.location.hostname}:6001`
        
        websocket = new WebSocket(wsUrl)

        websocket.onopen = () => {
            console.log('🔌 WebSocket connected successfully')
            websocketConnected.value = true
            websocketConnecting.value = false
            websocketReconnectAttempts.value = 0
            websocketError.value = null
        }

        websocket.onclose = () => {
            console.log('🔌 WebSocket disconnected')
            websocketConnected.value = false
            websocketConnecting.value = false
            handleReconnect()
        }

        websocket.onerror = (error) => {
            console.error('🔌 WebSocket error:', error)
            websocketError.value = 'Connection error'
            websocketConnecting.value = false
            handleReconnect()
        }

        websocket.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data)
                handleWebSocketMessage(data)
            } catch (error) {
                console.error('Error parsing WebSocket message:', error)
            }
        }
    }

    // Handle WebSocket reconnection
    const handleReconnect = () => {
        if (websocketReconnectAttempts.value >= maxReconnectAttempts) {
            console.log('🔌 Max reconnection attempts reached')
            websocketError.value = 'Connection failed after multiple attempts'
            return
        }

        websocketReconnectAttempts.value++
        const delay = Math.min(1000 * Math.pow(2, websocketReconnectAttempts.value), 30000)

        console.log(`🔌 Reconnecting in ${delay}ms (attempt ${websocketReconnectAttempts.value}/${maxReconnectAttempts})`)

        reconnectTimeout = setTimeout(() => {
            connectWebSocket()
        }, delay)
    }

    // Disconnect WebSocket
    const disconnectWebSocket = () => {
        if (reconnectTimeout) {
            clearTimeout(reconnectTimeout)
            reconnectTimeout = null
        }

        if (websocket) {
            if (window.Pusher && websocket instanceof window.Pusher) {
                websocket.disconnect()
            } else if (websocket instanceof WebSocket) {
                websocket.close()
            }
            websocket = null
        }

        websocketConnected.value = false
        websocketConnecting.value = false
        websocketReconnectAttempts.value = 0
        websocketError.value = null

        console.log('🔌 WebSocket disconnected')
    }

    // Subscribe to channel
    const subscribeToChannel = (channelName, eventName, callback) => {
        if (!websocketConnected.value || !websocket) {
            console.warn('WebSocket not connected, cannot subscribe to channel')
            return null
        }

        try {
            if (window.Pusher && websocket instanceof window.Pusher) {
                const channel = websocket.subscribe(channelName)
                const binding = channel.bind(eventName, callback)
                
                console.log(`📡 Subscribed to channel: ${channelName}, event: ${eventName}`)
                
                return {
                    channel: channelName,
                    event: eventName,
                    unbind: () => channel.unbind(eventName, callback),
                    unsubscribe: () => websocket.unsubscribe(channelName)
                }
            } else {
                // For native WebSocket, we'd need to implement channel subscription
                console.warn('Channel subscription not implemented for native WebSocket')
                return null
            }
        } catch (error) {
            console.error('Error subscribing to channel:', error)
            return null
        }
    }

    // Unsubscribe from channel
    const unsubscribeFromChannel = (subscription) => {
        if (subscription && subscription.unsubscribe) {
            subscription.unsubscribe()
            console.log(`📡 Unsubscribed from channel: ${subscription.channel}`)
        }
    }

    // Send message to channel
    const sendToChannel = async (channel, event, data) => {
        if (!websocketConnected.value) {
            console.warn('WebSocket not connected, cannot send message')
            return false
        }

        try {
            const response = await fetch('/api/websocket/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    channel: channel,
                    event: event,
                    data: data
                })
            })

            if (response.ok) {
                console.log(`📤 Message sent to channel: ${channel}, event: ${event}`)
                return true
            } else {
                console.error('Failed to send message to channel')
                return false
            }
        } catch (error) {
            console.error('Error sending message to channel:', error)
            return false
        }
    }

    // Send chat message
    const sendChatMessage = async (conversationId, message, messageType = 'text', attachments = []) => {
        try {
            const response = await fetch('/api/websocket/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    message: message,
                    message_type: messageType,
                    attachments: attachments
                })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('💬 Chat message sent successfully')
                return result.data
            } else {
                console.error('Failed to send chat message')
                return false
            }
        } catch (error) {
            console.error('Error sending chat message:', error)
            return false
        }
    }

    // Send typing indicator
    const sendTypingIndicator = async (conversationId, isTyping) => {
        try {
            const response = await fetch('/api/websocket/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    is_typing: isTyping
                })
            })

            if (response.ok) {
                console.log(`⌨️ Typing indicator sent: ${isTyping}`)
                return true
            } else {
                console.error('Failed to send typing indicator')
                return false
            }
        } catch (error) {
            console.error('Error sending typing indicator:', error)
            return false
        }
    }

    // Send user status
    const sendUserStatus = async (status) => {
        try {
            const response = await fetch('/api/websocket/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
            })

            if (response.ok) {
                console.log(`👤 User status updated: ${status}`)
                return true
            } else {
                console.error('Failed to update user status')
                return false
            }
        } catch (error) {
            console.error('Error updating user status:', error)
            return false
        }
    }

    // Handle incoming WebSocket messages
    const handleWebSocketMessage = (data) => {
        console.log('📨 WebSocket message received:', data)

        const { type, event, channel, data: messageData } = data

        switch (type) {
            case 'chat_message':
                handleChatMessage(messageData)
                break
            case 'typing_indicator':
                handleTypingIndicator(messageData)
                break
            case 'user_status':
                handleUserStatus(messageData)
                break
            case 'diagnosis_update':
                handleDiagnosisUpdate(messageData)
                break
            case 'maintenance_reminder':
                handleMaintenanceReminder(messageData)
                break
            case 'part_availability':
                handlePartAvailability(messageData)
                break
            case 'price_drop':
                handlePriceDrop(messageData)
                break
            case 'system_notification':
                handleSystemNotification(messageData)
                break
            default:
                console.log('📨 Unknown message type:', type)
        }
    }

    // Handle chat message
    const handleChatMessage = (data) => {
        console.log('💬 Chat message received:', data)
        // Emit custom event for chat components
        window.dispatchEvent(new CustomEvent('websocket:chat-message', { detail: data }))
    }

    // Handle typing indicator
    const handleTypingIndicator = (data) => {
        console.log('⌨️ Typing indicator received:', data)
        // Emit custom event for chat components
        window.dispatchEvent(new CustomEvent('websocket:typing-indicator', { detail: data }))
    }

    // Handle user status
    const handleUserStatus = (data) => {
        console.log('👤 User status received:', data)
        // Emit custom event for user status components
        window.dispatchEvent(new CustomEvent('websocket:user-status', { detail: data }))
    }

    // Handle diagnosis update
    const handleDiagnosisUpdate = (data) => {
        console.log('🔍 Diagnosis update received:', data)
        // Emit custom event for diagnosis components
        window.dispatchEvent(new CustomEvent('websocket:diagnosis-update', { detail: data }))
    }

    // Handle maintenance reminder
    const handleMaintenanceReminder = (data) => {
        console.log('🔧 Maintenance reminder received:', data)
        // Emit custom event for maintenance components
        window.dispatchEvent(new CustomEvent('websocket:maintenance-reminder', { detail: data }))
    }

    // Handle part availability
    const handlePartAvailability = (data) => {
        console.log('🔧 Part availability received:', data)
        // Emit custom event for part components
        window.dispatchEvent(new CustomEvent('websocket:part-availability', { detail: data }))
    }

    // Handle price drop
    const handlePriceDrop = (data) => {
        console.log('💰 Price drop received:', data)
        // Emit custom event for price components
        window.dispatchEvent(new CustomEvent('websocket:price-drop', { detail: data }))
    }

    // Handle system notification
    const handleSystemNotification = (data) => {
        console.log('🔔 System notification received:', data)
        // Emit custom event for notification components
        window.dispatchEvent(new CustomEvent('websocket:system-notification', { detail: data }))
    }

    // Get WebSocket status
    const getWebSocketStatus = async () => {
        try {
            const response = await fetch('/api/websocket/status', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })

            if (response.ok) {
                const result = await response.json()
                return result.data
            } else {
                console.error('Failed to get WebSocket status')
                return null
            }
        } catch (error) {
            console.error('Error getting WebSocket status:', error)
            return null
        }
    }

    // Test WebSocket connection
    const testWebSocket = async () => {
        try {
            const response = await fetch('/api/websocket/test', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })

            if (response.ok) {
                console.log('✅ WebSocket test successful')
                return true
            } else {
                console.error('❌ WebSocket test failed')
                return false
            }
        } catch (error) {
            console.error('Error testing WebSocket:', error)
            return false
        }
    }

    // Initialize on mount
    onMounted(() => {
        // Check if user is authenticated before connecting
        const token = localStorage.getItem('token')
        if (token) {
            connectWebSocket()
        }
    })

    // Cleanup on unmount
    onUnmounted(() => {
        disconnectWebSocket()
    })

    return {
        // State
        isConnected,
        isConnecting,
        hasError,
        websocketError,
        websocketReconnectAttempts,

        // Methods
        connectWebSocket,
        disconnectWebSocket,
        subscribeToChannel,
        unsubscribeFromChannel,
        sendToChannel,
        sendChatMessage,
        sendTypingIndicator,
        sendUserStatus,
        getWebSocketStatus,
        testWebSocket,
        handleWebSocketMessage
    }
}














