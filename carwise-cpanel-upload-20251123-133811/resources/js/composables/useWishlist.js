import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global wishlist state
const wishlist = ref([])
const wishlistStatistics = ref({})
const isLoading = ref(false)

export function useWishlist() {
  const { user, isAuthenticated } = useAuth()

  // Load user's wishlist
  const loadWishlist = async (params = {}) => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/wishlist?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          wishlist.value = data.data
          return {
            wishlist: data.data,
            pagination: data.pagination
          }
        }
      }
      return { wishlist: [], pagination: null }
    } catch (error) {
      console.error('Error loading wishlist:', error)
      return { wishlist: [], pagination: null }
    } finally {
      isLoading.value = false
    }
  }

  // Load wishlist statistics
  const loadWishlistStatistics = async () => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch('/api/wishlist/statistics', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          wishlistStatistics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading wishlist statistics:', error)
    }
  }

  // Add item to wishlist
  const addToWishlist = async (item) => {
    if (!isAuthenticated.value) return false

    try {
      isLoading.value = true
      const response = await fetch('/api/wishlist', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          part_id: item.id,
          part_name: item.name,
          part_brand: item.brand,
          part_number: item.part_number,
          part_description: item.description,
          part_image_url: item.image_url,
          part_category: item.category,
          part_price: item.price,
          part_currency: 'USD',
          source: item.source || 'carwise',
          affiliate_url: item.affiliate_url,
          priority: 'medium',
          notification_enabled: false
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          wishlist.value.unshift(data.data)
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error adding to wishlist:', error)
      return false
    } finally {
      isLoading.value = false
    }
  }

  // Remove item from wishlist
  const removeFromWishlist = async (itemId) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/wishlist/${itemId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          const index = wishlist.value.findIndex(item => item.id === itemId)
          if (index !== -1) {
            wishlist.value.splice(index, 1)
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error removing from wishlist:', error)
      return false
    }
  }

  // Update wishlist item
  const updateWishlistItem = async (itemId, updates) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/wishlist/${itemId}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          const index = wishlist.value.findIndex(item => item.id === itemId)
          if (index !== -1) {
            wishlist.value[index] = data.data
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error updating wishlist item:', error)
      return false
    }
  }

  // Check if item is in wishlist
  const isInWishlist = async (partId, partName) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/wishlist/check?part_id=${partId}&part_name=${encodeURIComponent(partName)}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          return data.data.in_wishlist
        }
      }
      return false
    } catch (error) {
      console.error('Error checking wishlist:', error)
      return false
    }
  }

  // Move item from wishlist to cart
  const moveToCart = async (itemId) => {
    if (!isAuthenticated.value) return null

    try {
      const response = await fetch(`/api/wishlist/${itemId}/move-to-cart`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          return data.data
        }
      }
      return null
    } catch (error) {
      console.error('Error moving to cart:', error)
      return null
    }
  }

  // Toggle item in wishlist
  const toggleWishlist = async (item) => {
    const isInList = wishlist.value.some(wishlistItem => 
      wishlistItem.part_id === item.id && wishlistItem.part_name === item.name
    )

    if (isInList) {
      const wishlistItem = wishlist.value.find(wishlistItem => 
        wishlistItem.part_id === item.id && wishlistItem.part_name === item.name
      )
      if (wishlistItem) {
        return await removeFromWishlist(wishlistItem.id)
      }
    } else {
      return await addToWishlist(item)
    }
    return false
  }

  // Get priority badge color
  const getPriorityBadgeColor = (priority) => {
    const colors = {
      high: 'red',
      medium: 'yellow',
      low: 'green'
    }
    return colors[priority] || 'gray'
  }

  // Get priority display name
  const getPriorityDisplay = (priority) => {
    const displays = {
      high: 'High Priority',
      medium: 'Medium Priority',
      low: 'Low Priority'
    }
    return displays[priority] || 'Normal'
  }

  // Format currency
  const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount)
  }

  // Format date
  const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }

  // Get wishlist items by priority
  const getItemsByPriority = (priority) => {
    return wishlist.value.filter(item => item.priority === priority)
  }

  // Get wishlist items by category
  const getItemsByCategory = (category) => {
    return wishlist.value.filter(item => item.part_category === category)
  }

  // Search wishlist items
  const searchWishlist = (query) => {
    if (!query) return wishlist.value
    return wishlist.value.filter(item => 
      item.part_name.toLowerCase().includes(query.toLowerCase()) ||
      item.part_brand?.toLowerCase().includes(query.toLowerCase()) ||
      item.part_number?.toLowerCase().includes(query.toLowerCase())
    )
  }

  // Computed properties
  const totalItems = computed(() => wishlistStatistics.value.total_items || 0)
  const highPriorityItems = computed(() => wishlistStatistics.value.high_priority_items || 0)
  const mediumPriorityItems = computed(() => wishlistStatistics.value.medium_priority_items || 0)
  const lowPriorityItems = computed(() => wishlistStatistics.value.low_priority_items || 0)
  const itemsWithAlerts = computed(() => wishlistStatistics.value.items_with_alerts || 0)
  const recentAdditions = computed(() => wishlistStatistics.value.recent_additions || 0)
  const categories = computed(() => wishlistStatistics.value.categories || {})

  return {
    // State
    wishlist: readonly(wishlist),
    wishlistStatistics: readonly(wishlistStatistics),
    isLoading: readonly(isLoading),
    
    // Actions
    loadWishlist,
    loadWishlistStatistics,
    addToWishlist,
    removeFromWishlist,
    updateWishlistItem,
    isInWishlist,
    moveToCart,
    toggleWishlist,
    
    // Utilities
    getPriorityBadgeColor,
    getPriorityDisplay,
    formatCurrency,
    formatDate,
    getItemsByPriority,
    getItemsByCategory,
    searchWishlist,
    
    // Computed
    totalItems,
    highPriorityItems,
    mediumPriorityItems,
    lowPriorityItems,
    itemsWithAlerts,
    recentAdditions,
    categories
  }
}

// Initialize wishlist (call this from components when needed)
export const initializeWishlist = () => {
  try {
    const { loadWishlist, loadWishlistStatistics } = useWishlist()
    loadWishlist()
    loadWishlistStatistics()
  } catch (error) {
    console.log('Wishlist initialization skipped:', error.message)
  }
}

