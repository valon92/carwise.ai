<template>
  <div 
    ref="swipeContainer"
    class="relative overflow-hidden"
    :class="containerClass"
  >
    <!-- Swipe Indicator -->
    <div 
      v-if="showIndicator && (isSwipingLeft || isSwipingRight)"
      class="absolute top-1/2 transform -translate-y-1/2 z-10 pointer-events-none"
      :class="[
        isSwipingLeft ? 'right-4' : 'left-4',
        'transition-opacity duration-200'
      ]"
    >
      <div class="bg-black bg-opacity-50 text-white p-2 rounded-full">
        <svg 
          class="w-6 h-6" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
          :class="{ 'rotate-180': isSwipingLeft }"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </div>
    </div>

    <!-- Content Container -->
    <div 
      class="transition-transform duration-300 ease-out"
      :style="contentStyle"
    >
      <slot></slot>
    </div>

    <!-- Swipe Hints (for first-time users) -->
    <div 
      v-if="showHints && !hasInteracted"
      class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center z-20 pointer-events-none"
    >
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-sm mx-4 text-center">
        <div class="flex justify-center mb-4">
          <div class="flex space-x-2">
            <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
            <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
            <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
          </div>
        </div>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Swipe to Navigate</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          Swipe left or right to navigate between sections
        </p>
        <div class="flex justify-center space-x-4">
          <div class="flex items-center text-xs text-gray-500">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
          </div>
          <div class="flex items-center text-xs text-gray-500">
            Next
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useTouch } from '../composables/useTouch'

const props = defineProps({
  // Navigation callbacks
  onSwipeLeft: {
    type: Function,
    default: null
  },
  onSwipeRight: {
    type: Function,
    default: null
  },
  
  // Visual options
  showIndicator: {
    type: Boolean,
    default: true
  },
  showHints: {
    type: Boolean,
    default: true
  },
  
  // Styling
  containerClass: {
    type: String,
    default: ''
  },
  
  // Behavior
  swipeThreshold: {
    type: Number,
    default: 50
  },
  enableFeedback: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['swipe-left', 'swipe-right', 'swipe-start', 'swipe-end'])

// Refs
const swipeContainer = ref(null)
const hasInteracted = ref(false)

// Touch composable
const {
  addTouchListeners,
  removeTouchListeners,
  setCallback,
  configure,
  isSwiping,
  swipeDirection,
  swipeDistance,
  isSwipingLeft,
  isSwipingRight
} = useTouch()

// Visual feedback state
const swipeOffset = ref(0)
const isAnimating = ref(false)

// Configure touch behavior
configure({
  swipeThreshold: props.swipeThreshold,
  preventScroll: false,
  velocityThreshold: 0.2
})

// Content style for visual feedback
const contentStyle = computed(() => {
  if (!props.enableFeedback || isAnimating.value) {
    return {}
  }
  
  return {
    transform: `translateX(${swipeOffset.value}px)`,
    opacity: Math.max(0.7, 1 - Math.abs(swipeOffset.value) / 200)
  }
})

// Handle swipe start
const handleSwipeStart = () => {
  hasInteracted.value = true
  emit('swipe-start')
}

// Handle swipe move (for visual feedback)
const handleSwipeMove = (data) => {
  if (props.enableFeedback && isSwiping.value) {
    // Limit offset to prevent excessive movement
    const maxOffset = 100
    swipeOffset.value = Math.max(-maxOffset, Math.min(maxOffset, data.deltaX || 0))
  }
}

// Handle swipe left
const handleSwipeLeft = (data) => {
  hasInteracted.value = true
  
  // Animate back to center
  animateToCenter()
  
  // Call prop callback
  if (props.onSwipeLeft) {
    props.onSwipeLeft(data)
  }
  
  // Emit event
  emit('swipe-left', data)
  emit('swipe-end', { direction: 'left', ...data })
  
  // Haptic feedback on mobile
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
}

// Handle swipe right
const handleSwipeRight = (data) => {
  hasInteracted.value = true
  
  // Animate back to center
  animateToCenter()
  
  // Call prop callback
  if (props.onSwipeRight) {
    props.onSwipeRight(data)
  }
  
  // Emit event
  emit('swipe-right', data)
  emit('swipe-end', { direction: 'right', ...data })
  
  // Haptic feedback on mobile
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
}

// Animate content back to center
const animateToCenter = () => {
  isAnimating.value = true
  swipeOffset.value = 0
  
  setTimeout(() => {
    isAnimating.value = false
  }, 300)
}

// Hide hints after interaction
watch(hasInteracted, (newValue) => {
  if (newValue) {
    localStorage.setItem('carwise_swipe_hints_seen', 'true')
  }
})

// Setup touch listeners
onMounted(() => {
  if (swipeContainer.value) {
    addTouchListeners(swipeContainer.value)
    
    // Set callbacks
    setCallback('onSwipeLeft', handleSwipeLeft)
    setCallback('onSwipeRight', handleSwipeRight)
    
    // Check if user has seen hints before
    hasInteracted.value = localStorage.getItem('carwise_swipe_hints_seen') === 'true'
  }
})

// Cleanup
onUnmounted(() => {
  if (swipeContainer.value) {
    removeTouchListeners(swipeContainer.value)
  }
})

// Expose methods for parent components
defineExpose({
  resetHints: () => {
    hasInteracted.value = false
    localStorage.removeItem('carwise_swipe_hints_seen')
  }
})
</script>

<style scoped>
/* Smooth transitions for swipe feedback */
.transition-transform {
  transition-property: transform, opacity;
}

/* Pulse animation for hints */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

