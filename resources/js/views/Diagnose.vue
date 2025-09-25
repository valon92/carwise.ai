<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full mb-6">
          <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
          </svg>
        </div>
        <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">
          {{ t('ai_powered_car_diagnosis') }}
        </h1>
        <p class="text-xl text-secondary-600 dark:text-secondary-400 max-w-3xl mx-auto">
          {{ t('get_instant_accurate_diagnosis') }}
        </p>
      </div>

      <!-- Progress Steps -->
      <div class="mb-8">
        <!-- Desktop Progress Steps -->
        <div class="hidden md:flex items-center justify-center space-x-4">
          <div v-for="(step, index) in steps" :key="index" class="flex items-center">
            <div 
              class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300"
              :class="currentStep >= index ? 'bg-primary-600 text-white' : 'bg-secondary-200 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'"
            >
              {{ index + 1 }}
            </div>
            <span 
              class="ml-2 text-sm font-medium transition-colors duration-300"
              :class="currentStep >= index ? 'text-primary-600 dark:text-primary-400' : 'text-secondary-600 dark:text-secondary-400'"
            >
              {{ step }}
            </span>
            <svg v-if="index < steps.length - 1" class="w-6 h-6 text-secondary-400 mx-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>

        <!-- Mobile Progress Steps -->
        <div class="md:hidden">
          <!-- Current Step Display -->
          <div class="text-center mb-4">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-2"
                 :class="currentStep >= 0 ? 'bg-primary-600 text-white' : 'bg-secondary-200 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'">
              {{ currentStep + 1 }}
            </div>
            <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">
              {{ steps[currentStep] }}
            </h3>
            <p class="text-sm text-secondary-600 dark:text-secondary-400">
              {{ t('step') }} {{ currentStep + 1 }} {{ t('of') }} {{ steps.length }}
            </p>
          </div>

          <!-- Progress Bar -->
          <div class="w-full bg-secondary-200 dark:bg-secondary-700 rounded-full h-2 mb-4">
            <div 
              class="bg-primary-600 h-2 rounded-full transition-all duration-500 ease-out"
              :style="{ width: ((currentStep + 1) / steps.length) * 100 + '%' }"
            ></div>
          </div>

          <!-- Step Indicators -->
          <div class="flex justify-center space-x-2">
            <div 
              v-for="(step, index) in steps" 
              :key="index"
              class="w-2 h-2 rounded-full transition-all duration-300"
              :class="currentStep >= index ? 'bg-primary-600' : 'bg-secondary-300 dark:bg-secondary-600'"
            ></div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
        <!-- Diagnosis Form -->
        <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 order-2 xl:order-1">
          <h2 class="text-2xl font-semibold text-secondary-900 dark:text-white mb-6">
            {{ t('vehicle_information_symptoms') }}
          </h2>
          
          <div class="space-y-6">
            <!-- Vehicle Information -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-secondary-900 dark:text-white">{{ t('vehicle_details') }}</h3>
                <div v-if="selectedCar" class="flex items-center text-sm text-green-600 dark:text-green-400">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  {{ t('pre_filled_from_your_car') }}
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    {{ t('make') }} *
                  </label>
                  <select v-model="diagnosisForm.make" class="input" :disabled="selectedCar" required @change="onMakeChange">
                    <option value="">{{ t('select_make') }}</option>
                    <option v-for="brand in carBrands" :key="brand.id" :value="brand.name">
                      {{ brand.name }} ({{ brand.country }})
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    {{ t('model') }} *
                  </label>
                  <select 
                    v-model="diagnosisForm.model" 
                    class="input" 
                    :disabled="selectedCar || !diagnosisForm.make" 
                    required
                  >
                    <option value="">{{ t('select_model') }}</option>
                    <option v-for="model in carModels" :key="model.id" :value="model.name">
                      {{ model.name }} {{ model.generation ? `(${model.generation})` : '' }}
                    </option>
                  </select>
                  <div v-if="!diagnosisForm.make" class="text-sm text-secondary-500 dark:text-secondary-400 mt-1">
                    {{ t('please_select_make_first') }}
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Year *
                  </label>
                  <input
                    v-model="diagnosisForm.year"
                    type="number"
                    placeholder="e.g., 2020"
                    min="1990"
                    :max="new Date().getFullYear() + 1"
                    class="input"
                    :disabled="selectedCar"
                    required
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Mileage
                  </label>
                  <input
                    v-model="diagnosisForm.mileage"
                    type="number"
                    placeholder="e.g., 50000"
                    class="input"
                    :disabled="selectedCar"
                  >
                </div>
              </div>
            </div>

            <!-- Engine Information -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-secondary-900 dark:text-white">Engine Details</h3>
                <div v-if="selectedCar" class="flex items-center text-sm text-green-600 dark:text-green-400">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Pre-filled from your car
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Engine Type
                  </label>
                  <select v-model="diagnosisForm.engineType" class="input" :disabled="selectedCar">
                    <option value="">Select Engine Type</option>
                    <option value="Gasoline">Gasoline</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Electric">Electric</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Engine Size
                  </label>
                  <input
                    v-model="diagnosisForm.engineSize"
                    type="text"
                    placeholder="e.g., 2.0L, 3.5L"
                    class="input"
                    :disabled="selectedCar"
                  >
                </div>
              </div>
            </div>

            <!-- Problem Description -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                {{ t('problem_description') }} *
              </label>
              
              <!-- Description Input Tabs -->
              <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-1 mb-3 bg-secondary-100 dark:bg-secondary-700 rounded-lg p-1">
                <button
                  type="button"
                  @click="activeInputType = 'text'"
                  class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-all duration-200"
                  :class="activeInputType === 'text' 
                    ? 'bg-white dark:bg-secondary-800 text-primary-600 dark:text-primary-400 shadow-sm' 
                    : 'text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-200'"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Text
                </button>
                <button
                  type="button"
                  @click="activeInputType = 'voice'"
                  class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-all duration-200"
                  :class="activeInputType === 'voice' 
                    ? 'bg-white dark:bg-secondary-800 text-primary-600 dark:text-primary-400 shadow-sm' 
                    : 'text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-200'"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                  </svg>
                  Voice
                </button>
              </div>

              <!-- Text Input -->
              <div v-if="activeInputType === 'text'">
                <textarea
                  v-model="diagnosisForm.description"
                  rows="4"
                  :placeholder="t('describe_symptoms_placeholder')"
                  class="input"
                  required
                ></textarea>
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
                  <button
                    type="button"
                    @click="toggleRecording"
                    :disabled="isProcessingAudio"
                    class="flex items-center justify-center w-16 h-16 rounded-full transition-all duration-200"
                    :class="isRecording 
                      ? 'bg-danger-500 hover:bg-danger-600 text-white animate-pulse' 
                      : 'bg-primary-500 hover:bg-primary-600 text-white'"
                  >
                    <svg v-if="!isRecording" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <rect x="6" y="6" width="12" height="12" rx="2"></rect>
                    </svg>
                  </button>
                  
                  <div class="text-center">
                    <p class="text-sm font-medium text-secondary-700 dark:text-secondary-300">
                      {{ isRecording ? 'Recording...' : 'Click to record' }}
                    </p>
                    <p v-if="recordingTime > 0" class="text-xs text-secondary-500 dark:text-secondary-500">
                      {{ formatTime(recordingTime) }}
                    </p>
                    <p v-if="!isRecording && !recordedAudio" class="text-xs text-secondary-400 dark:text-secondary-500">
                      Speak clearly into your microphone
                    </p>
                  </div>
                </div>

                <!-- Audio Player (if recorded) -->
                <div v-if="recordedAudio" class="p-4 bg-secondary-50 dark:bg-secondary-700 rounded-lg">
                  <div class="flex items-center space-x-4">
                    <audio :src="recordedAudio" controls class="flex-1"></audio>
                    <button
                      type="button"
                      @click="transcribeAudio"
                      :disabled="isProcessingAudio"
                      class="btn-primary text-sm"
                    >
                      {{ isProcessingAudio ? 'Transcribing...' : 'Convert to Text' }}
                    </button>
                    <button
                      type="button"
                      @click="clearRecording"
                      class="btn-secondary text-sm"
                    >
                      Clear
                    </button>
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

            <!-- Symptoms Checklist -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-3">
                {{ t('common_symptoms_select_all') }}
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label v-for="symptom in commonSymptoms" :key="symptom" class="flex items-center">
                  <input
                    v-model="diagnosisForm.symptoms"
                    type="checkbox"
                    :value="symptom"
                    class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                  >
                  <span class="ml-2 text-sm text-secondary-700 dark:text-secondary-300">{{ symptom }}</span>
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
                {{ t('upload_media_optional') }}
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
                  {{ t('drag_drop_photos_here') }}
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  {{ t('max_5_photos_10mb_each') }}
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
                  {{ t('drag_drop_videos_here') }}
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  {{ t('max_2_videos_50mb_each') }}
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
                  {{ t('drag_drop_audio_here') }}
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  {{ t('max_3_audio_20mb_each') }}
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

            
            <button 
              type="button" 
              :disabled="isLoading"
              @click="submitDiagnosis"
              class="btn-primary w-full flex items-center justify-center py-4 text-lg font-semibold"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              {{ isLoading ? t('analyzing') : t('start_ai_diagnosis') }}
            </button>
          </div>
        </div>

        <!-- Results Panel -->
        <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 order-1 xl:order-2">
          <h2 class="text-2xl font-semibold text-secondary-900 dark:text-white mb-6">
            {{ t('diagnosis_results') }}
          </h2>
          
          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full mb-4">
              <svg class="animate-spin w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-2">AI Analysis in Progress</h3>
            <p class="text-secondary-600 dark:text-secondary-400 mb-4">
              Our AI is analyzing your vehicle information and symptoms...
            </p>
            <div class="flex justify-center space-x-2">
              <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
          </div>

          <!-- Results State -->
          <div v-else-if="diagnosisResult" class="space-y-6">
            <!-- Debug/Refresh Button -->
            <div class="flex justify-between items-center mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <div class="text-sm text-yellow-800">
                <strong>Debug Info:</strong> 
                AI Provider: {{ diagnosisResult?.ai_provider || 'Unknown' }} | 
                Cache Status: {{ debugInfo.cacheStatus }} |
                Auth: {{ debugInfo.authStatus }}
              </div>
              <div class="flex space-x-2">
                <button 
                  @click="quickLogin"
                  class="btn-secondary text-sm bg-green-100 hover:bg-green-200 text-green-700 border-green-300"
                  title="Quick login for testing"
                >
                  🔑 Quick Login
                </button>
                <button 
                  @click="forceRefreshResults"
                  class="btn-secondary text-sm bg-red-100 hover:bg-red-200 text-red-700 border-red-300"
                  title="Force refresh to get latest results from Gemini API"
                >
                  🚨 Force Refresh (Clear Cache)
                </button>
              </div>
            </div>
            <!-- Diagnosis Summary -->
            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">{{ t('diagnosis_summary') }}</h3>
                <div class="flex items-center space-x-4">
                  <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" :class="getSeverityColor(diagnosisResult.severity)"></div>
                    <span class="font-medium text-sm" :class="getSeverityTextColor(diagnosisResult.severity)">
                      {{ getSeverityText(diagnosisResult.severity) }}
                    </span>
                  </div>
                  <div class="text-sm text-secondary-600 dark:text-secondary-400">
                    {{ diagnosisResult.confidence_score || 75 }}% Confidence
                  </div>
                </div>
              </div>
              <h4 class="text-xl font-semibold text-secondary-900 dark:text-white mb-2">
                {{ diagnosisResult.problem_title || 'Vehicle Issue Detected' }}
              </h4>
              <p class="text-secondary-700 dark:text-secondary-300">
                {{ diagnosisResult.problem_description || diagnosisResult.summary }}
              </p>
              
              <!-- AI Insights -->
              <div v-if="diagnosisResult.ai_insights" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-400">
                <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                  AI Insights
                </h5>
                <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                  <li v-for="(insight, index) in diagnosisResult.ai_insights" :key="index" class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                    {{ insight }}
                  </li>
                </ul>
              </div>
            </div>

            <!-- Likely Causes -->
            <div>
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">{{ t('likely_causes') }}</h3>
              <div class="space-y-3">
                <div v-for="(cause, index) in (diagnosisResult.likely_causes || diagnosisResult.likelyCauses)" :key="index" class="flex items-start">
                  <div class="w-6 h-6 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">{{ index + 1 }}</span>
                  </div>
                  <div>
                    <h4 class="font-medium text-secondary-900 dark:text-white">{{ cause.title }}</h4>
                    <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ cause.description }}</p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 rounded">
                      {{ cause.probability }}% probability
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recommended Actions -->
            <div>
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">{{ t('recommended_actions') }}</h3>
              <div class="space-y-3">
                <div v-for="(action, index) in (diagnosisResult.recommended_actions || diagnosisResult.recommendedActions)" :key="index" class="flex items-start">
                  <div class="w-6 h-6 bg-success-100 dark:bg-success-900 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-3 h-3 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-secondary-900 dark:text-white">{{ action.title }}</h4>
                    <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ action.description }}</p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs font-medium bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-400 rounded">
                      {{ action.urgency }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Estimated Costs -->
            <div v-if="diagnosisResult.estimated_costs" class="bg-warning-50 dark:bg-warning-900/20 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Estimated Costs
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="cost in diagnosisResult.estimated_costs" :key="cost.service" class="flex justify-between items-center p-3 bg-white dark:bg-secondary-800 rounded-lg border border-warning-200 dark:border-warning-700">
                  <span class="text-secondary-700 dark:text-secondary-300 font-medium">{{ cost.service }}</span>
                  <span class="font-semibold text-warning-700 dark:text-warning-300">
                    €{{ cost.min }} - €{{ cost.max }}
                  </span>
                </div>
              </div>
              <p class="text-xs text-warning-600 dark:text-warning-400 mt-3">
                * Cost estimates are approximate and may vary based on location and specific vehicle requirements.
              </p>
            </div>

            <!-- Urgency Alert -->
            <div v-if="diagnosisResult.requires_immediate_attention" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-6">
              <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                  <h4 class="font-semibold text-red-900 dark:text-red-100">Immediate Attention Required</h4>
                  <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                    This issue requires immediate professional attention to prevent further damage or safety risks.
                  </p>
                </div>
              </div>
            </div>

            <!-- Find Mechanics Button -->
            <div class="pt-4 border-t border-secondary-200 dark:border-secondary-700">
              <router-link to="/mechanics" class="btn-primary w-full flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Find Certified Mechanics
              </router-link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-secondary-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-2">Ready for Diagnosis</h3>
            <p class="text-secondary-600 dark:text-secondary-400 mb-6">
              Fill out the form and submit to get AI-powered diagnosis results.
            </p>
            
            <!-- Quick Tips -->
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 text-left max-w-md mx-auto">
              <h4 class="text-sm font-semibold text-primary-800 dark:text-primary-200 mb-3">💡 Quick Tips:</h4>
              <ul class="text-sm text-primary-700 dark:text-primary-300 space-y-2">
                <li class="flex items-start">
                  <span class="mr-2">📝</span>
                  <span>Describe symptoms in detail for better accuracy</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">📸</span>
                  <span>Upload photos or videos of the problem area</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">🎤</span>
                  <span>Use voice recording for hands-free input</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">✅</span>
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
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { diagnosisAPI, carsAPI } from '../services/api'
import axios from 'axios'
import { t } from '../utils/translations'

export default {
  name: 'Diagnose',
  setup() {
    const route = useRoute()
    const router = useRouter()
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
    const isProcessingAudio = ref(false)
    const recordedAudio = ref(null)
    const transcribedText = ref('')
    const recordingTime = ref(0)
    const mediaRecorder = ref(null)
    const audioChunks = ref([])
    const recordingInterval = ref(null)
    const microphonePermissionGranted = ref(false)
    const formErrors = ref([])

    const steps = [t('vehicle_info'), t('symptoms'), t('ai_analysis'), t('results')]

    const diagnosisForm = reactive({
      make: '',
      model: '',
      year: '',
      mileage: '',
      engineType: '',
      engineSize: '',
      description: '',
      symptoms: []
    })

    const commonSymptoms = [
      t('engine_wont_start'),
      t('rough_idle'),
      t('stalling'),
      t('poor_acceleration'),
      t('strange_noises'),
      t('warning_lights_on'),
      t('smoke_from_exhaust'),
      t('overheating'),
      t('poor_fuel_economy'),
      t('transmission_issues'),
      t('brake_problems'),
      t('steering_issues'),
      t('electrical_problems'),
      t('ac_not_working'),
      t('suspension_problems')
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

    const onMakeChange = () => {
      // Reset model when make changes
      diagnosisForm.model = ''
      // Load models for selected make
      loadCarModels(diagnosisForm.make)
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
          
          console.log('Car data loaded:', selectedCar.value)
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
      if (isRecording.value) {
        stopRecording()
      } else {
        startRecording()
      }
    }

    const startRecording = async () => {
      try {
        // Check if getUserMedia is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          throw new Error('getUserMedia is not supported in this browser')
        }

        // Check microphone permissions first
        if (navigator.permissions) {
          const permissionStatus = await navigator.permissions.query({ name: 'microphone' })
          
          if (permissionStatus.state === 'denied') {
            throw new Error('Microphone permission denied. Please enable microphone access in your browser settings.')
          }
        }

        // Request microphone access with better error handling
        const stream = await navigator.mediaDevices.getUserMedia({ 
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
          } 
        })
        
        mediaRecorder.value = new MediaRecorder(stream)
        audioChunks.value = []
        
        mediaRecorder.value.ondataavailable = (event) => {
          audioChunks.value.push(event.data)
        }
        
        mediaRecorder.value.onstop = () => {
          const audioBlob = new Blob(audioChunks.value, { type: 'audio/wav' })
          recordedAudio.value = URL.createObjectURL(audioBlob)
          stream.getTracks().forEach(track => track.stop())
        }
        
        mediaRecorder.value.start()
        isRecording.value = true
        recordingTime.value = 0
        microphonePermissionGranted.value = true
        
        // Start timer
        recordingInterval.value = setInterval(() => {
          recordingTime.value++
        }, 1000)
        
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
        } else if (error.message.includes('permission denied')) {
          errorMessage += 'Microphone permission denied. Please enable microphone access in your browser settings.'
        } else {
          errorMessage += 'Please check your microphone permissions and try again.'
        }
        
        // Show a more helpful error message
        alert(errorMessage)
        
        // Optionally, you could show a modal with instructions instead of alert
        // showMicrophonePermissionModal()
      }
    }

    const stopRecording = () => {
      if (mediaRecorder.value && isRecording.value) {
        mediaRecorder.value.stop()
        isRecording.value = false
        clearInterval(recordingInterval.value)
      }
    }

    const clearRecording = () => {
      recordedAudio.value = null
      transcribedText.value = ''
      recordingTime.value = 0
    }

    const transcribeAudio = async () => {
      if (!recordedAudio.value) return
      
      isProcessingAudio.value = true
      try {
        // Convert audio URL to blob
        const response = await fetch(recordedAudio.value)
        const audioBlob = await response.blob()
        
        // Create FormData for API call
        const formData = new FormData()
        formData.append('audio', audioBlob, 'recording.wav')
        
        // Call transcription API (you'll need to implement this endpoint)
        const transcriptionResponse = await axios.post('/api/transcribe-audio', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        if (transcriptionResponse.data.success) {
          transcribedText.value = transcriptionResponse.data.text
        } else {
          throw new Error(transcriptionResponse.data.message || 'Transcription failed')
        }
        
      } catch (error) {
        console.error('Transcription error:', error)
        // Fallback: show a placeholder text
        transcribedText.value = 'Transcription failed. Please try typing your description instead.'
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
        if (!navigator.permissions) {
          // Fallback for browsers that don't support permissions API
          microphonePermissionGranted.value = false
          return
        }

        const permissionStatus = await navigator.permissions.query({ name: 'microphone' })
        microphonePermissionGranted.value = permissionStatus.state === 'granted'
        
        // Listen for permission changes
        permissionStatus.onchange = () => {
          microphonePermissionGranted.value = permissionStatus.state === 'granted'
        }
      } catch (error) {
        console.log('Permission check not supported:', error)
        microphonePermissionGranted.value = false
      }
    }

    const validateForm = () => {
      formErrors.value = []
      
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
      
      if (!diagnosisForm.description.trim()) {
        formErrors.value.push('Please describe the problem or symptoms')
      }
      
      return formErrors.value.length === 0
    }

    const submitDiagnosis = async () => {
      console.log('=== SUBMIT DIAGNOSIS FUNCTION CALLED ===')
      console.log('Form data:', diagnosisForm)
      console.log('Uploaded files:', uploadedFiles.value)

      // Validate form before submission
      if (!validateForm()) {
        // Scroll to top to show validation errors
        window.scrollTo({ top: 0, behavior: 'smooth' })
        return
      }
      console.log('Current step before:', currentStep.value)
      
      // Prevent multiple submissions
      if (isLoading.value) {
        console.log('⚠️ Already loading, preventing duplicate submission')
        return
      }
      
      // SMART CACHE CLEARING - Keep authentication data
      console.log('🧹 CLEARING DIAGNOSIS CACHE (keeping auth)...')
      
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
      
      console.log('✅ DIAGNOSIS CACHE CLEARED (auth preserved)')
      
      // Check form validation
      if (!diagnosisForm.make || !diagnosisForm.model || !diagnosisForm.year || !diagnosisForm.description) {
        console.log('Form validation failed - missing required fields')
        console.log('make:', diagnosisForm.make)
        console.log('model:', diagnosisForm.model)
        console.log('year:', diagnosisForm.year)
        console.log('description:', diagnosisForm.description)
        alert('Ju lutem plotësoni të gjitha fushat e detyrueshme!')
        return
      }
      
      // Check if user is logged in
      const authToken = localStorage.getItem('token')
      const authUser = localStorage.getItem('user')
      console.log('🔐 AUTHENTICATION CHECK:')
      console.log('Token exists:', !!authToken)
      console.log('User exists:', !!authUser)
      console.log('Token value:', authToken ? authToken.substring(0, 20) + '...' : 'null')
      console.log('User value:', authUser ? JSON.parse(authUser).name : 'null')
      
      if (!authToken) {
        console.log('❌ No token found, redirecting to login')
        alert('Ju duhet të hyni në llogari për të dërguar diagnozën!')
        router.push('/login')
        return
      }
      
      console.log('✅ User is authenticated, proceeding with diagnosis...')

      console.log('Setting loading state...')
      isLoading.value = true
      currentStep.value = 2
      console.log('Loading state set - isLoading:', isLoading.value, 'currentStep:', currentStep.value)

      try {
        console.log('Creating FormData...')
        const formData = new FormData()
        formData.append('make', diagnosisForm.make)
        formData.append('model', diagnosisForm.model)
        formData.append('year', diagnosisForm.year)
        formData.append('mileage', diagnosisForm.mileage)
        formData.append('engine_type', diagnosisForm.engineType)
        formData.append('engine_size', diagnosisForm.engineSize)
        formData.append('description', diagnosisForm.description)
        
        // Add symptoms as individual form fields
        diagnosisForm.symptoms.forEach((symptom, index) => {
          formData.append(`symptoms[${index}]`, symptom)
        })

        console.log('FormData entries:')
        for (let [key, value] of formData.entries()) {
          console.log(key, value)
        }

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
          symptoms: diagnosisForm.symptoms || []
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
        'low': t('low_priority'),
        'medium': t('medium_priority'),
        'high': t('high_priority'),
        'critical': t('critical_priority')
      }
      return severityMap[severity?.toLowerCase()] || t('medium_priority')
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
      
      const carId = route.query.car
      if (carId) {
        console.log('Loading car data for ID:', carId)
        loadCarData(carId)
      }
    })

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
      clearRecording,
      transcribeAudio,
      useTranscribedText,
      formatTime,
      checkMicrophonePermission,
      validateForm,
      submitDiagnosis,
      getSeverityColor,
      getSeverityTextColor,
      getSeverityText,
      refreshResults,
      forceRefreshResults,
      quickLogin,
      onMakeChange,
      debugInfo,
      t
    }
  }
}
</script>