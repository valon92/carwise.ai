<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-900 dark:via-secondary-800 dark:to-primary-900">
    <!-- Header Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600/10 to-secondary-600/10"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <h1 class="text-4xl md:text-5xl font-bold text-secondary-900 dark:text-white mb-4">
            My Cars
          </h1>
          <p class="text-xl text-secondary-600 dark:text-secondary-300 mb-8 max-w-2xl mx-auto">
            Manage your vehicles and track their maintenance
          </p>
          <button 
            @click="showAddCarModal = true"
            class="btn-primary text-lg px-8 py-3 shadow-lg hover:shadow-xl transition-all duration-300"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add New Car
          </button>
        </div>
      </div>
    </div>

    <!-- Cars Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
      <div v-if="cars.length > 0" class="grid grid-cols-1 gap-8">
        <div 
          v-for="car in cars" 
          :key="car.id"
          class="card glass hover:shadow-2xl transition-all duration-300 group"
        >
          <div class="p-6">
            <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-2">
              {{ car.brand }} {{ car.model }}
            </h3>
            <p class="text-lg text-secondary-600 dark:text-secondary-400 mb-4">
              {{ car.year }}
            </p>
            <div class="flex space-x-4">
              <button 
                @click="editCar(car)"
                class="btn-secondary"
              >
                Edit
              </button>
              <button 
                @click="deleteCar(car.id)"
                class="btn-danger"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="text-6xl mb-4">🚗</div>
        <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">
          No cars added yet
        </h3>
        <p class="text-secondary-600 dark:text-secondary-400 mb-8">
          Add your first car to get started with CarWise.ai
        </p>
        <button 
          @click="showAddCarModal = true"
          class="btn-primary"
        >
          Add Your First Car
        </button>
      </div>
    </div>

    <!-- Add Car Modal -->
    <div v-if="showAddCarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-secondary-800 rounded-lg p-6 w-full max-w-md mx-4">
        <h2 class="text-xl font-bold mb-4">Add New Car</h2>
        <form @submit.prevent="saveCar" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Brand</label>
            <input 
              v-model="carForm.brand" 
              type="text" 
              class="input" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Model</label>
            <input 
              v-model="carForm.model" 
              type="text" 
              class="input" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Year</label>
            <input 
              v-model="carForm.year" 
              type="number" 
              class="input" 
              required 
            />
          </div>
          <div class="flex space-x-4">
            <button type="submit" class="btn-primary flex-1">
              Save
            </button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { carsAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'MyCarsSimple',
  setup() {
    const router = useRouter()
    const { user } = useAuth()
    
    const cars = ref([])
    const showAddCarModal = ref(false)
    const editingCar = ref(null)
    const loading = ref(false)
    const carForm = ref({
      brand: '',
      model: '',
      year: '',
    })

    const loadCars = async () => {
      try {
        loading.value = true
        const response = await carsAPI.getAll()
        if (response.data.success) {
          cars.value = response.data.cars
        } else {
          console.error('Failed to load cars:', response.data.message)
        }
      } catch (error) {
        console.error('Error loading cars:', error)
      } finally {
        loading.value = false
      }
    }

    const editCar = (car) => {
      editingCar.value = car
      carForm.value = {
        brand: car.brand || '',
        model: car.model || '',
        year: car.year || '',
      }
      showAddCarModal.value = true
    }

    const deleteCar = async (carId) => {
      if (confirm('Are you sure you want to delete this car?')) {
        try {
          const response = await carsAPI.delete(carId)
          if (response.data.success) {
            cars.value = cars.value.filter(car => car.id !== carId)
            alert('Car deleted successfully!')
          } else {
            alert('Error deleting car. Please try again.')
          }
        } catch (error) {
          console.error('Error deleting car:', error)
          alert('Error deleting car. Please try again.')
        }
      }
    }

    const saveCar = async () => {
      try {
        loading.value = true
        let response
        
        if (editingCar.value) {
          response = await carsAPI.update(editingCar.value.id, carForm.value)
        } else {
          response = await carsAPI.create(carForm.value)
        }
        
        if (response.data.success) {
          await loadCars()
          closeModal()
          alert('Car saved successfully!')
        } else {
          alert('Error saving car. Please try again.')
        }
      } catch (error) {
        console.error('Error saving car:', error)
        alert('Error saving car. Please try again.')
      } finally {
        loading.value = false
      }
    }

    const closeModal = () => {
      showAddCarModal.value = false
      editingCar.value = null
      carForm.value = {
        brand: '',
        model: '',
        year: '',
      }
    }

    onMounted(() => {
      loadCars()
    })

    return {
      cars,
      showAddCarModal,
      editingCar,
      loading,
      carForm,
      editCar,
      deleteCar,
      saveCar,
      closeModal,
    }
  }
}
</script>



