<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full mb-6">
          <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
          </svg>
        </div>
        <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">
          {{ t('certified_mechanics') }}
        </h1>
        <p class="text-xl text-secondary-600 dark:text-secondary-400 max-w-3xl mx-auto">
          {{ t('connect_with_professionals') }}
        </p>
      </div>

      <!-- Add Mechanic Button -->
      <div class="flex justify-end mb-8">
        <button 
          @click="showAddForm = !showAddForm"
          class="btn-primary flex items-center space-x-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <span>Add Mechanic</span>
        </button>
      </div>

      <!-- Add Mechanic Form (collapsible) -->
      <div v-if="showAddForm" class="card-hover mb-8">
        <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-4">Add New Mechanic</h2>
        <form @submit.prevent="submitMechanic" class="space-y-4" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input v-model="form.name" class="input" placeholder="Full name or business name" required />
            <input v-model="form.phone" class="input" placeholder="Phone number" />
            <input v-model="form.email" class="input" placeholder="Email" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input v-model="form.address" class="input" placeholder="Address" />
            <input v-model="form.city" class="input" placeholder="City" required />
            <input v-model="form.country" class="input" placeholder="Country" required />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input v-model.number="form.lat" class="input" placeholder="Latitude" />
            <input v-model.number="form.lng" class="input" placeholder="Longitude" />
            <input v-model.number="form.hourly_rate" class="input" placeholder="Hourly rate" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <textarea v-model="form.bio" class="input" placeholder="Short bio"></textarea>
            <input ref="logo" type="file" accept="image/*" class="input" />
          </div>
          <div>
            <label class="block text-sm mb-2 text-secondary-700 dark:text-secondary-300">Gallery images (optional)</label>
            <input ref="gallery" type="file" multiple accept="image/*" class="input" />
          </div>
          <div class="flex items-center space-x-3">
            <button class="btn-primary" :disabled="isSubmitting">{{ isSubmitting ? 'Saving...' : 'Save' }}</button>
            <button type="button" @click="showAddForm = false" class="btn-secondary">Cancel</button>
            <span v-if="submitError" class="text-danger-600 text-sm">{{ submitError }}</span>
            <span v-if="submitSuccess" class="text-success-600 text-sm">Saved!</span>
          </div>
        </form>
      </div>

      <!-- Search and Filters -->
      <div class="card-hover mb-8">
        <div class="grid md:grid-cols-3 gap-6">
          <div>
            <label for="search" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
              {{ t('search_mechanics') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input 
                id="search"
                v-model="searchQuery"
                type="text"
                class="input pl-10"
                placeholder="Search by name, city, country, expertise, or services..."
              />
            </div>
          </div>
          <div>
            <label for="location" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
              Location
            </label>
            <select 
              id="location"
              v-model="selectedLocation"
              class="input"
            >
              <option value="">All Locations</option>
              <option value="London">London, UK</option>
              <option value="Manchester">Manchester, UK</option>
              <option value="Birmingham">Birmingham, UK</option>
              <option value="Paris">Paris, France</option>
              <option value="Lyon">Lyon, France</option>
              <option value="Berlin">Berlin, Germany</option>
              <option value="Munich">Munich, Germany</option>
              <option value="Rome">Rome, Italy</option>
              <option value="Milan">Milan, Italy</option>
              <option value="Madrid">Madrid, Spain</option>
              <option value="Amsterdam">Amsterdam, Netherlands</option>
              <option value="Stockholm">Stockholm, Sweden</option>
              <option value="New York">New York, USA</option>
              <option value="Los Angeles">Los Angeles, USA</option>
              <option value="Chicago">Chicago, USA</option>
              <option value="Miami">Miami, USA</option>
              <option value="Toronto">Toronto, Canada</option>
              <option value="Vancouver">Vancouver, Canada</option>
              <option value="Montreal">Montreal, Canada</option>
              <option value="Tokyo">Tokyo, Japan</option>
              <option value="Shanghai">Shanghai, China</option>
              <option value="Seoul">Seoul, South Korea</option>
              <option value="Singapore">Singapore</option>
              <option value="Bangkok">Bangkok, Thailand</option>
              <option value="Kuala Lumpur">Kuala Lumpur, Malaysia</option>
              <option value="Johannesburg">Johannesburg, South Africa</option>
              <option value="Cairo">Cairo, Egypt</option>
              <option value="Lagos">Lagos, Nigeria</option>
              <option value="São Paulo">São Paulo, Brazil</option>
              <option value="Buenos Aires">Buenos Aires, Argentina</option>
              <option value="Santiago">Santiago, Chile</option>
              <option value="Lima">Lima, Peru</option>
              <option value="Sydney">Sydney, Australia</option>
              <option value="Melbourne">Melbourne, Australia</option>
              <option value="Auckland">Auckland, New Zealand</option>
            </select>
          </div>
          <div>
            <label for="expertise" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
              Expertise
            </label>
            <select 
              id="expertise"
              v-model="selectedExpertise"
              class="input"
            >
              <option value="">All Specialties</option>
              <option value="Engine">Engine</option>
              <option value="Transmission">Transmission</option>
              <option value="Diagnostics">Diagnostics</option>
              <option value="AC Systems">AC Systems</option>
              <option value="4WD Systems">4WD Systems</option>
              <option value="Performance Tuning">Performance Tuning</option>
              <option value="Luxury Cars">Luxury Cars</option>
              <option value="General Repair">General Repair</option>
              <option value="Exotic Vehicles">Exotic Vehicles</option>
              <option value="Hybrid Systems">Hybrid Systems</option>
              <option value="Winter Systems">Winter Systems</option>
              <option value="Classic Restoration">Classic Restoration</option>
              <option value="Bodywork">Bodywork</option>
              <option value="Paint">Paint</option>
              <option value="Suspension">Suspension</option>
              <option value="Brakes">Brakes</option>
              <option value="Electrical">Electrical</option>
              <option value="BMW">BMW</option>
              <option value="Mercedes">Mercedes</option>
              <option value="Audi">Audi</option>
              <option value="French Cars">French Cars</option>
              <option value="Italian Cars">Italian Cars</option>
              <option value="German Cars">German Cars</option>
              <option value="Japanese Cars">Japanese Cars</option>
              <option value="Korean Cars">Korean Cars</option>
              <option value="Chinese Cars">Chinese Cars</option>
              <option value="Swedish Cars">Swedish Cars</option>
              <option value="Brazilian Cars">Brazilian Cars</option>
              <option value="Indian Cars">Indian Cars</option>
            </select>
          </div>
        </div>
        
        <!-- Clear Filters Button -->
        <div class="flex justify-end mt-4">
          <button 
            @click="clearFilters"
            class="btn-secondary text-sm px-4 py-2 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Clear Filters</span>
          </button>
        </div>
      </div>

      <!-- Mechanics Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="mechanic in filteredMechanics" 
          :key="mechanic.id"
          class="card-hover group"
        >
          <!-- Mechanic Header -->
          <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0">
              <div class="h-16 w-16 gradient-primary rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                <span class="text-white text-xl font-bold">{{ mechanic.name.charAt(0) }}</span>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">{{ mechanic.name }}</h3>
              <p class="text-secondary-600 dark:text-secondary-400 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ mechanic.city }}, {{ mechanic.country }}
              </p>
              <div class="flex items-center mt-2">
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-warning-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  <span class="ml-1 text-sm text-secondary-600 dark:text-secondary-400 font-medium">{{ mechanic.rating }}</span>
                </div>
                <span class="mx-2 text-secondary-400">•</span>
                <span class="text-sm text-secondary-600 dark:text-secondary-400">{{ mechanic.review_count }} reviews</span>
              </div>
            </div>
          </div>

          <!-- Expertise Tags -->
          <div class="mb-6">
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="skill in mechanic.expertise" 
                :key="skill"
                class="badge badge-primary"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- Experience and Availability -->
          <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center">
              <span class="text-sm text-secondary-600 dark:text-secondary-400 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Experience
              </span>
              <span class="text-sm font-medium text-secondary-900 dark:text-white">{{ mechanic.experience_years }} years</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-secondary-600 dark:text-secondary-400 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Availability
              </span>
              <span class="text-sm font-medium text-success-600 dark:text-success-400 capitalize">{{ mechanic.availability }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-secondary-600 dark:text-secondary-400 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Hourly Rate
              </span>
              <span class="text-sm font-medium text-secondary-900 dark:text-white">€{{ mechanic.hourly_rate }}/hr</span>
            </div>
          </div>


          <!-- Actions -->
          <div class="flex space-x-3">
            <button 
              @click="contactMechanic(mechanic)"
              class="flex-1 btn-primary text-sm py-3 flex items-center justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              Contact
            </button>
            <button 
              @click="viewProfile(mechanic)"
              class="flex-1 btn-secondary text-sm py-3 flex items-center justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              Profile
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredMechanics.length === 0" class="text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-secondary-100 dark:bg-secondary-800 rounded-full mb-6">
          <svg class="w-10 h-10 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-2">No mechanics found</h3>
        <p class="text-secondary-600 dark:text-secondary-400 mb-6">
          No mechanics match your current search criteria. Try adjusting your filters or search terms.
        </p>
        <button 
          @click="clearFilters"
          class="btn-primary"
        >
          Clear All Filters
        </button>
      </div>

      <!-- Contact Modal -->
      <div v-if="showContactModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-md">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-semibold text-secondary-900 dark:text-white">
                Contact {{ selectedMechanic?.name }}
              </h3>
              <button 
                @click="closeContactModal"
                class="text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-300 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <form @submit.prevent="sendMessage" class="space-y-6">
              <div>
                <label for="subject" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Subject
                </label>
                <input 
                  id="subject"
                  v-model="contactForm.subject"
                  type="text"
                  required
                  class="input"
                  placeholder="Brief description of your issue"
                />
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Message
                </label>
                <textarea 
                  id="message"
                  v-model="contactForm.message"
                  rows="4"
                  required
                  class="input"
                  placeholder="Describe your car problem in detail..."
                ></textarea>
              </div>

              <div>
                <label for="contact-method" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Preferred Contact Method
                </label>
                <select 
                  id="contact-method"
                  v-model="contactForm.contactMethod"
                  class="input"
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
import { t } from '../utils/translations'

export default {
  name: 'Mechanics',
  setup() {
    const mechanics = ref([])
    const searchQuery = ref('')
    const selectedLocation = ref('')
    const selectedExpertise = ref('')
    const showContactModal = ref(false)
    const selectedMechanic = ref(null)
    const showAddForm = ref(false)
    // Form state
    const form = ref({
      name: '', phone: '', email: '', address: '', city: '', country: '',
      lat: '', lng: '', hourly_rate: '', bio: ''
    })
    const logo = ref(null)
    const gallery = ref(null)
    const isSubmitting = ref(false)
    const submitError = ref('')
    const submitSuccess = ref(false)

    const submitMechanic = async () => {
      submitError.value = ''
      submitSuccess.value = false
      if (isSubmitting.value) return
      try {
        isSubmitting.value = true
        const fd = new FormData()
        Object.entries(form.value).forEach(([k, v]) => v !== '' && fd.append(k, v))
        if (logo.value?.files?.[0]) fd.append('logo', logo.value.files[0])
        if (gallery.value?.files?.length) {
          Array.from(gallery.value.files).forEach(f => fd.append('gallery[]', f))
        }
        const res = await mechanicsAPI.create(fd)
        if (res.data?.success) {
          submitSuccess.value = true
          await loadMechanics()
          setTimeout(() => { submitSuccess.value = false }, 2000)
        } else {
          submitError.value = res.data?.message || 'Failed to save'
        }
      } catch (e) {
        console.error(e)
        submitError.value = 'Failed to save mechanic'
      } finally {
        isSubmitting.value = false
      }
    }
    const contactForm = ref({
      subject: '',
      message: '',
      contactMethod: 'message'
    })

    const filteredMechanics = computed(() => {
      return mechanics.value.filter(mechanic => {
        // Enhanced search functionality
        const searchTerm = searchQuery.value.toLowerCase()
        const matchesSearch = !searchQuery.value || 
          mechanic.name.toLowerCase().includes(searchTerm) ||
          mechanic.city.toLowerCase().includes(searchTerm) ||
          mechanic.country.toLowerCase().includes(searchTerm) ||
          mechanic.bio?.toLowerCase().includes(searchTerm) ||
          mechanic.expertise.some(skill => skill.toLowerCase().includes(searchTerm)) ||
          mechanic.services?.some(service => service.toLowerCase().includes(searchTerm))
        
        // Location filter - match city name
        const matchesLocation = !selectedLocation.value || 
          mechanic.city === selectedLocation.value ||
          mechanic.location?.toLowerCase().includes(selectedLocation.value.toLowerCase())
        
        // Expertise filter - exact match or partial match
        const matchesExpertise = !selectedExpertise.value || 
          mechanic.expertise.includes(selectedExpertise.value) ||
          mechanic.expertise.some(skill => skill.toLowerCase().includes(selectedExpertise.value.toLowerCase()))
        
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
            name: mechanic.name || mechanic.user?.name || 'Unknown',
            city: mechanic.city || 'Unknown',
            country: mechanic.country || 'Unknown',
            location: mechanic.location,
            rating: mechanic.rating || 4.5,
            review_count: mechanic.review_count || 0,
            experience_years: mechanic.experience_years || 0,
            availability: mechanic.availability || 'available',
            hourly_rate: mechanic.hourly_rate || 25,
            expertise: typeof mechanic.expertise === 'string' ? JSON.parse(mechanic.expertise) : (mechanic.expertise || ['Engine', 'Transmission', 'Diagnostics']),
            services: typeof mechanic.services === 'string' ? JSON.parse(mechanic.services) : (mechanic.services || [])
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

    const clearFilters = () => {
      searchQuery.value = ''
      selectedLocation.value = ''
      selectedExpertise.value = ''
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
      closeContactModal,
      clearFilters,
      showAddForm,
      form,
      logo,
      gallery,
      isSubmitting,
      submitError,
      submitSuccess,
      submitMechanic,
      t
    }
  }
}
</script>
