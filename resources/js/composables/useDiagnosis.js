import { ref, computed } from 'vue'
import { diagnosisAPI } from '../services/api'

// Global diagnosis state
const diagnosisHistory = ref([])
const currentDiagnosis = ref(null)
const isLoading = ref(false)

export function useDiagnosis() {
  const submitDiagnosis = async (diagnosisData) => {
    try {
      isLoading.value = true
      const response = await diagnosisAPI.submitDiagnosis(diagnosisData)
      
      if (response.data.success) {
        currentDiagnosis.value = response.data
        diagnosisHistory.value.unshift(response.data)
        
        if (window.$notify) {
          window.$notify.success('Diagnosis Complete', 'AI analysis has been completed successfully')
        }
        
        return { success: true, diagnosis: response.data }
      }
      
      return { success: false, message: 'Diagnosis failed' }
    } catch (error) {
      console.error('Diagnosis error:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Diagnosis failed. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const getDiagnosisHistory = async () => {
    try {
      isLoading.value = true
      const response = await diagnosisAPI.getHistory()
      
      if (response.data.success) {
        diagnosisHistory.value = response.data.diagnoses || []
        return { success: true, diagnoses: response.data.diagnoses }
      }
      
      return { success: false, message: 'Failed to load diagnosis history' }
    } catch (error) {
      console.error('Error loading diagnosis history:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error loading diagnosis history.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const getDiagnosisResult = async (sessionId) => {
    try {
      isLoading.value = true
      const response = await diagnosisAPI.getResult(sessionId)
      
      if (response.data.success) {
        return { success: true, result: response.data.result }
      }
      
      return { success: false, message: 'Failed to load diagnosis result' }
    } catch (error) {
      console.error('Error loading diagnosis result:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error loading diagnosis result.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const getDiagnosisById = (diagnosisId) => {
    return diagnosisHistory.value.find(diagnosis => diagnosis.id === diagnosisId)
  }

  // Computed properties
  const totalDiagnoses = computed(() => diagnosisHistory.value.length)
  const recentDiagnoses = computed(() => diagnosisHistory.value.slice(0, 5))
  const diagnosesBySeverity = computed(() => {
    const severity = {}
    diagnosisHistory.value.forEach(diagnosis => {
      const sev = diagnosis.severity || 'medium'
      severity[sev] = (severity[sev] || 0) + 1
    })
    return severity
  })

  const averageConfidence = computed(() => {
    if (diagnosisHistory.value.length === 0) return 0
    const total = diagnosisHistory.value.reduce((sum, diagnosis) => {
      return sum + (diagnosis.confidence_score || 0)
    }, 0)
    return Math.round(total / diagnosisHistory.value.length)
  })

  return {
    // State
    diagnosisHistory: readonly(diagnosisHistory),
    currentDiagnosis: readonly(currentDiagnosis),
    isLoading: readonly(isLoading),
    
    // Computed
    totalDiagnoses,
    recentDiagnoses,
    diagnosesBySeverity,
    averageConfidence,
    
    // Actions
    submitDiagnosis,
    getDiagnosisHistory,
    getDiagnosisResult,
    getDiagnosisById
  }
}
