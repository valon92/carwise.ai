<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <!-- Header -->
    <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <button @click="goBack" class="p-2 text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-white transition-colors duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <div>
              <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Edit Profile</h1>
              <p class="text-secondary-600 dark:text-secondary-400">Update your personal information</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-8 border border-white/20 dark:border-secondary-700/20">
        
        <!-- Success/Error Messages -->
        <div v-if="successMessage" class="mb-6 p-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700 rounded-lg">
          <div class="flex">
            <svg class="w-5 h-5 text-success-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-success-800 dark:text-success-200">{{ successMessage }}</p>
          </div>
        </div>

        <div v-if="errorMessage" class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700 rounded-lg">
          <div class="flex">
            <svg class="w-5 h-5 text-danger-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-danger-800 dark:text-danger-200">{{ errorMessage }}</p>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-8">
          <!-- Personal Information -->
          <div class="space-y-6">
            <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
              </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- First Name -->
              <div>
                <label for="first_name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  First Name *
                </label>
                <input
                  id="first_name"
                  v-model="form.first_name"
                  type="text"
                  required
                  class="input"
                  placeholder="Enter your first name"
                />
              </div>

              <!-- Last Name -->
              <div>
                <label for="last_name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Last Name *
                </label>
                <input
                  id="last_name"
                  v-model="form.last_name"
                  type="text"
                  required
                  class="input"
                  placeholder="Enter your last name"
                />
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Email Address *
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="input"
                  placeholder="Enter your email"
                />
              </div>

              <!-- Phone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Phone Number
                </label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  class="input"
                  placeholder="Enter your phone number"
                />
              </div>

              <!-- Location -->
              <div class="sm:col-span-2">
                <label for="location" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Location
                </label>
                <input
                  id="location"
                  v-model="form.location"
                  type="text"
                  class="input"
                  placeholder="Enter your location (city, country)"
                />
              </div>
            </div>
          </div>

          <!-- Mechanic Information (if user is mechanic) -->
          <div v-if="user?.role === 'mechanic'" class="space-y-6">
            <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Professional Information
              </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Experience Years -->
              <div>
                <label for="experience_years" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Years of Experience
                </label>
                <input
                  id="experience_years"
                  v-model="form.experience_years"
                  type="number"
                  min="0"
                  max="50"
                  class="input"
                  placeholder="Years of experience"
                />
              </div>

              <!-- Hourly Rate -->
              <div>
                <label for="hourly_rate" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Hourly Rate ({{ user?.currency_code || 'USD' }})
                </label>
                <input
                  id="hourly_rate"
                  v-model="form.hourly_rate"
                  type="number"
                  step="0.01"
                  min="0"
                  class="input"
                  placeholder="Hourly rate"
                />
              </div>

              <!-- Bio -->
              <div class="sm:col-span-2">
                <label for="bio" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Bio
                </label>
                <textarea
                  id="bio"
                  v-model="form.bio"
                  rows="4"
                  class="input"
                  placeholder="Tell us about your experience and expertise..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Password Change -->
          <div class="space-y-6">
            <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Change Password (Optional)
              </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- New Password -->
              <div>
                <label for="new_password" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  New Password
                </label>
                <div class="relative">
                  <input
                    id="new_password"
                    v-model="form.new_password"
                    :type="showNewPassword ? 'text' : 'password'"
                    class="input pr-10"
                    placeholder="Enter new password"
                  />
                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                  >
                    <svg v-if="showNewPassword" class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Confirm New Password
                </label>
                <div class="relative">
                  <input
                    id="new_password_confirmation"
                    v-model="form.new_password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="input pr-10"
                    placeholder="Confirm new password"
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                  >
                    <svg v-if="showConfirmPassword" class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-secondary-200 dark:border-secondary-700">
            <button
              type="button"
              @click="goBack"
              class="btn-outline"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="btn-primary"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isLoading ? 'Updating...' : 'Update Profile' }}
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
import { authAPI } from '../services/api'
import { t } from '../utils/translations'

export default {
  name: 'ProfileEdit',
  setup() {
    const router = useRouter()
    const user = ref(null)
    const isLoading = ref(false)
    const errorMessage = ref('')
    const successMessage = ref('')
    const showNewPassword = ref(false)
    const showConfirmPassword = ref(false)

    const form = ref({
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      location: '',
      experience_years: '',
      hourly_rate: '',
      bio: '',
      new_password: '',
      new_password_confirmation: ''
    })

    const loadUserData = async () => {
      try {
        const response = await authAPI.getUser()
        if (response.data.success) {
          user.value = response.data.user
          
          // Populate form with current user data
          form.value.first_name = user.value.first_name || ''
          form.value.last_name = user.value.last_name || ''
          form.value.email = user.value.email || ''
          form.value.phone = user.value.phone || ''
          form.value.location = user.value.location || ''
          form.value.experience_years = user.value.experience_years || ''
          form.value.hourly_rate = user.value.hourly_rate || ''
          form.value.bio = user.value.bio || ''
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        errorMessage.value = 'Failed to load user data'
      }
    }

    const handleSubmit = async () => {
      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        // Validate password if provided
        if (form.value.new_password && form.value.new_password !== form.value.new_password_confirmation) {
          errorMessage.value = 'New passwords do not match'
          return
        }

        const updateData = {
          first_name: form.value.first_name,
          last_name: form.value.last_name,
          email: form.value.email,
          phone: form.value.phone,
          location: form.value.location,
        }

        // Add password if provided
        if (form.value.new_password) {
          updateData.password = form.value.new_password
          updateData.password_confirmation = form.value.new_password_confirmation
        }

        // Add mechanic-specific fields if user is mechanic
        if (user.value?.role === 'mechanic') {
          updateData.experience_years = form.value.experience_years ? parseInt(form.value.experience_years) : null
          updateData.hourly_rate = form.value.hourly_rate ? parseFloat(form.value.hourly_rate) : null
          updateData.bio = form.value.bio
        }

        const response = await authAPI.updateProfile(updateData)

        if (response.data.success) {
          // Update local storage
          localStorage.setItem('user', JSON.stringify(response.data.user))
          
          successMessage.value = 'Profile updated successfully!'
          
          // Redirect to dashboard after a short delay
          setTimeout(() => {
            router.push('/dashboard')
          }, 1500)
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        errorMessage.value = error.response?.data?.message || 'Failed to update profile'
      } finally {
        isLoading.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    onMounted(() => {
      // Check if user is logged in
      const token = localStorage.getItem('token')
      const storedUser = localStorage.getItem('user')
      
      if (token && storedUser) {
        user.value = JSON.parse(storedUser)
        loadUserData()
      } else {
        router.push('/login')
      }
    })

    return {
      user,
      form,
      isLoading,
      errorMessage,
      successMessage,
      showNewPassword,
      showConfirmPassword,
      handleSubmit,
      goBack,
      t
    }
  }
}
</script>
