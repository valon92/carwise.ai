<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 dark:bg-primary-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary-200 dark:bg-secondary-700 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
      <div class="absolute top-40 left-40 w-80 h-80 bg-accent-200 dark:bg-accent-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Modern Header Section -->
    <div class="relative overflow-hidden">
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
          <!-- Modern Title with Icon -->
          <div class="flex items-center justify-center mb-6">
            <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
          </div>
          
          <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 dark:from-white dark:via-blue-100 dark:to-indigo-100 bg-clip-text text-transparent mb-6">
            AI Car Diagnosis
          </h1>
          <p class="text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
            Get instant, accurate AI-powered diagnosis for your vehicle. Describe your car's symptoms and receive professional-grade analysis with actionable solutions.
          </p>
          
          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-12">
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50">
              <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">95%</div>
              <div class="text-sm text-slate-600 dark:text-slate-400">Accuracy Rate</div>
            </div>
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50">
              <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">2min</div>
              <div class="text-sm text-slate-600 dark:text-slate-400">Average Analysis</div>
            </div>
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50">
              <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">24/7</div>
              <div class="text-sm text-slate-600 dark:text-slate-400">Available</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

      <!-- Modern Progress Steps -->
      <div class="mb-12">
        <!-- Desktop Progress Steps -->
        <div class="hidden md:flex items-center justify-center space-x-8">
          <div v-for="(step, index) in steps" :key="index" class="flex items-center">
            <div class="flex flex-col items-center">
              <div 
                class="w-14 h-14 rounded-2xl flex items-center justify-center text-sm font-bold transition-all duration-500 shadow-lg"
                :class="currentStep >= index 
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white scale-110 shadow-blue-500/25' 
                  : 'bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-400 border-2 border-slate-200 dark:border-slate-600'"
              >
                <svg v-if="currentStep > index" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span v-else>{{ index + 1 }}</span>
              </div>
              <span 
                class="mt-3 text-sm font-semibold transition-colors duration-300"
                :class="currentStep >= index ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400'"
              >
                {{ step }}
              </span>
            </div>
            <div v-if="index < steps.length - 1" class="flex-1 h-0.5 mx-4 bg-slate-200 dark:bg-slate-700 relative">
              <div 
                class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500"
                :style="{ width: currentStep > index ? '100%' : '0%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Mobile Progress Steps -->
        <div class="md:hidden">
          <!-- Current Step Display -->
          <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-lg"
                 :class="currentStep >= 0 
                   ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white' 
                   : 'bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-400 border-2 border-slate-200 dark:border-slate-600'">
              <svg v-if="currentStep > 0" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span v-else class="text-lg font-bold">{{ currentStep + 1 }}</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
              {{ steps[currentStep] }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              Step {{ currentStep + 1 }} of {{ steps.length }}
            </p>
          </div>

          <!-- Progress Bar -->
          <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 mb-6 overflow-hidden">
            <div 
              class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-700 ease-out relative"
              :style="{ width: ((currentStep + 1) / steps.length) * 100 + '%' }"
            >
              <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
          </div>

          <!-- Step Indicators -->
          <div class="flex justify-center space-x-3">
            <div 
              v-for="(step, index) in steps" 
              :key="index"
              class="w-3 h-3 rounded-full transition-all duration-300"
              :class="currentStep >= index 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 scale-125' 
                : 'bg-slate-300 dark:bg-slate-600'"
            ></div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 lg:gap-12">
        <!-- Modern Diagnosis Form -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-slate-700/50 p-8 order-1 xl:order-1 hover:shadow-3xl transition-all duration-300">
          <div class="flex items-center mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                Vehicle Information
              </h2>
              <p class="text-slate-600 dark:text-slate-400">
                Provide details about your vehicle and symptoms
              </p>
            </div>
          </div>
          
          <div class="space-y-8">
            <!-- Vehicle Information -->
            <div class="space-y-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-slate-900 dark:text-white">Vehicle Details</h3>
                </div>
                <div v-if="selectedCar" class="flex items-center text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Pre-filled from profile
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Car Model Selector -->
                <div class="sm:col-span-2">
                  <CarModelSelector
                    v-model:brand="diagnosisForm.make"
                    v-model:model="diagnosisForm.model"
                    v-model:year="diagnosisForm.year"
                    :disabled="selectedCar"
                    :show-year="true"
                    :show-details="false"
                    :popular-brands-only="true"
                    brand-label="Make"
                    model-label="Model"
                    year-label="Year"
                    brand-placeholder="Select Make"
                    model-placeholder="Select Model"
                    year-placeholder="e.g., 2020"
                    @change="onCarSelectionChange"
                  />
                </div>
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Year *
                  </label>
                  <input
                    v-model="diagnosisForm.year"
                    type="number"
                    placeholder="e.g., 2020"
                    min="1990"
                    :max="new Date().getFullYear() + 1"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    :disabled="selectedCar"
                    required
                  >
                </div>
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Mileage
                  </label>
                  <input
                    v-model="diagnosisForm.mileage"
                    type="number"
                    placeholder="e.g., 50000"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    :disabled="selectedCar"
                  >
                </div>
              </div>
            </div>

            <!-- Engine Information -->
            <div class="space-y-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-slate-900 dark:text-white">Engine Details</h3>
                </div>
                <div v-if="selectedCar" class="flex items-center text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Pre-filled from profile
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Engine Type
                  </label>
                  <select v-model="diagnosisForm.engineType" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" :disabled="selectedCar">
                    <option value="">Select Engine Type</option>
                    <option value="Gasoline">Gasoline</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Electric">Electric</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Engine Size
                  </label>
                  <input
                    v-model="diagnosisForm.engineSize"
                    type="text"
                    placeholder="e.g., 2.0L, 3.5L"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    :disabled="selectedCar"
                  >
                </div>
              </div>
            </div>

            <!-- Problem Description -->
            <div class="space-y-6">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Problem Description *</h3>
              </div>
              
              <!-- Modern Input Tabs -->
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mb-6 bg-slate-100 dark:bg-slate-700 rounded-2xl p-2">
                <button
                  type="button"
                  @click="activeInputType = 'text'"
                  class="flex-1 py-3 px-4 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center justify-center"
                  :class="activeInputType === 'text' 
                    ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-lg transform scale-105' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-600/50'"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Text Description
                </button>
                <button
                  type="button"
                  @click="activeInputType = 'voice'"
                  class="flex-1 py-3 px-4 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center justify-center"
                  :class="activeInputType === 'voice' 
                    ? 'bg-white dark:bg-slate-800 text-purple-600 dark:text-purple-400 shadow-lg transform scale-105' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-600/50'"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                  </svg>
                  Voice Recording
                </button>
              </div>

              <!-- Modern Text Input -->
              <div v-if="activeInputType === 'text'">
                <textarea
                  v-model="diagnosisForm.description"
                  rows="5"
                  placeholder="Describe the symptoms or issues you're experiencing in detail. The more specific you are, the better our AI can help you..."
                  class="w-full px-4 py-4 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                  required
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                  <p class="text-sm text-slate-500 dark:text-slate-400">
                    Be as detailed as possible for better diagnosis accuracy
                  </p>
                  <span class="text-sm text-slate-400 dark:text-slate-500">
                    {{ diagnosisForm.description?.length || 0 }} characters
                  </span>
                </div>
              </div>

              <!-- Voice Recording -->
              <div v-if="activeInputType === 'voice'" class="space-y-4">
                <!-- Permission Help -->
                <div v-if="!microphonePermissionGranted" class="p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-warning-600 dark:text-warning-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                      <h4 class="text-sm font-medium text-warning-800 dark:text-warning-200">Microphone Permission Required</h4>
                      <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                        To record audio, please allow microphone access when prompted by your browser.
                      </p>
                      <div class="mt-2 text-xs text-warning-600 dark:text-warning-400">
                        <p><strong>Chrome/Edge:</strong> Click the microphone icon in the address bar and select "Allow"</p>
                        <p><strong>Firefox:</strong> Click "Allow" when the permission dialog appears</p>
                        <p><strong>Safari:</strong> Go to Safari > Preferences > Websites > Microphone and allow access</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Recording Controls -->
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4 p-6 bg-secondary-50 dark:bg-secondary-700 rounded-lg">
                  <!-- Microphone Permission Button (if not granted) -->
                  <div v-if="!microphonePermissionGranted" class="text-center">
                    <button
                      type="button"
                      @click="requestMicrophonePermission"
                      class="btn-primary mb-2"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                      </svg>
                      Allow Microphone Access
                    </button>
                    <p class="text-xs text-secondary-500 dark:text-secondary-400">
                      Click to enable microphone for voice recording
                    </p>
                  </div>

                  <!-- Recording Button (if permission granted) -->
                  <div v-else>
                    <!-- Debug: Show recording state -->
                    <div class="text-xs text-gray-500 mb-2">
                      Debug: microphonePermissionGranted={{ microphonePermissionGranted }}, isRecording={{ isRecording }}, isPaused={{ isPaused }}
                    </div>
                    <div class="flex items-center space-x-4">
                      <!-- Main Record Button -->
                      <button
                        type="button"
                        @click="toggleRecording"
                        :disabled="isProcessingAudio"
                        class="flex items-center justify-center w-16 h-16 rounded-full transition-all duration-200 border-2 border-white shadow-lg"
                        :class="isRecording 
                          ? (isPaused ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-red-500 hover:bg-red-600 text-white animate-pulse')
                          : 'bg-blue-500 hover:bg-blue-600 text-white'"
                        style="z-index: 10; position: relative;"
                      >
                        <svg v-if="!isRecording" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                        <svg v-else-if="isPaused" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6-8h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2v-8a2 2 0 012-2z"></path>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                          <rect x="6" y="6" width="12" height="12" rx="2"></rect>
                        </svg>
                      </button>

                      <!-- Stop Button (only when recording) -->
                      <button
                        v-if="isRecording"
                        type="button"
                        @click="stopRecording"
                        class="flex items-center justify-center w-12 h-12 rounded-full bg-red-500 hover:bg-red-600 text-white transition-all duration-200 border-2 border-white shadow-lg"
                        style="z-index: 10; position: relative;"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"></path>
                        </svg>
                      </button>
                    </div>
                    
                    <div class="text-center mt-2">
                      <p class="text-sm font-medium text-secondary-700 dark:text-secondary-300">
                        <span v-if="!isRecording">Click to record</span>
                        <span v-else-if="isPaused">Recording paused</span>
                        <span v-else>Recording...</span>
                      </p>
                      <p v-if="recordingTime > 0" class="text-xs text-secondary-500 dark:text-secondary-500">
                        {{ formatTime(recordingTime) }}
                      </p>
                      <p v-if="!isRecording && !recordedAudio" class="text-xs text-secondary-400 dark:text-secondary-500">
                        Speak clearly into your microphone
                      </p>
                      <!-- Debug info -->
                      <p class="text-xs text-gray-400 mt-1">
                        Debug: isRecording={{ isRecording }}, isPaused={{ isPaused }}, recordingTime={{ recordingTime }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Audio Player (if recorded) -->
                <div v-if="recordedAudio" class="p-4 bg-secondary-50 dark:bg-secondary-700 rounded-lg">
                  <div class="space-y-4">
                    <!-- Audio Player -->
                    <div class="flex items-center space-x-4">
                      <audio :src="recordedAudio" controls class="flex-1"></audio>
                    </div>
                    
                    <!-- Audio Info -->
                    <div class="text-sm text-secondary-600 dark:text-secondary-400">
                      <p>Recording duration: {{ formatTime(recordingTime) }}</p>
                      <p>File size: {{ audioBlob ? (audioBlob.size / 1024).toFixed(1) + ' KB' : 'Unknown' }}</p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                      <button
                        type="button"
                        @click="transcribeAudio"
                        :disabled="isProcessingAudio"
                        class="btn-primary text-sm flex items-center"
                      >
                        <svg v-if="!isProcessingAudio" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ isProcessingAudio ? 'Transcribing...' : 'Convert to Text' }}
                      </button>
                      
                      <button
                        type="button"
                        @click="useTranscribedText"
                        v-if="transcribedText"
                        class="btn-secondary text-sm flex items-center"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Use Text
                      </button>
                      
                      <button
                        type="button"
                        @click="clearRecording"
                        class="btn-danger text-sm flex items-center"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Transcribed Text -->
                <div v-if="transcribedText" class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                  <label class="block text-sm font-medium text-primary-700 dark:text-primary-300 mb-2">
                    Transcribed Text:
                  </label>
                  <textarea
                    v-model="transcribedText"
                    rows="3"
                    class="input text-sm"
                    placeholder="Transcribed text will appear here..."
                  ></textarea>
                  <button
                    type="button"
                    @click="useTranscribedText"
                    class="mt-2 btn-primary text-sm"
                  >
                    Use This Text
                  </button>
                </div>
              </div>
            </div>

            <!-- Modern Symptoms Checklist -->
            <div class="space-y-6">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Common Symptoms</h3>
              </div>
              <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Select all symptoms that apply to your vehicle for more accurate diagnosis
              </p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label v-for="symptom in commonSymptoms" :key="symptom" class="flex items-center p-3 bg-slate-50 dark:bg-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 cursor-pointer group">
                  <input
                    v-model="diagnosisForm.symptoms"
                    type="checkbox"
                    :value="symptom"
                    class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all duration-200"
                  >
                  <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100 transition-colors duration-200">{{ symptom }}</span>
                </label>
              </div>
            </div>

            <!-- Form Validation Feedback -->
            <div v-if="formErrors.length > 0" class="p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg">
              <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-danger-600 dark:text-danger-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <h4 class="text-sm font-medium text-danger-800 dark:text-danger-200">Please fix the following errors:</h4>
                  <ul class="mt-2 text-sm text-danger-700 dark:text-danger-300 list-disc list-inside space-y-1">
                    <li v-for="error in formErrors" :key="error">{{ error }}</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Media Upload Section -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                Upload Media (Optional)
              </label>
              
              <!-- Upload Type Tabs -->
              <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-1 mb-4 bg-secondary-100 dark:bg-secondary-700 rounded-lg p-1">
                <button
                  type="button"
                  @click="activeMediaType = 'photos'"
                  class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-all duration-200"
                  :class="activeMediaType === 'photos' 
                    ? 'bg-white dark:bg-secondary-800 text-primary-600 dark:text-primary-400 shadow-sm' 
                    : 'text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-200'"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Photos
                </button>
                <button
                  type="button"
                  @click="activeMediaType = 'videos'"
                  class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-all duration-200"
                  :class="activeMediaType === 'videos' 
                    ? 'bg-white dark:bg-secondary-800 text-primary-600 dark:text-primary-400 shadow-sm' 
                    : 'text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-200'"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                  Videos
                </button>
                <button
                  type="button"
                  @click="activeMediaType = 'audio'"
                  class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-all duration-200"
                  :class="activeMediaType === 'audio' 
                    ? 'bg-white dark:bg-secondary-800 text-primary-600 dark:text-primary-400 shadow-sm' 
                    : 'text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-200'"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                  </svg>
                  Audio
                </button>
              </div>

              <!-- Photo Upload -->
              <div v-if="activeMediaType === 'photos'" 
                @click="triggerFileUpload('photos')"
                @dragover.prevent
                @drop.prevent="handleFileDrop"
                class="border-2 border-dashed border-secondary-300 dark:border-secondary-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-400 transition-colors duration-200"
              >
                <svg class="mx-auto h-12 w-12 text-secondary-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-secondary-600 dark:text-secondary-400">
                  Drag & drop photos here
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  Max 5 photos, 10MB each
                </p>
              </div>

              <!-- Video Upload -->
              <div v-if="activeMediaType === 'videos'" 
                @click="triggerFileUpload('videos')"
                @dragover.prevent
                @drop.prevent="handleFileDrop"
                class="border-2 border-dashed border-secondary-300 dark:border-secondary-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-400 transition-colors duration-200"
              >
                <svg class="mx-auto h-12 w-12 text-secondary-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-secondary-600 dark:text-secondary-400">
                  Drag & drop videos here
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  Max 2 videos, 50MB each
                </p>
              </div>

              <!-- Audio Upload -->
              <div v-if="activeMediaType === 'audio'" 
                @click="triggerFileUpload('audio')"
                @dragover.prevent
                @drop.prevent="handleFileDrop"
                class="border-2 border-dashed border-secondary-300 dark:border-secondary-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-400 transition-colors duration-200"
              >
                <svg class="mx-auto h-12 w-12 text-secondary-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-secondary-600 dark:text-secondary-400">
                  Drag & drop audio here
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  Max 3 audio files, 20MB each
                </p>
              </div>

              <!-- Hidden File Inputs -->
              <input
                ref="photoInput"
                type="file"
                multiple
                accept="image/*"
                @change="handleFileSelect"
                class="hidden"
              >
              <input
                ref="videoInput"
                type="file"
                multiple
                accept="video/*"
                @change="handleFileSelect"
                class="hidden"
              >
              <input
                ref="audioInput"
                type="file"
                multiple
                accept="audio/*"
                @change="handleFileSelect"
                class="hidden"
              >
              <div v-if="uploadedFiles.length > 0" class="mt-4">
                <div class="flex flex-wrap gap-2">
                  <div v-for="(file, index) in uploadedFiles" :key="index" class="relative">
                    <!-- Image Preview -->
                    <div v-if="file.type === 'image'" class="relative">
                      <img :src="file.preview" class="w-20 h-20 object-cover rounded-lg">
                      <div class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        IMG
                      </div>
                    </div>
                    
                    <!-- Video Preview -->
                    <div v-else-if="file.type === 'video'" class="relative">
                      <video :src="file.preview" class="w-20 h-20 object-cover rounded-lg" muted>
                        Your browser does not support video preview.
                      </video>
                      <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M8 5v14l11-7z"/>
                        </svg>
                      </div>
                      <div class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        VID
                      </div>
                    </div>
                    
                    <!-- Audio Preview -->
                    <div v-else-if="file.type === 'audio'" class="relative">
                      <div class="w-20 h-20 bg-secondary-100 dark:bg-secondary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                      </div>
                      <div class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                        AUD
                      </div>
                    </div>
                    
                    <!-- Remove Button -->
                    <button
                      @click="removeFile(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>

            
            <!-- Modern Submit Button -->
            <button 
              type="button" 
              :disabled="isLoading"
              @click="submitDiagnosis"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-slate-400 disabled:to-slate-500 text-white font-bold py-4 px-8 rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-105 disabled:scale-100 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center text-lg"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              {{ isLoading ? 'AI Analyzing...' : 'Start AI Diagnosis' }}
            </button>
          </div>
        </div>

        <!-- Modern Results Panel -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-slate-700/50 p-8 order-2 xl:order-2 hover:shadow-3xl transition-all duration-300">
          <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                  AI Diagnosis Results
                </h2>
                <p class="text-slate-600 dark:text-slate-400">
                  Professional analysis powered by advanced AI
                </p>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div v-if="diagnosisResult" class="flex items-center space-x-3">
              <button 
                @click="exportToPDF"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                title="Export to PDF"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
              </button>
              
              <button 
                @click="viewHistory"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                title="View History"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                History
              </button>
            </div>
          </div>
          
          <!-- Modern Loading State -->
          <div v-if="isLoading" class="text-center py-16">
            <div class="relative mb-8">
              <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl shadow-2xl">
                <svg class="animate-spin w-10 h-10 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
              <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/20 to-indigo-600/20 rounded-3xl animate-pulse"></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">AI Analysis in Progress</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">
              Our advanced AI is analyzing your vehicle information and symptoms to provide you with accurate diagnosis...
            </p>
            <div class="flex justify-center space-x-3">
              <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce"></div>
              <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-3 h-3 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
            <div class="mt-8 bg-slate-50 dark:bg-slate-700 rounded-2xl p-6 max-w-md mx-auto">
              <div class="text-sm text-slate-600 dark:text-slate-400 space-y-2">
                <div class="flex items-center justify-between">
                  <span>Processing vehicle data...</span>
                  <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                </div>
                <div class="flex items-center justify-between">
                  <span>Analyzing symptoms...</span>
                  <div class="w-4 h-4 bg-blue-500 rounded-full animate-pulse"></div>
                </div>
                <div class="flex items-center justify-between">
                  <span>Generating diagnosis...</span>
                  <div class="w-4 h-4 bg-purple-500 rounded-full animate-pulse"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modern Results State -->
          <div v-else-if="diagnosisResult" class="space-y-8">
            <!-- AI Provider Badge -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-3">
                <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-full text-sm font-semibold">
                  {{ diagnosisResult?.ai_provider || 'AI' }} Analysis
                </div>
                <div class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-medium">
                  {{ diagnosisResult.confidence_score || 85 }}% Confidence
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button 
                  @click="shareResults"
                  class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-200"
                  title="Share Results"
                >
                  <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                  </svg>
                </button>
                <button 
                  @click="downloadReport"
                  class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-200"
                  title="Download Report"
                >
                  <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </button>
              </div>
            </div>
            <!-- Modern Diagnosis Summary -->
            <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-3xl p-8 border border-blue-200 dark:border-blue-700 shadow-xl">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">AI Diagnosis Summary</h3>
                    <p class="text-slate-600 dark:text-slate-400">Professional analysis powered by advanced AI</p>
                  </div>
                </div>
                <div class="flex items-center space-x-3">
                  <div class="flex items-center px-3 py-1 rounded-full" :class="getSeverityBadgeClass(diagnosisResult.severity)">
                    <div class="w-2 h-2 rounded-full mr-2" :class="getSeverityColor(diagnosisResult.severity)"></div>
                    <span class="text-sm font-semibold" :class="getSeverityTextColor(diagnosisResult.severity)">
                      {{ getSeverityText(diagnosisResult.severity) }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <h4 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">
                  {{ diagnosisResult.problem_title || 'Vehicle Issue Detected' }}
                </h4>
                <p class="text-slate-700 dark:text-slate-300 text-lg leading-relaxed mb-6">
                  {{ diagnosisResult.problem_summary || diagnosisResult.problem_description || diagnosisResult.summary }}
                </p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                      {{ diagnosisResult.confidence_score || 85 }}%
                    </div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">Confidence</div>
                  </div>
                  <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                      {{ (diagnosisResult.likely_causes || diagnosisResult.likelyCauses || []).length || 3 }}
                    </div>
                    <div class="text-sm text-emerald-700 dark:text-emerald-300">Possible Causes</div>
                  </div>
                  <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/30 rounded-xl">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                      {{ (diagnosisResult.recommended_actions || diagnosisResult.recommendedActions || []).length || 4 }}
                    </div>
                    <div class="text-sm text-purple-700 dark:text-purple-300">Solutions</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- AI Insights -->
            <div v-if="diagnosisResult.ai_insights" class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-3xl p-8 border border-indigo-200 dark:border-indigo-700 shadow-xl">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">AI Insights</h3>
                  <p class="text-slate-600 dark:text-slate-400">Advanced analysis and recommendations</p>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <ul class="space-y-4">
                  <li v-for="(insight, index) in diagnosisResult.ai_insights" :key="index" class="flex items-start">
                    <div class="w-6 h-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-4 mt-0.5 flex-shrink-0">
                      <span class="text-white text-sm font-bold">{{ index + 1 }}</span>
                    </div>
                    <p class="text-slate-700 dark:text-slate-300 text-lg leading-relaxed">{{ insight }}</p>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Likely Causes -->
            <div class="bg-gradient-to-r from-orange-50 via-red-50 to-pink-50 dark:from-orange-900/20 dark:via-red-900/20 dark:to-pink-900/20 rounded-3xl p-8 border border-orange-200 dark:border-orange-700 shadow-xl">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Likely Causes</h3>
                  <p class="text-slate-600 dark:text-slate-400">Most probable reasons for the issue</p>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <div class="space-y-6">
                  <div v-for="(cause, index) in (diagnosisResult.likely_causes || diagnosisResult.likelyCauses || [])" :key="index" class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-orange-200 dark:border-orange-700 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mr-4">
                          <span class="text-white text-sm font-bold">{{ index + 1 }}</span>
                        </div>
                        <div>
                          <h4 class="text-xl font-bold text-slate-900 dark:text-white">{{ cause.title || cause.cause || 'Unknown Cause' }}</h4>
                          <p class="text-slate-600 dark:text-slate-400">{{ cause.description || cause.explanation || 'No description available' }}</p>
                        </div>
                      </div>
                      <div class="px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full text-sm font-semibold">
                        {{ cause.probability || 75 }}% probability
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recommended Actions -->
            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 dark:from-emerald-900/20 dark:via-teal-900/20 dark:to-cyan-900/20 rounded-3xl p-8 border border-emerald-200 dark:border-emerald-700 shadow-xl">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Recommended Actions</h3>
                  <p class="text-slate-600 dark:text-slate-400">Step-by-step solutions to fix the issue</p>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <div class="space-y-6">
                  <div v-for="(action, index) in (diagnosisResult.recommended_actions || diagnosisResult.recommendedActions || [])" :key="index" class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-700 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mr-4">
                          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                        <div>
                          <h4 class="text-xl font-bold text-slate-900 dark:text-white">{{ action.title || action.action || 'Recommended Action' }}</h4>
                          <p class="text-slate-600 dark:text-slate-400">{{ action.description || action.explanation || 'No description available' }}</p>
                        </div>
                      </div>
                      <div class="px-4 py-2 rounded-full text-sm font-semibold" :class="getUrgencyBadgeClass(action.urgency)">
                        {{ action.urgency || 'Immediate' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Estimated Costs -->
            <div v-if="diagnosisResult.estimated_costs" class="bg-gradient-to-r from-yellow-50 via-amber-50 to-orange-50 dark:from-yellow-900/20 dark:via-amber-900/20 dark:to-orange-900/20 rounded-3xl p-8 border border-yellow-200 dark:border-yellow-700 shadow-xl">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Estimated Costs</h3>
                  <p class="text-slate-600 dark:text-slate-400">Approximate repair costs and services</p>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-for="cost in diagnosisResult.estimated_costs" :key="cost.service" class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-yellow-200 dark:border-yellow-700 hover:shadow-lg transition-all duration-300">
                    <div class="flex justify-between items-center">
                      <div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white">{{ cost.service }}</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ cost.description || 'Repair service' }}</p>
                      </div>
                      <div class="text-right">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                          €{{ cost.min }} - €{{ cost.max }}
                        </div>
                        <div class="text-sm text-slate-500 dark:text-slate-500">{{ cost.urgency || 'Standard' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-500 mt-6 text-center">
                  * Cost estimates are approximate and may vary based on location and specific vehicle requirements.
                </p>
              </div>
            </div>

            <!-- Safety Warning -->
            <div v-if="diagnosisResult.severity === 'high' || diagnosisResult.severity === 'critical'" class="bg-gradient-to-r from-red-50 via-pink-50 to-rose-50 dark:from-red-900/20 dark:via-pink-900/20 dark:to-rose-900/20 rounded-3xl p-8 border border-red-200 dark:border-red-700 shadow-xl">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Immediate Attention Required</h3>
                  <p class="text-slate-600 dark:text-slate-400">This issue requires immediate professional attention</p>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <div class="flex items-center space-x-4">
                  <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-lg text-slate-700 dark:text-slate-300">
                      This issue requires immediate professional attention to prevent further damage or safety risks.
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-500 mt-2">
                      Please contact a certified mechanic as soon as possible.
                    </p>
                  </div>
                </div>
              </div>
            </div>



            <!-- Modern AI Recommended Parts -->
            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 dark:from-emerald-900/20 dark:via-teal-900/20 dark:to-cyan-900/20 rounded-3xl p-8 border border-emerald-200 dark:border-emerald-700 shadow-xl">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">AI Recommended Parts</h3>
                    <p class="text-slate-600 dark:text-slate-400">Smart suggestions based on your diagnosis</p>
                  </div>
                </div>
                <router-link to="/car-parts" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-2xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                  </svg>
                  Browse All Parts
                </router-link>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <p class="text-slate-700 dark:text-slate-300 mb-6 text-lg">
                  Based on your diagnosis, here are the recommended parts you can purchase from our authorized suppliers:
                </p>
                
                <!-- AI Recommended Parts -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                  <div v-for="(part, index) in getRecommendedParts()" :key="index" class="bg-white dark:bg-slate-800 rounded-2xl border border-emerald-200 dark:border-emerald-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-2 text-lg">{{ part.name }}</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">{{ part.brand }} - {{ part.part_number }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-500 mb-3">{{ part.description }}</p>
                      </div>
                      <div class="w-16 h-16 bg-gradient-to-r from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-xl flex items-center justify-center ml-3">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ part.price_range }}</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" :class="part.availability === 'In stock' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                          {{ part.availability }}
                        </span>
                      </div>
                      <p class="text-sm text-slate-500 dark:text-slate-500">{{ part.compatibility }}</p>
                      <p class="text-sm text-slate-500 dark:text-slate-500">Shipping: {{ part.shipping_time }}</p>
                      <button 
                        @click="searchPartInStore(part)"
                        class="block w-full mt-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-center py-3 px-4 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl"
                      >
                        View in Car Parts Store
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modern Licensed Repair Videos -->
            <div class="bg-gradient-to-r from-orange-50 via-red-50 to-pink-50 dark:from-orange-900/20 dark:via-red-900/20 dark:to-pink-900/20 rounded-3xl p-8 border border-orange-200 dark:border-orange-700 shadow-xl">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Licensed Repair Videos</h3>
                    <p class="text-slate-600 dark:text-slate-400">Professional guides from authorized companies</p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-sm font-semibold rounded-full">
                    Professional Guides
                  </span>
                  <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-medium rounded-full">
                    Licensed Content
                  </span>
                </div>
              </div>
              
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50">
                <p class="text-slate-700 dark:text-slate-300 mb-6 text-lg">
                  Professional repair guides from authorized companies and certified mechanics:
                </p>
                
                <!-- Licensed Repair Videos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                  <div v-for="(video, index) in getRepairVideos()" :key="index" class="bg-white dark:bg-slate-800 rounded-2xl border border-orange-200 dark:border-orange-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-2 text-lg">{{ video.title }}</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">{{ video.company }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-500 mb-3">{{ video.description }}</p>
                      </div>
                      <div class="ml-3 text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-medium" :class="getDifficultyBadgeClass(video.difficulty)">
                          {{ video.difficulty }}
                        </span>
                      </div>
                    </div>
                    
                    <!-- Video Thumbnail -->
                    <div class="relative mb-4 rounded-xl overflow-hidden bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30">
                      <div class="aspect-video flex items-center justify-center">
                        <div class="w-16 h-16 bg-white/90 dark:bg-slate-800/90 rounded-full flex items-center justify-center shadow-lg">
                          <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                          </svg>
                        </div>
                      </div>
                      <div class="absolute top-3 right-3 px-2 py-1 bg-black/70 text-white text-xs rounded">
                        {{ video.duration }}
                      </div>
                    </div>
                    
                    <div class="space-y-3">
                      <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Duration: {{ video.duration }}</span>
                        <span class="text-slate-600 dark:text-slate-400">{{ video.estimated_time }}</span>
                      </div>
                      <div v-if="video.licensed" class="flex items-center gap-2 text-sm">
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-medium">
                          ✓ Licensed
                        </span>
                        <span class="text-slate-500 dark:text-slate-400">{{ video.source }}</span>
                      </div>
                      <div v-if="video.tools_required && video.tools_required.length > 0" class="text-sm text-slate-500 dark:text-slate-500">
                        <strong>Tools needed:</strong> {{ video.tools_required.join(', ') }}
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-500">{{ video.views }} views</span>
                        <span class="text-sm text-slate-500 dark:text-slate-500">{{ video.rating }}/5 ⭐</span>
                      </div>
                      <button 
                        @click="openLicensedVideo(video)"
                        class="block w-full mt-4 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white text-center py-3 px-4 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Watch Licensed Video
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Modern Empty State -->
          <div v-else class="text-center py-16">
            <div class="relative mb-8">
              <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-slate-400 to-slate-500 rounded-3xl shadow-xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div class="absolute -inset-4 bg-gradient-to-r from-slate-400/20 to-slate-500/20 rounded-3xl animate-pulse"></div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Ready for AI Diagnosis</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">
              Fill out the form on the left and submit to get instant AI-powered diagnosis results for your vehicle.
            </p>
            
            <!-- Modern Quick Tips -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 text-left max-w-lg mx-auto border border-blue-200 dark:border-blue-700">
              <h4 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                </div>
                Pro Tips for Better Results
              </h4>
              <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-3">
                <li class="flex items-start">
                  <div class="w-6 h-6 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">1</span>
                  </div>
                  <span>Describe symptoms in detail for maximum accuracy</span>
                </li>
                <li class="flex items-start">
                  <div class="w-6 h-6 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">2</span>
                  </div>
                  <span>Upload photos or videos of the problem area</span>
                </li>
                <li class="flex items-start">
                  <div class="w-6 h-6 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">3</span>
                  </div>
                  <span>Use voice recording for hands-free input</span>
                </li>
                <li class="flex items-start">
                  <div class="w-6 h-6 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">4</span>
                  </div>
                  <span>Select all relevant symptoms from the checklist</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed, nextTick, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { diagnosisAPI, carsAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'
import CarModelSelector from '../components/CarModelSelector.vue'
import axios from 'axios'

export default {
  name: 'Diagnose',
  components: {
    CarModelSelector
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    
    // Use the global auth state
    const { user, isAuthenticated, checkAuth } = useAuth()
    const currentStep = ref(0)
    const isLoading = ref(false)
    const diagnosisResult = ref(null)
    const fileInput = ref(null)
    const photoInput = ref(null)
    const videoInput = ref(null)
    const audioInput = ref(null)
    const uploadedFiles = ref([])
    const selectedCar = ref(null)
    const carBrands = ref([])
    const carModels = ref([])
    
    // Media upload state
    const activeMediaType = ref('photos')
    const activeInputType = ref('text')
    
    // Voice recording state
    const isRecording = ref(false)
    const isPaused = ref(false)
    const isProcessingAudio = ref(false)
    const recordedAudio = ref(null)
    const transcribedText = ref('')
    const recordingTime = ref(0)
    const mediaRecorder = ref(null)
    const audioChunks = ref([])
    const recordingInterval = ref(null)
    const microphonePermissionGranted = ref(false)
    const audioBlob = ref(null)
    const audioUrl = ref(null)
    const recordingStartTime = ref(null)
    const formErrors = ref([])

    const steps = ['Vehicle Info', 'Symptoms', 'AI Analysis', 'Results']

    const diagnosisForm = reactive({
      make: '',
      model: '',
      year: '',
      mileage: '',
      engineType: '',
      engineSize: '',
      description: '',
      symptoms: [],
      selectedCarId: null
    })

    // Form persistence functions
    const saveFormData = () => {
      const formData = {
        make: diagnosisForm.make,
        model: diagnosisForm.model,
        year: diagnosisForm.year,
        mileage: diagnosisForm.mileage,
        engineType: diagnosisForm.engineType,
        engineSize: diagnosisForm.engineSize,
        description: diagnosisForm.description,
        symptoms: diagnosisForm.symptoms,
        selectedCarId: diagnosisForm.selectedCarId,
        timestamp: Date.now()
      }
      localStorage.setItem('diagnosis_form_data', JSON.stringify(formData))
      console.log('💾 Form data saved to localStorage')
    }

    const loadFormData = () => {
      try {
        const savedData = localStorage.getItem('diagnosis_form_data')
        if (savedData) {
          const formData = JSON.parse(savedData)
          // Only load if data is less than 24 hours old
          if (Date.now() - formData.timestamp < 24 * 60 * 60 * 1000) {
            diagnosisForm.make = formData.make || ''
            diagnosisForm.model = formData.model || ''
            diagnosisForm.year = formData.year || ''
            diagnosisForm.mileage = formData.mileage || ''
            diagnosisForm.engineType = formData.engineType || ''
            diagnosisForm.engineSize = formData.engineSize || ''
            diagnosisForm.description = formData.description || ''
            diagnosisForm.symptoms = formData.symptoms || []
            diagnosisForm.selectedCarId = formData.selectedCarId || null
            console.log('📂 Form data loaded from localStorage')
            return true
          } else {
            // Clear old data
            localStorage.removeItem('diagnosis_form_data')
            console.log('🗑️ Old form data cleared')
          }
        }
      } catch (error) {
        console.error('Error loading form data:', error)
        localStorage.removeItem('diagnosis_form_data')
      }
      return false
    }

    const clearFormData = () => {
      localStorage.removeItem('diagnosis_form_data')
      console.log('🗑️ Form data cleared from localStorage')
    }

    const commonSymptoms = [
      'Engine won\'t start',
      'Rough idle',
      'Stalling',
      'Poor acceleration',
      'Strange noises',
      'Warning lights on',
      'Smoke from exhaust',
      'Overheating',
      'Poor fuel economy',
      'Transmission issues',
      'Brake problems',
      'Steering issues',
      'Electrical problems',
      'AC not working',
      'Suspension problems'
    ]

    const loadCarBrands = async () => {
      try {
        const response = await axios.get('/api/car-brands/popular')
        if (response.data.success) {
          carBrands.value = response.data.data
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
        
        const response = await axios.get(`/api/car-models/brand/${brand.id}`)
        if (response.data.success) {
          carModels.value = response.data.data
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

    // Save diagnosis to user history
    const saveDiagnosisToHistory = async (result, sessionId) => {
      try {
        const diagnosisData = {
          session_id: sessionId,
          vehicle_info: {
            make: diagnosisForm.make,
            model: diagnosisForm.model,
            year: diagnosisForm.year,
            mileage: diagnosisForm.mileage,
            engine_type: diagnosisForm.engineType,
            engine_size: diagnosisForm.engineSize
          },
          symptoms: diagnosisForm.symptoms,
          description: diagnosisForm.description,
          result: result,
          created_at: new Date().toISOString(),
          status: 'completed'
        }

        // Save to localStorage for immediate access
        const existingHistory = JSON.parse(localStorage.getItem('diagnosis_history') || '[]')
        existingHistory.unshift(diagnosisData)
        
        // Keep only last 50 diagnoses
        if (existingHistory.length > 50) {
          existingHistory.splice(50)
        }
        
        localStorage.setItem('diagnosis_history', JSON.stringify(existingHistory))

        // Note: Diagnosis is already saved to backend database via submitDiagnosis API
        // The DiagnosisSession model stores the diagnosis data
        console.log('Diagnosis saved to localStorage history successfully')
        console.log('Diagnosis is already saved to backend database via submitDiagnosis API')
      } catch (error) {
        console.error('Error saving diagnosis to history:', error)
      }
    }

    // Export diagnosis to PDF
    const exportToPDF = async () => {
      try {
        if (!diagnosisResult.value) {
          alert('No diagnosis result to export')
          return
        }

        // Create PDF content
        const pdfContent = {
          title: 'CarWise.ai - AI Diagnosis Report',
          vehicle: {
            make: diagnosisForm.make,
            model: diagnosisForm.model,
            year: diagnosisForm.year,
            mileage: diagnosisForm.mileage,
            engine_type: diagnosisForm.engineType,
            engine_size: diagnosisForm.engineSize
          },
          symptoms: diagnosisForm.symptoms,
          description: diagnosisForm.description,
          result: diagnosisResult.value,
          generated_at: new Date().toISOString()
        }

        // Call backend to generate PDF
        const response = await axios.post('/api/diagnosis/export-pdf', pdfContent, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          responseType: 'blob'
        })

        // Create download link
        const blob = new Blob([response.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `CarWise_Diagnosis_${diagnosisForm.make}_${diagnosisForm.model}_${new Date().toISOString().split('T')[0]}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)

        console.log('PDF exported successfully')
      } catch (error) {
        console.error('Error exporting PDF:', error)
        alert('Failed to export PDF. Please try again.')
      }
    }

    // View diagnosis history
    const viewHistory = () => {
      router.push('/diagnosis-history')
    }

    // Search specific part in car-parts store
    const searchPartInStore = (part) => {
      // Create search parameters for the car-parts page
      const searchParams = {
        query: part.name,
        make: diagnosisForm.make,
        model: diagnosisForm.model,
        year: diagnosisForm.year,
        category: part.category || 'general',
        brand: part.brand
      }

      // Navigate to car-parts with search parameters
      router.push({
        path: '/car-parts',
        query: searchParams
      })
    }

    // Open licensed video
    const openLicensedVideo = (video) => {
      if (video.url && video.url !== '#') {
        // Open in new tab
        window.open(video.url, '_blank', 'noopener,noreferrer')
      } else {
        // Show modal or redirect to video platform
        alert(`Opening licensed video: ${video.title} by ${video.company}`)
      }
    }

    const loadCarData = async (carId) => {
      try {
        isLoading.value = true
        const response = await carsAPI.getById(carId)
        if (response.data.success) {
          selectedCar.value = response.data.car
          // Pre-fill the form with car data
          diagnosisForm.make = selectedCar.value.brand
          diagnosisForm.model = selectedCar.value.model
          diagnosisForm.year = selectedCar.value.year.toString()
          diagnosisForm.mileage = selectedCar.value.mileage ? selectedCar.value.mileage.toString() : ''
          diagnosisForm.engineType = selectedCar.value.fuel_type || ''
          diagnosisForm.engineSize = selectedCar.value.specifications?.engine_size || ''
          
          // Load models for the car's brand
          loadCarModels(selectedCar.value.brand)
          
        }
      } catch (error) {
        console.error('Error loading car data:', error)
        alert('Error loading car data. Please try again.')
      } finally {
        isLoading.value = false
      }
    }

    const triggerFileUpload = (type = 'photos') => {
      if (type === 'photos') {
        photoInput.value.click()
      } else if (type === 'videos') {
        videoInput.value.click()
      } else if (type === 'audio') {
        audioInput.value.click()
      }
    }

    const handleFileSelect = (event) => {
      const files = Array.from(event.target.files)
      processFiles(files)
    }

    const handleFileDrop = (event) => {
      const files = Array.from(event.dataTransfer.files)
      processFiles(files)
    }

    const processFiles = (files) => {
      files.forEach(file => {
        const fileType = file.type
        let maxFiles = 5
        let maxSize = 10 * 1024 * 1024 // 10MB
        
        // Set limits based on file type
        if (fileType.startsWith('video/')) {
          maxFiles = 2
          maxSize = 50 * 1024 * 1024 // 50MB
        } else if (fileType.startsWith('audio/')) {
          maxFiles = 3
          maxSize = 20 * 1024 * 1024 // 20MB
        }
        
        // Check file size and count limits
        if (file.size > maxSize) {
          alert(`File ${file.name} is too large. Maximum size is ${Math.round(maxSize / (1024 * 1024))}MB.`)
          return
        }
        
        if (uploadedFiles.value.length >= maxFiles) {
          alert(`Maximum ${maxFiles} files allowed.`)
          return
        }
        
        // Process the file
        if (fileType.startsWith('image/') || fileType.startsWith('video/') || fileType.startsWith('audio/')) {
          const reader = new FileReader()
          reader.onload = (e) => {
            uploadedFiles.value.push({
              file: file,
              preview: e.target.result,
              type: fileType.startsWith('image/') ? 'image' : fileType.startsWith('video/') ? 'video' : 'audio'
            })
          }
          reader.readAsDataURL(file)
        }
      })
    }

    const removeFile = (index) => {
      uploadedFiles.value.splice(index, 1)
    }

    // Voice recording functions
    const toggleRecording = async () => {
      console.log('toggleRecording called, isRecording:', isRecording.value, 'isPaused:', isPaused.value)
      if (isRecording.value) {
        if (isPaused.value) {
          resumeRecording()
        } else {
          pauseRecording()
        }
      } else {
        startRecording()
      }
    }

    const stopRecording = () => {
      if (mediaRecorder.value && isRecording.value) {
        mediaRecorder.value.stop()
        isRecording.value = false
        isPaused.value = false
        clearInterval(recordingInterval.value)
        recordingTime.value = 0
      }
    }

    const pauseRecording = () => {
      if (mediaRecorder.value && isRecording.value && !isPaused.value) {
        mediaRecorder.value.pause()
        isPaused.value = true
        clearInterval(recordingInterval.value)
      }
    }

    const resumeRecording = () => {
      if (mediaRecorder.value && isRecording.value && isPaused.value) {
        mediaRecorder.value.resume()
        isPaused.value = false
        startRecordingTimer()
      }
    }

    const startRecordingTimer = () => {
      recordingTime.value = 0
      recordingInterval.value = setInterval(() => {
        recordingTime.value++
      }, 1000)
    }

    const startRecording = async () => {
      try {
        // Check if getUserMedia is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          throw new Error('getUserMedia is not supported in this browser')
        }

        // Check if microphone permission is already granted
        if (!microphonePermissionGranted.value) {
          const permissionGranted = await requestMicrophonePermission()
          if (!permissionGranted) {
            throw new Error('Microphone permission required. Please allow microphone access to record audio.')
          }
        }

        // Request microphone access with better error handling
        const stream = await navigator.mediaDevices.getUserMedia({ 
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            sampleRate: 44100
          } 
        })
        
        mediaRecorder.value = new MediaRecorder(stream, {
          mimeType: 'audio/webm;codecs=opus'
        })
        audioChunks.value = []
        
        mediaRecorder.value.ondataavailable = (event) => {
          if (event.data.size > 0) {
            audioChunks.value.push(event.data)
          }
        }
        
        mediaRecorder.value.onstop = () => {
          audioBlob.value = new Blob(audioChunks.value, { type: 'audio/webm' })
          audioUrl.value = URL.createObjectURL(audioBlob.value)
          recordedAudio.value = audioUrl.value
          stream.getTracks().forEach(track => track.stop())
        }
        
        mediaRecorder.value.start(1000) // Collect data every second
        isRecording.value = true
        isPaused.value = false
        recordingStartTime.value = Date.now()
        microphonePermissionGranted.value = true
        
        console.log('Recording started, isRecording:', isRecording.value, 'isPaused:', isPaused.value)
        
        // Start timer
        startRecordingTimer()
        
      } catch (error) {
        console.error('Error starting recording:', error)
        
        let errorMessage = 'Could not access microphone. '
        
        if (error.name === 'NotAllowedError') {
          errorMessage += 'Please allow microphone access and try again.'
        } else if (error.name === 'NotFoundError') {
          errorMessage += 'No microphone found. Please connect a microphone and try again.'
        } else if (error.name === 'NotSupportedError') {
          errorMessage += 'Microphone access is not supported in this browser.'
        } else if (error.name === 'NotReadableError') {
          errorMessage += 'Microphone is being used by another application.'
        } else if (error.message.includes('permission denied') || error.message.includes('permission required')) {
          errorMessage += 'Microphone permission required. Please allow microphone access when prompted by your browser.\n\n'
          errorMessage += 'Chrome/Edge: Click the microphone icon in the address bar and select "Allow"\n'
          errorMessage += 'Firefox: Click "Allow" when the permission dialog appears\n'
          errorMessage += 'Safari: Go to Safari > Preferences > Websites > Microphone and allow access'
        } else {
          errorMessage += 'Please check your microphone permissions and try again.'
        }
        
        // Show a more helpful error message
        alert(errorMessage)
        
        // Optionally, you could show a modal with instructions instead of alert
        // showMicrophonePermissionModal()
      }
    }

    const clearRecording = () => {
      recordedAudio.value = null
      transcribedText.value = ''
      recordingTime.value = 0
      audioBlob.value = null
      if (audioUrl.value) {
        URL.revokeObjectURL(audioUrl.value)
        audioUrl.value = null
      }
      if (mediaRecorder.value && mediaRecorder.value.state !== 'inactive') {
        mediaRecorder.value.stop()
      }
      isRecording.value = false
      isPaused.value = false
      clearInterval(recordingInterval.value)
    }

    const transcribeAudio = async () => {
      if (!audioBlob.value) return
      
      isProcessingAudio.value = true
      try {
        // Create FormData for API call
        const formData = new FormData()
        formData.append('audio', audioBlob.value, 'recording.webm')
        
        // Call transcription API
        const transcriptionResponse = await axios.post('/api/transcribe-audio', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        if (transcriptionResponse.data.success) {
          transcribedText.value = transcriptionResponse.data.text
          // Auto-fill the description field with transcribed text
          diagnosisForm.description = transcribedText.value
        } else {
          throw new Error(transcriptionResponse.data.message || 'Transcription failed')
        }
        
      } catch (error) {
        console.error('Transcription error:', error)
        // Fallback: show a placeholder text
        transcribedText.value = 'Transcription failed. Please try typing your description instead.'
        alert('Error transcribing audio. Please try again or type your description manually.')
      } finally {
        isProcessingAudio.value = false
      }
    }

    const useTranscribedText = () => {
      diagnosisForm.description = transcribedText.value
      activeInputType.value = 'text'
    }

    const formatTime = (seconds) => {
      const mins = Math.floor(seconds / 60)
      const secs = seconds % 60
      return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    }

    const checkMicrophonePermission = async () => {
      try {
        // Check if browser supports mediaDevices
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          microphonePermissionGranted.value = false
          return
        }

        // Check if permissions API is supported
        if (navigator.permissions) {
          try {
            const permissionStatus = await navigator.permissions.query({ name: 'microphone' })
            microphonePermissionGranted.value = permissionStatus.state === 'granted'
            
            // Listen for permission changes
            permissionStatus.onchange = () => {
              microphonePermissionGranted.value = permissionStatus.state === 'granted'
            }
          } catch (permError) {
            // Fallback: try to request permission directly
            await requestMicrophonePermission()
          }
        } else {
          // Fallback for browsers that don't support permissions API
          microphonePermissionGranted.value = false
        }
      } catch (error) {
        microphonePermissionGranted.value = false
      }
    }

    const requestMicrophonePermission = async () => {
      try {
        // Check if getUserMedia is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          throw new Error('getUserMedia is not supported in this browser')
        }

        // Try to get microphone access to check permission
        const stream = await navigator.mediaDevices.getUserMedia({ 
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
          } 
        })
        
        // If successful, stop the stream and mark permission as granted
        stream.getTracks().forEach(track => track.stop())
        microphonePermissionGranted.value = true
        return true
      } catch (error) {
        microphonePermissionGranted.value = false
        return false
      }
    }

    const refreshMicrophonePermission = async () => {
      await checkMicrophonePermission()
    }

    const validateForm = () => {
      formErrors.value = []
      
      console.log('🔍 Validating form with data:', {
        make: diagnosisForm.make,
        model: diagnosisForm.model,
        year: diagnosisForm.year,
        description: diagnosisForm.description,
        descriptionLength: diagnosisForm.description?.length
      })
      
      if (!diagnosisForm.make) {
        formErrors.value.push('Please select a car make')
      }
      
      if (!diagnosisForm.model) {
        formErrors.value.push('Please select a car model')
      }
      
      if (!diagnosisForm.year) {
        formErrors.value.push('Please enter the car year')
      } else if (diagnosisForm.year < 1990 || diagnosisForm.year > new Date().getFullYear() + 1) {
        formErrors.value.push('Please enter a valid year (1990 - ' + (new Date().getFullYear() + 1) + ')')
      }
      
      if (!diagnosisForm.description || !diagnosisForm.description.trim()) {
        formErrors.value.push('Please describe the problem or symptoms')
      }
      
      console.log('✅ Form validation result:', {
        isValid: formErrors.value.length === 0,
        errors: formErrors.value
      })
      
      return formErrors.value.length === 0
    }

    const submitDiagnosis = async () => {
      // Prevent multiple submissions
      if (isLoading.value) {
        return
      }

      // Validate form before submission
      if (!validateForm()) {
        // Show validation errors
        console.log('Form validation failed:', formErrors.value)
        alert('Please fill in all required fields:\n' + formErrors.value.join('\n'))
        // Scroll to top to show validation errors
        window.scrollTo({ top: 0, behavior: 'smooth' })
        return
      }
      
      // SMART CACHE CLEARING - Keep authentication data
      
      // Save authentication data before clearing
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      // Clear Vue reactive data
      diagnosisResult.value = null
      currentStep.value = 0
      
      // Clear diagnosis-specific cache only
      localStorage.removeItem('lastDiagnosisResult')
      localStorage.removeItem('lastDiagnosisSession')
      localStorage.removeItem('diagnosisCache')
      sessionStorage.removeItem('diagnosisCache')
      
      // Restore authentication data
      if (token) localStorage.setItem('token', token)
      if (user) localStorage.setItem('user', user)
      
      // Clear browser caches (but keep auth)
      if ('caches' in window) {
        caches.keys().then(names => {
          names.forEach(name => {
            if (name.includes('diagnosis') || name.includes('carwise')) {
              caches.delete(name)
            }
          })
        })
      }
      
      // Check if user is logged in
      const authToken = localStorage.getItem('token')
      const authUser = localStorage.getItem('user')
      
      if (!authToken) {
        alert('You must be logged in to submit diagnosis!')
        router.push('/login')
        return
      }

      // Debug: Log form data before submission
      console.log('🚀 Starting diagnosis submission...')
      console.log('Form data:', {
        make: diagnosisForm.make,
        model: diagnosisForm.model,
        year: diagnosisForm.year,
        mileage: diagnosisForm.mileage,
        engineType: diagnosisForm.engineType,
        engineSize: diagnosisForm.engineSize,
        description: diagnosisForm.description,
        symptoms: diagnosisForm.symptoms,
        selectedCarId: diagnosisForm.selectedCarId
      })
      console.log('User authenticated:', !!authToken)
      console.log('Uploaded files:', uploadedFiles.value.length)

      isLoading.value = true
      currentStep.value = 2

      try {
        const formData = new FormData()
        formData.append('make', diagnosisForm.make)
        formData.append('model', diagnosisForm.model)
        formData.append('year', diagnosisForm.year)
        formData.append('mileage', diagnosisForm.mileage)
        formData.append('engine_type', diagnosisForm.engineType)
        formData.append('engine_size', diagnosisForm.engineSize)
        formData.append('description', diagnosisForm.description)
        
        // Add car_id if a car is selected
        if (diagnosisForm.selectedCarId) {
          formData.append('car_id', diagnosisForm.selectedCarId)
          console.log('🚗 Adding car_id to diagnosis:', diagnosisForm.selectedCarId)
        } else {
          console.log('⚠️ No car_id selected for diagnosis')
        }
        
        // Add symptoms as individual form fields
        diagnosisForm.symptoms.forEach((symptom, index) => {
          formData.append(`symptoms[${index}]`, symptom)
        })

        // Add uploaded files
        uploadedFiles.value.forEach((fileObj, index) => {
          formData.append(`photos[${index}]`, fileObj.file)
        })

        console.log('Calling diagnosisAPI.startDiagnosis...')
        const requestData = {
          make: diagnosisForm.make,
          model: diagnosisForm.model,
          year: diagnosisForm.year,
          mileage: diagnosisForm.mileage,
          engine_type: diagnosisForm.engineType,
          engine_size: diagnosisForm.engineSize,
          description: diagnosisForm.description,
          symptoms: diagnosisForm.symptoms || [],
          car_id: diagnosisForm.selectedCarId || null
        }
        const response = await diagnosisAPI.startDiagnosis(requestData)
        console.log('Submit response:', response.data)
        console.log('Session ID:', response.data.data?.session_id)
        console.log('Full response structure:', JSON.stringify(response.data, null, 2))
        
        // Get results immediately since processing is now synchronous
        try {
          const sessionId = response.data.data?.session_id
          console.log('Getting results for session:', sessionId)
          
          // Get fresh results with cache busting
          const resultResponse = await diagnosisAPI.getResult(sessionId)
          console.log('Result response:', resultResponse.data)
          
          // Ensure we get fresh translated results
          const freshResult = resultResponse.data.data?.result
          console.log('🎯 FRESH RESULT FROM AI:', freshResult)
          console.log('AI Provider:', freshResult?.ai_provider)
          console.log('Problem Title:', freshResult?.problem_title)
          
          // SIMPLE AND CLEAN RESULT SETTING
          diagnosisResult.value = freshResult
          currentStep.value = 3
          isLoading.value = false
          
          console.log('✅ RESULT SET SUCCESSFULLY')
          console.log('Current diagnosisResult:', diagnosisResult.value)
          
          // Save diagnosis to user history
          await saveDiagnosisToHistory(freshResult, sessionId)
          
          // Show success notification
          if (window.$notify) {
            window.$notify.success('Diagnosis Complete', 'AI analysis has been completed successfully')
          }
        } catch (resultError) {
          console.error('Error getting diagnosis result:', resultError)
          isLoading.value = false
          // Show error message
          if (window.$notify) {
            window.$notify.error('Error', 'Failed to get diagnosis result. Please try again.')
          }
          currentStep.value = 1 // Go back to form
        }
      } catch (error) {
        console.error('=== DIAGNOSIS ERROR ===')
        console.error('Error details:', error)
        console.error('Error response:', error.response)
        console.error('Error message:', error.message)
        isLoading.value = false
        currentStep.value = 1 // Go back to form
        console.log('Error state set - isLoading:', isLoading.value, 'currentStep:', currentStep.value)
        
        // Show error message
        if (window.$notify) {
          window.$notify.error('Error', 'Failed to submit diagnosis. Please try again.')
        }
      }
    }

    const getSeverityColor = (severity) => {
      switch (severity.toLowerCase()) {
        case 'low': return 'bg-success-500'
        case 'medium': return 'bg-warning-500'
        case 'high': return 'bg-danger-500'
        default: return 'bg-secondary-500'
      }
    }

    const getSeverityTextColor = (severity) => {
      switch (severity.toLowerCase()) {
        case 'low': return 'text-success-600 dark:text-success-400'
        case 'medium': return 'text-warning-600 dark:text-warning-400'
        case 'high': return 'text-danger-600 dark:text-danger-400'
        default: return 'text-secondary-600 dark:text-secondary-400'
      }
    }

    const getSeverityText = (severity) => {
      const severityMap = {
        'low': 'Low Priority',
        'medium': 'Medium Priority',
        'high': 'High Priority',
        'critical': 'Critical Priority'
      }
      return severityMap[severity?.toLowerCase()] || 'Medium Priority'
    }

    // Computed properties for debug info
    const debugInfo = computed(() => {
      if (typeof window === 'undefined') return { cacheStatus: 'Unknown', authStatus: 'Unknown' }
      
      return {
        cacheStatus: localStorage.getItem('diagnosisCache') ? 'Cached' : 'Fresh',
        authStatus: localStorage.getItem('token') ? 'Logged In' : 'Not Logged In'
      }
    })

    const refreshResults = () => {
      console.log('Refreshing results...')
      
      // Save authentication data before clearing
      const refreshToken = localStorage.getItem('token')
      const refreshUser = localStorage.getItem('user')
      
      // Clear diagnosis-specific cache only
      localStorage.removeItem('lastDiagnosisResult')
      localStorage.removeItem('lastDiagnosisSession')
      localStorage.removeItem('diagnosisCache')
      sessionStorage.removeItem('diagnosisCache')
      
      // Restore authentication data
      if (refreshToken) localStorage.setItem('token', refreshToken)
      if (refreshUser) localStorage.setItem('user', refreshUser)
      
      if ('caches' in window) {
        caches.keys().then(names => {
          names.forEach(name => {
            if (name.includes('diagnosis') || name.includes('carwise')) {
              caches.delete(name)
            }
          })
        })
      }
      
      // Clear current result
      diagnosisResult.value = null
      
      // Force hard reload with cache busting
      window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now()
    }

    const forceRefreshResults = () => {
      console.log('🚨 FORCE REFRESH - CLEARING EVERYTHING (keeping auth)...')
      
      // Save authentication data before clearing
      const forceToken = localStorage.getItem('token')
      const forceUser = localStorage.getItem('user')
      
      // Clear ALL data
      diagnosisResult.value = null
      currentStep.value = 0
      isLoading.value = false
      
      // Clear ALL storage except auth
      localStorage.clear()
      sessionStorage.clear()
      
      // Restore authentication data
      if (forceToken) localStorage.setItem('token', forceToken)
      if (forceUser) localStorage.setItem('user', forceUser)
      
      // Clear ALL caches
      if ('caches' in window) {
        caches.keys().then(names => {
          names.forEach(name => {
            caches.delete(name)
          })
        })
      }
      
      // Clear indexedDB
      if ('indexedDB' in window) {
        indexedDB.databases().then(databases => {
          databases.forEach(db => {
            indexedDB.deleteDatabase(db.name)
          })
        })
      }
      
      // Force complete page reload with cache busting
      const url = new URL(window.location)
      url.searchParams.set('_t', Date.now())
      url.searchParams.set('_r', Math.random())
      window.location.href = url.toString()
    }

    const quickLogin = async () => {
      console.log('🔑 QUICK LOGIN - Testing authentication...')
      
      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            email: 'test@example.com',
            password: 'password',
            remember: true
          })
        })
        
        const data = await response.json()
        
        if (data.success) {
          localStorage.setItem('token', data.token)
          localStorage.setItem('user', JSON.stringify(data.user))
          console.log('✅ Quick login successful!')
          alert('Login successful! You can now submit diagnosis.')
          // Refresh the page to update authentication state
          window.location.reload()
        } else {
          console.log('❌ Quick login failed:', data.message)
          alert('Login failed: ' + data.message)
        }
      } catch (error) {
        console.log('❌ Quick login error:', error)
        alert('Login error: ' + error.message)
      }
    }

    // Load car data if car ID is provided in URL
    onMounted(() => {
      loadCarBrands()
      checkMicrophonePermission()
      
      // First, try to load saved form data
      const hasLoadedFormData = loadFormData()
      
      // Check if car data is stored in localStorage (from My Cars page)
      const selectedCarData = localStorage.getItem('selectedCarForDiagnosis')
      console.log('Raw localStorage data:', selectedCarData)
      
      if (selectedCarData) {
        try {
          const carData = JSON.parse(selectedCarData)
          console.log('Parsed car data:', carData)
          console.log('Pre-filling form with car data:', carData)
          
          // Pre-fill the form with car data (this will override saved form data)
          diagnosisForm.make = carData.brand
          diagnosisForm.model = carData.model
          diagnosisForm.year = carData.year
          diagnosisForm.mileage = carData.mileage
          diagnosisForm.engineType = carData.engine_type
          diagnosisForm.engineSize = carData.engine_size
          diagnosisForm.selectedCarId = carData.id
          
          console.log('Form pre-filled with:', {
            make: diagnosisForm.make,
            model: diagnosisForm.model,
            year: diagnosisForm.year,
            mileage: diagnosisForm.mileage,
            engineType: diagnosisForm.engineType,
            engineSize: diagnosisForm.engineSize,
            selectedCarId: diagnosisForm.selectedCarId
          })
          
          // Force reactivity update
          nextTick(() => {
            console.log('Form after nextTick:', {
              make: diagnosisForm.make,
              model: diagnosisForm.model,
              year: diagnosisForm.year,
              mileage: diagnosisForm.mileage,
              engineType: diagnosisForm.engineType,
              engineSize: diagnosisForm.engineSize,
              selectedCarId: diagnosisForm.selectedCarId
            })
          })
          
          // Clear the stored data
          localStorage.removeItem('selectedCarForDiagnosis')
          
          // Load car models for the selected brand
          loadCarModels(carData.brand)
        } catch (error) {
          console.error('Error parsing car data:', error)
        }
      } else if (hasLoadedFormData) {
        // If we loaded form data, also load car models for the selected brand
        if (diagnosisForm.make) {
          loadCarModels(diagnosisForm.make)
        }
      }
      
      // Also check for car ID in query params (fallback)
      const carId = route.query.car
      if (carId) {
        console.log('Loading car data for ID:', carId)
        loadCarData(carId)
      }
    })

    // Watch for form changes and save to localStorage
    watch(() => diagnosisForm.make, () => {
      if (diagnosisForm.make) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.model, () => {
      if (diagnosisForm.model) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.year, () => {
      if (diagnosisForm.year) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.mileage, () => {
      saveFormData()
    })

    watch(() => diagnosisForm.engineType, () => {
      if (diagnosisForm.engineType) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.engineSize, () => {
      if (diagnosisForm.engineSize) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.description, () => {
      if (diagnosisForm.description && diagnosisForm.description.length > 10) {
        saveFormData()
      }
    })

    watch(() => diagnosisForm.symptoms, () => {
      if (diagnosisForm.symptoms.length > 0) {
        saveFormData()
      }
    }, { deep: true })

    // Clear form data when diagnosis is completed successfully
    watch(() => diagnosisResult.value, (newResult) => {
      if (newResult && newResult.status === 'completed') {
        // Clear form data after successful diagnosis
        setTimeout(() => {
          clearFormData()
        }, 5000) // Clear after 5 seconds
      }
    })

    // Modern Results Methods
    const shareResults = () => {
      if (navigator.share && diagnosisResult.value) {
        navigator.share({
          title: 'AI Car Diagnosis Results',
          text: `Check out my AI car diagnosis results: ${diagnosisResult.value.problem_title || 'Vehicle Issue Detected'}`,
          url: window.location.href
        }).catch(console.error)
      } else {
        // Fallback: copy to clipboard
        const text = `AI Car Diagnosis Results: ${diagnosisResult.value?.problem_title || 'Vehicle Issue Detected'}\n${window.location.href}`
        navigator.clipboard.writeText(text).then(() => {
          alert('Results link copied to clipboard!')
        }).catch(() => {
          alert('Unable to share results')
        })
      }
    }

    const downloadReport = () => {
      if (!diagnosisResult.value) return
      
      const reportData = {
        title: 'AI Car Diagnosis Report',
        date: new Date().toLocaleDateString(),
        vehicle: {
          make: diagnosisForm.make,
          model: diagnosisForm.model,
          year: diagnosisForm.year,
          mileage: diagnosisForm.mileage
        },
        diagnosis: diagnosisResult.value
      }
      
      const blob = new Blob([JSON.stringify(reportData, null, 2)], { type: 'application/json' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `car-diagnosis-report-${Date.now()}.json`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      URL.revokeObjectURL(url)
    }

    const getRecommendedParts = () => {
      // Sample recommended parts based on common issues
      return [
        {
          name: 'Engine Air Filter',
          brand: 'Bosch',
          part_number: 'AF1234',
          description: 'High-quality air filter for improved engine performance',
          price_range: '$15-25',
          availability: 'In stock',
          compatibility: 'Compatible with your vehicle',
          shipping_time: '2-3 business days'
        },
        {
          name: 'Oil Filter',
          brand: 'Mann-Filter',
          part_number: 'OF5678',
          description: 'Premium oil filter for optimal engine protection',
          price_range: '$8-15',
          availability: 'In stock',
          compatibility: 'Compatible with your vehicle',
          shipping_time: '1-2 business days'
        },
        {
          name: 'Spark Plugs Set',
          brand: 'NGK',
          part_number: 'SP9012',
          description: 'Iridium spark plugs for better fuel efficiency',
          price_range: '$25-40',
          availability: 'In stock',
          compatibility: 'Compatible with your vehicle',
          shipping_time: '3-5 business days'
        }
      ]
    }

    const getRepairVideos = () => {
      // Licensed repair videos from authorized companies
      return [
        {
          title: 'Engine Air Filter Replacement',
          company: 'Bosch Automotive',
          description: 'Step-by-step guide to replace your engine air filter',
          difficulty: 'Easy',
          duration: '5:30',
          estimated_time: '15-20 minutes',
          tools_required: ['Screwdriver', 'New air filter'],
          views: '125K',
          rating: '4.8',
          url: 'https://www.bosch-automotive.com/repair-guides/air-filter-replacement',
          licensed: true,
          source: 'Bosch Official Channel'
        },
        {
          title: 'Oil Change Tutorial',
          company: 'Castrol Professional',
          description: 'Complete oil change procedure with safety tips',
          difficulty: 'Medium',
          duration: '12:45',
          estimated_time: '30-45 minutes',
          tools_required: ['Oil filter wrench', 'Drain pan', 'New oil', 'New filter'],
          views: '89K',
          rating: '4.9',
          url: 'https://www.castrol.com/professional/repair-guides/oil-change',
          licensed: true,
          source: 'Castrol Professional'
        },
        {
          title: 'Brake Pad Replacement',
          company: 'Brembo Official',
          description: 'Professional brake pad replacement guide',
          difficulty: 'Medium',
          duration: '18:20',
          estimated_time: '45-60 minutes',
          tools_required: ['Jack', 'Lug wrench', 'C-clamp', 'New brake pads'],
          views: '156K',
          rating: '4.7',
          url: 'https://www.brembo.com/repair-guides/brake-pad-replacement',
          licensed: true,
          source: 'Brembo Official'
        },
        {
          title: 'Spark Plug Replacement',
          company: 'NGK Spark Plugs',
          description: 'How to replace spark plugs safely and correctly',
          difficulty: 'Easy',
          duration: '8:15',
          estimated_time: '20-30 minutes',
          tools_required: ['Spark plug socket', 'Ratchet', 'New spark plugs'],
          views: '98K',
          rating: '4.9',
          url: 'https://www.ngk.com/repair-guides/spark-plug-replacement',
          licensed: true,
          source: 'NGK Official'
        },
        {
          title: 'Battery Replacement',
          company: 'Varta Automotive',
          description: 'Safe battery replacement procedure',
          difficulty: 'Easy',
          duration: '6:45',
          estimated_time: '15-25 minutes',
          tools_required: ['Wrench set', 'New battery', 'Battery terminal cleaner'],
          views: '112K',
          rating: '4.8',
          url: 'https://www.varta-automotive.com/repair-guides/battery-replacement',
          licensed: true,
          source: 'Varta Official'
        },
        {
          title: 'Cooling System Flush',
          company: 'Prestone',
          description: 'Complete cooling system maintenance guide',
          difficulty: 'Hard',
          duration: '25:30',
          estimated_time: '60-90 minutes',
          tools_required: ['Drain pan', 'Funnel', 'Coolant', 'Distilled water'],
          views: '67K',
          rating: '4.6',
          url: 'https://www.prestone.com/repair-guides/cooling-system-flush',
          licensed: true,
          source: 'Prestone Professional'
        }
      ]
    }

    const getSeverityBadgeClass = (severity) => {
      const classes = {
        'low': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'medium': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'high': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'critical': 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200'
      }
      return classes[severity] || classes.medium
    }

    const getDifficultyBadgeClass = (difficulty) => {
      const classes = {
        'Easy': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'Medium': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'Hard': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'Expert': 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'
      }
      return classes[difficulty] || classes.Medium
    }

    const getUrgencyBadgeClass = (urgency) => {
      const classes = {
        'immediate': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'urgent': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'high': 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
        'medium': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'low': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'routine': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
      }
      return classes[urgency?.toLowerCase()] || classes.medium
    }

    return {
      currentStep,
      steps,
      diagnosisForm,
      commonSymptoms,
      isLoading,
      diagnosisResult,
      fileInput,
      photoInput,
      videoInput,
      audioInput,
      uploadedFiles,
      selectedCar,
      carBrands,
      carModels,
      activeMediaType,
      activeInputType,
      isRecording,
      isProcessingAudio,
      recordedAudio,
      transcribedText,
      recordingTime,
      microphonePermissionGranted,
      formErrors,
      triggerFileUpload,
      handleFileSelect,
      handleFileDrop,
      removeFile,
      toggleRecording,
      stopRecording,
      pauseRecording,
      resumeRecording,
      clearRecording,
      transcribeAudio,
      useTranscribedText,
      formatTime,
      isPaused,
      audioBlob,
      audioUrl,
      checkMicrophonePermission,
      requestMicrophonePermission,
      refreshMicrophonePermission,
      validateForm,
      submitDiagnosis,
      getSeverityColor,
      getSeverityTextColor,
      getSeverityText,
      refreshResults,
      forceRefreshResults,
      quickLogin,
      onCarSelectionChange,
      debugInfo,
      shareResults,
      downloadReport,
      getRecommendedParts,
      getRepairVideos,
      getSeverityBadgeClass,
      getDifficultyBadgeClass,
      getUrgencyBadgeClass,
      saveDiagnosisToHistory,
      exportToPDF,
      viewHistory,
      searchPartInStore,
      openLicensedVideo,
      saveFormData,
      loadFormData,
      clearFormData
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.float {
  animation: float 6s ease-in-out infinite;
}

/* Glass morphism effect */
.glass {
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  background-color: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(209, 213, 219, 0.3);
}

.dark .glass {
  background-color: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(71, 85, 105, 0.3);
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Hover lift effect */
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark ::-webkit-scrollbar-track {
  background: #1e293b;
}

.dark ::-webkit-scrollbar-thumb {
  background: #475569;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}
</style>