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
            Manage your vehicle collection and track maintenance history
          </p>
          <button 
            @click="showAddCarModal = true"
            class="btn-primary text-lg px-8 py-3 shadow-lg hover:shadow-xl transition-all duration-300"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Car
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">
            {{ statistics.totalCars }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Total Cars</div>
        </div>
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
            {{ statistics.totalDiagnoses }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Total Diagnoses</div>
        </div>
        <div class="card glass text-center">
          <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
            {{ statistics.averageAge }}
          </div>
          <div class="text-secondary-600 dark:text-secondary-400">Average Age</div>
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
          <!-- Car Card Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Side - Car Information -->
            <div class="space-y-6">
              <!-- Car Header -->
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="text-3xl font-bold text-secondary-900 dark:text-white mb-2">
                    {{ car.display_name }}
                  </h3>
                  <p class="text-xl text-secondary-600 dark:text-secondary-400 mb-3">
                    {{ car.year }} • {{ car.age }} years old
                  </p>
                  <div class="flex items-center space-x-6 text-sm text-secondary-500 dark:text-secondary-500">
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
              <div class="grid grid-cols-2 gap-4">
                <div v-if="car.fuel_type" class="text-center p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                  <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-2">Fuel Type</div>
                  <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                    {{ car.fuel_type }}
                  </div>
                </div>
                <div v-if="car.transmission" class="text-center p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                  <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-2">Transmission</div>
                  <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                    {{ car.transmission }}
                  </div>
                </div>
                <div v-if="car.color" class="text-center p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                  <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-2">Color</div>
                  <div class="text-lg font-semibold text-secondary-900 dark:text-white capitalize">
                    {{ car.color }}
                  </div>
                </div>
                <div v-if="car.vin" class="text-center p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                  <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-2">VIN</div>
                  <div class="text-sm font-semibold text-secondary-900 dark:text-white font-mono">
                    {{ car.vin.substring(0, 8) }}...
                  </div>
                </div>
              </div>

              <!-- Maintenance Status -->
              <div>
                <div class="flex items-center justify-between mb-4">
                  <h4 class="text-lg font-semibold text-secondary-900 dark:text-white">Maintenance Status</h4>
                  <div class="flex items-center space-x-2">
                    <div 
                      class="w-3 h-3 rounded-full"
                      :class="{
                        'bg-green-500': car.maintenance_status === 'good',
                        'bg-yellow-500': car.maintenance_status === 'warning',
                        'bg-red-500': car.maintenance_status === 'urgent'
                      }"
                    ></div>
                    <span class="text-sm font-medium capitalize" :class="{
                      'text-green-600 dark:text-green-400': car.maintenance_status === 'good',
                      'text-yellow-600 dark:text-yellow-400': car.maintenance_status === 'warning',
                      'text-red-600 dark:text-red-400': car.maintenance_status === 'urgent'
                    }">
                      {{ car.maintenance_status || 'Good' }}
                    </span>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                  <div class="p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Current Mileage</span>
                      <button 
                        @click="updateMileage(car)"
                        class="text-xs px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors"
                      >
                        Update
                      </button>
                    </div>
                    <div class="text-2xl font-bold text-secondary-900 dark:text-white">
                      {{ car.current_mileage ? car.current_mileage.toLocaleString() : 'N/A' }} km
                    </div>
                  </div>
                  
                  <div v-if="car.next_maintenance_due" class="p-4 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
                    <div class="text-sm font-medium text-secondary-600 dark:text-secondary-400 mb-2">Next Service Due</div>
                    <div class="text-2xl font-bold text-secondary-900 dark:text-white">
                      {{ car.next_maintenance_due }} km
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="grid grid-cols-2 gap-4">
                <button 
                  @click="startDiagnosis(car)"
                  class="btn-primary flex items-center justify-center"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                  Diagnose
                </button>
                <button 
                  @click="viewCarHistory(car)"
                  class="btn-secondary flex items-center justify-center"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  Maintenance History
                </button>
                <button 
                  @click="viewDiagnosisHistory(car)"
                  class="btn-secondary flex items-center justify-center"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                  Diagnosis History
                </button>
                <button 
                  @click="addMaintenanceRecord(car)"
                  class="btn-secondary flex items-center justify-center"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Add Service
                </button>
              </div>
            </div>

            <!-- Right Side - 3D Car Image -->
            <div class="flex items-center justify-center">
              <div class="relative w-full max-w-md">
                <!-- 3D Car Container -->
                <div class="relative bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-2xl">
                  <!-- Car Image based on brand/model -->
                  <div class="text-center">
                    <div class="mb-4">
                      <img 
                        :src="getCarImageUrl(car)" 
                        :alt="`${car.brand} ${car.model}`"
                        class="w-32 h-24 object-contain mx-auto rounded-lg shadow-lg"
                        @error="handleImageError"
                      />
                    </div>
                    <div class="text-2xl font-bold text-secondary-900 dark:text-white mb-2">
                      {{ car.model }}
                    </div>
                    <div class="text-lg text-secondary-600 dark:text-secondary-400 mb-4">
                      {{ car.year }} • {{ car.color || 'Unknown Color' }}
                    </div>
                    
                    
                    <!-- Car Specs -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div class="bg-white/50 dark:bg-black/20 rounded-lg p-3">
                        <div class="font-semibold text-secondary-700 dark:text-secondary-300">Engine</div>
                        <div class="text-secondary-600 dark:text-secondary-400">
                          {{ getEngineInfo(car) }}
                        </div>
                      </div>
                      <div class="bg-white/50 dark:bg-black/20 rounded-lg p-3">
                        <div class="font-semibold text-secondary-700 dark:text-secondary-300">Transmission</div>
                        <div class="text-secondary-600 dark:text-secondary-400 capitalize">
                          {{ car.transmission || 'Unknown' }}
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Decorative Elements -->
                  <div class="absolute top-4 right-4 w-16 h-16 bg-primary-500/10 rounded-full"></div>
                  <div class="absolute bottom-4 left-4 w-12 h-12 bg-secondary-500/10 rounded-full"></div>
                </div>
              </div>
            </div>
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
          <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">No Cars Yet</h3>
          <p class="text-secondary-600 dark:text-secondary-400 mb-8">
            Start by adding your first vehicle to track maintenance and get AI-powered diagnostics
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
                <select 
                  id="brand"
                  v-model="carForm.brand"
                  required
                  class="input w-full"
                  @change="onBrandChange"
                >
                  <option value="">Select Brand</option>
                  <option v-for="brand in carBrands" :key="brand.id" :value="brand.name">
                    {{ brand.name }}
                  </option>
                </select>
              </div>

              <div>
                <label for="model" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Model *
                </label>
                <select 
                  id="model"
                  v-model="carForm.model"
                  required
                  class="input w-full"
                  :disabled="!carForm.brand"
                >
                  <option value="">Select Model</option>
                  <option v-for="model in carModels" :key="model.id" :value="model.name">
                    {{ model.name }}
                  </option>
                </select>
                <div v-if="!carForm.brand" class="text-sm text-secondary-500 dark:text-secondary-400 mt-1">
                  Please select brand first
                </div>
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
                  Initial Mileage (km)
                </label>
                <input 
                  id="mileage"
                  v-model="carForm.mileage"
                  type="number"
                  min="0"
                  class="input w-full"
                  placeholder="e.g., 50000"
                />
                <div class="text-xs text-secondary-500 dark:text-secondary-400 mt-1">
                  Original mileage when you got the car
                </div>
              </div>

              <div>
                <label for="current_mileage" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Current Mileage (km)
                </label>
                <input 
                  id="current_mileage"
                  v-model="carForm.current_mileage"
                  type="number"
                  min="0"
                  class="input w-full"
                  placeholder="e.g., 75000"
                />
                <div class="text-xs text-secondary-500 dark:text-secondary-400 mt-1">
                  Current odometer reading
                </div>
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

              <div>
                <label for="purchase_price" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                  Purchase Price
                </label>
                <input 
                  id="purchase_price"
                  v-model="carForm.purchase_price"
                  type="number"
                  min="0"
                  step="0.01"
                  class="input w-full"
                  placeholder="e.g., 15000"
                />
              </div>
            </div>

            <!-- Maintenance Preferences -->
            <div class="border-t border-secondary-200 dark:border-secondary-700 pt-6">
              <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Maintenance Preferences</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="oil_change_interval" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                    Oil Change Interval (km)
                  </label>
                  <input 
                    id="oil_change_interval"
                    v-model="carForm.oil_change_interval"
                    type="number"
                    min="5000"
                    max="50000"
                    class="input w-full"
                    placeholder="15000"
                  />
                  <div class="text-xs text-secondary-500 dark:text-secondary-400 mt-1">
                    Default: 15,000 km
                  </div>
                </div>

                <div>
                  <label for="notification_advance_days" class="block text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-2">
                    Notification Advance (days)
                  </label>
                  <input 
                    id="notification_advance_days"
                    v-model="carForm.notification_advance_days"
                    type="number"
                    min="1"
                    max="365"
                    class="input w-full"
                    placeholder="30"
                  />
                  <div class="text-xs text-secondary-500 dark:text-secondary-400 mt-1">
                    How many days before service to notify
                  </div>
                </div>
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
                class="btn-ghost"
                :disabled="loading"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="loading"
                class="btn-primary"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ editingCar ? 'Update Car' : 'Add Car' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { carsAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'
import { useCarImages } from '../composables/useCarImages'

export default {
  name: 'MyCars',
  setup() {
    const router = useRouter()
    const { user } = useAuth()
    const { getCarImageUrl: getCarImageUrlFromAPI, getCarThumbnailUrl } = useCarImages()
    
    const cars = ref([])
    const carImageUrls = ref({})
    const statistics = ref({
      totalCars: 0,
      totalDiagnoses: 0,
      averageAge: 0
    })
    const showAddCarModal = ref(false)
    const editingCar = ref(null)
    const loading = ref(false)
    const carBrands = ref([])
    const carModels = ref([])
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
      current_mileage: '',
      purchase_date: '',
      purchase_price: '',
      notes: '',
      status: 'active',
      // Maintenance preferences
      oil_change_interval: 15000,
      notification_advance_days: 30
    })

    const loadCars = async () => {
      try {
        loading.value = true
        const response = await carsAPI.getAll()
        if (response.data.success) {
          cars.value = response.data.cars
          await loadStatistics()
          
          // Load car images only if not already provided by API
          for (const car of cars.value) {
            if (!car.image_url) {
              await loadCarImageUrl(car)
            }
          }
        } else {
          console.error('Failed to load cars:', response.data.message)
        }
      } catch (error) {
        console.error('Error loading cars:', error)
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

    const loadCarBrands = async () => {
      try {
        const response = await fetch('/api/car-brands')
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            carBrands.value = data.data
          }
        }
      } catch (error) {
        console.error('Error loading car brands:', error)
      }
    }

    const loadCarModels = async (brandName) => {
      try {
        if (!brandName) {
          carModels.value = []
          return
        }
        
        // Find brand by name
        const brand = carBrands.value.find(b => b.name === brandName)
        if (!brand) {
          carModels.value = []
          return
        }
        
        const response = await fetch(`/api/car-models/brand/${brand.id}`)
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            carModels.value = data.data
          }
        }
      } catch (error) {
        console.error('Error loading car models:', error)
        carModels.value = []
      }
    }

    const onBrandChange = () => {
      carForm.value.model = ''
      loadCarModels(carForm.value.brand)
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
        current_mileage: car.current_mileage || car.mileage || '',
        purchase_date: car.purchase_date || '',
        purchase_price: car.purchase_price || '',
        notes: car.notes || '',
        status: car.status || 'active',
        // Maintenance preferences
        oil_change_interval: car.oil_change_interval || 15000,
        notification_advance_days: car.notification_advance_days || 30
      }
      // Load models for the car's brand
      loadCarModels(car.brand)
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
            if (window.$notify) {
              window.$notify.success('Car Deleted', 'Car has been removed from your garage')
            } else {
              alert('Car deleted successfully!')
            }
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
        console.log('Saving car with data:', carForm.value)
        console.log('User authenticated:', !!user.value)
        console.log('Auth token exists:', !!localStorage.getItem('token'))
        let response
        
        if (editingCar.value) {
          console.log('Updating car:', editingCar.value.id)
          response = await carsAPI.update(editingCar.value.id, carForm.value)
        } else {
          console.log('Creating new car')
          response = await carsAPI.create(carForm.value)
        }
        
        if (response.data.success) {
          await loadCars()
          closeModal()
          if (window.$notify) {
            window.$notify.success(
              editingCar.value ? 'Car Updated' : 'Car Added',
              editingCar.value ? 'Your car has been updated successfully' : 'Your car has been added successfully'
            )
          } else {
            alert(editingCar.value ? 'Car updated successfully!' : 'Car added successfully!')
          }
        } else {
          console.error('Car save failed:', response.data)
          alert(response.data.message || 'Error saving car. Please try again.')
        }
      } catch (error) {
        console.error('Error saving car:', error)
        console.error('Error details:', error.response?.data)
        alert('Error saving car: ' + (error.response?.data?.message || error.message || 'Please try again.'))
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
        current_mileage: '',
        purchase_date: '',
        purchase_price: '',
        notes: '',
        status: 'active',
        // Maintenance preferences
        oil_change_interval: 15000,
        notification_advance_days: 30
      }
    }

    const viewCarHistory = (car) => {
      // Navigate to car history page or show modal
      console.log('View history for car:', car.id)
      alert(`Viewing history for ${car.display_name}. This feature will be implemented soon!`)
    }

    const updateMileage = async (car) => {
      const newMileage = prompt(`Enter new mileage for ${car.display_name}:`, car.current_mileage || car.mileage || '')
      if (newMileage && !isNaN(newMileage) && newMileage > 0) {
        try {
          const response = await fetch(`/api/cars/${car.id}/maintenance/mileage`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({ mileage: parseInt(newMileage) })
          })
          
          if (response.ok) {
            await loadCars()
            alert('Mileage updated successfully!')
          } else {
            alert('Error updating mileage. Please try again.')
          }
        } catch (error) {
          console.error('Error updating mileage:', error)
          alert('Error updating mileage. Please try again.')
        }
      }
    }

    const addMaintenanceRecord = (car) => {
      alert(`Add maintenance record for ${car.display_name}. This feature will be implemented soon!`)
    }

    const viewMaintenanceHistory = (car) => {
      alert(`View maintenance history for ${car.display_name}. This feature will be implemented soon!`)
    }

    const generateMaintenanceReport = (car) => {
      alert(`Generate PDF report for ${car.display_name}. This feature will be implemented soon!`)
    }

    // Car icon mapping
    const getCarIcon = (brand) => {
      const icons = {
        'Volkswagen': '🚗',
        'BMW': '🚙',
        'Mercedes-Benz': '🚘',
        'Audi': '🚖',
        'Toyota': '🚕',
        'Honda': '🚗',
        'Ford': '🚙',
        'Chevrolet': '🚘',
        'Nissan': '🚖',
        'Hyundai': '🚕',
        'Kia': '🚗',
        'Mazda': '🚙',
        'Subaru': '🚘',
        'Lexus': '🚖',
        'Infiniti': '🚕',
        'Acura': '🚗',
        'Porsche': '🚙',
        'Ferrari': '🚘',
        'Lamborghini': '🚖',
        'Maserati': '🚕'
      }
      return icons[brand] || '🚗'
    }

    // Engine info mapping
    const getEngineInfo = (car) => {
      // First try to get engine size from specifications
      if (car.specifications && car.specifications.engine_size) {
        return car.specifications.engine_size
      }
      
      // Fallback to fuel type based engine info
      if (car.fuel_type === 'diesel') {
        return '1.6L Diesel'
      } else if (car.fuel_type === 'gasoline') {
        return '1.6L Gasoline'
      } else if (car.fuel_type === 'electric') {
        return 'Electric Motor'
      } else if (car.fuel_type === 'hybrid') {
        return 'Hybrid Engine'
      }
      return 'Unknown Engine'
    }

    // Get car image URL
    const getCarImageUrl = (car) => {
      // Use image_url from API response if available
      if (car.image_url) {
        return car.image_url
      }
      
      // Fallback to cached image URLs
      const key = `${car.brand}-${car.model}-${car.year}-${car.color}`
      if (carImageUrls.value[key]) {
        return carImageUrls.value[key]
      }
      
      // Return a placeholder while loading
      return '/images/cars/default-car.svg'
    }

    // Load car image URL
    const loadCarImageUrl = async (car) => {
      const key = `${car.brand}-${car.model}-${car.year}-${car.color}`
      
      try {
        const imageUrl = await getCarImageUrlFromAPI(car.brand, car.model, car.year, car.color)
        carImageUrls.value[key] = imageUrl
      } catch (error) {
        console.error('Error loading car image:', error)
        carImageUrls.value[key] = '/images/cars/default-car.svg'
      }
    }

    // Handle image error
    const handleImageError = (event) => {
      event.target.src = '/images/cars/default-car.svg'
    }

    // Start diagnosis for a specific car
    const startDiagnosis = (car) => {
      const carData = {
        id: car.id,
        brand: car.brand,
        model: car.model,
        year: car.year,
        color: car.color,
        fuel_type: car.fuel_type,
        transmission: car.transmission,
        mileage: car.current_mileage || car.mileage,
        engine_type: car.fuel_type,
        engine_size: getEngineInfo(car)
      }
      
      console.log('Storing car data for diagnosis:', carData)
      console.log('Original car object:', car)
      
      // Store car data in localStorage for pre-filling diagnosis form
      localStorage.setItem('selectedCarForDiagnosis', JSON.stringify(carData))
      
      // Navigate to diagnose page
      router.push('/diagnose')
    }

    // View diagnosis history for a specific car
    const viewDiagnosisHistory = (car) => {
      // Navigate to diagnosis history page with car ID
      router.push(`/diagnose?car=${car.id}&history=true`)
    }

    onMounted(() => {
      loadCars()
      loadCarBrands()
    })

    return {
      cars,
      statistics,
      showAddCarModal,
      editingCar,
      loading,
      carForm,
      carBrands,
      carModels,
      editCar,
      deleteCar,
      saveCar,
      closeModal,
      viewCarHistory,
      onBrandChange,
      updateMileage,
      addMaintenanceRecord,
      viewMaintenanceHistory,
      generateMaintenanceReport,
      startDiagnosis,
      viewDiagnosisHistory,
      getCarIcon,
      getEngineInfo,
      getCarImageUrl,
      loadCarImageUrl,
      handleImageError
    }
  }
}
</script>