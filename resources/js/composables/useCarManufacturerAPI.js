import { ref, computed } from 'vue'

// Global state for car manufacturer APIs
const manufacturerAPIs = ref({})
const supportedManufacturers = ref([])
const apiStatus = ref({})

export function useCarManufacturerAPI() {
    // Get supported manufacturers
    const getSupportedManufacturers = async () => {
        try {
            const response = await fetch('/api/manufacturer/supported', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                }
            })

            if (response.ok) {
                const result = await response.json()
                supportedManufacturers.value = result.data.manufacturers
                return result.data.manufacturers
            } else {
                console.error('Failed to get supported manufacturers')
                return []
            }
        } catch (error) {
            console.error('Error getting supported manufacturers:', error)
            return []
        }
    }

    // Get API status
    const getAPIStatus = async () => {
        try {
            const response = await fetch('/api/manufacturer/status', {
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
            const response = await fetch('/api/manufacturer/documentation', {
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

    // Test manufacturer API
    const testManufacturerAPI = async (manufacturer) => {
        try {
            const response = await fetch('/api/manufacturer/test', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`✅ ${manufacturer} API test:`, result.data.status)
                return result.data
            } else {
                console.error(`❌ ${manufacturer} API test failed`)
                return { connection_test: 'failed', status: 'API connection failed' }
            }
        } catch (error) {
            console.error(`Error testing ${manufacturer} API:`, error)
            return { connection_test: 'failed', status: 'API connection failed' }
        }
    }

    // Get vehicle data
    const getVehicleData = async (manufacturer, vin) => {
        try {
            const response = await fetch('/api/manufacturer/vehicle/data', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer, vin })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`📊 Vehicle data retrieved for ${manufacturer}:`, result.data)
                return result.data
            } else {
                console.error('Failed to get vehicle data')
                return null
            }
        } catch (error) {
            console.error('Error getting vehicle data:', error)
            return null
        }
    }

    // Get vehicle diagnostics
    const getVehicleDiagnostics = async (manufacturer, vin) => {
        try {
            const response = await fetch('/api/manufacturer/vehicle/diagnostics', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer, vin })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`🔍 Vehicle diagnostics retrieved for ${manufacturer}:`, result.data)
                return result.data
            } else {
                console.error('Failed to get vehicle diagnostics')
                return null
            }
        } catch (error) {
            console.error('Error getting vehicle diagnostics:', error)
            return null
        }
    }

    // Get vehicle maintenance
    const getVehicleMaintenance = async (manufacturer, vin) => {
        try {
            const response = await fetch('/api/manufacturer/vehicle/maintenance', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer, vin })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`🔧 Vehicle maintenance retrieved for ${manufacturer}:`, result.data)
                return result.data
            } else {
                console.error('Failed to get vehicle maintenance')
                return null
            }
        } catch (error) {
            console.error('Error getting vehicle maintenance:', error)
            return null
        }
    }

    // Get vehicle status
    const getVehicleStatus = async (manufacturer, vin) => {
        try {
            const response = await fetch('/api/manufacturer/vehicle/status', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer, vin })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`📱 Vehicle status retrieved for ${manufacturer}:`, result.data)
                return result.data
            } else {
                console.error('Failed to get vehicle status')
                return null
            }
        } catch (error) {
            console.error('Error getting vehicle status:', error)
            return null
        }
    }

    // Get comprehensive vehicle information
    const getVehicleInfo = async (manufacturer, vin) => {
        try {
            const response = await fetch('/api/manufacturer/vehicle/info', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ manufacturer, vin })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`🚗 Comprehensive vehicle info retrieved for ${manufacturer}:`, result.data)
                return result.data
            } else {
                console.error('Failed to get comprehensive vehicle information')
                return null
            }
        } catch (error) {
            console.error('Error getting comprehensive vehicle information:', error)
            return null
        }
    }

    // Get manufacturer display name
    const getManufacturerDisplayName = (manufacturer) => {
        const displayNames = {
            'bmw': 'BMW',
            'mercedes': 'Mercedes-Benz',
            'volkswagen': 'Volkswagen',
            'audi': 'Audi',
            'ford': 'Ford',
            'toyota': 'Toyota',
            'volvo': 'Volvo',
            'tesla': 'Tesla'
        }
        return displayNames[manufacturer] || manufacturer
    }

    // Get manufacturer logo URL
    const getManufacturerLogo = (manufacturer) => {
        const logos = {
            'bmw': '/images/manufacturers/bmw-logo.png',
            'mercedes': '/images/manufacturers/mercedes-logo.png',
            'volkswagen': '/images/manufacturers/volkswagen-logo.png',
            'audi': '/images/manufacturers/audi-logo.png',
            'ford': '/images/manufacturers/ford-logo.png',
            'toyota': '/images/manufacturers/toyota-logo.png',
            'volvo': '/images/manufacturers/volvo-logo.png',
            'tesla': '/images/manufacturers/tesla-logo.png'
        }
        return logos[manufacturer] || '/images/manufacturers/default-logo.png'
    }

    // Get manufacturer API status
    const getManufacturerAPIStatus = (manufacturer) => {
        return apiStatus.value.apis?.[manufacturer] || { enabled: false, configured: false }
    }

    // Check if manufacturer API is available
    const isManufacturerAPIAvailable = (manufacturer) => {
        const status = getManufacturerAPIStatus(manufacturer)
        return status.enabled && status.configured
    }

    // Get enabled manufacturers
    const getEnabledManufacturers = computed(() => {
        return supportedManufacturers.value.filter(m => m.enabled)
    })

    // Get configured manufacturers
    const getConfiguredManufacturers = computed(() => {
        return supportedManufacturers.value.filter(m => m.configured)
    })

    // Get public APIs
    const getPublicAPIs = async () => {
        const documentation = await getAPIDocumentation()
        return Object.entries(documentation).filter(([_, doc]) => doc.status === 'Public')
    }

    // Get partner APIs
    const getPartnerAPIs = async () => {
        const documentation = await getAPIDocumentation()
        return Object.entries(documentation).filter(([_, doc]) => doc.status.includes('Partner'))
    }

    // Validate VIN
    const validateVIN = (vin) => {
        if (!vin || vin.length !== 17) {
            return false
        }
        
        // Basic VIN validation (alphanumeric, no I, O, Q)
        const vinRegex = /^[A-HJ-NPR-Z0-9]{17}$/
        return vinRegex.test(vin)
    }

    // Format VIN for display
    const formatVIN = (vin) => {
        if (!vin || vin.length !== 17) {
            return vin
        }
        
        // Format as XXXX-XXXX-XXXX-XXXXX
        return vin.replace(/(.{4})(.{4})(.{4})(.{5})/, '$1-$2-$3-$4')
    }

    // Get data source badge
    const getDataSourceBadge = (dataSource) => {
        const badges = {
            'manufacturer_api': { text: 'Live Data', class: 'bg-green-100 text-green-800' },
            'mock_data': { text: 'Demo Data', class: 'bg-yellow-100 text-yellow-800' },
            'unknown': { text: 'Unknown', class: 'bg-gray-100 text-gray-800' }
        }
        return badges[dataSource] || badges['unknown']
    }

    // Initialize manufacturer APIs
    const initializeManufacturerAPIs = async () => {
        try {
            await Promise.all([
                getSupportedManufacturers(),
                getAPIStatus()
            ])
            console.log('🚗 Manufacturer APIs initialized successfully')
        } catch (error) {
            console.error('Error initializing manufacturer APIs:', error)
        }
    }

    return {
        // State
        manufacturerAPIs,
        supportedManufacturers,
        apiStatus,
        getEnabledManufacturers,
        getConfiguredManufacturers,

        // Methods
        getSupportedManufacturers,
        getAPIStatus,
        getAPIDocumentation,
        testManufacturerAPI,
        getVehicleData,
        getVehicleDiagnostics,
        getVehicleMaintenance,
        getVehicleStatus,
        getVehicleInfo,
        getManufacturerDisplayName,
        getManufacturerLogo,
        getManufacturerAPIStatus,
        isManufacturerAPIAvailable,
        getPublicAPIs,
        getPartnerAPIs,
        validateVIN,
        formatVIN,
        getDataSourceBadge,
        initializeManufacturerAPIs
    }
}







