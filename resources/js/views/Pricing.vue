<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
      <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="text-center">
          <div v-if="route.query.action === 'upgrade'" class="mb-6">
            <button
              type="button"
              @click="goBackToDashboard"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900"
            >
              ← Back to account
            </button>
          </div>
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">CarWise.ai pricing</p>
          <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900">
            Pay when a full diagnosis is useful
          </h1>
          <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
            Preview is free. Unlocking opens steps, costs, AI problem visuals, and a dated PDF evidence report.
            No monthly subscription required.
          </p>
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="mb-12 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-600">
        <span class="inline-flex items-center gap-2">
          <span class="h-1.5 w-1.5 rounded-full bg-slate-900"></span>
          Free preview
        </span>
        <span class="inline-flex items-center gap-2">
          <span class="h-1.5 w-1.5 rounded-full bg-slate-900"></span>
          Pay once to unlock
        </span>
        <span class="inline-flex items-center gap-2">
          <span class="h-1.5 w-1.5 rounded-full bg-slate-900"></span>
          PDF + visuals included
        </span>
      </div>

      <div class="mx-auto mb-10 grid max-w-5xl grid-cols-1 gap-8 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
          <h3 class="text-2xl font-bold text-slate-900">Quick preview</h3>
          <p class="mt-2 text-slate-600">See the likely problem title and match score</p>
          <div class="mt-6">
            <span class="text-4xl font-bold text-slate-900">€0</span>
            <span class="text-slate-500"> · free</span>
          </div>
          <ul class="mt-8 space-y-3 text-sm text-slate-600">
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Guided symptom check</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Problem title + confidence</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Decide before you pay</li>
          </ul>
          <button
            type="button"
            @click="goDiagnose"
            class="mt-8 w-full rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white hover:bg-slate-800"
          >
            Start free preview
          </button>
        </div>

        <div class="relative rounded-2xl border-2 border-slate-900 bg-white p-8 shadow-sm">
          <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-slate-900 px-4 py-1 text-xs font-semibold text-white">
            Recommended
          </span>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Full diagnosis unlock</h3>
          <p class="mt-2 text-slate-600">What to do, why, costs, AI visual, downloadable PDF</p>
          <div class="mt-6">
            <span class="text-4xl font-bold text-slate-900">Usage-based</span>
          </div>
          <p class="mt-2 text-sm text-slate-500">
            Charged from the real AI analysis for that session (token/media usage × platform margin).
            Shown before you confirm Continue.
          </p>
          <ul class="mt-8 space-y-3 text-sm text-slate-600">
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Recommended actions & urgency</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Likely causes with probabilities</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Cost guide for workshops</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Realistic AI photos of the fault area on your car</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> PDF evidence (date, time, report ID)</li>
            <li class="flex gap-3"><span class="text-slate-900">✓</span> Saved with vehicle history</li>
          </ul>
          <button
            type="button"
            @click="goDiagnose"
            class="mt-8 w-full rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white hover:bg-slate-800"
          >
            Go to Diagnose
          </button>
        </div>
      </div>

      <div class="mx-auto mb-16 max-w-5xl">
        <h3 class="mb-4 text-lg font-semibold text-slate-900">Optional add-ons</h3>
        <p class="mb-6 text-sm text-slate-500">
          Separate one-time services if you want packaged extras beyond a single unlock. Unlock on Diagnose already includes the PDF and visual for that result.
        </p>
        <div class="grid gap-4 sm:grid-cols-3">
          <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h4 class="font-semibold text-slate-900">Written report pack</h4>
            <p class="mt-1 text-sm text-slate-500">Extended written packaging of a diagnosis</p>
            <p class="mt-4 text-2xl font-bold text-slate-900">€{{ prices.report }}</p>
            <button type="button" @click="selectPlan('smart-ai')" class="mt-4 text-sm font-semibold text-slate-900 underline">
              Pay once
            </button>
          </div>
          <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h4 class="font-semibold text-slate-900">Complete check pack</h4>
            <p class="mt-1 text-sm text-slate-500">Bundled diagnosis + report + preventive guidance</p>
            <p class="mt-4 text-2xl font-bold text-slate-900">€{{ prices.complete }}</p>
            <button type="button" @click="selectPlan('care-plus')" class="mt-4 text-sm font-semibold text-slate-900 underline">
              Pay once
            </button>
          </div>
          <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h4 class="font-semibold text-slate-900">Expert session</h4>
            <p class="mt-1 text-sm text-slate-500">Talk through a result you already have</p>
            <p class="mt-4 text-2xl font-bold text-slate-900">€{{ prices.expert }}</p>
            <button type="button" @click="selectPlan('pro-garage')" class="mt-4 text-sm font-semibold text-slate-900 underline">
              Pay once
            </button>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h3 class="mb-6 text-center text-2xl font-bold text-slate-900">Frequently asked questions</h3>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Do I pay every month?</h4>
            <p class="text-sm text-slate-600">No. Diagnosis unlocks and add-ons are one-time. There is no required subscription.</p>
          </div>
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Why is unlock “usage-based”?</h4>
            <p class="text-sm text-slate-600">
              The total follows the AI work for that session (model tokens and any media analysis), with a fixed platform margin. You see the amount before Continue.
            </p>
          </div>
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">What’s in the PDF?</h4>
            <p class="text-sm text-slate-600">
              Vehicle details, symptoms, full result, problem visual when available, and evidence fields: diagnosis time, unlock time, session ID, and report ID.
            </p>
          </div>
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Are add-ons required?</h4>
            <p class="text-sm text-slate-600">No. Most owners only unlock the diagnosis on the Diagnose page.</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCheckoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white shadow-2xl">
        <div class="p-6">
          <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900">Complete your purchase</h3>
            <button type="button" @click="showCheckoutModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
          </div>
          <div class="mb-6 rounded-xl bg-slate-50 p-4">
            <h4 class="font-semibold text-slate-900">{{ selectedPlan?.name }}</h4>
            <p class="text-2xl font-bold text-slate-900">€{{ finalPrice }}</p>
            <p class="text-sm text-slate-600">One-time · {{ selectedPlan?.description }}</p>
          </div>
          <div class="mb-6">
            <label class="mb-2 block text-sm font-medium text-slate-700">Coupon (optional)</label>
            <div class="flex">
              <input
                v-model="couponCode"
                type="text"
                placeholder="Enter coupon code"
                class="flex-1 rounded-l-lg border border-slate-300 px-4 py-2 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
              >
              <button type="button" @click="applyCoupon" class="rounded-r-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-800">
                Apply
              </button>
            </div>
            <p v-if="couponMessage" class="mt-2 text-sm" :class="couponValid ? 'text-emerald-600' : 'text-red-600'">
              {{ couponMessage }}
            </p>
          </div>
          <div class="mb-6">
            <label class="mb-2 block text-sm font-medium text-slate-700">Payment method</label>
            <div class="space-y-2">
              <label class="flex cursor-pointer items-center rounded-lg border border-slate-300 p-3 hover:bg-slate-50">
                <input v-model="paymentMethod" type="radio" value="stripe" class="mr-3">
                Credit Card (Stripe)
              </label>
              <label class="flex cursor-pointer items-center rounded-lg border border-slate-300 p-3 hover:bg-slate-50">
                <input v-model="paymentMethod" type="radio" value="paypal" class="mr-3">
                PayPal
              </label>
            </div>
          </div>
          <div class="mb-6 flex items-center justify-between border-t border-slate-200 pt-4">
            <span class="font-semibold text-slate-900">Total</span>
            <span class="text-2xl font-bold text-slate-900">€{{ finalPrice }}</span>
          </div>
          <button
            type="button"
            @click="processCheckout"
            :disabled="loading"
            class="w-full rounded-xl bg-slate-900 py-3 font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
          >
            {{ loading ? 'Processing…' : 'Complete purchase' }}
          </button>
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
    name: 'Quick preview',
    price: 0,
    description: 'Free problem preview',
  },
  {
    id: 'smart-ai',
    name: 'Written report pack',
    price: Number(prices.value.report),
    description: 'Extended written packaging',
  },
  {
    id: 'care-plus',
    name: 'Complete check pack',
    price: Number(prices.value.complete),
    description: 'Bundled diagnosis extras',
  },
  {
    id: 'pro-garage',
    name: 'Expert session',
    price: Number(prices.value.expert),
    description: 'One conversation about your result',
  },
])

const finalPrice = computed(() => {
  if (!selectedPlan.value) return '0.00'
  let price = selectedPlan.value.price
  if (couponValid.value && couponCode.value) {
    if (couponCode.value.includes('%')) {
      const discount = parseInt(couponCode.value.replace('%', ''), 10)
      price = price * (1 - discount / 100)
    } else if (couponCode.value.startsWith('FIRST')) {
      price = Math.max(0, price - 10)
    }
  }
  return price.toFixed(2)
})

const goDiagnose = () => {
  router.push('/diagnose')
}

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
    router.push('/diagnose')
  } catch (error) {
    alert('Checkout failed: ' + error.message)
  } finally {
    loading.value = false
  }
}

const goBackToDashboard = () => {
  router.push('/subscription/dashboard')
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
