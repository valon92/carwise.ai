<template>
  <TouchButton
    variant="ghost"
    size="md"
    :haptic-feedback="true"
    @click="handleClick"
    class="w-full justify-start px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200"
    :class="[
      active ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : 'text-gray-700 dark:text-gray-300',
      customClass
    ]"
  >
    <div class="flex items-center justify-between w-full">
      <div class="flex items-center space-x-3">
        <!-- Icon -->
        <div class="flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(icon)"></path>
          </svg>
        </div>
        
        <!-- Label -->
        <span class="font-medium">{{ label }}</span>
      </div>
      
      <!-- Badge or Arrow -->
      <div class="flex items-center space-x-2">
        <!-- Badge -->
        <span
          v-if="badge && badge > 0"
          class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[20px]"
        >
          {{ badge > 99 ? '99+' : badge }}
        </span>
        
        <!-- Arrow (for items with submenus) -->
        <svg
          v-if="hasSubmenu"
          class="w-4 h-4 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </div>
    </div>
  </TouchButton>
</template>

<script setup>
import { computed } from 'vue'
import TouchButton from './TouchButton.vue'

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  badge: {
    type: [Number, String],
    default: null
  },
  hasSubmenu: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['click'])

// Get icon SVG path based on icon name
const getIconPath = (iconName) => {
  const icons = {
    home: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
    car: "M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10",
    diagnostic: "M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0-8h10a2 2 0 012 2v6a2 2 0 01-2 2H9m0-8v8",
    garage: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4",
    mechanic: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
    orders: "M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z",
    wishlist: "M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z",
    settings: "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z",
    help: "M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
    login: "M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1",
    register: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z",
    logout: "M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
  }
  return icons[iconName] || icons.home
}

const handleClick = () => {
  emit('click')
  
  // Haptic feedback
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
}
</script>

<style scoped>
/* Ensure proper touch target size */
.w-full {
  min-height: 48px;
}

/* Smooth transitions */
.transition-colors {
  transition-property: color, background-color, border-color;
}

/* Active state styling */
.bg-primary-50 {
  position: relative;
}

/* Badge styling */
.min-w-[20px] {
  min-width: 20px;
}
</style>
