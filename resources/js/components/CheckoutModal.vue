<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h2>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Content -->
          <div class="flex-1 overflow-y-auto p-6">
            <!-- Order Summary -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
              <div class="space-y-3">
                <div v-for="item in cart" :key="item.id" class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                  <img :src="item.image_url" :alt="item.name" class="w-12 h-12 object-cover rounded-lg">
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ item.name }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-xs">{{ item.brand }} - {{ item.part_number }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Qty: {{ item.quantity }}</p>
                    <p class="text-primary-600 dark:text-primary-400 font-bold">{{ item.formatted_price }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Customer Information -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                  <input 
                    v-model="customerInfo.firstName"
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    placeholder="Enter first name"
                    required
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                  <input 
                    v-model="customerInfo.lastName"
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    placeholder="Enter last name"
                    required
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                  <input 
                    v-model="customerInfo.email"
                    type="email" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    placeholder="Enter email address"
                    required
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                  <input 
                    v-model="customerInfo.phone"
                    type="tel" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    placeholder="Enter phone number"
                    required
                  >
                </div>
              </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipping Address</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                  <input 
                    v-model="shippingInfo.address"
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    placeholder="Enter street address"
                    required
                  >
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                    <input 
                      v-model="shippingInfo.city"
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                      placeholder="Enter city"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province *</label>
                    <input 
                      v-model="shippingInfo.state"
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                      placeholder="Enter state"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP/Postal Code *</label>
                    <input 
                      v-model="shippingInfo.zipCode"
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                      placeholder="Enter ZIP code"
                      required
                    >
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country *</label>
                  <select 
                    v-model="shippingInfo.country"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-700 dark:text-white"
                    required
                  >
                    <option value="">Select country</option>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="GB">United Kingdom</option>
                    <option value="DE">Germany</option>
                    <option value="FR">France</option>
                    <option value="IT">Italy</option>
                    <option value="ES">Spain</option>
                    <option value="AL">Albania</option>
                    <option value="XK">Kosovo</option>
                    <option value="MK">North Macedonia</option>
                    <option value="ME">Montenegro</option>
                    <option value="RS">Serbia</option>
                    <option value="BA">Bosnia and Herzegovina</option>
                    <option value="HR">Croatia</option>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Payment Method -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Method</h3>
              <div class="space-y-3">
                <div class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                  <input 
                    v-model="paymentMethod" 
                    type="radio" 
                    value="credit_card" 
                    id="credit_card"
                    class="w-4 h-4 text-primary-600 focus:ring-primary-500"
                  >
                  <label for="credit_card" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Credit Card</span>
                  </label>
                </div>
                <div class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                  <input 
                    v-model="paymentMethod" 
                    type="radio" 
                    value="paypal" 
                    id="paypal"
                    class="w-4 h-4 text-primary-600 focus:ring-primary-500"
                  >
                  <label for="paypal" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.105-.633c-.73-3.627-3.35-5.53-7.893-5.53H5.998c-.524 0-.968.382-1.05.9L2.47 20.597h4.606l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c4.297 0 7.664-1.747 8.647-6.797.03-.149.054-.294.077-.437.292-1.867-.002-3.137-1.012-4.287z"/>
                    </svg>
                    <span>PayPal</span>
                  </label>
                </div>
                <div class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                  <input 
                    v-model="paymentMethod" 
                    type="radio" 
                    value="bank_transfer" 
                    id="bank_transfer"
                    class="w-4 h-4 text-primary-600 focus:ring-primary-500"
                  >
                  <label for="bank_transfer" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Bank Transfer</span>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Order Total -->
            <div class="bg-gray-50 dark:bg-secondary-700 rounded-lg p-4">
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="text-gray-900 dark:text-white">${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 dark:text-gray-400">Shipping:</span>
                <span class="text-gray-900 dark:text-white">${{ shipping.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                <span class="text-gray-900 dark:text-white">${{ tax.toFixed(2) }}</span>
              </div>
              <div class="border-t border-gray-300 dark:border-gray-600 pt-2">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                  <span class="text-xl font-bold text-primary-600 dark:text-primary-400">${{ total.toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div class="border-t border-gray-200 dark:border-secondary-700 p-6">
            <div class="flex space-x-4">
              <button 
                @click="$emit('close')" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
              >
                Cancel
              </button>
              <button 
                @click="processOrder"
                :disabled="!isFormValid || isProcessing"
                class="flex-1 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
              >
                <svg v-if="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isProcessing ? 'Processing...' : 'Complete Order' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  cart: {
    type: Array,
    default: () => []
  },
  cartTotal: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['close', 'orderComplete'])

// Form data
const customerInfo = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: ''
})

const shippingInfo = ref({
  address: '',
  city: '',
  state: '',
  zipCode: '',
  country: ''
})

const paymentMethod = ref('credit_card')
const isProcessing = ref(false)

// Computed properties
const subtotal = computed(() => props.cartTotal)
const shipping = computed(() => {
  // Free shipping for orders over $100
  return subtotal.value > 100 ? 0 : 9.99
})
const tax = computed(() => {
  // 8% tax
  return subtotal.value * 0.08
})
const total = computed(() => {
  return subtotal.value + shipping.value + tax.value
})

const isFormValid = computed(() => {
  return customerInfo.value.firstName &&
         customerInfo.value.lastName &&
         customerInfo.value.email &&
         customerInfo.value.phone &&
         shippingInfo.value.address &&
         shippingInfo.value.city &&
         shippingInfo.value.state &&
         shippingInfo.value.zipCode &&
         shippingInfo.value.country &&
         paymentMethod.value
})

// Methods
const processOrder = async () => {
  if (!isFormValid.value) return
  
  isProcessing.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Create order object
    const order = {
      id: `ORD-${Date.now()}`,
      customer: customerInfo.value,
      shipping: shippingInfo.value,
      paymentMethod: paymentMethod.value,
      items: props.cart,
      subtotal: subtotal.value,
      shipping: shipping.value,
      tax: tax.value,
      total: total.value,
      status: 'confirmed',
      createdAt: new Date().toISOString()
    }
    
    // Emit order complete event
    emit('orderComplete', order)
    
    // Reset form
    resetForm()
    
  } catch (error) {
    console.error('Error processing order:', error)
    alert('Error processing order. Please try again.')
  } finally {
    isProcessing.value = false
  }
}

const resetForm = () => {
  customerInfo.value = {
    firstName: '',
    lastName: '',
    email: '',
    phone: ''
  }
  shippingInfo.value = {
    address: '',
    city: '',
    state: '',
    zipCode: '',
    country: ''
  }
  paymentMethod.value = 'credit_card'
}

// Reset form when modal closes
watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) {
    resetForm()
  }
})
</script>


