import axios from 'axios'

// Create axios instance
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    console.log('API Request:', config.method?.toUpperCase(), config.url, config.data)
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response.status, response.config.url, response.data)
    return response
  },
  (error) => {
    console.error('API Error:', error.response?.status, error.config?.url, error.response?.data)
    
    // Show notification for errors
    if (window.$notify && error.response?.status >= 400) {
      const message = error.response?.data?.message || 'An error occurred'
      window.$notify.error('Error', message)
    }
    
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      if (window.$notify) {
        window.$notify.warning('Session Expired', 'Please log in again')
      }
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth API
export const authAPI = {
  register: (data) => api.post('/register', data),
  login: (data) => api.post('/login', data),
  logout: () => api.post('/logout'),
  getUser: () => api.get('/user'),
  updateProfile: (data) => api.put('/user/profile', data),
}

// Cars API
export const carsAPI = {
  getAll: () => api.get('/cars'),
  getById: (id) => api.get(`/cars/${id}`),
  create: (data) => api.post('/cars', data),
  update: (id, data) => api.put(`/cars/${id}`, data),
  delete: (id) => api.delete(`/cars/${id}`),
  statistics: () => api.get('/cars/statistics'),
}

// Diagnosis API
export const diagnosisAPI = {
  startDiagnosis: (data) => api.post('/diagnosis/start', data),
  submitDiagnosis: (formData) => api.post('/diagnosis/submit', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }),
  getResult: (sessionId) => api.get(`/diagnosis/result/${sessionId}?t=${Date.now()}&_=${Math.random()}`, {
    headers: {
      'Cache-Control': 'no-cache, no-store, must-revalidate',
      'Pragma': 'no-cache',
      'Expires': '0',
      'If-None-Match': '*'
    }
  }),
  getHistory: (params = {}) => api.get('/diagnosis/history', { params }),
  getCarDiagnosisHistory: (carId, params = {}) => api.get(`/diagnosis/car/${carId}/history`, { params }),
  transcribeAudio: (audioData) => api.post('/transcribe-audio', audioData, { headers: { 'Content-Type': 'multipart/form-data' } })
}

export const dashboardAPI = {
  getStatistics: () => api.get('/dashboard/statistics'),
  getNotifications: () => api.get('/dashboard/notifications'),
}


// Car Images API
export const carImagesAPI = {
  getPrimaryImage: (brand, model, year = null, color = null) => {
    const params = { brand, model }
    if (year) params.year = year
    if (color) params.color = color
    return api.get('/car-images/primary', { params })
  },
  getCarImages: (brand, model, year = null, color = null) => {
    const params = { brand, model }
    if (year) params.year = year
    if (color) params.color = color
    return api.get('/car-images/car', { params })
  },
  getBrandFallback: (brand) => api.get('/car-images/brand-fallback', { params: { brand } }),
  getDefaultImage: () => api.get('/car-images/default')
}

// AI Image Generation API // New API service
export const aiImageAPI = {
  getAvailableProviders: () => api.get('/ai-image/providers'),
  generateCarImage: (carId, provider = 'openai', forceRegenerate = false) => 
    api.post('/ai-image/generate', { car_id: carId, provider, force_regenerate: forceRegenerate }),
  generateImageIfNeeded: (carId, provider = 'openai') => 
    api.post('/ai-image/generate-if-needed', { car_id: carId, provider }),
  generateImagesForAllCars: (provider = 'openai', limit = 5) => 
    api.post('/ai-image/generate-all', { provider, limit }),
  getGenerationStatus: (carId) => api.get(`/ai-image/status/${carId}`)
}


export default api
