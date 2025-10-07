<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Search History</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage your search history</p>
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
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics.total_searches }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">Total Searches</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics.successful_searches }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">Successful</div>
              </div>
              <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ statistics.unique_queries }}</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">Unique Queries</div>
              </div>
              <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ formatDuration(statistics.average_duration) }}</div>
                <div class="text-sm text-orange-700 dark:text-orange-300">Avg Duration</div>
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
                  placeholder="Search in history..."
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
              </div>
              <div class="flex gap-2">
                <select 
                  v-model="searchTypeFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Types</option>
                  <option value="car_parts">Car Parts</option>
                  <option value="diagnosis">Diagnosis</option>
                  <option value="vehicles">Vehicles</option>
                  <option value="brands">Brands</option>
                  <option value="categories">Categories</option>
                </select>
                <select 
                  v-model="successFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Results</option>
                  <option value="true">Successful</option>
                  <option value="false">Failed</option>
                </select>
                <select 
                  v-model="resultsFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All Results</option>
                  <option value="true">With Results</option>
                  <option value="false">No Results</option>
                </select>
                <button 
                  @click="refreshHistory"
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
                <button 
                  @click="clearHistory"
                  class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Search History Items -->
          <div class="flex-1 overflow-y-auto p-6">
            <div v-if="isLoading" class="text-center py-12">
              <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600 dark:text-gray-400">Loading search history...</p>
            </div>
            
            <div v-else-if="filteredHistory.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No search history found</h3>
              <p class="text-gray-600 dark:text-gray-400">Your search history will appear here once you start searching.</p>
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="item in filteredHistory" 
                :key="item.id"
                class="bg-gray-50 dark:bg-secondary-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                      <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="getSearchTypeBgColor(item.search_type)">
                        <svg class="w-6 h-6" :class="getSearchTypeTextColor(item.search_type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getSearchTypeIcon(item.search_type)"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ item.search_query }}</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ item.search_category || 'No category' }}</p>
                      <p class="text-sm text-gray-500 dark:text-gray-500">{{ formatTimestamp(item.search_timestamp) }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <span 
                      :class="getSuccessBadgeClass(item.is_successful)"
                      class="px-3 py-1 rounded-full text-sm font-medium"
                    >
                      {{ getSuccessStatus(item.is_successful) }}
                    </span>
                    <span 
                      :class="getPerformanceBadgeClass(item.search_duration)"
                      class="px-3 py-1 rounded-full text-sm font-medium"
                    >
                      {{ getPerformanceRating(item.search_duration) }}
                    </span>
                  </div>
                </div>
                
                <!-- Search Details -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ item.results_count }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Results</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ formatDuration(item.search_duration) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Duration</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ item.device_type || 'Unknown' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Device</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ item.browser || 'Unknown' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Browser</div>
                  </div>
                </div>
                
                <!-- Search Filters -->
                <div v-if="item.search_filters && Object.keys(item.search_filters).length > 0" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                  <p class="text-sm text-blue-700 dark:text-blue-300">
                    <strong>Filters:</strong> {{ getFiltersDisplay(item.search_filters) }}
                  </p>
                </div>
                
                <!-- Error Message -->
                <div v-if="item.error_message" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                  <p class="text-sm text-red-700 dark:text-red-300">
                    <strong>Error:</strong> {{ item.error_message }}
                  </p>
                </div>
                
                <!-- Item Actions -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="repeatSearch(item)"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Repeat Search
                  </button>
                  <button 
                    @click="deleteEntry(item)"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
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
import { useSearchHistory } from '../composables/useSearchHistory'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'repeatSearch'])

// Search history composable
const { 
  searchHistory, 
  searchStatistics, 
  isLoading, 
  loadSearchHistory, 
  loadSearchStatistics,
  clearSearchHistory,
  deleteSearchHistoryEntry,
  getSearchTypeIcon,
  getSearchTypeColor,
  getPerformanceRating,
  getPerformanceColor,
  formatDuration,
  formatTimestamp,
  getSuccessStatus,
  filterSearchHistory
} = useSearchHistory()

// Local state
const searchQuery = ref('')
const searchTypeFilter = ref('')
const successFilter = ref('')
const resultsFilter = ref('')

// Computed properties
const filteredHistory = computed(() => {
  const filters = {
    search: searchQuery.value,
    searchType: searchTypeFilter.value,
    isSuccessful: successFilter.value ? successFilter.value === 'true' : null,
    hasResults: resultsFilter.value ? resultsFilter.value === 'true' : null
  }
  
  return filterSearchHistory(filters)
})

const statistics = computed(() => searchStatistics.value)

// Methods
const getSearchTypeBgColor = (type) => {
  const colors = {
    'car_parts': 'bg-blue-100 dark:bg-blue-900/20',
    'diagnosis': 'bg-green-100 dark:bg-green-900/20',
    'vehicles': 'bg-purple-100 dark:bg-purple-900/20',
    'brands': 'bg-orange-100 dark:bg-orange-900/20',
    'categories': 'bg-pink-100 dark:bg-pink-900/20'
  }
  return colors[type] || 'bg-gray-100 dark:bg-gray-900/20'
}

const getSearchTypeTextColor = (type) => {
  const colors = {
    'car_parts': 'text-blue-600 dark:text-blue-400',
    'diagnosis': 'text-green-600 dark:text-green-400',
    'vehicles': 'text-purple-600 dark:text-purple-400',
    'brands': 'text-orange-600 dark:text-orange-400',
    'categories': 'text-pink-600 dark:text-pink-400'
  }
  return colors[type] || 'text-gray-600 dark:text-gray-400'
}

const getSuccessBadgeClass = (isSuccessful) => {
  return isSuccessful 
    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
}

const getPerformanceBadgeClass = (duration) => {
  const rating = getPerformanceRating(duration)
  const classes = {
    'Excellent': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'Good': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'Average': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'Slow': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  }
  return classes[rating] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

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

const refreshHistory = async () => {
  await loadSearchHistory()
  await loadSearchStatistics()
}

const clearHistory = async () => {
  if (confirm('Are you sure you want to clear all search history? This action cannot be undone.')) {
    const success = await clearSearchHistory()
    if (success) {
      await refreshHistory()
    }
  }
}

const repeatSearch = (item) => {
  emit('repeatSearch', item.search_query)
}

const deleteEntry = async (item) => {
  if (confirm(`Are you sure you want to delete this search history entry?`)) {
    const success = await deleteSearchHistoryEntry(item.id)
    if (success) {
      await refreshHistory()
    }
  }
}

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    refreshHistory()
  }
})

// Load data on mount
onMounted(() => {
  if (props.isOpen) {
    refreshHistory()
  }
})
</script>
