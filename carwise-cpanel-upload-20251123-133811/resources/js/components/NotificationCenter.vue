<template>
  <div class="relative">
    <!-- Notification Bell Button -->
    <button
      @click="toggleNotificationCenter"
      class="relative bg-white dark:bg-secondary-800 text-gray-900 dark:text-white shadow-lg hover:shadow-xl rounded-full p-3 transition-all duration-200 border border-gray-200 dark:border-secondary-700"
      :class="{ 'text-primary-600 dark:text-primary-400': isNotificationCenterOpen }"
      title="View Notifications"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M15 17h5l-5 5v-5zM4.868 19.132l13.132-13.132a2.5 2.5 0 00-3.536-3.536L1.332 15.596 4.868 19.132z"></path>
      </svg>
      
      <!-- Unread Count Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
      
      <!-- Critical Alert Pulse -->
      <span
        v-if="criticalNotifications.length > 0"
        class="absolute -top-1 -right-1 bg-red-500 rounded-full h-3 w-3 animate-ping"
      ></span>
    </button>

    <!-- Notification Panel -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="isNotificationCenterOpen"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-hidden"
      >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Notifications
            </h3>
            <div class="flex items-center space-x-2">
              <!-- Settings Button -->
              <button
                @click="showSettings = !showSettings"
                class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded"
                title="Settings"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </button>
              
              <!-- Mark All Read -->
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200"
              >
                Mark all read
              </button>
              
              <!-- Clear All -->
              <button
                @click="clearAllNotifications"
                class="text-xs text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200"
              >
                Clear all
              </button>
            </div>
          </div>
          
          <!-- Filter Tabs -->
          <div class="flex space-x-1 mt-2">
            <button
              v-for="filter in filters"
              :key="filter.key"
              @click="activeFilter = filter.key"
              :class="[
                'px-3 py-1 text-xs rounded-full transition-colors',
                activeFilter === filter.key
                  ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
              ]"
            >
              {{ filter.label }}
              <span v-if="filter.count > 0" class="ml-1 font-semibold">{{ filter.count }}</span>
            </button>
          </div>
        </div>

        <!-- Settings Panel -->
        <div v-if="showSettings" class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Notification Settings</h4>
          <div class="space-y-2">
            <label class="flex items-center">
              <input
                v-model="notificationSettings.enableBrowserNotifications"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              >
              <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Browser notifications</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="notificationSettings.enableSoundNotifications"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              >
              <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Sound notifications</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="notificationSettings.autoMarkAsRead"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              >
              <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Auto-mark as read</span>
            </label>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-64 overflow-y-auto">
          <div v-if="filteredNotifications.length === 0" class="px-4 py-8 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 17h5l-5 5v-5zM4.868 19.132l13.132-13.132a2.5 2.5 0 00-3.536-3.536L1.332 15.596 4.868 19.132z"></path>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
          </div>
          
          <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
              v-for="notification in filteredNotifications"
              :key="notification.id"
              :class="[
                'px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors',
                !notification.isRead ? 'bg-blue-50 dark:bg-blue-900/20' : ''
              ]"
              @click="handleNotificationClick(notification)"
            >
              <div class="flex items-start space-x-3">
                <!-- Icon -->
                <div :class="[
                  'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm',
                  getNotificationIconClass(notification)
                ]">
                  {{ notification.icon }}
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ notification.title }}
                    </p>
                    <div class="flex items-center space-x-1">
                      <!-- Priority Indicator -->
                      <span
                        v-if="notification.priority === 'high' || notification.priority === 'critical'"
                        :class="[
                          'w-2 h-2 rounded-full',
                          notification.priority === 'critical' ? 'bg-red-500' : 'bg-orange-500'
                        ]"
                      ></span>
                      
                      <!-- Unread Indicator -->
                      <span
                        v-if="!notification.isRead"
                        class="w-2 h-2 bg-blue-500 rounded-full"
                      ></span>
                    </div>
                  </div>
                  
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ notification.message }}
                  </p>
                  
                  <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500 dark:text-gray-500">
                      {{ formatTimestamp(notification.timestamp) }}
                    </p>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                      <button
                        v-if="!notification.isRead"
                        @click.stop="markAsRead(notification.id)"
                        class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200"
                      >
                        Mark read
                      </button>
                      
                      <button
                        @click.stop="removeNotification(notification.id)"
                        class="text-xs text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                  
                  <!-- Action Buttons -->
                  <div v-if="notification.actions && notification.actions.length > 0" class="mt-2 flex space-x-2">
                    <button
                      v-for="action in notification.actions"
                      :key="action.label"
                      @click.stop="executeAction(action)"
                      class="px-2 py-1 text-xs bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors"
                    >
                      {{ action.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
          <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>{{ notifications.length }} total notifications</span>
            <button
              @click="clearOldNotifications(24)"
              class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200"
            >
              Clear old (24h+)
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Backdrop -->
    <div
      v-if="isNotificationCenterOpen"
      @click="isNotificationCenterOpen = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useNotifications } from '../composables/useNotifications'

const {
  notifications,
  isNotificationCenterOpen,
  notificationSettings,
  unreadCount,
  criticalNotifications,
  markAsRead,
  markAllAsRead,
  removeNotification,
  clearAllNotifications,
  clearOldNotifications,
  toggleNotificationCenter,
  NOTIFICATION_TYPES
} = useNotifications()

// Local state
const activeFilter = ref('all')
const showSettings = ref(false)

// Filters
const filters = computed(() => [
  {
    key: 'all',
    label: 'All',
    count: notifications.value.length
  },
  {
    key: 'unread',
    label: 'Unread',
    count: notifications.value.filter(n => !n.isRead).length
  },
  {
    key: 'price_alerts',
    label: 'Price',
    count: notifications.value.filter(n => n.type === NOTIFICATION_TYPES.PRICE_ALERT).length
  },
  {
    key: 'stock_alerts',
    label: 'Stock',
    count: notifications.value.filter(n => n.type === NOTIFICATION_TYPES.STOCK_ALERT).length
  },
  {
    key: 'critical',
    label: 'Critical',
    count: notifications.value.filter(n => n.priority === 'critical').length
  }
])

// Filtered notifications
const filteredNotifications = computed(() => {
  let filtered = notifications.value

  switch (activeFilter.value) {
    case 'unread':
      filtered = filtered.filter(n => !n.isRead)
      break
    case 'price_alerts':
      filtered = filtered.filter(n => n.type === NOTIFICATION_TYPES.PRICE_ALERT)
      break
    case 'stock_alerts':
      filtered = filtered.filter(n => n.type === NOTIFICATION_TYPES.STOCK_ALERT)
      break
    case 'critical':
      filtered = filtered.filter(n => n.priority === 'critical')
      break
  }

  return filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
})

// Methods
const handleNotificationClick = (notification) => {
  if (!notification.isRead) {
    markAsRead(notification.id)
  }
  
  if (notification.onClick) {
    notification.onClick()
  }
  
  // Close notification center
  isNotificationCenterOpen.value = false
}

const executeAction = (action) => {
  if (typeof action.handler === 'function') {
    action.handler()
  } else if (typeof action.handler === 'string') {
    // Execute as JavaScript code (be careful with this)
    try {
      eval(action.handler)
    } catch (error) {
      console.error('Error executing action:', error)
    }
  }
}

const getNotificationIconClass = (notification) => {
  const baseClasses = 'text-white'
  
  switch (notification.type) {
    case NOTIFICATION_TYPES.SUCCESS:
      return `${baseClasses} bg-green-500`
    case NOTIFICATION_TYPES.WARNING:
      return `${baseClasses} bg-orange-500`
    case NOTIFICATION_TYPES.ERROR:
      return `${baseClasses} bg-red-500`
    case NOTIFICATION_TYPES.PRICE_ALERT:
      return `${baseClasses} bg-yellow-500`
    case NOTIFICATION_TYPES.STOCK_ALERT:
      return `${baseClasses} bg-purple-500`
    case NOTIFICATION_TYPES.ORDER_UPDATE:
      return `${baseClasses} bg-indigo-500`
    case NOTIFICATION_TYPES.PROMOTION:
      return `${baseClasses} bg-pink-500`
    default:
      return `${baseClasses} bg-blue-500`
  }
}

const formatTimestamp = (timestamp) => {
  const now = new Date()
  const diff = now - new Date(timestamp)
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return new Date(timestamp).toLocaleDateString()
}

// Close settings when notification center closes
watch(isNotificationCenterOpen, (isOpen) => {
  if (!isOpen) {
    showSettings.value = false
  }
})
</script>

<style scoped>
/* Custom scrollbar for notifications list */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}
</style>
