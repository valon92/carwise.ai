<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-secondary-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cart Preferences</h2>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Content -->
          <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-6">
              <!-- Auto Save -->
              <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white">Auto Save Cart</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Automatically save cart items to your account</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input 
                    v-model="localPreferences.cart_auto_save" 
                    type="checkbox" 
                    class="sr-only peer"
                  >
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                </label>
              </div>

              <!-- Notifications -->
              <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white">Cart Notifications</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Show notifications when items are added to cart</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input 
                    v-model="localPreferences.cart_notifications" 
                    type="checkbox" 
                    class="sr-only peer"
                  >
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                </label>
              </div>

              <!-- Currency -->
              <div class="p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Preferred Currency</h3>
                <select 
                  v-model="localPreferences.cart_currency"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-600 dark:text-white"
                >
                  <option value="USD">USD - US Dollar</option>
                  <option value="EUR">EUR - Euro</option>
                  <option value="GBP">GBP - British Pound</option>
                  <option value="ALL">ALL - Albanian Lek</option>
                  <option value="CHF">CHF - Swiss Franc</option>
                </select>
              </div>

              <!-- Shipping Preference -->
              <div class="p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Default Shipping</h3>
                <select 
                  v-model="localPreferences.cart_shipping_preference"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-600 dark:text-white"
                >
                  <option value="standard">Standard (5-7 days)</option>
                  <option value="express">Express (2-3 days)</option>
                  <option value="overnight">Overnight (1 day)</option>
                </select>
              </div>

              <!-- Payment Preference -->
              <div class="p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Default Payment Method</h3>
                <select 
                  v-model="localPreferences.cart_payment_preference"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-600 dark:text-white"
                >
                  <option value="credit_card">Credit Card</option>
                  <option value="paypal">PayPal</option>
                  <option value="bank_transfer">Bank Transfer</option>
                </select>
              </div>

              <!-- Quantity Limit -->
              <div class="p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Maximum Quantity per Item</h3>
                <input 
                  v-model.number="localPreferences.cart_quantity_limit"
                  type="number" 
                  min="1" 
                  max="100"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-secondary-600 dark:text-white"
                >
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Maximum number of the same item that can be added to cart</p>
              </div>

              <!-- Display Options -->
              <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Display Options</h3>
                
                <!-- Show Tax -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Show Tax in Cart</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Display tax information in cart totals</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                      v-model="localPreferences.cart_show_tax" 
                      type="checkbox" 
                      class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                  </label>
                </div>

                <!-- Show Shipping -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Show Shipping in Cart</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Display shipping information in cart totals</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                      v-model="localPreferences.cart_show_shipping" 
                      type="checkbox" 
                      class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                  </label>
                </div>

                <!-- Remember Address -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Remember Address</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Remember shipping address for future orders</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                      v-model="localPreferences.cart_remember_address" 
                      type="checkbox" 
                      class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                  </label>
                </div>

                <!-- Auto Apply Coupons -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-secondary-700 rounded-lg">
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Auto Apply Coupons</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Automatically apply available coupons to cart</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                      v-model="localPreferences.cart_auto_apply_coupons" 
                      type="checkbox" 
                      class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div class="border-t border-gray-200 dark:border-secondary-700 p-6">
            <div class="flex space-x-4">
              <button 
                @click="resetToDefaults" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
              >
                Reset to Defaults
              </button>
              <button 
                @click="savePreferences"
                :disabled="isSaving"
                class="flex-1 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
              >
                <svg v-if="isSaving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSaving ? 'Saving...' : 'Save Preferences' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  preferences: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'save'])

// Local preferences state
const localPreferences = ref({})
const isSaving = ref(false)

// Default preferences
const defaultPreferences = {
  cart_auto_save: true,
  cart_notifications: true,
  cart_currency: 'USD',
  cart_shipping_preference: 'standard',
  cart_payment_preference: 'credit_card',
  cart_quantity_limit: 10,
  cart_show_tax: true,
  cart_show_shipping: true,
  cart_remember_address: true,
  cart_auto_apply_coupons: false
}

// Initialize local preferences
const initializePreferences = () => {
  localPreferences.value = { ...defaultPreferences, ...props.preferences }
}

// Watch for prop changes
watch(() => props.preferences, () => {
  initializePreferences()
}, { deep: true, immediate: true })

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    initializePreferences()
  }
})

// Save preferences
const savePreferences = async () => {
  isSaving.value = true
  
  try {
    emit('save', localPreferences.value)
    emit('close')
  } catch (error) {
    console.error('Error saving preferences:', error)
  } finally {
    isSaving.value = false
  }
}

// Reset to defaults
const resetToDefaults = () => {
  localPreferences.value = { ...defaultPreferences }
}
</script>


