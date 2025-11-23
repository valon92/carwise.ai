<template>
  <!-- Notification Container -->
  <div class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full">
    <TransitionGroup
      name="notification"
      tag="div"
      class="space-y-3"
    >
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="notification-item"
        :class="getNotificationClasses(notification.type)"
        @click="removeNotification(notification.id)"
      >
        <div class="flex items-start space-x-3">
          <!-- Icon -->
          <div class="flex-shrink-0">
            <svg 
              class="w-5 h-5" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                v-if="notification.type === 'success'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
              <path 
                v-else-if="notification.type === 'error'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
              <path 
                v-else-if="notification.type === 'warning'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
              />
              <path 
                v-else-if="notification.type === 'info'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-medium" :class="getTitleClasses(notification.type)">
              {{ notification.title }}
            </h4>
            <p class="text-sm mt-1" :class="getMessageClasses(notification.type)">
              {{ notification.message }}
            </p>
          </div>

          <!-- Close Button -->
          <div class="flex-shrink-0">
            <button
              @click.stop="removeNotification(notification.id)"
              class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-gray-600 dark:focus:text-gray-300 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Progress Bar -->
        <div 
          v-if="notification.duration > 0"
          class="absolute bottom-0 left-0 h-1 bg-current opacity-30 transition-all duration-100 ease-linear"
          :style="{ width: `${notification.progress}%` }"
        ></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'NotificationSystem',
  setup() {
    const notifications = ref([])
    const timers = new Map()

    const getNotificationClasses = (type) => {
      const baseClasses = 'relative overflow-hidden rounded-lg shadow-lg border backdrop-blur-sm cursor-pointer transform transition-all duration-300 hover:scale-105'
      
      switch (type) {
        case 'success':
          return `${baseClasses} bg-success-50 dark:bg-success-900/80 border-success-200 dark:border-success-700 text-success-800 dark:text-success-200`
        case 'error':
          return `${baseClasses} bg-danger-50 dark:bg-danger-900/80 border-danger-200 dark:border-danger-700 text-danger-800 dark:text-danger-200`
        case 'warning':
          return `${baseClasses} bg-warning-50 dark:bg-warning-900/80 border-warning-200 dark:border-warning-700 text-warning-800 dark:text-warning-200`
        case 'info':
          return `${baseClasses} bg-info-50 dark:bg-info-900/80 border-info-200 dark:border-info-700 text-info-800 dark:text-info-200`
        default:
          return `${baseClasses} bg-white dark:bg-secondary-800 border-secondary-200 dark:border-secondary-700 text-secondary-800 dark:text-secondary-200`
      }
    }

    const getTitleClasses = (type) => {
      switch (type) {
        case 'success':
          return 'text-success-900 dark:text-success-100'
        case 'error':
          return 'text-danger-900 dark:text-danger-100'
        case 'warning':
          return 'text-warning-900 dark:text-warning-100'
        case 'info':
          return 'text-info-900 dark:text-info-100'
        default:
          return 'text-secondary-900 dark:text-secondary-100'
      }
    }

    const getMessageClasses = (type) => {
      switch (type) {
        case 'success':
          return 'text-success-700 dark:text-success-300'
        case 'error':
          return 'text-danger-700 dark:text-danger-300'
        case 'warning':
          return 'text-warning-700 dark:text-warning-300'
        case 'info':
          return 'text-info-700 dark:text-info-300'
        default:
          return 'text-secondary-700 dark:text-secondary-300'
      }
    }

    const addNotification = (notification) => {
      const id = Date.now() + Math.random()
      const duration = notification.duration || 5000
      
      const newNotification = {
        id,
        type: notification.type || 'info',
        title: notification.title || 'Notification',
        message: notification.message || '',
        duration,
        progress: 100,
        timestamp: Date.now()
      }

      notifications.value.push(newNotification)

      // Auto-remove notification after duration
      if (duration > 0) {
        const timer = setInterval(() => {
          const notificationIndex = notifications.value.findIndex(n => n.id === id)
          if (notificationIndex !== -1) {
            const notification = notifications.value[notificationIndex]
            const elapsed = Date.now() - notification.timestamp
            const remaining = Math.max(0, duration - elapsed)
            notification.progress = (remaining / duration) * 100

            if (remaining <= 0) {
              removeNotification(id)
            }
          }
        }, 50)

        timers.set(id, timer)
      }

      return id
    }

    const removeNotification = (id) => {
      const index = notifications.value.findIndex(n => n.id === id)
      if (index !== -1) {
        notifications.value.splice(index, 1)
      }

      // Clear timer
      const timer = timers.get(id)
      if (timer) {
        clearInterval(timer)
        timers.delete(id)
      }
    }

    const clearAll = () => {
      notifications.value.forEach(notification => {
        const timer = timers.get(notification.id)
        if (timer) {
          clearInterval(timer)
        }
      })
      timers.clear()
      notifications.value = []
    }

    // Global notification methods
    const success = (title, message, duration = 5000) => {
      return addNotification({ type: 'success', title, message, duration })
    }

    const error = (title, message, duration = 7000) => {
      return addNotification({ type: 'error', title, message, duration })
    }

    const warning = (title, message, duration = 6000) => {
      return addNotification({ type: 'warning', title, message, duration })
    }

    const info = (title, message, duration = 5000) => {
      return addNotification({ type: 'info', title, message, duration })
    }

    // Expose methods globally
    onMounted(() => {
      window.$notify = {
        success,
        error,
        warning,
        info,
        add: addNotification,
        remove: removeNotification,
        clear: clearAll
      }
    })

    onUnmounted(() => {
      // Clean up timers
      timers.forEach(timer => clearInterval(timer))
      timers.clear()
      
      // Remove global methods
      if (window.$notify) {
        delete window.$notify
      }
    })

    return {
      notifications,
      getNotificationClasses,
      getTitleClasses,
      getMessageClasses,
      removeNotification,
      addNotification,
      success,
      error,
      warning,
      info,
      clearAll
    }
  }
}
</script>

<style scoped>
.notification-item {
  @apply p-4;
}

/* Notification animations */
.notification-enter-active {
  transition: all 0.3s ease-out;
}

.notification-leave-active {
  transition: all 0.3s ease-in;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.notification-move {
  transition: transform 0.3s ease;
}

/* Hover effects */
.notification-item:hover {
  @apply shadow-xl;
}

/* Mobile responsiveness */
@media (max-width: 640px) {
  .notification-item {
    @apply mx-4;
  }
}
</style>
