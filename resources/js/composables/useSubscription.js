import { ref } from 'vue'
import { useAuth } from './useAuth'

export function useSubscription() {
  const { authAPI } = useAuth()
  const loading = ref(false)
  const error = ref(null)

  /**
   * Get available subscription plans
   */
  const getPlans = async () => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.get('/subscription/plans')
      
      if (response.data.success) {
        return response.data.plans
      } else {
        throw new Error(response.data.error || 'Failed to fetch plans')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Get user's subscription status
   */
  const getSubscriptionStatus = async () => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.get('/subscription/status')
      
      if (response.data.success) {
        return response.data.subscription
      } else {
        throw new Error(response.data.error || 'Failed to fetch subscription status')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Create new subscription
   */
  const createSubscription = async (planId, paymentData = {}) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.post('/subscription/subscribe', {
        plan_id: planId,
        ...paymentData
      })
      
      if (response.data.success) {
        return response.data.subscription
      } else {
        throw new Error(response.data.error || 'Failed to create subscription')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Change subscription plan
   */
  const changePlan = async (newPlanId) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.post('/subscription/change-plan', {
        new_plan_id: newPlanId
      })
      
      if (response.data.success) {
        return response.data.subscription
      } else {
        throw new Error(response.data.error || 'Failed to change plan')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Cancel subscription
   */
  const cancelSubscription = async (reason = null) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.post('/subscription/cancel', {
        reason: reason
      })
      
      if (response.data.success) {
        return response.data.subscription
      } else {
        throw new Error(response.data.error || 'Failed to cancel subscription')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Check usage limits for a specific action
   */
  const checkUsageLimits = async (action) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.get(`/subscription/usage-limits?action=${action}`)
      
      if (response.data.success) {
        return response.data.limits
      } else {
        throw new Error(response.data.error || 'Failed to check usage limits')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Record usage for a specific action
   */
  const recordUsage = async (actionType, count = 1, metadata = null) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.post('/subscription/record-usage', {
        action_type: actionType,
        count: count,
        metadata: metadata
      })
      
      if (response.data.success) {
        return response.data
      } else {
        throw new Error(response.data.error || 'Failed to record usage')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Get billing history
   */
  const getBillingHistory = async () => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.get('/subscription/billing-history')
      
      if (response.data.success) {
        return response.data
      } else {
        throw new Error(response.data.error || 'Failed to fetch billing history')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Check if user has access to a specific feature
   */
  const checkFeatureAccess = async (feature) => {
    try {
      loading.value = true
      error.value = null

      const response = await authAPI.get(`/subscription/feature-access/${feature}`)
      
      if (response.data.success) {
        return response.data.has_access
      } else {
        throw new Error(response.data.error || 'Failed to check feature access')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Check if user can perform a diagnosis
   */
  const canPerformDiagnosis = async () => {
    try {
      const limits = await checkUsageLimits('diagnosis')
      return limits.allowed
    } catch (err) {
      console.error('Failed to check diagnosis limits:', err)
      return false
    }
  }

  /**
   * Check if user can add a vehicle
   */
  const canAddVehicle = async () => {
    try {
      const limits = await checkUsageLimits('vehicle_add')
      return limits.allowed
    } catch (err) {
      console.error('Failed to check vehicle limits:', err)
      return false
    }
  }

  /**
   * Get subscription plan features
   */
  const getPlanFeatures = (planId) => {
    const plans = {
      basic: [
        'basic_diagnosis',
        'email_support',
        'basic_reports',
        'vehicle_management'
      ],
      pro: [
        'ai_reports',
        'service_offers',
        'priority_support',
        'advanced_analytics',
        'parts_recommendations',
        'maintenance_reminders'
      ],
      elite: [
        'continuous_monitoring',
        'ai_advice',
        'preventive_care',
        'white_label_reports',
        'api_access',
        'custom_integrations',
        'dedicated_support'
      ]
    }

    return plans[planId] || []
  }

  /**
   * Get subscription plan limits
   */
  const getPlanLimits = (planId) => {
    const limits = {
      basic: {
        vehicles: 1,
        diagnoses_per_month: 1,
        storage: '100MB',
        api_calls_per_day: 10
      },
      pro: {
        vehicles: 3,
        diagnoses_per_month: 3,
        storage: '1GB',
        api_calls_per_day: 50
      },
      elite: {
        vehicles: 'unlimited',
        diagnoses_per_month: 'unlimited',
        storage: '10GB',
        api_calls_per_day: 'unlimited'
      }
    }

    return limits[planId] || limits.basic
  }

  /**
   * Format subscription plan price
   */
  const formatPlanPrice = (price, currency = 'EUR') => {
    return new Intl.NumberFormat('sq-AL', {
      style: 'currency',
      currency: currency
    }).format(price)
  }

  /**
   * Get subscription status text
   */
  const getSubscriptionStatusText = (status) => {
    const statusTexts = {
      active: 'Aktiv',
      trial: 'Trial',
      cancelled: 'Anuluar',
      expired: 'Skaduar'
    }

    return statusTexts[status] || 'I panjohur'
  }

  /**
   * Get subscription status color
   */
  const getSubscriptionStatusColor = (status) => {
    const statusColors = {
      active: 'text-green-600',
      trial: 'text-blue-600',
      cancelled: 'text-red-600',
      expired: 'text-gray-600'
    }

    return statusColors[status] || 'text-gray-600'
  }

  return {
    // State
    loading,
    error,

    // Methods
    getPlans,
    getSubscriptionStatus,
    createSubscription,
    changePlan,
    cancelSubscription,
    checkUsageLimits,
    recordUsage,
    getBillingHistory,
    checkFeatureAccess,
    canPerformDiagnosis,
    canAddVehicle,

    // Utilities
    getPlanFeatures,
    getPlanLimits,
    formatPlanPrice,
    getSubscriptionStatusText,
    getSubscriptionStatusColor
  }
}







