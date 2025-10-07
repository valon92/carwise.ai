<template>
  <button
    ref="buttonRef"
    :type="type"
    :disabled="disabled || loading"
    class="relative overflow-hidden transition-all duration-200 ease-out focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="[
      buttonClasses,
      {
        'transform scale-95': isPressed && !disabled,
        'cursor-not-allowed opacity-50': disabled,
        'cursor-wait': loading
      }
    ]"
    @click="handleClick"
  >
    <!-- Ripple Effect -->
    <span
      v-for="ripple in ripples"
      :key="ripple.id"
      class="absolute rounded-full bg-white bg-opacity-30 pointer-events-none animate-ping"
      :style="ripple.style"
    ></span>

    <!-- Loading Spinner -->
    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center"
    >
      <svg
        class="animate-spin h-5 w-5 text-current"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <!-- Button Content -->
    <span
      class="relative flex items-center justify-center space-x-2 transition-opacity duration-200"
      :class="{ 'opacity-0': loading }"
    >
      <!-- Icon -->
      <span v-if="icon" class="flex-shrink-0">
        <component :is="icon" class="w-5 h-5" />
      </span>
      
      <!-- Text -->
      <span v-if="$slots.default || text" class="font-medium">
        <slot>{{ text }}</slot>
      </span>
      
      <!-- Badge -->
      <span
        v-if="badge"
        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full"
      >
        {{ badge }}
      </span>
    </span>

    <!-- Long Press Progress -->
    <div
      v-if="longPress && isLongPressing"
      class="absolute bottom-0 left-0 h-1 bg-white bg-opacity-50 transition-all duration-75 ease-linear"
      :style="{ width: `${longPressProgress}%` }"
    ></div>

    <!-- Touch Feedback Overlay -->
    <div
      class="absolute inset-0 bg-black transition-opacity duration-150"
      :class="[
        isPressed && !disabled ? 'opacity-10' : 'opacity-0'
      ]"
    ></div>
  </button>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useTouch } from '../composables/useTouch'

const props = defineProps({
  // Button type
  type: {
    type: String,
    default: 'button'
  },
  
  // Variants
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'ghost', 'outline'].includes(value)
  },
  
  // Sizes
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  // Content
  text: {
    type: String,
    default: ''
  },
  icon: {
    type: [String, Object],
    default: null
  },
  badge: {
    type: [String, Number],
    default: null
  },
  
  // States
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  
  // Touch behavior
  hapticFeedback: {
    type: Boolean,
    default: true
  },
  rippleEffect: {
    type: Boolean,
    default: true
  },
  longPress: {
    type: Boolean,
    default: false
  },
  longPressDuration: {
    type: Number,
    default: 1000
  },
  
  // Styling
  rounded: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click', 'long-press', 'press-start', 'press-end'])

// Refs
const buttonRef = ref(null)
const isPressed = ref(false)
const isLongPressing = ref(false)
const longPressProgress = ref(0)
const ripples = ref([])

// Touch composable
const { addTouchListeners, removeTouchListeners, setCallback, setLongPress } = useTouch()

// Long press timer
let longPressTimer = null
let longPressInterval = null

// Button classes
const buttonClasses = computed(() => {
  const classes = []
  
  // Base classes
  classes.push('inline-flex', 'items-center', 'justify-center', 'font-medium', 'transition-all', 'duration-200')
  
  // Size classes
  const sizeClasses = {
    xs: 'px-2 py-1 text-xs min-h-[32px]',
    sm: 'px-3 py-2 text-sm min-h-[36px]',
    md: 'px-4 py-2 text-base min-h-[44px]', // 44px is Apple's recommended minimum touch target
    lg: 'px-6 py-3 text-lg min-h-[48px]',
    xl: 'px-8 py-4 text-xl min-h-[56px]'
  }
  classes.push(sizeClasses[props.size])
  
  // Variant classes
  const variantClasses = {
    primary: 'bg-primary-600 hover:bg-primary-700 text-white shadow-lg hover:shadow-xl focus:ring-primary-500',
    secondary: 'bg-gray-600 hover:bg-gray-700 text-white shadow-lg hover:shadow-xl focus:ring-gray-500',
    success: 'bg-green-600 hover:bg-green-700 text-white shadow-lg hover:shadow-xl focus:ring-green-500',
    warning: 'bg-yellow-600 hover:bg-yellow-700 text-white shadow-lg hover:shadow-xl focus:ring-yellow-500',
    danger: 'bg-red-600 hover:bg-red-700 text-white shadow-lg hover:shadow-xl focus:ring-red-500',
    ghost: 'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-gray-500',
    outline: 'border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white focus:ring-primary-500'
  }
  classes.push(variantClasses[props.variant])
  
  // Rounded classes
  if (props.rounded) {
    classes.push('rounded-full')
  } else {
    classes.push('rounded-lg')
  }
  
  // Full width
  if (props.fullWidth) {
    classes.push('w-full')
  }
  
  // Touch-friendly spacing
  classes.push('touch-manipulation', 'select-none')
  
  return classes.join(' ')
})

// Handle click
const handleClick = (event) => {
  if (props.disabled || props.loading) return
  
  // Haptic feedback
  if (props.hapticFeedback && navigator.vibrate) {
    navigator.vibrate(50)
  }
  
  // Ripple effect
  if (props.rippleEffect) {
    createRipple(event)
  }
  
  emit('click', event)
}

// Create ripple effect
const createRipple = (event) => {
  const button = buttonRef.value
  if (!button) return
  
  const rect = button.getBoundingClientRect()
  const size = Math.max(rect.width, rect.height)
  const x = event.clientX - rect.left - size / 2
  const y = event.clientY - rect.top - size / 2
  
  const ripple = {
    id: Date.now(),
    style: {
      left: `${x}px`,
      top: `${y}px`,
      width: `${size}px`,
      height: `${size}px`
    }
  }
  
  ripples.value.push(ripple)
  
  // Remove ripple after animation
  setTimeout(() => {
    const index = ripples.value.findIndex(r => r.id === ripple.id)
    if (index > -1) {
      ripples.value.splice(index, 1)
    }
  }, 600)
}

// Handle touch start
const handleTouchStart = () => {
  if (props.disabled || props.loading) return
  
  isPressed.value = true
  emit('press-start')
  
  // Start long press if enabled
  if (props.longPress) {
    isLongPressing.value = true
    longPressProgress.value = 0
    
    longPressTimer = setTimeout(() => {
      if (isLongPressing.value) {
        emit('long-press')
        
        // Stronger haptic feedback for long press
        if (props.hapticFeedback && navigator.vibrate) {
          navigator.vibrate([100, 50, 100])
        }
      }
    }, props.longPressDuration)
    
    // Update progress
    const startTime = Date.now()
    longPressInterval = setInterval(() => {
      const elapsed = Date.now() - startTime
      longPressProgress.value = Math.min((elapsed / props.longPressDuration) * 100, 100)
      
      if (elapsed >= props.longPressDuration) {
        clearInterval(longPressInterval)
      }
    }, 16) // ~60fps
  }
}

// Handle touch end
const handleTouchEnd = () => {
  isPressed.value = false
  isLongPressing.value = false
  longPressProgress.value = 0
  
  if (longPressTimer) {
    clearTimeout(longPressTimer)
    longPressTimer = null
  }
  
  if (longPressInterval) {
    clearInterval(longPressInterval)
    longPressInterval = null
  }
  
  emit('press-end')
}

// Setup touch listeners
onMounted(() => {
  if (buttonRef.value) {
    // Configure long press if enabled
    if (props.longPress) {
      setLongPress(true, props.longPressDuration)
      setCallback('onLongPress', () => emit('long-press'))
    }
    
    // Add custom touch listeners for press feedback
    buttonRef.value.addEventListener('touchstart', handleTouchStart, { passive: true })
    buttonRef.value.addEventListener('touchend', handleTouchEnd, { passive: true })
    buttonRef.value.addEventListener('touchcancel', handleTouchEnd, { passive: true })
    
    // Mouse events for desktop
    buttonRef.value.addEventListener('mousedown', handleTouchStart)
    buttonRef.value.addEventListener('mouseup', handleTouchEnd)
    buttonRef.value.addEventListener('mouseleave', handleTouchEnd)
  }
})

// Cleanup
onUnmounted(() => {
  if (buttonRef.value) {
    buttonRef.value.removeEventListener('touchstart', handleTouchStart)
    buttonRef.value.removeEventListener('touchend', handleTouchEnd)
    buttonRef.value.removeEventListener('touchcancel', handleTouchEnd)
    buttonRef.value.removeEventListener('mousedown', handleTouchStart)
    buttonRef.value.removeEventListener('mouseup', handleTouchEnd)
    buttonRef.value.removeEventListener('mouseleave', handleTouchEnd)
  }
  
  if (longPressTimer) {
    clearTimeout(longPressTimer)
  }
  
  if (longPressInterval) {
    clearInterval(longPressInterval)
  }
})
</script>

<style scoped>
/* Touch manipulation for better mobile performance */
.touch-manipulation {
  touch-action: manipulation;
}

/* Ripple animation */
@keyframes ping {
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ping {
  animation: ping 0.6s cubic-bezier(0, 0, 0.2, 1);
}

/* Spin animation for loading */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Ensure minimum touch target size on mobile */
@media (max-width: 768px) {
  button {
    min-height: 44px;
    min-width: 44px;
  }
}
</style>

