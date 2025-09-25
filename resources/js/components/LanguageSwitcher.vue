<template>
  <div class="relative">
    <!-- Language Button -->
    <button
      @click="toggleDropdown"
      class="flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
    >
      <span class="text-lg">{{ currentLanguageData.flag }}</span>
      <span class="text-sm font-medium">{{ currentLanguageData.native_name }}</span>
      <svg 
        class="w-4 h-4 transition-transform duration-200"
        :class="{ 'rotate-180': showDropdown }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="showDropdown"
      class="absolute top-full right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50"
    >
      <button
        v-for="language in availableLanguages"
        :key="language.code"
        @click="selectLanguage(language.code)"
        class="w-full flex items-center space-x-3 px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
        :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': currentLanguage === language.code }"
      >
        <span class="text-lg">{{ language.flag }}</span>
        <div class="flex-1">
          <div class="font-medium">{{ language.native_name }}</div>
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ language.name }}</div>
        </div>
        <svg
          v-if="currentLanguage === language.code"
          class="w-4 h-4 text-blue-600 dark:text-blue-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </button>
    </div>

    <!-- Backdrop -->
    <div
      v-if="showDropdown"
      @click="closeDropdown"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { setLanguage, getCurrentLanguage, getAvailableLanguages } from '../utils/translations'

export default {
  name: 'LanguageSwitcher',
  setup() {
    const currentLanguage = ref(getCurrentLanguage())
    const showDropdown = ref(false)
    const availableLanguages = getAvailableLanguages()

    const currentLanguageData = computed(() => {
      return availableLanguages.find(lang => lang.code === currentLanguage.value) || availableLanguages[0]
    })

    const toggleDropdown = () => {
      showDropdown.value = !showDropdown.value
    }

    const closeDropdown = () => {
      showDropdown.value = false
    }

    const selectLanguage = (languageCode) => {
      if (languageCode !== currentLanguage.value) {
        const success = setLanguage(languageCode)
        if (success) {
          currentLanguage.value = languageCode
          // Reload page to apply translations
          window.location.reload()
        }
      }
      closeDropdown()
    }

    // Close dropdown on escape key
    const handleEscape = (event) => {
      if (event.key === 'Escape') {
        closeDropdown()
      }
    }

    onMounted(() => {
      document.addEventListener('keydown', handleEscape)
    })

    return {
      currentLanguage,
      currentLanguageData,
      availableLanguages,
      showDropdown,
      toggleDropdown,
      closeDropdown,
      selectLanguage
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar for dropdown */
.dropdown-scroll::-webkit-scrollbar {
  width: 4px;
}

.dropdown-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.dropdown-scroll::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}

.dropdown-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}
</style>
