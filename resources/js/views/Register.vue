<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100">
          <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Or
          <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
            sign in to your existing account
          </router-link>
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input 
              id="name" 
              name="name" 
              type="text" 
              autocomplete="name" 
              required 
              v-model="form.name"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
              placeholder="Enter your full name"
            />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input 
              id="email" 
              name="email" 
              type="email" 
              autocomplete="email" 
              required 
              v-model="form.email"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
              placeholder="Enter your email"
            />
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input 
              id="phone" 
              name="phone" 
              type="tel" 
              autocomplete="tel" 
              v-model="form.phone"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
              placeholder="Enter your phone number"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input 
              id="password" 
              name="password" 
              type="password" 
              autocomplete="new-password" 
              required 
              v-model="form.password"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
              placeholder="Create a password"
            />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input 
              id="password_confirmation" 
              name="password_confirmation" 
              type="password" 
              autocomplete="new-password" 
              required 
              v-model="form.password_confirmation"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
              placeholder="Confirm your password"
            />
          </div>

          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Account Type</label>
            <select 
              id="role" 
              name="role" 
              required 
              v-model="form.role"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
              <option value="">Select account type</option>
              <option value="customer">Car Owner</option>
              <option value="mechanic">Certified Mechanic</option>
            </select>
          </div>

          <!-- Mechanic-specific fields -->
          <div v-if="form.role === 'mechanic'" class="space-y-4 border-t pt-4">
            <h3 class="text-lg font-medium text-gray-900">Mechanic Information</h3>
            
            <div>
              <label for="experience" class="block text-sm font-medium text-gray-700">Years of Experience</label>
              <input 
                id="experience" 
                name="experience" 
                type="number" 
                min="0"
                v-model="form.experience"
                class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
                placeholder="Years of experience"
              />
            </div>

            <div>
              <label for="expertise" class="block text-sm font-medium text-gray-700">Areas of Expertise</label>
              <div class="mt-2 grid grid-cols-2 gap-2">
                <label v-for="skill in availableSkills" :key="skill" class="flex items-center">
                  <input 
                    type="checkbox" 
                    :value="skill"
                    v-model="form.expertise"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ skill }}</span>
                </label>
              </div>
            </div>

            <div>
              <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
              <select 
                id="location" 
                name="location" 
                v-model="form.location"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              >
                <option value="">Select your location</option>
                <option value="prishtina">Prishtina</option>
                <option value="peja">Peja</option>
                <option value="prizren">Prizren</option>
                <option value="gjakova">Gjakova</option>
                <option value="ferizaj">Ferizaj</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="flex items-center">
            <input 
              id="terms" 
              name="terms" 
              type="checkbox" 
              required
              v-model="form.terms"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              I agree to the 
              <a href="#" class="text-primary-600 hover:text-primary-500">Terms of Service</a>
              and 
              <a href="#" class="text-primary-600 hover:text-primary-500">Privacy Policy</a>
            </label>
          </div>
        </div>

        <div>
          <button 
            type="submit" 
            :disabled="isLoading || !isFormValid"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? 'Creating account...' : 'Create account' }}
          </button>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                {{ errorMessage }}
              </h3>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                {{ successMessage }}
              </h3>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../services/api'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const errorMessage = ref('')
    const successMessage = ref('')
    const availableSkills = ref([
      'Engine', 'Transmission', 'Brakes', 'Electrical', 
      'Diagnostics', 'Suspension', 'Exhaust', 'AC/Heating'
    ])
    
    const form = ref({
      name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
      role: '',
      experience: '',
      expertise: [],
      location: '',
      terms: false
    })

    const isFormValid = computed(() => {
      const basicValid = form.value.name && 
                        form.value.email && 
                        form.value.password && 
                        form.value.password_confirmation && 
                        form.value.role && 
                        form.value.terms
      
      if (form.value.role === 'mechanic') {
        return basicValid && form.value.experience && form.value.expertise.length > 0 && form.value.location
      }
      
      return basicValid
    })

    const handleRegister = async () => {
      if (!isFormValid.value) return

      // Validate password match
      if (form.value.password !== form.value.password_confirmation) {
        errorMessage.value = 'Passwords do not match.'
        return
      }

      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        const registrationData = {
          name: form.value.name,
          email: form.value.email,
          password: form.value.password,
          password_confirmation: form.value.password_confirmation,
          phone: form.value.phone,
          role: form.value.role,
          ...(form.value.role === 'mechanic' && {
            experience_years: parseInt(form.value.experience),
            expertise: form.value.expertise,
            location: form.value.location
          })
        }

        const response = await authAPI.register(registrationData)

        if (response.data.success) {
          // Store user data and token
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          
          successMessage.value = 'Account created successfully! Redirecting...'
          
          // Redirect after a short delay
          setTimeout(() => {
            router.push('/')
          }, 1500)
        }
        
      } catch (error) {
        if (error.response?.data?.message) {
          errorMessage.value = error.response.data.message
        } else if (error.response?.data?.errors) {
          // Handle validation errors
          const errors = error.response.data.errors
          const firstError = Object.values(errors)[0]
          errorMessage.value = Array.isArray(firstError) ? firstError[0] : firstError
        } else {
          errorMessage.value = 'An error occurred during registration. Please try again.'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      isLoading,
      errorMessage,
      successMessage,
      availableSkills,
      isFormValid,
      handleRegister
    }
  }
}
</script>
