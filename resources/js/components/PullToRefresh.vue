<template>
  <div 
    ref="refreshContainer"
    class="relative overflow-hidden"
    :class="containerClass"
  >
    <!-- Pull to Refresh Indicator -->
    <div 
      class="absolute top-0 left-0 right-0 flex items-center justify-center transition-all duration-300 ease-out z-10"
      :style="indicatorStyle"
    >
      <div 
        class="bg-white dark:bg-gray-800 rounded-full shadow-lg border border-gray-200 dark:border-gray-700 p-3 flex items-center space-x-2"
        :class="{ 'animate-pulse': isRefreshing }"
      >
        <!-- Refresh Icon -->
        <div class="relative">
          <svg 
            class="w-5 h-5 text-primary-600 dark:text-primary-400 transition-transform duration-300"
            :class="{ 
              'animate-spin': isRefreshing,
              'rotate-180': canRefresh && !isRefreshing 
            }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            ></path>
          </svg>
          
          <!-- Progress Ring -->
          <svg 
            v-if="!isRefreshing"
            class="absolute inset-0 w-5 h-5 transform -rotate-90"
            viewBox="0 0 24 24"
          >
            <circle
              cx="12"
              cy="12"
              r="8"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              class="text-primary-200 dark:text-primary-800"
            />
            <circle
              cx="12"
              cy="12"
              r="8"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              class="text-primary-600 dark:text-primary-400"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="progressOffset"
              stroke-linecap="round"
            />
          </svg>
        </div>
        
        <!-- Status Text -->
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ statusText }}
        </span>
      </div>
    </div>

    <!-- Content -->
    <div 
      class="transition-transform duration-300 ease-out"
      :style="contentStyle"
    >
      <slot></slot>
    </div>

    <!-- Pull Instruction (for first-time users) -->
    <div 
      v-if="showInstructions && !hasUsedPullRefresh"
      class="absolute top-4 left-0 right-0 flex justify-center z-20 pointer-events-none"
    >
      <div class="bg-primary-600 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium animate-bounce">
        Pull down to refresh
        <svg class="w-4 h-4 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useTouch } from '../composables/useTouch'

const props = defineProps({
  // Refresh callback
  onRefresh: {
    type: Function,
    required: true
  },
  
  // Configuration
  threshold: {
    type: Number,
    default: 80
  },
  maxPull: {
    type: Number,
    default: 120
  },
  
  // Visual options
  showInstructions: {
    type: Boolean,
    default: true
  },
  containerClass: {
    type: String,
    default: ''
  },
  
  // Behavior
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['refresh-start', 'refresh-end', 'pull-start', 'pull-end'])

// Refs
const refreshContainer = ref(null)
const hasUsedPullRefresh = ref(false)

// Touch composable
const {
  addTouchListeners,
  removeTouchListeners,
  setCallback,
  setPullRefresh,
  isPulling,
  pullDistance,
  isRefreshing
} = useTouch()

// State
const startY = ref(0)
const currentY = ref(0)
const isPullingDown = ref(false)

// Configure pull to refresh
setPullRefresh(true, props.threshold)

// Progress calculation
const circumference = 2 * Math.PI * 8 // radius = 8
const progress = computed(() => {
  if (isRefreshing.value) return 100
  return Math.min((pullDistance.value / props.threshold) * 100, 100)
})

const progressOffset = computed(() => {
  return circumference - (progress.value / 100) * circumference
})

// Status text
const statusText = computed(() => {
  if (isRefreshing.value) return 'Refreshing...'
  if (canRefresh.value) return 'Release to refresh'
  if (isPulling.value) return 'Pull to refresh'
  return 'Pull down'
})

// Can refresh state
const canRefresh = computed(() => {
  return pullDistance.value >= props.threshold && !isRefreshing.value
})

// Indicator positioning
const indicatorStyle = computed(() => {
  const translateY = Math.min(pullDistance.value - 40, props.maxPull - 40)
  const opacity = Math.min(pullDistance.value / 40, 1)
  
  return {
    transform: `translateY(${Math.max(translateY, -40)}px)`,
    opacity: isPulling.value || isRefreshing.value ? opacity : 0
  }
})

// Content positioning
const contentStyle = computed(() => {
  if (props.disabled) return {}
  
  const translateY = Math.min(pullDistance.value * 0.5, props.maxPull * 0.5)
  return {
    transform: isPulling.value || isRefreshing.value ? `translateY(${translateY}px)` : 'translateY(0)'
  }
})

// Handle touch start
const handleTouchStart = (event) => {
  if (props.disabled || window.scrollY > 0) return
  
  startY.value = event.touches[0].clientY
  isPullingDown.value = false
}

// Handle touch move
const handleTouchMove = (event) => {
  if (props.disabled || window.scrollY > 0) return
  
  currentY.value = event.touches[0].clientY
  const deltaY = currentY.value - startY.value
  
  if (deltaY > 0 && window.scrollY === 0) {
    isPullingDown.value = true
    emit('pull-start')
    
    // Prevent default scroll behavior when pulling
    event.preventDefault()
  }
}

// Handle pull refresh
const handlePullRefresh = async () => {
  if (props.disabled) return
  
  hasUsedPullRefresh.value = true
  localStorage.setItem('carwise_pull_refresh_used', 'true')
  
  emit('refresh-start')
  
  try {
    await props.onRefresh()
  } catch (error) {
    console.error('Refresh error:', error)
  } finally {
    emit('refresh-end')
  }
}

// Handle touch end
const handleTouchEnd = () => {
  if (isPullingDown.value) {
    isPullingDown.value = false
    emit('pull-end')
  }
}

// Watch for scroll position to disable pull refresh
const handleScroll = () => {
  if (window.scrollY > 0 && isPulling.value) {
    // Reset pull state if user scrolls down
    isPullingDown.value = false
  }
}

// Setup
onMounted(() => {
  if (refreshContainer.value) {
    // Add touch listeners
    addTouchListeners(refreshContainer.value)
    
    // Set pull refresh callback
    setCallback('onPullRefresh', handlePullRefresh)
    
    // Add scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true })
    
    // Check if user has used pull refresh before
    hasUsedPullRefresh.value = localStorage.getItem('carwise_pull_refresh_used') === 'true'
  }
})

// Cleanup
onUnmounted(() => {
  if (refreshContainer.value) {
    removeTouchListeners(refreshContainer.value)
  }
  window.removeEventListener('scroll', handleScroll)
})

// Watch for refresh state changes
watch(isRefreshing, (newValue) => {
  if (newValue) {
    // Haptic feedback on mobile
    if (navigator.vibrate) {
      navigator.vibrate([50, 50, 50])
    }
  }
})

// Expose methods
defineExpose({
  refresh: handlePullRefresh,
  resetInstructions: () => {
    hasUsedPullRefresh.value = false
    localStorage.removeItem('carwise_pull_refresh_used')
  }
})
</script>

<style scoped>
/* Smooth animations */
.transition-transform {
  transition-property: transform, opacity;
}

/* Bounce animation for instructions */
@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce {
  animation: bounce 2s infinite;
}

/* Spin animation for refresh icon */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Pulse animation for refresh indicator */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

