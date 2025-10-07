<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase History</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage your orders</p>
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
              <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics.total_orders }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">Total Orders</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">${{ statistics.total_spent?.toFixed(2) || '0.00' }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">Total Spent</div>
              </div>
              <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ statistics.pending_orders }}</div>
                <div class="text-sm text-yellow-700 dark:text-yellow-300">Pending</div>
              </div>
              <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ statistics.shipped_orders }}</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">Shipped</div>
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
                  placeholder="Search orders or parts..."
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
              </div>
              <div class="flex gap-2">
                <select 
                  v-model="statusFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="refunded">Refunded</option>
                </select>
                <button 
                  @click="refreshOrders"
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
          
          <!-- Orders List -->
          <div class="flex-1 overflow-y-auto p-6">
            <div v-if="isLoading" class="text-center py-12">
              <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600 dark:text-gray-400">Loading orders...</p>
            </div>
            
            <div v-else-if="filteredOrders.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No orders found</h3>
              <p class="text-gray-600 dark:text-gray-400">You haven't placed any orders yet.</p>
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="order in filteredOrders" 
                :key="order.id"
                class="bg-gray-50 dark:bg-secondary-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ order.order_number }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatOrderDate(order.created_at) }}</p>
                  </div>
                  <div class="flex items-center space-x-4 mt-2 md:mt-0">
                    <span 
                      :class="getStatusBadgeClass(order.status)"
                      class="px-3 py-1 rounded-full text-sm font-medium"
                    >
                      {{ getOrderStatusDisplay(order.status) }}
                    </span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(order.total_amount, order.currency) }}</span>
                  </div>
                </div>
                
                <!-- Order Items -->
                <div class="mb-4">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Items ({{ order.items.length }})</h4>
                  <div class="space-y-2">
                    <div 
                      v-for="item in order.items.slice(0, 3)" 
                      :key="item.id"
                      class="flex items-center space-x-3 p-2 bg-white dark:bg-secondary-800 rounded"
                    >
                      <img 
                        :src="item.part_image_url || 'https://via.placeholder.com/50x50?text=No+Image'" 
                        :alt="item.part_name"
                        class="w-12 h-12 object-cover rounded"
                      >
                      <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.part_name }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ item.part_brand }} - Qty: {{ item.quantity }}</p>
                      </div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatCurrency(item.total_price, order.currency) }}</p>
                    </div>
                    <div v-if="order.items.length > 3" class="text-sm text-gray-600 dark:text-gray-400 text-center py-2">
                      +{{ order.items.length - 3 }} more items
                    </div>
                  </div>
                </div>
                
                <!-- Order Actions -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="viewOrderDetails(order)"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                  >
                    View Details
                  </button>
                  <button 
                    v-if="canCancelOrder(order)"
                    @click="cancelOrder(order)"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                  >
                    Cancel Order
                  </button>
                  <button 
                    v-if="order.tracking_number"
                    @click="trackOrder(order)"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                  >
                    Track Package
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
import { useOrders } from '../composables/useOrders'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'viewOrder', 'cancelOrder', 'trackOrder'])

// Orders composable
const { 
  orders, 
  orderStatistics, 
  isLoading, 
  loadOrders, 
  loadOrderStatistics,
  getOrderStatusDisplay,
  formatOrderDate,
  formatCurrency,
  canCancelOrder,
  cancelOrder: cancelOrderAction
} = useOrders()

// Local state
const searchQuery = ref('')
const statusFilter = ref('')

// Computed properties
const filteredOrders = computed(() => {
  let filtered = orders.value

  // Filter by status
  if (statusFilter.value) {
    filtered = filtered.filter(order => order.status === statusFilter.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => 
      order.order_number.toLowerCase().includes(query) ||
      order.items.some(item => 
        item.part_name.toLowerCase().includes(query) ||
        item.part_brand?.toLowerCase().includes(query)
      )
    )
  }

  return filtered
})

const statistics = computed(() => orderStatistics.value)

// Methods
const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    confirmed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
  }
  return classes[status] || classes.pending
}

const refreshOrders = async () => {
  await loadOrders()
  await loadOrderStatistics()
}

const viewOrderDetails = (order) => {
  emit('viewOrder', order)
}

const cancelOrder = async (order) => {
  if (confirm(`Are you sure you want to cancel order ${order.order_number}?`)) {
    const success = await cancelOrderAction(order.id, 'Cancelled by customer')
    if (success) {
      await refreshOrders()
      emit('cancelOrder', order)
    }
  }
}

const trackOrder = (order) => {
  emit('trackOrder', order)
}

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    refreshOrders()
  }
})

// Load data on mount
onMounted(() => {
  if (props.isOpen) {
    refreshOrders()
  }
})
</script>


