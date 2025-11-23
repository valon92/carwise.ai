import { ref, onMounted } from 'vue'

// Global New Relic state
const newRelicEnabled = ref(false)
const newRelicInitialized = ref(false)

export function useNewRelic() {
    // Initialize New Relic
    const initNewRelic = () => {
        if (typeof window !== 'undefined' && window.newrelic) {
            newRelicEnabled.value = true
            newRelicInitialized.value = true
            
            // Set application name
            window.newrelic.setApplicationID('CarWise.ai')
            
            console.log('📊 New Relic initialized successfully')
        }
    }

    // Track custom event
    const trackCustomEvent = (eventType, attributes = {}) => {
        if (!newRelicEnabled.value || !window.newrelic) return

        window.newrelic.addToTrace({
            name: eventType,
            start: Date.now(),
            end: Date.now(),
            ...attributes
        })
        
        console.log('📊 New Relic event tracked:', eventType, attributes)
    }

    // Track user registration
    const trackUserRegistration = (userData = {}) => {
        trackCustomEvent('UserRegistration', {
            user_id: userData.id,
            user_type: userData.role || 'customer',
            registration_method: userData.method || 'email',
            country: userData.country || 'unknown'
        })
    }

    // Track user login
    const trackUserLogin = (userData = {}) => {
        trackCustomEvent('UserLogin', {
            user_id: userData.id,
            user_type: userData.role || 'customer',
            login_method: userData.method || 'email'
        })
    }

    // Track car diagnosis
    const trackCarDiagnosis = (diagnosisData = {}) => {
        trackCustomEvent('CarDiagnosis', {
            user_id: diagnosisData.user_id,
            car_brand: diagnosisData.brand || 'unknown',
            car_model: diagnosisData.model || 'unknown',
            car_year: diagnosisData.year || 'unknown',
            diagnosis_type: diagnosisData.type || 'ai',
            severity: diagnosisData.severity || 'unknown',
            confidence_score: diagnosisData.confidence || 0,
            ai_provider: diagnosisData.ai_provider || 'unknown',
            processing_time: diagnosisData.processing_time || 0
        })
    }

    // Track car addition
    const trackCarAdded = (carData = {}) => {
        trackCustomEvent('CarAdded', {
            user_id: carData.user_id,
            car_brand: carData.brand || 'unknown',
            car_model: carData.model || 'unknown',
            car_year: carData.year || 'unknown',
            fuel_type: carData.fuel_type || 'unknown',
            engine_type: carData.engine_type || 'unknown'
        })
    }

    // Track part search
    const trackPartSearch = (searchData = {}) => {
        trackCustomEvent('PartSearch', {
            user_id: searchData.user_id,
            search_term: searchData.term || '',
            car_brand: searchData.brand || 'unknown',
            car_model: searchData.model || 'unknown',
            part_category: searchData.category || 'unknown',
            results_count: searchData.results_count || 0,
            search_duration: searchData.duration || 0
        })
    }

    // Track page view
    const trackPageView = (pageName, context = {}) => {
        trackCustomEvent('PageView', {
            page_name: pageName,
            page_url: context.url || window.location.href,
            user_id: context.user_id,
            session_id: context.session_id,
            load_time: context.load_time || 0,
            referrer: context.referrer || document.referrer
        })
    }

    // Track conversion
    const trackConversion = (conversionType, conversionData = {}) => {
        trackCustomEvent('Conversion', {
            conversion_type: conversionType,
            user_id: conversionData.user_id,
            value: conversionData.value || 0,
            currency: conversionData.currency || 'USD',
            funnel_step: conversionData.funnel_step || 'unknown',
            conversion_source: conversionData.source || 'unknown'
        })
    }

    // Track API call
    const trackAPICall = (endpoint, method, statusCode, duration, context = {}) => {
        trackCustomEvent('APICall', {
            endpoint: endpoint,
            method: method,
            status_code: statusCode,
            duration_ms: duration * 1000,
            user_id: context.user_id,
            response_size: context.response_size || 0,
            cache_hit: context.cache_hit || false
        })
    }

    // Track error
    const trackError = (errorType, errorMessage, context = {}) => {
        trackCustomEvent('ErrorOccurrence', {
            error_type: errorType,
            error_message: errorMessage,
            user_id: context.user_id,
            endpoint: context.endpoint || 'unknown',
            stack_trace: context.stack_trace,
            severity: context.severity || 'error'
        })
    }

    // Track business metric
    const trackBusinessMetric = (metricName, value, attributes = {}) => {
        trackCustomEvent('BusinessMetric', {
            metric_name: metricName,
            metric_value: value,
            timestamp: Date.now(),
            ...attributes
        })
    }

    // Track timing
    const trackTiming = (timingName, timingValue, timingCategory = 'performance') => {
        trackCustomEvent('Timing', {
            timing_name: timingName,
            timing_value: timingValue,
            timing_category: timingCategory
        })
    }

    // Track form interaction
    const trackFormInteraction = (formName, action, fieldName = '') => {
        trackCustomEvent('FormInteraction', {
            form_name: formName,
            form_action: action,
            field_name: fieldName
        })
    }

    // Track search
    const trackSearch = (searchTerm, resultsCount = 0, searchDuration = 0) => {
        trackCustomEvent('Search', {
            search_term: searchTerm,
            results_count: resultsCount,
            search_duration: searchDuration
        })
    }

    // Track file upload
    const trackFileUpload = (fileName, fileSize, fileType, uploadType = 'unknown') => {
        trackCustomEvent('FileUpload', {
            file_name: fileName,
            file_size: fileSize,
            file_type: fileType,
            upload_type: uploadType
        })
    }

    // Track social interaction
    const trackSocialInteraction = (platform, action, target = '') => {
        trackCustomEvent('SocialInteraction', {
            social_platform: platform,
            social_action: action,
            social_target: target
        })
    }

    // Set user attributes
    const setUserAttributes = (attributes = {}) => {
        if (!newRelicEnabled.value || !window.newrelic) return

        Object.keys(attributes).forEach(key => {
            window.newrelic.setCustomAttribute(key, attributes[key])
        })
        
        console.log('📊 New Relic user attributes set:', attributes)
    }

    // Set user ID
    const setUserId = (userId) => {
        if (!newRelicEnabled.value || !window.newrelic) return

        window.newrelic.setCustomAttribute('user_id', userId)
        console.log('📊 New Relic user ID set:', userId)
    }

    // Add custom attribute
    const addCustomAttribute = (key, value) => {
        if (!newRelicEnabled.value || !window.newrelic) return

        window.newrelic.setCustomAttribute(key, value)
        console.log('📊 New Relic custom attribute added:', key, value)
    }

    // Record custom event
    const recordCustomEvent = (eventType, attributes = {}) => {
        if (!newRelicEnabled.value || !window.newrelic) return

        window.newrelic.recordCustomEvent(eventType, attributes)
        console.log('📊 New Relic custom event recorded:', eventType, attributes)
    }

    // Test New Relic integration
    const testNewRelic = () => {
        if (!newRelicEnabled.value) {
            console.warn('📊 New Relic not enabled')
            return false
        }

        try {
            trackCustomEvent('IntegrationTest', {
                test: true,
                timestamp: new Date().toISOString(),
                environment: import.meta.env.MODE
            })
            return true
        } catch (error) {
            console.error('📊 New Relic test failed:', error)
            return false
        }
    }

    // Initialize on mount
    onMounted(() => {
        initNewRelic()
    })

    return {
        newRelicEnabled,
        newRelicInitialized,
        initNewRelic,
        trackCustomEvent,
        trackUserRegistration,
        trackUserLogin,
        trackCarDiagnosis,
        trackCarAdded,
        trackPartSearch,
        trackPageView,
        trackConversion,
        trackAPICall,
        trackError,
        trackBusinessMetric,
        trackTiming,
        trackFormInteraction,
        trackSearch,
        trackFileUpload,
        trackSocialInteraction,
        setUserAttributes,
        setUserId,
        addCustomAttribute,
        recordCustomEvent,
        testNewRelic
    }
}















