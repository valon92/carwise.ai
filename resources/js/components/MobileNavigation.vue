<template>
  <div class="mobile-navigation">
    <!-- Mobile Header -->
    <header 
      class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm"
      :style="{ paddingTop: `${safeAreaTop}px` }"
    >
      <div class="flex items-center justify-between h-16 px-4">
        <!-- Left Section -->
        <div class="flex items-center space-x-3">
          <!-- Menu Button -->
          <TouchButton
            variant="ghost"
            size="sm"
            :haptic-feedback="true"
            @click="toggleMenu"
            class="p-2"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </TouchButton>
          
          <!-- Logo -->
          <div class="flex items-center">
            <img src="/favicon.ico" alt="CarWise" class="w-8 h-8 mr-2">
            <span class="font-bold text-lg text-primary-600 dark:text-primary-400">CarWise</span>
          </div>
        </div>
        
        <!-- Center Section - Search (on larger mobile screens) -->
        <div v-if="!isSmallMobile" class="flex-1 max-w-md mx-4">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search parts..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-full bg-gray-50 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              @focus="showSearchSuggestions = true"
            >
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center space-x-2">
          <!-- Search Button (small screens) -->
          <TouchButton
            v-if="isSmallMobile"
            variant="ghost"
            size="sm"
            @click="toggleSearch"
            class="p-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </TouchButton>
          
          <!-- Notifications -->
          <TouchButton
            variant="ghost"
            size="sm"
            @click="toggleNotifications"
            class="p-2 relative"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 19.132l13.132-13.132a2.5 2.5 0 00-3.536-3.536L1.332 15.596 4.868 19.132z"></path>
            </svg>
            <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ notificationCount > 99 ? '99+' : notificationCount }}
            </span>
          </TouchButton>
          
          <!-- Cart -->
          <TouchButton
            variant="ghost"
            size="sm"
            @click="toggleCart"
            class="p-2 relative"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartCount > 99 ? '99+' : cartCount }}
            </span>
          </TouchButton>
        </div>
      </div>
      
      <!-- Expandable Search Bar (small screens) -->
      <div 
        v-if="isSmallMobile && showSearchBar"
        class="px-4 pb-3 border-t border-gray-200 dark:border-gray-700"
      >
        <div class="relative">
          <input
            ref="mobileSearchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Search car parts..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-base focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            @blur="hideSearchBar"
          >
          <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </div>
    </header>
    
    <!-- Mobile Menu Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showMenu"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="closeMenu"
      ></div>
    </Transition>
    
    <!-- Mobile Menu Sidebar -->
    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-if="showMenu"
        class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-white dark:bg-gray-900 z-50 overflow-y-auto"
        :style="{ paddingTop: `${safeAreaTop}px`, paddingBottom: `${safeAreaBottom}px` }"
      >
        <!-- Menu Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <img src="/favicon.ico" alt="CarWise" class="w-8 h-8 mr-3">
            <div>
              <h2 class="font-bold text-lg text-gray-900 dark:text-white">CarWise</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">AI Car Diagnostics</p>
            </div>
          </div>
          <TouchButton
            variant="ghost"
            size="sm"
            @click="closeMenu"
            class="p-2"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </TouchButton>
        </div>
        
        <!-- User Profile (if authenticated) -->
        <div v-if="isAuthenticated" class="p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
              <span class="text-primary-600 dark:text-primary-400 font-semibold text-lg">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </span>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ user?.name || 'User' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || 'user@example.com' }}</p>
            </div>
          </div>
        </div>
        
        <!-- Menu Items -->
        <nav class="py-4">
          <div class="space-y-1">
            <!-- Main Navigation -->
            <MobileMenuItem
              icon="home"
              label="Home"
              @click="navigateTo('/')"
              :active="currentRoute === '/'"
            />
            
            <MobileMenuItem
              icon="car"
              label="Car Parts"
              @click="navigateTo('/parts')"
              :active="currentRoute === '/parts'"
              :badge="partsCount"
            />
            
            <MobileMenuItem
              icon="diagnostic"
              label="AI Diagnosis"
              @click="navigateTo('/diagnose')"
              :active="currentRoute === '/diagnose'"
            />
            
            <MobileMenuItem
              icon="garage"
              label="My Cars"
              @click="navigateTo('/cars')"
              :active="currentRoute === '/cars'"
              :badge="carsCount"
            />
            
            <MobileMenuItem
              icon="mechanic"
              label="Find Mechanics"
              @click="navigateTo('/mechanics')"
              :active="currentRoute === '/mechanics'"
            />
            
            <!-- Divider -->
            <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>
            
            <!-- Secondary Navigation -->
            <MobileMenuItem
              v-if="isAuthenticated"
              icon="orders"
              label="My Orders"
              @click="navigateTo('/orders')"
              :badge="ordersCount"
            />
            
            <MobileMenuItem
              v-if="isAuthenticated"
              icon="wishlist"
              label="Wishlist"
              @click="navigateTo('/wishlist')"
              :badge="wishlistCount"
            />
            
            <MobileMenuItem
              icon="settings"
              label="Settings"
              @click="navigateTo('/settings')"
            />
            
            <MobileMenuItem
              icon="help"
              label="Help & Support"
              @click="navigateTo('/help')"
            />
            
            <!-- Divider -->
            <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>
            
            <!-- Authentication -->
            <MobileMenuItem
              v-if="!isAuthenticated"
              icon="login"
              label="Sign In"
              @click="handleLogin"
            />
            
            <MobileMenuItem
              v-if="!isAuthenticated"
              icon="register"
              label="Sign Up"
              @click="handleRegister"
            />
            
            <MobileMenuItem
              v-if="isAuthenticated"
              icon="logout"
              label="Sign Out"
              @click="handleLogout"
              class="text-red-600 dark:text-red-400"
            />
          </div>
        </nav>
        
        <!-- App Info -->
        <div class="mt-auto p-4 border-t border-gray-200 dark:border-gray-700">
          <div class="text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">CarWise AI v1.0.0</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">© 2024 CarWise. All rights reserved.</p>
          </div>
        </div>
      </div>
    </Transition>
    
    <!-- Bottom Navigation (for very small screens) -->
    <nav 
      v-if="isSmallMobile"
      class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-40"
      :style="{ paddingBottom: `${safeAreaBottom}px` }"
    >
      <div class="flex items-center justify-around py-2">
        <TouchButton
          variant="ghost"
          size="sm"
          @click="navigateTo('/')"
          class="flex flex-col items-center py-2 px-3"
          :class="{ 'text-primary-600 dark:text-primary-400': currentRoute === '/' }"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span class="text-xs">Home</span>
        </TouchButton>
        
        <TouchButton
          variant="ghost"
          size="sm"
          @click="navigateTo('/parts')"
          class="flex flex-col items-center py-2 px-3 relative"
          :class="{ 'text-primary-600 dark:text-primary-400': currentRoute === '/parts' }"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span class="text-xs">Parts</span>
        </TouchButton>
        
        <TouchButton
          variant="ghost"
          size="sm"
          @click="navigateTo('/diagnose')"
          class="flex flex-col items-center py-2 px-3"
          :class="{ 'text-primary-600 dark:text-primary-400': currentRoute === '/diagnose' }"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0-8h10a2 2 0 012 2v6a2 2 0 01-2 2H9m0-8v8"></path>
          </svg>
          <span class="text-xs">Diagnose</span>
        </TouchButton>
        
        <TouchButton
          variant="ghost"
          size="sm"
          @click="navigateTo('/cars')"
          class="flex flex-col items-center py-2 px-3"
          :class="{ 'text-primary-600 dark:text-primary-400': currentRoute === '/cars' }"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          <span class="text-xs">Cars</span>
        </TouchButton>
        
        <TouchButton
          variant="ghost"
          size="sm"
          @click="toggleMenu"
          class="flex flex-col items-center py-2 px-3"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          <span class="text-xs">Menu</span>
        </TouchButton>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue'
import { useMobileLayout } from '../composables/useMobileLayout'
import TouchButton from './TouchButton.vue'
import MobileMenuItem from './MobileMenuItem.vue'

const props = defineProps({
  isAuthenticated: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  },
  cartCount: {
    type: Number,
    default: 0
  },
  notificationCount: {
    type: Number,
    default: 0
  },
  partsCount: {
    type: Number,
    default: 0
  },
  carsCount: {
    type: Number,
    default: 0
  },
  ordersCount: {
    type: Number,
    default: 0
  },
  wishlistCount: {
    type: Number,
    default: 0
  },
  currentRoute: {
    type: String,
    default: '/'
  }
})

const emit = defineEmits([
  'toggle-menu',
  'toggle-cart',
  'toggle-notifications',
  'navigate',
  'login',
  'register',
  'logout',
  'search'
])

// Mobile layout composable
const { 
  isSmallMobile, 
  safeAreaTop, 
  safeAreaBottom 
} = useMobileLayout()

// Component state
const showMenu = ref(false)
const showSearchBar = ref(false)
const showSearchSuggestions = ref(false)
const searchQuery = ref('')
const mobileSearchInput = ref(null)

// Menu methods
const toggleMenu = () => {
  showMenu.value = !showMenu.value
  emit('toggle-menu', showMenu.value)
  
  // Haptic feedback
  if (navigator.vibrate) {
    navigator.vibrate(50)
  }
}

const closeMenu = () => {
  showMenu.value = false
  emit('toggle-menu', false)
}

// Search methods
const toggleSearch = async () => {
  showSearchBar.value = !showSearchBar.value
  
  if (showSearchBar.value) {
    await nextTick()
    mobileSearchInput.value?.focus()
  }
}

const hideSearchBar = () => {
  setTimeout(() => {
    showSearchBar.value = false
  }, 150)
}

// Navigation methods
const navigateTo = (route) => {
  emit('navigate', route)
  closeMenu()
}

const toggleCart = () => {
  emit('toggle-cart')
}

const toggleNotifications = () => {
  emit('toggle-notifications')
}

// Authentication methods
const handleLogin = () => {
  emit('login')
  closeMenu()
}

const handleRegister = () => {
  emit('register')
  closeMenu()
}

const handleLogout = () => {
  emit('logout')
  closeMenu()
}

// Search handling
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    emit('search', searchQuery.value.trim())
    showSearchSuggestions.value = false
  }
}

// Watch search query
watch(() => searchQuery.value, (newValue) => {
  if (newValue.trim()) {
    emit('search', newValue.trim())
  }
})

// Prevent body scroll when menu is open
watch(showMenu, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  // Ensure body scroll is reset
  document.body.style.overflow = ''
})
</script>

<style scoped>
/* Ensure proper z-index stacking */
.mobile-navigation {
  position: relative;
}

/* Smooth transitions */
.transition-transform {
  transition-property: transform;
}

.transition-opacity {
  transition-property: opacity;
}

/* Safe area handling */
@supports (padding-top: env(safe-area-inset-top)) {
  .mobile-navigation header {
    padding-top: env(safe-area-inset-top);
  }
  
  .mobile-navigation nav {
    padding-bottom: env(safe-area-inset-bottom);
  }
}
</style>

