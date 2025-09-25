<template>
  <div class="space-y-6">
    <!-- Diagnosis Summary -->
    <div class="bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-primary-900/20 dark:to-secondary-900/20 rounded-lg p-6">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Diagnosis Summary</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">Problem Title</p>
          <p class="font-medium text-secondary-900 dark:text-white">{{ diagnosis.result?.problem_title || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">Severity</p>
          <span :class="getSeverityBadgeClass(diagnosis.result?.severity)" class="px-2 py-1 rounded-full text-xs font-medium">
            {{ getSeverityText(diagnosis.result?.severity) }}
          </span>
        </div>
        <div>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">Confidence Score</p>
          <p class="font-medium text-secondary-900 dark:text-white">{{ diagnosis.result?.confidence_score || 'N/A' }}%</p>
        </div>
        <div>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">Processing Time</p>
          <p class="font-medium text-secondary-900 dark:text-white">{{ diagnosis.result?.processing_time || 'N/A' }}s</p>
        </div>
      </div>
    </div>

    <!-- Problem Description -->
    <div>
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Problem Description</h4>
      <div class="bg-secondary-50 dark:bg-secondary-700/50 rounded-lg p-4">
        <p class="text-secondary-700 dark:text-secondary-300">{{ diagnosis.description }}</p>
      </div>
    </div>

    <!-- Likely Causes -->
    <div v-if="diagnosis.result?.likely_causes">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Likely Causes</h4>
      <div class="space-y-3">
        <div v-for="(cause, index) in getLikelyCauses()" :key="index" 
             class="bg-white dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-lg p-4">
          <h5 class="font-medium text-secondary-900 dark:text-white mb-2">{{ cause.title }}</h5>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ cause.description }}</p>
        </div>
      </div>
    </div>

    <!-- Recommended Actions -->
    <div v-if="diagnosis.result?.recommended_actions">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Recommended Actions</h4>
      <div class="space-y-3">
        <div v-for="(action, index) in getRecommendedActions()" :key="index" 
             class="bg-white dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-lg p-4">
          <h5 class="font-medium text-secondary-900 dark:text-white mb-2">{{ action.title }}</h5>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ action.description }}</p>
        </div>
      </div>
    </div>

    <!-- Estimated Costs -->
    <div v-if="diagnosis.result?.estimated_costs">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Estimated Costs</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="(cost, index) in getEstimatedCosts()" :key="index" 
             class="bg-white dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-lg p-4">
          <h5 class="font-medium text-secondary-900 dark:text-white mb-2">{{ cost.service }}</h5>
          <p class="text-sm text-secondary-600 dark:text-secondary-400">{{ cost.description }}</p>
          <p class="text-lg font-semibold text-primary-600 dark:text-primary-400 mt-2">{{ cost.cost }}</p>
        </div>
      </div>
    </div>

    <!-- AI Insights -->
    <div v-if="diagnosis.result?.ai_insights">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">AI Insights</h4>
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <p class="text-blue-800 dark:text-blue-200">{{ diagnosis.result.ai_insights }}</p>
      </div>
    </div>

    <!-- Symptoms -->
    <div v-if="diagnosis.symptoms && diagnosis.symptoms.length > 0">
      <h4 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">Reported Symptoms</h4>
      <div class="flex flex-wrap gap-2">
        <span v-for="symptom in diagnosis.symptoms" :key="symptom" 
              class="px-3 py-1 bg-secondary-100 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-300 rounded-full text-sm">
          {{ symptom }}
        </span>
      </div>
    </div>

    <!-- Urgency Alert -->
    <div v-if="diagnosis.result?.requires_immediate_attention" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-6">
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
  </div>
</template>

<script>
export default {
  name: 'DiagnosisDetail',
  props: {
    diagnosis: {
      type: Object,
      required: true
    }
  },
  methods: {
    getLikelyCauses() {
      if (!this.diagnosis.result?.likely_causes) return []
      try {
        return typeof this.diagnosis.result.likely_causes === 'string' 
          ? JSON.parse(this.diagnosis.result.likely_causes)
          : this.diagnosis.result.likely_causes
      } catch {
        return []
      }
    },
    
    getRecommendedActions() {
      if (!this.diagnosis.result?.recommended_actions) return []
      try {
        return typeof this.diagnosis.result.recommended_actions === 'string' 
          ? JSON.parse(this.diagnosis.result.recommended_actions)
          : this.diagnosis.result.recommended_actions
      } catch {
        return []
      }
    },
    
    getEstimatedCosts() {
      if (!this.diagnosis.result?.estimated_costs) return []
      try {
        return typeof this.diagnosis.result.estimated_costs === 'string' 
          ? JSON.parse(this.diagnosis.result.estimated_costs)
          : this.diagnosis.result.estimated_costs
      } catch {
        return []
      }
    },
    
    getSeverityBadgeClass(severity) {
      switch (severity?.toLowerCase()) {
        case 'low': return 'bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-400'
        case 'medium': return 'bg-warning-100 dark:bg-warning-900 text-warning-600 dark:text-warning-400'
        case 'high': return 'bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-400'
        case 'critical': return 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400'
        default: return 'bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-400'
      }
    },
    
    getSeverityText(severity) {
      switch (severity?.toLowerCase()) {
        case 'low': return 'Low'
        case 'medium': return 'Medium'
        case 'high': return 'High'
        case 'critical': return 'Critical'
        default: return 'Unknown'
      }
    }
  }
}
</script>
