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
          AI-Powered Car Diagnosis
        </h1>
        <p class="text-xl text-secondary-600 dark:text-secondary-400 max-w-3xl mx-auto">
          Get instant, accurate diagnosis for your vehicle problems using advanced AI technology. 
          Upload symptoms, photos, or describe the issue for comprehensive analysis.
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
              Step {{ currentStep + 1 }} of {{ steps.length }}
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
        <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8">
          <h2 class="text-2xl font-semibold text-secondary-900 dark:text-white mb-6">
            Vehicle Information & Symptoms
          </h2>
          
          <form @submit.prevent="submitDiagnosis" class="space-y-6">
            <!-- Vehicle Information -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-secondary-900 dark:text-white">Vehicle Details</h3>
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
                    Make *
                  </label>
                  <select v-model="diagnosisForm.make" class="input" :disabled="selectedCar" required>
                    <option value="">Select Make</option>
                    <option value="Toyota">Toyota</option>
                    <option value="Honda">Honda</option>
                    <option value="Ford">Ford</option>
                    <option value="BMW">BMW</option>
                    <option value="Mercedes">Mercedes</option>
                    <option value="Audi">Audi</option>
                    <option value="Volkswagen">Volkswagen</option>
                    <option value="Nissan">Nissan</option>
                    <option value="Hyundai">Hyundai</option>
                    <option value="Kia">Kia</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Model *
                  </label>
                  <input
                    v-model="diagnosisForm.model"
                    type="text"
                    placeholder="e.g., Camry, Civic, F-150"
                    class="input"
                    :disabled="selectedCar"
                    required
                  >
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
                Problem Description *
              </label>
              <textarea
                v-model="diagnosisForm.description"
                rows="4"
                placeholder="Describe the symptoms, sounds, or issues you're experiencing in detail..."
                class="input"
                required
              ></textarea>
            </div>

            <!-- Symptoms Checklist -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-3">
                Common Symptoms (Select all that apply)
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

            <!-- Photo Upload -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                Upload Photos (Optional)
              </label>
              <div 
                @click="triggerFileUpload"
                @dragover.prevent
                @drop.prevent="handleFileDrop"
                class="border-2 border-dashed border-secondary-300 dark:border-secondary-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-400 transition-colors duration-200"
              >
                <svg class="mx-auto h-12 w-12 text-secondary-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-secondary-600 dark:text-secondary-400">
                  Drag and drop photos here, or click to select
                </p>
                <p class="text-xs text-secondary-500 dark:text-secondary-500 mt-1">
                  Max 5 photos, 10MB each
                </p>
              </div>
              <input
                ref="fileInput"
                type="file"
                multiple
                accept="image/*"
                @change="handleFileSelect"
                class="hidden"
              >
              <div v-if="uploadedFiles.length > 0" class="mt-4">
                <div class="flex flex-wrap gap-2">
                  <div v-for="(file, index) in uploadedFiles" :key="index" class="relative">
                    <img :src="file.preview" class="w-20 h-20 object-cover rounded-lg">
                    <button
                      @click="removeFile(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <button 
              type="submit" 
              :disabled="isLoading"
              class="btn-primary w-full flex items-center justify-center"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              {{ isLoading ? 'Analyzing...' : 'Start AI Diagnosis' }}
            </button>
          </form>
        </div>

        <!-- Results Panel -->
        <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8">
          <h2 class="text-2xl font-semibold text-secondary-900 dark:text-white mb-6">
            Diagnosis Results
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
            <p class="text-secondary-600 dark:text-secondary-400">
              Our AI is analyzing your vehicle information and symptoms...
            </p>
          </div>

          <!-- Results State -->
          <div v-else-if="diagnosisResult" class="space-y-6">
            <!-- Diagnosis Summary -->
            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">Diagnosis Summary</h3>
              <div class="flex items-center mb-4">
                <div class="w-3 h-3 rounded-full mr-3" :class="getSeverityColor(diagnosisResult.severity)"></div>
                <span class="font-medium" :class="getSeverityTextColor(diagnosisResult.severity)">
                  {{ diagnosisResult.severity }} Priority
                </span>
              </div>
              <p class="text-secondary-700 dark:text-secondary-300">{{ diagnosisResult.summary }}</p>
            </div>

            <!-- Likely Causes -->
            <div>
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">Likely Causes</h3>
              <div class="space-y-3">
                <div v-for="(cause, index) in diagnosisResult.likelyCauses" :key="index" class="flex items-start">
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
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">Recommended Actions</h3>
              <div class="space-y-3">
                <div v-for="(action, index) in diagnosisResult.recommendedActions" :key="index" class="flex items-start">
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
            <div v-if="diagnosisResult.estimatedCosts" class="bg-warning-50 dark:bg-warning-900/20 rounded-lg p-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-3">Estimated Costs</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="cost in diagnosisResult.estimatedCosts" :key="cost.service" class="flex justify-between items-center">
                  <span class="text-secondary-700 dark:text-secondary-300">{{ cost.service }}</span>
                  <span class="font-medium text-secondary-900 dark:text-white">{{ cost.range }}</span>
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
            <p class="text-secondary-600 dark:text-secondary-400">
              Fill out the form and submit to get AI-powered diagnosis results.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { diagnosisAPI, carsAPI } from '../services/api'

export default {
  name: 'Diagnose',
  setup() {
    const route = useRoute()
    const currentStep = ref(0)
    const isLoading = ref(false)
    const diagnosisResult = ref(null)
    const fileInput = ref(null)
    const uploadedFiles = ref([])
    const selectedCar = ref(null)

    const steps = ['Vehicle Info', 'Symptoms', 'AI Analysis', 'Results']

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
      'Air conditioning not working',
      'Suspension problems'
    ]

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
          
          console.log('Car data loaded:', selectedCar.value)
        }
      } catch (error) {
        console.error('Error loading car data:', error)
        alert('Error loading car data. Please try again.')
      } finally {
        isLoading.value = false
      }
    }

    const triggerFileUpload = () => {
      fileInput.value.click()
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
        if (file.type.startsWith('image/') && uploadedFiles.value.length < 5) {
          const reader = new FileReader()
          reader.onload = (e) => {
            uploadedFiles.value.push({
              file: file,
              preview: e.target.result
            })
          }
          reader.readAsDataURL(file)
        }
      })
    }

    const removeFile = (index) => {
      uploadedFiles.value.splice(index, 1)
    }

    const submitDiagnosis = async () => {
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
        formData.append('symptoms', JSON.stringify(diagnosisForm.symptoms))

        // Add uploaded files
        uploadedFiles.value.forEach((fileObj, index) => {
          formData.append(`photos[${index}]`, fileObj.file)
        })

        const response = await diagnosisAPI.submitDiagnosis(formData)
        diagnosisResult.value = response.data
        currentStep.value = 3
      } catch (error) {
        console.error('Diagnosis error:', error)
        // For demo purposes, show mock result
        diagnosisResult.value = {
          severity: 'Medium',
          summary: 'Based on your symptoms, the issue appears to be related to the engine management system. The most likely cause is a faulty oxygen sensor or fuel injection problem.',
          likelyCauses: [
            {
              title: 'Faulty Oxygen Sensor',
              description: 'The oxygen sensor monitors the air-fuel ratio and can cause poor performance when malfunctioning.',
              probability: 75
            },
            {
              title: 'Fuel Injection Issues',
              description: 'Clogged or malfunctioning fuel injectors can cause rough idle and poor acceleration.',
              probability: 60
            },
            {
              title: 'Mass Air Flow Sensor',
              description: 'A dirty or faulty MAF sensor can cause engine performance issues.',
              probability: 45
            }
          ],
          recommendedActions: [
            {
              title: 'Check Engine Codes',
              description: 'Use an OBD-II scanner to read diagnostic trouble codes.',
              urgency: 'Immediate'
            },
            {
              title: 'Inspect Oxygen Sensor',
              description: 'Have a mechanic inspect the oxygen sensor for proper operation.',
              urgency: 'Within 1 week'
            },
            {
              title: 'Fuel System Cleaning',
              description: 'Consider a fuel system cleaning service to address potential injector issues.',
              urgency: 'Within 2 weeks'
            }
          ],
          estimatedCosts: [
            { service: 'Oxygen Sensor Replacement', range: '$150 - $300' },
            { service: 'Fuel System Cleaning', range: '$100 - $200' },
            { service: 'MAF Sensor Replacement', range: '$200 - $400' }
          ]
        }
        currentStep.value = 3
      } finally {
        isLoading.value = false
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

    // Load car data if car ID is provided in URL
    onMounted(() => {
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
      uploadedFiles,
      selectedCar,
      triggerFileUpload,
      handleFileSelect,
      handleFileDrop,
      removeFile,
      submitDiagnosis,
      getSeverityColor,
      getSeverityTextColor
    }
  }
}
</script>