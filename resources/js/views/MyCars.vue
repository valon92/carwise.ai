<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
    <!-- Modern Header Section -->
    <div class="relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-indigo-600/5 to-purple-600/5"></div>
        <div class="absolute top-0 left-1/4 w-72 h-72 bg-blue-400/10 rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
        <div class="absolute top-0 right-1/4 w-72 h-72 bg-purple-400/10 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/3 w-72 h-72 bg-indigo-400/10 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000"></div>
      </div>
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
          <!-- Modern Title with Icon -->
          <div class="flex items-center justify-center mb-6">
            <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
          </div>
          
          <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 dark:from-white dark:via-blue-100 dark:to-indigo-100 bg-clip-text text-transparent mb-6">
            My Garage
          </h1>
          <p class="text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
            Your personal vehicle management hub. Track maintenance, monitor health, and get AI-powered insights for all your cars.
          </p>
          
          <!-- Modern Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <button 
              @click="showAddCarModal = true"
              class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Vehicle
              </div>
            </button>
            
            <button 
              @click="refreshData"
              class="group px-6 py-4 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm text-slate-700 dark:text-slate-300 font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border border-slate-200 dark:border-slate-700"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modern Statistics Dashboard -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 mb-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Total Cars Card -->
        <div class="group relative bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-500 border border-white/20 dark:border-slate-700/50">
          <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-6">
              <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div class="text-right">
                <div class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                  {{ statistics.totalCars }}
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Total Vehicles</div>
              </div>
            </div>
            <div class="text-slate-600 dark:text-slate-300 text-sm">
              Active vehicles in your garage
            </div>
          </div>
        </div>

        <!-- Total Diagnoses Card -->
        <div class="group relative bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-500 border border-white/20 dark:border-slate-700/50">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-6">
              <div class="p-3 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div class="text-right">
                <div class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                  {{ statistics.totalDiagnoses }}
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">AI Diagnoses</div>
              </div>
            </div>
            <div class="text-slate-600 dark:text-slate-300 text-sm">
              AI-powered health checks performed
            </div>
          </div>
        </div>

        <!-- Average Age Card -->
        <div class="group relative bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-500 border border-white/20 dark:border-slate-700/50">
          <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-6">
              <div class="p-3 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="text-right">
                <div class="text-4xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                  {{ statistics.averageAge }}
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Avg. Age</div>
              </div>
            </div>
            <div class="text-slate-600 dark:text-slate-300 text-sm">
              Average age of your vehicles
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modern Cars Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div v-if="cars.length > 0" class="space-y-8">
        <div 
          v-for="car in cars" 
          :key="car.id"
          class="group relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-500 border border-white/20 dark:border-slate-700/50 overflow-hidden"
        >
          <!-- Car Card Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
            <!-- Left Side - Car Image & Visual -->
            <div class="relative lg:col-span-1 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 p-8 flex items-center justify-center">
              <!-- Car Image Container -->
              <div class="relative w-full max-w-sm">
                <div class="relative bg-white/90 dark:bg-slate-900/90 rounded-2xl p-6 shadow-lg">
                  <img 
                    :src="getCarImageUrl(car)" 
                    :alt="`${car.brand} ${car.model}`"
                    class="w-full h-32 object-contain rounded-lg"
                    @error="handleImageError"
                  />
                </div>
                
                <!-- Car Brand Badge -->
                <div class="absolute -top-2 -right-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                  {{ car.brand }}
                </div>
                
                <!-- Maintenance Status Indicator -->
                <div class="absolute -bottom-2 -left-2 flex items-center space-x-2 bg-white/90 dark:bg-slate-900/90 rounded-full px-3 py-2 shadow-lg">
                  <div 
                    class="w-3 h-3 rounded-full"
                    :class="{
                      'bg-emerald-500': car.maintenance_status === 'good',
                      'bg-amber-500': car.maintenance_status === 'warning',
                      'bg-red-500': car.maintenance_status === 'urgent'
                    }"
                  ></div>
                  <span class="text-xs font-medium text-slate-700 dark:text-slate-300 capitalize">
                    {{ car.maintenance_status || 'Good' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Middle - Car Information -->
            <div class="lg:col-span-1 p-8 space-y-6">
              <!-- Car Header -->
              <div class="space-y-4">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                      {{ car.display_name }}
                    </h3>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-3">
                      {{ car.year }} • {{ car.age }} years old
                    </p>
                    <div class="flex items-center space-x-4 text-sm text-slate-500 dark:text-slate-500">
                      <span v-if="car.vin" class="flex items-center bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ car.vin.substring(0, 8) }}...
                      </span>
                      <span v-if="car.license_plate" class="flex items-center bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3m0 0h8"></path>
                        </svg>
                        {{ car.license_plate }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Car Details Grid -->
              <div class="grid grid-cols-2 gap-4">
                <div v-if="car.fuel_type" class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                  <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Fuel Type</div>
                  <div class="text-lg font-bold text-slate-900 dark:text-white capitalize">
                    {{ car.fuel_type }}
                  </div>
                </div>
                <div v-if="car.transmission" class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-4 border border-emerald-200/50 dark:border-emerald-700/50">
                  <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-1">Transmission</div>
                  <div class="text-lg font-bold text-slate-900 dark:text-white capitalize">
                    {{ car.transmission }}
                  </div>
                </div>
                <div v-if="car.color" class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl p-4 border border-amber-200/50 dark:border-amber-700/50">
                  <div class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-1">Color</div>
                  <div class="text-lg font-bold text-slate-900 dark:text-white capitalize">
                    {{ car.color }}
                  </div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-4 border border-purple-200/50 dark:border-purple-700/50">
                  <div class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Mileage</div>
                  <div class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ car.current_mileage ? car.current_mileage.toLocaleString() : 'N/A' }} km
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Side - Actions & Maintenance -->
            <div class="lg:col-span-1 p-8 space-y-6 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800">
              <!-- Action Buttons -->
              <div class="space-y-4">
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Quick Actions</h4>
                
                <div class="grid grid-cols-2 gap-3">
                  <button 
                    @click="startDiagnosis(car)"
                    class="group relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>
                      <span class="text-sm">Diagnose</span>
                    </div>
                  </button>
                  
                  <button 
                    @click="viewCarHistory(car)"
                    class="group relative bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                      <span class="text-sm">History</span>
                    </div>
                  </button>
                  
                  <button 
                    @click="addMaintenanceRecord(car)"
                    class="group relative bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600 to-orange-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      <span class="text-sm">Service</span>
                    </div>
                  </button>
                  
                  <button 
                    @click="updateMileage(car)"
                    class="group relative bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                  >
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                      </svg>
                      <span class="text-sm">Update</span>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Maintenance Status -->
              <div class="space-y-4">
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Maintenance Status</h4>
                
                <div class="space-y-3">
                  <div class="bg-white/80 dark:bg-slate-800/80 rounded-2xl p-4 border border-slate-200/50 dark:border-slate-700/50">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Current Mileage</span>
                      <span class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ car.current_mileage ? car.current_mileage.toLocaleString() : 'N/A' }} km
                      </span>
                    </div>
                  </div>
                  
                  <div v-if="car.next_maintenance_due" class="bg-white/80 dark:bg-slate-800/80 rounded-2xl p-4 border border-slate-200/50 dark:border-slate-700/50">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Next Service</span>
                      <span class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ car.next_maintenance_due }} km
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Car Management Actions -->
              <div class="flex space-x-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button 
                  @click="editCar(car)"
                  class="flex-1 p-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors duration-200"
                  title="Edit Car"
                >
                  <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  @click="deleteCar(car.id)"
                  class="flex-1 p-3 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors duration-200"
                  title="Delete Car"
                >
                  <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Empty State -->
      <div v-else class="text-center py-24">
        <div class="max-w-2xl mx-auto">
          <!-- Animated Icon -->
          <div class="relative mb-12">
            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-full flex items-center justify-center shadow-2xl">
              <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
            </div>
            <!-- Floating Elements -->
            <div class="absolute -top-4 -right-4 w-8 h-8 bg-emerald-400 rounded-full animate-bounce"></div>
            <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-amber-400 rounded-full animate-bounce animation-delay-1000"></div>
            <div class="absolute top-1/2 -left-8 w-4 h-4 bg-purple-400 rounded-full animate-bounce animation-delay-2000"></div>
          </div>
          
          <h3 class="text-4xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-300 bg-clip-text text-transparent mb-6">
            Your Garage is Empty
          </h3>
          <p class="text-xl text-slate-600 dark:text-slate-400 mb-12 leading-relaxed max-w-lg mx-auto">
            Start building your digital garage by adding your first vehicle. Track maintenance, get AI-powered diagnostics, and keep your cars running smoothly.
          </p>
          
          <!-- Feature Highlights -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/20 dark:border-slate-700/50">
              <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <h4 class="font-semibold text-slate-900 dark:text-white mb-2">AI Diagnostics</h4>
              <p class="text-sm text-slate-600 dark:text-slate-400">Get instant health checks and maintenance recommendations</p>
            </div>
            
            <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/20 dark:border-slate-700/50">
              <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Maintenance Tracking</h4>
              <p class="text-sm text-slate-600 dark:text-slate-400">Keep detailed records of all service and repairs</p>
            </div>
            
            <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/20 dark:border-slate-700/50">
              <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z"></path>
                </svg>
              </div>
              <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Smart Reminders</h4>
              <p class="text-sm text-slate-600 dark:text-slate-400">Never miss important maintenance schedules</p>
            </div>
          </div>
          
          <button 
            @click="showAddCarModal = true"
            class="group relative px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add Your First Vehicle
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Modern Add/Edit Car Modal -->
    <div v-if="showAddCarModal" class="fixed inset-0 bg-black/60 backdrop-blur-md overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto border border-white/20 dark:border-slate-700/50">
        <!-- Modern Modal Header -->
        <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 px-8 py-6 rounded-t-3xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white">
                  {{ editingCar ? 'Edit Vehicle' : 'Add New Vehicle' }}
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                  {{ editingCar ? 'Update your vehicle information' : 'Add a new vehicle to your garage' }}
                </p>
              </div>
            </div>
            <button 
              @click="closeModal"
              class="p-3 rounded-2xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modern Modal Body -->
        <div class="p-8">
          <form @submit.prevent="saveCar" class="space-y-8">
            <!-- Basic Information Section -->
            <div class="space-y-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Basic Information</h4>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Car Model Selector -->
                <div class="md:col-span-2">
                  <CarModelSelector
                    v-model:brand="carForm.brand"
                    v-model:model="carForm.model"
                    v-model:year="carForm.year"
                    :show-year="true"
                    :show-details="true"
                    :popular-brands-only="false"
                    brand-label="Brand"
                    model-label="Model"
                    year-label="Year"
                    brand-placeholder="Select Brand"
                    model-placeholder="Select Model"
                    year-placeholder="e.g., 2020"
                    @change="onCarSelectionChange"
                  />
                </div>

                <div class="space-y-2">
                  <label for="year" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Year *
                  </label>
                  <input 
                    id="year"
                    v-model="carForm.year"
                    type="number"
                    required
                    min="1900"
                    :max="new Date().getFullYear() + 1"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="e.g., 2020"
                  />
                </div>

                <div class="space-y-2">
                  <label for="color" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Color
                  </label>
                  <input 
                    id="color"
                    v-model="carForm.color"
                    type="text"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="e.g., Red, Blue, Silver"
                  />
                </div>
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

    <!-- Car History Modal -->
    <div v-if="showCarHistoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-md overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-white/20 dark:border-slate-700/50">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 px-8 py-6 rounded-t-3xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-200">Car History</h2>
                <p class="text-slate-600 dark:text-slate-400">{{ selectedCar?.display_name }}</p>
              </div>
            </div>
            <button 
              @click="closeCarHistoryModal"
              class="p-3 rounded-2xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8">
          <div v-if="carHistory.length === 0" class="text-center py-12">
            <div class="p-4 bg-slate-100 dark:bg-slate-700 rounded-2xl w-20 h-20 mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300 mb-2">No History Found</h3>
            <p class="text-slate-500 dark:text-slate-400">This car doesn't have any maintenance records or diagnosis history yet.</p>
          </div>

          <div v-else class="space-y-4">
            <div 
              v-for="item in carHistory" 
              :key="`${item.type}-${item.id}`"
              class="bg-white dark:bg-slate-700 rounded-2xl p-6 border border-slate-200 dark:border-slate-600 shadow-sm hover:shadow-md transition-all duration-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                  <div class="p-2 rounded-xl" :class="item.type === 'diagnosis' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30'">
                    <svg v-if="item.type === 'diagnosis'" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="font-semibold text-slate-800 dark:text-slate-200 mb-1">
                      {{ item.type === 'diagnosis' ? 'AI Diagnosis' : item.title }}
                    </h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                      {{ item.type === 'diagnosis' ? item.description : item.description }}
                    </p>
                    <div class="flex items-center space-x-4 text-xs text-slate-500 dark:text-slate-400">
                      <span>{{ new Date(item.created_at || item.service_date).toLocaleDateString() }}</span>
                      <span v-if="item.service_mileage">{{ item.service_mileage }} km</span>
                      <span v-if="item.cost">${{ item.cost }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 text-xs font-medium rounded-full" :class="item.type === 'diagnosis' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'">
                    {{ item.type === 'diagnosis' ? 'Diagnosis' : 'Maintenance' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Maintenance Record Modal -->
    <div v-if="showMaintenanceModal" class="fixed inset-0 bg-black/60 backdrop-blur-md overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-white/20 dark:border-slate-700/50">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 px-8 py-6 rounded-t-3xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-200">Add Maintenance Record</h2>
                <p class="text-slate-600 dark:text-slate-400">{{ selectedCar?.display_name }}</p>
              </div>
            </div>
            <button 
              @click="closeMaintenanceModal"
              class="p-3 rounded-2xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8">
          <form @submit.prevent="saveMaintenanceRecord" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Maintenance Type *
                </label>
                <select v-model="maintenanceForm.maintenance_type" required class="input w-full">
                  <option value="">Select type</option>
                  <option value="oil_change">Oil Change</option>
                  <option value="tire_change">Tire Change</option>
                  <option value="timing_belt">Timing Belt</option>
                  <option value="brake_pad">Brake Pad</option>
                  <option value="air_filter">Air Filter</option>
                  <option value="fuel_filter">Fuel Filter</option>
                  <option value="spark_plugs">Spark Plugs</option>
                  <option value="battery">Battery</option>
                  <option value="general_service">General Service</option>
                  <option value="inspection">Inspection</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Title *
                </label>
                <input v-model="maintenanceForm.title" type="text" required class="input w-full" placeholder="e.g., Oil Change Service" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Service Date *
                </label>
                <input v-model="maintenanceForm.service_date" type="date" required class="input w-full" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Service Mileage (km) *
                </label>
                <input v-model="maintenanceForm.service_mileage" type="number" required class="input w-full" placeholder="e.g., 75000" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Cost
                </label>
                <input v-model="maintenanceForm.cost" type="number" step="0.01" class="input w-full" placeholder="e.g., 150.00" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                  Currency
                </label>
                <select v-model="maintenanceForm.currency" class="input w-full">
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="GBP">GBP</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                Service Provider
              </label>
              <input v-model="maintenanceForm.service_provider" type="text" class="input w-full" placeholder="e.g., Auto Service Center" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                Description
              </label>
              <textarea v-model="maintenanceForm.description" rows="3" class="input w-full" placeholder="Describe what was done..."></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                Notes
              </label>
              <textarea v-model="maintenanceForm.notes" rows="2" class="input w-full" placeholder="Additional notes..."></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-slate-200 dark:border-slate-700">
              <button 
                type="button"
                @click="closeMaintenanceModal"
                class="btn-ghost"
                :disabled="loading"
              >
                Cancel
              </button>
              <button 
                type="submit"
                class="btn-primary"
                :disabled="loading"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Add Record
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Maintenance History Modal -->
    <div v-if="showMaintenanceHistoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-md overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-white/20 dark:border-slate-700/50">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 px-8 py-6 rounded-t-3xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-200">Maintenance History</h2>
                <p class="text-slate-600 dark:text-slate-400">{{ selectedCar?.display_name }}</p>
              </div>
            </div>
            <button 
              @click="closeMaintenanceHistoryModal"
              class="p-3 rounded-2xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8">
          <div v-if="maintenanceHistory.length === 0" class="text-center py-12">
            <div class="p-4 bg-slate-100 dark:bg-slate-700 rounded-2xl w-20 h-20 mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300 mb-2">No Maintenance Records</h3>
            <p class="text-slate-500 dark:text-slate-400">This car doesn't have any maintenance records yet.</p>
          </div>

          <div v-else class="space-y-4">
            <div 
              v-for="record in maintenanceHistory" 
              :key="record.id"
              class="bg-white dark:bg-slate-700 rounded-2xl p-6 border border-slate-200 dark:border-slate-600 shadow-sm hover:shadow-md transition-all duration-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                  <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="font-semibold text-slate-800 dark:text-slate-200 mb-1">{{ record.title }}</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ record.description }}</p>
                    <div class="flex items-center space-x-4 text-xs text-slate-500 dark:text-slate-400">
                      <span>{{ new Date(record.service_date).toLocaleDateString() }}</span>
                      <span>{{ record.service_mileage }} km</span>
                      <span v-if="record.cost">${{ record.cost }} {{ record.currency }}</span>
                      <span v-if="record.service_provider">{{ record.service_provider }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                    {{ record.maintenance_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
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
import CarModelSelector from '../components/CarModelSelector.vue'

export default {
  name: 'MyCars',
  components: {
    CarModelSelector
  },
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
    const showCarHistoryModal = ref(false)
    const showMaintenanceModal = ref(false)
    const showMaintenanceHistoryModal = ref(false)
    const editingCar = ref(null)
    const selectedCar = ref(null)
    const loading = ref(false)
    const carBrands = ref([])
    const carModels = ref([])
    const carHistory = ref([])
    const maintenanceHistory = ref([])
    const maintenanceForm = ref({
      maintenance_type: '',
      title: '',
      description: '',
      service_date: '',
      service_mileage: '',
      cost: '',
      currency: 'USD',
      service_provider: '',
      notes: ''
    })
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

    const onCarSelectionChange = (selection) => {
      console.log('Car selection changed:', selection)
      // The v-model bindings will automatically update the form
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
      // Show car history modal
      selectedCar.value = car
      showCarHistoryModal.value = true
      loadCarHistory(car.id)
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
      selectedCar.value = car
      showMaintenanceModal.value = true
      resetMaintenanceForm()
    }

    const viewMaintenanceHistory = (car) => {
      selectedCar.value = car
      showMaintenanceHistoryModal.value = true
      loadMaintenanceHistory(car.id)
    }

    const generateMaintenanceReport = async (car) => {
      try {
        const response = await fetch(`/api/cars/${car.id}/maintenance/report`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          const blob = await response.blob()
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          link.download = `Maintenance_Report_${car.display_name}_${new Date().toISOString().split('T')[0]}.pdf`
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          window.URL.revokeObjectURL(url)
        } else {
          alert('Error generating report. Please try again.')
        }
      } catch (error) {
        console.error('Error generating report:', error)
        alert('Error generating report. Please try again.')
      }
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

    // Load car history (diagnosis + maintenance)
    const loadCarHistory = async (carId) => {
      try {
        const [diagnosisResponse, maintenanceResponse] = await Promise.all([
          fetch(`/api/cars/${carId}/diagnosis-history`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          }),
          fetch(`/api/cars/${carId}/maintenance/history`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          })
        ])
        
        const diagnosisData = diagnosisResponse.ok ? await diagnosisResponse.json() : { data: [] }
        const maintenanceData = maintenanceResponse.ok ? await maintenanceResponse.json() : { data: [] }
        
        carHistory.value = [
          ...diagnosisData.data.map(item => ({ ...item, type: 'diagnosis' })),
          ...maintenanceData.data.map(item => ({ ...item, type: 'maintenance' }))
        ].sort((a, b) => new Date(b.created_at || b.service_date) - new Date(a.created_at || a.service_date))
      } catch (error) {
        console.error('Error loading car history:', error)
        carHistory.value = []
      }
    }

    // Load maintenance history
    const loadMaintenanceHistory = async (carId) => {
      try {
        const response = await fetch(`/api/cars/${carId}/maintenance/history`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          maintenanceHistory.value = data.data || []
        } else {
          maintenanceHistory.value = []
        }
      } catch (error) {
        console.error('Error loading maintenance history:', error)
        maintenanceHistory.value = []
      }
    }

    // Save maintenance record
    const saveMaintenanceRecord = async () => {
      try {
        loading.value = true
        const response = await fetch(`/api/cars/${selectedCar.value.id}/maintenance`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(maintenanceForm.value)
        })
        
        if (response.ok) {
          showMaintenanceModal.value = false
          resetMaintenanceForm()
          await loadCars() // Refresh cars to update maintenance status
          if (window.$notify) {
            window.$notify.success('Maintenance Record Added', 'Maintenance record has been saved successfully')
          } else {
            alert('Maintenance record added successfully!')
          }
        } else {
          const error = await response.json()
          alert(error.message || 'Error adding maintenance record. Please try again.')
        }
      } catch (error) {
        console.error('Error saving maintenance record:', error)
        alert('Error saving maintenance record. Please try again.')
      } finally {
        loading.value = false
      }
    }

    // Reset maintenance form
    const resetMaintenanceForm = () => {
      maintenanceForm.value = {
        maintenance_type: '',
        title: '',
        description: '',
        service_date: new Date().toISOString().split('T')[0],
        service_mileage: selectedCar.value?.current_mileage || selectedCar.value?.mileage || '',
        cost: '',
        currency: 'USD',
        service_provider: '',
        notes: ''
      }
    }

    // Close modals
    const closeCarHistoryModal = () => {
      showCarHistoryModal.value = false
      selectedCar.value = null
      carHistory.value = []
    }

    const closeMaintenanceModal = () => {
      showMaintenanceModal.value = false
      selectedCar.value = null
      resetMaintenanceForm()
    }

    const closeMaintenanceHistoryModal = () => {
      showMaintenanceHistoryModal.value = false
      selectedCar.value = null
      maintenanceHistory.value = []
    }

    // Refresh all data
    const refreshData = async () => {
      try {
        loading.value = true
        await Promise.all([
          loadCars(),
          loadStatistics(),
          loadCarBrands()
        ])
        if (window.$notify) {
          window.$notify.success('Data Refreshed', 'All data has been updated successfully')
        }
      } catch (error) {
        console.error('Error refreshing data:', error)
        if (window.$notify) {
          window.$notify.error('Refresh Failed', 'Failed to refresh data. Please try again.')
        }
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadCars()
      loadCarBrands()
    })

    return {
      cars,
      statistics,
      showAddCarModal,
      showCarHistoryModal,
      showMaintenanceModal,
      showMaintenanceHistoryModal,
      editingCar,
      selectedCar,
      loading,
      carForm,
      maintenanceForm,
      carBrands,
      carModels,
      carHistory,
      maintenanceHistory,
      editCar,
      deleteCar,
      saveCar,
      closeModal,
      viewCarHistory,
      onCarSelectionChange,
      updateMileage,
      addMaintenanceRecord,
      viewMaintenanceHistory,
      generateMaintenanceReport,
      startDiagnosis,
      viewDiagnosisHistory,
      refreshData,
      getCarIcon,
      getEngineInfo,
      getCarImageUrl,
      loadCarImageUrl,
      handleImageError,
      loadCarHistory,
      loadMaintenanceHistory,
      saveMaintenanceRecord,
      resetMaintenanceForm,
      closeCarHistoryModal,
      closeMaintenanceModal,
      closeMaintenanceHistoryModal
    }
  }
}
</script>

<style scoped>
/* Custom Animation Delays */
.animation-delay-1000 {
  animation-delay: 1s;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Custom Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Glass Effect */
.glass {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Gradient Text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Hover Effects */
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}
</style>