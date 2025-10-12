<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <!-- Header -->
    <div class="text-center mb-12 px-4">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Plan</h1>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Select the perfect CarWise.ai subscription plan for your needs
      </p>
    </div>

    <!-- Authentication Warning -->
    <div v-if="!isAuthenticated" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
              Duhet të jeni të loguar
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>Për të blerë një abonim, ju lutemi <router-link to="/login" class="font-medium underline">bëni login</router-link> ose <router-link to="/register" class="font-medium underline">regjistrohuni</router-link>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Plans Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Free Plan -->
        <div class="bg-white rounded-2xl shadow-lg p-8 relative">
          <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plans.free.name }}</h3>
            <p class="text-gray-600 mb-6">{{ plans.free.description }}</p>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">€{{ plans.free.price }}</span>
              <span class="text-gray-600">/month</span>
            </div>
            <button 
              :class="plans.free.buttonClass"
              class="w-full py-3 px-6 rounded-lg font-semibold transition-colors"
              disabled
            >
              {{ plans.free.buttonText }}
            </button>
          </div>
          <div class="mt-8">
            <h4 class="font-semibold text-gray-900 mb-4">Features:</h4>
            <ul class="space-y-2">
              <li v-for="feature in plans.free.features" :key="feature" class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ feature }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Basic Plan -->
        <div class="bg-white rounded-2xl shadow-lg p-8 relative">
          <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plans.basic.name }}</h3>
            <p class="text-gray-600 mb-6">{{ plans.basic.description }}</p>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">€{{ plans.basic.price }}</span>
              <span class="text-gray-600">/month</span>
            </div>
            <button 
              @click="selectPlan('basic')"
              :disabled="loading && selectedPlan === 'basic'"
              :class="plans.basic.buttonClass"
              class="w-full py-3 px-6 rounded-lg font-semibold transition-colors"
            >
              <span v-if="loading && selectedPlan === 'basic'">Loading...</span>
              <span v-else>{{ plans.basic.buttonText }}</span>
            </button>
          </div>
          <div class="mt-8">
            <h4 class="font-semibold text-gray-900 mb-4">Features:</h4>
            <ul class="space-y-2">
              <li v-for="feature in plans.basic.features" :key="feature" class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ feature }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Pro Plan (Popular) -->
        <div class="bg-white rounded-2xl shadow-lg p-8 relative border-2 border-blue-500">
          <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-semibold">Most Popular</span>
          </div>
          <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plans.pro.name }}</h3>
            <p class="text-gray-600 mb-6">{{ plans.pro.description }}</p>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">€{{ plans.pro.price }}</span>
              <span class="text-gray-600">/month</span>
            </div>
            <button 
              @click="selectPlan('pro')"
              :disabled="loading && selectedPlan === 'pro'"
              :class="plans.pro.buttonClass"
              class="w-full py-3 px-6 rounded-lg font-semibold transition-colors"
            >
              <span v-if="loading && selectedPlan === 'pro'">Loading...</span>
              <span v-else>{{ plans.pro.buttonText }}</span>
            </button>
          </div>
          <div class="mt-8">
            <h4 class="font-semibold text-gray-900 mb-4">Features:</h4>
            <ul class="space-y-2">
              <li v-for="feature in plans.pro.features" :key="feature" class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ feature }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Elite Plan -->
        <div class="bg-white rounded-2xl shadow-lg p-8 relative">
          <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plans.elite.name }}</h3>
            <p class="text-gray-600 mb-6">{{ plans.elite.description }}</p>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">€{{ plans.elite.price }}</span>
              <span class="text-gray-600">/month</span>
            </div>
            <button 
              @click="selectPlan('elite')"
              :disabled="loading && selectedPlan === 'elite'"
              :class="plans.elite.buttonClass"
              class="w-full py-3 px-6 rounded-lg font-semibold transition-colors"
            >
              <span v-if="loading && selectedPlan === 'elite'">Loading...</span>
              <span v-else>{{ plans.elite.buttonText }}</span>
            </button>
          </div>
          <div class="mt-8">
            <h4 class="font-semibold text-gray-900 mb-4">Features:</h4>
            <ul class="space-y-2">
              <li v-for="feature in plans.elite.features" :key="feature" class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ feature }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-md mx-4">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Subscription Created!</h3>
          <p class="text-gray-600 mb-6">Your {{ selectedPlanName }} subscription has been activated successfully.</p>
          <button 
            @click="goToDashboard"
            class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-600 transition-colors"
          >
            Go to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useSubscription } from '@/composables/useSubscription'
import { useAnalytics } from '@/composables/useAnalytics'

const router = useRouter()
const { isAuthenticated } = useAuth()
const { createSubscription } = useSubscription()
const { trackEvent } = useAnalytics()

// Reactive data
const loading = ref(false)
const selectedPlan = ref(null)
const showSuccessModal = ref(false)

// Subscription plans data
const plans = ref({
  free: {
    id: 'free',
    name: 'Free Plan',
    price: 0,
    description: 'Perfect for trying out CarWise.ai',
    features: [
      'Basic car diagnosis',
      'Limited AI suggestions',
      'Community support',
      'Basic car information'
    ],
    limitations: [
      'No BMW API access',
      'Limited diagnosis history',
      'No priority support'
    ],
    popular: false,
    buttonText: 'Current Plan',
    buttonClass: 'bg-gray-500 text-white cursor-not-allowed'
  },
  basic: {
    id: 'basic',
    name: 'Basic Plan',
    price: 4.99,
    description: 'Great for occasional car owners',
    features: [
      '1 AI diagnosis per month',
      'Basic BMW API access',
      'Email support',
      'Diagnosis history (30 days)',
      'Basic car maintenance tips'
    ],
    limitations: [
      'Limited to 1 diagnosis per month',
      'No service recommendations',
      'No priority support'
    ],
    popular: false,
    buttonText: 'Choose Basic',
    buttonClass: 'bg-blue-500 hover:bg-blue-600 text-white'
  },
  pro: {
    id: 'pro',
    name: 'Pro Plan',
    price: 9.99,
    description: 'Perfect for car enthusiasts',
    features: [
      '3 AI diagnoses per month',
      'Full BMW API access',
      'Priority email support',
      'Diagnosis history (1 year)',
      'Advanced maintenance recommendations',
      'Service partner recommendations',
      'Technical reports'
    ],
    limitations: [
      'Limited to 3 diagnoses per month',
      'No phone support'
    ],
    popular: true,
    buttonText: 'Choose Pro',
    buttonClass: 'bg-blue-500 hover:bg-blue-600 text-white'
  },
  elite: {
    id: 'elite',
    name: 'Elite Plan',
    price: 19.99,
    description: 'For serious car owners and professionals',
    features: [
      'Unlimited AI diagnoses',
      'Full BMW API access',
      'Priority phone & email support',
      'Unlimited diagnosis history',
      'Advanced maintenance recommendations',
      'Service partner network access',
      'Detailed technical reports',
      'Fleet management tools',
      'API access for developers'
    ],
    limitations: [],
    popular: false,
    buttonText: 'Choose Elite',
    buttonClass: 'bg-blue-500 hover:bg-blue-600 text-white'
  }
})

// Computed properties
const selectedPlanName = computed(() => {
  return selectedPlan.value ? plans.value[selectedPlan.value].name : ''
})

const selectPlan = async (planId) => {
  if (!isAuthenticated.value) {
    router.push('/login')
    return
  }

  try {
    loading.value = true
    selectedPlan.value = planId

    // Track plan selection
    trackEvent('subscription_plan_selected', {
      plan_id: planId,
      plan_name: plans.value[planId].name,
      plan_price: plans.value[planId].price
    })

    // Create subscription
    const result = await createSubscription(planId)

    if (result.success) {
      // Track successful subscription
      trackEvent('subscription_created', {
        plan_id: planId,
        plan_name: plans.value[planId].name,
        plan_price: plans.value[planId].price
      })

      showSuccessModal.value = true
    } else {
      throw new Error(result.error || 'Failed to create subscription')
    }
  } catch (error) {
    console.error('Subscription creation failed:', error)
    alert('Gabim në krijimin e abonimit: ' + error.message)
  } finally {
    loading.value = false
    selectedPlan.value = null
  }
}

const goToDashboard = () => {
  showSuccessModal.value = false
  router.push('/subscription/dashboard')
}
</script>

<style scoped>
.plan-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.plan-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.features-list li {
  text-align: left;
}
</style>

