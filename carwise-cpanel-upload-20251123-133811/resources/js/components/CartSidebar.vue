<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute right-0 top-0 h-full w-96 bg-white dark:bg-secondary-800 shadow-xl">
      <div class="flex flex-col h-full">
          <!-- Cart Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <div class="flex-1">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Shopping Cart</h2>
              <div v-if="isAuthenticated && user" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Welcome, {{ user.first_name || user.name }}!
              </div>
            </div>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Authentication Section -->
          <div v-if="!isAuthenticated" class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-secondary-700">
            <div class="text-center">
              <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                Sign in to save your cart and get personalized recommendations
              </p>
              <button 
                @click="$emit('login')"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
              >
                Sign In
              </button>
            </div>
          </div>
          
          <div v-else class="p-4 bg-green-50 dark:bg-green-900/20 border-b border-gray-200 dark:border-secondary-700">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm text-green-700 dark:text-green-300">Signed in as {{ user.first_name || user.name }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <button 
                  @click="$emit('openPreferences')"
                  class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
                  title="Cart Preferences"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('openPurchaseHistory')"
                  class="text-sm text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200"
                  title="Purchase History"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('openWishlist')"
                  class="text-sm text-pink-600 hover:text-pink-800 dark:text-pink-400 dark:hover:text-pink-200"
                  title="Wishlist"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('openSearchHistory')"
                  class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200"
                  title="Search History"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('openSavedSearches')"
                  class="text-sm text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200"
                  title="Saved Searches"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('logout')"
                  class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                >
                  Sign Out
                </button>
              </div>
            </div>
          </div>
        
        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="cart.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
            <p class="text-gray-600 dark:text-gray-400">Add some car parts to get started!</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="item in cart" :key="item.id" class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
              <img :src="item.image_url" :alt="item.name" class="w-16 h-16 object-cover rounded-lg">
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ item.name }}</h4>
                <p class="text-gray-600 dark:text-gray-400 text-xs">{{ item.brand }} - {{ item.part_number }}</p>
                <p class="text-primary-600 dark:text-primary-400 font-bold">{{ item.formatted_price }}</p>
              </div>
              <div class="flex items-center space-x-2">
                <button @click="$emit('updateQuantity', item.id, item.quantity - 1)" class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                  </svg>
                </button>
                <span class="w-8 text-center font-semibold text-gray-900 dark:text-white">{{ item.quantity }}</span>
                <button @click="$emit('updateQuantity', item.id, item.quantity + 1)" class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                </button>
              </div>
              <button @click="$emit('removeItem', item.id)" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Cart Footer -->
        <div v-if="cart.length > 0" class="border-t border-gray-200 dark:border-secondary-700 p-6">
          <div class="flex justify-between items-center mb-4">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
            <span class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ formattedTotal }}</span>
          </div>
          <div class="space-y-2">
            <button @click="$emit('checkout')" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
              Proceed to Checkout
            </button>
            <button @click="$emit('clearCart')" class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
              Clear Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  cart: {
    type: Array,
    default: () => []
  },
  total: {
    type: Number,
    default: 0
  },
  isAuthenticated: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'updateQuantity', 'removeItem', 'checkout', 'clearCart', 'login', 'logout', 'openPreferences', 'openPurchaseHistory', 'openWishlist', 'openSearchHistory', 'openSavedSearches'])

const formattedTotal = computed(() => {
  return `$${props.total.toFixed(2)}`
})
</script>
