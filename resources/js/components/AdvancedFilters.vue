<template>
  <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm border border-gray-200 dark:border-secondary-700">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 dark:border-secondary-700">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Advanced Filters</h3>
        <div class="flex items-center space-x-2">
          <button 
            @click="clearAllFilters"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            Clear All
          </button>
          <button 
            @click="toggleExpanded"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <svg 
              class="w-5 h-5 transition-transform duration-200" 
              :class="{ 'rotate-180': isExpanded }"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Filters Content -->
    <div v-if="isExpanded" class="p-4 space-y-6">
      <!-- Price Range -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Price Range
        </label>
        <div class="space-y-3">
          <div class="flex items-center space-x-4">
            <div class="flex-1">
              <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Min Price</label>
              <input 
                v-model.number="filters.priceRange.min"
                type="number"
                min="0"
                step="0.01"
                placeholder="0"
                class="w-full px-3 py-2 border border-gray-300 dark:border-secondary-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
              />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Max Price</label>
              <input 
                v-model.number="filters.priceRange.max"
                type="number"
                min="0"
                step="0.01"
                placeholder="1000"
                class="w-full px-3 py-2 border border-gray-300 dark:border-secondary-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
              />
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <input 
              v-model="filters.priceRange.min"
              type="range"
              min="0"
              max="1000"
              step="1"
              class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
            />
            <span class="text-sm text-gray-600 dark:text-gray-400">${{ filters.priceRange.min || 0 }}</span>
          </div>
          <div class="flex items-center space-x-2">
            <input 
              v-model="filters.priceRange.max"
              type="range"
              min="0"
              max="1000"
              step="1"
              class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
            />
            <span class="text-sm text-gray-600 dark:text-gray-400">${{ filters.priceRange.max || 1000 }}</span>
          </div>
        </div>
      </div>

      <!-- Rating -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Minimum Rating
        </label>
        <div class="space-y-2">
          <div class="flex items-center space-x-2">
            <input 
              v-model="filters.rating"
              type="range"
              min="0"
              max="5"
              step="0.1"
              class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
            />
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ filters.rating || 0 }} ⭐</span>
          </div>
          <div class="flex items-center space-x-4">
            <button 
              v-for="rating in [0, 1, 2, 3, 4, 5]"
              :key="rating"
              @click="filters.rating = rating"
              :class="[
                'px-3 py-1 rounded-full text-sm font-medium transition-colors duration-200',
                filters.rating >= rating 
                  ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' 
                  : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
              ]"
            >
              {{ rating }}+ ⭐
            </button>
          </div>
        </div>
      </div>

      <!-- Brand/Manufacturer -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Brand/Manufacturer
        </label>
        <div class="space-y-2">
          <input 
            v-model="brandSearch"
            type="text"
            placeholder="Search brands..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-secondary-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
          />
          <div class="max-h-32 overflow-y-auto space-y-1">
            <label 
              v-for="brand in filteredBrands"
              :key="brand"
              class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 p-2 rounded"
            >
              <input 
                v-model="filters.brands"
                :value="brand"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ brand }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Category -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Category
        </label>
        <div class="space-y-2">
          <input 
            v-model="categorySearch"
            type="text"
            placeholder="Search categories..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-secondary-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
          />
          <div class="max-h-32 overflow-y-auto space-y-1">
            <label 
              v-for="category in filteredCategories"
              :key="category"
              class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 p-2 rounded"
            >
              <input 
                v-model="filters.categories"
                :value="category"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ category }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Condition -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Condition
        </label>
        <div class="space-y-2">
          <label 
            v-for="condition in conditions"
            :key="condition.value"
            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 p-2 rounded"
          >
            <input 
              v-model="filters.conditions"
              :value="condition.value"
              type="checkbox"
              class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ condition.label }}</span>
          </label>
        </div>
      </div>

      <!-- Availability -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Availability
        </label>
        <div class="space-y-2">
          <label 
            v-for="availability in availabilityOptions"
            :key="availability.value"
            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 p-2 rounded"
          >
            <input 
              v-model="filters.availability"
              :value="availability.value"
              type="checkbox"
              class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ availability.label }}</span>
          </label>
        </div>
      </div>

      <!-- Shipping Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Shipping Options
        </label>
        <div class="space-y-2">
          <label 
            v-for="shipping in shippingOptions"
            :key="shipping.value"
            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 p-2 rounded"
          >
            <input 
              v-model="filters.shipping"
              :value="shipping.value"
              type="checkbox"
              class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ shipping.label }}</span>
          </label>
        </div>
      </div>

      <!-- Sort Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Sort By
        </label>
        <select 
          v-model="filters.sortBy"
          class="w-full px-3 py-2 border border-gray-300 dark:border-secondary-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
        >
          <option value="relevance">Relevance</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="rating_desc">Rating: High to Low</option>
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="name_asc">Name: A to Z</option>
          <option value="name_desc">Name: Z to A</option>
        </select>
      </div>

      <!-- Apply Filters Button -->
      <div class="pt-4 border-t border-gray-200 dark:border-secondary-700">
        <button 
          @click="applyFilters"
          class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
        >
          Apply Filters
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isExpanded: {
    type: Boolean,
    default: false
  },
  brands: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:filters', 'clear-filters', 'apply-filters'])

// Local state
const isExpanded = ref(props.isExpanded)
const brandSearch = ref('')
const categorySearch = ref('')

// Filters state
const filters = ref({
  priceRange: {
    min: null,
    max: null
  },
  rating: null,
  brands: [],
  categories: [],
  conditions: [],
  availability: [],
  shipping: [],
  sortBy: 'relevance'
})

// Options
const conditions = ref([
  { value: 'new', label: 'New' },
  { value: 'used', label: 'Used' },
  { value: 'refurbished', label: 'Refurbished' },
  { value: 'remanufactured', label: 'Remanufactured' }
])

const availabilityOptions = ref([
  { value: 'in_stock', label: 'In Stock' },
  { value: 'out_of_stock', label: 'Out of Stock' },
  { value: 'pre_order', label: 'Pre-order' },
  { value: 'discontinued', label: 'Discontinued' }
])

const shippingOptions = ref([
  { value: 'free_shipping', label: 'Free Shipping' },
  { value: 'fast_shipping', label: 'Fast Shipping (1-2 days)' },
  { value: 'standard_shipping', label: 'Standard Shipping (3-5 days)' },
  { value: 'express_shipping', label: 'Express Shipping (Next day)' }
])

// Computed
const filteredBrands = computed(() => {
  if (!brandSearch.value) return props.brands
  return props.brands.filter(brand => 
    brand.toLowerCase().includes(brandSearch.value.toLowerCase())
  )
})

const filteredCategories = computed(() => {
  if (!categorySearch.value) return props.categories
  return props.categories.filter(category => 
    category.toLowerCase().includes(categorySearch.value.toLowerCase())
  )
})

// Methods
const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value
}

const clearAllFilters = () => {
  filters.value = {
    priceRange: {
      min: null,
      max: null
    },
    rating: null,
    brands: [],
    categories: [],
    conditions: [],
    availability: [],
    shipping: [],
    sortBy: 'relevance'
  }
  emit('clear-filters')
}

const applyFilters = () => {
  emit('apply-filters', filters.value)
}

// Watch for filter changes
watch(filters, (newFilters) => {
  emit('update:filters', newFilters)
}, { deep: true })

// Watch for expanded state changes
watch(() => props.isExpanded, (newValue) => {
  isExpanded.value = newValue
})
</script>


