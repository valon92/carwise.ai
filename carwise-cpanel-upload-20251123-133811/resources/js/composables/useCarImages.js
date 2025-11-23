import { ref, computed } from 'vue'
import { carImagesAPI } from '../services/api'

export function useCarImages() {
  const carImage = ref(null)
  const carImages = ref([])
  const loading = ref(false)
  const error = ref(null)

  /**
   * Get primary image for a car
   */
  const getPrimaryImage = async (brand, model, year = null, color = null) => {
    try {
      loading.value = true
      error.value = null

      const response = await carImagesAPI.getPrimaryImage(brand, model, year, color)
      
      if (response.data.success) {
        carImage.value = response.data.image
        return response.data.image
      } else {
        // Try brand fallback
        const fallbackResponse = await carImagesAPI.getBrandFallback(brand)
        if (fallbackResponse.data.success) {
          carImage.value = fallbackResponse.data.image
          return fallbackResponse.data.image
        } else {
          // Use default image
          const defaultResponse = await carImagesAPI.getDefaultImage()
          carImage.value = defaultResponse.data.image
          return defaultResponse.data.image
        }
      }
    } catch (err) {
      console.error('Error getting primary car image:', err)
      error.value = err.message
      
      // Return default image on error
      try {
        const defaultResponse = await carImagesAPI.getDefaultImage()
        carImage.value = defaultResponse.data.image
        return defaultResponse.data.image
      } catch (defaultErr) {
        console.error('Error getting default image:', defaultErr)
        return null
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Get all images for a car
   */
  const getCarImages = async (brand, model, year = null, color = null) => {
    try {
      loading.value = true
      error.value = null

      const response = await carImagesAPI.getCarImages(brand, model, year, color)
      
      if (response.data.success) {
        carImages.value = response.data.images
        return response.data.images
      }
      
      return []
    } catch (err) {
      console.error('Error getting car images:', err)
      error.value = err.message
      return []
    } finally {
      loading.value = false
    }
  }

  /**
   * Get car image URL with fallback
   */
  const getCarImageUrl = async (brand, model, year = null, color = null) => {
    const image = await getPrimaryImage(brand, model, year, color)
    return image?.image_url || '/images/cars/default-car.svg'
  }

  /**
   * Get car thumbnail URL with fallback
   */
  const getCarThumbnailUrl = async (brand, model, year = null, color = null) => {
    const image = await getPrimaryImage(brand, model, year, color)
    return image?.thumbnail_url || image?.image_url || '/images/cars/default-car-thumb.png'
  }

  /**
   * Clear current car image data
   */
  const clearCarImage = () => {
    carImage.value = null
    carImages.value = []
    error.value = null
  }

  /**
   * Computed properties
   */
  const hasImage = computed(() => !!carImage.value)
  const hasImages = computed(() => carImages.value.length > 0)
  const currentImageUrl = computed(() => carImage.value?.image_url || '/images/cars/default-car.svg')
  const currentThumbnailUrl = computed(() => carImage.value?.thumbnail_url || carImage.value?.image_url || '/images/cars/default-car.svg')

  return {
    // State
    carImage,
    carImages,
    loading,
    error,
    
    // Computed
    hasImage,
    hasImages,
    currentImageUrl,
    currentThumbnailUrl,
    
    // Methods
    getPrimaryImage,
    getCarImages,
    getCarImageUrl,
    getCarThumbnailUrl,
    clearCarImage
  }
}
