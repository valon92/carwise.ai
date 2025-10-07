import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'

export function useNotifications() {
  // State
  const notifications = ref([])
  const isNotificationCenterOpen = ref(false)
  const notificationSettings = ref({
    enableBrowserNotifications: true,
    enableInAppNotifications: true,
    enableSoundNotifications: true,
    enableEmailNotifications: false,
    enablePushNotifications: false,
    autoMarkAsRead: false,
    notificationDuration: 5000, // 5 seconds
    maxNotifications: 50,
    enabledCategories: {
      price_alerts: true,
      stock_alerts: true,
      order_updates: true,
      system_updates: true,
      promotions: true,
      maintenance: true,
      security: true
    }
  })

  // Notification types and priorities
  const NOTIFICATION_TYPES = {
    PRICE_ALERT: 'price_alert',
    STOCK_ALERT: 'stock_alert',
    ORDER_UPDATE: 'order_update',
    SYSTEM_UPDATE: 'system_update',
    PROMOTION: 'promotion',
    MAINTENANCE: 'maintenance',
    SECURITY: 'security',
    SUCCESS: 'success',
    WARNING: 'warning',
    ERROR: 'error',
    INFO: 'info'
  }

  const NOTIFICATION_PRIORITIES = {
    LOW: 'low',
    MEDIUM: 'medium',
    HIGH: 'high',
    CRITICAL: 'critical'
  }

  // Notification counter
  let notificationIdCounter = 0

  // Sound effects
  const sounds = {
    success: '/sounds/success.mp3',
    warning: '/sounds/warning.mp3',
    error: '/sounds/error.mp3',
    info: '/sounds/info.mp3'
  }

  // Create a new notification
  const createNotification = (options) => {
    const notification = {
      id: `notification_${Date.now()}_${++notificationIdCounter}`,
      title: options.title || 'Notification',
      message: options.message || '',
      type: options.type || NOTIFICATION_TYPES.INFO,
      priority: options.priority || NOTIFICATION_PRIORITIES.MEDIUM,
      category: options.category || 'general',
      timestamp: new Date(),
      isRead: false,
      isSticky: options.isSticky || false,
      duration: options.duration || notificationSettings.value.notificationDuration,
      actions: options.actions || [],
      data: options.data || {},
      icon: options.icon || getDefaultIcon(options.type),
      color: options.color || getDefaultColor(options.type),
      sound: options.sound || getDefaultSound(options.type),
      onClick: options.onClick || null,
      onClose: options.onClose || null
    }

    // Check if category is enabled
    if (!notificationSettings.value.enabledCategories[notification.category]) {
      return null
    }

    // Add to notifications array
    notifications.value.unshift(notification)

    // Limit number of notifications
    if (notifications.value.length > notificationSettings.value.maxNotifications) {
      notifications.value = notifications.value.slice(0, notificationSettings.value.maxNotifications)
    }

    // Show notification
    if (notificationSettings.value.enableInAppNotifications) {
      showInAppNotification(notification)
    }

    if (notificationSettings.value.enableBrowserNotifications) {
      showBrowserNotification(notification)
    }

    if (notificationSettings.value.enableSoundNotifications && notification.sound) {
      playNotificationSound(notification.sound)
    }

    // Auto-remove non-sticky notifications
    if (!notification.isSticky && notification.duration > 0) {
      setTimeout(() => {
        removeNotification(notification.id)
      }, notification.duration)
    }

    // Auto-mark as read if enabled
    if (notificationSettings.value.autoMarkAsRead) {
      setTimeout(() => {
        markAsRead(notification.id)
      }, notification.duration / 2)
    }

    // Save to storage
    saveNotificationsToStorage()

    return notification
  }

  // Show in-app notification
  const showInAppNotification = (notification) => {
    const container = getOrCreateNotificationContainer()
    const element = createNotificationElement(notification)
    
    container.appendChild(element)

    // Animate in
    setTimeout(() => {
      element.classList.add('notification-enter')
    }, 100)

    // Auto-remove if not sticky
    if (!notification.isSticky) {
      setTimeout(() => {
        removeNotificationElement(element)
      }, notification.duration)
    }
  }

  // Create notification DOM element
  const createNotificationElement = (notification) => {
    const element = document.createElement('div')
    element.id = `notification-${notification.id}`
    element.className = `notification-toast ${notification.type} ${notification.priority}`
    
    const priorityColors = {
      low: 'bg-gray-500',
      medium: 'bg-blue-500',
      high: 'bg-orange-500',
      critical: 'bg-red-500'
    }

    const typeIcons = {
      success: '✓',
      warning: '⚠',
      error: '✕',
      info: 'ℹ'
    }

    element.innerHTML = `
      <div class="notification-content ${priorityColors[notification.priority]} text-white rounded-lg shadow-lg p-4 mb-2 max-w-sm transform translate-x-full transition-all duration-300 ease-out">
        <div class="flex items-start justify-between">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <span class="text-lg">${notification.icon || typeIcons[notification.type] || 'ℹ'}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-semibold text-sm">${notification.title}</div>
              <div class="text-sm opacity-90 mt-1">${notification.message}</div>
              <div class="text-xs opacity-75 mt-2">${notification.timestamp.toLocaleTimeString()}</div>
            </div>
          </div>
          <button class="notification-close ml-2 text-white hover:text-gray-200 focus:outline-none" onclick="this.closest('.notification-toast').remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        ${notification.actions.length > 0 ? `
          <div class="mt-3 flex space-x-2">
            ${notification.actions.map(action => `
              <button class="px-3 py-1 text-xs bg-white bg-opacity-20 rounded hover:bg-opacity-30 transition-colors" 
                      onclick="${action.handler}">
                ${action.label}
              </button>
            `).join('')}
          </div>
        ` : ''}
      </div>
    `

    // Add click handler
    if (notification.onClick) {
      element.addEventListener('click', notification.onClick)
    }

    return element
  }

  // Get or create notification container
  const getOrCreateNotificationContainer = () => {
    let container = document.getElementById('notification-container')
    if (!container) {
      container = document.createElement('div')
      container.id = 'notification-container'
      container.className = 'fixed top-4 right-4 z-50 pointer-events-none'
      container.style.cssText = 'max-height: 80vh; overflow-y: auto;'
      document.body.appendChild(container)
    }
    return container
  }

  // Remove notification element
  const removeNotificationElement = (element) => {
    if (element && element.parentNode) {
      element.classList.add('notification-exit')
      setTimeout(() => {
        if (element.parentNode) {
          element.parentNode.removeChild(element)
        }
      }, 300)
    }
  }

  // Show browser notification
  const showBrowserNotification = (notification) => {
    if ('Notification' in window && Notification.permission === 'granted') {
      const browserNotification = new Notification(notification.title, {
        body: notification.message,
        icon: '/icons/icon-96x96.png',
        badge: '/icons/icon-96x96.png',
        tag: `carwise-${notification.type}-${notification.id}`,
        requireInteraction: notification.priority === NOTIFICATION_PRIORITIES.CRITICAL,
        silent: !notificationSettings.value.enableSoundNotifications,
        data: notification.data
      })

      browserNotification.onclick = () => {
        window.focus()
        if (notification.onClick) {
          notification.onClick()
        }
        browserNotification.close()
      }

      // Auto close for non-critical notifications
      if (notification.priority !== NOTIFICATION_PRIORITIES.CRITICAL) {
        setTimeout(() => browserNotification.close(), notification.duration)
      }
    }
  }

  // Play notification sound
  const playNotificationSound = (soundUrl) => {
    try {
      const audio = new Audio(soundUrl)
      audio.volume = 0.5
      audio.play().catch(error => {
        console.warn('Could not play notification sound:', error)
      })
    } catch (error) {
      console.warn('Error playing notification sound:', error)
    }
  }

  // Get default icon for notification type
  const getDefaultIcon = (type) => {
    const icons = {
      [NOTIFICATION_TYPES.SUCCESS]: '✓',
      [NOTIFICATION_TYPES.WARNING]: '⚠',
      [NOTIFICATION_TYPES.ERROR]: '✕',
      [NOTIFICATION_TYPES.INFO]: 'ℹ',
      [NOTIFICATION_TYPES.PRICE_ALERT]: '💰',
      [NOTIFICATION_TYPES.STOCK_ALERT]: '📦',
      [NOTIFICATION_TYPES.ORDER_UPDATE]: '🛒',
      [NOTIFICATION_TYPES.PROMOTION]: '🏷️',
      [NOTIFICATION_TYPES.MAINTENANCE]: '🔧',
      [NOTIFICATION_TYPES.SECURITY]: '🔒'
    }
    return icons[type] || 'ℹ'
  }

  // Get default color for notification type
  const getDefaultColor = (type) => {
    const colors = {
      [NOTIFICATION_TYPES.SUCCESS]: 'green',
      [NOTIFICATION_TYPES.WARNING]: 'orange',
      [NOTIFICATION_TYPES.ERROR]: 'red',
      [NOTIFICATION_TYPES.INFO]: 'blue',
      [NOTIFICATION_TYPES.PRICE_ALERT]: 'yellow',
      [NOTIFICATION_TYPES.STOCK_ALERT]: 'purple',
      [NOTIFICATION_TYPES.ORDER_UPDATE]: 'indigo',
      [NOTIFICATION_TYPES.PROMOTION]: 'pink',
      [NOTIFICATION_TYPES.MAINTENANCE]: 'gray',
      [NOTIFICATION_TYPES.SECURITY]: 'red'
    }
    return colors[type] || 'blue'
  }

  // Get default sound for notification type
  const getDefaultSound = (type) => {
    const soundMap = {
      [NOTIFICATION_TYPES.SUCCESS]: sounds.success,
      [NOTIFICATION_TYPES.WARNING]: sounds.warning,
      [NOTIFICATION_TYPES.ERROR]: sounds.error,
      [NOTIFICATION_TYPES.INFO]: sounds.info
    }
    return soundMap[type] || sounds.info
  }

  // Mark notification as read
  const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
      notification.isRead = true
      saveNotificationsToStorage()
    }
  }

  // Mark all notifications as read
  const markAllAsRead = () => {
    notifications.value.forEach(notification => {
      notification.isRead = true
    })
    saveNotificationsToStorage()
  }

  // Remove notification
  const removeNotification = (notificationId) => {
    const index = notifications.value.findIndex(n => n.id === notificationId)
    if (index !== -1) {
      const notification = notifications.value[index]
      notifications.value.splice(index, 1)
      
      // Remove DOM element if exists
      const element = document.getElementById(`notification-${notificationId}`)
      if (element) {
        removeNotificationElement(element)
      }

      // Call onClose callback
      if (notification.onClose) {
        notification.onClose()
      }

      saveNotificationsToStorage()
    }
  }

  // Clear all notifications
  const clearAllNotifications = () => {
    notifications.value.forEach(notification => {
      if (notification.onClose) {
        notification.onClose()
      }
    })
    notifications.value = []
    
    // Clear DOM elements
    const container = document.getElementById('notification-container')
    if (container) {
      container.innerHTML = ''
    }
    
    saveNotificationsToStorage()
  }

  // Clear old notifications
  const clearOldNotifications = (olderThanHours = 24) => {
    const cutoffTime = new Date(Date.now() - olderThanHours * 60 * 60 * 1000)
    const oldNotifications = notifications.value.filter(n => n.timestamp < cutoffTime)
    
    oldNotifications.forEach(notification => {
      removeNotification(notification.id)
    })
  }

  // Request notification permission
  const requestNotificationPermission = async () => {
    if ('Notification' in window) {
      if (Notification.permission === 'default') {
        const permission = await Notification.requestPermission()
        return permission === 'granted'
      }
      return Notification.permission === 'granted'
    }
    return false
  }

  // Toggle notification center
  const toggleNotificationCenter = () => {
    isNotificationCenterOpen.value = !isNotificationCenterOpen.value
  }

  // Save notifications to localStorage
  const saveNotificationsToStorage = () => {
    try {
      const dataToSave = {
        notifications: notifications.value.slice(0, 20), // Save only last 20
        settings: notificationSettings.value
      }
      localStorage.setItem('carwise_notifications', JSON.stringify(dataToSave))
    } catch (error) {
      console.error('Failed to save notifications to storage:', error)
    }
  }

  // Load notifications from localStorage
  const loadNotificationsFromStorage = () => {
    try {
      const saved = localStorage.getItem('carwise_notifications')
      if (saved) {
        const data = JSON.parse(saved)
        if (data.notifications) {
          notifications.value = data.notifications.map(n => ({
            ...n,
            timestamp: new Date(n.timestamp)
          }))
        }
        if (data.settings) {
          notificationSettings.value = { ...notificationSettings.value, ...data.settings }
        }
      }
    } catch (error) {
      console.error('Failed to load notifications from storage:', error)
    }
  }

  // Computed properties
  const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.isRead)
  })

  const unreadCount = computed(() => {
    return unreadNotifications.value.length
  })

  const notificationsByType = computed(() => {
    const grouped = {}
    notifications.value.forEach(notification => {
      if (!grouped[notification.type]) {
        grouped[notification.type] = []
      }
      grouped[notification.type].push(notification)
    })
    return grouped
  })

  const criticalNotifications = computed(() => {
    return notifications.value.filter(n => n.priority === NOTIFICATION_PRIORITIES.CRITICAL && !n.isRead)
  })

  // Predefined notification creators
  const showSuccess = (title, message, options = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.SUCCESS,
      priority: NOTIFICATION_PRIORITIES.LOW,
      ...options
    })
  }

  const showWarning = (title, message, options = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.WARNING,
      priority: NOTIFICATION_PRIORITIES.MEDIUM,
      ...options
    })
  }

  const showError = (title, message, options = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.ERROR,
      priority: NOTIFICATION_PRIORITIES.HIGH,
      isSticky: true,
      ...options
    })
  }

  const showInfo = (title, message, options = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.INFO,
      priority: NOTIFICATION_PRIORITIES.LOW,
      ...options
    })
  }

  const showPriceAlert = (title, message, data = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.PRICE_ALERT,
      category: 'price_alerts',
      priority: NOTIFICATION_PRIORITIES.MEDIUM,
      data,
      actions: [
        {
          label: 'View Part',
          handler: `window.location.href='/parts/${data.partId}'`
        }
      ]
    })
  }

  const showStockAlert = (title, message, data = {}) => {
    return createNotification({
      title,
      message,
      type: NOTIFICATION_TYPES.STOCK_ALERT,
      category: 'stock_alerts',
      priority: data.critical ? NOTIFICATION_PRIORITIES.HIGH : NOTIFICATION_PRIORITIES.MEDIUM,
      data
    })
  }

  // Lifecycle
  onMounted(() => {
    loadNotificationsFromStorage()
    requestNotificationPermission()
    
    // Clean up old notifications periodically
    setInterval(() => {
      clearOldNotifications(24) // Clear notifications older than 24 hours
    }, 60 * 60 * 1000) // Check every hour

    // Add CSS for animations
    addNotificationStyles()
  })

  onUnmounted(() => {
    saveNotificationsToStorage()
  })

  // Add notification styles
  const addNotificationStyles = () => {
    if (document.getElementById('notification-styles')) return

    const style = document.createElement('style')
    style.id = 'notification-styles'
    style.textContent = `
      .notification-toast {
        pointer-events: auto;
      }
      
      .notification-content {
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
      }
      
      .notification-enter .notification-content {
        transform: translateX(0);
      }
      
      .notification-exit .notification-content {
        transform: translateX(100%);
        opacity: 0;
      }
      
      .notification-close {
        transition: opacity 0.2s ease;
      }
      
      .notification-close:hover {
        opacity: 0.7;
      }
    `
    document.head.appendChild(style)
  }

  return {
    // State
    notifications,
    isNotificationCenterOpen,
    notificationSettings,
    
    // Methods
    createNotification,
    markAsRead,
    markAllAsRead,
    removeNotification,
    clearAllNotifications,
    clearOldNotifications,
    toggleNotificationCenter,
    requestNotificationPermission,
    
    // Convenience methods
    showSuccess,
    showWarning,
    showError,
    showInfo,
    showPriceAlert,
    showStockAlert,
    
    // Computed
    unreadNotifications,
    unreadCount,
    notificationsByType,
    criticalNotifications,
    
    // Constants
    NOTIFICATION_TYPES,
    NOTIFICATION_PRIORITIES
  }
}

