import { ref, computed, watch, readonly } from 'vue'
import axios from 'axios'

// Global language state
const currentLanguage = ref('en')
const translations = ref({})
const availableLanguages = ref({})
const isLoading = ref(false)

export function useLanguage() {
    // Load available languages
    const loadLanguages = async () => {
        try {
            const response = await axios.get('/api/languages')
            if (response.data.success) {
                availableLanguages.value = response.data.data.languages
                currentLanguage.value = response.data.data.current.code
                // Load translations after getting current language
                await loadTranslations()
            }
        } catch (error) {
            console.error('Error loading languages:', error)
        }
    }

    // Load translations for current language
    const loadTranslations = async (language = null) => {
        try {
            isLoading.value = true
            const lang = language || currentLanguage.value
            const response = await axios.get(`/api/languages/translations?locale=${lang}`)
            
            if (response.data.success) {
                translations.value = response.data.data.translations
                currentLanguage.value = response.data.data.language
                console.log('Translations loaded for', lang, ':', translations.value)
            }
        } catch (error) {
            console.error('Error loading translations:', error)
        } finally {
            isLoading.value = false
        }
    }

    // Set language
    const setLanguage = async (languageCode) => {
        try {
            const response = await axios.post('/api/languages/set', {
                language: languageCode
            })
            
            if (response.data.success) {
                currentLanguage.value = languageCode
                await loadTranslations(languageCode)
                
                // Update document language
                document.documentElement.lang = languageCode
                
                // Update page title if needed
                updatePageTitle()
                
                return true
            }
        } catch (error) {
            console.error('Error setting language:', error)
        }
        return false
    }

    // Detect language from browser
    const detectLanguage = async () => {
        try {
            const response = await axios.get('/api/languages/detect')
            if (response.data.success) {
                currentLanguage.value = response.data.data.code
                await loadTranslations(response.data.data.code)
                return response.data.data
            }
        } catch (error) {
            console.error('Error detecting language:', error)
        }
        return null
    }

    // Get translation
    const t = (key, params = {}) => {
        const keys = key.split('.')
        let value = translations.value
        
        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k]
            } else {
                return key // Return key if translation not found
            }
        }
        
        // Replace parameters
        if (typeof value === 'string' && Object.keys(params).length > 0) {
            Object.keys(params).forEach(param => {
                value = value.replace(`:${param}`, params[param])
            })
        }
        
        return value
    }

    // Get current language info
    const getCurrentLanguage = computed(() => {
        return availableLanguages.value[currentLanguage.value] || availableLanguages.value['en']
    })

    // Get available languages list
    const getAvailableLanguages = computed(() => {
        return Object.values(availableLanguages.value)
    })

    // Check if language is RTL
    const isRTL = computed(() => {
        const rtlLanguages = ['ar', 'he', 'fa', 'ur']
        return rtlLanguages.includes(currentLanguage.value)
    })

    // Get text direction
    const direction = computed(() => {
        return isRTL.value ? 'rtl' : 'ltr'
    })

    // Update page title based on current language
    const updatePageTitle = () => {
        const title = t('home.title')
        if (title && title !== 'home.title') {
            document.title = `${title} - CarWise.ai`
        }
    }

    // Initialize language system
    const initialize = async () => {
        await loadLanguages()
        
        // Check if language is stored in localStorage
        const storedLanguage = localStorage.getItem('language')
        if (storedLanguage && availableLanguages.value[storedLanguage]) {
            await setLanguage(storedLanguage)
        } else {
            // Detect language from browser
            await detectLanguage()
        }
        
        // Store language in localStorage
        localStorage.setItem('language', currentLanguage.value)
    }

    // Watch for language changes
    watch(currentLanguage, (newLang) => {
        localStorage.setItem('language', newLang)
        document.documentElement.lang = newLang
        document.documentElement.dir = direction.value
    })

    return {
        // State
        currentLanguage: readonly(currentLanguage),
        translations: readonly(translations),
        availableLanguages: readonly(availableLanguages),
        isLoading: readonly(isLoading),
        
        // Computed
        getCurrentLanguage,
        getAvailableLanguages,
        isRTL,
        direction,
        
        // Methods
        loadLanguages,
        loadTranslations,
        setLanguage,
        detectLanguage,
        t,
        initialize,
        updatePageTitle
    }
}

// Global language instance
let languageInstance = null

export function getLanguageInstance() {
    if (!languageInstance) {
        languageInstance = useLanguage()
        // Initialize language on first use
        languageInstance.loadLanguages()
    }
    return languageInstance
}

// Global translation function
export function $t(key, params = {}) {
    const instance = getLanguageInstance()
    return instance.t(key, params)
}
