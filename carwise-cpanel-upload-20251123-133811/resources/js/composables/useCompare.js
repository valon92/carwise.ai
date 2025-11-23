import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global compare state
const compareList = ref([])
const compareStatistics = ref({})
const isLoading = ref(false)
const maxCompareItems = 4

export function useCompare() {
  const { user, isAuthenticated } = useAuth()

  // Load user's compare list
  const loadCompareList = async (params = {}) => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/compare?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          compareList.value = data.data
          return {
            compareList: data.data,
            pagination: data.pagination
          }
        }
      }
      return { compareList: [], pagination: null }
    } catch (error) {
      console.error('Error loading compare list:', error)
      return { compareList: [], pagination: null }
    } finally {
      isLoading.value = false
    }
  }

  // Load compare statistics
  const loadCompareStatistics = async () => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch('/api/compare/statistics', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          compareStatistics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading compare statistics:', error)
    }
  }

  // Add item to compare list
  const addToCompare = async (item) => {
    if (!isAuthenticated.value) return false

    // Check if already in compare list
    if (isInCompare(item.id)) {
      return false
    }

    // Check if compare list is full
    if (compareList.value.length >= maxCompareItems) {
      return false
    }

    try {
      isLoading.value = true
      const response = await fetch('/api/compare', {
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
          specifications: item.specifications,
          features: item.features,
          compatibility: item.compatibility,
          warranty: item.warranty,
          shipping_info: item.shipping_info
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          compareList.value.push(data.data)
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error adding to compare:', error)
      return false
    } finally {
      isLoading.value = false
    }
  }

  // Remove item from compare list
  const removeFromCompare = async (itemId) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/compare/${itemId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          const index = compareList.value.findIndex(item => item.id === itemId)
          if (index !== -1) {
            compareList.value.splice(index, 1)
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error removing from compare:', error)
      return false
    }
  }

  // Update compare item
  const updateCompareItem = async (itemId, updates) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/compare/${itemId}`, {
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
          const index = compareList.value.findIndex(item => item.id === itemId)
          if (index !== -1) {
            compareList.value[index] = data.data
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error updating compare item:', error)
      return false
    }
  }

  // Check if item is in compare list
  const isInCompare = (partId) => {
    return compareList.value.some(item => item.part_id === partId)
  }

  // Check if item is in compare list (async version)
  const isInCompareAsync = async (partId, partName) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/compare/check?part_id=${partId}&part_name=${encodeURIComponent(partName)}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          return data.data.in_compare
        }
      }
      return false
    } catch (error) {
      console.error('Error checking compare list:', error)
      return false
    }
  }

  // Clear compare list
  const clearCompareList = async () => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch('/api/compare/clear', {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          compareList.value = []
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error clearing compare list:', error)
      return false
    }
  }

  // Reorder compare items
  const reorderCompareItems = async (items) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch('/api/compare/reorder', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ items })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          // Reload compare list to get updated order
          await loadCompareList()
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error reordering compare items:', error)
      return false
    }
  }

  // Toggle item in compare list
  const toggleCompare = async (item) => {
    if (isInCompare(item.id)) {
      const compareItem = compareList.value.find(compareItem => compareItem.part_id === item.id)
      if (compareItem) {
        return await removeFromCompare(compareItem.id)
      }
    } else {
      return await addToCompare(item)
    }
    return false
  }

  // Get compare items by source
  const getItemsBySource = (source) => {
    return compareList.value.filter(item => item.source === source)
  }

  // Get compare items by category
  const getItemsByCategory = (category) => {
    return compareList.value.filter(item => item.part_category === category)
  }

  // Search compare items
  const searchCompare = (query) => {
    if (!query) return compareList.value
    return compareList.value.filter(item => 
      item.part_name.toLowerCase().includes(query.toLowerCase()) ||
      item.part_brand?.toLowerCase().includes(query.toLowerCase()) ||
      item.part_number?.toLowerCase().includes(query.toLowerCase())
    )
  }

  // Get price comparison data
  const getPriceComparison = () => {
    if (compareList.value.length < 2) return null

    const prices = compareList.value.map(item => item.part_price)
    const minPrice = Math.min(...prices)
    const maxPrice = Math.max(...prices)
    const avgPrice = prices.reduce((sum, price) => sum + price, 0) / prices.length

    return {
      min: minPrice,
      max: maxPrice,
      average: avgPrice,
      range: maxPrice - minPrice,
      savings: maxPrice - minPrice
    }
  }

  // Get feature comparison data
  const getFeatureComparison = () => {
    if (compareList.value.length < 2) return null

    const allFeatures = new Set()
    compareList.value.forEach(item => {
      if (item.features) {
        item.features.forEach(feature => allFeatures.add(feature))
      }
    })

    const featureMatrix = {}
    allFeatures.forEach(feature => {
      featureMatrix[feature] = compareList.value.map(item => 
        item.features ? item.features.includes(feature) : false
      )
    })

    return featureMatrix
  }

  // Get specification comparison data
  const getSpecificationComparison = () => {
    if (compareList.value.length < 2) return null

    const allSpecs = new Set()
    compareList.value.forEach(item => {
      if (item.specifications) {
        Object.keys(item.specifications).forEach(spec => allSpecs.add(spec))
      }
    })

    const specMatrix = {}
    allSpecs.forEach(spec => {
      specMatrix[spec] = compareList.value.map(item => 
        item.specifications ? item.specifications[spec] : 'N/A'
      )
    })

    return specMatrix
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

  // Computed properties
  const totalItems = computed(() => compareList.value.length)
  const canAddMore = computed(() => compareList.value.length < maxCompareItems)
  const isFull = computed(() => compareList.value.length >= maxCompareItems)
  const isEmpty = computed(() => compareList.value.length === 0)
  const hasMultipleItems = computed(() => compareList.value.length > 1)

  return {
    // State
    compareList: readonly(compareList),
    compareStatistics: readonly(compareStatistics),
    isLoading: readonly(isLoading),
    maxCompareItems,
    
    // Actions
    loadCompareList,
    loadCompareStatistics,
    addToCompare,
    removeFromCompare,
    updateCompareItem,
    isInCompare,
    isInCompareAsync,
    clearCompareList,
    reorderCompareItems,
    toggleCompare,
    
    // Utilities
    getItemsBySource,
    getItemsByCategory,
    searchCompare,
    getPriceComparison,
    getFeatureComparison,
    getSpecificationComparison,
    formatCurrency,
    formatDate,
    
    // Computed
    totalItems,
    canAddMore,
    isFull,
    isEmpty,
    hasMultipleItems
  }
}

// Initialize compare list (call this from components when needed)
export const initializeCompare = () => {
  try {
    const { loadCompareList, loadCompareStatistics } = useCompare()
    loadCompareList()
    loadCompareStatistics()
  } catch (error) {
    console.log('Compare initialization skipped:', error.message)
  }
}
