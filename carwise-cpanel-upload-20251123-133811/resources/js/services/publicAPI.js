// Public API Service for NHTSA, eBay, Amazon integration
import api from './api'

export const publicAPI = {
  // NHTSA Vehicle API
  async getVehicleByVIN(vin) {
    try {
      const response = await fetch(`/api/public/vehicle/vin/${vin}`)
      return await response.json()
    } catch (error) {
      console.error('VIN lookup error:', error)
      return { success: false, message: 'Error retrieving vehicle data' }
    }
  },

  async getVehicleByMakeModelYear(make, model, year) {
    try {
      const response = await fetch(`/api/public/vehicle/make/${make}/model/${model}/year/${year}`)
      return await response.json()
    } catch (error) {
      console.error('Vehicle lookup error:', error)
      return { success: false, message: 'Error retrieving vehicle data' }
    }
  },

  async getAllMakes() {
    try {
      const response = await fetch('/api/public/vehicle/makes')
      return await response.json()
    } catch (error) {
      console.error('Makes lookup error:', error)
      return { success: false, message: 'Error retrieving vehicle makes' }
    }
  },

  // eBay Motors API
  async searchEbayParts(query, categoryId = null, limit = 20) {
    try {
      const response = await api.post('/public/ebay/search', {
        query,
        category_id: categoryId,
        limit
      })
      return response.data
    } catch (error) {
      console.error('eBay search error:', error)
      return { success: false, message: 'Error searching eBay parts' }
    }
  },

  async getEbayItemDetails(itemId) {
    try {
      const response = await fetch(`/api/public/ebay/item/${itemId}`)
      return await response.json()
    } catch (error) {
      console.error('eBay item details error:', error)
      return { success: false, message: 'Error retrieving eBay item details' }
    }
  },

  // Amazon Product Advertising API
  async searchAmazonParts(query, category = 'Automotive', limit = 10) {
    try {
      const response = await api.post('/public/amazon/search', {
        query,
        category,
        limit
      })
      return response.data
    } catch (error) {
      console.error('Amazon search error:', error)
      return { success: false, message: 'Error searching Amazon parts' }
    }
  },

  // Multi-API search
  async searchAllAPIs(query, vehicleData = null) {
    try {
      const response = await api.post('/public/search', {
        query,
        ...vehicleData
      })
      return response.data
    } catch (error) {
      console.error('Multi-API search error:', error)
      return { success: false, message: 'Error searching across APIs' }
    }
  },

  // API status and configuration
  async getAPIStatus() {
    try {
      const response = await fetch('/api/public/status')
      return await response.json()
    } catch (error) {
      console.error('API status error:', error)
      return { success: false, message: 'Error retrieving API status' }
    }
  },

  async getSupportedCategories() {
    try {
      const response = await fetch('/api/public/categories')
      return await response.json()
    } catch (error) {
      console.error('Categories error:', error)
      return { success: false, message: 'Error retrieving categories' }
    }
  },

  // Utility functions
  formatVehicleData(vehicleData) {
    if (!vehicleData) return null

    return {
      vin: vehicleData.vin,
      make: vehicleData.make,
      model: vehicleData.model,
      year: vehicleData.year,
      engine: vehicleData.engine,
      fuelType: vehicleData.fuel_type,
      bodyClass: vehicleData.body_class,
      driveType: vehicleData.drive_type,
      transmission: vehicleData.transmission,
      doors: vehicleData.doors,
      cylinders: vehicleData.cylinders,
      displacement: vehicleData.displacement,
      manufacturer: vehicleData.manufacturer,
      plantCountry: vehicleData.plant_country,
      series: vehicleData.series,
      trim: vehicleData.trim,
      vehicleType: vehicleData.vehicle_type,
      displayName: `${vehicleData.year} ${vehicleData.make} ${vehicleData.model}`,
      searchQuery: `${vehicleData.year} ${vehicleData.make} ${vehicleData.model} ${vehicleData.engine || ''}`.trim()
    }
  },

  formatPartsData(partsData, source) {
    if (!partsData || !partsData.parts) return []

    return partsData.parts.map(part => ({
      id: part.id,
      name: part.title,
      description: part.description || part.title,
      price: part.price,
      formatted_price: part.formatted_price || `$${part.price}`,
      currency: part.currency || 'USD',
      image_url: part.image_url,
      condition: part.condition,
      brand: part.brand,
      part_number: part.part_number,
      rating: part.rating || 4.0,
      review_count: part.review_count || 0,
      stock_quantity: part.stock_quantity || 1,
      source: source,
      affiliate_url: part.affiliate_url || part.item_web_url,
      category: this.extractCategory(part.title || part.name),
      ai_recommended: part.ai_recommended || false,
      shipping_cost: part.shipping_cost,
      estimated_delivery: part.estimated_delivery,
      seller: part.seller,
      prime_eligible: part.prime_eligible,
      availability: part.availability
    }))
  },

  extractCategory(title) {
    const categories = {
      'engine': ['engine', 'motor', 'spark plug', 'air filter', 'oil filter', 'belt', 'gasket', 'pump'],
      'brakes': ['brake', 'pad', 'rotor', 'caliper', 'line', 'fluid'],
      'electrical': ['battery', 'alternator', 'starter', 'fuse', 'wire', 'sensor', 'electrical'],
      'suspension': ['shock', 'strut', 'spring', 'control arm', 'tie rod', 'ball joint', 'suspension'],
      'transmission': ['transmission', 'clutch', 'fluid', 'filter', 'solenoid'],
      'exhaust': ['exhaust', 'muffler', 'catalytic', 'pipe', 'header', 'o2 sensor'],
      'cooling': ['radiator', 'thermostat', 'water pump', 'hose', 'coolant', 'cooling'],
      'fuel': ['fuel pump', 'fuel filter', 'injector', 'fuel line', 'tank']
    }

    const lowerTitle = title.toLowerCase()
    
    for (const [category, keywords] of Object.entries(categories)) {
      if (keywords.some(keyword => lowerTitle.includes(keyword))) {
        return category
      }
    }

    return 'other'
  },

  // VIN validation
  validateVIN(vin) {
    if (!vin || typeof vin !== 'string') return false
    
    // Remove spaces and convert to uppercase
    vin = vin.replace(/\s/g, '').toUpperCase()
    
    // Check length
    if (vin.length !== 17) return false
    
    // Check for invalid characters (I, O, Q are not allowed)
    if (/[IOQ]/.test(vin)) return false
    
    // Check format (alphanumeric)
    if (!/^[A-HJ-NPR-Z0-9]{17}$/.test(vin)) return false
    
    return true
  },

  // Generate search suggestions based on vehicle data
  generateSearchSuggestions(vehicleData) {
    if (!vehicleData) return []

    const suggestions = []
    const { make, model, year, engine } = vehicleData

    // Basic vehicle-specific searches
    suggestions.push(`${year} ${make} ${model} parts`)
    suggestions.push(`${make} ${model} ${year} accessories`)
    
    if (engine) {
      suggestions.push(`${year} ${make} ${model} ${engine} parts`)
    }

    // Category-specific searches
    const categories = ['brake pads', 'air filter', 'oil filter', 'spark plugs', 'battery', 'tires']
    categories.forEach(category => {
      suggestions.push(`${year} ${make} ${model} ${category}`)
    })

    return suggestions.slice(0, 6) // Limit to 6 suggestions
  },

  // Track affiliate clicks
  async trackAffiliateClick(partId, source, partData) {
    try {
      await api.post('/affiliate/track-click', {
        part_id: partId,
        source: source,
        part_data: partData,
        user_agent: navigator.userAgent,
        referrer: document.referrer,
        timestamp: new Date().toISOString()
      })
    } catch (error) {
      console.error('Affiliate tracking error:', error)
    }
  },

  // Get price comparison across APIs
  async comparePricesAcrossAPIs(query, vehicleData = null) {
    try {
      const results = await this.searchAllAPIs(query, vehicleData)
      
      if (!results.success) return null

      const priceComparison = {
        query,
        vehicle_data: vehicleData,
        sources: {},
        best_price: null,
        total_results: 0
      }

      // Process eBay results
      if (results.data.ebay && results.data.ebay.success) {
        const ebayParts = this.formatPartsData(results.data.ebay.data, 'ebay')
        priceComparison.sources.ebay = {
          count: ebayParts.length,
          parts: ebayParts,
          average_price: this.calculateAveragePrice(ebayParts),
          min_price: this.calculateMinPrice(ebayParts),
          max_price: this.calculateMaxPrice(ebayParts)
        }
        priceComparison.total_results += ebayParts.length
      }

      // Process Amazon results
      if (results.data.amazon && results.data.amazon.success) {
        const amazonParts = this.formatPartsData(results.data.amazon.data, 'amazon')
        priceComparison.sources.amazon = {
          count: amazonParts.length,
          parts: amazonParts,
          average_price: this.calculateAveragePrice(amazonParts),
          min_price: this.calculateMinPrice(amazonParts),
          max_price: this.calculateMaxPrice(amazonParts)
        }
        priceComparison.total_results += amazonParts.length
      }

      // Find best price
      priceComparison.best_price = this.findBestPrice(priceComparison.sources)

      return priceComparison

    } catch (error) {
      console.error('Price comparison error:', error)
      return null
    }
  },

  calculateAveragePrice(parts) {
    if (!parts.length) return 0
    const total = parts.reduce((sum, part) => sum + (parseFloat(part.price) || 0), 0)
    return total / parts.length
  },

  calculateMinPrice(parts) {
    if (!parts.length) return 0
    return Math.min(...parts.map(part => parseFloat(part.price) || Infinity))
  },

  calculateMaxPrice(parts) {
    if (!parts.length) return 0
    return Math.max(...parts.map(part => parseFloat(part.price) || 0))
  },

  findBestPrice(sources) {
    let bestPrice = null
    let bestSource = null

    Object.entries(sources).forEach(([source, data]) => {
      if (data.min_price && (!bestPrice || data.min_price < bestPrice.price)) {
        bestPrice = {
          price: data.min_price,
          source: source,
          part: data.parts.find(part => parseFloat(part.price) === data.min_price)
        }
      }
    })

    return bestPrice
  }
}

export default publicAPI
