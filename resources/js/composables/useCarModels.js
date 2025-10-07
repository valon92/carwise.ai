import { ref, computed, readonly } from 'vue'
import axios from 'axios'

// Global car models state
const carModels = ref([])
const carBrands = ref([])
const isLoading = ref(false)
const error = ref(null)

export function useCarModels() {
  /**
   * Load all car brands
   */
  const loadCarBrands = async (popularOnly = false) => {
    try {
      isLoading.value = true
      error.value = null
      
      const endpoint = popularOnly ? '/api/car-brands/popular' : '/api/car-brands'
      const response = await axios.get(endpoint)
      
      if (response.data.success) {
        carBrands.value = response.data.data
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load car brands' }
    } catch (err) {
      console.error('Error loading car brands:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car brands. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Load car models by brand ID
   */
  const loadCarModelsByBrand = async (brandId) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get(`/api/car-models/brand/${brandId}`)
      
      if (response.data.success) {
        carModels.value = response.data.data
        return { success: true, data: response.data.data, brand: response.data.brand }
      }
      
      return { success: false, message: 'Failed to load car models' }
    } catch (err) {
      console.error('Error loading car models:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Load car models by brand name
   */
  const loadCarModelsByBrandName = async (brandName) => {
    try {
      if (!brandName) {
        carModels.value = []
        return { success: true, data: [] }
      }
      
      // Find brand by name
      const brand = carBrands.value.find(b => b.name === brandName)
      if (!brand) {
        carModels.value = []
        return { success: false, message: 'Brand not found' }
      }
      
      return await loadCarModelsByBrand(brand.id)
    } catch (err) {
      console.error('Error loading car models by brand name:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    }
  }

  /**
   * Load car models by brand slug
   */
  const loadCarModelsByBrandSlug = async (brandSlug) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get(`/api/car-models/brand-slug/${brandSlug}`)
      
      if (response.data.success) {
        carModels.value = response.data.data
        return { success: true, data: response.data.data, brand: response.data.brand }
      }
      
      return { success: false, message: 'Failed to load car models' }
    } catch (err) {
      console.error('Error loading car models by brand slug:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Search car models
   */
  const searchCarModels = async (query, filters = {}) => {
    try {
      isLoading.value = true
      error.value = null
      
      const params = new URLSearchParams({ q: query, ...filters })
      const response = await axios.get(`/api/car-models/search?${params}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to search car models' }
    } catch (err) {
      console.error('Error searching car models:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error searching car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get popular car models
   */
  const loadPopularCarModels = async () => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get('/api/car-models/popular')
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load popular car models' }
    } catch (err) {
      console.error('Error loading popular car models:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading popular car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get car models by body type
   */
  const loadCarModelsByBodyType = async (bodyType) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get(`/api/car-models/body-type/${bodyType}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load car models by body type' }
    } catch (err) {
      console.error('Error loading car models by body type:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get car models by segment
   */
  const loadCarModelsBySegment = async (segment) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get(`/api/car-models/segment/${segment}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load car models by segment' }
    } catch (err) {
      console.error('Error loading car models by segment:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get car models by fuel type
   */
  const loadCarModelsByFuelType = async (fuelType) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get(`/api/car-models/fuel-type/${fuelType}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load car models by fuel type' }
    } catch (err) {
      console.error('Error loading car models by fuel type:', err)
      error.value = err.message
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car models. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get available body types
   */
  const loadBodyTypes = async () => {
    try {
      const response = await axios.get('/api/car-models/body-types')
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load body types' }
    } catch (err) {
      console.error('Error loading body types:', err)
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading body types. Please try again.' 
      }
    }
  }

  /**
   * Get available segments
   */
  const loadSegments = async () => {
    try {
      const response = await axios.get('/api/car-models/segments')
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load segments' }
    } catch (err) {
      console.error('Error loading segments:', err)
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading segments. Please try again.' 
      }
    }
  }

  /**
   * Get available fuel types
   */
  const loadFuelTypes = async () => {
    try {
      const response = await axios.get('/api/car-models/fuel-types')
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Failed to load fuel types' }
    } catch (err) {
      console.error('Error loading fuel types:', err)
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading fuel types. Please try again.' 
      }
    }
  }

  /**
   * Get a specific car model by ID
   */
  const getCarModelById = async (modelId) => {
    try {
      const response = await axios.get(`/api/car-models/${modelId}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Car model not found' }
    } catch (err) {
      console.error('Error getting car model by ID:', err)
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car model. Please try again.' 
      }
    }
  }

  /**
   * Get a specific car model by slug
   */
  const getCarModelBySlug = async (slug) => {
    try {
      const response = await axios.get(`/api/car-models/slug/${slug}`)
      
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      
      return { success: false, message: 'Car model not found' }
    } catch (err) {
      console.error('Error getting car model by slug:', err)
      return { 
        success: false, 
        message: err.response?.data?.message || 'Error loading car model. Please try again.' 
      }
    }
  }

  /**
   * Get brand by name
   */
  const getBrandByName = (brandName) => {
    return carBrands.value.find(brand => brand.name === brandName)
  }

  /**
   * Get brand by ID
   */
  const getBrandById = (brandId) => {
    return carBrands.value.find(brand => brand.id === brandId)
  }

  /**
   * Get model by name within current models
   */
  const getModelByName = (modelName) => {
    return carModels.value.find(model => model.name === modelName)
  }

  /**
   * Get model by ID within current models
   */
  const getModelById = (modelId) => {
    return carModels.value.find(model => model.id === modelId)
  }

  /**
   * Clear current models
   */
  const clearCarModels = () => {
    carModels.value = []
  }

  /**
   * Clear current brands
   */
  const clearCarBrands = () => {
    carBrands.value = []
  }

  /**
   * Clear all data
   */
  const clearAll = () => {
    carModels.value = []
    carBrands.value = []
    error.value = null
  }

  // Computed properties
  const totalBrands = computed(() => carBrands.value.length)
  const totalModels = computed(() => carModels.value.length)
  const popularBrands = computed(() => carBrands.value.filter(brand => brand.is_popular))
  const activeBrands = computed(() => carBrands.value.filter(brand => brand.is_active))
  const popularModels = computed(() => carModels.value.filter(model => model.is_popular))
  const activeModels = computed(() => carModels.value.filter(model => model.is_active))
  
  const modelsByBodyType = computed(() => {
    const grouped = {}
    carModels.value.forEach(model => {
      if (model.body_type) {
        if (!grouped[model.body_type]) {
          grouped[model.body_type] = []
        }
        grouped[model.body_type].push(model)
      }
    })
    return grouped
  })

  const modelsBySegment = computed(() => {
    const grouped = {}
    carModels.value.forEach(model => {
      if (model.segment) {
        if (!grouped[model.segment]) {
          grouped[model.segment] = []
        }
        grouped[model.segment].push(model)
      }
    })
    return grouped
  })

  const modelsByBrand = computed(() => {
    const grouped = {}
    carModels.value.forEach(model => {
      if (model.brand) {
        const brandName = model.brand.name
        if (!grouped[brandName]) {
          grouped[brandName] = []
        }
        grouped[brandName].push(model)
      }
    })
    return grouped
  })

  return {
    // State
    carModels: readonly(carModels),
    carBrands: readonly(carBrands),
    isLoading: readonly(isLoading),
    error: readonly(error),
    
    // Computed
    totalBrands,
    totalModels,
    popularBrands,
    activeBrands,
    popularModels,
    activeModels,
    modelsByBodyType,
    modelsBySegment,
    modelsByBrand,
    
    // Actions
    loadCarBrands,
    loadCarModelsByBrand,
    loadCarModelsByBrandName,
    loadCarModelsByBrandSlug,
    searchCarModels,
    loadPopularCarModels,
    loadCarModelsByBodyType,
    loadCarModelsBySegment,
    loadCarModelsByFuelType,
    loadBodyTypes,
    loadSegments,
    loadFuelTypes,
    getCarModelById,
    getCarModelBySlug,
    getBrandByName,
    getBrandById,
    getModelByName,
    getModelById,
    clearCarModels,
    clearCarBrands,
    clearAll
  }
}

