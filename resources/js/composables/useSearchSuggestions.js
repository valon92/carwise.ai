import { ref, computed, watch, onMounted } from 'vue'
import { useAuth } from './useAuth'

// Global search suggestions state
const suggestions = ref([])
const popularSearches = ref([])
const recentSearches = ref([])
const isLoading = ref(false)
const isVisible = ref(false)

export function useSearchSuggestions() {
  const { isAuthenticated } = useAuth()

  // Load search suggestions
  const loadSuggestions = async (query, limit = 10) => {
    if (!query || query.length < 2) {
      suggestions.value = []
      return []
    }

    try {
      isLoading.value = true
      const response = await fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}&limit=${limit}`)
      
      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          suggestions.value = data.data
          return data.data
        }
      }
      return []
    } catch (error) {
      console.error('Error loading search suggestions:', error)
      return []
    } finally {
      isLoading.value = false
    }
  }

  // Load popular searches
  const loadPopularSearches = async (limit = 10) => {
    try {
      const response = await fetch(`/api/search-suggestions/popular?limit=${limit}`)
      
      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          popularSearches.value = data.data
          return data.data
        }
      }
      return []
    } catch (error) {
      console.error('Error loading popular searches:', error)
      return []
    }
  }

  // Load recent searches
  const loadRecentSearches = async (limit = 10) => {
    if (!isAuthenticated.value) {
      recentSearches.value = []
      return []
    }

    try {
      const response = await fetch(`/api/search-suggestions/recent?limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })
      
      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          recentSearches.value = data.data
          return data.data
        }
      }
      return []
    } catch (error) {
      console.error('Error loading recent searches:', error)
      return []
    }
  }

  // Save search to recent searches
  const saveSearch = async (query) => {
    if (!isAuthenticated.value || !query || query.length < 2) {
      return
    }

    try {
      // In a real app, you would save to backend
      // For now, save to localStorage
      const recent = JSON.parse(localStorage.getItem('recent_searches') || '[]')
      const newRecent = [query, ...recent.filter(item => item !== query)].slice(0, 10)
      localStorage.setItem('recent_searches', JSON.stringify(newRecent))
      
      // Update recent searches
      recentSearches.value = newRecent.map(item => ({
        text: item,
        type: 'recent',
        category: 'Recent Searches',
        relevance: 100
      }))
    } catch (error) {
      console.error('Error saving search:', error)
    }
  }

  // Clear recent searches
  const clearRecentSearches = () => {
    localStorage.removeItem('recent_searches')
    recentSearches.value = []
  }

  // Get suggestion icon based on type
  const getSuggestionIcon = (type) => {
    const icons = {
      part: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
      brand: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
      category: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
      vehicle: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2',
      term: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
      popular: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
      recent: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
    }
    return icons[type] || icons.term
  }

  // Get suggestion color based on type
  const getSuggestionColor = (type) => {
    const colors = {
      part: 'text-blue-600',
      brand: 'text-green-600',
      category: 'text-purple-600',
      vehicle: 'text-orange-600',
      term: 'text-gray-600',
      popular: 'text-yellow-600',
      recent: 'text-indigo-600'
    }
    return colors[type] || colors.term
  }

  // Get suggestion background color based on type
  const getSuggestionBgColor = (type) => {
    const colors = {
      part: 'bg-blue-50',
      brand: 'bg-green-50',
      category: 'bg-purple-50',
      vehicle: 'bg-orange-50',
      term: 'bg-gray-50',
      popular: 'bg-yellow-50',
      recent: 'bg-indigo-50'
    }
    return colors[type] || colors.term
  }

  // Filter suggestions by type
  const filterSuggestionsByType = (type) => {
    return suggestions.value.filter(suggestion => suggestion.type === type)
  }

  // Get suggestions by category
  const getSuggestionsByCategory = (category) => {
    return suggestions.value.filter(suggestion => suggestion.category === category)
  }

  // Search suggestions with debounce
  const searchWithDebounce = (() => {
    let timeoutId
    return (query, callback, delay = 300) => {
      clearTimeout(timeoutId)
      timeoutId = setTimeout(() => {
        callback(query)
      }, delay)
    }
  })()

  // Show suggestions
  const showSuggestions = () => {
    isVisible.value = true
  }

  // Hide suggestions
  const hideSuggestions = () => {
    isVisible.value = false
  }

  // Toggle suggestions visibility
  const toggleSuggestions = () => {
    isVisible.value = !isVisible.value
  }

  // Get all suggestions (search + popular + recent)
  const getAllSuggestions = computed(() => {
    const all = []
    
    // Add search suggestions
    if (suggestions.value.length > 0) {
      all.push(...suggestions.value)
    }
    
    // Add popular searches if no search suggestions
    if (suggestions.value.length === 0 && popularSearches.value.length > 0) {
      all.push(...popularSearches.value)
    }
    
    // Add recent searches if authenticated and no other suggestions
    if (suggestions.value.length === 0 && popularSearches.value.length === 0 && recentSearches.value.length > 0) {
      all.push(...recentSearches.value)
    }
    
    return all
  })

  // Get suggestions count by type
  const getSuggestionsCount = computed(() => {
    const count = {}
    suggestions.value.forEach(suggestion => {
      count[suggestion.type] = (count[suggestion.type] || 0) + 1
    })
    return count
  })

  // Initialize recent searches from localStorage
  const initializeRecentSearches = () => {
    try {
      const recent = JSON.parse(localStorage.getItem('recent_searches') || '[]')
      recentSearches.value = recent.map(item => ({
        text: item,
        type: 'recent',
        category: 'Recent Searches',
        relevance: 100
      }))
    } catch (error) {
      console.error('Error initializing recent searches:', error)
      recentSearches.value = []
    }
  }

  // Auto-initialize on mount
  onMounted(() => {
    initializeRecentSearches()
    loadPopularSearches()
  })

  return {
    // State
    suggestions: computed(() => suggestions.value),
    popularSearches: computed(() => popularSearches.value),
    recentSearches: computed(() => recentSearches.value),
    isLoading: computed(() => isLoading.value),
    isVisible: computed(() => isVisible.value),
    
    // Actions
    loadSuggestions,
    loadPopularSearches,
    loadRecentSearches,
    saveSearch,
    clearRecentSearches,
    searchWithDebounce,
    showSuggestions,
    hideSuggestions,
    toggleSuggestions,
    
    // Utilities
    getSuggestionIcon,
    getSuggestionColor,
    getSuggestionBgColor,
    filterSuggestionsByType,
    getSuggestionsByCategory,
    
    // Computed
    getAllSuggestions,
    getSuggestionsCount
  }
}


