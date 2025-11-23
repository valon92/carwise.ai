import { ref, computed } from 'vue'

// Global state for parts marketplace APIs
const marketplaceAPIs = ref({})
const supportedMarketplaces = ref([])
const apiStatus = ref({})

export function usePartsMarketplaceAPI() {
    // Get supported marketplaces
    const getSupportedMarketplaces = async () => {
        try {
            const response = await fetch('/api/marketplace/supported', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                }
            })

            if (response.ok) {
                const result = await response.json()
                supportedMarketplaces.value = result.data.marketplaces
                return result.data.marketplaces
            } else {
                console.error('Failed to get supported marketplaces')
                return []
            }
        } catch (error) {
            console.error('Error getting supported marketplaces:', error)
            return []
        }
    }

    // Get API status
    const getAPIStatus = async () => {
        try {
            const response = await fetch('/api/marketplace/status', {
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
            const response = await fetch('/api/marketplace/documentation', {
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

    // Test marketplace API
    const testMarketplaceAPI = async (marketplace) => {
        try {
            const response = await fetch('/api/marketplace/test', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ marketplace })
            })

            if (response.ok) {
                const result = await response.json()
                console.log(`✅ ${marketplace} API test:`, result.data.status)
                return result.data
            } else {
                console.error(`❌ ${marketplace} API test failed`)
                return { connection_test: 'failed', status: 'API connection failed' }
            }
        } catch (error) {
            console.error(`Error testing ${marketplace} API:`, error)
            return { connection_test: 'failed', status: 'API connection failed' }
        }
    }

    // Search eBay Motors parts
    const searchEbayMotorsParts = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/ebay-motors/search', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 eBay Motors parts search:', result.data)
                return result.data
            } else {
                console.error('Failed to search eBay Motors parts')
                return null
            }
        } catch (error) {
            console.error('Error searching eBay Motors parts:', error)
            return null
        }
    }

    // Search Amazon parts
    const searchAmazonParts = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/amazon/search', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 Amazon parts search:', result.data)
                return result.data
            } else {
                console.error('Failed to search Amazon parts')
                return null
            }
        } catch (error) {
            console.error('Error searching Amazon parts:', error)
            return null
        }
    }

    // Search AutoZone parts
    const searchAutoZoneParts = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/autozone/search', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 AutoZone parts search:', result.data)
                return result.data
            } else {
                console.error('Failed to search AutoZone parts')
                return null
            }
        } catch (error) {
            console.error('Error searching AutoZone parts:', error)
            return null
        }
    }

    // Search RockAuto parts
    const searchRockAutoParts = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/rockauto/search', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 RockAuto parts search:', result.data)
                return result.data
            } else {
                console.error('Failed to search RockAuto parts')
                return null
            }
        } catch (error) {
            console.error('Error searching RockAuto parts:', error)
            return null
        }
    }

    // Search PartsGeek parts
    const searchPartsGeekParts = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/partsgeek/search', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 PartsGeek parts search:', result.data)
                return result.data
            } else {
                console.error('Failed to search PartsGeek parts')
                return null
            }
        } catch (error) {
            console.error('Error searching PartsGeek parts:', error)
            return null
        }
    }

    // Search parts across all marketplaces
    const searchAllMarketplaces = async (searchParams) => {
        try {
            const response = await fetch('/api/marketplace/search/all', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchParams)
            })

            if (response.ok) {
                const result = await response.json()
                console.log('🛒 All marketplaces search:', result.data)
                return result.data
            } else {
                console.error('Failed to search all marketplaces')
                return null
            }
        } catch (error) {
            console.error('Error searching all marketplaces:', error)
            return null
        }
    }

    // Get marketplace display name
    const getMarketplaceDisplayName = (marketplace) => {
        const displayNames = {
            'ebay_motors': 'eBay Motors',
            'amazon_paapi': 'Amazon',
            'autozone': 'AutoZone',
            'rockauto': 'RockAuto',
            'partsgeek': 'PartsGeek'
        }
        return displayNames[marketplace] || marketplace
    }

    // Get marketplace logo URL
    const getMarketplaceLogo = (marketplace) => {
        const logos = {
            'ebay_motors': '/images/marketplaces/ebay-logo.png',
            'amazon_paapi': '/images/marketplaces/amazon-logo.png',
            'autozone': '/images/marketplaces/autozone-logo.png',
            'rockauto': '/images/marketplaces/rockauto-logo.png',
            'partsgeek': '/images/marketplaces/partsgeek-logo.png'
        }
        return logos[marketplace] || '/images/marketplaces/default-logo.png'
    }

    // Get marketplace API status
    const getMarketplaceAPIStatus = (marketplace) => {
        return apiStatus.value.marketplaces?.[marketplace] || { enabled: false, configured: false }
    }

    // Check if marketplace API is available
    const isMarketplaceAPIAvailable = (marketplace) => {
        const status = getMarketplaceAPIStatus(marketplace)
        return status.enabled && status.configured
    }

    // Get enabled marketplaces
    const getEnabledMarketplaces = computed(() => {
        return supportedMarketplaces.value.filter(m => m.enabled)
    })

    // Get configured marketplaces
    const getConfiguredMarketplaces = computed(() => {
        return supportedMarketplaces.value.filter(m => m.configured)
    })

    // Get marketplace features
    const getMarketplaceFeatures = (marketplace) => {
        const features = {
            'ebay_motors': [
                'Parts search and discovery',
                'Parts compatibility checking',
                'Price comparison',
                'Seller ratings and feedback',
                'Shipping information',
                'Condition filtering'
            ],
            'amazon_paapi': [
                'Product search and discovery',
                'Price comparison',
                'Product details and specifications',
                'Customer reviews and ratings',
                'Availability information',
                'Prime shipping eligibility'
            ],
            'autozone': [
                'Parts search and catalog',
                'Vehicle compatibility',
                'Store locator',
                'Inventory checking',
                'Price comparison',
                'Professional services'
            ],
            'rockauto': [
                'Global parts catalog',
                'Vehicle-specific parts',
                'Brand and category filtering',
                'Price comparison',
                'Shipping information',
                'Parts compatibility'
            ],
            'partsgeek': [
                'OEM and aftermarket parts',
                'Vehicle compatibility',
                'Price comparison',
                'Brand filtering',
                'Category browsing',
                'Shipping options'
            ]
        }
        return features[marketplace] || []
    }

    // Get marketplace authentication type
    const getMarketplaceAuthType = (marketplace) => {
        const authTypes = {
            'ebay_motors': 'OAuth 2.0',
            'amazon_paapi': 'AWS Signature V4',
            'autozone': 'API Key',
            'rockauto': 'API Key',
            'partsgeek': 'API Key'
        }
        return authTypes[marketplace] || 'API Key'
    }

    // Get marketplace rate limits
    const getMarketplaceRateLimits = (marketplace) => {
        const rateLimits = {
            'ebay_motors': '5000 requests/day',
            'amazon_paapi': '8640 requests/day',
            'autozone': '1000 requests/hour',
            'rockauto': '2000 requests/hour',
            'partsgeek': '1500 requests/hour'
        }
        return rateLimits[marketplace] || 'Unknown'
    }

    // Get data source badge
    const getDataSourceBadge = (dataSource) => {
        const badges = {
            'ebay_motors_api': { text: 'eBay Motors', class: 'bg-blue-100 text-blue-800' },
            'amazon_paapi_api': { text: 'Amazon', class: 'bg-orange-100 text-orange-800' },
            'autozone_api': { text: 'AutoZone', class: 'bg-red-100 text-red-800' },
            'rockauto_api': { text: 'RockAuto', class: 'bg-green-100 text-green-800' },
            'partsgeek_api': { text: 'PartsGeek', class: 'bg-purple-100 text-purple-800' },
            'mock_data': { text: 'Demo Data', class: 'bg-yellow-100 text-yellow-800' },
            'unknown': { text: 'Unknown', class: 'bg-gray-100 text-gray-800' }
        }
        return badges[dataSource] || badges['unknown']
    }

    // Get marketplace comparison
    const getMarketplaceComparison = () => {
        return {
            'ebay_motors': {
                'name': 'eBay Motors',
                'parts_count': 'Millions',
                'auth': 'OAuth 2.0',
                'rate_limit': '5000/day',
                'features': ['Used Parts', 'New Parts', 'Seller Ratings', 'Compatibility'],
                'best_for': 'Used & New Parts'
            },
            'amazon_paapi': {
                'name': 'Amazon',
                'parts_count': 'Millions',
                'auth': 'AWS Signature V4',
                'rate_limit': '8640/day',
                'features': ['Prime Shipping', 'Reviews', 'Fast Delivery', 'Wide Selection'],
                'best_for': 'Fast Delivery'
            },
            'autozone': {
                'name': 'AutoZone',
                'parts_count': 'Hundreds of Thousands',
                'auth': 'API Key',
                'rate_limit': '1000/hour',
                'features': ['Store Locator', 'Professional Services', 'Vehicle Specific', 'Inventory'],
                'best_for': 'Professional Services'
            },
            'rockauto': {
                'name': 'RockAuto',
                'parts_count': 'Hundreds of Thousands',
                'auth': 'API Key',
                'rate_limit': '2000/hour',
                'features': ['Global Catalog', 'Vehicle Specific', 'Brand Filtering', 'Compatibility'],
                'best_for': 'Global Parts Catalog'
            },
            'partsgeek': {
                'name': 'PartsGeek',
                'parts_count': 'Hundreds of Thousands',
                'auth': 'API Key',
                'rate_limit': '1500/hour',
                'features': ['OEM Parts', 'Aftermarket', 'Brand Filtering', 'Category Browsing'],
                'best_for': 'OEM & Aftermarket'
            }
        }
    }

    // Format price for display
    const formatPrice = (price, currency = 'USD') => {
        if (!price) return 'N/A'
        
        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency
        })
        
        return formatter.format(price)
    }

    // Get price range from aggregated results
    const getPriceRange = (aggregatedResults) => {
        if (!aggregatedResults?.price_range) return null
        
        const { min, max, average } = aggregatedResults.price_range
        
        return {
            min: formatPrice(min),
            max: formatPrice(max),
            average: formatPrice(average)
        }
    }

    // Get marketplace statistics
    const getMarketplaceStats = (searchResults) => {
        if (!searchResults?.marketplaces) return {}
        
        const stats = {}
        
        Object.entries(searchResults.marketplaces).forEach(([marketplace, data]) => {
            stats[marketplace] = {
                name: getMarketplaceDisplayName(marketplace),
                results_count: data.parts?.length || 0,
                total_results: data.total_results || 0,
                data_source: data.data_source || 'unknown'
            }
        })
        
        return stats
    }

    // Sort parts by price
    const sortPartsByPrice = (parts, order = 'asc') => {
        return parts.sort((a, b) => {
            const priceA = a.price?.value || 0
            const priceB = b.price?.value || 0
            
            return order === 'asc' ? priceA - priceB : priceB - priceA
        })
    }

    // Filter parts by price range
    const filterPartsByPrice = (parts, minPrice, maxPrice) => {
        return parts.filter(part => {
            const price = part.price?.value || 0
            return price >= minPrice && price <= maxPrice
        })
    }

    // Filter parts by brand
    const filterPartsByBrand = (parts, brand) => {
        if (!brand) return parts
        
        return parts.filter(part => 
            part.brand?.toLowerCase().includes(brand.toLowerCase())
        )
    }

    // Filter parts by marketplace
    const filterPartsByMarketplace = (parts, marketplace) => {
        if (!marketplace) return parts
        
        return parts.filter(part => 
            part.marketplace?.toLowerCase() === marketplace.toLowerCase()
        )
    }

    // Get unique brands from parts
    const getUniqueBrands = (parts) => {
        const brands = new Set()
        parts.forEach(part => {
            if (part.brand) {
                brands.add(part.brand)
            }
        })
        return Array.from(brands).sort()
    }

    // Get unique categories from parts
    const getUniqueCategories = (parts) => {
        const categories = new Set()
        parts.forEach(part => {
            if (part.category) {
                categories.add(part.category)
            }
        })
        return Array.from(categories).sort()
    }

    // Initialize marketplace APIs
    const initializeMarketplaceAPIs = async () => {
        try {
            await Promise.all([
                getSupportedMarketplaces(),
                getAPIStatus()
            ])
            console.log('🛒 Parts Marketplace APIs initialized successfully')
        } catch (error) {
            console.error('Error initializing marketplace APIs:', error)
        }
    }

    return {
        // State
        marketplaceAPIs,
        supportedMarketplaces,
        apiStatus,
        getEnabledMarketplaces,
        getConfiguredMarketplaces,

        // Methods
        getSupportedMarketplaces,
        getAPIStatus,
        getAPIDocumentation,
        testMarketplaceAPI,
        searchEbayMotorsParts,
        searchAmazonParts,
        searchAutoZoneParts,
        searchRockAutoParts,
        searchPartsGeekParts,
        searchAllMarketplaces,
        getMarketplaceDisplayName,
        getMarketplaceLogo,
        getMarketplaceAPIStatus,
        isMarketplaceAPIAvailable,
        getMarketplaceFeatures,
        getMarketplaceAuthType,
        getMarketplaceRateLimits,
        getDataSourceBadge,
        getMarketplaceComparison,
        formatPrice,
        getPriceRange,
        getMarketplaceStats,
        sortPartsByPrice,
        filterPartsByPrice,
        filterPartsByBrand,
        filterPartsByMarketplace,
        getUniqueBrands,
        getUniqueCategories,
        initializeMarketplaceAPIs
    }
}














