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
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('token')
      localStorage.removeItem('user')
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
  submitDiagnosis: (formData) => api.post('/diagnosis/submit', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }),
  getResult: (sessionId) => api.get(`/diagnosis/result/${sessionId}`),
  getHistory: (params = {}) => api.get('/diagnosis/history', { params }),
}

// Mechanics API
export const mechanicsAPI = {
  getAll: (params = {}) => api.get('/mechanics', { params }),
  getById: (id) => api.get(`/mechanics/${id}`),
  update: (id, data) => api.put(`/mechanics/${id}`, data),
}

export default api
