<template>
  <form 
    class="mobile-form space-y-6"
    :class="formClasses"
    @submit.prevent="handleSubmit"
  >
    <!-- Form Header -->
    <div v-if="title || subtitle" class="form-header">
      <h2 v-if="title" class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ title }}</h2>
      <p v-if="subtitle" class="text-gray-600 dark:text-gray-400">{{ subtitle }}</p>
    </div>

    <!-- Form Fields -->
    <div class="form-fields space-y-4">
      <slot></slot>
    </div>

    <!-- Form Actions -->
    <div v-if="showActions" class="form-actions pt-4">
      <div class="flex flex-col space-y-3">
        <!-- Primary Action -->
        <TouchButton
          type="submit"
          :variant="primaryAction.variant || 'primary'"
          :size="isMobileDevice ? 'lg' : 'md'"
          :loading="loading"
          :disabled="disabled || !isValid"
          :haptic-feedback="true"
          class="w-full"
        >
          {{ primaryAction.label || 'Submit' }}
        </TouchButton>

        <!-- Secondary Actions -->
        <div v-if="secondaryActions.length > 0" class="flex space-x-3">
          <TouchButton
            v-for="action in secondaryActions"
            :key="action.label"
            :variant="action.variant || 'outline'"
            :size="isMobileDevice ? 'lg' : 'md'"
            :disabled="action.disabled"
            @click="action.handler"
            class="flex-1"
          >
            {{ action.label }}
          </TouchButton>
        </div>
      </div>
    </div>

    <!-- Form Footer -->
    <div v-if="$slots.footer" class="form-footer pt-4 border-t border-gray-200 dark:border-gray-700">
      <slot name="footer"></slot>
    </div>
  </form>
</template>

<script setup>
import { computed } from 'vue'
import { useMobileLayout } from '../composables/useMobileLayout'
import TouchButton from './TouchButton.vue'

const props = defineProps({
  // Content
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  
  // Actions
  primaryAction: {
    type: Object,
    default: () => ({ label: 'Submit', variant: 'primary' })
  },
  secondaryActions: {
    type: Array,
    default: () => []
  },
  showActions: {
    type: Boolean,
    default: true
  },
  
  // State
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  isValid: {
    type: Boolean,
    default: true
  },
  
  // Layout
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'card', 'modal'].includes(value)
  }
})

const emit = defineEmits(['submit'])

// Mobile layout
const { isMobileDevice, getContainerPadding } = useMobileLayout()

// Computed properties
const formClasses = computed(() => {
  const classes = []
  
  // Base classes
  classes.push('w-full', 'max-w-md', 'mx-auto')
  
  // Variant classes
  switch (props.variant) {
    case 'card':
      classes.push('bg-white', 'dark:bg-gray-800', 'rounded-lg', 'shadow-lg', 'p-6', 'border', 'border-gray-200', 'dark:border-gray-700')
      break
    case 'modal':
      classes.push('bg-white', 'dark:bg-gray-800', 'p-6')
      break
    default:
      classes.push(getContainerPadding())
  }
  
  // Mobile-specific classes
  if (isMobileDevice.value) {
    classes.push('touch-manipulation')
  }
  
  return classes.join(' ')
})

// Form submission
const handleSubmit = () => {
  if (props.disabled || props.loading || !props.isValid) return
  
  // Haptic feedback
  if (navigator.vibrate) {
    navigator.vibrate(100)
  }
  
  emit('submit')
}
</script>

<style scoped>
/* Touch manipulation for better mobile performance */
.touch-manipulation {
  touch-action: manipulation;
}

/* Form spacing adjustments for mobile */
@media (max-width: 640px) {
  .mobile-form {
    padding: 1rem;
  }
  
  .form-fields {
    gap: 1rem;
  }
  
  .form-actions {
    padding-top: 1.5rem;
  }
}

/* Ensure proper form field spacing */
.form-fields > * + * {
  margin-top: 1rem;
}

/* Form header styling */
.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

@media (max-width: 640px) {
  .form-header {
    margin-bottom: 1.5rem;
  }
}
</style>

