import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global saved searches state
const savedSearches = ref([])
const savedSearchStatistics = ref({})
const publicSearches = ref([])
const trendingSearches = ref([])
const recommendedSearches = ref([])
const isLoading = ref(false)

export function useSavedSearches() {
  const { user, isAuthenticated } = useAuth()

  // Load saved searches
  const loadSavedSearches = async (params = {}) => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/saved-searches?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          savedSearches.value = data.data
          return {
            searches: data.data,
            pagination: data.pagination
          }
        }
      }
      return { searches: [], pagination: null }
    } catch (error) {
      console.error('Error loading saved searches:', error)
      return { searches: [], pagination: null }
    } finally {
      isLoading.value = false
    }
  }

  // Load saved search statistics
  const loadSavedSearchStatistics = async () => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch('/api/saved-searches/statistics', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          savedSearchStatistics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading saved search statistics:', error)
    }
  }

  // Load public saved searches
  const loadPublicSearches = async (params = {}) => {
    try {
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/saved-searches/public?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          publicSearches.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading public searches:', error)
    }
  }

  // Load trending saved searches
  const loadTrendingSearches = async (limit = 10, days = 7) => {
    try {
      const response = await fetch(`/api/saved-searches/trending?limit=${limit}&days=${days}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          trendingSearches.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading trending searches:', error)
    }
  }

  // Load recommended saved searches
  const loadRecommendedSearches = async (limit = 10) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/saved-searches/recommended?limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          recommendedSearches.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading recommended searches:', error)
    }
  }

  // Create saved search
  const createSavedSearch = async (searchData) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch('/api/saved-searches', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          search_name: searchData.name,
          search_query: searchData.query,
          search_type: searchData.type || 'car_parts',
          search_category: searchData.category,
          search_filters: searchData.filters,
          search_description: searchData.description,
          is_public: searchData.isPublic || false,
          is_favorite: searchData.isFavorite || false,
          tags: searchData.tags,
          notification_enabled: searchData.notificationEnabled || false,
          notification_frequency: searchData.notificationFrequency || 'weekly',
          search_source: searchData.source || 'web',
          search_context: searchData.context,
          additional_data: searchData.additionalData
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          await loadSavedSearchStatistics()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error creating saved search:', error)
      return false
    }
  }

  // Update saved search
  const updateSavedSearch = async (id, updates) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}`, {
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
          await loadSavedSearches()
          await loadSavedSearchStatistics()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error updating saved search:', error)
      return false
    }
  }

  // Delete saved search
  const deleteSavedSearch = async (id) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          await loadSavedSearchStatistics()
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error deleting saved search:', error)
      return false
    }
  }

  // Execute saved search
  const executeSavedSearch = async (id, resultsCount = 0, duration = 0, isSuccessful = true) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}/execute`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          results_count: resultsCount,
          duration: duration,
          is_successful: isSuccessful
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          await loadSavedSearchStatistics()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error executing saved search:', error)
      return false
    }
  }

  // Toggle favorite
  const toggleFavorite = async (id) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}/toggle-favorite`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error toggling favorite:', error)
      return false
    }
  }

  // Toggle notification
  const toggleNotification = async (id) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}/toggle-notification`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error toggling notification:', error)
      return false
    }
  }

  // Duplicate saved search
  const duplicateSavedSearch = async (id) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/saved-searches/${id}/duplicate`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          await loadSavedSearches()
          await loadSavedSearchStatistics()
          return data.data
        }
      }
      return false
    } catch (error) {
      console.error('Error duplicating saved search:', error)
      return false
    }
  }

  // Search saved searches
  const searchSavedSearches = async (query, isPublic = null, limit = 20) => {
    try {
      const params = new URLSearchParams({
        q: query,
        limit: limit
      })
      
      if (isPublic !== null) {
        params.append('is_public', isPublic)
      }

      const response = await fetch(`/api/saved-searches/search?${params}`, {
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
      return []
    } catch (error) {
      console.error('Error searching saved searches:', error)
      return []
    }
  }

  // Save current search as saved search
  const saveCurrentSearch = async (searchData) => {
    if (!isAuthenticated.value) return false

    const savedSearchData = {
      name: searchData.name || searchData.query,
      query: searchData.query,
      type: searchData.type || 'car_parts',
      category: searchData.category,
      filters: searchData.filters,
      description: searchData.description,
      isPublic: searchData.isPublic || false,
      isFavorite: searchData.isFavorite || false,
      tags: searchData.tags,
      notificationEnabled: searchData.notificationEnabled || false,
      notificationFrequency: searchData.notificationFrequency || 'weekly',
      source: searchData.source || 'web',
      context: searchData.context,
      additionalData: searchData.additionalData
    }

    return await createSavedSearch(savedSearchData)
  }

  // Get saved search by ID
  const getSavedSearchById = (id) => {
    return savedSearches.value.find(search => search.id === id)
  }

  // Check if search is saved
  const isSearchSaved = (query, type = 'car_parts') => {
    return savedSearches.value.some(search => 
      search.search_query === query && search.search_type === type
    )
  }

  // Get saved search by query
  const getSavedSearchByQuery = (query, type = 'car_parts') => {
    return savedSearches.value.find(search => 
      search.search_query === query && search.search_type === type
    )
  }

  // Format search count
  const formatSearchCount = (count) => {
    if (count >= 1000) {
      return Math.round(count / 1000 * 10) / 10 + 'k'
    }
    return count.toString()
  }

  // Format results count
  const formatResultsCount = (count) => {
    if (count >= 1000) {
      return Math.round(count / 1000 * 10) / 10 + 'k'
    }
    return count.toString()
  }

  // Format average duration
  const formatAverageDuration = (duration) => {
    if (duration < 1) {
      return Math.round(duration * 1000) + 'ms'
    }
    return Math.round(duration * 100) / 100 + 's'
  }

  // Format success rate
  const formatSuccessRate = (rate) => {
    return Math.round(rate * 10) / 10 + '%'
  }

  // Get search type icon
  const getSearchTypeIcon = (type) => {
    const icons = {
      'car_parts': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
      'diagnosis': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
      'vehicles': 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-2',
      'brands': 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
      'categories': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
    }
    return icons[type] || icons.car_parts
  }

  // Get search type color
  const getSearchTypeColor = (type) => {
    const colors = {
      'car_parts': 'blue',
      'diagnosis': 'green',
      'vehicles': 'purple',
      'brands': 'orange',
      'categories': 'pink'
    }
    return colors[type] || 'gray'
  }

  // Get performance rating
  const getPerformanceRating = (duration) => {
    if (duration < 0.5) return 'Excellent'
    if (duration < 1.0) return 'Good'
    if (duration < 2.0) return 'Average'
    return 'Slow'
  }

  // Get performance color
  const getPerformanceColor = (duration) => {
    const rating = getPerformanceRating(duration)
    const colors = {
      'Excellent': 'green',
      'Good': 'blue',
      'Average': 'yellow',
      'Slow': 'red'
    }
    return colors[rating] || 'gray'
  }

  // Get notification frequency display
  const getNotificationFrequencyDisplay = (frequency) => {
    const frequencies = {
      'daily': 'Daily',
      'weekly': 'Weekly',
      'monthly': 'Monthly',
      'instant': 'Instant'
    }
    return frequencies[frequency] || 'Disabled'
  }

  // Get tags display
  const getTagsDisplay = (tags) => {
    if (!tags || tags.length === 0) {
      return 'No tags'
    }
    return tags.join(', ')
  }

  // Get filters display
  const getFiltersDisplay = (filters) => {
    if (!filters || Object.keys(filters).length === 0) {
      return 'No filters'
    }
    
    const filterStrings = []
    for (const [key, value] of Object.entries(filters)) {
      if (value) {
        filterStrings.push(`${key}: ${value}`)
      }
    }
    
    return filterStrings.join(', ')
  }

  // Get last searched display
  const getLastSearchedDisplay = (lastSearchedAt) => {
    if (!lastSearchedAt) {
      return 'Never'
    }
    
    const date = new Date(lastSearchedAt)
    const now = new Date()
    const diff = Math.floor((now - date) / (1000 * 60 * 60 * 24))
    
    if (diff === 0) {
      return 'Today'
    } else if (diff === 1) {
      return 'Yesterday'
    } else if (diff < 7) {
      return `${diff} days ago`
    } else if (diff < 30) {
      return `${Math.round(diff / 7)} weeks ago`
    } else {
      return `${Math.round(diff / 30)} months ago`
    }
  }

  // Filter saved searches
  const filterSavedSearches = (filters) => {
    let filtered = savedSearches.value

    if (filters.searchType) {
      filtered = filtered.filter(search => search.search_type === filters.searchType)
    }

    if (filters.searchCategory) {
      filtered = filtered.filter(search => search.search_category === filters.searchCategory)
    }

    if (filters.isPublic !== null) {
      filtered = filtered.filter(search => search.is_public === filters.isPublic)
    }

    if (filters.isFavorite !== null) {
      filtered = filtered.filter(search => search.is_favorite === filters.isFavorite)
    }

    if (filters.hasNotifications !== null) {
      filtered = filtered.filter(search => search.notification_enabled === filters.hasNotifications)
    }

    if (filters.tag) {
      filtered = filtered.filter(search => 
        search.tags && search.tags.includes(filters.tag)
      )
    }

    if (filters.search) {
      const query = filters.search.toLowerCase()
      filtered = filtered.filter(search => 
        search.search_name.toLowerCase().includes(query) ||
        search.search_query.toLowerCase().includes(query) ||
        search.search_description?.toLowerCase().includes(query) ||
        (search.tags && search.tags.some(tag => tag.toLowerCase().includes(query)))
      )
    }

    return filtered
  }

  // Saved search statistics
  const totalSavedSearches = computed(() => savedSearchStatistics.value.total_saved_searches || 0)
  const publicSavedSearches = computed(() => savedSearchStatistics.value.public_saved_searches || 0)
  const privateSavedSearches = computed(() => savedSearchStatistics.value.private_saved_searches || 0)
  const favoriteSavedSearches = computed(() => savedSearchStatistics.value.favorite_saved_searches || 0)
  const searchesWithNotifications = computed(() => savedSearchStatistics.value.searches_with_notifications || 0)
  const totalSearchCount = computed(() => savedSearchStatistics.value.total_search_count || 0)
  const averageSearchCount = computed(() => savedSearchStatistics.value.average_search_count || 0)
  const mostSearched = computed(() => savedSearchStatistics.value.most_searched || '')

  return {
    // State
    savedSearches: readonly(savedSearches),
    savedSearchStatistics: readonly(savedSearchStatistics),
    publicSearches: readonly(publicSearches),
    trendingSearches: readonly(trendingSearches),
    recommendedSearches: readonly(recommendedSearches),
    isLoading: readonly(isLoading),
    
    // Actions
    loadSavedSearches,
    loadSavedSearchStatistics,
    loadPublicSearches,
    loadTrendingSearches,
    loadRecommendedSearches,
    createSavedSearch,
    updateSavedSearch,
    deleteSavedSearch,
    executeSavedSearch,
    toggleFavorite,
    toggleNotification,
    duplicateSavedSearch,
    searchSavedSearches,
    saveCurrentSearch,
    
    // Utilities
    getSavedSearchById,
    isSearchSaved,
    getSavedSearchByQuery,
    formatSearchCount,
    formatResultsCount,
    formatAverageDuration,
    formatSuccessRate,
    getSearchTypeIcon,
    getSearchTypeColor,
    getPerformanceRating,
    getPerformanceColor,
    getNotificationFrequencyDisplay,
    getTagsDisplay,
    getFiltersDisplay,
    getLastSearchedDisplay,
    filterSavedSearches,
    
    // Computed
    totalSavedSearches,
    publicSavedSearches,
    privateSavedSearches,
    favoriteSavedSearches,
    searchesWithNotifications,
    totalSearchCount,
    averageSearchCount,
    mostSearched
  }
}


