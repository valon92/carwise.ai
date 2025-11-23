import { ref, onMounted } from 'vue'

// Global Sentry state
const sentryEnabled = ref(false)
const sentryInitialized = ref(false)

export function useSentry() {
    // Initialize Sentry
    const initSentry = () => {
        if (typeof window !== 'undefined' && window.Sentry) {
            sentryEnabled.value = true
            sentryInitialized.value = true
            
            // Configure Sentry
            window.Sentry.configureScope((scope) => {
                scope.setTag('platform', 'carwise-ai')
                scope.setTag('version', '1.0.0')
                scope.setContext('app', {
                    name: 'CarWise.ai',
                    version: '1.0.0',
                    environment: import.meta.env.MODE
                })
            })
            
            console.log('🔍 Sentry initialized successfully')
        }
    }

    // Capture exception
    const captureException = (error, context = {}) => {
        if (!sentryEnabled.value || !window.Sentry) return

        window.Sentry.withScope((scope) => {
            // Add custom context
            Object.keys(context).forEach(key => {
                scope.setContext(key, context[key])
            })

            // Add user context if available
            const user = JSON.parse(localStorage.getItem('user') || 'null')
            if (user) {
                scope.setUser({
                    id: user.id,
                    email: user.email,
                    username: user.name || user.first_name,
                    role: user.role || 'customer'
                })
            }

            // Add page context
            scope.setContext('page', {
                url: window.location.href,
                path: window.location.pathname,
                title: document.title,
                referrer: document.referrer
            })

            window.Sentry.captureException(error)
        })
        
        console.log('🔍 Exception captured:', error.message)
    }

    // Capture message
    const captureMessage = (message, level = 'info', context = {}) => {
        if (!sentryEnabled.value || !window.Sentry) return

        window.Sentry.withScope((scope) => {
            Object.keys(context).forEach(key => {
                scope.setContext(key, context[key])
            })

            scope.setLevel(level)
            window.Sentry.captureMessage(message)
        })
        
        console.log('🔍 Message captured:', message)
    }

    // Add breadcrumb
    const addBreadcrumb = (message, category = 'user', level = 'info', data = {}) => {
        if (!sentryEnabled.value || !window.Sentry) return

        window.Sentry.addBreadcrumb({
            message,
            category,
            level,
            data
        })
    }

    // Track user actions
    const trackUserAction = (action, data = {}) => {
        addBreadcrumb(`User action: ${action}`, 'user_action', 'info', data)
    }

    // Track API calls
    const trackAPICall = (endpoint, method, statusCode, duration = null) => {
        addBreadcrumb(`API call: ${method} ${endpoint}`, 'api', statusCode >= 400 ? 'error' : 'info', {
            endpoint,
            method,
            status_code: statusCode,
            duration
        })
    }

    // Track form interactions
    const trackFormInteraction = (formName, action, fieldName = '') => {
        addBreadcrumb(`Form interaction: ${formName} - ${action}`, 'form', 'info', {
            form_name: formName,
            form_action: action,
            field_name: fieldName
        })
    }

    // Track navigation
    const trackNavigation = (from, to) => {
        addBreadcrumb(`Navigation: ${from} → ${to}`, 'navigation', 'info', {
            from,
            to
        })
    }

    // Track performance issues
    const trackPerformanceIssue = (message, data = {}) => {
        captureMessage(message, 'warning', {
            performance: {
                ...data,
                timestamp: new Date().toISOString()
            }
        })
    }

    // Track business logic errors
    const trackBusinessError = (error, context = {}) => {
        captureException(error, {
            error_type: 'business_logic',
            ...context
        })
    }

    // Track AI diagnosis errors
    const trackAIDiagnosisError = (error, diagnosisData = {}) => {
        captureException(error, {
            error_type: 'ai_diagnosis',
            diagnosis: {
                car_brand: diagnosisData.brand || 'unknown',
                car_model: diagnosisData.model || 'unknown',
                car_year: diagnosisData.year || 'unknown',
                symptoms: diagnosisData.symptoms || [],
                ai_provider: diagnosisData.ai_provider || 'unknown'
            }
        })
    }

    // Track authentication errors
    const trackAuthError = (error, authData = {}) => {
        captureException(error, {
            error_type: 'authentication',
            authentication: {
                action: authData.action || 'unknown',
                email: authData.email || 'unknown',
                ip_address: authData.ip_address || 'unknown'
            }
        })
    }

    // Track file upload errors
    const trackFileUploadError = (error, fileData = {}) => {
        captureException(error, {
            error_type: 'file_upload',
            file_upload: {
                file_name: fileData.file_name || 'unknown',
                file_size: fileData.file_size || 0,
                file_type: fileData.file_type || 'unknown',
                upload_type: fileData.upload_type || 'unknown'
            }
        })
    }

    // Set user context
    const setUserContext = (user) => {
        if (!sentryEnabled.value || !window.Sentry) return

        window.Sentry.configureScope((scope) => {
            scope.setUser({
                id: user.id,
                email: user.email,
                username: user.name || user.first_name,
                role: user.role || 'customer'
            })
        })
    }

    // Clear user context
    const clearUserContext = () => {
        if (!sentryEnabled.value || !window.Sentry) return

        window.Sentry.configureScope((scope) => {
            scope.setUser(null)
        })
    }

    // Test Sentry integration
    const testSentry = () => {
        if (!sentryEnabled.value) {
            console.warn('🔍 Sentry not enabled')
            return false
        }

        try {
            captureMessage('Sentry integration test', 'info', {
                test: true,
                timestamp: new Date().toISOString()
            })
            return true
        } catch (error) {
            console.error('🔍 Sentry test failed:', error)
            return false
        }
    }

    // Initialize on mount
    onMounted(() => {
        initSentry()
    })

    return {
        sentryEnabled,
        sentryInitialized,
        initSentry,
        captureException,
        captureMessage,
        addBreadcrumb,
        trackUserAction,
        trackAPICall,
        trackFormInteraction,
        trackNavigation,
        trackPerformanceIssue,
        trackBusinessError,
        trackAIDiagnosisError,
        trackAuthError,
        trackFileUploadError,
        setUserContext,
        clearUserContext,
        testSentry
    }
}















