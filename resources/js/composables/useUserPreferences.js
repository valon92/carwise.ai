import { ref, computed, readonly } from 'vue'
import { useAuth } from './useAuth'

// Global preferences state
const preferences = ref({})
const isLoading = ref(false)
const cartPreferences = ref({})

export function useUserPreferences() {
  const { user, isAuthenticated } = useAuth()

  // Default cart preferences
  const defaultCartPreferences = {
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

  // Load all user preferences
  const loadPreferences = async () => {
    if (!isAuthenticated.value) return

    try {
      isLoading.value = true
      const response = await fetch('/api/preferences', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          preferences.value = data.data.reduce((acc, pref) => {
            acc[pref.key] = pref.value
            return acc
          }, {})
        }
      }
    } catch (error) {
      console.error('Error loading preferences:', error)
    } finally {
      isLoading.value = false
    }
  }

  // Load cart-specific preferences
  const loadCartPreferences = async () => {
    if (!isAuthenticated.value) {
      // Use default preferences for guests
      cartPreferences.value = { ...defaultCartPreferences }
      return
    }

    try {
      isLoading.value = true
      const response = await fetch('/api/preferences/cart/all', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          cartPreferences.value = { ...defaultCartPreferences, ...data.data }
        }
      }
    } catch (error) {
      console.error('Error loading cart preferences:', error)
      cartPreferences.value = { ...defaultCartPreferences }
    } finally {
      isLoading.value = false
    }
  }

  // Set a preference
  const setPreference = async (key, value, type = 'string', description = null) => {
    if (!isAuthenticated.value) return false

    try {
      const response = await fetch('/api/preferences', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          key,
          value,
          type,
          description
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          preferences.value[key] = value
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error setting preference:', error)
      return false
    }
  }

  // Set cart preferences
  const setCartPreferences = async (newPreferences) => {
    if (!isAuthenticated.value) {
      // For guests, just update local state
      cartPreferences.value = { ...cartPreferences.value, ...newPreferences }
      return true
    }

    try {
      const response = await fetch('/api/preferences/cart/set', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          preferences: newPreferences
        })
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          cartPreferences.value = { ...cartPreferences.value, ...newPreferences }
          return true
        }
      }
      return false
    } catch (error) {
      console.error('Error setting cart preferences:', error)
      return false
    }
  }

  // Get a preference value
  const getPreference = (key, defaultValue = null) => {
    return preferences.value[key] ?? defaultValue
  }

  // Get a cart preference value
  const getCartPreference = (key, defaultValue = null) => {
    return cartPreferences.value[key] ?? defaultValue
  }

  // Update a cart preference
  const updateCartPreference = async (key, value) => {
    const newPreferences = { [key]: value }
    return await setCartPreferences(newPreferences)
  }

  // Reset cart preferences to defaults
  const resetCartPreferences = async () => {
    return await setCartPreferences(defaultCartPreferences)
  }

  // Computed properties for common cart preferences
  const autoSave = computed(() => getCartPreference('cart_auto_save', true))
  const notifications = computed(() => getCartPreference('cart_notifications', true))
  const currency = computed(() => getCartPreference('cart_currency', 'USD'))
  const shippingPreference = computed(() => getCartPreference('cart_shipping_preference', 'standard'))
  const paymentPreference = computed(() => getCartPreference('cart_payment_preference', 'credit_card'))
  const quantityLimit = computed(() => getCartPreference('cart_quantity_limit', 10))
  const showTax = computed(() => getCartPreference('cart_show_tax', true))
  const showShipping = computed(() => getCartPreference('cart_show_shipping', true))
  const rememberAddress = computed(() => getCartPreference('cart_remember_address', true))
  const autoApplyCoupons = computed(() => getCartPreference('cart_auto_apply_coupons', false))

  return {
    // State
    preferences: readonly(preferences),
    cartPreferences: readonly(cartPreferences),
    isLoading: readonly(isLoading),
    
    // Actions
    loadPreferences,
    loadCartPreferences,
    setPreference,
    setCartPreferences,
    updateCartPreference,
    resetCartPreferences,
    
    // Getters
    getPreference,
    getCartPreference,
    
    // Computed cart preferences
    autoSave,
    notifications,
    currency,
    shippingPreference,
    paymentPreference,
    quantityLimit,
    showTax,
    showShipping,
    rememberAddress,
    autoApplyCoupons
  }
}

// Initialize cart preferences (call this from components when needed)
export const initializeCartPreferences = () => {
  try {
    const { loadCartPreferences } = useUserPreferences()
    loadCartPreferences()
  } catch (error) {
    console.log('Cart preferences initialization skipped:', error.message)
  }
}

