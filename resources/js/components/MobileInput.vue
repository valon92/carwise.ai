<template>
  <div class="mobile-input-group" :class="groupClasses">
    <!-- Label -->
    <label 
      v-if="label"
      :for="inputId"
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
      :class="{ 'text-red-600 dark:text-red-400': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative" :class="containerClasses">
      <!-- Leading Icon -->
      <div 
        v-if="leadingIcon"
        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
      >
        <component :is="leadingIcon" class="h-5 w-5 text-gray-400" />
      </div>

      <!-- Input Element -->
      <component
        :is="inputComponent"
        :id="inputId"
        ref="inputRef"
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :autocomplete="autocomplete"
        :inputmode="inputMode"
        :pattern="pattern"
        :min="min"
        :max="max"
        :step="step"
        :rows="rows"
        :cols="cols"
        class="mobile-input"
        :class="inputClasses"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeyDown"
      />

      <!-- Trailing Elements -->
      <div 
        v-if="trailingIcon || showClear || showPassword"
        class="absolute inset-y-0 right-0 flex items-center pr-3 space-x-2"
      >
        <!-- Clear Button -->
        <TouchButton
          v-if="showClear && modelValue"
          variant="ghost"
          size="xs"
          @click="clearInput"
          class="p-1"
          title="Clear"
        >
          <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </TouchButton>

        <!-- Password Toggle -->
        <TouchButton
          v-if="showPassword"
          variant="ghost"
          size="xs"
          @click="togglePasswordVisibility"
          class="p-1"
          :title="showPasswordText ? 'Hide password' : 'Show password'"
        >
          <svg v-if="showPasswordText" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
          </svg>
          <svg v-else class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
        </TouchButton>

        <!-- Trailing Icon -->
        <component 
          v-if="trailingIcon" 
          :is="trailingIcon" 
          class="h-5 w-5 text-gray-400" 
        />
      </div>

      <!-- Loading Indicator -->
      <div 
        v-if="loading"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>

    <!-- Helper Text / Error Message -->
    <div v-if="helperText || errorMessage" class="mt-2">
      <p 
        class="text-sm"
        :class="hasError ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
      >
        {{ hasError ? errorMessage : helperText }}
      </p>
    </div>

    <!-- Character Count -->
    <div v-if="showCharCount && maxLength" class="mt-1 text-right">
      <span 
        class="text-xs"
        :class="isNearLimit ? 'text-orange-500' : 'text-gray-400'"
      >
        {{ characterCount }}/{{ maxLength }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useMobileLayout } from '../composables/useMobileLayout'
import TouchButton from './TouchButton.vue'

const props = defineProps({
  // v-model
  modelValue: {
    type: [String, Number],
    default: ''
  },
  
  // Input attributes
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  helperText: {
    type: String,
    default: ''
  },
  errorMessage: {
    type: String,
    default: ''
  },
  
  // Validation
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  
  // Input behavior
  autocomplete: {
    type: String,
    default: ''
  },
  inputMode: {
    type: String,
    default: ''
  },
  pattern: {
    type: String,
    default: ''
  },
  
  // Number inputs
  min: {
    type: [String, Number],
    default: undefined
  },
  max: {
    type: [String, Number],
    default: undefined
  },
  step: {
    type: [String, Number],
    default: undefined
  },
  
  // Textarea
  rows: {
    type: Number,
    default: 3
  },
  cols: {
    type: Number,
    default: undefined
  },
  
  // Character limit
  maxLength: {
    type: Number,
    default: undefined
  },
  showCharCount: {
    type: Boolean,
    default: false
  },
  
  // Icons and actions
  leadingIcon: {
    type: [String, Object],
    default: null
  },
  trailingIcon: {
    type: [String, Object],
    default: null
  },
  showClear: {
    type: Boolean,
    default: false
  },
  showPassword: {
    type: Boolean,
    default: false
  },
  
  // State
  loading: {
    type: Boolean,
    default: false
  },
  
  // Appearance
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'filled', 'outlined'].includes(value)
  }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'clear', 'enter'])

// Mobile layout
const { isMobileDevice } = useMobileLayout()

// Component state
const inputRef = ref(null)
const isFocused = ref(false)
const showPasswordText = ref(false)
const inputId = `mobile-input-${Math.random().toString(36).substr(2, 9)}`

// Computed properties
const inputComponent = computed(() => {
  return props.type === 'textarea' ? 'textarea' : 'input'
})

const inputType = computed(() => {
  if (props.type === 'password' && showPasswordText.value) {
    return 'text'
  }
  return props.type === 'textarea' ? undefined : props.type
})

const hasError = computed(() => {
  return !!props.errorMessage
})

const characterCount = computed(() => {
  return String(props.modelValue || '').length
})

const isNearLimit = computed(() => {
  if (!props.maxLength) return false
  return characterCount.value >= props.maxLength * 0.8
})

const groupClasses = computed(() => {
  const classes = ['w-full']
  
  if (isMobileDevice.value) {
    classes.push('touch-manipulation')
  }
  
  return classes.join(' ')
})

const containerClasses = computed(() => {
  const classes = []
  
  if (props.loading) {
    classes.push('pr-10')
  }
  
  return classes.join(' ')
})

const inputClasses = computed(() => {
  const classes = [
    'block',
    'w-full',
    'border',
    'rounded-lg',
    'shadow-sm',
    'transition-colors',
    'duration-200',
    'focus:ring-2',
    'focus:ring-offset-0',
    'focus:outline-none'
  ]
  
  // Size classes
  const sizeClasses = {
    sm: isMobileDevice.value ? 'px-3 py-3 text-base' : 'px-3 py-2 text-sm',
    md: isMobileDevice.value ? 'px-4 py-3 text-base' : 'px-3 py-2 text-base',
    lg: isMobileDevice.value ? 'px-4 py-4 text-lg' : 'px-4 py-3 text-lg'
  }
  classes.push(sizeClasses[props.size])
  
  // Mobile-specific minimum height for better touch targets
  if (isMobileDevice.value) {
    classes.push('min-h-[44px]')
  }
  
  // Icon padding
  if (props.leadingIcon) {
    classes.push('pl-10')
  }
  if (props.trailingIcon || props.showClear || props.showPassword || props.loading) {
    classes.push('pr-10')
  }
  
  // Variant classes
  switch (props.variant) {
    case 'filled':
      classes.push(
        'bg-gray-100',
        'dark:bg-gray-700',
        'border-transparent',
        'focus:bg-white',
        'dark:focus:bg-gray-800',
        'focus:border-primary-500',
        'focus:ring-primary-500'
      )
      break
    case 'outlined':
      classes.push(
        'bg-transparent',
        'border-2',
        'border-gray-300',
        'dark:border-gray-600',
        'focus:border-primary-500',
        'focus:ring-primary-500'
      )
      break
    default:
      classes.push(
        'bg-white',
        'dark:bg-gray-800',
        'border-gray-300',
        'dark:border-gray-600',
        'focus:border-primary-500',
        'focus:ring-primary-500'
      )
  }
  
  // State classes
  if (hasError.value) {
    classes.push(
      'border-red-300',
      'dark:border-red-600',
      'focus:border-red-500',
      'focus:ring-red-500'
    )
  }
  
  if (props.disabled) {
    classes.push(
      'bg-gray-50',
      'dark:bg-gray-900',
      'text-gray-500',
      'dark:text-gray-400',
      'cursor-not-allowed'
    )
  }
  
  // Text color
  classes.push('text-gray-900', 'dark:text-white')
  
  // Placeholder color
  classes.push('placeholder-gray-500', 'dark:placeholder-gray-400')
  
  return classes.join(' ')
})

// Event handlers
const handleInput = (event) => {
  let value = event.target.value
  
  // Enforce max length
  if (props.maxLength && value.length > props.maxLength) {
    value = value.slice(0, props.maxLength)
    event.target.value = value
  }
  
  emit('update:modelValue', value)
}

const handleFocus = (event) => {
  isFocused.value = true
  emit('focus', event)
}

const handleBlur = (event) => {
  isFocused.value = false
  emit('blur', event)
}

const handleKeyDown = (event) => {
  if (event.key === 'Enter' && props.type !== 'textarea') {
    emit('enter', event)
  }
}

const clearInput = () => {
  emit('update:modelValue', '')
  emit('clear')
  
  // Focus input after clearing
  nextTick(() => {
    inputRef.value?.focus()
  })
}

const togglePasswordVisibility = () => {
  showPasswordText.value = !showPasswordText.value
}

// Focus method for parent components
const focus = () => {
  inputRef.value?.focus()
}

const blur = () => {
  inputRef.value?.blur()
}

// Expose methods
defineExpose({
  focus,
  blur
})
</script>

<style scoped>
/* Touch manipulation for better mobile performance */
.touch-manipulation {
  touch-action: manipulation;
}

/* Ensure proper input sizing on mobile */
@media (max-width: 640px) {
  .mobile-input {
    font-size: 16px; /* Prevents zoom on iOS */
    -webkit-appearance: none;
    appearance: none;
  }
  
  /* Textarea specific adjustments */
  .mobile-input[type="textarea"] {
    resize: vertical;
    min-height: 80px;
  }
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Focus ring adjustments for mobile */
.mobile-input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Disabled state */
.mobile-input:disabled {
  opacity: 0.6;
}

/* Remove default input styling on iOS */
.mobile-input {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Number input adjustments */
.mobile-input[type="number"] {
  -moz-appearance: textfield;
}

.mobile-input[type="number"]::-webkit-outer-spin-button,
.mobile-input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>

