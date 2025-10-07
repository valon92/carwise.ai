import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global orders state
const orders = ref([])
const orderStatistics = ref({})
const isLoading = ref(false)
const currentOrder = ref(null)

export function useOrders() {
  const { user, isAuthenticated } = useAuth()

  // Load user's order history
  const loadOrders = async (params = {}) => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/orders?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          orders.value = data.data
          return {
            orders: data.data,
            pagination: data.pagination
          }
        }
      }
      return { orders: [], pagination: null }
    } catch (error) {
      console.error('Error loading orders:', error)
      return { orders: [], pagination: null }
    } finally {
      isLoading.value = false
    }
  }

  // Load order statistics
  const loadOrderStatistics = async () => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch('/api/orders/statistics', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          orderStatistics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading order statistics:', error)
    }
  }

  // Get a specific order
  const getOrder = async (orderId) => {
    if (!isAuthenticated.value) return null

    try {
      const response = await fetch(`/api/orders/${orderId}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          currentOrder.value = data.data
          return data.data
        }
      }
      return null
    } catch (error) {
      console.error('Error loading order:', error)
      return null
    }
  }

  // Create a new order from cart
  const createOrder = async (orderData) => {
    if (!isAuthenticated.value) return null

    try {
      isLoading.value = true
      const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          // Add to orders list
          orders.value.unshift(data.data)
          return data.data
        }
      }
      return null
    } catch (error) {
      console.error('Error creating order:', error)
      return null
    } finally {
      isLoading.value = false
    }
  }

  // Cancel an order
  const cancelOrder = async (orderId, reason = '') => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/orders/${orderId}/cancel`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          // Update order in list
          const index = orders.value.findIndex(order => order.id === orderId)
          if (index !== -1) {
            orders.value[index] = data.data
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error cancelling order:', error)
      return false
    }
  }

  // Get order status badge color
  const getOrderStatusColor = (status) => {
    const colors = {
      pending: 'yellow',
      confirmed: 'blue',
      shipped: 'purple',
      delivered: 'green',
      cancelled: 'red',
      refunded: 'gray'
    }
    return colors[status] || 'gray'
  }

  // Get order status display name
  const getOrderStatusDisplay = (status) => {
    const displays = {
      pending: 'Pending',
      confirmed: 'Confirmed',
      shipped: 'Shipped',
      delivered: 'Delivered',
      cancelled: 'Cancelled',
      refunded: 'Refunded'
    }
    return displays[status] || 'Unknown'
  }

  // Format order date
  const formatOrderDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  // Format currency
  const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount)
  }

  // Check if order can be cancelled
  const canCancelOrder = (order) => {
    return order && ['pending', 'confirmed'].includes(order.status)
  }

  // Check if order can be refunded
  const canRefundOrder = (order) => {
    return order && order.status === 'delivered' && !order.refunded_at
  }

  // Get order tracking info
  const getOrderTrackingInfo = (order) => {
    if (!order) return null

    return {
      status: order.status,
      trackingNumber: order.tracking_number,
      shippedAt: order.shipped_at,
      deliveredAt: order.delivered_at,
      estimatedDelivery: order.shipped_at ? 
        new Date(new Date(order.shipped_at).getTime() + 3 * 24 * 60 * 60 * 1000) : null
    }
  }

  // Computed properties
  const totalOrders = computed(() => orderStatistics.value.total_orders || 0)
  const totalSpent = computed(() => orderStatistics.value.total_spent || 0)
  const pendingOrders = computed(() => orderStatistics.value.pending_orders || 0)
  const shippedOrders = computed(() => orderStatistics.value.shipped_orders || 0)
  const deliveredOrders = computed(() => orderStatistics.value.delivered_orders || 0)
  const cancelledOrders = computed(() => orderStatistics.value.cancelled_orders || 0)
  const recentOrders = computed(() => orderStatistics.value.recent_orders || 0)

  // Get orders by status
  const getOrdersByStatus = (status) => {
    return orders.value.filter(order => order.status === status)
  }

  // Get recent orders
  const getRecentOrders = (limit = 5) => {
    return orders.value.slice(0, limit)
  }

  // Search orders
  const searchOrders = (query) => {
    if (!query) return orders.value
    return orders.value.filter(order => 
      order.order_number.toLowerCase().includes(query.toLowerCase()) ||
      order.items.some(item => 
        item.part_name.toLowerCase().includes(query.toLowerCase())
      )
    )
  }

  return {
    // State
    orders: readonly(orders),
    orderStatistics: readonly(orderStatistics),
    currentOrder: readonly(currentOrder),
    isLoading: readonly(isLoading),
    
    // Actions
    loadOrders,
    loadOrderStatistics,
    getOrder,
    createOrder,
    cancelOrder,
    
    // Utilities
    getOrderStatusColor,
    getOrderStatusDisplay,
    formatOrderDate,
    formatCurrency,
    canCancelOrder,
    canRefundOrder,
    getOrderTrackingInfo,
    getOrdersByStatus,
    getRecentOrders,
    searchOrders,
    
    // Computed
    totalOrders,
    totalSpent,
    pendingOrders,
    shippedOrders,
    deliveredOrders,
    cancelledOrders,
    recentOrders
  }
}

// Initialize orders (call this from components when needed)
export const initializeOrders = () => {
  try {
    const { loadOrders, loadOrderStatistics } = useOrders()
    loadOrders()
    loadOrderStatistics()
  } catch (error) {
    console.log('Orders initialization skipped:', error.message)
  }
}

