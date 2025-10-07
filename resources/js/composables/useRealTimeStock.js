import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'

export function useRealTimeStock() {
  // State
  const isConnected = ref(false)
  const stockUpdates = reactive(new Map())
  const lastUpdate = ref(null)
  const reconnectAttempts = ref(0)
  const maxReconnectAttempts = 5
  const reconnectDelay = ref(1000)
  
  // WebSocket connection
  let ws = null
  let reconnectTimer = null
  let heartbeatTimer = null

  // Stock levels and notifications
  const stockAlerts = ref([])
  const lowStockThreshold = ref(5)
  const criticalStockThreshold = ref(2)

  // Initialize WebSocket connection
  const connect = () => {
    try {
      // Use secure WebSocket in production, regular WebSocket in development
      const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:'
      const wsUrl = `${protocol}//${window.location.host}/ws/stock-updates`
      
      ws = new WebSocket(wsUrl)
      
      ws.onopen = () => {
        console.log('🔗 Real-time stock connection established')
        isConnected.value = true
        reconnectAttempts.value = 0
        reconnectDelay.value = 1000
        
        // Send authentication if user is logged in
        const token = localStorage.getItem('token')
        if (token) {
          ws.send(JSON.stringify({
            type: 'auth',
            token: token
          }))
        }
        
        // Start heartbeat
        startHeartbeat()
        
        // Subscribe to stock updates
        ws.send(JSON.stringify({
          type: 'subscribe',
          channel: 'stock_updates'
        }))
      }
      
      ws.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)
          handleStockUpdate(data)
        } catch (error) {
          console.error('Error parsing WebSocket message:', error)
        }
      }
      
      ws.onclose = (event) => {
        console.log('📡 Stock connection closed:', event.code, event.reason)
        isConnected.value = false
        stopHeartbeat()
        
        // Attempt to reconnect if not a normal closure
        if (event.code !== 1000 && reconnectAttempts.value < maxReconnectAttempts) {
          scheduleReconnect()
        }
      }
      
      ws.onerror = (error) => {
        console.error('❌ WebSocket error:', error)
        isConnected.value = false
      }
      
    } catch (error) {
      console.error('Failed to establish WebSocket connection:', error)
      scheduleReconnect()
    }
  }

  // Handle incoming stock updates
  const handleStockUpdate = (data) => {
    lastUpdate.value = new Date()
    
    switch (data.type) {
      case 'stock_update':
        updatePartStock(data.payload)
        break
      case 'stock_alert':
        addStockAlert(data.payload)
        break
      case 'bulk_update':
        handleBulkUpdate(data.payload)
        break
      case 'pong':
        // Heartbeat response
        break
      default:
        console.log('Unknown message type:', data.type)
    }
  }

  // Update individual part stock
  const updatePartStock = (update) => {
    const { part_id, stock_quantity, previous_quantity, source, timestamp } = update
    
    // Store the update
    stockUpdates.set(part_id, {
      stock_quantity,
      previous_quantity,
      source,
      timestamp: new Date(timestamp),
      change: stock_quantity - (previous_quantity || 0)
    })
    
    // Check for low stock alerts
    if (stock_quantity <= criticalStockThreshold.value) {
      addStockAlert({
        part_id,
        type: 'critical',
        message: `Critical stock level: Only ${stock_quantity} left!`,
        stock_quantity
      })
    } else if (stock_quantity <= lowStockThreshold.value) {
      addStockAlert({
        part_id,
        type: 'low',
        message: `Low stock: Only ${stock_quantity} left`,
        stock_quantity
      })
    }
    
    // Emit custom event for components to listen to
    window.dispatchEvent(new CustomEvent('stockUpdate', {
      detail: { part_id, stock_quantity, previous_quantity, change: stock_quantity - (previous_quantity || 0) }
    }))
  }

  // Handle bulk stock updates
  const handleBulkUpdate = (updates) => {
    updates.forEach(update => {
      updatePartStock(update)
    })
  }

  // Add stock alert
  const addStockAlert = (alert) => {
    const alertWithId = {
      id: Date.now() + Math.random(),
      timestamp: new Date(),
      ...alert
    }
    
    stockAlerts.value.unshift(alertWithId)
    
    // Keep only last 50 alerts
    if (stockAlerts.value.length > 50) {
      stockAlerts.value = stockAlerts.value.slice(0, 50)
    }
    
    // Show browser notification if permission granted
    if (Notification.permission === 'granted') {
      new Notification('Stock Alert', {
        body: alert.message,
        icon: '/icons/icon-96x96.png',
        tag: `stock-${alert.part_id}`
      })
    }
  }

  // Start heartbeat to keep connection alive
  const startHeartbeat = () => {
    heartbeatTimer = setInterval(() => {
      if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ type: 'ping' }))
      }
    }, 30000) // Send ping every 30 seconds
  }

  // Stop heartbeat
  const stopHeartbeat = () => {
    if (heartbeatTimer) {
      clearInterval(heartbeatTimer)
      heartbeatTimer = null
    }
  }

  // Schedule reconnection
  const scheduleReconnect = () => {
    if (reconnectAttempts.value >= maxReconnectAttempts) {
      console.log('Max reconnection attempts reached')
      return
    }
    
    reconnectAttempts.value++
    console.log(`Scheduling reconnection attempt ${reconnectAttempts.value} in ${reconnectDelay.value}ms`)
    
    reconnectTimer = setTimeout(() => {
      connect()
      reconnectDelay.value = Math.min(reconnectDelay.value * 2, 30000) // Exponential backoff, max 30s
    }, reconnectDelay.value)
  }

  // Disconnect WebSocket
  const disconnect = () => {
    if (reconnectTimer) {
      clearTimeout(reconnectTimer)
      reconnectTimer = null
    }
    
    stopHeartbeat()
    
    if (ws) {
      ws.close(1000, 'Manual disconnect')
      ws = null
    }
    
    isConnected.value = false
  }

  // Get stock info for a specific part
  const getPartStock = (partId) => {
    return stockUpdates.get(partId) || null
  }

  // Get stock level indicator
  const getStockLevel = (quantity) => {
    if (quantity <= 0) return 'out-of-stock'
    if (quantity <= criticalStockThreshold.value) return 'critical'
    if (quantity <= lowStockThreshold.value) return 'low'
    return 'normal'
  }

  // Get stock level color
  const getStockColor = (quantity) => {
    const level = getStockLevel(quantity)
    const colors = {
      'out-of-stock': 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/20',
      'critical': 'text-orange-600 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/20',
      'low': 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20',
      'normal': 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20'
    }
    return colors[level]
  }

  // Get stock level text
  const getStockText = (quantity) => {
    const level = getStockLevel(quantity)
    const texts = {
      'out-of-stock': 'Out of Stock',
      'critical': `Only ${quantity} left!`,
      'low': `${quantity} in stock`,
      'normal': 'In Stock'
    }
    return texts[level]
  }

  // Clear old alerts
  const clearOldAlerts = () => {
    const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000)
    stockAlerts.value = stockAlerts.value.filter(alert => alert.timestamp > oneHourAgo)
  }

  // Dismiss alert
  const dismissAlert = (alertId) => {
    const index = stockAlerts.value.findIndex(alert => alert.id === alertId)
    if (index !== -1) {
      stockAlerts.value.splice(index, 1)
    }
  }

  // Request notification permission
  const requestNotificationPermission = async () => {
    if ('Notification' in window && Notification.permission === 'default') {
      const permission = await Notification.requestPermission()
      return permission === 'granted'
    }
    return Notification.permission === 'granted'
  }

  // Computed properties
  const connectionStatus = computed(() => {
    if (isConnected.value) return 'connected'
    if (reconnectAttempts.value > 0) return 'reconnecting'
    return 'disconnected'
  })

  const hasUnreadAlerts = computed(() => {
    return stockAlerts.value.some(alert => !alert.read)
  })

  const criticalAlerts = computed(() => {
    return stockAlerts.value.filter(alert => alert.type === 'critical')
  })

  // Lifecycle
  onMounted(() => {
    connect()
    requestNotificationPermission()
    
    // Clear old alerts every hour
    setInterval(clearOldAlerts, 60 * 60 * 1000)
  })

  onUnmounted(() => {
    disconnect()
  })

  return {
    // State
    isConnected,
    connectionStatus,
    stockUpdates,
    stockAlerts,
    lastUpdate,
    hasUnreadAlerts,
    criticalAlerts,
    
    // Methods
    connect,
    disconnect,
    getPartStock,
    getStockLevel,
    getStockColor,
    getStockText,
    dismissAlert,
    clearOldAlerts,
    requestNotificationPermission,
    
    // Configuration
    lowStockThreshold,
    criticalStockThreshold
  }
}

