import { ref, computed } from 'vue'

// Global state for multi-brand platform APIs
const platformAPIs = ref({})
const supportedPlatforms = ref([])
const apiStatus = ref({})

export function useMultiBrandPlatformAPI() {
    // Get supported platforms
    const getSupportedPlatforms = async () => {
        try {
            const response = await fetch('/api/platform/supported', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                }
            })

            if (response.ok) {
                const result = await response.json()
                supportedPlatforms.value = result.data.platforms
                return result.data.platforms
            } else {
                console.error('Failed to get supported platforms')
                return []
            }
        } catch (error) {
            console.error('Error getting supported platforms:', error)
            return []
        }
    }

    // Get API status
    const getAPIStatus = async () => {
        try {
            const response = await fetch('/api/platform/status', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                }
            })

            if (response.ok) {
                const result = await response.json()
                apiStatus.value = result.data
                return result.data
            } else {
                console.error('Failed to get API status')
                return {}
            }
        } catch (error) {
            console.error('Error getting API status:', error)
            return {}
        }
    }

    // Get API documentation
    const getAPIDocumentation = async () => {
        try {
            const response = await fetch('/api/platform/documentation', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                }
            })

            if (response.ok) {
                const result = await response.json()
                return result.data.documentation
            } else {
                console.error('Failed to get API documentation')
                return {}
            }
        } catch (error) {
            console.error('Error getting API documentation:', error)
            return {}
        }
    }

    // Test platform API
    const testPlatformAPI = async (platform) => {
        try {
            const response = await fetch('/api/platform/test', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ platform })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`✅ ${platform} API test:`, result.data.status)
                return result.data
            } else {
                console.error(`❌ ${platform} API test failed`)
                return { connection_test: 'failed', status: 'API connection failed' }
            }
        } catch (error) {
            console.error(`Error testing ${platform} API:`, error)
            return { connection_test: 'failed', status: 'API connection failed' }
        }
    }

    // Get Smartcar vehicle data
    const getSmartcarVehicleData = async (vehicleId, accessToken) => {
        try {
            const response = await fetch('/api/platform/smartcar/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vehicle_id: vehicleId, access_token: accessToken })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 Smartcar vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get Smartcar vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting Smartcar vehicle data:', error)
            return null
        }
    }

    // Get High Mobility vehicle data
    const getHighMobilityVehicleData = async (vehicleId) => {
        try {
            const response = await fetch('/api/platform/high-mobility/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vehicle_id: vehicleId })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 High Mobility vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get High Mobility vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting High Mobility vehicle data:', error)
            return null
        }
    }

    // Get Otonomo vehicle data
    const getOtonomoVehicleData = async (vehicleId) => {
        try {
            const response = await fetch('/api/platform/otonomo/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vehicle_id: vehicleId })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 Otonomo vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get Otonomo vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting Otonomo vehicle data:', error)
            return null
        }
    }

    // Get Wejo vehicle data
    const getWejoVehicleData = async (vehicleId) => {
        try {
            const response = await fetch('/api/platform/wejo/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vehicle_id: vehicleId })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 Wejo vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get Wejo vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting Wejo vehicle data:', error)
            return null
        }
    }

    // Get MotorData diagnostics
    const getMotorDataDiagnostics = async (vin, dtcCode = null) => {
        try {
            const response = await fetch('/api/platform/motordata/diagnostics', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vin, dtc_code: dtcCode })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🔍 MotorData diagnostics retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get MotorData diagnostics')
                return null
            }
        } catch (error) {
            console.error('Error getting MotorData diagnostics:', error)
            return null
        }
    }

    // Get CarAPI vehicle data
    const getCarAPIVehicleData = async (make, model, year) => {
        try {
            const response = await fetch('/api/platform/carapi/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ make, model, year })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 CarAPI vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get CarAPI vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting CarAPI vehicle data:', error)
            return null
        }
    }

    // Get comprehensive vehicle data from all platforms
    const getComprehensiveVehicleData = async (vehicleId, make = null, model = null, year = null) => {
        try {
            const response = await fetch('/api/platform/comprehensive/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ vehicle_id: vehicleId, make, model, year })
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🚗 Comprehensive vehicle data retrieved:', result.data)
                return result.data
            } else {
                console.error('Failed to get comprehensive vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting comprehensive vehicle data:', error)
            return null
        }
    }

    // Get platform display name
    const getPlatformDisplayName = (platform) => {
        const displayNames = {
            'smartcar': 'Smartcar',
            'high_mobility': 'High Mobility',
            'otonomo': 'Otonomo',
            'wejo': 'Wejo',
            'motordata': 'MotorData',
            'carapi': 'CarAPI.app'
        }
        return displayNames[platform] || platform
    }

    // Get platform logo URL
    const getPlatformLogo = (platform) => {
        const logos = {
            'smartcar': '/images/platforms/smartcar-logo.png',
            'high_mobility': '/images/platforms/high-mobility-logo.png',
            'otonomo': '/images/platforms/otonomo-logo.png',
            'wejo': '/images/platforms/wejo-logo.png',
            'motordata': '/images/platforms/motordata-logo.png',
            'carapi': '/images/platforms/carapi-logo.png'
        }
        return logos[platform] || '/images/platforms/default-logo.png'
    }

    // Get platform API status
    const getPlatformAPIStatus = (platform) => {
        return apiStatus.value.platforms?.[platform] || { enabled: false, configured: false }
    }

    // Check if platform API is available
    const isPlatformAPIAvailable = (platform) => {
        const status = getPlatformAPIStatus(platform)
        return status.enabled && status.configured
    }

    // Get enabled platforms
    const getEnabledPlatforms = computed(() => {
        return supportedPlatforms.value.filter(p => p.enabled)
    })

    // Get configured platforms
    const getConfiguredPlatforms = computed(() => {
        return supportedPlatforms.value.filter(p => p.configured)
    })

    // Get platforms by supported brands
    const getPlatformsByBrand = (brand) => {
        return supportedPlatforms.value.filter(p => 
            p.supported_brands && p.supported_brands.includes(brand)
        )
    }

    // Get all supported brands across platforms
    const getAllSupportedBrands = computed(() => {
        const brands = new Set()
        supportedPlatforms.value.forEach(platform => {
            if (platform.supported_brands) {
                platform.supported_brands.forEach(brand => brands.add(brand))
            }
        })
        return Array.from(brands).sort()
    })

    // Get platform features
    const getPlatformFeatures = (platform) => {
        const features = {
            'smartcar': [
                'OAuth 2.0 Authentication',
                '25+ Car Brands',
                'Real-time Vehicle Data',
                'Battery & Fuel Levels',
                'Location Tracking',
                'Remote Vehicle Control'
            ],
            'high_mobility': [
                'Connected Car Platform',
                'Sandbox Environment',
                'Vehicle Data Access',
                'Diagnostic Information',
                'Maintenance Scheduling',
                'Real-time Status Updates'
            ],
            'otonomo': [
                'Data-as-a-Service',
                'Fleet Management',
                'Vehicle Telemetry',
                'Diagnostic Information',
                'Maintenance Scheduling',
                'Analytics & Insights'
            ],
            'wejo': [
                'Big Data Platform',
                'Connected Vehicles',
                'Real-time Telemetry',
                'Diagnostic Information',
                'Analytics & Insights',
                'Big Data Processing'
            ],
            'motordata': [
                'Multi-brand Diagnostics',
                'DTC Codes',
                'Repair Information',
                'Vehicle Specifications',
                'Maintenance Schedules',
                'Recall Information'
            ],
            'carapi': [
                'Multi-brand Data',
                'Vehicle Specifications',
                'Make, Model, Year Data',
                'Engine Information',
                'Body Styles & Colors',
                'Diagnostic Information'
            ]
        }
        return features[platform] || []
    }

    // Get platform authentication type
    const getPlatformAuthType = (platform) => {
        const authTypes = {
            'smartcar': 'OAuth 2.0',
            'high_mobility': 'API Key',
            'otonomo': 'API Key',
            'wejo': 'API Key',
            'motordata': 'API Key',
            'carapi': 'API Key'
        }
        return authTypes[platform] || 'API Key'
    }

    // Get platform rate limits
    const getPlatformRateLimits = (platform) => {
        const rateLimits = {
            'smartcar': '1000 requests/hour',
            'high_mobility': '500 requests/hour',
            'otonomo': '2000 requests/hour',
            'wejo': '1500 requests/hour',
            'motordata': '1000 requests/hour',
            'carapi': '1000 requests/hour'
        }
        return rateLimits[platform] || 'Unknown'
    }

    // Get data source badge
    const getDataSourceBadge = (dataSource) => {
        const badges = {
            'smartcar_api': { text: 'Smartcar', class: 'bg-blue-100 text-blue-800' },
            'high_mobility_api': { text: 'High Mobility', class: 'bg-green-100 text-green-800' },
            'otonomo_api': { text: 'Otonomo', class: 'bg-purple-100 text-purple-800' },
            'wejo_api': { text: 'Wejo', class: 'bg-orange-100 text-orange-800' },
            'motordata_api': { text: 'MotorData', class: 'bg-red-100 text-red-800' },
            'carapi_api': { text: 'CarAPI', class: 'bg-indigo-100 text-indigo-800' },
            'mock_data': { text: 'Demo Data', class: 'bg-yellow-100 text-yellow-800' },
            'unknown': { text: 'Unknown', class: 'bg-gray-100 text-gray-800' }
        }
        return badges[dataSource] || badges['unknown']
    }

    // Get platform comparison
    const getPlatformComparison = () => {
        return {
            'smartcar': {
                'name': 'Smartcar',
                'brands': 25,
                'auth': 'OAuth 2.0',
                'rate_limit': '1000/hour',
                'features': ['Real-time Data', 'Remote Control', 'Location Tracking'],
                'best_for': 'Consumer Apps'
            },
            'high_mobility': {
                'name': 'High Mobility',
                'brands': 14,
                'auth': 'API Key',
                'rate_limit': '500/hour',
                'features': ['Sandbox', 'Diagnostics', 'Maintenance'],
                'best_for': 'Testing & Development'
            },
            'otonomo': {
                'name': 'Otonomo',
                'brands': 14,
                'auth': 'API Key',
                'rate_limit': '2000/hour',
                'features': ['Fleet Management', 'Analytics', 'Telemetry'],
                'best_for': 'Fleet Management'
            },
            'wejo': {
                'name': 'Wejo',
                'brands': 21,
                'auth': 'API Key',
                'rate_limit': '1500/hour',
                'features': ['Big Data', 'Analytics', 'Real-time'],
                'best_for': 'Big Data Analytics'
            },
            'motordata': {
                'name': 'MotorData',
                'brands': 29,
                'auth': 'API Key',
                'rate_limit': '1000/hour',
                'features': ['Diagnostics', 'Repair Info', 'DTC Codes'],
                'best_for': 'Diagnostics & Repair'
            },
            'carapi': {
                'name': 'CarAPI.app',
                'brands': 34,
                'auth': 'API Key',
                'rate_limit': '1000/hour',
                'features': ['Specifications', 'Vehicle Data', 'Maintenance'],
                'best_for': 'Vehicle Information'
            }
        }
    }

    // Initialize platform APIs
    const initializePlatformAPIs = async () => {
        try {
            await Promise.all([
                getSupportedPlatforms(),
                getAPIStatus()
            ])
            console.log('🚗 Multi-brand Platform APIs initialized successfully')
        } catch (error) {
            console.error('Error initializing platform APIs:', error)
        }
    }

    return {
        // State
        platformAPIs,
        supportedPlatforms,
        apiStatus,
        getEnabledPlatforms,
        getConfiguredPlatforms,
        getAllSupportedBrands,

        // Methods
        getSupportedPlatforms,
        getAPIStatus,
        getAPIDocumentation,
        testPlatformAPI,
        getSmartcarVehicleData,
        getHighMobilityVehicleData,
        getOtonomoVehicleData,
        getWejoVehicleData,
        getMotorDataDiagnostics,
        getCarAPIVehicleData,
        getComprehensiveVehicleData,
        getPlatformDisplayName,
        getPlatformLogo,
        getPlatformAPIStatus,
        isPlatformAPIAvailable,
        getPlatformsByBrand,
        getPlatformFeatures,
        getPlatformAuthType,
        getPlatformRateLimits,
        getDataSourceBadge,
        getPlatformComparison,
        initializePlatformAPIs
    }
}

// Global state for multi-brand platform APIs















