<template>
  <section class="relative py-24 overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
    <!-- Animated Background Effects -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 opacity-90">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute top-0 left-0 w-full h-full">
        <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-20 right-20 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-10 left-1/3 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
      </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-16">
        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-6">
          <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse mr-3"></div>
          <span class="text-white/90 text-sm font-medium">{{ listingTier === 'luxury' ? 'Verified listings' : 'Latest Releases' }}</span>
        </div>
        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
          {{ listingTier === 'luxury' ? 'Luxury vehicles for sale' : 'New Vehicles in Market' }}
        </h2>
        <p class="text-xl md:text-2xl text-white/80 max-w-3xl mx-auto leading-relaxed">
          {{ listingTier === 'luxury'
            ? 'Real dealer inventory, sorted by price — continue on the seller’s secure site to purchase.'
            : 'Discover the latest models from top manufacturers' }}
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="w-16 h-16 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
      </div>

      <!-- Carousel -->
      <div v-else-if="vehicles.length > 0" class="relative">
        <!-- Carousel Container -->
        <div 
          ref="carouselRef"
          class="flex gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory scroll-smooth pb-4"
          @scroll="updateScrollPosition"
          style="scrollbar-width: none; -ms-overflow-style: none;"
        >
          <!-- Vehicle Cards -->
          <div
            v-for="(vehicle, index) in vehicles"
            :key="vehicle.id"
            class="flex-shrink-0 w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.67rem)] lg:w-[calc(16.666%-0.83rem)] snap-start"
          >
            <div
              @click="openVehicleModal(vehicle)"
              class="group relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 dark:border-slate-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:scale-105"
            >
              <!-- Image Container -->
              <div class="relative h-48 overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900">
                <img
                  :src="vehicle.image_url"
                  :alt="vehicle.name"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  @error="handleImageError"
                />
                <div
                  class="absolute inset-x-2 top-2 flex justify-between items-start gap-2 pointer-events-none z-[1]"
                >
                  <span
                    class="shrink min-w-0 max-w-[55%] px-2 py-0.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full text-white text-xs font-semibold capitalize truncate shadow-sm"
                    :title="cardBadgeLabel(vehicle)"
                  >
                    {{ cardBadgeLabel(vehicle) }}
                  </span>
                  <span
                    class="shrink-0 max-w-[42%] px-2 py-0.5 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-semibold border border-white/30 truncate text-right shadow-sm"
                    :title="vehicle.manufacturer"
                  >
                    {{ vehicle.manufacturer }}
                  </span>
                </div>
              </div>

              <!-- Content -->
              <div class="p-4 pt-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 text-center break-words px-0.5">
                  {{ vehicle.name }}
                </h3>
                
                <!-- Price -->
                <div class="text-center">
                  <p class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ formatPrice(vehicle.price, vehicle.currency) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button
          v-if="canScrollLeft"
          @click="scrollLeft"
          class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all duration-300 z-10 border border-white/20 shadow-lg hover:shadow-xl transform hover:scale-110"
          aria-label="Scroll left"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <button
          v-if="canScrollRight"
          @click="scrollRight"
          class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all duration-300 z-10 border border-white/20 shadow-lg hover:shadow-xl transform hover:scale-110"
          aria-label="Scroll right"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading" class="text-center py-20 max-w-xl mx-auto px-4">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-full mb-6">
          <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
          </svg>
        </div>
        <p class="text-white/80 text-lg font-medium mb-2">{{ emptyTitle }}</p>
        <p class="text-white/60 text-sm leading-relaxed">{{ emptySubtitle }}</p>
      </div>
    </div>

    <!-- Vehicle Details Modal -->
    <div
      v-if="selectedVehicle"
      @click.self="closeModal"
      class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @keydown.esc="closeModal"
    >
      <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="relative">
          <!-- Close Button -->
          <button
            @click="closeModal"
            class="absolute top-4 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white z-10 transition-all border border-white/20 shadow-lg hover:scale-110"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <!-- Image -->
          <div class="relative h-80 overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900">
            <img
              :src="selectedVehicle.image_url"
              :alt="selectedVehicle.name"
              class="w-full h-full object-cover"
              @error="handleImageError"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6">
              <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full text-white text-sm font-semibold">
                  {{ selectedVehicle.manufacturer }}
                </span>
                <span class="px-3 py-1 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full text-white text-sm font-semibold">
                  {{ selectedVehicle.year }}
                </span>
              </div>
              <h2 class="text-3xl md:text-4xl font-bold text-white">
                {{ selectedVehicle.name }}
              </h2>
            </div>
          </div>

          <!-- Content -->
          <div class="p-8">
            <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
              {{ selectedVehicle.description }}
            </p>

            <!-- Specifications Grid -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
              <div class="space-y-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                  </div>
                  Technical Specifications
                </h3>
                <div class="space-y-3">
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Engine Type</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.engine_type }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Engine Size</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.engine_size }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Horsepower</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.horsepower }} HP</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Torque</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.torque }} Nm</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Transmission</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.transmission }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Drivetrain</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.drivetrain }}</span>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  Vehicle Details
                </h3>
                <div class="space-y-3">
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Body Type</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.body_type }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Seats</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.seats }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Doors</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.doors }}</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Fuel Type</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.fuel_type }}</span>
                  </div>
                  <div v-if="selectedVehicle.fuel_consumption" class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Fuel Consumption</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.fuel_consumption }} L/100km</span>
                  </div>
                  <div v-if="selectedVehicle.co2_emissions" class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">CO₂ Emissions</span>
                    <span class="text-slate-900 dark:text-white font-semibold">{{ selectedVehicle.co2_emissions }} g/km</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Features -->
            <div v-if="selectedVehicle.features && selectedVehicle.features.length > 0" class="mb-8">
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                Key Features
              </h3>
              <div class="grid md:grid-cols-2 gap-3">
                <div
                  v-for="(feature, index) in selectedVehicle.features"
                  :key="index"
                  class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl"
                >
                  <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-slate-700 dark:text-slate-300">{{ feature }}</span>
                </div>
              </div>
            </div>

            <!-- Additional Specifications -->
            <div v-if="selectedVehicle.specifications" class="mb-8">
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                Additional Specifications
              </h3>
              <div class="grid md:grid-cols-2 gap-3">
                <div
                  v-for="(value, key) in selectedVehicle.specifications"
                  :key="key"
                  class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl"
                >
                  <span class="text-slate-600 dark:text-slate-400 font-medium capitalize">{{ key.replace('_', ' ') }}</span>
                  <span class="text-slate-900 dark:text-white font-semibold">{{ value }}</span>
                </div>
              </div>
            </div>

            <!-- Price -->
            <div v-if="selectedVehicle.price" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-2xl">
              <div>
                <p class="text-white/80 text-sm mb-1">Listed price</p>
                <p class="text-3xl font-bold text-white">{{ formatPrice(selectedVehicle.price, selectedVehicle.currency) }}</p>
              </div>
              <a
                v-if="listingPurchaseUrl(selectedVehicle)"
                :href="listingPurchaseUrl(selectedVehicle)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex justify-center px-8 py-3 bg-white text-blue-600 rounded-2xl font-semibold hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl text-center"
              >
                Continue to seller
              </a>
              <span
                v-else
                class="inline-flex justify-center px-8 py-3 bg-white/20 text-white rounded-2xl font-semibold text-center text-sm"
              >
                Listing link unavailable
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../services/api'

const vehicles = ref([])
const loading = ref(true)
const listSource = ref('')
const listingTier = ref('')
const apiMessage = ref('')
const selectedVehicle = ref(null)

const emptyTitle = computed(() => {
  if (listSource.value === 'not_configured') {
    return 'Live inventory is not configured'
  }
  if (listSource.value === 'error') {
    return 'Unable to load listings'
  }
  if (listSource.value === 'marketcheck') {
    return 'No live listings to show right now'
  }
  return 'No vehicles available at the moment.'
})

const emptySubtitle = computed(() => {
  if (apiMessage.value) {
    return apiMessage.value
  }
  if (listSource.value === 'not_configured') {
    return 'Add your MarketCheck API key and a US ZIP (or latitude/longitude) in .env, then clear config cache.'
  }
  return 'Please check back later or contact support.'
})
const carouselRef = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(true)

const fetchVehicles = async () => {
  try {
    loading.value = true
    console.log('Fetching vehicles from /latest-vehicles/featured')
    const response = await api.get('/latest-vehicles/featured')
    console.log('API Response:', response)
    console.log('API Response Data:', response.data)
    
    if (response && response.data) {
      listSource.value = response.data.source || ''
      listingTier.value = response.data.listing_tier || ''
      apiMessage.value = response.data.message || ''
      if (response.data.success && Array.isArray(response.data.data)) {
        vehicles.value = response.data.data
        console.log('Vehicles loaded successfully:', vehicles.value.length)
      } else if (Array.isArray(response.data)) {
        // Handle case where API returns array directly
        vehicles.value = response.data
        console.log('Vehicles loaded (direct array):', vehicles.value.length)
      } else {
        console.warn('API response format unexpected:', response.data)
        vehicles.value = []
      }
    } else {
      console.warn('No response data received')
      vehicles.value = []
    }
  } catch (error) {
    console.error('Error fetching vehicles:', error)
    console.error('Error details:', error.response?.data || error.message)
    console.error('Error stack:', error.stack)
    vehicles.value = []
    listSource.value = 'error'
    listingTier.value = ''
    apiMessage.value = error.response?.data?.message || 'Could not load vehicle listings.'
  } finally {
    loading.value = false
    console.log('Loading complete. Vehicles count:', vehicles.value.length)
  }
}

const updateScrollPosition = () => {
  if (!carouselRef.value) return
  
  const { scrollLeft, scrollWidth, clientWidth } = carouselRef.value
  canScrollLeft.value = scrollLeft > 0
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10
}

const listingPurchaseUrl = (vehicle) => {
  if (!vehicle) return null
  return vehicle.cta_url || vehicle.vdp_url || null
}

/** Avoid misleading "Live" on DB demos; real MarketCheck rows use inventory_type when present. */
const cardBadgeLabel = (vehicle) => {
  if (!vehicle) return 'Featured'
  if (listSource.value === 'marketcheck') {
    const t = vehicle.inventory_type
    if (t != null && String(t).trim() !== '') {
      return String(t).trim()
    }
    return 'Listed'
  }
  return 'Featured'
}

const formatPrice = (price, currency = 'EUR') => {
  if (!price) return 'N/A'
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  const cur = currency || 'EUR'
  const sym = cur === 'USD' ? '$' : '€'
  if (numPrice >= 1000000) {
    return `${sym}${(numPrice / 1000000).toFixed(1)}M`
  }
  if (numPrice >= 1000) {
    return `${sym}${(numPrice / 1000).toFixed(0)}K`
  }
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: cur,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(numPrice)
}

const scrollLeft = () => {
  if (carouselRef.value) {
    const cardWidth = carouselRef.value.querySelector('.flex-shrink-0')?.offsetWidth || 200
    carouselRef.value.scrollBy({ left: -cardWidth * 6, behavior: 'smooth' })
  }
}

const scrollRight = () => {
  if (carouselRef.value) {
    const cardWidth = carouselRef.value.querySelector('.flex-shrink-0')?.offsetWidth || 200
    carouselRef.value.scrollBy({ left: cardWidth * 6, behavior: 'smooth' })
  }
}

const openVehicleModal = (vehicle) => {
  selectedVehicle.value = vehicle
  document.body.style.overflow = 'hidden'
}

const closeModal = () => {
  selectedVehicle.value = null
  document.body.style.overflow = ''
}

const handleImageError = (event) => {
  // Prevent infinite loop - if already a placeholder or data URL, stop
  if (event.target.src.includes('placeholder') || event.target.src.startsWith('data:') || event.target.dataset.fallback) {
    event.target.style.display = 'none'
    return
  }
  
  // Mark as fallback to prevent retry
  event.target.dataset.fallback = 'true'
  
  // Use a simple inline SVG placeholder instead of external URL
  const svgPlaceholder = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(`
    <svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
      <rect width="800" height="600" fill="#e5e7eb"/>
      <text x="400" y="300" font-family="Arial, sans-serif" font-size="24" fill="#6b7280" text-anchor="middle" dominant-baseline="middle">Vehicle Image</text>
    </svg>
  `)
  
  event.target.src = svgPlaceholder
  event.target.onerror = null // Prevent further error handling
}

onMounted(() => {
  fetchVehicles()
  if (carouselRef.value) {
    carouselRef.value.addEventListener('scroll', updateScrollPosition)
    updateScrollPosition()
  }
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}
</style>
