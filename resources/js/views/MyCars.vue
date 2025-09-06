<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900">My Cars</h1>
          <p class="text-xl text-gray-600 mt-2">Manage your vehicles and view diagnosis history</p>
        </div>
        <button 
          @click="showAddCarModal = true"
          class="btn-primary"
        >
          Add New Car
        </button>
      </div>

      <!-- Cars Grid -->
      <div v-if="cars.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div 
          v-for="car in cars" 
          :key="car.id"
          class="card hover:shadow-lg transition-shadow duration-200"
        >
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-xl font-semibold text-gray-900">{{ car.brand }} {{ car.model }}</h3>
              <p class="text-gray-600">{{ car.year }}</p>
              <p class="text-sm text-gray-500">VIN: {{ car.vin }}</p>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="editCar(car)"
                class="text-primary-600 hover:text-primary-800"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button 
                @click="deleteCar(car.id)"
                class="text-red-600 hover:text-red-800"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Car Stats -->
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-primary-600">{{ car.diagnosisCount }}</div>
              <div class="text-sm text-gray-600">Diagnoses</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ car.lastDiagnosis }}</div>
              <div class="text-sm text-gray-600">Last Check</div>
            </div>
          </div>

          <!-- Recent Diagnoses -->
          <div class="space-y-2">
            <h4 class="text-sm font-medium text-gray-900">Recent Diagnoses</h4>
            <div v-if="car.recentDiagnoses && car.recentDiagnoses.length > 0" class="space-y-1">
              <div 
                v-for="diagnosis in car.recentDiagnoses.slice(0, 2)" 
                :key="diagnosis.id"
                class="text-sm text-gray-600 flex justify-between items-center"
              >
                <span>{{ diagnosis.problem }}</span>
                <span class="text-xs text-gray-500">{{ diagnosis.date }}</span>
              </div>
            </div>
            <div v-else class="text-sm text-gray-500">
              No diagnoses yet
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex space-x-2">
              <router-link 
                :to="`/diagnose?car=${car.id}`"
                class="flex-1 btn-primary text-center text-sm py-2"
              >
                Diagnose
              </router-link>
              <button 
                @click="viewCarHistory(car)"
                class="flex-1 btn-secondary text-sm py-2"
              >
                History
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No cars added yet</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by adding your first car.</p>
        <div class="mt-6">
          <button 
            @click="showAddCarModal = true"
            class="btn-primary"
          >
            Add Your First Car
          </button>
        </div>
      </div>

      <!-- Add/Edit Car Modal -->
      <div v-if="showAddCarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingCar ? 'Edit Car' : 'Add New Car' }}
            </h3>
            
            <form @submit.prevent="saveCar" class="space-y-4">
              <div>
                <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                <input 
                  id="brand"
                  v-model="carForm.brand"
                  type="text"
                  required
                  class="input-field mt-1"
                  placeholder="e.g., Toyota"
                />
              </div>

              <div>
                <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                <input 
                  id="model"
                  v-model="carForm.model"
                  type="text"
                  required
                  class="input-field mt-1"
                  placeholder="e.g., Camry"
                />
              </div>

              <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <input 
                  id="year"
                  v-model="carForm.year"
                  type="number"
                  required
                  min="1900"
                  :max="new Date().getFullYear() + 1"
                  class="input-field mt-1"
                  placeholder="e.g., 2020"
                />
              </div>

              <div>
                <label for="vin" class="block text-sm font-medium text-gray-700">VIN (Optional)</label>
                <input 
                  id="vin"
                  v-model="carForm.vin"
                  type="text"
                  class="input-field mt-1"
                  placeholder="Vehicle Identification Number"
                />
              </div>

              <div class="flex justify-end space-x-3 pt-4">
                <button 
                  type="button"
                  @click="closeModal"
                  class="btn-secondary"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="btn-primary"
                >
                  {{ editingCar ? 'Update' : 'Add' }} Car
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { carsAPI } from '../services/api'

export default {
  name: 'MyCars',
  setup() {
    const cars = ref([])
    const showAddCarModal = ref(false)
    const editingCar = ref(null)
    const carForm = ref({
      brand: '',
      model: '',
      year: '',
      vin: ''
    })

    const loadCars = async () => {
      try {
        const response = await carsAPI.getAll()
        if (response.data.success) {
          cars.value = response.data.cars
        }
      } catch (error) {
        console.error('Error loading cars:', error)
      }
    }

    const editCar = (car) => {
      editingCar.value = car
      carForm.value = {
        brand: car.brand,
        model: car.model,
        year: car.year,
        vin: car.vin
      }
      showAddCarModal.value = true
    }

    const deleteCar = async (carId) => {
      if (confirm('Are you sure you want to delete this car?')) {
        try {
          const response = await carsAPI.delete(carId)
          if (response.data.success) {
            cars.value = cars.value.filter(car => car.id !== carId)
          }
        } catch (error) {
          console.error('Error deleting car:', error)
          alert('Error deleting car. Please try again.')
        }
      }
    }

    const saveCar = async () => {
      try {
        if (editingCar.value) {
          // Update existing car
          const response = await carsAPI.update(editingCar.value.id, carForm.value)
          if (response.data.success) {
            const index = cars.value.findIndex(car => car.id === editingCar.value.id)
            if (index !== -1) {
              cars.value[index] = response.data.car
            }
          }
        } else {
          // Add new car
          const response = await carsAPI.create(carForm.value)
          if (response.data.success) {
            cars.value.push(response.data.car)
          }
        }
        
        closeModal()
      } catch (error) {
        console.error('Error saving car:', error)
        alert('Error saving car. Please try again.')
      }
    }

    const closeModal = () => {
      showAddCarModal.value = false
      editingCar.value = null
      carForm.value = {
        brand: '',
        model: '',
        year: '',
        vin: ''
      }
    }

    const viewCarHistory = (car) => {
      // Navigate to car history page or show modal
      console.log('View history for car:', car.id)
    }

    onMounted(() => {
      loadCars()
    })

    return {
      cars,
      showAddCarModal,
      editingCar,
      carForm,
      editCar,
      deleteCar,
      saveCar,
      closeModal,
      viewCarHistory
    }
  }
}
</script>
