<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800">
    <!-- Header -->
    <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <button @click="goBack" class="p-2 text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-white transition-colors duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <div>
              <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Diagnosis History</h1>
              <p class="text-secondary-600 dark:text-secondary-400">View all your past car diagnoses</p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <div class="text-sm text-secondary-600 dark:text-secondary-400">
              Total: {{ diagnoses.length }} diagnoses
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20 mb-8">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex-1 min-w-64">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by car make, model, or problem..."
              class="input w-full"
            />
          </div>
          <select v-model="statusFilter" class="input min-w-32">
            <option value="">All Status</option>
            <option value="completed">Completed</option>
            <option value="processing">Processing</option>
            <option value="failed">Failed</option>
          </select>
          <select v-model="severityFilter" class="input min-w-32">
            <option value="">All Severity</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>
          <button @click="clearFilters" class="btn-outline text-sm">
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500 mx-auto"></div>
        <p class="text-secondary-600 dark:text-secondary-400 mt-4">Loading diagnosis history...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredDiagnoses.length === 0" class="text-center py-20">
        <svg class="w-16 h-16 text-secondary-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-2">No diagnoses found</h3>
        <p class="text-secondary-600 dark:text-secondary-400 mb-6">
          {{ diagnoses.length === 0 ? 'You haven\'t performed any diagnoses yet.' : 'No diagnoses match your current filters.' }}
        </p>
        <router-link to="/diagnose" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Start New Diagnosis
        </router-link>
      </div>

      <!-- Diagnoses List -->
      <div v-else class="space-y-6">
        <div v-for="diagnosis in filteredDiagnoses" :key="diagnosis.id" 
             class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md rounded-2xl p-6 border border-white/20 dark:border-secondary-700/20 hover:shadow-lg transition-all duration-200">
          
          <!-- Diagnosis Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-2">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">
                  {{ diagnosis.make }} {{ diagnosis.model }} ({{ diagnosis.year }})
                </h3>
                <span :class="getStatusBadgeClass(diagnosis.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(diagnosis.status) }}
                </span>
                <span v-if="diagnosis.result?.severity" :class="getSeverityBadgeClass(diagnosis.result.severity)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getSeverityText(diagnosis.result.severity) }}
                </span>
              </div>
              <p class="text-sm text-secondary-600 dark:text-secondary-400">
                {{ formatDate(diagnosis.created_at) }} • {{ diagnosis.result?.ai_provider || 'Unknown AI' }}
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="viewDiagnosis(diagnosis)" class="btn-outline text-sm">
                View Details
              </button>
              <button @click="downloadReport(diagnosis)" class="btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download
              </button>
            </div>
          </div>

          <!-- Problem Description -->
          <div class="mb-4">
            <h4 class="text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">Problem Description</h4>
            <p class="text-sm text-secondary-600 dark:text-secondary-400 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg p-3">
              {{ diagnosis.description }}
            </p>
          </div>

          <!-- Diagnosis Result Summary -->
          <div v-if="diagnosis.result" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
              <h5 class="text-sm font-medium text-primary-700 dark:text-primary-300 mb-1">Problem Title</h5>
              <p class="text-sm text-primary-900 dark:text-primary-100">{{ diagnosis.result.problem_title }}</p>
            </div>
            <div class="bg-info-50 dark:bg-info-900/20 rounded-lg p-4">
              <h5 class="text-sm font-medium text-info-700 dark:text-info-300 mb-1">Confidence Score</h5>
              <p class="text-sm text-info-900 dark:text-info-100">{{ diagnosis.result.confidence_score }}%</p>
            </div>
            <div class="bg-success-50 dark:bg-success-900/20 rounded-lg p-4">
              <h5 class="text-sm font-medium text-success-700 dark:text-success-300 mb-1">Processing Time</h5>
              <p class="text-sm text-success-900 dark:text-success-100">{{ diagnosis.result.processing_time }}s</p>
            </div>
          </div>

          <!-- Symptoms -->
          <div v-if="diagnosis.symptoms && diagnosis.symptoms.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">Reported Symptoms</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="symptom in diagnosis.symptoms" :key="symptom" 
                    class="px-3 py-1 bg-secondary-100 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-300 rounded-full text-xs">
                {{ symptom }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="filteredDiagnoses.length > 0" class="mt-8 flex items-center justify-between">
        <div class="text-sm text-secondary-600 dark:text-secondary-400">
          Showing {{ filteredDiagnoses.length }} of {{ diagnoses.length }} diagnoses
        </div>
        <div class="flex items-center space-x-2">
          <button 
            @click="loadMore" 
            v-if="hasMore"
            class="btn-outline text-sm"
          >
            Load More
          </button>
        </div>
      </div>
    </div>

    <!-- Diagnosis Detail Modal -->
    <div v-if="selectedDiagnosis" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-secondary-200 dark:border-secondary-700">
          <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">
            Diagnosis Details - {{ selectedDiagnosis.make }} {{ selectedDiagnosis.model }}
          </h3>
          <button @click="closeModal" class="p-2 text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <DiagnosisDetail :diagnosis="selectedDiagnosis" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { diagnosisAPI } from '../services/api'
import DiagnosisDetail from '../components/DiagnosisDetail.vue'

export default {
  name: 'DiagnosisHistory',
  components: {
    DiagnosisDetail
  },
  setup() {
    const router = useRouter()
    const diagnoses = ref([])
    const isLoading = ref(true)
    const selectedDiagnosis = ref(null)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const severityFilter = ref('')
    const hasMore = ref(false)

    const filteredDiagnoses = computed(() => {
      let filtered = diagnoses.value

      // Search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(d => 
          d.make.toLowerCase().includes(query) ||
          d.model.toLowerCase().includes(query) ||
          d.description.toLowerCase().includes(query) ||
          d.result?.problem_title?.toLowerCase().includes(query)
        )
      }

      // Status filter
      if (statusFilter.value) {
        filtered = filtered.filter(d => d.status === statusFilter.value)
      }

      // Severity filter
      if (severityFilter.value) {
        filtered = filtered.filter(d => d.result?.severity === severityFilter.value)
      }

      return filtered
    })

    const loadDiagnoses = async () => {
      try {
        isLoading.value = true
        console.log('Loading diagnosis history...')
        
        const response = await diagnosisAPI.getHistory()
        console.log('Diagnosis history response:', response)
        
        if (response.data.success) {
          // The API returns paginated data with 'data' property
          diagnoses.value = response.data.data?.data || []
          console.log('Loaded diagnoses:', diagnoses.value.length)
        } else {
          console.error('API returned error:', response.data.message)
        }
      } catch (error) {
        console.error('Error loading diagnosis history:', error)
        console.error('Error details:', error.response?.data)
        
        // Check if it's an authentication error
        if (error.response?.status === 401) {
          console.log('Authentication error, redirecting to login')
          router.push('/login')
        }
      } finally {
        isLoading.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    const viewDiagnosis = (diagnosis) => {
      selectedDiagnosis.value = diagnosis
    }

    const closeModal = () => {
      selectedDiagnosis.value = null
    }

    const downloadReport = async (diagnosis) => {
      try {
        // Create PDF content
        const pdfContent = {
          title: 'CarWise.ai - AI Diagnosis Report',
          vehicle: {
            make: diagnosis.make,
            model: diagnosis.model,
            year: diagnosis.year,
            mileage: diagnosis.mileage,
            engine_type: diagnosis.engine_type,
            engine_size: diagnosis.engine_size
          },
          symptoms: diagnosis.symptoms || [],
          description: diagnosis.description,
          result: diagnosis.result,
          generated_at: diagnosis.created_at
        }

        // Call backend to generate PDF
        const response = await fetch('/api/diagnosis/export-pdf', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(pdfContent)
        })

        if (response.ok) {
          // Create download link
          const blob = await response.blob()
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          link.download = `CarWise_Diagnosis_${diagnosis.make}_${diagnosis.model}_${new Date(diagnosis.created_at).toISOString().split('T')[0]}.pdf`
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          window.URL.revokeObjectURL(url)
        } else {
          const error = await response.json()
          alert('Failed to download report: ' + (error.message || 'Unknown error'))
        }
      } catch (error) {
        console.error('Error downloading report:', error)
        alert('Failed to download report. Please try again.')
      }
    }

    const clearFilters = () => {
      searchQuery.value = ''
      statusFilter.value = ''
      severityFilter.value = ''
    }

    const loadMore = () => {
      // TODO: Implement pagination
      console.log('Load more diagnoses')
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const getStatusBadgeClass = (status) => {
      switch (status?.toLowerCase()) {
        case 'completed': return 'bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-400'
        case 'processing': return 'bg-warning-100 dark:bg-warning-900 text-warning-600 dark:text-warning-400'
        case 'failed': return 'bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-400'
        default: return 'bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'
      }
    }

    const getStatusText = (status) => {
      switch (status?.toLowerCase()) {
        case 'completed': return 'Completed'
        case 'processing': return 'Processing'
        case 'failed': return 'Failed'
        default: return 'Unknown'
      }
    }

    const getSeverityBadgeClass = (severity) => {
      switch (severity?.toLowerCase()) {
        case 'low': return 'bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-400'
        case 'medium': return 'bg-warning-100 dark:bg-warning-900 text-warning-600 dark:text-warning-400'
        case 'high': return 'bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-400'
        case 'critical': return 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400'
        default: return 'bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'
      }
    }

    const getSeverityText = (severity) => {
      switch (severity?.toLowerCase()) {
        case 'low': return 'Low'
        case 'medium': return 'Medium'
        case 'high': return 'High'
        case 'critical': return 'Critical'
        default: return 'Unknown'
      }
    }

    onMounted(() => {
      // Check if user is logged in
      const token = localStorage.getItem('token')
      if (!token) {
        router.push('/login')
        return
      }
      
      loadDiagnoses()
    })

    return {
      diagnoses,
      isLoading,
      selectedDiagnosis,
      searchQuery,
      statusFilter,
      severityFilter,
      hasMore,
      filteredDiagnoses,
      loadDiagnoses,
      goBack,
      viewDiagnosis,
      closeModal,
      downloadReport,
      clearFilters,
      loadMore,
      formatDate,
      getStatusBadgeClass,
      getStatusText,
      getSeverityBadgeClass,
      getSeverityText,
      t
    }
  }
}
</script>
