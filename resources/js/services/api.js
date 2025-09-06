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
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
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
}

// Diagnosis API
export const diagnosisAPI = {
  getAll: () => api.get('/diagnoses'),
  getById: (id) => api.get(`/diagnoses/${id}`),
  create: (data) => {
    const formData = new FormData()
    formData.append('car_id', data.car_id)
    formData.append('description', data.description || '')
    formData.append('media_file', data.media_file)
    
    return api.post('/diagnoses', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
}

// Mechanics API
export const mechanicsAPI = {
  getAll: (params = {}) => api.get('/mechanics', { params }),
  getById: (id) => api.get(`/mechanics/${id}`),
  update: (id, data) => api.put(`/mechanics/${id}`, data),
}

export default api
