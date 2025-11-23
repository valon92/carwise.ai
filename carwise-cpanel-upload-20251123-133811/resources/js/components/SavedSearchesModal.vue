<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Saved Searches</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your saved searches</p>
            </div>
            <div class="flex items-center space-x-3">
              <button 
                @click="showCreateModal = true"
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Save Search
              </button>
              <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Statistics -->
          <div v-if="statistics" class="p-6 border-b border-gray-200 dark:border-secondary-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics.total_saved_searches }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">Total Saved</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics.favorite_saved_searches }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">Favorites</div>
              </div>
              <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ statistics.public_saved_searches }}</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">Public</div>
              </div>
              <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ statistics.searches_with_notifications }}</div>
                <div class="text-sm text-orange-700 dark:text-orange-300">With Alerts</div>
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
                  placeholder="Search saved searches..."
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
                  v-model="favoriteFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All</option>
                  <option value="true">Favorites</option>
                  <option value="false">Not Favorites</option>
                </select>
                <select 
                  v-model="publicFilter"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                >
                  <option value="">All</option>
                  <option value="true">Public</option>
                  <option value="false">Private</option>
                </select>
                <button 
                  @click="refreshSearches"
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
          
          <!-- Saved Searches List -->
          <div class="flex-1 overflow-y-auto p-6">
            <div v-if="isLoading" class="text-center py-12">
              <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600 dark:text-gray-400">Loading saved searches...</p>
            </div>
            
            <div v-else-if="filteredSearches.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No saved searches found</h3>
              <p class="text-gray-600 dark:text-gray-400">Start saving your searches to access them quickly later.</p>
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="search in filteredSearches" 
                :key="search.id"
                class="bg-gray-50 dark:bg-secondary-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                      <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="getSearchTypeBgColor(search.search_type)">
                        <svg class="w-6 h-6" :class="getSearchTypeTextColor(search.search_type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getSearchTypeIcon(search.search_type)"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ search.search_name }}</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ search.search_query }}</p>
                      <p v-if="search.search_description" class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ search.search_description }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <span 
                      v-if="search.is_favorite"
                      class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400"
                    >
                      Favorite
                    </span>
                    <span 
                      v-if="search.is_public"
                      class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400"
                    >
                      Public
                    </span>
                    <span 
                      v-if="search.notification_enabled"
                      class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                    >
                      Alerts
                    </span>
                  </div>
                </div>
                
                <!-- Search Details -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ formatSearchCount(search.search_count) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Searches</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ formatResultsCount(search.results_count) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Avg Results</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ formatAverageDuration(search.average_duration) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Avg Duration</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ formatSuccessRate(search.success_rate) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">Success Rate</div>
                  </div>
                </div>
                
                <!-- Tags -->
                <div v-if="search.tags && search.tags.length > 0" class="mb-4">
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="tag in search.tags" 
                      :key="tag"
                      class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full"
                    >
                      {{ tag }}
                    </span>
                  </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="executeSearch(search)"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Execute Search
                  </button>
                  <button 
                    @click="toggleFavoriteSearch(search)"
                    class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="search.is_favorite ? 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' : 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'"></path>
                    </svg>
                    {{ search.is_favorite ? 'Unfavorite' : 'Favorite' }}
                  </button>
                  <button 
                    @click="editSearch(search)"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Edit
                  </button>
                  <button 
                    @click="deleteSearch(search)"
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
import { useSavedSearches } from '../composables/useSavedSearches'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'executeSearch'])

// Saved searches composable
const { 
  savedSearches, 
  savedSearchStatistics, 
  isLoading, 
  loadSavedSearches, 
  loadSavedSearchStatistics,
  deleteSavedSearch,
  toggleFavorite,
  executeSavedSearch
} = useSavedSearches()

// Local state
const searchQuery = ref('')
const searchTypeFilter = ref('')
const favoriteFilter = ref('')
const publicFilter = ref('')
const showCreateModal = ref(false)

// Computed properties
const filteredSearches = computed(() => {
  const filters = {
    search: searchQuery.value,
    searchType: searchTypeFilter.value,
    isFavorite: favoriteFilter.value ? favoriteFilter.value === 'true' : null,
    isPublic: publicFilter.value ? publicFilter.value === 'true' : null
  }
  
  return filterSavedSearches(filters)
})

const statistics = computed(() => savedSearchStatistics.value)

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

const refreshSearches = async () => {
  await loadSavedSearches()
  await loadSavedSearchStatistics()
}

const executeSearch = (search) => {
  emit('executeSearch', search.search_query)
}

const editSearch = (search) => {
  // TODO: Implement edit functionality
  console.log('Edit search:', search)
}

const deleteSearch = async (search) => {
  if (confirm(`Are you sure you want to delete "${search.search_name}"?`)) {
    const success = await deleteSavedSearch(search.id)
    if (success) {
      await refreshSearches()
    }
  }
}

const toggleFavoriteSearch = async (search) => {
  const success = await toggleFavorite(search.id)
  if (success) {
    await refreshSearches()
  }
}

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    refreshSearches()
  }
})

// Load data on mount
onMounted(() => {
  if (props.isOpen) {
    refreshSearches()
  }
})
</script>
