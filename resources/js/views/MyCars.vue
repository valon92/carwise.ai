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
            Manage your vehicles, track maintenance, and view diagnosis history all in one place
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

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">
            {{ statistics.total_cars || 0 }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Total Cars</div>
        </div>
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
            {{ statistics.active_cars || 0 }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Active Cars</div>
        </div>
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
            {{ statistics.total_diagnoses || 0 }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Total Diagnoses</div>
        </div>
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
            {{ Math.round(statistics.average_age || 0) }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Avg. Age (years)</div>
        </div>
      </div>
    </div>

    <!-- Cars Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
      <div v-if="cars.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="car in cars" 
          :key="car.id"
          class="card glass hover:shadow-2xl transition-all duration-300 group"
        >
          <!-- Car Header -->
          <div class="flex justify-between items-start mb-6">
            <div class="flex-1">
              <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-1">
                {{ car.display_name }}
              </h3>
              <p class="text-lg text-secondary-600 dark:text-secondary-400 mb-2">
                {{ car.year }} • {{ car.age }} years old
              </p>
              <div class="flex items-center space-x-4 text-sm text-secondary-500 dark:text-secondary-500">
                <span v-if="car.vin" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  {{ car.vin }}
                </span>
                <span v-if="car.license_plate" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3m0 0h8"></path>
                  </svg>
                  {{ car.license_plate }}
                </span>
              </div>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="editCar(car)"
                class="p-2 rounded-lg text-secondary-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors duration-200"
                title="Edit Car"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button 
                @click="deleteCar(car.id)"
                class="p-2 rounded-lg text-secondary-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                title="Delete Car"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Car Details -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div v-if="car.fuel_type" class="text-center p-3 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
              <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-1">Fuel Type</div>
              <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                {{ car.fuel_type }}
              </div>
            </div>
            <div v-if="car.transmission" class="text-center p-3 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
              <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-1">Transmission</div>
              <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                {{ car.transmission }}
              </div>
            </div>
            <div v-if="car.mileage" class="text-center p-3 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
              <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-1">Mileage</div>
              <div class="text-lg font-semibold text-secondary-900 dark:text-white">
                {{ car.mileage.toLocaleString() }} km
              </div>
            </div>
            <div v-if="car.color" class="text-center p-3 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
              <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-1">Color</div>
              <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                {{ car.color }}
              </div>
            </div>
          </div>

          <!-- Diagnosis Stats -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="text-center p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
              <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mb-1">
                {{ car.diagnosis_count }}
              </div>
              <div class="text-sm text-secondary-600 dark:text-secondary-400">Diagnoses</div>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <div class="text-sm font-bold text-green-600 dark:text-green-400 mb-1">
                {{ car.last_diagnosis }}
              </div>
              <div class="text-sm text-secondary-600 dark:text-secondary-400">Last Check</div>
            </div>
          </div>

          <!-- Recent Diagnoses -->
          <div class="mb-6">
            <h4 class="text-sm font-semibold text-secondary-900 dark:text-white mb-3">Recent Diagnoses</h4>
            <div v-if="car.recent_diagnoses && car.recent_diagnoses.length > 0" class="space-y-2">
              <div 
                v-for="diagnosis in car.recent_diagnoses.slice(0, 2)" 
                :key="diagnosis.id"
                class="flex justify-between items-center p-2 bg-secondary-50 dark:bg-secondary-800 rounded-lg"
              >
                <span class="text-sm text-secondary-700 dark:text-secondary-300 truncate flex-1 mr-2">
                  {{ diagnosis.problem }}
                </span>
                <span class="text-xs text-secondary-500 dark:text-secondary-500 whitespace-nowrap">
                  {{ diagnosis.date }}
                </span>
              </div>
            </div>
            <div v-else class="text-center py-4 text-secondary-500 dark:text-secondary-500">
              <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="text-sm">No diagnoses yet</div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex space-x-3">
            <router-link 
              :to="`/diagnose?car=${car.id}`"
              class="flex-1 btn-primary text-center text-sm py-3 flex items-center justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Diagnose
            </router-link>
            <button 
              @click="viewCarHistory(car)"
              class="flex-1 btn-secondary text-sm py-3 flex items-center justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              History
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 mx-auto mb-8 bg-gradient-to-br from-primary-100 to-secondary-100 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">No cars added yet</h3>
          <p class="text-secondary-600 dark:text-secondary-400 mb-8">
            Start by adding your first car to begin tracking maintenance and getting AI-powered diagnoses.
          </p>
          <button 
            @click="showAddCarModal = true"
            class="btn-primary text-lg px-8 py-3"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Your First Car
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Car Modal -->
    <div v-if="showAddCarModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-secondary-800 border-b border-secondary-200 dark:border-secondary-700 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold text-secondary-900 dark:text-white">
              {{ editingCar ? 'Edit Car' : 'Add New Car' }}
            </h3>
            <button 
              @click="closeModal"
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
          <form @submit.prevent="saveCar" class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="brand" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Brand *
                </label>
                <input 
                  id="brand"
                  v-model="carForm.brand"
                  type="text"
                  required
                  class="input w-full"
                  placeholder="e.g., Toyota, BMW, Mercedes"
                />
              </div>

              <div>
                <label for="model" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Model *
                </label>
                <input 
                  id="model"
                  v-model="carForm.model"
                  type="text"
                  required
                  class="input w-full"
                  placeholder="e.g., Camry, X5, C-Class"
                />
              </div>

              <div>
                <label for="year" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Year *
                </label>
                <input 
                  id="year"
                  v-model="carForm.year"
                  type="number"
                  required
                  min="1900"
                  :max="new Date().getFullYear() + 1"
                  class="input w-full"
                  placeholder="e.g., 2020"
                />
              </div>

              <div>
                <label for="color" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Color
                </label>
                <input 
                  id="color"
                  v-model="carForm.color"
                  type="text"
                  class="input w-full"
                  placeholder="e.g., Red, Blue, Silver"
                />
              </div>
            </div>

            <!-- Vehicle Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="vin" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  VIN (Vehicle Identification Number)
                </label>
                <input 
                  id="vin"
                  v-model="carForm.vin"
                  type="text"
                  maxlength="17"
                  class="input w-full"
                  placeholder="17-character VIN"
                />
              </div>

              <div>
                <label for="license_plate" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  License Plate
                </label>
                <input 
                  id="license_plate"
                  v-model="carForm.license_plate"
                  type="text"
                  class="input w-full"
                  placeholder="e.g., ABC-123"
                />
              </div>

              <div>
                <label for="fuel_type" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Fuel Type
                </label>
                <select 
                  id="fuel_type"
                  v-model="carForm.fuel_type"
                  class="input w-full"
                >
                  <option value="">Select fuel type</option>
                  <option value="gasoline">Gasoline</option>
                  <option value="diesel">Diesel</option>
                  <option value="electric">Electric</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>

              <div>
                <label for="transmission" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Transmission
                </label>
                <select 
                  id="transmission"
                  v-model="carForm.transmission"
                  class="input w-full"
                >
                  <option value="">Select transmission</option>
                  <option value="manual">Manual</option>
                  <option value="automatic">Automatic</option>
                  <option value="cvt">CVT</option>
                </select>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="mileage" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Mileage (km)
                </label>
                <input 
                  id="mileage"
                  v-model="carForm.mileage"
                  type="number"
                  min="0"
                  class="input w-full"
                  placeholder="e.g., 50000"
                />
              </div>

              <div>
                <label for="purchase_date" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Purchase Date
                </label>
                <input 
                  id="purchase_date"
                  v-model="carForm.purchase_date"
                  type="date"
                  class="input w-full"
                />
              </div>
            </div>

            <div>
              <label for="notes" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                Notes
              </label>
              <textarea 
                id="notes"
                v-model="carForm.notes"
                rows="3"
                class="input w-full"
                placeholder="Any additional notes about your car..."
              ></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-secondary-200 dark:border-secondary-700">
              <button 
                type="button"
                @click="closeModal"
                class="btn-secondary px-6 py-3"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="loading"
                class="btn-primary px-6 py-3 flex items-center"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loading ? 'Saving...' : (editingCar ? 'Update Car' : 'Add Car') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl p-8 shadow-2xl">
        <div class="flex items-center space-x-4">
          <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-lg font-medium text-secondary-900 dark:text-white">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { carsAPI } from '../services/api'

export default {
  name: 'MyCars',
  setup() {
    const cars = ref([])
    const statistics = ref({})
    const showAddCarModal = ref(false)
    const editingCar = ref(null)
    const loading = ref(false)
    const carForm = ref({
      brand: '',
      model: '',
      year: '',
      vin: '',
      license_plate: '',
      color: '',
      fuel_type: '',
      transmission: '',
      mileage: '',
      purchase_date: '',
      notes: '',
      status: 'active'
    })

    const loadCars = async () => {
      try {
        loading.value = true
        const response = await carsAPI.getAll()
        if (response.data.success) {
          cars.value = response.data.cars
        }
      } catch (error) {
        console.error('Error loading cars:', error)
        alert('Error loading cars. Please try again.')
      } finally {
        loading.value = false
      }
    }

    const loadStatistics = async () => {
      try {
        const response = await carsAPI.statistics()
        if (response.data.success) {
          statistics.value = response.data.statistics
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
      }
    }

    const editCar = (car) => {
      editingCar.value = car
      carForm.value = {
        brand: car.brand || '',
        model: car.model || '',
        year: car.year || '',
        vin: car.vin || '',
        license_plate: car.license_plate || '',
        color: car.color || '',
        fuel_type: car.fuel_type || '',
        transmission: car.transmission || '',
        mileage: car.mileage || '',
        purchase_date: car.purchase_date || '',
        notes: car.notes || '',
        status: car.status || 'active'
      }
      showAddCarModal.value = true
    }

    const deleteCar = async (carId) => {
      if (confirm('Are you sure you want to delete this car? This action cannot be undone.')) {
        try {
          loading.value = true
          const response = await carsAPI.delete(carId)
          if (response.data.success) {
            cars.value = cars.value.filter(car => car.id !== carId)
            await loadStatistics()
            alert('Car deleted successfully!')
          } else {
            alert(response.data.message || 'Error deleting car. Please try again.')
          }
        } catch (error) {
          console.error('Error deleting car:', error)
          alert('Error deleting car. Please try again.')
        } finally {
          loading.value = false
        }
      }
    }

    const saveCar = async () => {
      try {
        loading.value = true
        
        // Prepare form data
        const formData = { ...carForm.value }
        
        // Convert empty strings to null for optional fields
        Object.keys(formData).forEach(key => {
          if (formData[key] === '') {
            formData[key] = null
          }
        })

        if (editingCar.value) {
          // Update existing car
          const response = await carsAPI.update(editingCar.value.id, formData)
          if (response.data.success) {
            const index = cars.value.findIndex(car => car.id === editingCar.value.id)
            if (index !== -1) {
              cars.value[index] = response.data.car
            }
            alert('Car updated successfully!')
          } else {
            alert(response.data.message || 'Error updating car. Please try again.')
          }
        } else {
          // Add new car
          const response = await carsAPI.create(formData)
          if (response.data.success) {
            cars.value.push(response.data.car)
            alert('Car added successfully!')
          } else {
            alert(response.data.message || 'Error adding car. Please try again.')
          }
        }
        
        await loadStatistics()
        closeModal()
      } catch (error) {
        console.error('Error saving car:', error)
        if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          alert('Validation errors:\n' + errors.join('\n'))
        } else {
          alert('Error saving car. Please try again.')
        }
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
        vin: '',
        license_plate: '',
        color: '',
        fuel_type: '',
        transmission: '',
        mileage: '',
        purchase_date: '',
        notes: '',
        status: 'active'
      }
    }

    const viewCarHistory = (car) => {
      // Navigate to car history page or show modal
      console.log('View history for car:', car.id)
      alert(`Viewing history for ${car.display_name}. This feature will be implemented soon!`)
    }

    onMounted(() => {
      loadCars()
      loadStatistics()
    })

    return {
      cars,
      statistics,
      showAddCarModal,
      editingCar,
      loading,
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