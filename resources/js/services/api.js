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
}

export const dashboardAPI = {
  getStatistics: () => api.get('/dashboard/statistics'),
  getNotifications: () => api.get('/dashboard/notifications'),
}

// Mechanics API
export const mechanicsAPI = {
  getAll: (params = {}) => api.get('/mechanics', { params }),
  getById: (id) => api.get(`/mechanics/${id}`),
  create: (formData) => api.post('/mechanics', formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
  update: (id, formData) => api.post(`/mechanics/${id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
}

export default api
