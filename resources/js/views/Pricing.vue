<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <div v-if="route.query.action === 'upgrade'" class="mb-6">
            <button @click="goBackToDashboard" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Dashboard
            </button>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-4">
            Pay once for a diagnosis
          </h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Choose a one-time service. You pay only when you need it. No subscription and no recurring charge.
          </p>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Trust Indicators -->
      <div class="text-center mb-12">
        <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-600">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>Pay once</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>No subscription</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>No setup fees</span>
          </div>
        </div>
      </div>

      <!-- Pricing Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 max-w-5xl mx-auto">
        <!-- Basic Plan -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden hover:shadow-xl transition-all duration-300"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'basic' }">
          <div class="p-8">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Quick check</h3>
              <p class="text-gray-600 mb-4">A first look, with no charge</p>
              <div v-if="route.query.current_plan === 'basic'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-gray-600 to-gray-800 bg-clip-text text-transparent">€0</span>
                <span class="text-gray-600"> one-time</span>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                1 guided symptom check
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Access to car articles library
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Email notifications
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Basic support
              </li>
            </ul>
            
            <button 
              @click="selectPlan('basic')"
              class="w-full bg-gradient-to-r from-gray-500 to-gray-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-gray-600 hover:to-gray-800 transition-all duration-300 transform hover:scale-105"
            >
              Get Started Free
            </button>
          </div>
        </div>

        <!-- Complete service -->
        <div class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border-2 border-blue-500 overflow-hidden hover:shadow-xl transition-all duration-300 relative"
             :class="{ 'ring-2 ring-blue-500': route.query.current_plan === 'care-plus' }">
          <!-- Popular Badge -->
          <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-lg">
              Full service
            </span>
          </div>
          
          <div class="p-8 pt-12">
            <div class="text-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Complete check</h3>
              <p class="text-gray-600 mb-4">Diagnosis, written report, and preventive guidance</p>
              <div v-if="route.query.current_plan === 'care-plus'" class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Current Plan
                </span>
              </div>
              <div class="mb-4">
                <span class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">€{{ prices.complete }}</span>
                <span class="text-gray-600"> one-time</span>
              </div>
            </div>
            
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                AI diagnosis and repair recommendations
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Written report
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Preventive guidance
              </li>
              <li class="flex items-center text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Saved with your vehicle history
              </li>
            </ul>
            
            <button 
              @click="selectPlan('care-plus')"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
            >
              Pay once
            </button>
          </div>
        </div>

      </div>

      <div class="max-w-5xl mx-auto mb-16 bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Expert session</h3>
          <p class="text-gray-600 text-sm mt-1">A separate one-time conversation about a result you already have. It is not included in the complete check.</p>
        </div>
        <div class="flex items-center gap-4 shrink-0">
          <span class="text-2xl font-bold text-gray-900">€{{ prices.expert }}</span>
          <button
            @click="selectPlan('pro-garage')"
            class="bg-gradient-to-r from-purple-500 to-pink-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-700 transition-all duration-300"
          >
            Pay once
          </button>
        </div>
      </div>

      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-200/50">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Frequently Asked Questions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Do I pay every month?</h4>
            <p class="text-gray-600 text-sm">No. Every service on this page is a single payment.</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Is there a free option?</h4>
            <p class="text-gray-600 text-sm">Yes. The quick check is free.</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">What does the complete check include?</h4>
            <p class="text-gray-600 text-sm">One AI diagnosis, a written report, and preventive guidance, paid once.</p>
          </div>
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Is the expert session included?</h4>
            <p class="text-gray-600 text-sm">No. It is a separate one-time payment if you want to talk through a result.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Modal -->
    <div v-if="showCheckoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Complete Your Purchase</h3>
            <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Selected Plan -->
          <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <h4 class="font-semibold text-gray-900">{{ selectedPlan?.name }}</h4>
            <p class="text-2xl font-bold text-blue-600">€{{ finalPrice }}</p>
            <p class="text-sm text-gray-600">One-time payment · {{ selectedPlan?.description }}</p>
          </div>

          <!-- Coupon Code -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code (Optional)</label>
            <div class="flex">
              <input 
                v-model="couponCode"
                type="text" 
                placeholder="Enter coupon code"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <button 
                @click="applyCoupon"
                class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors"
              >
                Apply
              </button>
            </div>
            <p v-if="couponMessage" class="text-sm mt-2" :class="couponValid ? 'text-green-600' : 'text-red-600'">
              {{ couponMessage }}
            </p>
          </div>

          <!-- Payment Method -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="stripe" class="mr-3">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.274 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.573-2.354 1.573-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                  </svg>
                  Credit Card (Stripe)
                </div>
              </label>
              <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="paypal" class="mr-3">
                <div class="flex items-center">
                  <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.105-.633c-.365-1.882-1.46-3.2-3.338-3.2H9.95c-.524 0-.968.382-1.05.9L7.78 19.337h4.696l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c3.298 0 6.664-1.747 8.647-6.797.03-.149.054-.294.077-.437z"/>
                  </svg>
                  PayPal
                </div>
              </label>
            </div>
          </div>

          <!-- Total -->
          <div class="border-t pt-4 mb-6">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-900">Total</span>
              <span class="text-2xl font-bold text-blue-600">€{{ finalPrice }}</span>
            </div>
          </div>

          <!-- Checkout Button -->
          <button 
            @click="processCheckout"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Complete Purchase</span>
          </button>

          <!-- Trial Notice -->
          <p v-if="selectedPlan && selectedPlan.price === 0" class="text-sm text-gray-600 text-center mt-4">
            This check is free. No card is charged.
          </p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import api from '../services/api'

const router = useRouter()
const route = useRoute()
const { isAuthenticated } = useAuth()

const showCheckoutModal = ref(false)
const selectedPlan = ref(null)
const couponCode = ref('')
const couponValid = ref(false)
const couponMessage = ref('')
const paymentMethod = ref('stripe')
const loading = ref(false)

const prices = ref({
  diagnosis: '10.39',
  report: '19.49',
  predictive: '25.99',
  expert: '3.89',
  complete: '55.86',
})

const plans = computed(() => [
  {
    id: 'basic',
    name: 'Quick check',
    price: 0,
    description: 'A first look, with no charge',
  },
  {
    id: 'smart-ai',
    name: 'AI Diagnosis',
    price: Number(prices.value.diagnosis),
    description: 'One detailed diagnosis',
  },
  {
    id: 'care-plus',
    name: 'Complete check',
    price: Number(prices.value.complete),
    description: 'Diagnosis, written report, and preventive guidance',
  },
  {
    id: 'pro-garage',
    name: 'Expert session',
    price: Number(prices.value.expert),
    description: 'One live conversation about your result',
  },
])

const finalPrice = computed(() => {
  if (!selectedPlan.value) return '0.00'

  let price = selectedPlan.value.price

  if (couponValid.value && couponCode.value) {
    if (couponCode.value.includes('%')) {
      const discount = parseInt(couponCode.value.replace('%', ''))
      price = price * (1 - discount / 100)
    } else if (couponCode.value.startsWith('FIRST')) {
      price = Math.max(0, price - 10)
    }
  }

  return price.toFixed(2)
})

// Methods
const selectPlan = (planId) => {
  if (!isAuthenticated.value) {
    router.push('/login')
    return
  }
  
  selectedPlan.value = plans.value.find(p => p.id === planId)
  showCheckoutModal.value = true
  couponCode.value = ''
  couponValid.value = false
  couponMessage.value = ''
}

const applyCoupon = () => {
  if (!couponCode.value) {
    couponMessage.value = 'Please enter a coupon code'
    couponValid.value = false
    return
  }
  
  // Simulate coupon validation
  const validCoupons = ['20%OFF', 'FIRST10', 'SAVE15']
  
  if (validCoupons.includes(couponCode.value.toUpperCase())) {
    couponValid.value = true
    couponMessage.value = 'Coupon applied successfully!'
  } else {
    couponValid.value = false
    couponMessage.value = 'Invalid coupon code'
  }
}

const processCheckout = async () => {
  if (!selectedPlan.value) return

  loading.value = true

  try {
    await new Promise(resolve => setTimeout(resolve, 1200))
    alert(`Payment recorded. One-time charge of €${finalPrice.value}.`)
    showCheckoutModal.value = false
    router.push('/dashboard')
  } catch (error) {
    console.error('Checkout failed:', error)
    alert('Checkout failed: ' + error.message)
  } finally {
    loading.value = false
  }
}

const goBackToDashboard = () => {
  router.push('/dashboard')
}

onMounted(async () => {
  document.title = 'Pricing - CarWise.ai'

  try {
    const response = await api.get('/customer-services')
    const services = response.data?.services
    if (response.data?.success && services?.complete) {
      prices.value = {
        diagnosis: services.diagnosis,
        report: services.report,
        predictive: services.predictive,
        expert: services.expert,
        complete: services.complete,
      }
    }
  } catch (error) {
    console.error('Failed to load service prices:', error)
  }
})
</script>

<style scoped>
/* Custom styles for the pricing page */
</style>
