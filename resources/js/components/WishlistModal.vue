<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Wishlist</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Save parts you want to buy later</p>
            </div>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Statistics -->
          <div v-if="statistics" class="p-6 border-b border-gray-200 dark:border-secondary-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ statistics.high_priority_items }}</div>
                <div class="text-sm text-red-700 dark:text-red-300">High Priority</div>
              </div>
              <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ statistics.medium_priority_items }}</div>
                <div class="text-sm text-yellow-700 dark:text-yellow-300">Medium Priority</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics.low_priority_items }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">Low Priority</div>
              </div>
              <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics.items_with_alerts }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">Price Alerts</div>
              </div>
            </div>
          </div>
          
          <!-- Filters and Search -->
          <div class="p-6 border-b border-gray-200 dark:border-secondary-700">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1">
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Search wishlist items..."
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
              </div>
              <div class="flex gap-2">
                <select 
                  v-model="priorityFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Priorities</option>
                  <option value="high">High Priority</option>
                  <option value="medium">Medium Priority</option>
                  <option value="low">Low Priority</option>
                </select>
                <select 
                  v-model="categoryFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Categories</option>
                  <option v-for="(count, category) in statistics?.categories" :key="category" :value="category">
                    {{ category }} ({{ count }})
                  </option>
                </select>
                <button 
                  @click="refreshWishlist"
                  :disabled="isLoading"
                  class="px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white rounded-lg transition-colors duration-200"
                >
                  <svg v-if="isLoading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Wishlist Items -->
          <div class="flex-1 overflow-y-auto p-6">
            <div v-if="isLoading" class="text-center py-12">
              <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600 dark:text-gray-400">Loading wishlist...</p>
            </div>
            
            <div v-else-if="filteredWishlist.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Your wishlist is empty</h3>
              <p class="text-gray-600 dark:text-gray-400">Add parts you want to buy later by clicking the heart icon.</p>
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="item in filteredWishlist" 
                :key="item.id"
                class="bg-gray-50 dark:bg-secondary-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                  <div class="flex items-start space-x-4">
                    <img 
                      :src="item.part_image_url || 'https://via.placeholder.com/100x100?text=No+Image'" 
                      :alt="item.part_name"
                      class="w-20 h-20 object-cover rounded-lg"
                    >
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ item.part_name }}</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ item.part_brand }}</p>
                      <p v-if="item.part_number" class="text-xs text-gray-500 dark:text-gray-500">Part #: {{ item.part_number }}</p>
                      <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mt-2">{{ formatCurrency(item.part_price, item.part_currency) }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <span 
                      :class="getPriorityBadgeClass(item.priority)"
                      class="px-3 py-1 rounded-full text-sm font-medium"
                    >
                      {{ getPriorityDisplay(item.priority) }}
                    </span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(item.added_at) }}</span>
                  </div>
                </div>
                
                <!-- Item Notes -->
                <div v-if="item.notes" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                  <p class="text-sm text-blue-700 dark:text-blue-300">
                    <strong>Notes:</strong> {{ item.notes }}
                  </p>
                </div>
                
                <!-- Price Alert -->
                <div v-if="item.notification_enabled && item.price_alert_threshold" class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                  <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    <strong>Price Alert:</strong> Notify when price drops below {{ formatCurrency(item.price_alert_threshold, item.part_currency) }}
                  </p>
                </div>
                
                <!-- Item Actions -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="moveToCart(item)"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    Add to Cart
                  </button>
                  <button 
                    @click="editItem(item)"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                  </button>
                  <button 
                    @click="removeFromWishlist(item)"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useWishlist } from '../composables/useWishlist'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'moveToCart', 'editItem', 'removeItem'])

// Wishlist composable
const { 
  wishlist, 
  wishlistStatistics, 
  isLoading, 
  loadWishlist, 
  loadWishlistStatistics,
  removeFromWishlist: removeFromWishlistAction,
  moveToCart: moveToCartAction,
  getPriorityBadgeColor,
  getPriorityDisplay,
  formatCurrency,
  formatDate
} = useWishlist()

// Local state
const searchQuery = ref('')
const priorityFilter = ref('')
const categoryFilter = ref('')

// Computed properties
const filteredWishlist = computed(() => {
  let filtered = wishlist.value

  // Filter by priority
  if (priorityFilter.value) {
    filtered = filtered.filter(item => item.priority === priorityFilter.value)
  }

  // Filter by category
  if (categoryFilter.value) {
    filtered = filtered.filter(item => item.part_category === categoryFilter.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => 
      item.part_name.toLowerCase().includes(query) ||
      item.part_brand?.toLowerCase().includes(query) ||
      item.part_number?.toLowerCase().includes(query)
    )
  }

  return filtered
})

const statistics = computed(() => wishlistStatistics.value)

// Methods
const getPriorityBadgeClass = (priority) => {
  const classes = {
    high: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    low: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
  }
  return classes[priority] || classes.medium
}

const refreshWishlist = async () => {
  await loadWishlist()
  await loadWishlistStatistics()
}

const moveToCart = async (item) => {
  const cartItem = await moveToCartAction(item.id)
  if (cartItem) {
    emit('moveToCart', cartItem)
  }
}

const editItem = (item) => {
  emit('editItem', item)
}

const removeFromWishlist = async (item) => {
  if (confirm(`Are you sure you want to remove "${item.part_name}" from your wishlist?`)) {
    const success = await removeFromWishlistAction(item.id)
    if (success) {
      await refreshWishlist()
      emit('removeItem', item)
    }
  }
}

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    refreshWishlist()
  }
})

// Load data on mount
onMounted(() => {
  if (props.isOpen) {
    refreshWishlist()
  }
})
</script>


