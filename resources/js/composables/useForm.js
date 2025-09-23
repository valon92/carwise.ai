import { ref, reactive, computed } from 'vue'

export function useForm(initialData = {}, validationRules = {}) {
  const form = reactive({ ...initialData })
  const errors = ref({})
  const isSubmitting = ref(false)
  const isDirty = ref(false)

  // Validation rules
  const rules = reactive(validationRules)

  // Computed properties
  const isValid = computed(() => {
    return Object.keys(errors.value).length === 0 && 
           Object.values(form).some(value => value !== '' && value !== null && value !== undefined)
  })

  const hasErrors = computed(() => Object.keys(errors.value).length > 0)

  // Validation functions
  const validateField = (fieldName, value) => {
    const rule = rules[fieldName]
    if (!rule) return true

    // Required validation
    if (rule.required && (!value || value.toString().trim() === '')) {
      errors.value[fieldName] = `${fieldName} is required`
      return false
    }

    // Email validation
    if (rule.email && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(value)) {
        errors.value[fieldName] = 'Please enter a valid email address'
        return false
      }
    }

    // Min length validation
    if (rule.minLength && value && value.length < rule.minLength) {
      errors.value[fieldName] = `${fieldName} must be at least ${rule.minLength} characters`
      return false
    }

    // Max length validation
    if (rule.maxLength && value && value.length > rule.maxLength) {
      errors.value[fieldName] = `${fieldName} must be no more than ${rule.maxLength} characters`
      return false
    }

    // Pattern validation
    if (rule.pattern && value && !rule.pattern.test(value)) {
      errors.value[fieldName] = rule.message || `${fieldName} format is invalid`
      return false
    }

    // Custom validation
    if (rule.validator && typeof rule.validator === 'function') {
      const result = rule.validator(value, form)
      if (result !== true) {
        errors.value[fieldName] = result
        return false
      }
    }

    // Clear error if validation passes
    delete errors.value[fieldName]
    return true
  }

  const validateForm = () => {
    errors.value = {}
    let isValid = true

    Object.keys(rules).forEach(fieldName => {
      if (!validateField(fieldName, form[fieldName])) {
        isValid = false
      }
    })

    return isValid
  }

  const validateFieldOnBlur = (fieldName) => {
    validateField(fieldName, form[fieldName])
  }

  // Form actions
  const resetForm = () => {
    Object.keys(initialData).forEach(key => {
      form[key] = initialData[key]
    })
    errors.value = {}
    isDirty.value = false
  }

  const setFieldValue = (fieldName, value) => {
    form[fieldName] = value
    isDirty.value = true
    // Clear error when user starts typing
    if (errors.value[fieldName]) {
      delete errors.value[fieldName]
    }
  }

  const setFieldError = (fieldName, message) => {
    errors.value[fieldName] = message
  }

  const clearFieldError = (fieldName) => {
    delete errors.value[fieldName]
  }

  const clearAllErrors = () => {
    errors.value = {}
  }

  const getFieldError = (fieldName) => {
    return errors.value[fieldName] || ''
  }

  const hasFieldError = (fieldName) => {
    return !!errors.value[fieldName]
  }

  // Submit handler
  const handleSubmit = async (submitFn) => {
    if (!validateForm()) {
      return { success: false, errors: errors.value }
    }

    try {
      isSubmitting.value = true
      const result = await submitFn(form)
      return result
    } catch (error) {
      console.error('Form submission error:', error)
      return { 
        success: false, 
        message: error.message || 'An error occurred while submitting the form' 
      }
    } finally {
      isSubmitting.value = false
    }
  }

  // Watch for changes to mark form as dirty
  const markDirty = () => {
    isDirty.value = true
  }

  return {
    // State
    form,
    errors: readonly(errors),
    isSubmitting: readonly(isSubmitting),
    isDirty: readonly(isDirty),
    
    // Computed
    isValid,
    hasErrors,
    
    // Validation
    validateField,
    validateForm,
    validateFieldOnBlur,
    
    // Form actions
    resetForm,
    setFieldValue,
    setFieldError,
    clearFieldError,
    clearAllErrors,
    getFieldError,
    hasFieldError,
    handleSubmit,
    markDirty
  }
}

// Common validation rules
export const commonValidationRules = {
  email: {
    required: true,
    email: true,
    message: 'Please enter a valid email address'
  },
  password: {
    required: true,
    minLength: 8,
    message: 'Password must be at least 8 characters'
  },
  phone: {
    pattern: /^[\+]?[1-9][\d]{0,15}$/,
    message: 'Please enter a valid phone number'
  },
  required: {
    required: true
  }
}
