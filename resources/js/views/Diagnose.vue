<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Car Diagnosis
        </h1>
        <p class="text-xl text-gray-600">
          Upload photos, videos, or audio to get an AI-powered diagnosis
        </p>
      </div>

      <!-- Upload Form -->
      <div class="card mb-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Upload Media</h2>
        
        <div class="space-y-6">
          <!-- File Upload Area -->
          <div 
            @drop="handleDrop"
            @dragover.prevent
            @dragenter.prevent
            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-500 transition-colors"
            :class="{ 'border-primary-500 bg-primary-50': isDragOver }"
          >
            <div class="space-y-4">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div>
                <label for="file-upload" class="cursor-pointer">
                  <span class="mt-2 block text-sm font-medium text-gray-900">
                    Drop files here or click to upload
                  </span>
                  <span class="mt-1 block text-sm text-gray-500">
                    PNG, JPG, MP4, MP3 up to 10MB
                  </span>
                </label>
                <input 
                  id="file-upload" 
                  name="file-upload" 
                  type="file" 
                  class="sr-only" 
                  multiple
                  accept="image/*,video/*,audio/*"
                  @change="handleFileSelect"
                />
              </div>
            </div>
          </div>

          <!-- Selected Files -->
          <div v-if="selectedFiles.length > 0" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Selected Files:</h3>
            <div class="space-y-2">
              <div 
                v-for="(file, index) in selectedFiles" 
                :key="index"
                class="flex items-center justify-between p-3 bg-gray-100 rounded-lg"
              >
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <svg v-if="file.type.startsWith('image/')" class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <svg v-else-if="file.type.startsWith('video/')" class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <svg v-else class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ file.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatFileSize(file.size) }}</p>
                  </div>
                </div>
                <button 
                  @click="removeFile(index)"
                  class="text-red-500 hover:text-red-700"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Car Selection -->
          <div class="space-y-4">
            <label for="car-select" class="block text-sm font-medium text-gray-700">
              Select Car (Optional)
            </label>
            <select 
              id="car-select"
              v-model="selectedCar"
              class="input-field"
            >
              <option value="">Choose a car...</option>
              <option v-for="car in userCars" :key="car.id" :value="car.id">
                {{ car.brand }} {{ car.model }} ({{ car.year }})
              </option>
            </select>
          </div>

          <!-- Description -->
          <div class="space-y-4">
            <label for="description" class="block text-sm font-medium text-gray-700">
              Describe the Problem (Optional)
            </label>
            <textarea 
              id="description"
              v-model="description"
              rows="4"
              class="input-field"
              placeholder="Describe what you're experiencing with your car..."
            ></textarea>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button 
              @click="submitDiagnosis"
              :disabled="selectedFiles.length === 0 || isSubmitting"
              class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isSubmitting" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Analyzing...
              </span>
              <span v-else>Analyze with AI</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Results Section -->
      <div v-if="diagnosisResult" class="card">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Diagnosis Results</h2>
        
        <div class="space-y-6">
          <!-- Problem Summary -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Potential Problem</h3>
            <p class="text-yellow-700">{{ diagnosisResult.problem }}</p>
          </div>

          <!-- Confidence Level -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-blue-800 mb-2">Confidence Level</h3>
            <div class="flex items-center space-x-2">
              <div class="flex-1 bg-blue-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: diagnosisResult.confidence + '%' }"
                ></div>
              </div>
              <span class="text-blue-700 font-medium">{{ diagnosisResult.confidence }}%</span>
            </div>
          </div>

          <!-- Recommended Solutions -->
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-green-800 mb-2">Recommended Solutions</h3>
            <ul class="space-y-2">
              <li 
                v-for="(solution, index) in diagnosisResult.solutions" 
                :key="index"
                class="text-green-700 flex items-start space-x-2"
              >
                <span class="text-green-500 mt-1">•</span>
                <span>{{ solution }}</span>
              </li>
            </ul>
          </div>

          <!-- Next Steps -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-800 mb-2">Next Steps</h3>
            <p class="text-gray-700 mb-4">{{ diagnosisResult.nextSteps }}</p>
            <div class="flex space-x-4">
              <button class="btn-primary">
                Find a Mechanic
              </button>
              <button class="btn-secondary">
                Save Diagnosis
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { carsAPI, diagnosisAPI } from '../services/api'

export default {
  name: 'Diagnose',
  setup() {
    const selectedFiles = ref([])
    const selectedCar = ref('')
    const description = ref('')
    const isSubmitting = ref(false)
    const isDragOver = ref(false)
    const diagnosisResult = ref(null)
    const userCars = ref([])

    const handleDrop = (e) => {
      e.preventDefault()
      isDragOver.value = false
      const files = Array.from(e.dataTransfer.files)
      addFiles(files)
    }

    const handleFileSelect = (e) => {
      const files = Array.from(e.target.files)
      addFiles(files)
    }

    const addFiles = (files) => {
      files.forEach(file => {
        if (file.size <= 10 * 1024 * 1024) { // 10MB limit
          selectedFiles.value.push(file)
        } else {
          alert(`File ${file.name} is too large. Maximum size is 10MB.`)
        }
      })
    }

    const removeFile = (index) => {
      selectedFiles.value.splice(index, 1)
    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    const submitDiagnosis = async () => {
      if (selectedFiles.value.length === 0) return

      isSubmitting.value = true

      try {
        const file = selectedFiles.value[0] // Take first file for now
        
        const response = await diagnosisAPI.create({
          car_id: selectedCar.value,
          description: description.value,
          media_file: file
        })

        if (response.data.success) {
          diagnosisResult.value = {
            problem: response.data.diagnosis.problem,
            confidence: response.data.diagnosis.confidence,
            solutions: response.data.diagnosis.solutions,
            nextSteps: response.data.diagnosis.next_steps
          }
        }
      } catch (error) {
        console.error('Diagnosis failed:', error)
        alert('Diagnosis failed. Please try again.')
      } finally {
        isSubmitting.value = false
      }
    }

    const loadUserCars = async () => {
      try {
        const response = await carsAPI.getAll()
        if (response.data.success) {
          userCars.value = response.data.cars
        }
      } catch (error) {
        console.error('Error loading cars:', error)
      }
    }

    onMounted(() => {
      loadUserCars()
    })

    return {
      selectedFiles,
      selectedCar,
      description,
      isSubmitting,
      isDragOver,
      diagnosisResult,
      userCars,
      handleDrop,
      handleFileSelect,
      removeFile,
      formatFileSize,
      submitDiagnosis
    }
  }
}
</script>
