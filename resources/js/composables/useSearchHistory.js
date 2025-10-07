import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global search history state
const searchHistory = ref([])
const searchStatistics = ref({})
const popularSearches = ref([])
const searchTrends = ref([])
const recentSearches = ref([])
const searchAnalytics = ref({})
const isLoading = ref(false)

export function useSearchHistory() {
  const { user, isAuthenticated } = useAuth()

  // Load search history
  const loadSearchHistory = async (params = {}) => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const queryParams = new URLSearchParams(params).toString()
      const response = await fetch(`/api/search-history?${queryParams}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          searchHistory.value = data.data
          return {
            history: data.data,
            pagination: data.pagination
          }
        }
      }
      return { history: [], pagination: null }
    } catch (error) {
      console.error('Error loading search history:', error)
      return { history: [], pagination: null }
    } finally {
      isLoading.value = false
    }
  }

  // Load search statistics
  const loadSearchStatistics = async (days = 30) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/search-history/statistics?days=${days}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          searchStatistics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading search statistics:', error)
    }
  }

  // Load popular searches
  const loadPopularSearches = async (limit = 10, days = 30) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/search-history/popular?limit=${limit}&days=${days}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          popularSearches.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading popular searches:', error)
    }
  }

  // Load search trends
  const loadSearchTrends = async (days = 30) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/search-history/trends?days=${days}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          searchTrends.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading search trends:', error)
    }
  }

  // Load recent searches
  const loadRecentSearches = async (limit = 10) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/search-history/recent?limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          recentSearches.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading recent searches:', error)
    }
  }

  // Load search analytics
  const loadSearchAnalytics = async (days = 30) => {
    if (!isAuthenticated.value) return

    try {
      const response = await fetch(`/api/search-history/analytics?days=${days}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          searchAnalytics.value = data.data
        }
      }
    } catch (error) {
      console.error('Error loading search analytics:', error)
    }
  }

  // Record search history
  const recordSearch = async (searchData) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch('/api/search-history', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          search_query: searchData.query,
          search_type: searchData.type || 'car_parts',
          search_category: searchData.category,
          search_filters: searchData.filters,
          results_count: searchData.resultsCount || 0,
          search_duration: searchData.duration,
          search_source: searchData.source || 'web',
          search_context: searchData.context,
          additional_data: searchData.additionalData,
          is_successful: searchData.isSuccessful !== false,
          error_message: searchData.errorMessage
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          // Add to recent searches
          recentSearches.value.unshift(data.data)
          if (recentSearches.value.length > 10) {
            recentSearches.value = recentSearches.value.slice(0, 10)
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error recording search:', error)
      return false
    }
  }

  // Clear search history
  const clearSearchHistory = async (days = null) => {
    if (!isAuthenticated.value) return false

    try {
      const url = days ? `/api/search-history?days=${days}` : '/api/search-history'
      const response = await fetch(url, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          searchHistory.value = []
          recentSearches.value = []
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error clearing search history:', error)
      return false
    }
  }

  // Delete specific search history entry
  const deleteSearchHistoryEntry = async (id) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch(`/api/search-history/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          // Remove from local state
          const index = searchHistory.value.findIndex(item => item.id === id)
          if (index !== -1) {
            searchHistory.value.splice(index, 1)
          }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error deleting search history entry:', error)
      return false
    }
  }

  // Get search frequency for a query
  const getSearchFrequency = (query) => {
    return searchHistory.value.filter(item => item.search_query === query).length
  }

  // Get search performance rating
  const getPerformanceRating = (duration) => {
    if (duration < 0.5) return 'Excellent'
    if (duration < 1.0) return 'Good'
    if (duration < 2.0) return 'Average'
    return 'Slow'
  }

  // Get search performance color
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

  // Format search duration
  const formatDuration = (duration) => {
    if (duration < 1) {
      return Math.round(duration * 1000) + 'ms'
    }
    return Math.round(duration * 100) / 100 + 's'
  }

  // Format search timestamp
  const formatTimestamp = (timestamp) => {
    const date = new Date(timestamp)
    const now = new Date()
    const diff = now - date

    if (diff < 60000) { // Less than 1 minute
      return 'Just now'
    } else if (diff < 3600000) { // Less than 1 hour
      const minutes = Math.floor(diff / 60000)
      return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
    } else if (diff < 86400000) { // Less than 1 day
      const hours = Math.floor(diff / 3600000)
      return `${hours} hour${hours > 1 ? 's' : ''} ago`
    } else if (diff < 604800000) { // Less than 1 week
      const days = Math.floor(diff / 86400000)
      return `${days} day${days > 1 ? 's' : ''} ago`
    } else {
      return date.toLocaleDateString()
    }
  }

  // Get search result status
  const getResultStatus = (resultsCount) => {
    if (resultsCount > 0) {
      return `Found ${resultsCount} result${resultsCount > 1 ? 's' : ''}`
    }
    return 'No results found'
  }

  // Get search success status
  const getSuccessStatus = (isSuccessful) => {
    return isSuccessful ? 'Successful' : 'Failed'
  }

  // Get search type icon
  const getSearchTypeIcon = (type) => {
    const icons = {
      'car_parts': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
      'diagnosis': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
      'vehicles': 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-2',
      'brands': 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
      'categories': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
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

  // Filter search history
  const filterSearchHistory = (filters) => {
    let filtered = searchHistory.value

    if (filters.searchType) {
      filtered = filtered.filter(item => item.search_type === filters.searchType)
    }

    if (filters.searchCategory) {
      filtered = filtered.filter(item => item.search_category === filters.searchCategory)
    }

    if (filters.isSuccessful !== null) {
      filtered = filtered.filter(item => item.is_successful === filters.isSuccessful)
    }

    if (filters.hasResults !== null) {
      if (filters.hasResults) {
        filtered = filtered.filter(item => item.results_count > 0)
      } else {
        filtered = filtered.filter(item => item.results_count === 0)
      }
    }

    if (filters.deviceType) {
      filtered = filtered.filter(item => item.device_type === filters.deviceType)
    }

    if (filters.browser) {
      filtered = filtered.filter(item => item.browser === filters.browser)
    }

    if (filters.search) {
      const query = filters.search.toLowerCase()
      filtered = filtered.filter(item => 
        item.search_query.toLowerCase().includes(query)
      )
    }

    return filtered
  }

  // Search history statistics
  const totalSearches = computed(() => searchStatistics.value.total_searches || 0)
  const successfulSearches = computed(() => searchStatistics.value.successful_searches || 0)
  const failedSearches = computed(() => searchStatistics.value.failed_searches || 0)
  const searchesWithResults = computed(() => searchStatistics.value.searches_with_results || 0)
  const searchesWithoutResults = computed(() => searchStatistics.value.searches_without_results || 0)
  const averageResults = computed(() => searchStatistics.value.average_results || 0)
  const averageDuration = computed(() => searchStatistics.value.average_duration || 0)
  const uniqueQueries = computed(() => searchStatistics.value.unique_queries || 0)
  const mostSearchedQuery = computed(() => searchStatistics.value.most_searched_query || '')

  return {
    // State
    searchHistory: readonly(searchHistory),
    searchStatistics: readonly(searchStatistics),
    popularSearches: readonly(popularSearches),
    searchTrends: readonly(searchTrends),
    recentSearches: readonly(recentSearches),
    searchAnalytics: readonly(searchAnalytics),
    isLoading: readonly(isLoading),
    
    // Actions
    loadSearchHistory,
    loadSearchStatistics,
    loadPopularSearches,
    loadSearchTrends,
    loadRecentSearches,
    loadSearchAnalytics,
    recordSearch,
    clearSearchHistory,
    deleteSearchHistoryEntry,
    
    // Utilities
    getSearchFrequency,
    getPerformanceRating,
    getPerformanceColor,
    formatDuration,
    formatTimestamp,
    getResultStatus,
    getSuccessStatus,
    getSearchTypeIcon,
    getSearchTypeColor,
    filterSearchHistory,
    
    // Computed
    totalSearches,
    successfulSearches,
    failedSearches,
    searchesWithResults,
    searchesWithoutResults,
    averageResults,
    averageDuration,
    uniqueQueries,
    mostSearchedQuery
  }
}


