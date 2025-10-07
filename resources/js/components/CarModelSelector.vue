<template>
  <div class="space-y-4">
    <!-- Brand Selection -->
    <div class="space-y-2">
      <label 
        :for="brandId" 
        class="block text-sm font-semibold text-slate-700 dark:text-slate-300"
      >
        {{ brandLabel }} *
      </label>
      <div class="relative">
        <select 
          :id="brandId"
          v-model="selectedBrand"
          :disabled="disabled"
          :required="required"
          class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
          @change="onBrandChange"
        >
          <option value="">{{ brandPlaceholder }}</option>
          <option 
            v-for="brand in carBrands" 
            :key="brand.id" 
            :value="brand.name"
          >
            {{ brand.name }}
          </option>
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
          <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
      </div>
      <div v-if="!selectedBrand && showHelp" class="text-sm text-slate-500 dark:text-slate-400">
        {{ brandHelpText }}
      </div>
    </div>

    <!-- Model Selection -->
    <div class="space-y-2">
      <label 
        :for="modelId" 
        class="block text-sm font-semibold text-slate-700 dark:text-slate-300"
      >
        {{ modelLabel }} *
      </label>
      <div class="relative">
        <select 
          :id="modelId"
          v-model="selectedModel"
          :disabled="disabled || !selectedBrand || isLoadingModels"
          :required="required"
          class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
          @change="onModelChange"
        >
          <option value="">{{ modelPlaceholder }}</option>
          <option 
            v-for="model in carModels" 
            :key="model.id" 
            :value="model.name"
          >
            {{ model.name }}
          </option>
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
          <svg v-if="isLoadingModels" class="w-5 h-5 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <svg v-else class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
      </div>
      <div v-if="!selectedBrand && showHelp" class="text-sm text-slate-500 dark:text-slate-400">
        {{ modelHelpText }}
      </div>
      <div v-if="selectedBrand && carModels.length === 0 && !isLoadingModels" class="text-sm text-amber-600 dark:text-amber-400">
        No models found for {{ selectedBrand }}
      </div>
    </div>

    <!-- Year Selection (Optional) -->
    <div v-if="showYear" class="space-y-2">
      <label 
        :for="yearId" 
        class="block text-sm font-semibold text-slate-700 dark:text-slate-300"
      >
        {{ yearLabel }}
      </label>
      <input 
        :id="yearId"
        v-model="selectedYear"
        type="number"
        :min="minYear"
        :max="maxYear"
        :disabled="disabled"
        class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
        :placeholder="yearPlaceholder"
        @input="onYearChange"
      />
      <div v-if="showHelp" class="text-sm text-slate-500 dark:text-slate-400">
        {{ yearHelpText }}
      </div>
    </div>

    <!-- Selected Model Details (Optional) -->
    <div v-if="showDetails && selectedModelData" class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600">
      <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Model Details</h4>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div v-if="selectedModelData.generation">
          <span class="text-slate-500 dark:text-slate-400">Generation:</span>
          <span class="ml-2 text-slate-900 dark:text-slate-100">{{ selectedModelData.generation }}</span>
        </div>
        <div v-if="selectedModelData.body_type">
          <span class="text-slate-500 dark:text-slate-400">Body Type:</span>
          <span class="ml-2 text-slate-900 dark:text-slate-100">{{ selectedModelData.body_type }}</span>
        </div>
        <div v-if="selectedModelData.segment">
          <span class="text-slate-500 dark:text-slate-400">Segment:</span>
          <span class="ml-2 text-slate-900 dark:text-slate-100">{{ selectedModelData.segment }}</span>
        </div>
        <div v-if="selectedModelData.start_year">
          <span class="text-slate-500 dark:text-slate-400">Production Years:</span>
          <span class="ml-2 text-slate-900 dark:text-slate-100">
            {{ selectedModelData.start_year }}
            <span v-if="selectedModelData.end_year"> - {{ selectedModelData.end_year }}</span>
            <span v-else> - Present</span>
          </span>
        </div>
        <div v-if="selectedModelData.fuel_types && selectedModelData.fuel_types.length > 0">
          <span class="text-slate-500 dark:text-slate-400">Fuel Types:</span>
          <span class="ml-2 text-slate-900 dark:text-slate-100">{{ selectedModelData.fuel_types.join(', ') }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useCarModels } from '../composables/useCarModels'

// Props
const props = defineProps({
  // Values
  brand: {
    type: String,
    default: ''
  },
  model: {
    type: String,
    default: ''
  },
  year: {
    type: [String, Number],
    default: ''
  },
  
  // Configuration
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: true
  },
  showYear: {
    type: Boolean,
    default: false
  },
  showDetails: {
    type: Boolean,
    default: false
  },
  showHelp: {
    type: Boolean,
    default: true
  },
  popularBrandsOnly: {
    type: Boolean,
    default: false
  },
  
  // Labels
  brandLabel: {
    type: String,
    default: 'Brand'
  },
  modelLabel: {
    type: String,
    default: 'Model'
  },
  yearLabel: {
    type: String,
    default: 'Year'
  },
  
  // Placeholders
  brandPlaceholder: {
    type: String,
    default: 'Select Brand'
  },
  modelPlaceholder: {
    type: String,
    default: 'Select Model'
  },
  yearPlaceholder: {
    type: String,
    default: 'e.g., 2020'
  },
  
  // Help text
  brandHelpText: {
    type: String,
    default: 'Please select a brand first'
  },
  modelHelpText: {
    type: String,
    default: 'Please select a model'
  },
  yearHelpText: {
    type: String,
    default: 'Optional: Enter the year of manufacture'
  },
  
  // Year constraints
  minYear: {
    type: Number,
    default: 1900
  },
  maxYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  },
  
  // IDs for accessibility
  brandId: {
    type: String,
    default: 'car-brand'
  },
  modelId: {
    type: String,
    default: 'car-model'
  },
  yearId: {
    type: String,
    default: 'car-year'
  }
})

// Emits
const emit = defineEmits(['update:brand', 'update:model', 'update:year', 'change', 'brand-change', 'model-change', 'year-change'])

// Composables
const { 
  carBrands, 
  carModels, 
  isLoading: isLoadingModels,
  loadCarBrands, 
  loadCarModelsByBrandName,
  getModelByName 
} = useCarModels()

// Local state
const selectedBrand = ref(props.brand)
const selectedModel = ref(props.model)
const selectedYear = ref(props.year)

// Computed
const selectedModelData = computed(() => {
  if (!selectedModel.value) return null
  return getModelByName(selectedModel.value)
})

// Methods
const onBrandChange = async () => {
  selectedModel.value = ''
  selectedYear.value = ''
  
  emit('update:brand', selectedBrand.value)
  emit('brand-change', selectedBrand.value)
  emit('change', {
    brand: selectedBrand.value,
    model: selectedModel.value,
    year: selectedYear.value
  })
  
  if (selectedBrand.value) {
    await loadCarModelsByBrandName(selectedBrand.value)
  }
}

const onModelChange = () => {
  emit('update:model', selectedModel.value)
  emit('model-change', selectedModel.value)
  emit('change', {
    brand: selectedBrand.value,
    model: selectedModel.value,
    year: selectedYear.value
  })
}

const onYearChange = () => {
  emit('update:year', selectedYear.value)
  emit('year-change', selectedYear.value)
  emit('change', {
    brand: selectedBrand.value,
    model: selectedModel.value,
    year: selectedYear.value
  })
}

// Watchers
watch(() => props.brand, (newValue) => {
  selectedBrand.value = newValue
})

watch(() => props.model, (newValue) => {
  selectedModel.value = newValue
})

watch(() => props.year, (newValue) => {
  selectedYear.value = newValue
})

// Lifecycle
onMounted(async () => {
  await loadCarBrands(props.popularBrandsOnly)
  
  // If brand is already selected, load its models
  if (selectedBrand.value) {
    await loadCarModelsByBrandName(selectedBrand.value)
  }
})
</script>

<style scoped>
/* Custom select styling */
select {
  background-image: none;
}

select:focus {
  outline: none;
}

/* Loading animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
