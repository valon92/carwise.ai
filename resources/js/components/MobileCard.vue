<template>
  <div
    class="mobile-card relative overflow-hidden transition-all duration-200 ease-out"
    :class="[
      cardClasses,
      {
        'transform scale-95': isPressed && !disabled,
        'cursor-not-allowed opacity-50': disabled,
        'shadow-lg hover:shadow-xl': !flat && !disabled
      }
    ]"
    @touchstart="handleTouchStart"
    @touchend="handleTouchEnd"
    @touchcancel="handleTouchEnd"
    @mousedown="handleTouchStart"
    @mouseup="handleTouchEnd"
    @mouseleave="handleTouchEnd"
    @click="handleClick"
  >
    <!-- Card Header -->
    <div v-if="$slots.header || title" class="card-header p-4 pb-2">
      <slot name="header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
          <div v-if="badge" class="flex items-center space-x-2">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="getBadgeClasses(badgeVariant)"
            >
              {{ badge }}
            </span>
          </div>
        </div>
        <p v-if="subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ subtitle }}</p>
      </slot>
    </div>

    <!-- Card Image -->
    <div v-if="$slots.image || image" class="card-image relative overflow-hidden">
      <slot name="image">
        <img
          :src="image"
          :alt="imageAlt || title"
          class="w-full h-48 object-cover"
          :class="{ 'h-32': compact, 'h-64': !compact }"
          loading="lazy"
        >
        
        <!-- Image Overlay -->
        <div v-if="imageOverlay" class="absolute inset-0 bg-black bg-opacity-40 flex items-end">
          <div class="p-4 text-white">
            <h4 class="font-semibold">{{ imageOverlay.title }}</h4>
            <p v-if="imageOverlay.subtitle" class="text-sm opacity-90">{{ imageOverlay.subtitle }}</p>
          </div>
        </div>
        
        <!-- Favorite Button -->
        <TouchButton
          v-if="showFavorite"
          variant="ghost"
          size="sm"
          @click.stop="toggleFavorite"
          class="absolute top-2 right-2 p-2 bg-white bg-opacity-90 rounded-full"
        >
          <svg 
            class="w-5 h-5" 
            :class="isFavorite ? 'text-red-500 fill-current' : 'text-gray-600'"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
          </svg>
        </TouchButton>
      </slot>
    </div>

    <!-- Card Content -->
    <div class="card-content p-4" :class="{ 'pt-2': $slots.header || title }">
      <slot>
        <div v-if="content" class="text-gray-700 dark:text-gray-300">
          {{ content }}
        </div>
      </slot>
    </div>

    <!-- Card Actions -->
    <div v-if="$slots.actions || actions.length > 0" class="card-actions p-4 pt-0">
      <slot name="actions">
        <div class="flex items-center justify-between space-x-3">
          <div class="flex space-x-2">
            <TouchButton
              v-for="action in primaryActions"
              :key="action.label"
              :variant="action.variant || 'primary'"
              :size="action.size || 'sm'"
              :disabled="action.disabled"
              :loading="action.loading"
              @click.stop="action.handler"
              class="flex-1"
            >
              {{ action.label }}
            </TouchButton>
          </div>
          
          <div v-if="secondaryActions.length > 0" class="flex space-x-1">
            <TouchButton
              v-for="action in secondaryActions"
              :key="action.label"
              variant="ghost"
              size="sm"
              :disabled="action.disabled"
              @click.stop="action.handler"
              class="p-2"
              :title="action.label"
            >
              <component :is="action.icon" class="w-4 h-4" />
            </TouchButton>
          </div>
        </div>
      </slot>
    </div>

    <!-- Loading Overlay -->
    <div
      v-if="loading"
      class="absolute inset-0 bg-white bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 flex items-center justify-center"
    >
      <div class="flex flex-col items-center space-y-2">
        <svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm text-gray-600 dark:text-gray-400">{{ loadingText }}</span>
      </div>
    </div>

    <!-- Touch Feedback Overlay -->
    <div
      class="absolute inset-0 bg-black transition-opacity duration-150 pointer-events-none"
      :class="isPressed && !disabled ? 'opacity-5' : 'opacity-0'"
    ></div>

    <!-- Ripple Effect -->
    <span
      v-for="ripple in ripples"
      :key="ripple.id"
      class="absolute rounded-full bg-black bg-opacity-20 pointer-events-none animate-ping"
      :style="ripple.style"
    ></span>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useMobileLayout } from '../composables/useMobileLayout'
import TouchButton from './TouchButton.vue'

const props = defineProps({
  // Content
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  content: {
    type: String,
    default: ''
  },
  image: {
    type: String,
    default: ''
  },
  imageAlt: {
    type: String,
    default: ''
  },
  imageOverlay: {
    type: Object,
    default: null
  },
  
  // Badge
  badge: {
    type: [String, Number],
    default: null
  },
  badgeVariant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'].includes(value)
  },
  
  // Actions
  actions: {
    type: Array,
    default: () => []
  },
  
  // Appearance
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'outlined', 'elevated', 'filled'].includes(value)
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
  loadingText: {
    type: String,
    default: 'Loading...'
  },
  
  // Layout
  compact: {
    type: Boolean,
    default: false
  },
  flat: {
    type: Boolean,
    default: false
  },
  
  // Interactive
  clickable: {
    type: Boolean,
    default: true
  },
  rippleEffect: {
    type: Boolean,
    default: true
  },
  
  // Favorite
  showFavorite: {
    type: Boolean,
    default: false
  },
  isFavorite: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click', 'favorite-toggle'])

// Mobile layout
const { isMobileDevice, getCardWidth } = useMobileLayout()

// Component state
const isPressed = ref(false)
const ripples = ref([])

// Computed properties
const cardClasses = computed(() => {
  const classes = ['bg-white', 'dark:bg-gray-800', 'rounded-lg', 'border', 'border-gray-200', 'dark:border-gray-700']
  
  // Variant classes
  switch (props.variant) {
    case 'outlined':
      classes.push('border-2', 'border-primary-200', 'dark:border-primary-800')
      break
    case 'elevated':
      classes.push('shadow-lg', 'border-0')
      break
    case 'filled':
      classes.push('bg-primary-50', 'dark:bg-primary-900', 'border-primary-200', 'dark:border-primary-800')
      break
  }
  
  // Mobile-specific classes
  if (isMobileDevice.value) {
    classes.push('touch-manipulation')
  }
  
  // Clickable
  if (props.clickable && !props.disabled) {
    classes.push('cursor-pointer')
  }
  
  // Responsive width
  classes.push(getCardWidth())
  
  return classes.join(' ')
})

const primaryActions = computed(() => {
  return props.actions.filter(action => action.primary !== false)
})

const secondaryActions = computed(() => {
  return props.actions.filter(action => action.primary === false && action.icon)
})

// Badge classes
const getBadgeClasses = (variant) => {
  const variants = {
    primary: 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200',
    secondary: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    danger: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    info: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
  }
  return variants[variant] || variants.primary
}

// Touch handlers
const handleTouchStart = () => {
  if (props.disabled || !props.clickable) return
  isPressed.value = true
}

const handleTouchEnd = () => {
  isPressed.value = false
}

// Click handler
const handleClick = (event) => {
  if (props.disabled || !props.clickable) return
  
  // Create ripple effect
  if (props.rippleEffect) {
    createRipple(event)
  }
  
  // Haptic feedback
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
  
  emit('click', event)
}

// Favorite toggle
const toggleFavorite = () => {
  emit('favorite-toggle', !props.isFavorite)
  
  // Haptic feedback
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
}

// Ripple effect
const createRipple = (event) => {
  const card = event.currentTarget
  const rect = card.getBoundingClientRect()
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
</script>

<style scoped>
/* Touch manipulation for better mobile performance */
.touch-manipulation {
  touch-action: manipulation;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
}

.transition-opacity {
  transition-property: opacity;
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

/* Loading spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Card sections */
.card-header {
  border-bottom: 1px solid theme('colors.gray.200');
}

.dark .card-header {
  border-bottom-color: theme('colors.gray.700');
}

.card-image + .card-content {
  padding-top: 1rem;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .mobile-card {
    margin-bottom: 1rem;
  }
  
  .card-actions {
    padding: 0.75rem;
  }
  
  .card-actions .flex {
    flex-direction: column;
    space-y: 0.5rem;
  }
  
  .card-actions .flex > div {
    width: 100%;
  }
}
</style>

