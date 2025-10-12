import { ref, onMounted } from 'vue'

// Global analytics state
const analyticsEnabled = ref(false)
const clientId = ref(null)

export function useAnalytics() {
    // Initialize analytics
    const initAnalytics = () => {
        if (typeof window !== 'undefined' && window.gtag) {
            analyticsEnabled.value = true
            console.log('📊 Google Analytics initialized')
        }
    }

    // Track page view
    const trackPageView = (pageName, additionalData = {}) => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('event', 'page_view', {
            page_title: pageName,
            page_location: window.location.href,
            page_path: window.location.pathname,
            ...additionalData
        })
        
        console.log('📊 Page view tracked:', pageName)
    }

    // Track custom event
    const trackEvent = (eventName, parameters = {}) => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('event', eventName, {
            event_category: parameters.category || 'general',
            event_label: parameters.label || '',
            value: parameters.value || 0,
            ...parameters
        })
        
        console.log('📊 Event tracked:', eventName, parameters)
    }

    // Track user registration
    const trackUserRegistration = (userData = {}) => {
        trackEvent('user_registration', {
            category: 'user',
            label: userData.role || 'customer',
            user_type: userData.role || 'customer',
            registration_method: userData.method || 'email',
            country: userData.country || 'unknown'
        })
    }

    // Track car diagnosis
    const trackDiagnosis = (diagnosisData = {}) => {
        trackEvent('car_diagnosis', {
            category: 'diagnosis',
            label: `${diagnosisData.brand || 'unknown'} ${diagnosisData.model || 'unknown'}`,
            car_brand: diagnosisData.brand || 'unknown',
            car_model: diagnosisData.model || 'unknown',
            car_year: diagnosisData.year || 'unknown',
            diagnosis_type: diagnosisData.type || 'ai',
            severity: diagnosisData.severity || 'unknown',
            confidence_score: diagnosisData.confidence || 0,
            value: diagnosisData.value || 0
        })
    }

    // Track car part search
    const trackPartSearch = (searchData = {}) => {
        trackEvent('part_search', {
            category: 'search',
            label: searchData.term || '',
            search_term: searchData.term || '',
            car_brand: searchData.brand || 'unknown',
            car_model: searchData.model || 'unknown',
            part_category: searchData.category || 'unknown',
            results_count: searchData.results_count || 0
        })
    }

    // Track car addition
    const trackCarAdded = (carData = {}) => {
        trackEvent('car_added', {
            category: 'car_management',
            label: `${carData.brand || 'unknown'} ${carData.model || 'unknown'}`,
            car_brand: carData.brand || 'unknown',
            car_model: carData.model || 'unknown',
            car_year: carData.year || 'unknown',
            fuel_type: carData.fuel_type || 'unknown',
            engine_type: carData.engine_type || 'unknown'
        })
    }

    // Track mechanic interaction
    const trackMechanicInteraction = (interactionData = {}) => {
        trackEvent('mechanic_interaction', {
            category: 'mechanic',
            label: interactionData.type || 'unknown',
            interaction_type: interactionData.type || 'unknown',
            mechanic_id: interactionData.mechanic_id || 'unknown',
            location: interactionData.location || 'unknown',
            rating: interactionData.rating || 0
        })
    }

    // Track conversion
    const trackConversion = (conversionType, conversionData = {}) => {
        trackEvent('conversion', {
            category: 'conversion',
            label: conversionType,
            conversion_type: conversionType,
            value: conversionData.value || 0,
            currency: conversionData.currency || 'USD',
            ...conversionData
        })
    }

    // Track user engagement
    const trackEngagement = (engagementType, engagementData = {}) => {
        trackEvent('user_engagement', {
            category: 'engagement',
            label: engagementType,
            engagement_type: engagementType,
            session_duration: engagementData.duration || 0,
            interactions: engagementData.interactions || 0,
            ...engagementData
        })
    }

    // Track timing
    const trackTiming = (timingName, timingValue, timingCategory = 'performance') => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('event', 'timing_complete', {
            name: timingName,
            value: timingValue,
            event_category: timingCategory
        })
        
        console.log('📊 Timing tracked:', timingName, timingValue)
    }

    // Track exception/error
    const trackException = (error, fatal = false) => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('event', 'exception', {
            description: error.message || error,
            fatal: fatal
        })
        
        console.log('📊 Exception tracked:', error)
    }

    // Track scroll depth
    const trackScrollDepth = (depth) => {
        trackEvent('scroll_depth', {
            category: 'engagement',
            label: `${depth}%`,
            scroll_depth: depth
        })
    }

    // Track form interaction
    const trackFormInteraction = (formName, action, fieldName = '') => {
        trackEvent('form_interaction', {
            category: 'form',
            label: `${formName} - ${action}`,
            form_name: formName,
            form_action: action,
            field_name: fieldName
        })
    }

    // Track search
    const trackSearch = (searchTerm, resultsCount = 0) => {
        trackEvent('search', {
            category: 'search',
            label: searchTerm,
            search_term: searchTerm,
            results_count: resultsCount
        })
    }

    // Track video interaction
    const trackVideoInteraction = (videoTitle, action, progress = 0) => {
        trackEvent('video_interaction', {
            category: 'video',
            label: videoTitle,
            video_title: videoTitle,
            video_action: action,
            video_progress: progress
        })
    }

    // Track file download
    const trackDownload = (fileName, fileType = '') => {
        trackEvent('file_download', {
            category: 'download',
            label: fileName,
            file_name: fileName,
            file_type: fileType
        })
    }

    // Track social interaction
    const trackSocialInteraction = (platform, action, target = '') => {
        trackEvent('social_interaction', {
            category: 'social',
            label: `${platform} - ${action}`,
            social_platform: platform,
            social_action: action,
            social_target: target
        })
    }

    // Set user properties
    const setUserProperties = (properties = {}) => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('config', window.gtag.config?.measurement_id, {
            user_properties: properties
        })
        
        console.log('📊 User properties set:', properties)
    }

    // Set user ID
    const setUserId = (userId) => {
        if (!analyticsEnabled.value || !window.gtag) return

        window.gtag('config', window.gtag.config?.measurement_id, {
            user_id: userId
        })
        
        console.log('📊 User ID set:', userId)
    }

    // Initialize on mount
    onMounted(() => {
        initAnalytics()
    })

    return {
        analyticsEnabled,
        initAnalytics,
        trackPageView,
        trackEvent,
        trackUserRegistration,
        trackDiagnosis,
        trackPartSearch,
        trackCarAdded,
        trackMechanicInteraction,
        trackConversion,
        trackEngagement,
        trackTiming,
        trackException,
        trackScrollDepth,
        trackFormInteraction,
        trackSearch,
        trackVideoInteraction,
        trackDownload,
        trackSocialInteraction,
        setUserProperties,
        setUserId
    }
}








