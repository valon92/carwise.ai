import { ref, computed } from 'vue'
import { carsAPI } from '../services/api'

// Global cars state
const cars = ref([])
const statistics = ref({})
const isLoading = ref(false)

export function useCars() {
  const loadCars = async () => {
    try {
      isLoading.value = true
      const response = await carsAPI.getAll()
      
      if (response.data.success) {
        cars.value = response.data.cars
        return { success: true, cars: response.data.cars }
      }
      
      return { success: false, message: 'Failed to load cars' }
    } catch (error) {
      console.error('Error loading cars:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error loading cars. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const loadStatistics = async () => {
    try {
      const response = await carsAPI.statistics()
      
      if (response.data.success) {
        statistics.value = response.data.statistics
        return { success: true, statistics: response.data.statistics }
      }
      
      return { success: false, message: 'Failed to load statistics' }
    } catch (error) {
      console.error('Error loading statistics:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error loading statistics.' 
      }
    }
  }

  const createCar = async (carData) => {
    try {
      isLoading.value = true
      const response = await carsAPI.create(carData)
      
      if (response.data.success) {
        cars.value.push(response.data.car)
        
        if (window.$notify) {
          window.$notify.success('Car Added', 'New car has been added to your garage')
        }
        
        return { success: true, car: response.data.car }
      }
      
      return { success: false, message: response.data.message || 'Failed to add car' }
    } catch (error) {
      console.error('Error creating car:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error adding car. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const updateCar = async (carId, carData) => {
    try {
      isLoading.value = true
      const response = await carsAPI.update(carId, carData)
      
      if (response.data.success) {
        const index = cars.value.findIndex(car => car.id === carId)
        if (index !== -1) {
          cars.value[index] = response.data.car
        }
        
        if (window.$notify) {
          window.$notify.success('Car Updated', 'Car information has been updated successfully')
        }
        
        return { success: true, car: response.data.car }
      }
      
      return { success: false, message: response.data.message || 'Failed to update car' }
    } catch (error) {
      console.error('Error updating car:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error updating car. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const deleteCar = async (carId) => {
    try {
      isLoading.value = true
      const response = await carsAPI.delete(carId)
      
      if (response.data.success) {
        cars.value = cars.value.filter(car => car.id !== carId)
        
        if (window.$notify) {
          window.$notify.success('Car Deleted', 'Car has been removed from your garage')
        }
        
        return { success: true }
      }
      
      return { success: false, message: response.data.message || 'Failed to delete car' }
    } catch (error) {
      console.error('Error deleting car:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error deleting car. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const getCarById = (carId) => {
    return cars.value.find(car => car.id === carId)
  }

  // Computed properties
  const totalCars = computed(() => cars.value.length)
  const activeCars = computed(() => cars.value.filter(car => car.status === 'active').length)
  const carsByBrand = computed(() => {
    const brands = {}
    cars.value.forEach(car => {
      brands[car.brand] = (brands[car.brand] || 0) + 1
    })
    return brands
  })

  return {
    // State
    cars: readonly(cars),
    statistics: readonly(statistics),
    isLoading: readonly(isLoading),
    
    // Computed
    totalCars,
    activeCars,
    carsByBrand,
    
    // Actions
    loadCars,
    loadStatistics,
    createCar,
    updateCar,
    deleteCar,
    getCarById
  }
}
