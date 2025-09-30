<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-900 dark:via-secondary-800 dark:to-primary-900">
    <!-- Header Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600/10 to-secondary-600/10"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 dark:bg-primary-900 rounded-full mb-6 shadow-lg">
            <svg class="w-10 h-10 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-12">
      <!-- Page Title and Subtitle -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-secondary-900 dark:text-white mb-4">
          Authorized Car Parts
        </h1>
        <p class="text-lg text-secondary-600 dark:text-secondary-400 max-w-3xl mx-auto">
          Find high-quality, certified car parts for your vehicle. All parts are manufacturer-authorized and come with warranty.
        </p>
      </div>

      <!-- Search and Filters -->
      <div class="card glass mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="md:col-span-2">
            <div class="relative">
              <input 
                v-model="searchQuery"
                @input="debouncedSearch"
                type="text" 
                class="input pl-10" 
                placeholder="Search for parts..."
              />
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

          <!-- Category Filter -->
          <div>
            <select v-model="selectedCategory" @change="filterParts" class="input">
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category.charAt(0).toUpperCase() + category.slice(1) }}
              </option>
            </select>
          </div>

          <!-- Manufacturer Filter -->
          <div>
            <select v-model="selectedManufacturer" @change="filterParts" class="input">
              <option value="">All Manufacturers</option>
              <option v-for="manufacturer in manufacturers" :key="manufacturer" :value="manufacturer">
                {{ manufacturer }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Featured Parts -->
      <div v-if="featuredParts.length > 0 && !searchQuery" class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl md:text-3xl font-bold text-secondary-900 dark:text-white">
            Featured Parts
          </h2>
          <div class="h-1 flex-1 ml-4 bg-gradient-to-r from-primary-600 to-transparent rounded"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="part in featuredParts" 
            :key="part.id"
            class="card glass cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            @click="viewPart(part)"
          >
            <div class="aspect-w-16 aspect-h-9 mb-4">
              <img 
                :src="part.image_url || '/images/parts/placeholder.jpg'" 
                :alt="part.name"
                class="w-full h-48 object-cover rounded-lg"
              />
            </div>
            <div class="p-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                {{ part.name }}
              </h3>
              <p class="text-secondary-600 dark:text-secondary-400 text-sm mb-3 line-clamp-2">
                {{ part.description }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                  {{ part.formatted_price }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getQualityColor(part.quality_grade)">
                  {{ part.quality_display }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Parts Grid -->
      <div v-if="parts.length > 0">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
          <div class="flex items-center">
            <h2 class="text-2xl md:text-3xl font-bold text-secondary-900 dark:text-white">
              All Parts
            </h2>
            <span class="ml-3 text-lg font-normal text-secondary-600 dark:text-secondary-400">
              ({{ totalParts }})
            </span>
            <div class="h-1 flex-1 ml-4 bg-gradient-to-r from-primary-600 to-transparent rounded hidden md:block"></div>
          </div>
          
          <!-- Sort Options -->
          <div class="flex items-center space-x-4">
            <select v-model="sortBy" @change="sortParts" class="input">
              <option value="name">Sort by Name</option>
              <option value="price">Sort by Price</option>
              <option value="rating">Sort by Rating</option>
              <option value="created_at">Sort by Newest</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
            v-for="part in parts" 
            :key="part.id"
            class="card glass cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            @click="viewPart(part)"
          >
            <div class="aspect-w-16 aspect-h-9 mb-4">
              <img 
                :src="part.image_url || '/images/parts/placeholder.jpg'" 
                :alt="part.name"
                class="w-full h-48 object-cover rounded-lg"
              />
            </div>
            <div class="p-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                {{ part.name }}
              </h3>
              <p class="text-secondary-600 dark:text-secondary-400 text-sm mb-3 line-clamp-2">
                {{ part.description }}
              </p>
              
              <!-- Part Details -->
              <div class="space-y-2 mb-3">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-secondary-500 dark:text-secondary-400">Category:</span>
                  <span class="font-medium">{{ part.category.charAt(0).toUpperCase() + part.category.slice(1) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-secondary-500 dark:text-secondary-400">Manufacturer:</span>
                  <span class="font-medium">{{ part.manufacturer }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-secondary-500 dark:text-secondary-400">Difficulty:</span>
                  <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getDifficultyColor(part.difficulty_level)">
                    {{ part.difficulty_level.charAt(0).toUpperCase() + part.difficulty_level.slice(1) }}
                  </span>
                </div>
              </div>

              <!-- Price and Quality -->
              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                  {{ part.formatted_price }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getQualityColor(part.quality_grade)">
                  {{ part.quality_display }}
                </span>
              </div>

              <!-- Stock Status -->
              <div class="mt-3 flex items-center justify-between">
                <span class="text-sm" :class="part.in_stock ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                  {{ part.in_stock ? 'In Stock' : 'Out of Stock' }}
                </span>
                <div class="flex items-center text-sm text-secondary-500 dark:text-secondary-400">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  {{ parseFloat(part.rating).toFixed(1) }} ({{ part.review_count }})
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center space-x-2">
            <button 
              @click="changePage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm font-medium text-secondary-500 bg-white border border-secondary-300 rounded-md hover:bg-secondary-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            
            <span 
              v-for="page in visiblePages" 
              :key="page"
              @click="changePage(page)"
              class="px-3 py-2 text-sm font-medium rounded-md cursor-pointer"
              :class="page === currentPage 
                ? 'text-white bg-primary-600 border border-primary-600' 
                : 'text-secondary-500 bg-white border border-secondary-300 hover:bg-secondary-50'"
            >
              {{ page }}
            </span>
            
            <button 
              @click="changePage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm font-medium text-secondary-500 bg-white border border-secondary-300 rounded-md hover:bg-secondary-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="card glass text-center py-20">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 mx-auto mb-8 bg-gradient-to-br from-primary-100 to-secondary-100 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">
            No Parts Found
          </h3>
          <p class="text-secondary-600 dark:text-secondary-400 mb-8">
            Try adjusting your search criteria or filters to find the parts you need.
          </p>
          <button 
            @click="clearFilters"
            class="btn-primary"
          >
            Clear Filters
          </button>
        </div>
      </div>

    </div>

    <!-- Part Detail Modal -->
    <div v-if="selectedPart" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-secondary-800 border-b border-secondary-200 dark:border-secondary-700 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold text-secondary-900 dark:text-white">
              {{ selectedPart.name }}
            </h3>
            <button 
              @click="selectedPart = null"
              class="p-2 rounded-lg text-secondary-400 hover:text-secondary-600 hover:bg-secondary-100 dark:hover:bg-secondary-700 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Part Image -->
            <div>
              <img 
                :src="selectedPart.image_url || '/images/parts/placeholder.jpg'" 
                :alt="selectedPart.name"
                class="w-full h-64 object-cover rounded-lg mb-4"
              />
            </div>

            <!-- Part Details -->
            <div class="space-y-6">
              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Description
                </h4>
                <p class="text-secondary-600 dark:text-secondary-400">
                  {{ selectedPart.description }}
                </p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Part Number:</span>
                  <p class="font-medium">{{ selectedPart.part_number }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Manufacturer:</span>
                  <p class="font-medium">{{ selectedPart.manufacturer }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Category:</span>
                  <p class="font-medium">{{ selectedPart.category.charAt(0).toUpperCase() + selectedPart.category.slice(1) }}</p>
                </div>
                <div>
                  <span class="text-sm text-secondary-500 dark:text-secondary-400">Quality:</span>
                  <p class="font-medium">{{ selectedPart.quality_display }}</p>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Installation Information
                </h4>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Difficulty:</span>
                    <p class="font-medium">{{ selectedPart.difficulty_level.charAt(0).toUpperCase() + selectedPart.difficulty_level.slice(1) }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Installation Time:</span>
                    <p class="font-medium">{{ selectedPart.installation_time_display }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Warranty:</span>
                    <p class="font-medium">{{ selectedPart.warranty_display }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Weight:</span>
                    <p class="font-medium">{{ selectedPart.weight || 'Not specified' }}</p>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">
                  Compatibility
                </h4>
                <div class="space-y-2">
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Compatible Brands:</span>
                    <p class="font-medium">{{ selectedPart.compatible_brands?.join(', ') || 'Not specified' }}</p>
                  </div>
                  <div>
                    <span class="text-sm text-secondary-500 dark:text-secondary-400">Compatible Models:</span>
                    <p class="font-medium">{{ selectedPart.compatible_models?.join(', ') || 'Not specified' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Price and Actions -->
          <div class="mt-8 pt-6 border-t border-secondary-200 dark:border-secondary-700">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                  {{ selectedPart.formatted_price }}
                </span>
                <p class="text-sm text-secondary-500 dark:text-secondary-400">
                  {{ selectedPart.in_stock ? 'In Stock' : 'Out of Stock' }}
                </p>
              </div>
              <div class="flex space-x-4">
                <button 
                  class="btn-ghost"
                  @click="selectedPart = null"
                >
                  Close
                </button>
                <button 
                  class="btn-primary"
                  :disabled="!selectedPart.in_stock"
                >
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'

// Reactive data
const parts = ref([])
const featuredParts = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedManufacturer = ref('')
const sortBy = ref('name')
const currentPage = ref(1)
const totalPages = ref(1)
const totalParts = ref(0)
const selectedPart = ref(null)

// Filters
const categories = ref([])
const manufacturers = ref([])

// Computed
const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const loadParts = async () => {
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: 12,
      sort_by: sortBy.value,
      sort_order: 'asc'
    })

    if (selectedCategory.value) {
      params.append('category', selectedCategory.value)
    }

    if (selectedManufacturer.value) {
      params.append('manufacturer', selectedManufacturer.value)
    }

    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }

    const response = await fetch(`/api/car-parts?${params}`)
    const data = await response.json()

    if (data.success) {
      parts.value = data.data
      totalPages.value = data.pagination.last_page
      totalParts.value = data.pagination.total
      
      if (data.filters) {
        categories.value = data.filters.categories || []
        manufacturers.value = data.filters.manufacturers || []
      }
    }
  } catch (error) {
    console.error('Error loading parts:', error)
  }
}

const loadFeaturedParts = async () => {
  try {
    const response = await fetch('/api/car-parts/featured')
    const data = await response.json()

    if (data.success) {
      featuredParts.value = data.data
    }
  } catch (error) {
    console.error('Error loading featured parts:', error)
  }
}

const debouncedSearch = debounce(() => {
  currentPage.value = 1
  loadParts()
}, 500)

const filterParts = () => {
  currentPage.value = 1
  loadParts()
}

const sortParts = () => {
  currentPage.value = 1
  loadParts()
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    loadParts()
  }
}

const viewPart = (part) => {
  selectedPart.value = part
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedManufacturer.value = ''
  currentPage.value = 1
  loadParts()
}

const getQualityColor = (quality) => {
  const colors = {
    'oem': 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20',
    'premium': 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20',
    'standard': 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20',
    'economy': 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-900/20'
  }
  return colors[quality] || colors.standard
}

const getDifficultyColor = (difficulty) => {
  const colors = {
    'easy': 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20',
    'medium': 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20',
    'hard': 'text-orange-600 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/20',
    'professional': 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/20'
  }
  return colors[difficulty] || colors.medium
}

// Debounce utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Lifecycle
onMounted(() => {
  loadFeaturedParts()
  loadParts()
})

// Watch for page changes
watch(currentPage, () => {
  loadParts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
