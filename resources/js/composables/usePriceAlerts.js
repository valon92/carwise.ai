import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'

export function usePriceAlerts() {
  // State
  const priceAlerts = ref([])
  const activePriceAlerts = ref([])
  const priceHistory = reactive(new Map())
  const isTrackingPrices = ref(false)
  const lastPriceUpdate = ref(null)

  // Price alert settings
  const alertSettings = ref({
    enablePriceDropAlerts: true,
    enablePriceIncreaseAlerts: false,
    enableTargetPriceAlerts: true,
    minimumDropPercentage: 5, // Alert when price drops by 5% or more
    minimumIncreasePercentage: 10, // Alert when price increases by 10% or more
    checkInterval: 30000, // Check every 30 seconds
    maxHistoryDays: 30 // Keep price history for 30 days
  })

  // Price alert types
  const ALERT_TYPES = {
    PRICE_DROP: 'price_drop',
    PRICE_INCREASE: 'price_increase',
    TARGET_PRICE: 'target_price',
    SIGNIFICANT_CHANGE: 'significant_change'
  }

  // Create a price alert
  const createPriceAlert = (partId, alertType, targetPrice = null, threshold = null) => {
    const alert = {
      id: `alert_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
      partId,
      alertType,
      targetPrice,
      threshold,
      isActive: true,
      createdAt: new Date(),
      lastChecked: null,
      triggeredCount: 0,
      metadata: {
        originalPrice: getCurrentPrice(partId),
        userAgent: navigator.userAgent,
        source: 'user_created'
      }
    }

    priceAlerts.value.push(alert)
    saveAlertsToStorage()
    
    return alert
  }

  // Remove a price alert
  const removePriceAlert = (alertId) => {
    const index = priceAlerts.value.findIndex(alert => alert.id === alertId)
    if (index !== -1) {
      priceAlerts.value.splice(index, 1)
      saveAlertsToStorage()
      return true
    }
    return false
  }

  // Update price for a part and check alerts
  const updatePartPrice = (partId, newPrice, oldPrice = null) => {
    const now = new Date()
    
    // Store price history
    if (!priceHistory.has(partId)) {
      priceHistory.set(partId, [])
    }
    
    const history = priceHistory.get(partId)
    history.push({
      price: newPrice,
      timestamp: now,
      change: oldPrice ? newPrice - oldPrice : 0,
      changePercentage: oldPrice ? ((newPrice - oldPrice) / oldPrice) * 100 : 0
    })

    // Keep only recent history
    const maxEntries = 100
    if (history.length > maxEntries) {
      history.splice(0, history.length - maxEntries)
    }

    lastPriceUpdate.value = now

    // Check alerts for this part
    checkAlertsForPart(partId, newPrice, oldPrice)

    // Emit custom event for other components
    window.dispatchEvent(new CustomEvent('priceUpdate', {
      detail: { partId, newPrice, oldPrice, timestamp: now }
    }))
  }

  // Check alerts for a specific part
  const checkAlertsForPart = (partId, currentPrice, previousPrice) => {
    const partAlerts = priceAlerts.value.filter(alert => 
      alert.partId === partId && alert.isActive
    )

    partAlerts.forEach(alert => {
      alert.lastChecked = new Date()
      
      let shouldTrigger = false
      let alertMessage = ''
      let alertData = {}

      switch (alert.alertType) {
        case ALERT_TYPES.PRICE_DROP:
          if (previousPrice && currentPrice < previousPrice) {
            const dropPercentage = ((previousPrice - currentPrice) / previousPrice) * 100
            if (dropPercentage >= (alert.threshold || alertSettings.value.minimumDropPercentage)) {
              shouldTrigger = true
              alertMessage = `Price dropped by ${dropPercentage.toFixed(1)}%! Now $${currentPrice.toFixed(2)} (was $${previousPrice.toFixed(2)})`
              alertData = { dropPercentage, currentPrice, previousPrice }
            }
          }
          break

        case ALERT_TYPES.PRICE_INCREASE:
          if (previousPrice && currentPrice > previousPrice) {
            const increasePercentage = ((currentPrice - previousPrice) / previousPrice) * 100
            if (increasePercentage >= (alert.threshold || alertSettings.value.minimumIncreasePercentage)) {
              shouldTrigger = true
              alertMessage = `Price increased by ${increasePercentage.toFixed(1)}%! Now $${currentPrice.toFixed(2)} (was $${previousPrice.toFixed(2)})`
              alertData = { increasePercentage, currentPrice, previousPrice }
            }
          }
          break

        case ALERT_TYPES.TARGET_PRICE:
          if (alert.targetPrice && currentPrice <= alert.targetPrice) {
            shouldTrigger = true
            alertMessage = `Target price reached! Now $${currentPrice.toFixed(2)} (target: $${alert.targetPrice.toFixed(2)})`
            alertData = { targetPrice: alert.targetPrice, currentPrice }
          }
          break

        case ALERT_TYPES.SIGNIFICANT_CHANGE:
          if (previousPrice) {
            const changePercentage = Math.abs(((currentPrice - previousPrice) / previousPrice) * 100)
            if (changePercentage >= (alert.threshold || 15)) {
              shouldTrigger = true
              const direction = currentPrice > previousPrice ? 'increased' : 'decreased'
              alertMessage = `Significant price change! Price ${direction} by ${changePercentage.toFixed(1)}%`
              alertData = { changePercentage, currentPrice, previousPrice, direction }
            }
          }
          break
      }

      if (shouldTrigger) {
        triggerPriceAlert(alert, alertMessage, alertData)
      }
    })
  }

  // Trigger a price alert
  const triggerPriceAlert = (alert, message, data) => {
    alert.triggeredCount++
    
    const alertNotification = {
      id: `notification_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
      alertId: alert.id,
      partId: alert.partId,
      type: alert.alertType,
      message,
      data,
      timestamp: new Date(),
      isRead: false,
      priority: getPriorityLevel(alert.alertType, data)
    }

    activePriceAlerts.value.unshift(alertNotification)

    // Keep only last 50 notifications
    if (activePriceAlerts.value.length > 50) {
      activePriceAlerts.value = activePriceAlerts.value.slice(0, 50)
    }

    // Show browser notification if permission granted
    showBrowserNotification(alertNotification)

    // Show in-app notification
    showInAppNotification(alertNotification)

    // Save to storage
    saveAlertsToStorage()
  }

  // Get priority level for alert
  const getPriorityLevel = (alertType, data) => {
    switch (alertType) {
      case ALERT_TYPES.TARGET_PRICE:
        return 'high'
      case ALERT_TYPES.PRICE_DROP:
        return data.dropPercentage > 20 ? 'high' : 'medium'
      case ALERT_TYPES.SIGNIFICANT_CHANGE:
        return data.changePercentage > 25 ? 'high' : 'medium'
      default:
        return 'low'
    }
  }

  // Show browser notification
  const showBrowserNotification = (alertNotification) => {
    if (Notification.permission === 'granted') {
      const notification = new Notification('CarWise Price Alert', {
        body: alertNotification.message,
        icon: '/icons/icon-96x96.png',
        tag: `price-alert-${alertNotification.partId}`,
        badge: '/icons/icon-96x96.png',
        requireInteraction: alertNotification.priority === 'high'
      })

      notification.onclick = () => {
        window.focus()
        // Navigate to part details or open price alerts panel
        window.dispatchEvent(new CustomEvent('openPriceAlert', {
          detail: { alertId: alertNotification.alertId, partId: alertNotification.partId }
        }))
        notification.close()
      }

      // Auto close after 10 seconds for low priority alerts
      if (alertNotification.priority !== 'high') {
        setTimeout(() => notification.close(), 10000)
      }
    }
  }

  // Show in-app notification
  const showInAppNotification = (alertNotification) => {
    const notification = document.createElement('div')
    notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg text-white text-sm font-medium transition-all duration-300 transform translate-x-full`
    
    // Set color based on priority
    const colors = {
      'high': 'bg-red-500',
      'medium': 'bg-orange-500',
      'low': 'bg-blue-500'
    }
    notification.className += ` ${colors[alertNotification.priority] || colors.low}`
    
    notification.innerHTML = `
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="font-semibold mb-1">Price Alert</div>
          <div class="text-sm opacity-90">${alertNotification.message}</div>
        </div>
        <button class="ml-2 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    `
    
    document.body.appendChild(notification)
    
    // Animate in
    setTimeout(() => {
      notification.classList.remove('translate-x-full')
    }, 100)
    
    // Auto remove after 8 seconds
    setTimeout(() => {
      notification.classList.add('translate-x-full')
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification)
        }
      }, 300)
    }, 8000)
  }

  // Get current price for a part
  const getCurrentPrice = (partId) => {
    const history = priceHistory.get(partId)
    return history && history.length > 0 ? history[history.length - 1].price : null
  }

  // Get price history for a part
  const getPriceHistory = (partId, days = 7) => {
    const history = priceHistory.get(partId) || []
    const cutoffDate = new Date(Date.now() - days * 24 * 60 * 60 * 1000)
    return history.filter(entry => entry.timestamp >= cutoffDate)
  }

  // Get price trend for a part
  const getPriceTrend = (partId, days = 7) => {
    const history = getPriceHistory(partId, days)
    if (history.length < 2) return 'stable'
    
    const firstPrice = history[0].price
    const lastPrice = history[history.length - 1].price
    const changePercentage = ((lastPrice - firstPrice) / firstPrice) * 100
    
    if (changePercentage > 5) return 'increasing'
    if (changePercentage < -5) return 'decreasing'
    return 'stable'
  }

  // Mark alert notification as read
  const markAlertAsRead = (notificationId) => {
    const notification = activePriceAlerts.value.find(alert => alert.id === notificationId)
    if (notification) {
      notification.isRead = true
      saveAlertsToStorage()
    }
  }

  // Clear old notifications
  const clearOldNotifications = () => {
    const oneWeekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
    activePriceAlerts.value = activePriceAlerts.value.filter(alert => alert.timestamp > oneWeekAgo)
    saveAlertsToStorage()
  }

  // Save alerts to localStorage
  const saveAlertsToStorage = () => {
    try {
      localStorage.setItem('carwise_price_alerts', JSON.stringify(priceAlerts.value))
      localStorage.setItem('carwise_active_price_alerts', JSON.stringify(activePriceAlerts.value))
      localStorage.setItem('carwise_alert_settings', JSON.stringify(alertSettings.value))
    } catch (error) {
      console.error('Failed to save price alerts to storage:', error)
    }
  }

  // Load alerts from localStorage
  const loadAlertsFromStorage = () => {
    try {
      const savedAlerts = localStorage.getItem('carwise_price_alerts')
      const savedActiveAlerts = localStorage.getItem('carwise_active_price_alerts')
      const savedSettings = localStorage.getItem('carwise_alert_settings')
      
      if (savedAlerts) {
        priceAlerts.value = JSON.parse(savedAlerts).map(alert => ({
          ...alert,
          createdAt: new Date(alert.createdAt),
          lastChecked: alert.lastChecked ? new Date(alert.lastChecked) : null
        }))
      }
      
      if (savedActiveAlerts) {
        activePriceAlerts.value = JSON.parse(savedActiveAlerts).map(alert => ({
          ...alert,
          timestamp: new Date(alert.timestamp)
        }))
      }
      
      if (savedSettings) {
        alertSettings.value = { ...alertSettings.value, ...JSON.parse(savedSettings) }
      }
    } catch (error) {
      console.error('Failed to load price alerts from storage:', error)
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
  const unreadAlerts = computed(() => {
    return activePriceAlerts.value.filter(alert => !alert.isRead)
  })

  const highPriorityAlerts = computed(() => {
    return activePriceAlerts.value.filter(alert => alert.priority === 'high' && !alert.isRead)
  })

  const alertsCount = computed(() => {
    return {
      total: priceAlerts.value.length,
      active: priceAlerts.value.filter(alert => alert.isActive).length,
      unread: unreadAlerts.value.length,
      highPriority: highPriorityAlerts.value.length
    }
  })

  // Lifecycle
  onMounted(() => {
    loadAlertsFromStorage()
    requestNotificationPermission()
    
    // Clean up old notifications periodically
    setInterval(clearOldNotifications, 60 * 60 * 1000) // Every hour
  })

  onUnmounted(() => {
    saveAlertsToStorage()
  })

  return {
    // State
    priceAlerts,
    activePriceAlerts,
    priceHistory,
    isTrackingPrices,
    lastPriceUpdate,
    alertSettings,
    
    // Methods
    createPriceAlert,
    removePriceAlert,
    updatePartPrice,
    getCurrentPrice,
    getPriceHistory,
    getPriceTrend,
    markAlertAsRead,
    clearOldNotifications,
    requestNotificationPermission,
    
    // Computed
    unreadAlerts,
    highPriorityAlerts,
    alertsCount,
    
    // Constants
    ALERT_TYPES
  }
}

