<template>
  <div v-if="isVisible && (suggestions.length > 0 || popularSearches.length > 0 || recentSearches.length > 0)" 
       class="absolute top-full left-0 right-0 z-50 mt-1 bg-white dark:bg-secondary-800 border border-gray-200 dark:border-secondary-700 rounded-lg shadow-lg max-h-96 overflow-y-auto">
    
    <!-- Search Suggestions -->
    <div v-if="suggestions.length > 0" class="p-2">
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 px-2">
        Suggestions
      </div>
      <div class="space-y-1">
        <button
          v-for="suggestion in suggestions"
          :key="`suggestion-${suggestion.text}`"
          @click="selectSuggestion(suggestion)"
          class="w-full flex items-center px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-secondary-700 rounded-lg transition-colors duration-150 group"
        >
          <div class="flex-shrink-0 mr-3">
            <svg class="w-4 h-4" :class="getSuggestionColor(suggestion.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getSuggestionIcon(suggestion.type)"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ suggestion.text }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ suggestion.category }}
            </div>
          </div>
          <div class="flex-shrink-0 ml-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getSuggestionBgColor(suggestion.type), getSuggestionColor(suggestion.type)">
              {{ suggestion.type }}
            </span>
          </div>
        </button>
      </div>
    </div>

    <!-- Popular Searches -->
    <div v-if="suggestions.length === 0 && popularSearches.length > 0" class="p-2">
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 px-2">
        Popular Searches
      </div>
      <div class="space-y-1">
        <button
          v-for="search in popularSearches"
          :key="`popular-${search.text}`"
          @click="selectSuggestion(search)"
          class="w-full flex items-center px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-secondary-700 rounded-lg transition-colors duration-150 group"
        >
          <div class="flex-shrink-0 mr-3">
            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ search.text }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              Popular Search
            </div>
          </div>
          <div class="flex-shrink-0 ml-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-600">
              Popular
            </span>
          </div>
        </button>
      </div>
    </div>

    <!-- Recent Searches -->
    <div v-if="suggestions.length === 0 && popularSearches.length === 0 && recentSearches.length > 0" class="p-2">
      <div class="flex items-center justify-between mb-2 px-2">
        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
          Recent Searches
        </div>
        <button
          @click="clearRecentSearches"
          class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          title="Clear recent searches"
        >
          Clear
        </button>
      </div>
      <div class="space-y-1">
        <button
          v-for="search in recentSearches"
          :key="`recent-${search.text}`"
          @click="selectSuggestion(search)"
          class="w-full flex items-center px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-secondary-700 rounded-lg transition-colors duration-150 group"
        >
          <div class="flex-shrink-0 mr-3">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ search.text }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              Recent Search
            </div>
          </div>
          <div class="flex-shrink-0 ml-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-600">
              Recent
            </span>
          </div>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="p-4 text-center">
      <svg class="animate-spin h-5 w-5 text-primary-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-sm text-gray-600 dark:text-gray-400">Loading suggestions...</p>
    </div>

    <!-- Empty State -->
    <div v-if="!isLoading && suggestions.length === 0 && popularSearches.length === 0 && recentSearches.length === 0" class="p-4 text-center">
      <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
      <p class="text-sm text-gray-600 dark:text-gray-400">No suggestions found</p>
    </div>
  </div>
</template>

<script setup>
import { useSearchSuggestions } from '../composables/useSearchSuggestions'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  suggestions: {
    type: Array,
    default: () => []
  },
  popularSearches: {
    type: Array,
    default: () => []
  },
  recentSearches: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select', 'clearRecent'])

// Search suggestions composable
const { 
  getSuggestionIcon, 
  getSuggestionColor, 
  getSuggestionBgColor,
  clearRecentSearches: clearRecentSearchesAction
} = useSearchSuggestions()

// Methods
const selectSuggestion = (suggestion) => {
  emit('select', suggestion.text)
}

const clearRecentSearches = () => {
  clearRecentSearchesAction()
  emit('clearRecent')
}
</script>


