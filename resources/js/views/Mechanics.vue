<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Certified Mechanics
        </h1>
        <p class="text-xl text-gray-600">
          Connect with professional mechanics for expert advice and services
        </p>
      </div>

      <!-- Search and Filters -->
      <div class="card mb-8">
        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Mechanics</label>
            <input 
              id="search"
              v-model="searchQuery"
              type="text"
              class="input-field"
              placeholder="Search by name or expertise..."
            />
          </div>
          <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
            <select 
              id="location"
              v-model="selectedLocation"
              class="input-field"
            >
              <option value="">All Locations</option>
              <option value="prishtina">Prishtina</option>
              <option value="peja">Peja</option>
              <option value="prizren">Prizren</option>
              <option value="gjakova">Gjakova</option>
              <option value="ferizaj">Ferizaj</option>
            </select>
          </div>
          <div>
            <label for="expertise" class="block text-sm font-medium text-gray-700 mb-2">Expertise</label>
            <select 
              id="expertise"
              v-model="selectedExpertise"
              class="input-field"
            >
              <option value="">All Specialties</option>
              <option value="engine">Engine</option>
              <option value="transmission">Transmission</option>
              <option value="brakes">Brakes</option>
              <option value="electrical">Electrical</option>
              <option value="diagnostics">Diagnostics</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Mechanics Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="mechanic in filteredMechanics" 
          :key="mechanic.id"
          class="card hover:shadow-lg transition-shadow duration-200"
        >
          <!-- Mechanic Header -->
          <div class="flex items-start space-x-4 mb-4">
            <div class="flex-shrink-0">
              <div class="h-16 w-16 rounded-full bg-primary-500 flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ mechanic.name.charAt(0) }}</span>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900">{{ mechanic.name }}</h3>
              <p class="text-gray-600">{{ mechanic.location }}</p>
              <div class="flex items-center mt-1">
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <span class="ml-1 text-sm text-gray-600">{{ mechanic.rating }}</span>
                </div>
                <span class="mx-2 text-gray-400">•</span>
                <span class="text-sm text-gray-600">{{ mechanic.reviewCount }} reviews</span>
              </div>
            </div>
          </div>

          <!-- Expertise Tags -->
          <div class="mb-4">
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="skill in mechanic.expertise" 
                :key="skill"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- Experience and Availability -->
          <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Experience:</span>
              <span class="text-gray-900">{{ mechanic.experience }} years</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Availability:</span>
              <span class="text-green-600 font-medium">{{ mechanic.availability }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Hourly Rate:</span>
              <span class="text-gray-900">€{{ mechanic.hourlyRate }}/hr</span>
            </div>
          </div>

          <!-- Recent Reviews -->
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Review</h4>
            <div class="bg-gray-50 rounded-lg p-3">
              <p class="text-sm text-gray-700 italic">"{{ mechanic.recentReview }}"</p>
              <p class="text-xs text-gray-500 mt-1">- {{ mechanic.recentReviewer }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex space-x-2">
            <button 
              @click="contactMechanic(mechanic)"
              class="flex-1 btn-primary text-sm py-2"
            >
              Contact
            </button>
            <button 
              @click="viewProfile(mechanic)"
              class="flex-1 btn-secondary text-sm py-2"
            >
              Profile
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredMechanics.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No mechanics found</h3>
        <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
      </div>

      <!-- Contact Modal -->
      <div v-if="showContactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Contact {{ selectedMechanic?.name }}
            </h3>
            
            <form @submit.prevent="sendMessage" class="space-y-4">
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input 
                  id="subject"
                  v-model="contactForm.subject"
                  type="text"
                  required
                  class="input-field mt-1"
                  placeholder="Brief description of your issue"
                />
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea 
                  id="message"
                  v-model="contactForm.message"
                  rows="4"
                  required
                  class="input-field mt-1"
                  placeholder="Describe your car problem in detail..."
                ></textarea>
              </div>

              <div>
                <label for="contact-method" class="block text-sm font-medium text-gray-700">Preferred Contact Method</label>
                <select 
                  id="contact-method"
                  v-model="contactForm.contactMethod"
                  class="input-field mt-1"
                >
                  <option value="message">In-app Message</option>
                  <option value="phone">Phone Call</option>
                  <option value="video">Video Call</option>
                </select>
              </div>

              <div class="flex justify-end space-x-3 pt-4">
                <button 
                  type="button"
                  @click="closeContactModal"
                  class="btn-secondary"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="btn-primary"
                >
                  Send Message
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
import { ref, computed, onMounted } from 'vue'
import { mechanicsAPI } from '../services/api'

export default {
  name: 'Mechanics',
  setup() {
    const mechanics = ref([])
    const searchQuery = ref('')
    const selectedLocation = ref('')
    const selectedExpertise = ref('')
    const showContactModal = ref(false)
    const selectedMechanic = ref(null)
    const contactForm = ref({
      subject: '',
      message: '',
      contactMethod: 'message'
    })

    const filteredMechanics = computed(() => {
      return mechanics.value.filter(mechanic => {
        const matchesSearch = !searchQuery.value || 
          mechanic.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          mechanic.expertise.some(skill => skill.toLowerCase().includes(searchQuery.value.toLowerCase()))
        
        const matchesLocation = !selectedLocation.value || 
          mechanic.location.toLowerCase() === selectedLocation.value.toLowerCase()
        
        const matchesExpertise = !selectedExpertise.value || 
          mechanic.expertise.includes(selectedExpertise.value)
        
        return matchesSearch && matchesLocation && matchesExpertise
      })
    })

    const loadMechanics = async () => {
      try {
        const params = {}
        if (searchQuery.value) params.search = searchQuery.value
        if (selectedLocation.value) params.location = selectedLocation.value
        if (selectedExpertise.value) params.expertise = selectedExpertise.value

        const response = await mechanicsAPI.getAll(params)
        if (response.data.success) {
          mechanics.value = response.data.mechanics.map(mechanic => ({
            id: mechanic.id,
            name: mechanic.user.name,
            location: mechanic.location,
            rating: mechanic.rating,
            reviewCount: mechanic.review_count,
            experience: mechanic.experience_years,
            availability: mechanic.availability,
            hourlyRate: mechanic.hourly_rate,
            expertise: mechanic.expertise,
            recentReview: 'Great service and professional work.',
            recentReviewer: 'Customer'
          }))
        }
      } catch (error) {
        console.error('Error loading mechanics:', error)
      }
    }

    const contactMechanic = (mechanic) => {
      selectedMechanic.value = mechanic
      showContactModal.value = true
    }

    const viewProfile = (mechanic) => {
      // Navigate to mechanic profile or show detailed modal
      console.log('View profile for mechanic:', mechanic.id)
    }

    const sendMessage = () => {
      // Send message to mechanic
      console.log('Sending message:', contactForm.value)
      alert('Message sent successfully!')
      closeContactModal()
    }

    const closeContactModal = () => {
      showContactModal.value = false
      selectedMechanic.value = null
      contactForm.value = {
        subject: '',
        message: '',
        contactMethod: 'message'
      }
    }

    onMounted(() => {
      loadMechanics()
    })

    return {
      mechanics,
      searchQuery,
      selectedLocation,
      selectedExpertise,
      filteredMechanics,
      showContactModal,
      selectedMechanic,
      contactForm,
      contactMechanic,
      viewProfile,
      sendMessage,
      closeContactModal
    }
  }
}
</script>
